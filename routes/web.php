<?php

use App\Models\Product;
use App\Models\OrderDetails;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ProductController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    $data = Product::orderBy('created_at', 'desc')->take(9)->get();
    $products = OrderDetails::selectRaw('product_id, SUM(quantity) as total_qty')
    ->groupBy('product_id')
    ->orderByDesc('total_qty')
    ->take(3)
    ->with('product')
    ->get();
    return view('welcome', compact('products', 'data'));
});

Route::resource('product', ProductController::class);

// >>> START MODIFIKASI: Pisahkan store/show dari resource dan tambahkan route Midtrans

// Order Resource (kecuali store, karena akan dibuat rute custom di bawah)
Route::resource('order', OrderController::class)->except(['store', 'create']);

// Rute untuk membuat Order dari keranjang (Checkout - TANPA MIDDLEWARE AUTH)
Route::post('/order', [OrderController::class, 'store'])->name('order.store');

// Rute untuk menginisiasi pembayaran Midtrans (TANPA MIDDLEWARE AUTH)
Route::match(['get', 'post'], '/order/{order}/pay', [OrderController::class, 'initiatePayment'])->name('order.pay');

// Rute untuk notifikasi/webhook dari Midtrans (TANPA MIDDLEWARE AUTH)
Route::post('/payment/notification', [OrderController::class, 'notificationHandler']); 

// <<< END MODIFIKASI

Route::post('/add-to-cart', [CartController::class, 'addToCart'])->name('cart.add');
Route::get('/cart', [CartController::class, 'viewCart'])->name('cart.view');
Route::post('/cart/remove', [CartController::class, 'removeFromCart'])->name('cart.remove');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/review/index', [ReviewController::class, 'index'])->name('review.index');
Route::get('/review/create/{order}', [ReviewController::class, 'create'])->name('review.create');
Route::post('reviews/{product}', [ReviewController::class, 'store'])->name('review.store');
Route::group(['middleware' => ['auth', 'role:admin']], function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
});