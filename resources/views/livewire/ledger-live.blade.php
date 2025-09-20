<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ledger') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white text-black shadow sm:rounded-lg">
                <div class="max-w-7xl">
                    <section>
                        <header class="mb-4">
                            <h2 class="text-lg font-medium text-black">
                                {{ __('Ledger List') }}
                            </h2>
                        </header>

                        <!-- Responsive Table -->
                        <div class="overflow-x-auto max-h-96 overflow-y-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full border-collapse text-sm sm:text-base">
                                <thead class="bg-gray-100 sticky top-0 text-xs sm:text-sm shadow-md">
                                    <tr>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Client Number</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Name</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Address</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-center font-medium border">Option</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y text-xs sm:text-sm">
                                    @forelse($clients as $client)
                                        <tr class="hover:bg-gray-100">
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">{{ $client->client_number }}</td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">{{ $client->client_name }}</td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">{{ $client->location }}</td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 text-center border">
                                                <button wire:click="openLedgerModal({{ $client->id }})"
                                                    class="px-2 py-1 sm:px-3 sm:py-1 text-[10px] sm:text-xs text-white bg-blue-600 rounded hover:bg-blue-700">
                                                    View
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-gray-500 py-4">No clients found.</td>
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
@if($showLedgerModal && $selectedClient)
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-6xl p-8 relative overflow-y-auto max-h-[90vh]">
        
        {{-- Close Button --}}
        <button wire:click="closeLedgerModal"
            class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-2xl">✖</button>

        {{-- Client Info --}}
        <div class="mb-6">
            <h3 class="text-xl font-bold mb-2">Client Ledger</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 mb-2">
                <div><strong>Client Number:</strong> {{ $selectedClient->client_number }}</div>
                <div><strong>Name:</strong> {{ $selectedClient->client_name }}</div>
                <div><strong>Address:</strong> {{ $selectedClient->location }}</div>
                <div><strong>Payment Type:</strong> {{ $selectedClient->payment_type }}</div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="mb-4 flex flex-wrap gap-4 items-center">
            <!-- Year -->
            <div>
                <label class="font-semibold mr-1">Year:</label>
                <select wire:model="filterYear" class="form-select w-full border rounded">
                    @for ($y = now()->year; $y >= 2000; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <!-- Month -->
            <div>
                <label class="font-semibold mr-1">Month</label>
                <select wire:model="filterMonth" class="form-select w-full border rounded">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                    @endfor
                </select>
            </div>

            <!-- Category -->
            <div>
                <label class="font-semibold mr-1">Category</label>
                <select wire:model="filterCategory" class="form-select w-full border rounded">
                    <option value="all">All</option>
                    <option value="order">Orders</option>
                    <option value="payment">Payments</option>
                </select>
            </div>

            <!-- Generate button -->
            <div class="self-end">
                <button
                    wire:click="generateLedger"
                    wire:loading.attr="disabled"
                    class="mt-2 px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700"
                >
                    <span wire:loading.remove>Generate</span>
                    <span wire:loading>Generating…</span>
                </button>
            </div>
        </div>

        {{-- Ledger Table --}}
        <div>
            <table class="min-w-full border-collapse text-xs sm:text-sm mb-4">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-2 py-1 border" colspan="9">Summary of Accounts</th>
                    </tr>
                </thead>
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-2 py-1 border">Date</th>
                        <th class="px-2 py-1 border">Order Slip</th>
                        <th class="px-2 py-1 border">Debit</th>
                        <th class="px-2 py-1 border">Credit</th>
                        <th class="px-2 py-1 border">Balance</th>
                        <th class="px-2 py-1 border">Remarks</th>
                        <th class="px-2 py-1 border">Payment Type</th>
                        <th class="px-2 py-1 border">Payment Date</th>
                        <th class="px-2 py-1 border">Order Made</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Balance Forwarded --}}
                    <tr class="bg-gray-50 font-semibold">
                        <td class="px-2 py-1 border text-right" colspan="4">Balance Forwarded</td>
                        <td class="px-2 py-1 border text-right">{{ number_format($balanceForwarded, 2) }}</td>
                        <td class="px-2 py-1 border" colspan="4"></td>
                    </tr>

                    @forelse($ledgerEntries as $entry)
                    <tr>
                        {{-- Date --}}
                        <td class="px-2 py-1 border">
                            @if($entry['payment'] && strtolower($entry['payment']->payment_type) === 'post date check' && $entry['payment']->check_date)
                                {{ \Carbon\Carbon::parse($entry['payment']->check_date)->format('Y-m-d') }}
                            @elseif($entry['payment'])
                                {{ $entry['payment']->created_at->format('Y-m-d') }}
                            @else
                                {{ $entry['date']->format('Y-m-d') }}
                            @endif
                        </td>

                        {{-- Order Slip --}}
                        <td class="px-2 py-1 border">
                            @if($entry['payment'])
                                - {{-- payments never show order slip --}}
                            @elseif($entry['order'])
                                {{ $entry['order']->order_slip }}
                            @else
                                -
                            @endif
                        </td>

                        {{-- Debit --}}
                        <td class="px-2 py-1 border text-right">
                            {{ $entry['debit'] ? number_format($entry['debit'], 2) : '' }}
                        </td>

                        {{-- Credit --}}
                        <td class="px-2 py-1 border text-right">
                            {{ $entry['credit'] ? number_format($entry['credit'], 2) : '' }}
                        </td>

                        {{-- Balance --}}
                        <td class="px-2 py-1 border text-right">
                            {{ number_format($entry['balance'], 2) }}
                        </td>

                        {{-- Remarks --}}
                        <td class="px-2 py-1 border">
                            {{ $entry['remarks'] ?? '-' }}
                        </td>

                        {{-- Payment Type --}}
                        <td class="px-2 py-1 border">
                            {{ $entry['payment']?->payment_type ?? '-' }}
                        </td>

                        {{-- Payment Date --}}
                        <td class="px-2 py-1 border">
                            @if($entry['payment'] && strtolower($entry['payment']->payment_type) === 'post date check' && $entry['payment']->check_date)
                                {{ \Carbon\Carbon::parse($entry['payment']->check_date)->format('Y-m-d') }}
                            @elseif($entry['payment'])
                                {{ $entry['payment']->created_at->format('Y-m-d') }}
                            @endif
                        </td>

                        {{-- Order Made --}}
                        <td class="px-2 py-1 border">
                            @if($entry['payment'])
                                - {{-- payments never show orders made --}}
                            @elseif($entry['order'])
                                @foreach($entry['order']->items as $item)
                                    -{{ Str::substr($item->product->product_category, 0, 1) }}
                                    {{ $item->product->product_name }} x{{ $item->quantity }}<br>
                                @endforeach
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-gray-500 py-4">
                            No transactions found.
                        </td>
                    </tr>
                @endforelse


                    {{-- Totals --}}
                    <tr class="bg-gray-100 font-semibold">
                        <td class="px-2 py-1 border text-right" colspan="4">Total Balance</td>
                        <td class="px-2 py-1 border text-right">{{ number_format($totalBalance, 2) }}</td>
                        <td class="px-2 py-1 border" colspan="4">
                            <button 
                                wire:click="printLedger({{ $selectedClient->id }})"
                                class="px-3 py-1 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700 inline-flex items-center"
                            >
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                Print
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif


</div>
