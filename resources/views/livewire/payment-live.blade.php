<div>
    <x-slot name="header">
         <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Payment') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white text-black shadow sm:rounded-lg">
                <div class="max-w-7xl">
                    <section>
                        <header class="mb-4">
                            <h2 class="text-lg font-medium text-black">
                                {{ __('Payment List') }}
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
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Date</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Total</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Balance</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-center font-medium border">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y text-xs sm:text-sm">
                                    @forelse ($orders as $order)
                                        @php
                                            $totalPaid = $order->payments->sum('amount_paid');
                                            $balance = $order->total - $totalPaid;
                                        @endphp
                                        <tr class="hover:bg-gray-100">
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">{{ $order->order_slip }}</td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">{{ $order->client->client_number }}</td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">{{ $order->client->client_name }}</td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">{{ $order->created_at->format('d/m/y') }}</td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">₱{{ number_format($order->total, 2) }}</td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">₱{{ number_format($balance, 2) }}</td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 text-center border">
                                                <button wire:click="openPaymentModal({{ $order->id }})" 
                                                        class="px-2 py-1 sm:px-3 sm:py-1 text-[10px] sm:text-xs text-white bg-blue-600 rounded hover:bg-blue-700">
                                                    Pay
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-4 py-2 text-center text-gray-500">
                                                No orders available for payment.
                                            </td>
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

    <!-- ✅ Payment Modal -->
    @if($showPaymentModal && $selectedOrder)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl p-6 relative overflow-y-auto max-h-[90vh]">

                <h3 class="text-lg font-semibold mb-2">Payment for Order: {{ $selectedOrder->order_slip }}</h3>

                <p><strong>Client:</strong> {{ $selectedOrder->client->client_name }}</p>
                <p><strong>Price Code:</strong> {{ ucfirst($selectedOrder->price_code) }}</p>
                <p><strong>Discount:</strong> 
                    {{ $selectedOrder->discount_type === 'percent' 
                        ? $selectedOrder->discount . '%' 
                        : '₱' . number_format($selectedOrder->discount, 2) }}
                </p>
                <p><strong>Total:</strong> ₱{{ number_format($selectedOrder->total, 2) }}</p>

                <h4 class="mt-3 font-semibold">Products</h4>
                <ul class="list-disc ml-6 text-sm">
                    @foreach($selectedOrder->items as $item)
                        <li>
                            {{ $item->product->product_category }} {{ $item->product->product_name }} - 
                            Qty: {{ $item->quantity }} - 
                            ₱{{ number_format($item->subtotal, 2) }}
                        </li>
                    @endforeach
                </ul>

                <!-- Payment Form -->
                <div class="mt-5 border-t pt-4">
                    <h4 class="font-semibold mb-2">Enter Payment Details</h4>

                    <div class="mb-3">
                        <label class="block text-sm font-medium">Payment Amount</label>
                        <input type="number" step="0.01" wire:model="payment_amount" 
                            class="w-full border rounded px-3 py-2 text-sm" />

                        @error('payment_amount')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div x-data="{ paymentType: @entangle('payment_type') }">
                        <label class="block text-sm font-medium">Payment Type</label>
                        <select x-model="paymentType" wire:model="payment_type" class="w-full border rounded px-3 py-2 text-sm">
                            @foreach($this->availablePaymentTypes as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>

                        <div x-show="paymentType && paymentType.toLowerCase() === 'post date check'" class="mb-3 pt-3" x-transition>
                            <label class="block text-sm font-medium">Check Date</label>
                            <input type="date" wire:model="check_date" class="w-full border rounded px-3 py-2 text-sm" />
                            @error('check_date')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 pt-3">
                        <label class="block text-sm font-medium">Reference No. (Optional)</label>
                        <input type="text" wire:model="reference_no" 
                            class="w-full border rounded px-3 py-2 text-sm">
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm font-medium">Remarks (Optional)</label>
                        <textarea wire:model="remarks" 
                                class="w-full border rounded px-3 py-2 text-sm"></textarea>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-4 flex space-x-2">
                    <button wire:click="savePayment" class="px-3 py-1 bg-blue-600 text-white rounded">Save Payment</button>
                    <button wire:click="closePaymentModal" class="px-3 py-1 bg-gray-500 text-white rounded">Cancel</button>
                </div>
            </div>
        </div>
    @endif


    <div class="py-1">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white text-black shadow sm:rounded-lg">
                <div class="max-w-7xl">
                    <section>
                        <header class="mb-4">
                            <h2 class="text-lg font-medium text-black">
                                {{ __('Post Date List') }}
                            </h2>
                        </header>

                        <!-- Responsive Table -->
                        <div class="overflow-x-auto max-h-96 overflow-y-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full border-collapse text-sm sm:text-base">
                                <thead class="bg-gray-100 sticky top-0 text-xs sm:text-sm shadow-md">
                                    <tr>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Client Number</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Name</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Amount</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Ref No.</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Remarks</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Check Date</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-center font-medium border">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y text-xs sm:text-sm">
                                    @forelse($postDateChecks as $payment)
                                        <tr class="hover:bg-gray-100">
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">{{ $payment->client->client_number }}</td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">{{ $payment->client->client_name }}</td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">₱{{ number_format($payment->amount_paid, 2) }}</td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">{{ $payment->reference_no ?? '—' }}</td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">{{ $payment->remarks ?? '—' }}</td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">
                                                {{ \Carbon\Carbon::parse($payment->check_date)->format('d/m/Y') }}
                                            </td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 text-center border">
                                                <button wire:click="markCheckAsPaid({{ $payment->id }})" 
                                                    class="px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700">
                                                    Mark as Paid
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-4 py-2 text-center text-gray-500">
                                                No post-dated checks awaiting clearance.
                                            </td>
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


  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white text-black shadow sm:rounded-lg">
                <div class="max-w-7xl">
                    <section>
                        <header class="mb-4">
                            <h2 class="text-lg font-medium text-black">
                                {{ __('Payments Made') }}
                            </h2>
                        </header>

                        <!-- Responsive Table -->
                        <div class="overflow-x-auto max-h-96 overflow-y-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full border-collapse text-sm sm:text-base">
                                <thead class="bg-gray-100 sticky top-0 text-xs sm:text-sm shadow-md">
                                    <tr>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Payment Date</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Order Slip No.</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Client Name</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Ref No.</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Amount Paid</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Payment Type</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Remarks</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y text-xs sm:text-sm">
                                    @forelse($payments->filter(function($p) {
                                        // Show all except post-dated checks that are not yet Paid
                                        return !($p->payment_type === 'Post Date Check' && $p->check_status !== 'Paid');
                                    }) as $payment)
                                        <tr class="hover:bg-gray-100">
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">
                                                {{ \Carbon\Carbon::parse($payment->created_at)->format('d/m/Y') }}
                                            </td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">
                                                {{ $payment->order->order_slip ?? '—' }}
                                            </td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">
                                                {{ $payment->client->client_name }}
                                            </td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">
                                                {{ $payment->reference_no ?? '—' }}
                                            </td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">
                                                ₱{{ number_format($payment->amount_paid, 2) }}
                                            </td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">
                                                {{ $payment->payment_type }}
                                            </td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">
                                                {{ $payment->remarks ?? '—' }}
                                            </td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">
                                                {{ $payment->payment_status ?? 'Unpaid' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="px-4 py-2 text-center text-gray-500">
                                                No payments recorded yet.
                                            </td>
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

    <div class="py-1">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white text-black shadow sm:rounded-lg">
                <div class="max-w-7xl">
                    <section>
                        <header class="mb-4">
                            <h2 class="text-lg font-medium text-black">
                                {{ __('Outstanding Balance') }}
                            </h2>
                        </header>

                        <!-- Responsive Table -->
                        <div class="overflow-x-auto max-h-96 overflow-y-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full border-collapse text-sm sm:text-base">
                                <thead class="bg-gray-100 sticky top-0 text-xs sm:text-sm shadow-md">
                                    <tr>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Client Number</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Name</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Total Outstanding</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y text-xs sm:text-sm">
                                     @forelse($clientsWithBalance as $client)
                                        <tr class="hover:bg-gray-100">
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">{{ $client->client_number}}</td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">{{ $client->client_name }}</td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border text-red-600 font-semibold">
                                                ₱{{ number_format($client->outstanding_balance, 2) }}
                                            </td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 text-center border">
                                                <button wire:click="openOutstandingPaymentModal({{ $client->id }})"
                                                    class="px-2 py-1 sm:px-3 sm:py-1 text-xs text-white bg-blue-600 rounded hover:bg-blue-700">
                                                    Pay Balance
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-2 py-4 text-center text-gray-500">No outstanding balances 🎉</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{-- Outstanding Balance Payment Modal --}}
                            @if($showOutstandingModal && $outstandingClient)
                                <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm">
                                    <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 relative">
                                        <button wire:click="closeOutstandingModal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">✖</button>
                                        <h3 class="text-lg font-semibold mb-4">Pay Outstanding Balance</h3>
                                        <div class="mb-2"><strong>Client Number:</strong> {{ $outstandingClient->client_number }}</div>
                                        <div class="mb-2"><strong>Name:</strong> {{ $outstandingClient->client_name }}</div>
                                        <div class="mb-2"><strong>Location:</strong> {{ $outstandingClient->location ?? '-' }}</div>
                                        <div class="mb-2"><strong>Outstanding Balance:</strong> ₱{{ number_format($outstandingClient->outstanding_balance, 2) }}</div>   
                                        <div x-data="{ outstandingType: @entangle('outstanding_payment_type') }" class="mb-4">
                                            <label class="block text-sm font-medium mb-1">Payment Type</label>
                                            <select x-model="outstandingType" wire:model="outstanding_payment_type" class="w-full border rounded px-3 py-2 text-sm">
                                                @foreach($this->outstandingPaymentTypes as $type)
                                                    <option value="{{ $type }}">{{ $type }}</option>
                                                @endforeach
                                            </select>
                                            @error('outstanding_payment_type')
                                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                            @enderror

                                            <div x-show="outstandingType && outstandingType.toLowerCase() === 'post date check'" class="mb-4" x-transition>
                                                <label class="block text-sm font-medium mb-1">Check Date</label>
                                                <input type="date" wire:model="outstanding_check_date" class="w-full border rounded px-3 py-2 text-sm" />
                                                @error('outstanding_check_date')
                                                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium mb-1">Reference No. (Optional)</label>
                                            <input type="text" wire:model="outstanding_reference_no" class="w-full border rounded px-3 py-2 text-sm" />
                                            @error('outstanding_reference_no')
                                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium mb-1">Remarks (Optional)</label>
                                            <textarea wire:model="outstanding_remarks" class="w-full border rounded px-3 py-2 text-sm"></textarea>
                                            @error('outstanding_remarks')
                                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium mb-1">Amount</label>
                                            <input type="number" step="0.01" wire:model="outstanding_payment_amount" class="w-full border rounded px-3 py-2 text-sm" />
                                            @error('outstanding_payment_amount')
                                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="flex justify-end space-x-2">
                                            <button wire:click="saveOutstandingPayment" class="px-3 py-1 bg-blue-600 text-white rounded">Save Payment</button>
                                            <button wire:click="closeOutstandingModal" class="px-3 py-1 bg-gray-500 text-white rounded">Cancel</button>
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
