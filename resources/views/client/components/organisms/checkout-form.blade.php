<div class="container py-4">
    <form action="{{ route('clientCheckoutSave') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" class="form-control  @error('name') is-invalid @enderror bg-transparent" placeholder="Mike" value="{{ old('name') }}" required>
            @error('name')
            <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <div class="form-group">
            <label for="phone">Phone number</label>
            <input type="text" name="phone" id="phone" class="form-control  @error('phone') is-invalid @enderror bg-transparent" placeholder="08122387xxxx" value="{{ old('phone') }}" required>
            @error('phone')
            <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <!-- <div class="form-group">
            <label for="address">Address</label>
            <input type="text" name="address" id="address" class="form-control  @error('address') is-invalid @enderror bg-transparent" placeholder="3425 Stone Street" value="{{ old('address') }}" required>
            @error('address') 
              <small class="text-danger">{{ $message }}</small>
            @enderror
        </div> -->
        <div class="row">
            <div class="form-group mb-3 col-lg-6 col-md-12">
                <label for="document" class="mb-2">Upload Dokumen (PDF, DOC, DOCX) - Max 30MB</label>
                <input type="file" class="form-control @error('document') is-invalid @enderror bg-transparent" id="document" name="document" accept=".pdf,.doc,.docx" required>

                @error('document')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group mb-3 col-lg-6 col-md-12">
                <label for="document" class="mb-2">Upload Bukti Pembayaran - Max 5MB</label>
                <input type="file" class="form-control @error('payment') is-invalid @enderror bg-transparent" id="payment" name="payment" accept=".jpg,.jpeg,.png" required>

                @error('document')
                <span class=" invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
        <div class="form-group">
            <label class="mb-2">Note (Opsi Tambahan)</label>

            <div class="form-check">
                <input class="form-check-input @error('notes') is-invalid @enderror" type="checkbox" name="notes[]" value="No Bibliography" id="note1" {{ in_array('No Bibliography', old('notes', [])) ? 'checked' : '' }}>
                <label class="form-check-label" for="note1">
                    No Bibliography
                </label>
            </div>

            <div class="form-check">
                <input class="form-check-input @error('notes') is-invalid @enderror" type="checkbox" name="notes[]" value="Abaikan Teks Kutipan" id="note2" {{ in_array('Abaikan Teks Kutipan', old('notes', [])) ? 'checked' : '' }}>
                <label class="form-check-label" for="note2">
                    Abaikan Teks Kutipan
                </label>
            </div>

            @error('notes')
            <span class="text-danger" style="font-size: 80%; mt-1" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary float-end">Order</button>
    </form>
</div>
@push('js')
<script>
    autosize();

    function autosize() {
        var text = $('#note');

        text.each(function() {
            $(this).attr('rows', 1);
            resize($(this));
            this.style.overflow = 'hidden';
            this.style.backgroundColor = 'transparent';
        });

        text.on('input', function() {
            resize($(this));
        });

        function resize($text) {
            $text.css('height', 'auto');
            $text.css('height', $text[0].scrollHeight + 'px');
        }
    }
</script>
@endpush