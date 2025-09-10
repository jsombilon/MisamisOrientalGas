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
                <div>
                    <label class="font-semibold mr-1">Year:</label>
                    <select wire:model="filterYear" class="border rounded px-3 py-1">
                        @for($y = now()->year; $y >= now()->year - 5; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="font-semibold mr-1">Month:</label>
                    <select wire:model="filterMonth" class="border rounded px-2 py-1">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="font-semibold mr-1">Category:</label>
                    <select wire:model="filterCategory" class="border rounded px-2 py-1">
                        <option value="all">All</option>
                        <option value="content">Content</option>
                        <option value="sold">Sold</option>
                    </select>
                </div>
            </div>

            {{-- Ledger Table --}}
            <div>
                <table class="min-w-full border-collapse text-xs sm:text-sm mb-4">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-2 py-1 border">Date</th>
                            <th class="px-2 py-1 border">Order Slip</th>
                            <th class="px-2 py-1 border">Debit</th>
                            <th class="px-2 py-1 border">Credit</th>
                            <th class="px-2 py-1 border">Balance</th>
                            <th class="px-2 py-1 border">Remarks</th>
                            <th class="px-2 py-1 border">Payment Type</th>
                            <th class="px-2 py-1 border">Order Made</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ledgerEntries as $entry)
                        <tr>
                            {{-- Date --}}
                            <td class="px-2 py-1 border">{{ $entry->date->format('Y-m-d') }}</td>

                            {{-- Order Slip --}}
                            <td class="px-2 py-1 border">
                                @if($entry->order)
                                    {{ $entry->order->order_slip }}
                                @elseif($entry->payment)
                                    Payment
                                @else
                                    -
                                @endif
                            </td>

                            {{-- Debit --}}
                            <td class="px-2 py-1 border text-right">
                                {{ $entry->debit ? number_format($entry->debit, 2) : '' }}
                            </td>

                            {{-- Credit --}}
                            <td class="px-2 py-1 border text-right">
                                {{ $entry->credit ? number_format($entry->credit, 2) : '' }}
                            </td>

                            {{-- Balance --}}
                            <td class="px-2 py-1 border text-right">
                                {{ number_format($entry->balance, 2) }}
                            </td>

                            {{-- Remarks --}}
                            <td class="px-2 py-1 border">
                                @if($entry->payment)
                                    {{ $entry->payment->remarks ?? '-' }}
                                @else
                                    {{ $entry->remarks }}
                                @endif
                            </td>

                            {{-- Payment Type --}}
                            <td class="px-2 py-1 border">
                                {{ $entry->payment?->payment_type ?? '-' }}
                            </td>

                            {{-- Order Made --}}
                            <td class="px-2 py-1 border">
                                @if ($entry->payment!= null)
                                    -
                                @elseif ($entry->order)
                                    @foreach($entry->order->items as $item)
                                        -{{ $item->product->product_name }} x{{ $item->quantity }}<br>
                                    @endforeach
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-gray-500 py-4">
                                No transactions found.
                            </td>
                        </tr>
                    @endforelse

                    </tbody>
                </table>

                {{-- Balance Forwarded --}}
                <div class="mb-2">
                    <strong>Balance Forwarded:</strong>
                    ₱{{ number_format($balanceForwarded, 2) }}
                </div>

                {{-- Total Balance --}}
                <div class="mb-2">
                    <strong>Total Balance:</strong>
                    ₱{{ number_format($totalBalance, 2) }}
                </div>
            </div>
        </div>
    </div>
@endif

</div>
