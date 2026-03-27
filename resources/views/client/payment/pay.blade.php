<x-template.layout title="$title">
    <x-organisms.navbar :cartCount="count((array) session('cart'))" :path="$shop->path" />

    <div class="bg-primary text-white py-5 text-center shadow-sm">
        <div class="container">
            <h1 class="fw-bold text-white mb-2">Instruksi Pembayaran</h1>
            <p class="opacity-75">Selesaikan transfer untuk aktivasi #{{ $topup->reference_code }}</p>
        </div>
    </div>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <x-organisms.payment.instruction-card :topup="$topup" />
            </div>
        </div>
    </div>

    <x-organisms.footer :shop="$shop" />
</x-template.layout>