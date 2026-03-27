<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderLog;
use Illuminate\Http\Request;
use App\Models\OrderStatusLog;
use Illuminate\Support\Facades\DB;
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;
use Midtrans\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; 

class OrderController extends Controller
{
    public function index()
    {
        $order = Order::with('orderDetails')->paginate(6);
        return view('order.admin.index', compact('order'));
    }

    public function create()
    {
        $product = Product::all();
        return view('order.create', compact('product'));
    }
    public function store(Request $request)
    {
        $products = collect($request->input('products'))->filter(function ($product) {
            return isset($product['id']) && isset($product['quantity']) && $product['quantity'] > 0;
        });

        if ($products->isEmpty()) {
            return redirect()->back()->withErrors(['products' => 'No valid products selected.']);
        }

        $validatedData = $request->validate([
            'username' => 'required|string|max:255',
        ]);
        
        try {
            DB::beginTransaction();
            
            $order = Order::create([
                'username' => $validatedData['username'],
                'order_code' => strtoupper('ORD-' . uniqid()),
                'total_price' => 0,
                'status' => 'unpaid',
            ]);

            $totalPrice = 0;

            foreach ($products as $productData) {
                $product = Product::findOrFail($productData['id']);
                $quantity = $productData['quantity'];
                $subtotal = $product->price * $quantity;

                $order->orderDetails()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price,
                    'subtotal' => $subtotal,
                ]);

                $totalPrice += $subtotal;
            }

            $order->update(['total_price' => $totalPrice]);

            session()->forget('cart');

            DB::commit();

            return redirect()->route('order.pay', $order->id)->with('success', 'Order created. Please complete payment.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membuat pesanan: ' . $e->getMessage());
        }
    }


    /*
    //Render order track
    */
    public function show($id)
    {
        
        $order = Order::with('OrderDetails.product')->findOrFail($id);
        $orderLogs = OrderLog::where('order_id', $order->id)->orderBy('created_at', 'asc')->get();

        return view('order.show', compact('order', 'orderLogs'));
    }

    /*
    // initiatePayment 
    */
    public function initiatePayment(Request $request, Order $order)
    {
        if ($order->status !== 'unpaid') {
             return redirect()->route('order.show', $order->id)->with('error', 'Pesanan sudah dibayar atau tidak valid.');
        }

        MidtransConfig::$serverKey = config('services.midtrans.server_key');
        MidtransConfig::$isProduction = config('services.midtrans.is_production');
        MidtransConfig::$isSanitized = true;
        MidtransConfig::$is3ds = true;

        // ... (Logika detail transaksi dan pelanggan) ...
        $transaction_details = [ 'order_id' => $order->id . '-' . time(), 'gross_amount' => (int) $order->total_price ];
        $customer_details = [ 'first_name' => $order->username, 'email' => 'guest-' . $order->id . '@coffeeshop.com', 'phone' => '08123456789' ];
        $item_details = $order->orderDetails->map(function ($detail) {
            return [
                'id' => $detail->product_id,
                'price' => (int) $detail->price,
                'quantity' => (int) $detail->quantity,
                'name' => $detail->Product->name ?? 'Produk Kopi', 
            ];
        })->toArray();
        
        $midtrans_params = [
            'transaction_details' => $transaction_details,
            'customer_details' => $customer_details,
            'item_details' => $item_details,
            'callbacks' => [
                'finish' => route('order.show', $order->id), 
                'error' => route('order.show', $order->id),
                'pending' => route('order.show', $order->id),
            ]
        ];

        try {
            $snapToken = Snap::getSnapToken($midtrans_params);

            $order->update(['snap_token' => $snapToken, 'payment_method' => 'midtrans_snap']);

            return view('order.payment', compact('snapToken', 'order'));

        } catch (\Exception $e) {
            return redirect()->route('order.show', $order->id)->with('error', 'Gagal memuat Midtrans: ' . $e->getMessage());
        }
    }


   
    public function notificationHandler(Request $request)
    {
        
        try {
            DB::transaction(function () use ($request) {
                
                
                MidtransConfig::$serverKey = config('services.midtrans.server_key');
                MidtransConfig::$isProduction = config('services.midtrans.is_production');
                MidtransConfig::$isSanitized = true;
                
                
                $notification = new Notification();

                $transactionStatus = $notification->transaction_status;
                $orderIdWithTimestamp = $notification->order_id;
                $transactionId = $notification->transaction_id;
                $fraudStatus = $notification->fraud_status;

                
                $orderId = explode('-', $orderIdWithTimestamp)[0]; 
                $order = Order::find($orderId);

                if (!$order) {
                    
                    throw new \Exception("Order ID {$orderId} not found in database.");
                }

                $newStatus = $order->status;
                
                
                if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                    
                    if ($fraudStatus == 'accept') {
                        $newStatus = 'paid'; 
                    }
                } else if ($transactionStatus == 'pending') {
                    
                    $newStatus = 'unpaid'; 
                } else if ($transactionStatus == 'deny' || $transactionStatus == 'cancel' || $transactionStatus == 'expire') {
                    
                    $newStatus = 'cancelled';
                }
                
                
                if ($newStatus !== $order->status && $order->status === 'unpaid') {
                    $order->midtrans_transaction_id = $transactionId;
                    $order->status = $newStatus;
                    $order->save();
                    
                    
                }

            });
            
            
            return response('OK', 200);

        } catch (\Exception $e) {
            
            Log::error("Midtrans Webhook Failed: {$e->getMessage()}", ['request' => $request->all()]);
            
            
            return response('Internal Server Error', 500); 
        }
    }
    
}