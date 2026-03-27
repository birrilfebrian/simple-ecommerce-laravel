@props(['credits', 'price'])

<div class="card h-100 border-0 shadow-sm p-4 text-center" style="border-radius: 20px; transition: 0.3s;">
    <div class="card-body">
        <div class="mb-3">
            <h1 class="fw-bold text-primary mb-0">{{ $credits }}</h1>
            <small class="text-muted text-uppercase fw-bold">Credits</small>
        </div>
        <h4 class="fw-bold mb-4">Rp {{ number_format($price, 0, ',', '.') }}</h4>

        <form action="{{ route('topup.store') }}" method="POST">
            @csrf
            <input type="hidden" name="credits" value="{{ $credits }}">
            <input type="hidden" name="price" value="{{ $price }}">
            <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold">
                Pilih Paket
            </button>
        </form>
    </div>
</div>

<style>
    .card:hover {
        transform: translateY(-10px);
        border: 1px solid #0d6efd !important;
    }
</style>