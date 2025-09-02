<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class OrderLive extends Component
{
    public function render()
    {
        return view('livewire.order-live', [
             'products' => Product::orderBy('product_name', 'asc')
                                    ->get()
        ])->layout('layouts.app');
    }
}
