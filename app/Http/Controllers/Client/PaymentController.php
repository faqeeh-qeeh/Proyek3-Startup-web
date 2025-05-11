<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:client');

        // Set Midtrans configuration
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = config('services.midtrans.is_sanitized');
        Config::$is3ds = config('services.midtrans.is_3ds');
    }

    public function create(Order $order)
    {
        if ($order->client_id !== auth('client')->id() || $order->payment_status !== 'unpaid') {
            abort(403);
        }

        return view('client.payments.create', compact('order'));
    }

    public function store(Request $request, Order $order)
    {
        if ($order->client_id !== auth('client')->id() || $order->payment_status !== 'unpaid') {
            abort(403);
        }

        $client = auth('client')->user();

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
            
            return view('client.payments.checkout', compact('snapToken', 'order'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Payment gateway error: ' . $e->getMessage());
        }
    }

    public function handleNotification(Request $request)
    {
        $payload = $request->getContent();
        $notification = json_decode($payload, true);

        $validSignatureKey = hash('sha512', 
            $notification['order_id'] . 
            $notification['status_code'] . 
            $notification['gross_amount'] . 
            config('services.midtrans.server_key')
        );

        if ($notification['signature_key'] !== $validSignatureKey) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $order = Order::where('order_number', $notification['order_id'])->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $transactionStatus = $notification['transaction_status'];
        $fraudStatus = $notification['fraud_status'];

        // Handle notification status
        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'challenge') {
                $order->update(['payment_status' => 'pending']);
            } else if ($fraudStatus == 'accept') {
                $order->update([
                    'payment_status' => 'paid',
                    'payment_method' => $notification['payment_type'],
                    'midtrans_transaction_id' => $notification['transaction_id'],
                ]);
            }
        } else if ($transactionStatus == 'settlement') {
            $order->update([
                'payment_status' => 'paid',
                'payment_method' => $notification['payment_type'],
                'midtrans_transaction_id' => $notification['transaction_id'],
            ]);
        } else if ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
            $order->update(['payment_status' => 'failed']);
        } else if ($transactionStatus == 'pending') {
            $order->update(['payment_status' => 'pending']);
        }

        return response()->json(['message' => 'Notification handled']);
    }

    public function success(Order $order)
    {
        if ($order->client_id !== auth('client')->id()) {
            abort(403);
        }
    
        // Verifikasi status pembayaran langsung ke Midtrans
        try {
            Config::$serverKey = config('services.midtrans.server_key');
            Config::$isProduction = config('services.midtrans.is_production');
            Config::$isSanitized = config('services.midtrans.is_sanitized');
            Config::$is3ds = config('services.midtrans.is_3ds');
    
            $status = \Midtrans\Transaction::status($order->order_number);
            
            // Update status berdasarkan response
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
        } catch (\Exception $e) {
            // Log error jika perlu
            \Log::error('Midtrans status check error: ' . $e->getMessage());
        }
    
        return view('client.payments.success', compact('order'));
    }

    public function failure(Order $order)
    {
        if ($order->client_id !== auth('client')->id()) {
            abort(403);
        }

        return view('client.payments.failure', compact('order'));
    }
    public function checkStatus(Order $order)
    {
        if ($order->client_id !== auth('client')->id()) {
            abort(403);
        }

        // Skip jika sudah paid
        if ($order->payment_status == 'paid') {
            return redirect()->back()->with('info', 'Payment already confirmed');
        }

        // Gunakan Midtrans API untuk cek status terbaru
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');

        try {
            $status = \Midtrans\Transaction::status($order->order_number);

            // Proses sama seperti handleNotification
            $this->processStatusUpdate($order, $status);

            return redirect()->back()->with('success', 'Payment status updated');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to check status: '.$e->getMessage());
        }
    }
    public function processStatusUpdate(Order $order)
    {
        try {
            Config::$serverKey = config('services.midtrans.server_key');
            Config::$isProduction = config('services.midtrans.is_production');
            
            $status = \Midtrans\Transaction::status($order->order_number);
            
            if ($status->transaction_status == 'settlement' || $status->transaction_status == 'capture') {
                $order->update([
                    'payment_status' => 'paid',
                    'payment_method' => $status->payment_type,
                    'midtrans_transaction_id' => $status->transaction_id,
                ]);
                return 'paid';
            } elseif ($status->transaction_status == 'pending') {
                $order->update(['payment_status' => 'pending']);
                return 'pending';
            } else {
                $order->update(['payment_status' => 'failed']);
                return 'failed';
            }
        } catch (\Exception $e) {
            \Log::error('Status update error: ' . $e->getMessage());
            return 'error';
        }
    }

}