<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');

        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = config('services.midtrans.is_sanitized');
        Config::$is3ds = config('services.midtrans.is_3ds');
    }

    public function create(Order $order)
    {
        if ($order->client_id !== auth()->id() || $order->payment_status !== 'unpaid') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $client = auth()->user();

        $params = [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => $order->total_amount,
            ],
            'customer_details' => [
                'first_name' => $client->full_name,
                'email' => $client->email,
                'phone' => $client->whatsapp_number,
            ],
            'enabled_payments' => ['gopay', 'bank_transfer', 'credit_card'],
            'expiry' => [
                'start_time' => date('Y-m-d H:i:s T'),
                'unit' => 'hours',
                'duration' => 24,
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            $order->update(['midtrans_token' => $snapToken]);
            
            return response()->json([
                'snap_token' => $snapToken,
                'order' => $order
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Payment gateway error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function checkStatus(Order $order)
    {
        if ($order->client_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $status = \Midtrans\Transaction::status($order->order_number);
            
            if ($status->transaction_status == 'settlement' || $status->transaction_status == 'capture') {
                $order->update([
                    'payment_status' => 'paid',
                    'payment_method' => $status->payment_type,
                    'midtrans_transaction_id' => $status->transaction_id,
                ]);
            } elseif ($status->transaction_status == 'pending') {
                $order->update(['payment_status' => 'pending']);
            } elseif (in_array($status->transaction_status, ['deny', 'cancel', 'expire'])) {
                $order->update(['payment_status' => 'failed']);
            }

            return response()->json([
                'payment_status' => $order->payment_status,
                'order' => $order
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to check status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}