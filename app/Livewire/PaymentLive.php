<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PaymentLive extends Component
{
    // UI lists
    public $orders;              
    public $paymentsMade;        
    public $postDateChecks;      
    public $clientsWithBalance;  

    // Modal / form props for Order Payment
    public $selectedOrder = null;
    public $payment_amount;
    public $payment_type = '';
    public $check_date;
    public $reference_no;
    public $remarks;
    public $showPaymentModal = false;

    // Outstanding payment modal
    public $showOutstandingModal = false;
    public $outstandingClient = null;
    public $outstanding_payment_amount;
    public $outstanding_payment_type;
    public $outstanding_check_date;
    public $outstanding_reference_no;
    public $outstanding_remarks;

    // --- lifecycle
    public function mount()
    {
        $this->refreshAllLists();
    }

    // --- list loaders
    protected function loadOrders()
    {
        $this->orders = Order::where('locked', true)
            ->where('paid', false)
            ->with('client')
            ->latest()
            ->get();
    }

    protected function loadPaymentsMade()
    {
        $this->paymentsMade = Payment::with(['client', 'order'])
            ->where(function ($q) {
                $q->where('payment_type', '!=', 'Post Date Check')
                  ->orWhere(function ($q2) {
                      $q2->where('payment_type', 'Post Date Check')
                         ->where('check_status', 'Paid');
                  });
            })
            ->latest()
            ->get();
    }

    protected function loadPostDateChecks()
    {
        // PDC list: Post Date Check entries that are not yet Paid (i.e., Unpaid or Bounced)
        $this->postDateChecks = Payment::with(['client', 'order'])
            ->where('payment_type', 'Post Date Check')
            ->where(function ($q) {
                $q->whereNull('check_status')
                  ->orWhere('check_status', 'Unpaid')
                  ->orWhere('check_status', 'Bounced');
            })
            ->latest()
            ->get();
    }

    protected function loadClientsWithBalance()
    {
        $this->clientsWithBalance = Client::where('outstanding_balance', '>', 0)->get();
    }

    protected function refreshAllLists()
    {
        $this->loadOrders();
        $this->loadPaymentsMade();
        $this->loadPostDateChecks();
        $this->loadClientsWithBalance();
    }

    // --- helpers
    public function getAvailablePaymentTypesProperty()
    {
        // If no selectedOrder, return basic set
        $clientType = $this->selectedOrder->client->payment_type ?? null;

        if (in_array($clientType, ['Charge', 'Post Date Check'])) {
            return ['Cash','On Date Check','Charge','Post Date Check'];
        }

        return ['Cash','On Date Check'];
    }

    // --- Order payment modal controls
    public function openPaymentModal($orderId)
    {
        $this->selectedOrder = Order::with('client','items.product')->findOrFail($orderId);

        // default payment type = client's registered payment_type (fallback Cash)
        $this->payment_type = $this->selectedOrder->client->payment_type ?? 'Cash';
        $this->payment_amount = null;
        $this->check_date = null;
        $this->reference_no = null;
        $this->remarks = null;
        $this->showPaymentModal = true;
    }

    public function closePaymentModal()
    {
        $this->selectedOrder = null;
        $this->payment_type = '';
        $this->payment_amount = null;
        $this->check_date = null;
        $this->reference_no = null;
        $this->remarks = null;
        $this->showPaymentModal = false;
    }

    // --- Save payment (for an order) ---
    public function savePayment()
    {
        // validation rules
        $rules = [
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_type'   => 'required|string',
        ];

        if (strtolower($this->payment_type) === 'post date check') {
            $rules['check_date'] = 'required|date|after_or_equal:today';
        }

        $this->validate($rules);

        DB::transaction(function () {
            $order = Order::with('payments')->findOrFail($this->selectedOrder->id);

            $paidAlready = $order->payments()
                ->where(function ($q) {
                    $q->where('payment_type', '!=', 'Post Date Check')
                      ->orWhere(function ($q2) {
                          $q2->where('payment_type', 'Post Date Check')
                             ->where('check_status', 'Paid');
                      });
                })->sum('amount_paid');

            $remaining = $order->total - $paidAlready;

            // prevent overpay for the order
            if ($this->payment_amount > $remaining) {
                // throw validation-like error (we're in transaction; throw then catch outside)
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'payment_amount' => 'Payment exceeds remaining order balance (₱' . number_format($remaining,2) . ').',
                ]);
            }

            // determine payment fields
            $isPDC = strtolower($this->payment_type) === 'post date check';
            $check_status = $isPDC ? 'Unpaid' : null;
            $check_date = $isPDC ? $this->check_date : null;

            // what the payment_status of this payment entry should be:
            $newTotalPaid = $paidAlready + $this->payment_amount;
            $payment_entry_status = $newTotalPaid >= $order->total ? 'Paid' : 'Partial';

            // store payment record
            $payment = Payment::create([
                'order_id'       => $order->id,
                'client_id'      => $order->client_id,
                'amount_paid'    => $this->payment_amount,
                'payment_type'   => $this->payment_type,
                'reference_no'   => $this->reference_no,
                'remarks'        => $this->remarks,
                'payment_status' => $payment_entry_status,
                'check_date'     => $check_date,
                'check_status'   => $check_status,
            ]);

            // business rule you requested:
            // when a payment is created for an order (including PDC), remove order from Payment List immediately:
            // i.e., set order->paid = true (so it no longer appears in loadOrders())
            // BUT only decrement outstanding_balance for non-PDC payments — PDCs only decrement when cleared (markCheckAsPaid)
            $order->update([
                'paid' => true,
                // payment_status will be recalculated; set to Paid if fully covered now, Partial otherwise
                'payment_status' => $newTotalPaid >= $order->total ? 'Paid' : 'Partial',
            ]);

            if (! $isPDC) {
                // decrement outstanding balance immediately for client
                $order->client->decrement('outstanding_balance', $this->payment_amount);
            }
        });

        // if validation exception thrown in transaction, rethrow for Livewire to show errors
        $this->refreshAllLists();
        $this->closePaymentModal();
        // Refresh lists after saving

        session()->flash('message', 'Payment recorded.');
    }

    // --- Outstanding payments (client-level) ---
    public function openOutstandingPaymentModal($clientId)
    {
        $this->outstandingClient = Client::findOrFail($clientId);
        $this->outstanding_payment_type = $this->outstandingClient->payment_type ?? 'Cash';
        $this->outstanding_payment_amount = null;
        $this->outstanding_check_date = null;
        $this->outstanding_reference_no = null;
        $this->outstanding_remarks = null;
        $this->showOutstandingModal = true;
    }

    public function closeOutstandingModal()
    {
        $this->showOutstandingModal = false;
        $this->outstandingClient = null;
        $this->outstanding_payment_amount = null;
        $this->outstanding_payment_type = null;
        $this->outstanding_check_date = null;
        $this->outstanding_reference_no = null;
        $this->outstanding_remarks = null;
    }
    public function getOutstandingPaymentTypesProperty()
    {
        // You can customize this logic if you want to restrict types per client
        return ['Cash', 'On Date Check', 'Charge', 'Post Date Check'];
    }
    public function saveOutstandingPayment()
    {
        $rules = [
            'outstanding_payment_amount' => 'required|numeric|min:0.01',
            'outstanding_payment_type'   => 'required|string',
        ];

        if (strtolower($this->outstanding_payment_type) === 'post date check') {
            $rules['outstanding_check_date'] = 'required|date|after_or_equal:today';
        }

        $this->validate($rules);

        DB::transaction(function () {
            $client = Client::findOrFail($this->outstandingClient->id);

            $newBalance = $client->outstanding_balance - $this->outstanding_payment_amount;
            $paymentStatus = $newBalance <= 0 ? 'Cleared' : 'Installment';

            $isPDC = strtolower($this->outstanding_payment_type) === 'post date check';
            $checkStatus = $isPDC ? 'Unpaid' : null;
            $checkDate = $isPDC ? $this->outstanding_check_date : null;

            // Create payment with order_id = null
            Payment::create([
                'order_id'       => null,
                'client_id'      => $client->id,
                'amount_paid'    => $this->outstanding_payment_amount,
                'payment_type'   => $this->outstanding_payment_type,
                'reference_no'   => $this->outstanding_reference_no,
                'remarks'        => $this->outstanding_remarks ?? 'Outstanding balance payment',
                'payment_status' => $paymentStatus,
                'check_date'     => $checkDate,
                'check_status'   => $checkStatus,
            ]);

            if (! $isPDC) {
                $client->decrement('outstanding_balance', $this->outstanding_payment_amount);
            }
        });

        $this->refreshAllLists();
        $this->closeOutstandingModal();
        session()->flash('message', 'Outstanding payment saved.');
    }

    // --- Post Date Check actions ---
    public function markCheckAsPaid($paymentId)
    {
        DB::transaction(function () use ($paymentId) {
            /** @var Payment $payment */
            $payment = Payment::with('order', 'client')->findOrFail($paymentId);

            // mark the payment check as paid
            $payment->update([
                'check_status'   => 'Paid',
                'payment_status' => 'Paid',
            ]);

            // decrement client outstanding_balance
            if ($payment->client) {
                $payment->client->decrement('outstanding_balance', $payment->amount_paid);
            }

            // if linked to an order, recalc order totals and status
            if ($payment->order) {
                $order = $payment->order;

                $totalPaid = $order->payments()
                    ->where(function ($q) {
                        $q->where('payment_type', '!=', 'Post Date Check')
                          ->orWhere(function ($q2) {
                              $q2->where('payment_type', 'Post Date Check')
                                 ->where('check_status', 'Paid');
                          });
                    })->sum('amount_paid');

                $order->update([
                    'paid' => $totalPaid > 0, // you wanted order removed when any payment exists; keep true if any payment
                    'payment_status' => $totalPaid >= $order->total ? 'Paid' : 'Partial',
                ]);
            }
        });

        $this->refreshAllLists();
        session()->flash('message', 'Check marked as paid — payment moved to Payments Made.');
    }

    public function markCheckAsBounced($paymentId)
    {
        DB::transaction(function () use ($paymentId) {
            $payment = Payment::with('order', 'client')->findOrFail($paymentId);

            // mark bounced, do NOT change outstanding_balance
            $payment->update([
                'check_status'   => 'Bounced',
                'payment_status' => 'Bounced',
            ]);

            // If it was linked to an order, we might want to set order.paid back to false
            // only if there are no other payments that keep it "paid" according to your rules.
            if ($payment->order) {
                $order = $payment->order;

                $totalPaid = $order->payments()
                    ->where(function ($q) {
                        $q->where('payment_type', '!=', 'Post Date Check')
                          ->orWhere(function ($q2) {
                              $q2->where('payment_type', 'Post Date Check')
                                 ->where('check_status', 'Paid');
                          });
                    })->sum('amount_paid');

                // If no payments now, mark order as unpaid so it reappears in Payment List
                $order->update([
                    'paid' => $totalPaid > 0,
                    'payment_status' => $totalPaid >= $order->total ? 'Paid' : ($totalPaid > 0 ? 'Partial' : 'Unpaid'),
                ]);
            }
        });

        $this->refreshAllLists();
        session()->flash('message', 'Check marked as bounced.');
    }

    // --- render
    public function render()
    {
        
        return view('livewire.payment-live', [
            'orders' => $this->orders,
            'payments' => $this->paymentsMade,
            'postDateChecks' => $this->postDateChecks,
            'clientsWithBalance' => $this->clientsWithBalance,
        ])->layout('layouts.app');
    }
}
