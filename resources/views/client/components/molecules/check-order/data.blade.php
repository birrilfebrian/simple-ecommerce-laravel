@push('css')
<style>
    .order-info>tbody>tr {
        height: 35px !important;
    }
</style>
@endpush
<div class="container py-3">
    <div class="card bg-transparent border">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-3 col-12">
                    <table class="order-info">
                        <tr>
                            <td><b>Status</b></td>
                            <td>&nbsp; : &nbsp;</td>
                            <td>
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
                            </td>
                        </tr>
                        <tr>
                            <td><b>Order Code</b></td>
                            <td>&nbsp; : &nbsp;</td>
                            <td><b><u>${{ $order->order_code }}</u></b></td>
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
                            <td><b>Phone</b></td>
                            <td>&nbsp; : &nbsp;</td>
                            <td>{{ $order->phone }}</td>
                        </tr>
                        <tr>
                            <td><b>Note</b></td>
                            <td>&nbsp; : &nbsp;</td>
                            <td>{{ $order->note }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-9 col-12">
                    <h4>Order Detail</h4>
                    <div class="table-responsive d-md-block d-sm-blovk d-none">
                        <table class="table table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <td>No</td>
                                    <td>Title</td>
                                    <td>Price</td>
                                    <td>Quantity</td>
                                    <td>Sub Total</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orderDetail as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{!! str_replace('-', ' ', ucwords($item->title)) !!}</td>
                                    <td>${{ $item->price }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>${!! $item->price * $item->quantity !!}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-lg-none d-sm-none d-block">
                        @foreach($orderDetail as $row)
                        <div class="card mt-2 bg-transparent" style="width: 100%;box-shadow: rgb(0 0 0 / 10%) 0px 10px 15px -3px, rgb(0 0 0 / 5%) 0px 4px 6px -2px;">
                            <div class="card-body" style="padding: .8rem;">
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="font-bold font-primary">{!! str_replace('-', ' ', ucwords($row->title)) !!}</h6>
                                    </div>
                                    <div class="col-6">
                                        <label for="">Price</label>
                                        <p class="font-bold">${{ $row->price }}</p>
                                    </div>
                                    <div class="col-6">
                                        <label for="">Sub Total</label>
                                        <p class="font-bold">${!! $row->price * $row->quantity !!}</p>
                                    </div>
                                    <div class="col-12">
                                        <label for="">Quantity</label>
                                        <p class="font-bold">X {{ $row->quantity }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @if(in_array($order->status, [4, 5]) && $order->turnitin_result)
            <div class="card mt-4 border-success shadow-sm">
                <div class="card-body bg-light text-center">
                    <h5 class="text-success mb-3">
                        <i class="fas fa-check-circle"></i> Hasil Review Admin
                    </h5>
                    <p class="mb-3">Silakan unduh hasil pengecekan Turnitin terbaru melalui tombol di bawah ini:</p>

                    <a href="{{ asset('storage/' . $order->turnitin_result) }}" target="_blank" class="btn btn-success mb-3" download>
                        📥 Download Hasil Turnitin
                    </a>

                    @if($order->status == 4)
                    <hr>
                    <div class="mt-4 px-md-5">
                        <h6 class="text-danger fw-bold mb-3">Punya File Revisi?</h6>
                        <p class="small text-muted">Jika Anda ingin mengajukan amandemen, silakan upload file revisi Anda di bawah ini agar Admin bisa mengecek kembali.</p>

                        <form action="{{ route('clientUploadAmandement', $order->order_code) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="input-group">
                                <input type="file" name="amandement_file" class="form-control" required>
                                <button type="submit" class="btn btn-danger">Upload Revisi</button>
                            </div>
                            <small class="text-muted d-block mt-2">Format: PDF, DOC, DOCX (Max 30MB)</small>
                        </form>
                    </div>
                    @else
                    <br>
                    <small class="text-muted">Pesanan ini sudah selesai. Jika dalam 3x24 jam tidak ada kendala, sistem akan menutup tiket ini.</small>
                    @endif
                </div>
            </div>

            @elseif($order->status == 5 && !$order->turnitin_result)
            <div class="alert alert-warning mt-4 text-center text-black">
                Pesanan telah selesai, namun file hasil sedang disiapkan oleh sistem. Harap muat ulang halaman ini beberapa saat lagi.
            </div>

            @elseif($order->status == 4 && !$order->turnitin_result)
            <div class="alert alert-info mt-4 text-center text-black">
                Pesanan dalam status <strong>Amandement</strong>. Admin sedang memproses revisi Anda. Mohon tunggu.
            </div>
            @endif
        </div>
    </div>

    @if($order->status != 5)
    <div class="card bg-transparent mt-3 border">
        <div class="card-body">
            <div class="row">
                <div class="col-4">
                    <a href="{{ url('/') }}" class="btn btn-sm btn-outline-secondary font-secondary"><span class="d-flex align-items-center gap-2"><i class="bi bi-arrow-left"></i> Home </span></a>
                </div>
                <div class="col-8">
                    <a href="https://wa.me/{{ $nomorAdmin }}?text={{ $pesanWA }}"
                        target="_blank"
                        class="btn btn-outline-success float-end">
                        <i class="fab fa-whatsapp me-1"></i> Hubungi Admin via WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>