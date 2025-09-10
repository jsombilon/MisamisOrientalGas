<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class ProductLive extends Component
{
    public $producttype,$category, $product,$kg,$ext, $price, $pickup, $spu, $available;
    public $formKey = 0;

     protected $rules = [
        'producttype'   => 'required|string|max:255',
        'product'        => 'required|string|max:255|unique:product_db,product_name',
        'category'        => 'required|string|max:255',
        'kg'    => 'required|numeric',
        'ext'        => 'nullable|string|max:255',
        'price'    => 'nullable|numeric',
        'pickup' => 'nullable|numeric',
        'spu'  => 'nullable|numeric',
        'available'  => 'nullable|numeric',
    ];
    
        public function register()
    {
        $this->validate();

        $formattedProductName = ucwords(strtolower($this->product));
        $formattedProductName .= ' Gasul ' . $this->kg . 'kg ' . $this->ext;

        if (Product::where('product_category', $this->category)
           ->where('product_name', $formattedProductName)
           ->exists()) {
            session()->flash('error', 'Duplicate product! A product with the same category and name already exists.');
            return; 
        }

        Product::create([
            'product_type'   => $this->producttype,
            'product_category'   => $this->category,
            'product_name'     => $formattedProductName,
            'kg'     => $this->kg,
            'ext'     => $this->ext,
            'price'        => $this->price,
            'pickup'         => $this->pickup,
            'spu'  => $this->spu,
            'available'    => $this->available,
        ]);

        // Reset values
        $this->reset(['producttype','category','kg', 'product','ext', 'price', 'pickup', 'spu', 'available']);

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
