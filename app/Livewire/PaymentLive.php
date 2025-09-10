<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PaymentLive extends Component
{
    public $orders;
    public $amount_paid,  $reference_no, $remarks;
    public $payment_amount;
    public $selectedOrder = null;
    public $payment_type = null;
    public $payments;
    public $showPaymentModal = false;

    public $clientsWithBalance;

    public $showOutstandingModal = false;
    public $outstandingClient = null;
    public $outstanding_payment_amount = null;
    public $outstanding_payment_type = null;
    public function mount()
    {
        $this->loadOrders();
        $this->payments = Payment::with(['client', 'order'])->latest()->get();
        $this->loadClientsWithBalance();
    }

    public function loadOrders()
    {
        $this->orders = Order::where('locked', true)
            ->where('paid', false)
            ->with('client')
            ->get();
    }

    public function loadClientsWithBalance()
    {
        $this->clientsWithBalance = \App\Models\Client::where('outstanding_balance', '>', 0)
            ->get();
    }

    public function selectOrder($orderId)
    {
        $this->selectedOrder = \App\Models\Order::with('client','items.product')->findOrFail($orderId);

        // set initial value to client's registered payment_type
        $this->payment_type = $this->selectedOrder->client->payment_type ?? null;
    }

    public function closePaymentModal()
    {
        $this->selectedOrder = null;
        $this->payment_type = null;
        $this->amount_paid = null;
        $this->reference_no = null;
        $this->remarks = null;
        $this->showPaymentModal = false;
    }

    public function getAvailablePaymentTypesProperty()
    {
        $clientType = $this->selectedOrder->client->payment_type ?? null;

        if (in_array($clientType, ['Charge', 'Post Date Check'])) {
            return ['Cash','On Date Check','Charge','Post Date Check'];
        }

        return ['Cash','On Date Check'];
    }


    public function openPaymentModal($orderId)
    {
        $this->selectedOrder = Order::with('client')->findOrFail($orderId);

        // Autofill with client's registered payment type
        $this->payment_type = $this->selectedOrder->client->payment_type;

        $this->showPaymentModal = true;
    }

    public function savePayment()
    {
        $this->validate([
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_type'   => 'required',
        ]);

        $order = Order::with('payments')->findOrFail($this->selectedOrder->id);

        // Calculate remaining balance
        $totalPaid = $order->payments->sum('amount_paid');
        $remainingBalance = $order->total - $totalPaid;

        // Prevent overpayment
        if ($this->payment_amount > $remainingBalance) {
            $this->addError('payment_amount', 'Payment exceeds the total amount.');
            return;
        }

        // Determine payment status for this payment entry
        $newTotalPaid = $totalPaid + $this->payment_amount;
        $paymentStatus = $newTotalPaid >= $order->total ? 'Paid' : 'Partial';

        // Save payment with its own status
        Payment::create([
            'order_id'       => $order->id,
            'client_id'      => $order->client_id,
            'amount_paid'    => $this->payment_amount,
            'payment_type'   => $this->payment_type,
            'reference_no'   => $this->reference_no,
            'remarks'        => $this->remarks,
            'payment_status' => $paymentStatus,
        ]);

        // Update order status (always considered "paid" once something is paid)
        if ($newTotalPaid >= $order->total) {
            $order->update([
                'paid'           => true,
                'payment_status' => 'Paid',
            ]);
        } else {
            $order->update([
                'paid'           => true, // ✅ still mark as true, per your rule
                'payment_status' => 'Partial',
            ]);
        }

        // Adjust outstanding balance
        $order->client->decrement('outstanding_balance', $this->payment_amount);

        // Refresh orders and payments lists
        $this->loadOrders();
        $this->payments = Payment::with(['client', 'order'])->latest()->get();

        // Close modal and reset fields
        $this->closePaymentModal();
    }
    ////////////////////////////////// Out standing Payment Modal ////////////////////////////////
    // Open the modal for a client
    public function openOutstandingPaymentModal($clientId)
    {
        $this->outstandingClient = \App\Models\Client::findOrFail($clientId);
        $this->outstanding_payment_type = $this->outstandingClient->payment_type;
        $this->outstanding_payment_amount = null;
        $this->showOutstandingModal = true;
    }

    // Payment type options for outstanding modal
    public function getOutstandingPaymentTypesProperty()
    {
        $type = $this->outstandingClient->payment_type ?? null;
        if (in_array($type, ['Charge', 'Post Date Check'])) {
            return ['Cash', 'On Date Check', 'Charge', 'Post Date Check'];
        }
        return ['Cash', 'On Date Check'];
    }

    // Save payment for outstanding balance
    public function saveOutstandingPayment()
    {
        $this->validate([
            'outstanding_payment_amount' => 'required|numeric|min:0.01',
            'outstanding_payment_type'   => 'required',
        ]);

        // Get current outstanding balance before payment
        $currentBalance = $this->outstandingClient->outstanding_balance;

        // Determine payment status
        $newBalance = $currentBalance - $this->outstanding_payment_amount;
        $paymentStatus = $newBalance <= 0 ? 'Cleared' : 'Installment';

        // Create a payment record with order_id = null
        Payment::create([
            'order_id'       => null, // explicitly null for outstanding balance payments
            'client_id'      => $this->outstandingClient->id,
            'amount_paid'    => $this->outstanding_payment_amount,
            'payment_type'   => $this->outstanding_payment_type,
            'reference_no'   => null,
            'remarks'        => 'OB payment',
            'payment_status' => $paymentStatus,
        ]);

        // Decrement outstanding balance
        $this->outstandingClient->decrement('outstanding_balance', $this->outstanding_payment_amount);

        // Refresh lists
        $this->payments = Payment::with(['client', 'order'])->latest()->get();
        $this->loadClientsWithBalance();

        // Close modal and reset
        $this->closeOutstandingModal();
    }


    public function closeOutstandingModal()
    {
        $this->showOutstandingModal = false;
        $this->outstandingClient = null;
        $this->outstanding_payment_amount = null;
        $this->outstanding_payment_type = null;
    }



    public function render()
    {
        return view('livewire.payment-live', [
            'orders' => $this->orders
        ])->layout('layouts.app');
    }
}
