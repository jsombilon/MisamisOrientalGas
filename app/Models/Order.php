<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    protected $table = 'order_db'; 
    protected $fillable = [
        'order_slip',
        'client_id',
        'price_code',
        'discount',
        'discount_type',  
        'purchase_order',
        'wwrs',
        'truck',
        'details',
        'delivery_details',
        'total',
        'locked',
        'paid',
        'payment_status',
    ];


    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
     public function payments()
    {
        return $this->hasMany(Payment::class, 'order_id');
    }

    public function ledgerEntries()
    {
        return $this->hasMany(Ledger::class, 'order_id');
    }

    // 📌 Business Logic

    /** Lock the order and deduct stock */
    public function lockOrder()
    {
        if ($this->locked) {
            return; // already locked
        }

        DB::transaction(function () {
            foreach ($this->items as $item) {
                $product = $item->product;

                // deduct available stock
                $product->available -= $item->quantity;
                $product->sold = ($product->sold ?? 0) + $item->quantity;
                $product->save();
            }

            $this->locked = true;
            $this->save();

            // insert ledger entry
            Ledger::create([
                'client_id'  => $this->client_id,
                'order_id'   => $this->id,
                'entry_type' => 'Order',
                'debit'      => $this->total,
                'credit'     => 0,
                'balance'    => $this->client->ledgerEntries()->latest()->first()->balance + $this->total ?? $this->total,
                'remarks'    => "Order placed - {$this->order_slip}",
            ]);
        });
    }

    public function scopeLockedUnpaid($query)
    {
        return $query->where('locked', true)->where('paid', false);
    }



}
