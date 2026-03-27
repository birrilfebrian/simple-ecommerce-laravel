@extends('admin.layout')
@section('css')
<style>
  .order-info>tbody>tr {
    height: 35px !important;
  }

  .img-zoom-container {
    overflow: hidden;
    /* Potong bagian gambar yang keluar box */
    cursor: zoom-in;
  }

  .img-zoom-container img {
    transition: transform .3s ease;
  }

  .img-zoom-container:hover img {
    transform: scale(2);
    /* Zoom 2x lipat saat kursor di atasnya */
    cursor: move;
  }

  .medium-zoom-overlay {
    z-index: 10 !important;
  }

  .zoom-me {
    z-index: 11 !important;
  }
</style>
@endsection
@php
$phone = preg_replace('/[^0-9]/', '', $order->phone);
if (str_starts_with($phone, '0')) {
$phone = '62' . substr($phone, 1);
}else{
$phone = '62' . $phone;
}
$message = "Halo kak {$order->name}, pemberitahuan untuk No Order: *{$order->order_code}*. Filenya sudah selesai diproses ya, silakan bisa dicek kembali di website SAMY. Terima kasih!";
$waUrl = "https://wa.me/{$phone}?text=" . urlencode($message);
@endphp
@section('content')
<div class="card">
  <div class="card-body">
    <div class="row ">
      <div class="col-md-4 col-12 mb-3">
        <table class="order-info">
          <tr>
            <td><b>Status</b></td>
            <td>&nbsp; : &nbsp;</td>
            <td>
              <button type="button" data-bs-toggle="modal" data-bs-target="#modalUpdateStatus" style="background-color:transparent;border:none;">
                @if($order->status == 0)
                <span class="badge bg-warning">Unprocessed</span>
                @elseif($order->status == 1)
                <span class="badge bg-info">Confirmed</span>
                @elseif($order->status == 2)
                <span class="badge bg-primary">Processed</span>
                @elseif($order->status == 3)
                <span class="badge bg-danger">Pending</span>
                @elseif($order->status == 4)
                <span class="badge bg-secondary">Amandement</span>
                @elseif($order->status == 5)
                <span class="badge bg-success">Completed</span>
                @endif
              </button>
            </td>
          </tr>
          <tr>
            <td><b>Total</b></td>
            <td>&nbsp; : &nbsp;</td>
            <td><b><u>${{ $order->total }}</u></b></td>
          </tr>
          <tr>
            <td><b>Name</b></td>
            <td>&nbsp; : &nbsp;</td>
            <td>{{ $order->name }}</td>
          </tr>
          <tr>
            <td style="vertical-align: middle;"><b>Phone</b></td>
            <td style="vertical-align: middle;">&nbsp; : &nbsp;</td>
            <td style="vertical-align: middle;">
              <div class="d-inline-flex align-items-center">
                <span>{{ $order->phone }}</span>

                <a href="{{ $waUrl }}" target="_blank" class="btn btn-sm btn-success ms-2 px-3 py-1" style="border-radius: 20px; font-size: 0.75rem;">
                  <i class="bi bi-whatsapp"></i> Hubungi Wa
                </a>
              </div>
            </td>
          </tr>
        </table>
        <hr>
        <div class="mt-4">
          <h6 class="fw-bold"><i class="bi bi-sticky"></i> Instruksi Tambahan (Notes):</h6>
          <div class="d-flex flex-wrap gap-2 mt-2">
            @foreach(explode(', ', $order->note) as $item)
            <span class="badge rounded-pill bg-primary px-3 py-2">
              {{ $item }}
            </span>
            @endforeach
          </div>
        </div>
      </div>
      <div class="col-md-8 col-12">
        <h4>Order Detail</h4>
        <div class="table-responsive">
          <!-- <table class="table table table-striped table-bordered">
            <thead>
              <tr>
                <td>No</td>
                <td>Title</td>
                <td>Price</td>
                <td>Sub Total</td>
              </tr>
            </thead>
            <tbody>
              @foreach ($orderDetail as $item)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{!! str_replace('-', ' ', ucwords($item->title)) !!}</td>
                <td>${{ $item->price }}</td>
                <td>${!! $item->price * $item->quantity !!}</td>
              </tr>
              @endforeach
            </tbody>
          </table> -->
          @if($order->document_path || $order->payment_path || $order->amandement_path)
          <div class=" p-3 rounded" style="background-color: #f8f9fa;">
            <div class="row">
              <div class="col-md-6 mb-3 mb-md-0 border-end">
                @if($order->document_path)
                <div class="mb-3">
                  <strong class="text-primary">File Turnitin Awal:</strong>
                  <br>
                  <a href="{{ asset('storage/' . $order->document_path) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2" download>
                    📥 Download Dokumen Utama
                  </a>
                </div>
                @endif
                @if($order->amandement_path)
                <div class="mt-3 p-2 border-top">
                  <strong class="text-danger">File Revisi (Amandement):</strong>
                  <br>
                  @php
                  $revisiFiles = explode('|', $order->amandement_path);
                  @endphp
                  @foreach($revisiFiles as $index => $fileRevisi)
                  <a href="{{ asset('storage/' . $fileRevisi) }}" target="_blank" class="btn btn-sm btn-danger mt-2 d-block" download>
                    📄 Download Revisi #{{ $index + 1 }}
                  </a>
                  @endforeach
                </div>
                @endif
              </div>

            </div>
          </div>
          @endif
        </div>

      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <a href="javascript:void(0)" onclick="alertconfirm('{{route('orderDelete', $order->order_code)}}')" class="btn btn-danger float-end">Delete Order</a>
  </div>
