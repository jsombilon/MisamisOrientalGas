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
                    <h2 class="text-xl font-semibold mb-6">Ordering Interface</h2>

                    <!-- First Row -->
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                        <div>
                            <x-input-label for="order_slip" :value="__('Order Slip No.')" />
                            <x-text-input id="order_slip" name="order_slip" wire:model.defer="order_slip" type="number" class="mt-1 block w-full"  required  />
                            <x-input-error class="mt-2" :messages="$errors->get('order_slip')" />
                        </div>
                        <div>
                            <x-input-label for="client_number" :value="__('Client Number')" />
                            <x-text-input id="client_number" name="client_number" wire:model.defer="client_number" type="number" class="mt-1 block w-full"  required  />
                            <x-input-error class="mt-2" :messages="$errors->get('client_number')" />
                        </div>
                        <div>
                            <x-input-label for="client_name" :value="__('Client Name')" />
                            <x-text-input id="client_name" name="client_name" wire:model.defer="client_name" type="text" class="mt-1 block w-full"  required  />
                            <x-input-error class="mt-2" :messages="$errors->get('client_name')" />
                        </div>
                        <div>
                            <x-input-label for="price_code" :value="__('Price Code')" />
                            <select id="price_code" wire:model.defer="price_code" class="mt-1 block w-full border rounded-lg px-3 py-2 text-sm shadow-sm">
                                <option value="">-- Select --</option>
                                <option value="Unit Price">Unit Price</option>
                                <option value="Pick Up Price">Pick Up Price</option>
                                <option value="SPU Price">SPU Price</option>
                            </select>  
                            <x-input-error class="mt-2" :messages="$errors->get('price_code')" />
                        </div>
                        <div>
                            <x-input-label for="discount" :value="__('Content Discount')" />
                            <x-text-input id="discount" name="discount" wire:model.defer="discount" type="number" class="mt-1 block w-full"  required  />
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

                    <!-- Products Table -->
                    <h3 class="text-lg font-medium mb-3">Petron Products</h3>
                    <div class="overflow-x-auto">
                        <div class="max-h-80 overflow-y-auto border rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-100 sticky top-0 text-xs sm:text-sm shadow-md">
                                    <tr>
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
                                    @forelse ($products as $product)
                                        <tr class="hover:bg-gray-100">
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">{{ $product->product_name }}</td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">₱{{ $product->price }}</td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">₱{{ $product->pickup }}</td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">₱{{ $product->spu }}</td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">{{ $product->available }}</td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">
                                                <input type="number" class="w-20 border rounded px-2 py-1 text-sm" value="0">
                                            </td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">₱0.00</td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 text-center border">
                                                <button class="px-2 py-1 sm:px-3 sm:py-1 text-[10px] sm:text-xs text-white bg-red-600 rounded hover:bg-red-700">Clear</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-2 py-4 text-center text-gray-500">No clients found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="flex justify-between items-center mt-6">
                        <!-- Total Price -->
                        <div class="text-xs sm:text-sm font-semibold text-gray-700">
                            Total Price: <span id="total-price" class="text-green-600">₱0.00</span>
                        </div>

                        <!-- Submit Button -->
                        <button class="px-6 py-2 sm:px-3 sm:py-1 text-xs sm:text-sm bg-blue-600 text-white font-medium rounded-lg shadow hover:bg-blue-700">
                            Submit Order
                        </button>
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
                            <table class="min-w-full border-collapse text-sm sm:text-base">
                                <thead class="bg-gray-100 sticky top-0 text-xs sm:text-sm shadow-md">
                                    <tr>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Order Slip No.</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Client Number</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Name</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Purchase Order No.</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Price Type</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Amount</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-center font-medium border">Option</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y text-xs sm:text-sm">
                                    <tr class="hover:bg-gray-100">
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">1111</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">0001</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">Juan Dela Cruz</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">0001</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">31/08/25</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">₱3,000</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 text-center border">
                                            <button class="px-2 py-1 sm:px-3 sm:py-1 text-[10px] sm:text-xs text-white bg-blue-600 rounded hover:bg-blue-700">View</button>
                                            <button class="px-2 py-1 sm:px-3 sm:py-1 text-[10px] sm:text-xs text-white bg-red-600 rounded hover:bg-red-700">Edit</button>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-gray-100">
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">2222</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">0002</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">Pedro Santos</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">0002</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">31/08/25</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">₱2,000</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 text-center border">
                                            <button class="px-2 py-1 sm:px-3 sm:py-1 text-[10px] sm:text-xs text-white bg-blue-600 rounded hover:bg-blue-700">View</button>
                                            <button class="px-2 py-1 sm:px-3 sm:py-1 text-[10px] sm:text-xs text-white bg-red-600 rounded hover:bg-red-700">Edit</button>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-gray-100">
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">3333</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">0003</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">Jose Rizal</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">0003</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">31/08/25</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">₱1,000</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 text-center border">
                                            <button class="px-2 py-1 sm:px-3 sm:py-1 text-[10px] sm:text-xs text-white bg-blue-600 rounded hover:bg-blue-700">View</button>
                                            <button class="px-2 py-1 sm:px-3 sm:py-1 text-[10px] sm:text-xs text-white bg-red-600 rounded hover:bg-red-700">Edit</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>
