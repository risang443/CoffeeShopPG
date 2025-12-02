@extends('layout.pages') 

@section('content')
@push('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
@endpush

<div class="container my-5 text-center">
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    
    <h2 class="mb-4">Selesaikan Pembayaran</h2>

    @if ($order->status == 'paid')
        <div class="alert alert-success">Pesanan **#{{ $order->order_code }}** sudah **LUNAS**!</div>
        <a href="{{ route('order.show', $order->id) }}" class="btn btn-primary mt-3">Lihat Detail Pesanan</a>
    @elseif ($order->snap_token)
        <p class="lead">Pesanan untuk: **{{ $order->username }}** (#{{ $order->order_code }})</p>
        <p class="lead">Total Pembayaran: **Rp {{ number_format($order->total_price, 0, ',', '.') }}**</p>
        
        <button id="pay-button" class="btn btn-success btn-lg mt-4">Bayar Sekarang</button>
        <p class="mt-3 text-muted">Klik tombol di atas untuk memilih metode pembayaran Midtrans.</p>
    @else
        <div class="alert alert-danger">Terjadi kesalahan. Token pembayaran tidak tersedia.</div>
        <a href="{{ route('order.show', $order->id) }}" class="btn btn-primary mt-3">Kembali ke Detail Order</a>
    @endif
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
{{-- Midtrans Snap Script (Sandbox/Production tergantung setting .env) --}}
<script type="text/javascript"
    src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
    data-client-key="{{ config('services.midtrans.client_key') }}"></script>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        var payButton = document.getElementById('pay-button');
        if (payButton) {
            payButton.onclick = function(){
                // Memunculkan pop-up Midtrans Snap
                snap.pay('{{ $snapToken }}', {
                    onSuccess: function(result){
                        alert("Pembayaran berhasil!"); 
                        window.location.href = "{{ route('order.show', $order->id) }}";
                    },
                    onPending: function(result){
                        alert("Menunggu pembayaran!");
                        window.location.href = "{{ route('order.show', $order->id) }}";
                    },
                    onError: function(result){
                        alert("Pembayaran gagal!");
                        window.location.href = "{{ route('order.show', $order->id) }}";
                    },
                    onClose: function(){
                        alert('Anda menutup jendela pembayaran. Status transaksi masih tertunda.');
                    }
                });
            };
        }
    });
</script>
@endpush