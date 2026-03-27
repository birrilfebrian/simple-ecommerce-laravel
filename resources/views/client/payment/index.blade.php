<x-template.layout title="$title">
    <x-organisms.navbar cartCount=10 :path="$shop->path" />

    <div class="bg-secondary text-white py-5 text-center">
        <div class="container">
            <h1 class="fw-bold text-white">Saldo Saya: {{ auth()->user()->credits ?? 0 }} Credits</h1>
            <p class="opacity-75">Isi ulang saldo untuk lanjut menggunakan layanan</p>
        </div>
    </div>

    <x-organisms.payment.package-list :packages="$packages" />

    <div class="container pb-5 text-center">
        <a href="{{ route('topup.history') }}" class="text-decoration-none fw-bold">
            <i class="bi bi-clock-history"></i> Lihat Riwayat Transaksi
        </a>
    </div>

    <x-organisms.footer :shop="$shop" />
</x-template.layout>