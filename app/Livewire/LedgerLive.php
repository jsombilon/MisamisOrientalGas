<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Order;
use App\Models\Payment;
use Livewire\Component;
use Carbon\Carbon;

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

    public function updatedFilterYear()
    {
        $this->loadLedger();
    }

    public function updatedFilterMonth()
    {
        $this->loadLedger();
    }

    public function updatedFilterCategory()
    {
        $this->loadLedger();
    }

    private function loadLedger()
    {
        if (!$this->selectedClient) return;

        $year = $this->filterYear;
        $month = $this->filterMonth;

        // Dates for filtering
        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth   = Carbon::create($year, $month, 1)->endOfMonth();

        // Fetch orders
        $orders = Order::with(['items.product'])
            ->where('client_id', $this->selectedClient->id)
            ->get()
            ->map(function ($order) {
                return (object) [
                    'type' => 'order',
                    'date' => $order->created_at,
                    'debit' => $order->total, // Adds to balance
                    'credit' => 0,
                    'balance' => 0, // placeholder, will compute later
                    'remarks' => $order->remarks ?? 'Order',
                    'order' => $order,
                    'payment' => null,
                ];
            });

        // Fetch payments
        $payments = Payment::where('client_id', $this->selectedClient->id)
            ->get()
            ->map(function ($payment) {
                return (object) [
                    'type' => 'payment',
                    'date' => $payment->created_at,
                    'debit' => 0,
                    'credit' => $payment->amount_paid, // Reduces balance
                    'balance' => 0, // placeholder
                    'remarks' => $payment->remarks ?? 'Payment',
                    'order' => $payment->order,
                    'payment' => $payment,
                ];
            });

        // Merge + sort
        $allEntries = $orders->merge($payments)->sortBy('date')->values();

        $runningBalance = 0;
        $forwarded = 0;
        $filteredEntries = [];

        foreach ($allEntries as $entry) {
            $runningBalance += $entry->debit - $entry->credit;

            // Before selected month → goes to forwarded
            if ($entry->date->lt($startOfMonth)) {
                $forwarded = $runningBalance;
                continue;
            }

            // Within filter range
            if ($entry->date->between($startOfMonth, $endOfMonth)) {
                $entry->balance = $runningBalance;
                $filteredEntries[] = $entry;
            }
        }

        // Set properties for Blade
        $this->ledgerEntries = $filteredEntries;
        $this->balanceForwarded = $forwarded;
        $this->totalBalance = $runningBalance;
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
