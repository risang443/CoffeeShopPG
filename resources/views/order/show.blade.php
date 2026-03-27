@extends('layout.pages')
@section('content')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
@endpush

<div class="container my-5">
    <h2 class="text-center mb-4">Track Your Order Status</h2>
    
    {{-- Notifikasi Error/Success --}}
    @if (session('error'))
        <div class="alert alert-danger text-center">{{ session('error') }}</div>
    @endif

    {{-- >>> START MODIFIKASI: Tampilkan status dan tombol bayar (Guest Friendly) --}}
    @if ($order->status == 'unpaid')
        <div class="alert alert-warning text-center p-4 mb-5 shadow-sm">
            <h4>⚠️ Pesanan Belum Dibayar!</h4>
            <p class="lead">Total yang harus dibayar: <strong>Rp {{ number_format($order->total_price, 0, ',', '.') }}</strong></p>
            
            {{-- Form untuk menginisiasi/melanjutkan pembayaran Midtrans --}}
            <form action="{{ route('order.pay', $order->id) }}" method="POST" class="mt-3">
                @csrf
                <button type="submit" class="btn btn-danger btn-lg">
                    <i class="bi bi-wallet"></i> Lanjutkan Pembayaran Sekarang
                </button>
            </form>
            <p class="mt-2 text-muted">Akan membuka jendela pembayaran online Midtrans.</p>
        </div>
    @elseif ($order->status == 'paid')
        <div class="alert alert-success text-center p-4 mb-5 shadow-sm">
            <h4>✅ Pembayaran Berhasil!</h4>
            <p>Pesanan Anda telah lunas dan statusnya akan segera diperbarui menjadi PROCEED.</p>
        </div>
    @endif
    {{-- <<< END MODIFIKASI --}}

    <div class="timeline">
        @foreach ($orderLogs as $log)
            <div class="timeline-step card border border-success p-3 mt-5 {{ $log->new_status == $order->status ? 'active' : '' }}">
                <div class="timeline-icon">
                    @if ($log->new_status == $order->status)
                        <i class="bi bi-check-circle-fill text-success"></i>
                    @else
                        <i class="bi bi-clock text-muted"></i>
                    @endif
                </div>
                <div class="timeline-content">
                    <h5 class="fw-bold mb-1">{{ strtoupper($log->new_status) }}</h5>
                    <p class="mb-1">
                        @switch($log->new_status)
                            @case('unpaid')
                                Waiting for payment.
                                @break
                            @case('paid')
                                Your payment has been received.
                                @break
                            @case('proceed')
                                Your order is being prepared.
                                @break
                            @case('completed')
                                Order completed!
                                @break
                            @case('cancelled')
                                Order cancelled.
                                @break
                        @endswitch
                    </p>
                    <small class="text-muted">{{ $log->created_at->format('d M Y, H:i') }}</small>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Tambahkan detail order di sini agar lebih informatif --}}
    <div class="card my-5 shadow-sm">
        <div class="card-body">
            <h5>Detail Pembayaran</h5>
            <ul>
                <li>Total Harga: <strong>Rp {{ number_format($order->total_price, 0, ',', '.') }}</strong></li>
                <li>Nama Pelanggan: <strong>{{ $order->username }}</strong></li>
                <li>Metode Pembayaran: {{ $order->payment_method ?? 'Belum Dibayar' }}</li>
                <li>ID Midtrans: {{ $order->midtrans_transaction_id ?? '-' }}</li>
            </ul>
        </div>
    </div>


    <div class="text-center mt-4">
        @if ($order->status == 'completed')
            <a href="{{ route('review.create', ['order' => $order->id]) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Write a Review
            </a>
        @else
            <a href="{{ route('order.index') }}" class="btn btn-success">+ Place New Order</a>
        @endif
        <a href="/" class="btn btn-outline-secondary mt-2">
            <i class="bi bi-house"></i> Home
        </a>
    </div>

</div>

@endsection

@section('styles')
<style>
    .timeline {
        /* ... (CSS yang sudah ada) ... */
    }
    /* ... (CSS yang sudah ada) ... */
</style>
@endsection