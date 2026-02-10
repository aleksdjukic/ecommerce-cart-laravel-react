<h1>Order Confirmation</h1>

<p><strong>Order #:</strong> {{ $order->id }}</p>
<p><strong>Total:</strong> {{ number_format($order->total_price, 2) }}</p>

<table cellpadding="6" cellspacing="0" border="1">
    <thead>
        <tr>
            <th align="left">Product</th>
            <th align="right">Price</th>
            <th align="right">Qty</th>
            <th align="right">Line total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($order->items as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td align="right">{{ number_format($item->price, 2) }}</td>
                <td align="right">{{ $item->quantity }}</td>
                <td align="right">{{ number_format($item->price * $item->quantity, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
