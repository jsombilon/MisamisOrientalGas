<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'product_db';
    protected $guarded = [];
    
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }
}
