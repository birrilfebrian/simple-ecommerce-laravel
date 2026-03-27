@props(['history', 'nomorAdmin', 'pesanWA'])

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">Tanggal</th>
                    <th class="py-3 text-uppercase small fw-bold text-muted">Referensi</th>
                    <th class="py-3 text-uppercase small fw-bold text-muted">Total Credit</th>
                    <th class="py-3 text-uppercase small fw-bold text-muted">Total</th>
                    <th class="py-3 text-uppercase small fw-bold text-muted">Status</th>
                    <th class="pe-4 py-3 text-end text-uppercase small fw-bold text-muted">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($history as $item)
                <tr>
                    <td class="ps-4">
                        <span class="d-block fw-bold text-dark lh-sm">{{ $item->created_at->format('d M Y') }}</span>
                        <small class="text-muted" style="font-size: 0.75rem;">{{ $item->created_at->format('H:i') }} WIB</small>
                    </td>
                    <td>
                        <code class="text-primary fw-bold" style="font-size: 0.85rem;">#{{ $item->reference_code }}</code>
                    </td>
                    <td>
                        <span class="fw-500 text-secondary" style="font-size: 0.9rem;">{{ $item->amount_credits }}&nbsp;CR</span>
                    </td>
                    <td>
                        <span class="fw-bold text-dark">Rp {{ number_format($item->price_total, 0, ',', '.') }}</span>
                    </td>
                    <td>
                        @if($item->status == 0)
                        <span class="badge rounded-pill bg-warning text-dark px-3 py-2 d-inline-flex align-items-center">
                            <i class="bi bi-hourglass-split me-1"></i> Pending
                        </span>
                        @elseif($item->status == 1)
                        <span class="badge rounded-pill bg-success px-3 py-2 d-inline-flex align-items-center">
                            <i class="bi bi-check-circle me-1"></i> Berhasil
                        </span>
                        @else
                        <span class="badge rounded-pill bg-danger px-3 py-2 d-inline-flex align-items-center">
                            <i class="bi bi-x-circle me-1"></i> Ditolak
                        </span>
                        @endif
                    </td>
                    <td class="pe-4">
                        @if ($item->status != 1)
                        <div class="d-flex justify-content-end align-items-center">
                            <a href="https://wa.me/{{ $nomorAdmin }}?text={{ $pesanWA }}" target="_blank"
                                class="btn btn-sm btn-success rounded-pill px-3 fw-bold shadow-sm d-flex align-items-center"
                                style="height: 32px; font-size: 0.8rem;">
                                <i class="bi bi-whatsapp me-2"></i> Hubungi Admin
                            </a>
                        </div>
                        @else
                        <div class="d-flex justify-content-end align-items-center">
                            -
                        </div>

                        @endif
                    </td>
                </tr>
                @empty
                @endforelse
            </tbody>
        </table>
    </div>
</div>