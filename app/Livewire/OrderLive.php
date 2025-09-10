<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class OrderLive extends Component
{
    // Order fields
    public $order_slip;
    public $client_number = '';
    public $client_name = '';
    public $price_code;
    public $discount = 0;
    public $discount_type = 'fixed';
    public $purchase_order;
    public $wwrs;
    public $truck;
    public $details;
    public $delivery_details;

    public $clients;
    public $products;
    public $quantities = []; 
    public $subtotals = [];  
    public $total = 0;

    // Summary
    public $showSummary = false;
    public $summaryProducts = [];

    // Daily Sales Report
    public $todayOrders = [];
    public $selectedOrder;
    public $selectedOrderItems = [];
    public $mode = null;

    // For editing only
    public $editOrderSlip;
    public $editClientNumber;
    public $editClientName;
    public $editPriceCode;
    public $editDiscount = 0;
    public $editDiscountType = 'fixed';
    public $editPurchaseOrder;
    public $editWwrs;
    public $editTruck;
    public $editDetails;
    public $editDeliveryDetails;

    public $editQuantities = [];
    public $editSubtotals = [];
    public $editTotal = 0;

    public $productFilter = 'all'; // default filter


    public $formKey = 0;

    public function mount()
    {
        $this->clients = Client::all();
        $this->products = Product::orderBy('product_name', 'asc')->get();
        $this->order_slip = $this->generateOrderSlip();

        $this->loadTodayOrders();
    }

    private function generateOrderSlip()
    {
        $today = now()->format('dmY'); // e.g. 03092025
        $countToday = Order::whereDate('created_at', now())->count() + 1;
        return $today . '-' . str_pad($countToday, 2, '0', STR_PAD_LEFT);
    }

    private function loadTodayOrders()
    {
        $this->todayOrders = Order::with('client')
            ->whereDate('created_at', today())
            ->latest()
            ->get();
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'price_code' || strpos($propertyName, 'quantities') === 0) {
            $this->recalculateTotals();
        }

        if ($propertyName === 'client_number') {
            $client = Client::where('client_number', $this->client_number)->first();
            $this->client_name = $client?->client_name ?? '';
        }
    }

    public function recalculateTotals()
    {
        $this->total = 0;
        $this->subtotals = [];

        foreach ($this->products as $product) {
            $qty = (int) ($this->quantities[$product->id] ?? 0);
            $price = $this->resolveProductPrice($product, $this->price_code);

            $subtotal = $qty * $price;
            $this->subtotals[$product->id] = $subtotal;
            $this->total += $subtotal;
        }

        if ($this->discount_type === 'fixed') {
            $this->total = max($this->total - $this->discount, 0);
        } elseif ($this->discount_type === 'percent') {
            $this->total = max($this->total - ($this->total * ($this->discount / 100)), 0);
        }
    }


    public function submitOrder()
    {
        // Build summary of selected products
        $this->summaryProducts = [];

        foreach ($this->products as $product) {
            $qty = $this->quantities[$product->id] ?? 0;
            if ($qty > 0) {
                $subtotal = $this->subtotals[$product->id] ?? 0;
                $this->summaryProducts[] = [
                    'id'       => $product->id,
                    'name'     => $product->product_name,
                    'qty'      => $qty,
                    'subtotal' => $subtotal,
                ];
            }
        }

        $this->showSummary = true;
    }

    public function confirmSave()
    {
        $client = Client::where('client_number', $this->client_number)->first();

        $order = Order::create([
            'order_slip'        => $this->order_slip,
            'client_id'         => $client->id,
            'price_code'        => $this->price_code,
            'discount'          => $this->discount,
            'discount_type'     => $this->discount_type, // ✅ now saved
            'purchase_order'    => $this->purchase_order,
            'wwrs'              => $this->wwrs,
            'truck'             => $this->truck,
            'details'           => $this->details,
            'delivery_details'  => $this->delivery_details,
            'total'             => $this->total,
        ]);

        foreach ($this->quantities as $productId => $qty) {
            if ($qty > 0) {
                $product = Product::find($productId);
                $price = $this->resolveProductPrice($product, $this->price_code);

                $order->items()->create([
                    'product_id' => $productId,
                    'quantity'   => $qty,
                    'unit_price' => $price,
                    'subtotal'   => $this->subtotals[$productId] ?? 0,
                ]);
            }
        }


        $this->reset(['quantities', 'subtotals', 'total', 'showSummary', 'summaryProducts']);
        $this->order_slip = $this->generateOrderSlip();

        $this->todayOrders = Order::with('client')
            ->whereDate('created_at', today())
            ->latest()
            ->get();
            
        $this->formKey++;
        session()->flash('status', 'success');
    }

    public function render()
    {
        return view('livewire.order-live', [
            'products' => $this->products,
            'clients'  => $this->clients,
            'todayOrders' => $this->todayOrders,
        ])->layout('layouts.app');
    }