</div>

<!-- Modal Update Status -->
<div class="modal fade" id="modalUpdateStatus" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalUpdateStatusLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalUpdateStatusLabel">Update Status Order</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route('orderUpdateStatus', $order->order_code) }}" method="post" enctype="multipart/form-data">
          @csrf

          <div class="input-group mb-3">
            <select class="form-select status-select" name="status" id="statusSelect-{{ $order->id }}">
              <option value="0" {{ $order->status == 0 ? 'selected' : '' }}>Unprocessed</option>
              <option value="1" {{ $order->status == 1 ? 'selected' : '' }}>Confirmed</option>
              <option value="2" {{ $order->status == 2 ? 'selected' : '' }}>Processed</option>
              <option value="3" {{ $order->status == 3 ? 'selected' : '' }}>Pending</option>
              <option value="4" {{ $order->status == 4 ? 'selected' : '' }}>Amandement</option>
              <option value="5" {{ $order->status == 5 ? 'selected' : '' }}>Completed</option>
            </select>
            <button type="submit" class="input-group-text btn btn-primary">Save</button>
          </div>
          <div class="form-group result-file-container d-none" id="fileContainer-{{ $order->id }}">
            <label for="result_file" class="mb-2 text-primary fw-bold">Upload Hasil Turnitin (PDF/Word)</label>
            <input type="file" class="form-control" name="result_file" accept=".pdf,.doc,.docx">
            <small class="text-muted">Wajib diisi jika status di-amandement.</small>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
@section('js')
<script src="https://cdn.jsdelivr.net/npm/medium-zoom/dist/medium-zoom.min.js"></script>
<script>
  // Inisialisasi zoom untuk gambar dengan class 'zoom-me'
  mediumZoom('.zoom-me', {
    margin: 24,
    background: 'rgba(0,0,0,0.9)'
  });
</script>
<script>
  const alertconfirm = (url) => {
    Swal.fire({
      title: 'Sure to delete this order?',
      text: "This order will delete permanently",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.replace(url);
      }
    })
  }
</script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const statusSelects = document.querySelectorAll('.status-select');

    statusSelects.forEach(select => {
      toggleFileContainer(select);

      select.addEventListener('change', function() {
        toggleFileContainer(this);
      });
    });

    function toggleFileContainer(selectElement) {
      const orderId = selectElement.id.split('-')[1];
      const fileContainer = document.getElementById('fileContainer-' + orderId);

      if (fileContainer) {
        if (selectElement.value == '4') {
          fileContainer.classList.remove('d-none');
        } else {
          fileContainer.classList.add('d-none');
        }
      }
    }
  });
</script>
@endsection