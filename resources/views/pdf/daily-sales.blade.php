<h1>Daily Sales Report ({{ $date->format('d.m.Y') }})</h1>

<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Total</th>
    </tr>

    @foreach($orders as $order)
        <tr>
            <td>{{ $order->id }}</td>
            <td>{{ $order->total_price }}</td>
        </tr>
    @endforeach
</table>

<h3>Total revenue: {{ $total }}</h3>
