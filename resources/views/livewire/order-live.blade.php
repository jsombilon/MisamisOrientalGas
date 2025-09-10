<div>
    <x-slot name="header">
         <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Order') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div wire:key="form-{{ $formKey }}">
                        <form wire:submit.prevent="submitOrder">
                        <h2 class="text-xl font-semibold mb-6">Ordering Interface</h2>

                        <!-- First Row -->
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                            <!-- Order Slip (readonly, auto-generated) -->
                            <div>
                                <x-input-label for="order_slip" :value="__('Order Slip No.')" />
                                <x-text-input id="order_slip" type="text" class="mt-1 block w-full" wire:model="order_slip" readonly />
                            </div>

                            <!-- Client Number (dropdown) -->
                            <div>
                                <x-input-label for="client_number" :value="__('Client Number')" />
                                <select id="client_number" wire:model.lazy="client_number" class="mt-1 block w-full border rounded-lg px-3 py-2 text-sm shadow-sm">
                                    <option value="">-- Select --</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->client_number }}">
                                            {{ $client->client_number }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Client Name (auto-filled) -->
                            <div>
                                <x-input-label for="client_name" :value="__('Client Name')" />
                                <x-text-input id="client_name" type="text" class="mt-1 block w-full" wire:model="client_name" value=" {{ $client_name }}" readonly />
                            </div>

                            <!-- Price Code -->
                            <div>
                                <x-input-label for="price_code" :value="__('Price Code')" />
                                <select id="price_code" wire:model="price_code" wire:change="recalculateTotals" class="mt-1 block w-full border rounded-lg px-3 py-2 text-sm shadow-sm">
                                    <option value="">-- Select --</option>
                                        <option value="unit">Unit Price</option>
                                        <option value="pickup">Pick Up Price</option>
                                        <option value="spu">SPU Price</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="discount" :value="__('Discount')" />
                                <div class="flex gap-2">
                                    <x-text-input id="discount" name="discount" wire:model="discount" type="number" min="0" class="mt-1 block w-full" />
                                    <select wire:model="discount_type" class="mt-1 block border rounded-lg  pr-6 py-1 text-sm">
                                        <option value="fixed">₱ Fix</option>
                                        <option value="percent">% Per</option>
                                    </select>
                                </div>
                                <x-input-error class="mt-2" :messages="$errors->get('discount')" />
                            </div>

                        </div>

                        <!-- Second Row -->
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                            <div>
                                <x-input-label for="purchase_order" :value="__('Purchase Order No.')" />
                                <x-text-input id="purchase_order" name="purchase_order" wire:model.defer="purchase_order" type="number" class="mt-1 block w-full"  required  />
                                <x-input-error class="mt-2" :messages="$errors->get('purchase_order')" />
                            </div>
                            <div>
                                <x-input-label for="wwrs" :value="__('WWRS')" />
                                <x-text-input id="wwrs" name="wwrs" wire:model.defer="wwrs" type="text" class="mt-1 block w-full"  required  />
                                <x-input-error class="mt-2" :messages="$errors->get('wwrs')" />
                            </div>
                            <div>
                                <x-input-label for="truck" :value="__('Truck')" />
                                <x-text-input id="truck" name="truck" wire:model.defer="truck" type="text" class="mt-1 block w-full"  required  />
                                <x-input-error class="mt-2" :messages="$errors->get('truck')" />
                            </div>
                            <div>
                                <x-input-label for="details" :value="__('Details')" />
                                <x-text-input id="details" name="details" wire:model.defer="details" type="text" class="mt-1 block w-full"  required  />
                                <x-input-error class="mt-2" :messages="$errors->get('details')" />
                            </div>
                            <div>
                                <x-input-label for="delivery_details" :value="__('Delivery Details')" />
                                <x-text-input id="delivery_details" name="delivery_details" wire:model.defer="delivery_details" type="text" class="mt-1 block w-full"  required  />
                                <x-input-error class="mt-2" :messages="$errors->get('delivery_details')" />
                            </div>
                        </div>

                        <!-- Products Table with Filter -->
                        <div class="mb-3">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                <h3 class="text-lg font-medium mb-2 sm:mb-0">Product List</h3>

                                <!-- Filter Styled as Checkboxes -->
                                <div class="flex items-center space-x-4">
                                    <!-- All -->
                                    <label class="flex items-center space-x-1 cursor-pointer select-none">
                                        <input type="radio" wire:model="productFilter" value="all" class="sr-only">
                                        <span class="w-5 h-5 border rounded-sm flex items-center justify-center transition-all duration-200
                                                    {{ $productFilter === 'all' ? 'bg-blue-600 text-white scale-110' : 'bg-white' }}">
                                            @if($productFilter === 'all')
                                                ✓
                                            @endif
                                        </span>
                                        <span class="text-sm sm:text-base">All</span>
                                    </label>

                                    <!-- Content -->
                                    <label class="flex items-center space-x-1 cursor-pointer select-none">
                                        <input type="radio" wire:model="productFilter" value="content" class="hidden">
                                        <span class="w-5 h-5 border rounded-sm flex items-center justify-center transition-all duration-200
                                                    {{ $productFilter === 'content' ? 'bg-blue-600 text-white scale-110' : 'bg-white' }}">
                                            @if($productFilter === 'content')
                                                ✓
                                            @endif
                                        </span>
                                        <span class="text-sm sm:text-base">Content</span>
                                    </label>

                                    <!-- Sold -->
                                    <label class="flex items-center space-x-1 cursor-pointer select-none">
                                        <input type="radio" wire:model="productFilter" value="sold" class="hidden">
                                        <span class="w-5 h-5 border rounded-sm flex items-center justify-center transition-all duration-200
                                                    {{ $productFilter === 'sold' ? 'bg-blue-600 text-white scale-110' : 'bg-white' }}">
                                            @if($productFilter === 'sold')
                                                ✓
                                            @endif
                                        </span>
                                        <span class="text-sm sm:text-base">Sold</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Product Table -->
                        <div class="overflow-x-auto">
                            <div class="max-h-80 overflow-y-auto border rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-100 sticky top-0 text-xs sm:text-sm shadow-md">
                                        <tr>
                                            <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Category</th>
                                            <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Item Name</th>
                                            <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Unit Price</th>
                                            <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Pick Up Price</th>
                                            <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">SPU Price</th>
                                            <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Available</th>
                                            <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Quantity</th>
                                            <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Subtotal</th>
                                            <th class="px-2 py-1 sm:px-4 sm:py-2 font-medium text-center border">Option</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 text-xs sm:text-sm">
                                        @foreach ($products as $product)
                                            @if($productFilter === 'all' || $product->product_category === ucfirst($productFilter))
                                                <tr class="hover:bg-gray-100 transition-colors duration-200">
                                                    <td class="px-2 py-1 sm:px-4 sm:py-2 border">{{ $product->product_category }}</td>
                                                    <td class="px-2 py-1 sm:px-4 sm:py-2 border">{{ $product->product_name }}</td>
                                                    <td class="px-2 py-1 sm:px-4 sm:py-2 border">₱{{ $product->price }}</td>
                                                    <td class="px-2 py-1 sm:px-4 sm:py-2 border">₱{{ $product->pickup }}</td>
                                                    <td class="px-2 py-1 sm:px-4 sm:py-2 border">₱{{ $product->spu }}</td>
                                                    <td class="px-2 py-1 sm:px-4 sm:py-2 border">{{ $product->available }}</td>
                                                    <td class="px-2 py-1 sm:px-4 sm:py-2 border">
                                                        <input type="number" min="0"
                                                            class="w-20 border rounded px-2 py-1 text-sm"
                                                            wire:model="quantities.{{ $product->id }}"
                                                            wire:input="recalculateTotals">
                                                    </td>
                                                    <td class="px-2 py-1 sm:px-4 sm:py-2 border">
                                                        ₱{{ number_format($subtotals[$product->id] ?? 0, 2) }}
                                                    </td>
                                                    <td class="px-2 py-1 sm:px-4 sm:py-2 text-center border">
                                                        <button type="button" wire:click="$set('quantities.{{ $product->id }}', 0)"
                                                                class="px-2 py-1 sm:px-3 sm:py-1 text-[10px] sm:text-xs text-white bg-red-600 rounded hover:bg-red-700 transition-colors">
                                                            Clear
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach    
                                    </tbody>
                                </table>
                            </div>
                        </div>


                        <div class="flex justify-between items-center mt-6">
                            <div class="text-xs sm:text-sm font-semibold text-gray-700">
                                Subtotal: <span class="text-gray-800">₱{{ number_format(array_sum($subtotals), 2) }}</span><br>
                                Discount: 
                                    @if($discount_type === 'percent')
                                        <span class="text-red-600">{{ $discount }}%</span>
                                    @else
                                        <span class="text-red-600">₱{{ number_format($discount, 2) }}</span>
                                    @endif
                                <br>
                                <span class="font-bold">Total Price: <span class="text-green-600">₱{{ number_format($total, 2) }}</span></span>
                            </div>
                            <button type="button"
                                wire:click="submitOrder"
                                class="px-6 py-2 sm:px-3 sm:py-1 text-xs sm:text-sm bg-blue-600 text-white font-medium rounded-lg shadow hover:bg-blue-700">
                                Submit Order
                                
                            </button>
                            @if (session('status') === 'success')
                                    <p
                                        x-data="{ show: true }"
                                        x-show="show"
                                        x-transition
                                        x-init="setTimeout(() => show = false, 2000)"
                                        class="text-sm text-red-600 dark:text-red-400"
                                    >{{ __('Order Saved!.') }}</p>
                                @endif
                        </div>
                            <!-- Order Summary Section -->
                            @if($showSummary)
                                <div class="mt-8 border-t pt-4">
                                    <h3 class="text-lg font-semibold mb-4">Order Summary</h3>

                                    <p><strong>Order Slip:</strong> {{ $order_slip }}</p>
                                    <p><strong>Client Number:</strong> {{ $client_number }}</p>
                                    <p><strong>Client Name:</strong> {{ $client_name }}</p>
                                    <p><strong>Price Code:</strong> {{ $price_code }}</p>
                                    <p><strong>Discount:</strong> {{ $discount }} ({{ $discount_type }})</p>
                                    <p><strong>Total:</strong> ₱{{ number_format($total, 2) }}</p>

                                    <h4 class="text-md font-semibold mt-4">Products</h4>
                                    <table class="min-w-full border mt-2 text-sm">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="border px-2 py-1">Product</th>
                                                <th class="border px-2 py-1">Qty</th>
                                                <th class="border px-2 py-1">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($summaryProducts as $product)
                                                <tr>
                                                    <td class="border px-2 py-1">{{ $product['name'] }}</td>
                                                    <td class="border px-2 py-1">{{ $product['qty'] }}</td>
                                                    <td class="border px-2 py-1">₱{{ number_format($product['subtotal'], 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                    <div class="mt-4 flex gap-2">
                                        <!-- inside your summary actions -->
                                        <button type="button" wire:click="confirmSave" class="px-6 py-2 sm:px-3 sm:py-1 text-xs sm:text-sm bg-blue-600 text-white font-medium rounded-lg shadow hover:bg-blue-700">
                                            Confirm & Save
                                        </button>

                                        <!-- CANCEL should NOT submit the form -->
                                        <button type="button" wire:click="$set('showSummary', false)" class="px-6 py-2 sm:px-3 sm:py-1 text-xs sm:text-sm bg-red-600 text-white font-medium rounded-lg shadow hover:bg-red-700">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="py-1">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white text-black shadow sm:rounded-lg">
                <div class="max-w-7xl">
                    <section>
                        <header class="mb-4">
                            <h2 class="text-lg font-medium text-black">
                                {{ __('Daily Sales Report') }}
                            </h2>
                        </header>

                        <!-- Responsive Table -->
                        <div class="overflow-x-auto max-h-96 overflow-y-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-3 py-2 border">Order Slip</th>
                                        <th class="px-3 py-2 border">Client Number</th>
                                        <th class="px-3 py-2 border">Client Name</th>
                                        <th class="px-3 py-2 border">Purchase Order</th>
                                        <th class="px-3 py-2 border">Amount</th>
                                        <th class="px-3 py-2 border">Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($todayOrders as $order)
                                        <tr class="hover:bg-gray-50">
                                            <td class="border px-3 py-1">{{ $order->order_slip }}</td>
                                            <td class="border px-3 py-1">{{ $order->client->client_number }}</td>
                                            <td class="border px-3 py-1">{{ $order->client->client_name }}</td>
                                            <td class="border px-3 py-1">{{ $order->purchase_order }}</td>
                                            <td class="border px-3 py-1">₱{{ number_format($order->total, 2) }}</td>
                                            <td class="border px-3 py-1 text-center">
                                                <button wire:click="viewOrder({{ $order->id }})" class="px-2 py-1 bg-green-600 text-white rounded">View</button>

                                                @if(!$order->locked)
                                                    <button wire:click="editOrder({{ $order->id }})" class="px-2 py-1 bg-blue-600 text-white rounded">Edit</button>
                                                @else
                                                    <button class="px-2 py-1 bg-gray-600 text-white rounded">Locked</button>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-2 text-gray-500">No orders today.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{-- ================= VIEW MODE (MODAL) ================= --}}
                            @if($mode === 'view' && $selectedOrder)
                                <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm">
                                    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl p-6 relative overflow-y-auto max-h-[90vh]">

                                        <h3 class="text-lg font-semibold">Viewing Order: {{ $selectedOrder->order_slip }}</h3>

                                        <p><strong>Client:</strong> {{ $selectedOrder->client->client_name }}</p>
                                        <p><strong>Price Code:</strong> {{ ucfirst($selectedOrder->price_code) }}</p>
                                        <p><strong>Discount:</strong> 
                                            {{ $selectedOrder->discount_type === 'percent' 
                                                ? $selectedOrder->discount . '%' 
                                                : '₱' . number_format($selectedOrder->discount, 2) }}
                                        </p>
                                        <p><strong>Total:</strong> ₱{{ number_format($selectedOrder->total, 2) }}</p>

                                        <h4 class="mt-3 font-semibold">Products</h4>
                                        <ul class="list-disc ml-6">
                                            @foreach($selectedOrder->items as $item)
                                                <li>
                                                    {{ $item->product->product_name }} - Qty: {{ $item->quantity }} - 
                                                    ₱{{ number_format($item->subtotal,2) }}
                                                </li>
                                            @endforeach
                                        </ul>

                                        <div class="mt-4 flex space-x-2">
                                            @if(!$selectedOrder->locked)
                                                <button wire:click="lockOrder({{ $selectedOrder->id }})" 
                                                        class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded">
                                                    Lock Order
                                                </button>
                                            @else
                                                <span class="px-3 py-1 bg-gray-400 text-white rounded">
                                                    Order Locked
                                                </span>
                                            @endif

                                            <button wire:click="cancelEdit" 
                                                    class="px-3 py-1 bg-gray-500 hover:bg-gray-700 text-white rounded">
                                                Close
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            {{-- ================= EDIT MODE (MODAL) ================= --}}
                            @if($mode === 'edit' && $selectedOrder)
                                <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm">
                                    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl p-6 relative overflow-y-auto max-h-[90vh]">

                                        <h3 class="text-lg font-semibold">Editing Order: {{ $editOrderSlip }}</h3>

                                        <div class="mt-2">
                                            <x-input-label for="editPurchaseOrder" :value="__('Purchase Order No.')" />
                                            <x-text-input id="editPurchaseOrder" type="text" wire:model="editPurchaseOrder" />
                                        </div>

                                        <p class="mt-2"><strong>Price Code:</strong> {{ ucfirst($editPriceCode) }}</p>
                                        <p><strong>Discount:</strong> 
                                            {{ $editDiscountType === 'percent' 
                                                ? $editDiscount . '%' 
                                                : '₱' . number_format($editDiscount, 2) }}
                                        </p>

                                        <h4 class="mt-4 font-semibold">Products</h4>
                                        @foreach($products as $product)
                                            <div class="flex items-center space-x-2 mb-1">
                                                <span class="w-40">{{ $product->product_name }}</span>
                                                <input type="number" min="0" 
                                                    wire:model="editQuantities.{{ $product->id }}" 
                                                    wire:input="recalculateEditTotals" 
                                                    class="w-20 border rounded px-2 py-1">
                                                <span>₱{{ number_format($editSubtotals[$product->id] ?? 0, 2) }}</span>
                                            </div>
                                        @endforeach

                                        <div class="mt-3 font-bold">Total: ₱{{ number_format($editTotal, 2) }}</div>

                                        <div class="mt-4 flex space-x-2">
                                            <button wire:click="updateOrder" 
                                                    class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded">
                                                Save Changes
                                            </button>
                                            <button wire:click="cancelEdit" 
                                                    class="px-3 py-1 bg-gray-500 hover:bg-gray-700 text-white rounded">
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>

