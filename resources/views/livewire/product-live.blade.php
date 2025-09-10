<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Products') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Product Addition') }}
                            </h2>
                        </header>
                        <div wire:key="form-{{ $formKey }}">
                            <form wire:submit.prevent="register" class="mt-6 space-y-6">
                                @csrf

                                <div class="row g-3">

                                    <div class="col-md-2">
                                        <x-input-label for="producttype" :value="__('Product Type')" />
                                        <select id="producttype" wire:model.defer="producttype" class="mt-1 block w-full border rounded-lg px-3 py-2 text-sm">
                                            <option value="">-- Select --</option>
                                            <option value="Main Product">Main Product</option>
                                            <option value="Add On">Add On</option>
                                        </select>  
                                        <x-input-error class="mt-2" :messages="$errors->get('producttype')" />
                                    </div>

                                    <div class="col-md-3">
                                        <x-input-label for="category" :value="__('Product Category')" />
                                        <select id="category" wire:model.defer="category" class="mt-1 block w-full border rounded-lg px-3 py-2 text-sm">
                                            <option value="">-- Select --</option>
                                            <option value="Content">Content</option>
                                            <option value="Sold">Sold</option>
                                        </select>  
                                        <x-input-error class="mt-2" :messages="$errors->get('category')" />
                                    </div>

                                    <div class="col-md-3">
                                        <x-input-label for="product" :value="__('Product Name')" />
                                        <x-text-input id="product" name="product" wire:model.defer="product" type="text" class="mt-1 block w-full capitalize" style="text-transform: capitalize;"  required  />
                                        <x-input-error class="mt-2" :messages="$errors->get('product')" />
                                    </div>

                                    <div class="col-md-2">
                                        <x-input-label for="kg" :value="__('Kilogram')" />
                                        <x-text-input id="kg" name="kg" wire:model.defer="kg" type="text" class="mt-1 block w-full" required  />
                                        <x-input-error class="mt-2" :messages="$errors->get('kg')" />
                                    </div>

                                     <div class="col-md-2">
                                        <x-input-label for="ext" :value="__('Extension')" />
                                        <x-text-input id="ext" name="ext" wire:model.defer="ext" type="text" class="mt-1 block w-full" required  />
                                        <x-input-error class="mt-2" :messages="$errors->get('ext')" />
                                    </div>


                                    <div class="col-md-3">
                                        <x-input-label for="price" :value="__('Unit Price')" />
                                        <x-text-input id="price" name="price" wire:model.defer="price" type="number" class="mt-1 block w-full"   />
                                        <x-input-error class="mt-2" :messages="$errors->get('price')" />
                                    </div>

                                    <div class="col-md-3">
                                        <x-input-label for="pickup" :value="__('Pick Up Price')" />
                                        <x-text-input id="pickup" name="pickup" wire:model.defer="pickup" type="number" class="mt-1 block w-full"   />
                                        <x-input-error class="mt-2" :messages="$errors->get('pickup')" />
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <x-input-label for="spu" :value="__('SPU Price')" />
                                        <x-text-input id="spu" name="spu" wire:model.defer="spu" type="number" class="mt-1 block w-full"   />
                                        <x-input-error class="mt-2" :messages="$errors->get('spu')" />
                                    </div>

                                    <div class="col-md-3">
                                        <x-input-label for="available" :value="__('Available')" />
                                        <x-text-input id="available" name="available" wire:model.defer="available" type="number" class="mt-1 block w-full"   />
                                        <x-input-error class="mt-2" :messages="$errors->get('available')" />
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-4">
                                    <x-primary-button>{{ __('Save') }}</x-primary-button>

                                    @if (session('status') === 'product-added')
                                        <p
                                            x-data="{ show: true }"
                                            x-show="show"
                                            x-transition
                                            x-init="setTimeout(() => show = false, 2000)"
                                            class="text-sm text-red-600 dark:text-red-400"
                                        >{{ __('Saved.') }}</p>
                                    @endif
                                    @if (session()->has('error'))
                                        <p
                                            x-data="{ show: true }"
                                            x-show="show"
                                            x-transition
                                            x-init="setTimeout(() => show = false, 3000)"
                                            class="text-sm text-red-600 dark:text-red-400"
                                        >{{ session('error') }}</p>
                                    @endif
                                </div>
                            </form>
                        </div>    
                    </section>
                </div>
            </div>
        </div>
    </div>

    <div class="py-1">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white text-black shadow sm:rounded-lg">
                <section>
                    <header>
                        <h2 class="text-lg font-medium text-black">
                            {{ __('Product List') }}
                        </h2>
                    </header>

                    <!-- Responsive Table Wrapper -->
                    <div class="mt-4 overflow-x-auto max-h-96 overflow-y-auto border rounded-lg">
                        <table class="min-w-full border-collapse">
                            <thead class="bg-gray-100 sticky top-0 text-xs sm:text-sm shadow-md">
                                <tr>
                                    <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Category</th>
                                    <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Item Name</th>
                                    <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Unit Price</th>
                                    <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Pick-Up Price</th>
                                    <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">SPU Price</th>
                                    <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Available</th>
                                    <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Sold</th>
                                    <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Returned</th>
                                    <th class="px-2 py-1 sm:px-4 sm:py-2 text-center font-medium border">Option</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y text-xs sm:text-sm">
                                @forelse ($products as $product)
                                        <tr class="hover:bg-gray-100">
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">{{ $product->product_category }}</td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">{{ $product->product_name }}</td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">₱{{ $product->price }}</td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">₱{{ $product->pickup }}</td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">₱{{ $product->spu }}</td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">{{ $product->available }}</td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">{{ $product->sold }}</td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">{{ $product->returned }}</td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 text-center border">
                                                <button class="px-2 py-1 sm:px-3 sm:py-1 text-[10px] sm:text-xs text-white bg-blue-600 rounded hover:bg-blue-700">Edit</button>
                                                <button class="px-2 py-1 sm:px-3 sm:py-1 text-[10px] sm:text-xs text-white bg-red-600 rounded hover:bg-red-700">Delete</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-2 py-4 text-center text-gray-500">No products found.</td>
                                        </tr>
                                    @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
