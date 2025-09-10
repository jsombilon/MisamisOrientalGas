<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ledger extends Model
{
    protected $table = 'ledger_db';

    protected $fillable = [
        'client_id',
        'order_id',
        'payment_id',
        'entry_date',
        'entry_type',
        'category',
        'debit',
        'credit',
        'balance',
        'remarks',
    ];

    // 🔗 Relationships
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }
}
