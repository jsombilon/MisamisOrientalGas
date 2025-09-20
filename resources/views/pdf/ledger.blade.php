<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Client Ledger</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #000; padding: 4px; font-size: 11px; }
        th { background: #f0f0f0; }
        .header-table, .client-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .header-table td { border: 1px solid #000; padding: 6px; font-size: 9px; }
        .client-table td { border: 1px solid #000; padding: 6px; font-size: 11px; }
        .no-border { border: none !important; }
        .left { text-align: left; }
    </style>
</head>
<body>

    <!-- HEADER SECTION -->
    <table class="header-table">
        <tr>
            <td rowspan="2" style="width: 100px; text-align: center;"> <img src="{{ public_path('images/receipt-icon.jpg') }}" alt="Receipt Icon" style="height: 50px; width: 65px;"></td>
            <td class="left"><strong>MISAMIS ORIENTAL GASUL CENTER <br> HOME ENERGY LPG </strong></td>
            <td rowspan="2" style="width: 120px; text-align: center;">
                <strong>
                    CLIENT NUMBER<br>
                    {{ $client->client_number }}
                </strong>
            </td>
        </tr>
        <tr>
            <td class="left"> Brgy. Puerto, Cagayan de Oro City 9000, Misamis Oriental <br>  Contact / Email: 0917-729-8100 | 09399395623 | misorgascdo@gmail.com</td>
        </tr>
    </table>

    <!-- CLIENT INFO SECTION -->
    <table class="client-table">
        <tr>
            <td>CLIENT NAME<br><strong>{{ $client->client_name }}</strong></td>
            <td>CONTACT NUMBER<br><strong>{{ $client->contact ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td>ADDRESS<br><strong>{{ $client->location }}</strong></td>
            <td>CONTACT PERSON<br><strong>{{ $client->contact_person ?? '-' }}</strong></td>
        </tr>
    </table>

    <!-- FILTER INFO -->
    <p>
        <strong>Year:</strong> {{ $filterYear }}
        | <strong>Month:</strong> {{ $filterMonth }}
        | <strong>Category:</strong> {{ ucfirst($filterCategory) }}
        {{-- | <strong>Payment Type:</strong> {{ $client->payment_type }} --}}
    </p>
    <!-- LEDGER TABLE -->
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
                          
                    </td>
                </tr>
        </tbody>
    </table>  
    


</body>
</html>
