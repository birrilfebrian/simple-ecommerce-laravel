<div class="container py-4">
    <form action="{{ route('clientCheckoutSave') }}" method="post" enctype="multipart/form-data" id="orderForm">
        @csrf
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror bg-transparent" placeholder="Mike" value="{{ old('name') }}" required>
            @error('name')
            <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label for="phone">Phone number</label>
            <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror bg-transparent" placeholder="08122387xxxx" value="{{ old('phone') }}" required>
            @error('phone')
            <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="row">
            <div class="form-group mb-3 col-lg-6 col-md-12">
                <label for="document" class="mb-2">Upload Dokumen (PDF, DOC, DOCX)</label>
                <input type="file" class="form-control bg-transparent" id="document_input" accept=".pdf,.doc,.docx">
                <input type="hidden" name="document" id="document_path">

                <div id="document_progress_container" class="mt-2" style="display:none;">
                    <div class="progress" style="height: 10px;">
                        <div id="document_bar" class="progress-bar bg-success progress-bar-striped progress-bar-animated" style="width: 0%;"></div>
                    </div>
                    <small id="document_status" class="text-muted" style="font-size: 11px;">Mengunggah...</small>
                </div>
            </div>

            <div class="form-group mb-3 col-lg-6 col-md-12">
                <label for="payment" class="mb-2">Upload Bukti Pembayaran</label>
                <input type="file" class="form-control bg-transparent" id="payment_input" accept=".jpg,.jpeg,.png">
                <input type="hidden" name="payment" id="payment_path">

                <div id="payment_progress_container" class="mt-2" style="display:none;">
                    <div class="progress" style="height: 10px;">
                        <div id="payment_bar" class="progress-bar bg-success progress-bar-striped progress-bar-animated" style="width: 0%;"></div>
                    </div>
                    <small id="payment_status" class="text-muted" style="font-size: 11px;">Mengunggah...</small>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="mb-2">Note (Opsi Tambahan)</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="notes[]" value="Small Matches" id="smallMatches" {{ in_array('Small Matches', old('notes', [])) ? 'checked' : '' }}>
                <label class="form-check-label" for="smallMatches">Abaikan Sumber Yang Kurang Dari - Small Matches</label>
            </div>

            <div id="smallMatchesOptions" style="display: {{ in_array('Small Matches', old('notes', [])) ? 'block' : 'none' }}; margin-left: 25px; margin-top: 10px;">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label style="font-size: 0.85rem;">Jumlah Kata (Words)</label>
                        <div class="input-group input-group-sm">
                            <input type="number" name="small_match_word" class="form-control" placeholder="Contoh: 10" value="{{ old('small_match_word') }}">
                            <span class="input-group-text">Words</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label style="font-size: 0.85rem;">Persens (%)</label>
                        <div class="input-group input-group-sm">
                            <input type="number" name="small_match_percent" class="form-control" placeholder="Contoh: 1" value="{{ old('small_match_percent') }}">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary float-end" id="btnOrder">Order</button>
    </form>
</div>

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('orderForm');
        const checkbox = document.getElementById('smallMatches');
        const options = document.getElementById('smallMatchesOptions');

        // Logic Show/Hide Small Matches
        checkbox.addEventListener('change', function() {
            options.style.display = this.checked ? 'block' : 'none';
            if (!this.checked) options.querySelectorAll('input').forEach(i => i.value = '');
        });

    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        setupAutoUpload('document_input', 'document_path', 'document_progress_container', 'document_bar', 'document_status');
        setupAutoUpload('payment_input', 'payment_path', 'payment_progress_container', 'payment_bar', 'payment_status');

        function setupAutoUpload(inputId, hiddenId, containerId, barId, statusId) {
            const input = document.getElementById(inputId);
            if (!input) return; // Guard agar tidak error jika ID tidak ditemukan

            input.addEventListener('change', function() {
                if (this.files.length === 0) return;

                const file = this.files[0];
                const formData = new FormData();
                formData.append('file', file);
                // Kita tidak perlu append token ke FormData jika sudah ditaruh di Header

                const container = document.getElementById(containerId);
                const bar = document.getElementById(barId);
                const status = document.getElementById(statusId);

                container.style.display = 'block';
                bar.style.width = '0%';
                bar.classList.add('progress-bar-animated');
                status.innerText = 'Menyiapkan unggahan...';

                const xhr = new XMLHttpRequest();
                xhr.open('POST', '{{ route("upload.temporary") }}', true);

                // --- BAGIAN PENTING: CSRF TOKEN ---
                xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                // Tambahkan ini agar Laravel tahu ini request AJAX
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                xhr.upload.onprogress = function(e) {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 100);
                        bar.style.width = percent + '%';
                        bar.innerText = percent + '%';
                        status.innerText = 'Mengunggah: ' + percent + '%';
                    }
                };

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            document.getElementById(hiddenId).value = response.path;
                            status.innerHTML = '<span class="text-success fw-bold">✅ Berhasil diunggah</span>';
                            bar.classList.remove('progress-bar-animated');
                            bar.classList.replace('bg-primary', 'bg-success'); // Opsional: ganti warna jadi hijau
                        } catch (e) {
                            status.innerHTML = '<span class="text-danger">❌ Error memproses respon server</span>';
                        }
                    } else {
                        status.innerHTML = '<span class="text-danger">❌ Gagal upload (Error ' + xhr.status + ')</span>';
                    }
                };

                xhr.onerror = function() {
                    status.innerHTML = '<span class="text-danger">❌ Koneksi terputus</span>';
                };

                xhr.send(formData);
            });
        }
    });
</script>
@endpush