<?php

namespace App\Models;

use App\Models\OrderDetails;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User; // Tambahkan use statement untuk Model User

class Order extends Model
{
    use HasFactory;

    // >>> START MODIFIKASI
    protected $fillable = ['total_price', 'username', 'order_code', 'status', 'payment_method', 'midtrans_transaction_id', 'snap_token'];
    // <<< END MODIFIKASI

    public function OrderDetails()
    {
        return $this->hasMany(OrderDetails::class);
    }
    
    // Tambahkan relasi ke User (asumsi order dibuat oleh User yang login)
    public function user()
    {
        return $this->belongsTo(User::class, 'username', 'username'); // Asumsikan Anda memiliki kolom username di tabel users
        // CATATAN: Jika Anda menggunakan ID user, ganti kolomnya
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function Review()
    {
        return $this->hasMany(Review::class, 'order_id', 'id');
    }
    public function logs()
    {
        return $this->hasMany(OrderLog::class);
    }
}