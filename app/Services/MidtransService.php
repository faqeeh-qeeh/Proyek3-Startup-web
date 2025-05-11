<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use App\Models\Order;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = config('services.midtrans.is_sanitized');
        Config::$is3ds = config('services.midtrans.is_3ds');
    }

    public function createSnapToken(Order $order)
    {
        $params = [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => $order->total_amount,
            ],
            'customer_details' => [
                'first_name' => $order->client->full_name,
                'email' => $order->client->email,
                'phone' => $order->client->whatsapp_number,
            ],
            'item_details' => $this->prepareItemDetails($order),
            'callbacks' => [
                'finish' => route('client.orders.show', $order->id),
            ]
        ];

        return Snap::getSnapToken($params);
    }

    protected function prepareItemDetails(Order $order)
    {
        $items = [];
        
        foreach ($order->items as $item) {
            $items[] = [
                'id' => $item->product_id,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'name' => $item->product->name,
            ];
        }

        return $items;
    }

    public function handleNotification()
    {
        $notification = new Notification();
        
        $order = Order::where('order_number', $notification->order_id)->first();
        
        if (!$order) {
            return ['status' => 'error', 'message' => 'Order not found'];
        }

        $transactionStatus = $notification->transaction_status;
        $fraudStatus = $notification->fraud_status;

        // Handle status pembayaran
        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'challenge') {
                $order->payment_status = 'pending';
            } else if ($fraudStatus == 'accept') {
                $order->payment_status = 'paid';
            }
        } else if ($transactionStatus == 'settlement') {
            $order->payment_status = 'paid';
        } else if ($transactionStatus == 'pending') {
            $order->payment_status = 'pending';
        } else if ($transactionStatus == 'deny' || 
                  $transactionStatus == 'expire' || 
                  $transactionStatus == 'cancel') {
            $order->payment_status = 'failed';
        }

        $order->midtrans_transaction_id = $notification->transaction_id;
        $order->save();

        return ['status' => 'success', 'order' => $order];
    }
    
}