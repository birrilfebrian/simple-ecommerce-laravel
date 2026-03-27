@props(['topup'])

<div class="card border-0 shadow-lg rounded-4 overflow-hidden">
    <div class="card-body p-4 p-md-5">
        <div class="text-center mb-4 p-4 rounded-4 bg-light border border-dashed border-primary">
            <span class="text-muted small text-uppercase fw-bold d-block mb-1">Total yang Harus Ditransfer</span>
            <h2 class="fw-bold text-primary mb-0">Rp {{ number_format($topup->price_total, 0, ',', '.') }}</h2>
            <p class="text-danger small mt-2 mb-0 italic">*Mohon transfer tepat hingga digit terakhir agar otomatis terverifikasi</p>
        </div>

        <div class="mb-4">
            <label class="small fw-bold text-muted mb-2 d-block text-uppercase">Transfer Ke Rekening:</label>
            <div class="p-3 bg-white border rounded-4 d-flex justify-content-between align-items-center shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="bg-primary text-white rounded-3 p-2 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-bank fs-4"></i>
                    </div>
                    <div>
                        <strong class="d-block text-dark fs-5" style="letter-spacing: 1px;">1234567890</strong>
                        <span class="text-muted small">Bank BCA - A/N Nama Kamu</span>
                    </div>
                </div>
                <button class="btn btn-sm btn-light rounded-pill px-3 fw-bold border" onclick="copyText('1234567890')">Salin</button>
            </div>
        </div>

        <hr class="my-4 opacity-50">

        <form action="{{ route('topup.uploadProof') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="reference_code" value="{{ $topup->reference_code }}">

            <div class="mb-4">
                <label class="small fw-bold text-muted mb-2 d-block text-uppercase">Unggah Bukti Transfer</label>
                <div class="custom-file-upload">
                    <input type="file" name="payment_proof" id="proof" class="form-control rounded-4 @error('payment_proof') is-invalid @enderror" required>
                </div>
                @error('payment_proof') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
            </div>

            <button type="submit" class="btn btn-dark w-100 py-3 rounded-pill fw-bold shadow">
                <i class="bi bi-cloud-arrow-up me-2"></i> Konfirmasi Pembayaran
            </button>
        </form>

        <div class="mt-4 text-center">
            <a href="{{ route('topup.index') }}" class="text-muted text-decoration-none small">
                <i class="bi bi-arrow-left me-1"></i> Batal / Pilih Paket Lain
            </a>
        </div>
    </div>
</div>

<script>
    function copyText(text) {
        navigator.clipboard.writeText(text);
        alert('Nomor rekening disalin!');
    }
</script>