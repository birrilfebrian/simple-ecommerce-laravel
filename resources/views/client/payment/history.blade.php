<x-template.layout :title="$title">
    <x-organisms.navbar :cartCount="count((array) session('cart'))" :path="$shop->path" />

    <div class="bg-dark text-white py-5 text-center shadow-sm">
        <div class="container">
            <h1 class="fw-bold text-white mb-2">
                <i class="bi bi-clock-history me-2"></i>{{ $title }}
            </h1>
            <p class="opacity-75">Pantau status pengisian saldo dan penggunaan kredit Anda</p>
        </div>
    </div>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <x-organisms.payment.transaction-table
                    :history="$historys"
                    :nomorAdmin="$nomorAdmin"
                    :pesanWA="$pesanWA" />

                <div class="d-flex justify-content-center mt-4">
                    {{ $topups->links() }}
                </div>

                <div class="mt-4 text-center">
                    <a href="{{ route('topup.index') }}"
                        class="btn btn-outline-secondary rounded-pill px-4 fw-bold d-inline-flex align-items-center justify-content-center"
                        style="height: 45px; transition: all 0.3s ease;">
                        <i class="bi bi-wallet2 me-2"></i>
                        <span>Top Up Lagi</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <x-organisms.footer :shop="$shop" />
</x-template.layout>