<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class ProductLive extends Component
{
    public $producttype, $product,$kg, $price, $pickup, $spu, $available;
    public $formKey = 0;

     protected $rules = [
        'producttype'   => 'required|string|max:255',
        'product'        => 'required|string|max:255|unique:product_db,product_name',
        'kg'    => 'required|numeric',
        'price'    => 'nullable|numeric',
        'pickup' => 'nullable|numeric',
        'spu'  => 'nullable|numeric',
        'available'  => 'nullable|numeric',
    ];
    
        public function register()
    {
        $this->validate();

        $formattedProductName = ucwords(strtolower($this->product));
        $formattedProductName .= ' Gasul ' . $this->kg . 'kg';
        Product::create([
            'product_type'   => $this->producttype,
            'product_name'     => $formattedProductName,
            'price'        => $this->price,
            'pickup'         => $this->pickup,
            'spu'  => $this->spu,
            'available'    => $this->available,
        ]);

        // Reset values
        $this->reset(['producttype','kg', 'product', 'price', 'pickup', 'spu', 'available']);

        $this->resetValidation();

        $this->formKey++;

        session()->flash('status', 'product-added');

    }

    public function render()
    {
        return view('livewire.product-live', [
            'products' => Product::all()
        ])->layout('layouts.app');
    }
    


}
