@extends('admin.layout')

@section('css')
<style>
  .info-table tr {
    height: 40px;
  }

  .img-zoom-container {
    overflow: hidden;
    border-radius: 15px;
    border: 2px solid #eee;
    background: #f9f9f9;
    cursor: zoom-in;
  }

  .img-zoom-container img {
    transition: transform .3s ease;
    width: 100%;
    display: block;
  }

  .img-zoom-container:hover img {
    transform: scale(1.5);
  }

  /* Style khusus untuk memperjelas teks nominal */
  .nominal-box {
    background: #eef2ff;
    border: 1px dashed #435ebe;
    color: #435ebe;
    padding: 10px;
    border-radius: 10px;
    font-size: 1.2rem;
  }

  .medium-zoom-overlay {
    z-index: 10 !important;
  }

  .zoom-me {
    z-index: 11 !important;
  }
</style>
@endsection

@section('content')
<div class="row">
  <div class="col-md-5 col-12">
    <div class="card shadow-sm h-100">
      <div class="card-header bg-white border-0 pt-4">
        <h5 class="fw-bold">Informasi Top Up</h5>
      </div>
      <div class="card-body">
        <table class="table info-table">
          <tr>
            <td><b class="text-muted">Ref Code</b></td>
            <td>:</td>
            <td><span class="badge bg-light-primary text-primary fw-bold">#{{ $topup->reference_code }}</span></td>
          </tr>
          <tr>
            <td><b class="text-muted">Customer</b></td>
            <td>:</td>
            <td>{{ $topup->user->name }}</td>
          </tr>
          <tr>
            <td><b class="text-muted">Tanggal</b></td>
            <td>:</td>
            <td>{{ $topup->created_at->format('d M Y, H:i') }} WIB</td>
          </tr>
          <tr>
            <td><b class="text-muted">Status</b></td>
            <td>:</td>
            <td>
              @if($topup->status == 0)
              <span class="badge bg-warning">Pending / Verification</span>
              @elseif($topup->status == 1)
              <span class="badge bg-success">Approved / Success</span>
              @else
              <span class="badge bg-danger">Rejected</span>
              @endif
            </td>
          </tr>
        </table>

        <div class="mt-4">
          <label class="small fw-bold text-muted d-block mb-2">NOMINAL HARUS DITERIMA:</label>
          <div class="nominal-box fw-bold">
            Rp {{ number_format($topup->price_total, 0, ',', '.') }}
          </div>
          <small class="text-muted mt-2 d-block italic">*Pastikan angka di bukti transfer sama persis dengan angka di atas.</small>
        </div>

        <hr class="my-4">

        @if($topup->status == 0)
        <div class="d-grid gap-2">
          <form action="{{ route('adminTopupApprove', $topup->reference_code) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold mb-2" onclick="return confirm('Konfirmasi saldo untuk user ini?')">
              <i class="bi bi-check-circle me-2"></i> Approve & Tambah Saldo
            </button>
          </form>

          <button type="button" class="btn btn-outline-danger rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#modalReject">
            <i class="bi bi-x-circle me-2"></i> Tolak Pembayaran
          </button>
        </div>
        @else
        <div class="alert alert-light-secondary text-center">
          Transaksi ini sudah diproses pada {{ $topup->updated_at->format('d/m/y H:i') }}
        </div>
        @endif
      </div>
    </div>
  </div>

  <div class="col-md-7 col-12">
    <div class="card shadow-sm h-100">
      <div class="card-header bg-white border-0 pt-4 d-flex justify-content-between">
        <h5 class="fw-bold">Bukti Transfer</h5>
        @if($topup->payment_proof)
        <a href="{{ asset('storage/' . $topup->payment_proof) }}" target="_blank" class="btn btn-sm btn-light border text-primary">
          <i class="bi bi-box-arrow-up-right me-1"></i> Buka Fullscreen
        </a>
        @endif
      </div>
      <div class="card-body text-center">
        @if($topup->payment_proof)
        <div class="img-zoom-container mx-auto" style="max-width: 450px;">
          <img src="{{ asset('storage/' . $topup->payment_proof) }}" class="zoom-me img-fluid" alt="Proof Payment">
        </div>
        <p class="text-muted mt-3 small"><i class="bi bi-info-circle me-1"></i> Arahkan kursor untuk zoom atau klik untuk memperbesar</p>
        @else
        <div class="py-5">
          <i class="bi bi-image-alt display-1 text-light"></i>
          <p class="text-muted mt-3">User belum mengunggah bukti transfer.</p>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalReject" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tolak Top Up</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('adminTopupReject', $topup->reference_code) }}" method="POST">
        @csrf
        <div class="modal-body">
          <p>Apakah Anda yakin ingin menolak top up ini? Saldo tidak akan bertambah.</p>
          <label class="fw-bold mb-2">Alasan Penolakan (Opsional):</label>
          <textarea name="reason" class="form-control" placeholder="Contoh: Bukti transfer tidak jelas / palsu"></textarea>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Ya, Tolak</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/medium-zoom/dist/medium-zoom.min.js"></script>
<script>
  mediumZoom('.zoom-me', {
    margin: 24,
    background: 'rgba(0,0,0,0.9)'
  });

  // --- Logic SweetAlert untuk Notifikasi ---
  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true
  });

  @if(session('success'))
  Toast.fire({
    icon: 'success',
    title: '{{ session("success") }}'
  });
  @endif

  @if(session('error'))
  Swal.fire({
    icon: 'error',
    title: 'Waduh, Gagal!',
    text: '{{ session("error") }}',
    confirmButtonColor: '#435ebe',
  });
  @endif

  @if(session('info'))
  Toast.fire({
    icon: 'info',
    title: '{{ session("info") }}'
  });
  @endif
</script>
@endsection