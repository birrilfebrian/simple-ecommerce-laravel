@props(['packages'])

<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Top Up Credits</h2>
            <p class="text-muted">Gunakan credit untuk melakukan pengecekan Turnitin secara instan.</p>
        </div>

        <div class="row g-4 justify-content-center">
            @foreach($packages as $package)
            <div class="col-lg-3 col-md-6">
                {{-- Ganti dari <x-molecules... /> menjadi jalur lengkap ini: --}}
                <x-molecules.payment.package-card :credits="$package['credits']" :price="$package['price']" />
            </div>
            @endforeach
        </div>
    </div>
</section>