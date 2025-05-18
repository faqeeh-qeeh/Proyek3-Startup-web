<!DOCTYPE html>
<html>
<head>
    <title>Orders Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .header { text-align: center; margin-bottom: 20px; }
        .badge {
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 12px;
            color: white;
        }
        .badge-success { background-color: #28a745; }
        .badge-warning { background-color: #ffc107; }
        .badge-danger { background-color: #dc3545; }
        .badge-secondary { background-color: #6c757d; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Orders Report</h1>
        <p>Generated on: {{ date('d M Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Order #</th>
                <th>Client</th>
                <th>Date</th>
                <th class="text-right">Amount</th>
                <th>Status</th>
                <th>Payment</th>
                <th>Items</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->order_number }}</td>
                <td>{{ $order->client->full_name }}</td>
                <td>{{ $order->created_at->format('d M Y') }}</td>
                <td class="text-right">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                <td>
                    <span class="badge badge-{{ $order->status == 'completed' ? 'success' : ($order->status == 'cancelled' ? 'danger' : 'warning') }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </td>
                <td>
                    <span class="badge badge-{{ $order->payment_status == 'paid' ? 'success' : ($order->payment_status == 'failed' ? 'danger' : 'secondary') }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </td>
                <td>{{ $order->items->count() }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>