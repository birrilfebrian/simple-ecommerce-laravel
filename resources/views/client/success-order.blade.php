@php
// Link tujuan scan (sudah include order_code)
$linkScan = route('clientCheckOrderStatus') . '?order_code=' . $order_code;

// Gunakan API dari QRServer (lebih stabil)
$qrApi = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($linkScan);
@endphp
<x-template.layout title="{{ $title }}">
  <x-organisms.navbar :path="$shop->path" />
  <div class="container py-5 d-flex flex-column align-items-center gap-3">
    <div class="text-center mt-3">
      <h4 class="fw-bold">Thank you so much for your order!</h4>

      <div class="my-4">
        <img src="{{ $qrApi }}"
          class="border border-2 rounded-3 shadow-sm p-2 bg-white"
          style="width: 180px; max-width: 80%; height: auto;"
          alt="QR Code Status">
        <p class="small text-muted mt-2 italic"><i class="fas fa-camera"></i> Scan untuk cek status otomatis</p>
      </div>

      <div class="mb-4">
        <p class="mb-1">Order Code :</p>
        <h3 class="text-danger fw-bold"><u>{{ $order_code }}</u></h3>
      </div>

      <div class="px-md-5">
        <p class="mb-1">Silakan <b>Screenshot QR Code</b> ini atau catat kode order Anda.</p>
        <p class="small text-muted">
          Gunakan menu <a href="{{ route('clientCheckOrder') }}" class="text-decoration-underline">Check Order</a>
          di navigasi untuk memantau status pesanan Anda secara berkala.
        </p>
      </div>
    </div>

    <div class="d-grid gap-2 col-md-6 col-12">
      <a href="{{ route('clientCheckOrderStatus', ['order_code' => $order_code]) }}" class="btn btn-primary btn-lg shadow-sm">
        <i class="fas fa-search me-1"></i> Check Order Now
      </a>

      <p class="text-center mt-3 mb-1 small text-muted">Ada kendala dengan pesanan Anda?</p>
      <a href="https://wa.me/{{ $nomorAdmin }}?text={{ $pesanWA }}"
        target="_blank"
        class="btn btn-outline-success">
        <i class="fab fa-whatsapp me-1"></i> Hubungi Admin via WhatsApp
      </a>
    </div>
  </div>
  <x-organisms.footer :shop="$shop" />
</x-template.layout>