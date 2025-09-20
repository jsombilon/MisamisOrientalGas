<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Order;
use App\Models\Payment;
use Livewire\Component;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf as DomPDF;



class LedgerLive extends Component
{
    public $clients;
    public $showLedgerModal = false;
    public $selectedClient = null;

    public $filterYear;
    public $filterMonth;
    public $filterCategory = 'all';

    public $ledgerEntries = [];
    public $balanceForwarded = 0;
    public $totalBalance = 0;

    public function mount()
    {
        $this->clients = Client::all();
    }

    public function openLedgerModal($clientId)
    {
        $this->selectedClient = Client::findOrFail($clientId);

        // Default filters to current year & month
        $this->filterYear = now()->year;
        $this->filterMonth = now()->month;
        $this->filterCategory = 'all';

        // initial load when modal opens
        $this->loadLedger();

        $this->showLedgerModal = true;
    }

    public function closeLedgerModal()
    {
        $this->showLedgerModal = false;
        $this->selectedClient = null;
        $this->ledgerEntries = [];
        $this->balanceForwarded = 0;
        $this->totalBalance = 0;
    }

    /**
     * This is the button action called from Blade.
     * It intentionally does NOT run on every dropdown change.
     */
    public function generateLedger()
    {
        $this->loadLedger();
    }

    /**
     * Build the ledger entries (orders + payments), compute forwarded balance and totals
     * NOTE: this method expects $this->filterYear and $this->filterMonth to be set.
     */
    private function loadLedger()
    {
        if (!$this->selectedClient) return;

        $year = $this->filterYear ?? now()->year;
        $month = $this->filterMonth ?? now()->month;

        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth   = Carbon::create($year, $month, 1)->endOfMonth();

        // --- Orders (debit) ---
        $orders = Order::with(['items.product'])
            ->where('client_id', $this->selectedClient->id)
            ->get()
            ->map(function ($order) {
                return [
                    'type' => 'order',
                    'date' => $order->created_at,
                    'debit' => (float) $order->total,
                    'credit' => 0.0,
                    'remarks' => $order->details ?? 'Order',
                    'order' => $order,
                    'payment' => null,
                ];
            });

        // --- Payments (credit) ---
        $payments = Payment::where('client_id', $this->selectedClient->id)
            ->where(function ($q) {
                $q->where('payment_type', '!=', 'Post Date Check')
                ->orWhere(function ($q2) {
                    $q2->where('payment_type', 'Post Date Check')
                        ->where('check_status', 'Paid');
                });
            })
            ->get()
            ->map(function ($payment) {
                $isPDC = strtolower($payment->payment_type) === 'post date check';
                $date = $isPDC && $payment->check_date ? Carbon::parse($payment->check_date) : $payment->created_at;
                return [
                    'type' => 'payment',
                    'date' => $date,
                    'debit' => 0.0,
                    'credit' => (float) $payment->amount_paid,
                    'remarks' => $payment->remarks ?? 'Payment',
                    'order' => $payment->order,
                    'payment' => $payment,
                ];
            });

        // --- Merge and sort ---
        $allEntries = collect($orders)->merge($payments)->sortBy('date')->values();

        $runningBalance = 0.0;
        $forwarded = 0.0;
        $filteredEntries = [];

        foreach ($allEntries as $entry) {
            $runningBalance += ($entry['debit'] ?? 0) - ($entry['credit'] ?? 0);

            if ($entry['date']->lt($startOfMonth)) {
                // everything before this month contributes to forwarded
                $forwarded = $runningBalance;
                continue;
            }

            if ($entry['date']->between($startOfMonth, $endOfMonth)) {
                if ($this->filterCategory === 'all' || $this->filterCategory === $entry['type']) {
                    // balance inside THIS month starts at forwarded and counts forward
                    $entry['balance'] = $forwarded 
                        + collect($filteredEntries)->sum(fn($e) => ($e['debit'] - $e['credit'])) 
                        + ($entry['debit'] - $entry['credit']);
                    $filteredEntries[] = $entry;
                }
            }
        }

        // monthly balance = forwarded + net of filtered entries
        $monthlyNet = collect($filteredEntries)->sum(fn($e) => ($e['debit'] - $e['credit']));
        $monthEndBalance = $forwarded + $monthlyNet;

        $this->ledgerEntries = $filteredEntries;
        $this->balanceForwarded = $forwarded;
        $this->totalBalance = $monthEndBalance; // <-- only balance of this month
    }


    public function printLedger($clientId)
    {
        $client = Client::findOrFail($clientId);
        $this->loadLedger();
        // Use already prepared properties
        $pdf = DomPDF::loadView('pdf.ledger', [
            'client' => $client,
            'ledgerEntries' => $this->ledgerEntries,
            'balanceForwarded' => $this->balanceForwarded,
            'totalBalance' => $this->totalBalance,
            'filterYear' => $this->filterYear,
            'filterMonth' => $this->filterMonth,
            'filterCategory' => $this->filterCategory,
        ]);

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'ledger-'.$client->client_number.'.pdf'
        );

    }

    public function render()
    {
        return view('livewire.ledger-live', [
            'clients' => $this->clients,
            'ledgerEntries' => $this->ledgerEntries,
            'balanceForwarded' => $this->balanceForwarded,
            'totalBalance' => $this->totalBalance,
        ])->layout('layouts.app');
    }
}
