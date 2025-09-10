<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'client_db'; // since your table isn’t the default "clients"
    protected $guarded = [];

    public function orders()
    {
        return $this->hasMany(Order::class, 'client_id');
    }
        public function payments()
    {
        return $this->hasMany(Payment::class, 'client_id');
    }

    public function ledgerEntries()
    {
        return $this->hasMany(Ledger::class, 'client_id');
    }

    // 📌 Outstanding Balance = sum of all locked orders - payments
    public function outstandingBalance()
    {
        $totalOrders  = $this->orders()->where('locked', true)->sum('total');
        $totalPaid    = $this->payments()->sum('amount_paid');

        return $totalOrders - $totalPaid;
    }

    // 📌 Scope: fetch only clients with outstanding balance > 0
    public function scopeWithOutstanding($query)
    {
        return $query->whereHas('orders', function ($q) {
            $q->where('locked', true);
        })->get()->filter(function ($client) {
            return $client->outstandingBalance() > 0;
        });
    }
}