//////////// Daily Sales Report actions////////////////////////
    public function viewOrder($id)
    {
        $this->selectedOrder = Order::with('items.product', 'client')->findOrFail($id);
        $this->selectedOrderItems = $this->selectedOrder->items->toArray();
        $this->mode = 'view';
    }

    // Show order in Edit mode
    public function editOrder($id)
    {
        $order = Order::with('items.product', 'client')->findOrFail($id);

        $this->selectedOrder = $order;

        $this->editOrderSlip    = $order->order_slip;
        $this->editClientNumber = $order->client->client_number;
        $this->editClientName   = $order->client->client_name;
        $this->editPriceCode    = $order->price_code;
        $this->editDiscount     = $order->discount;
        $this->editDiscountType = $order->discount_type;
        $this->editPurchaseOrder = $order->purchase_order;
        $this->editWwrs          = $order->wwrs;
        $this->editTruck         = $order->truck;
        $this->editDetails       = $order->details;
        $this->editDeliveryDetails = $order->delivery_details;

        $this->editQuantities = [];
        foreach ($order->items as $item) {
            $this->editQuantities[$item->product_id] = $item->quantity;
            $this->editSubtotals[$item->product_id]  = $item->subtotal;
        }

        $this->recalculateEditTotals();
        $this->mode = 'edit';
    }

    public function recalculateEditTotals()
    {
        $this->editTotal = 0;
        $this->editSubtotals = [];

        foreach ($this->products as $product) {
            $qty = (int) ($this->editQuantities[$product->id] ?? 0);
            $price = $this->resolveProductPrice($product, $this->editPriceCode);

            $subtotal = $qty * $price;
            $this->editSubtotals[$product->id] = $subtotal;
            $this->editTotal += $subtotal;
        }

        if ($this->editDiscountType === 'fixed') {
            $this->editTotal = max($this->editTotal - $this->editDiscount, 0);
        } elseif ($this->editDiscountType === 'percent') {
            $this->editTotal = max($this->editTotal - ($this->editTotal * ($this->editDiscount / 100)), 0);
        }
    }


    // Save edited order
    public function updateOrder()
    {
        if (!$this->selectedOrder) return;

        $order = $this->selectedOrder;
        $order->update([
            'price_code'       => $this->editPriceCode,
            'discount'         => $this->editDiscount,
            'discount_type'    => $this->editDiscountType,
            'purchase_order'   => $this->editPurchaseOrder,
            'wwrs'             => $this->editWwrs,
            'truck'            => $this->editTruck,
            'details'          => $this->editDetails,
            'delivery_details' => $this->editDeliveryDetails,
            'total'            => $this->editTotal,
        ]);

        // Delete old items & re-save
        $order->items()->delete();
        foreach ($this->editQuantities as $productId => $qty) {
            if ($qty > 0) {
                $product = Product::find($productId);
                $price = $this->resolveProductPrice($product, $this->editPriceCode);
                $subtotal = $price * $qty;

                $order->items()->create([
                    'product_id' => $productId,
                    'quantity'   => $qty,
                    'unit_price' => $price,
                    'subtotal'   => $subtotal,
                ]);
            }
        }

        $this->resetEditState();
        $this->refreshTodayOrders();
        session()->flash('message', 'Order updated successfully!');
    }

    public function cancelEdit()
    {
        $this->reset([
            'selectedOrder', 'selectedOrderItems', 'mode',
            'editOrderSlip', 'editClientNumber', 'editClientName', 'editPriceCode',
            'editDiscount', 'editDiscountType', 'editPurchaseOrder',
            'editWwrs', 'editTruck', 'editDetails', 'editDeliveryDetails',
            'editQuantities', 'editSubtotals', 'editTotal'
        ]);
    }

    // Lock order (make uneditable + subtract stock)
    public function lockOrder($id)
    {
        $order = Order::with('items.product')->findOrFail($id);

        // Check if already locked
        if ($order->locked) {
            session()->flash('message', 'Order is already locked.');
            return;
        }

        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $product = $item->product;
                if (! $product) continue;

                // Decrease available stock
                $product->available = max(0, ($product->available ?? 0) - (int)$item->quantity);

                // Increase sold count (ensure not null)
                $product->sold = (int)($product->sold ?? 0) + (int)$item->quantity;

                $product->save();
            }

            // Mark order as locked
            $order->update(['locked' => true]);
            $order->update(['payment_status' => 'Unpaid']); // Set initial payment status
            
            // ✅ Add to client’s outstanding balance
            $order->client->increment('outstanding_balance', $order->total);
        });

        $this->refreshTodayOrders();
        session()->flash('message', 'Order locked, stock updated, and sold count increased!');
        $this->formKey++;
        $this->mode = null; 
    }



    // Reset edit state
    private function resetEditState()
    {
        $this->selectedOrder = null;
        $this->selectedOrderItems = [];
        $this->mode = null;
    }

    // Refresh daily sales list
    private function refreshTodayOrders()
    {
        $this->todayOrders = Order::with('client')
            ->whereDate('created_at', today())
            ->latest()
            ->get();
    }

    private function resolveProductPrice(Product $product, string $priceCode): float
    {
        return match ($priceCode) {
            'unit'   => $product->price,
            'pickup' => $product->pickup,
            'spu'    => $product->spu,
            default  => 0,
        };
    }

}
