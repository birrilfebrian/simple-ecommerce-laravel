<div class="container py-4">
    <div class="alert alert-info d-flex justify-content-between align-items-center">
        <span><i class="bi bi-wallet2"></i> Saldo Kredit Anda:</span>
        <strong class="fs-5">{{ auth()->user()->credits }} Credits</strong>
    </div>

    <form action="{{ route('clientCheckoutSave') }}" method="post" id="orderForm">
        @csrf
        <div class="form-group mb-3">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" class="form-control bg-transparent" placeholder="Mike" value="{{ auth()->user()->name }}" required>
        </div>

        <div class="form-group mb-3">
            <label for="phone">Phone number</label>
            <input type="text" name="phone" id="phone" class="form-control bg-transparent" placeholder="08122387xxxx" value="{{ old('phone') }}" required>
        </div>

        <div class="row">
            <div class="form-group mb-3 col-12">
                <label for="document" class="mb-2">Upload Dokumen (PDF, DOC, DOCX) - <span class="text-danger">Biaya: {{current(session('cart'))['price']}} Credit</span></label>
                <input type="file" class="form-control bg-transparent" id="document_input" accept=".pdf,.doc,.docx">
                <input type="hidden" name="document" id="document_path" required>

                <div id="document_progress_container" class="mt-2" style="display:none;">
                    <div class="progress" style="height: 12px;">
                        <div id="document_bar" class="progress-bar bg-primary progress-bar-striped progress-bar-animated" style="width: 0%;"></div>
                    </div>
                    <small id="document_status" class="text-muted" style="font-size: 11px;">Mengunggah...</small>
                </div>
            </div>
        </div>

        <div class="form-group mb-4">
            <label class="mb-2 fw-bold">Note (Opsi Tambahan)</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="notes[]" value="Small Matches" id="smallMatches">
                <label class="form-check-label" for="smallMatches">Abaikan Sumber Yang Kurang Dari - Small Matches</label>
            </div>

            <div id="smallMatchesOptions" style="display: none; margin-left: 25px; margin-top: 10px;">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label style="font-size: 0.85rem;">Jumlah Kata (Words)</label>
                        <div class="input-group input-group-sm">
                            <input type="number" name="small_match_word" class="form-control" placeholder="Contoh: 10">
                            <span class="input-group-text">Words</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label style="font-size: 0.85rem;">Persen (%)</label>
                        <div class="input-group input-group-sm">
                            <input type="number" name="small_match_percent" class="form-control" placeholder="Contoh: 1">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold" id="btnOrder">SUBMIT ORDER</button>
    </form>
</div>

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('orderForm');

        // 1. Logic Show/Hide Small Matches
        const checkbox = document.getElementById('smallMatches');
        const options = document.getElementById('smallMatchesOptions');
        checkbox.addEventListener('change', function() {
            options.style.display = this.checked ? 'block' : 'none';
        });

        // 2. Logic Auto Upload Document
        setupAutoUpload('document_input', 'document_path', 'document_progress_container', 'document_bar', 'document_status');

        function setupAutoUpload(inputId, hiddenId, containerId, barId, statusId) {
            const input = document.getElementById(inputId);
            input.addEventListener('change', function() {
                if (this.files.length === 0) return;

                const formData = new FormData();
                formData.append('file', this.files[0]);

                document.getElementById(containerId).style.display = 'block';
                const bar = document.getElementById(barId);
                const status = document.getElementById(statusId);

                const xhr = new XMLHttpRequest();
                xhr.open('POST', '{{ route("upload.temporary") }}', true);
                xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

                xhr.upload.onprogress = function(e) {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 100);
                        bar.style.width = percent + '%';
                        bar.innerText = percent + '%';
                    }
                };

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        document.getElementById(hiddenId).value = response.path;
                        status.innerHTML = '<span class="text-success fw-bold">✅ File siap digunakan</span>';
                        bar.classList.replace('bg-primary', 'bg-success');
                    } else {
                        status.innerHTML = '<span class="text-danger">❌ Gagal upload</span>';
                    }
                };
                xhr.send(formData);
            });
        }

        // 3. Logic Submit Form Final via AJAX
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Cek apakah file sudah diupload
            if (!document.getElementById('document_path').value) {
                Swal.fire('Oops!', 'Tunggu sampai file selesai diunggah.', 'warning');
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Order',
                text: "Saldo Anda akan dipotong {{current(session('cart'))['price']}} Credit untuk pengerjaan ini.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Order Sekarang!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitFinalOrder();
                }
            });
        });

        function submitFinalOrder() {
            Swal.fire({
                title: 'Memproses...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const formData = new FormData(form);
            const xhr = new XMLHttpRequest();
            xhr.open('POST', form.action, true);
            xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.onload = function() {
                const response = JSON.parse(xhr.responseText);
                if (xhr.status === 200) {
                    Swal.fire('Berhasil!', 'Order sukses, saldo telah dipotong.', 'success').then(() => {
                        window.location.href = response.redirect_url;
                    });
                } else {
                    Swal.fire('Gagal!', response.error || 'Terjadi kesalahan.', 'error');
                }
            };
            xhr.send(formData);
        }
    });
</script>
@endpush