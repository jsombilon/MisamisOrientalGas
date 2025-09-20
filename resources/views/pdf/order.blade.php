<!DOCTYPE html>
<html>
<head>
    <title>Order PDF</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px;}
        th, td { border: 1px solid #333; padding: 6px; }
    </style>
</head>
<body>
    <h2>Order Slip: {{ $order->order_slip }}</h2>
    <p><strong>Client Number:</strong> {{ $order->client->client_number }}</p>
    <p><strong>Client Name:</strong> {{ $order->client->client_name }}</p>
    <p><strong>Price Code:</strong> {{ $order->price_code }}</p>
    <p><strong>Discount:</strong> {{ $order->discount }} ({{ $order->discount_type }})</p>
    <p><strong>Total:</strong> ₱{{ number_format($order->total, 2) }}</p>
    <p><strong>Purchase Order:</strong> {{ $order->purchase_order }}</p>
    <p><strong>WWRS:</strong> {{ $order->wwrs }}</p>
    <p><strong>Truck:</strong> {{ $order->truck }}</p>
    <p><strong>Details:</strong> {{ $order->details }}</p>
    <p><strong>Delivery Details:</strong> {{ $order->delivery_details }}</p>

    <h3>Products Ordered</h3>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product->product_category }} {{ $item->product->product_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>₱ {{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>