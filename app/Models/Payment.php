<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Payment extends Model
{
    protected $table = 'payments_db';

    protected $fillable = [
        'order_id',
        'client_id',
        'amount_paid',
        'payment_type',
        'reference_no',
        'remarks',
        'payment_status',
        'check_date',
        'check_status',
    ];

    // 🔗 Relationships
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function ledgerEntries()
    {
        return $this->hasMany(Ledger::class, 'payment_id');
    }

     // 📌 Business Logic
    public static function recordPayment($order, $amount, $paymentType, $reference = null, $remarks = null)
    {
        return DB::transaction(function () use ($order, $amount, $paymentType, $reference, $remarks) {
            // create payment record
            $payment = self::create([
                'order_id'     => $order->id,
                'client_id'    => $order->client_id,
                'amount_paid'  => $amount,
                'payment_type' => $paymentType,
                'reference_no' => $reference,
                'remarks'      => $remarks,
            ]);

            // mark order as paid if fully settled
            if ($amount >= $order->total) {
                $order->paid = true;
                $order->save();
            }

            // ledger entry
            $latestBalance = $order->client->ledgerEntries()->latest()->first()->balance ?? 0;
            $newBalance    = $latestBalance - $amount;

            Ledger::create([
                'client_id'  => $order->client_id,
                'order_id'   => $order->id,
                'payment_id' => $payment->id,
                'entry_type' => 'Payment',
                'debit'      => 0,
                'credit'     => $amount,
                'balance'    => $newBalance,
                'remarks'    => "Payment for Order {$order->order_slip}",
            ]);

            return $payment;
        });
    }
}
