<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daily Sales Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #000; padding: 4px; text-align: left; font-size: 11px; }
        th { background: #f0f0f0; }
        h2 { margin-bottom: 5px; }
    </style>
</head>
<body>
    <h2>Daily Sales Report</h2>
    <p><strong>Date:</strong> {{ $date }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Customer Name</th>
                <th>Order Slip No</th>
                <th>Orders Made</th>
                <th>Account</th>
                <th>Cash</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{ $order['#'] }}</td>
                    <td>{{ $order['customer'] }}</td>
                    <td>{{ $order['order_slip'] }}</td>
                    <td>
                        @foreach($order['orders_made'] as $item)
                            -{{ Str::substr($item->product->product_category, 0, 1) }}
                            {{ $item->product->product_name }} x{{ $item->quantity }}<br>
                        @endforeach
                    </td>
                    <td style="text-align:right;">{{ $order['account'] ? number_format($order['account'], 2) : '' }}</td>
                    <td style="text-align:right;">{{ $order['cash'] ? number_format($order['cash'], 2) : '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Summary of Products</h3>
    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th>Product</th>
                <th>Total Qty</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productSummary as $summary)
                <tr>
                    <td>{{ $summary['category'] }}</td>
                    <td>{{ $summary['name'] }}</td>
                    <td style="text-align:right;">{{ $summary['qty'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>


    <p><strong>Total Account:</strong> ₱{{ number_format($totalAccount, 2) }}</p>
    <p><strong>Total Cash:</strong> ₱{{ number_format($totalCash, 2) }}</p>
</body>
</html>
