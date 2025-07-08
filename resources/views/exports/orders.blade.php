<!DOCTYPE html>
<html>
<head>
    <title>Laporan Order</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 4px; text-align: left; }
    </style>
</head>
<body>
    <h2>Laporan Order</h2>
    <table>
        <thead>
            <tr>
                <th>No Order</th>
                <th>Tanggal</th>
                <th>Customer</th>
                <th>Sales</th>
                <th>Total</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
                <tr>
                    <td>{{ $order['Order No'] }}</td>
                    <td>{{ $order['Tanggal'] }}</td>
                    <td>{{ $order['Customer'] }}</td>
                    <td>{{ $order['Sales'] }}</td>
                    <td>Rp {{ number_format($order['Total'], 0, ',', '.') }}</td>
                    <td>{{ $order['Catatan'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>