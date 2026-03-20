@push('css')
<style>
  .autosize:focus {
    box-shadow: none !important;
  }

  @media screen and (max-width: 767px) {
    #input_div {
      display: flex;
      justify-content: space-between;
      gap: 10px;
    }

    .add-to-cart,
    #count {
      width: 100% !important;
    }
  }

  #count {
    width: 100px;
  }
</style>
@endpush

<div>
  <h3 class="mt-md-0 mt-4">{!! str_replace('-', ' ', ucwords($dataProductContent->title)) !!}</h3>
  <hr />
  <h5>${{$dataProductContent->price}}</h5>
  <p>Category : <a href="{{ route('clientCategoryProducts', $dataProductContent->category->name) }}">{!! str_replace('-', ' ', ucwords($dataProductContent->category->name)) !!}</a></p>
  <p><b>Description</b></p>
  <div class="form-group">
    <textarea class="form-control autosize" readonly>{{$dataProductContent->desc}}</textarea>
  </div>
  <!-- <p><b>Stock : {{$dataProductContent->stock}}</b></p>
  @if($dataProductContent->stock !== 0)
  <div id="input_div">
    <input type="button" value="-" id="moins" onclick="minus()" class="btn btn-outline-primary">
    <input type="text" value="1" id="count" class="btn btn-outline-primary font-secondary" disabled>
    <input type="button" value="+" id="plus" data-stok="{{$dataProductContent->stock}}" onclick="plus()" class="btn btn-outline-primary">
  </div> -->

  {{-- Bungkus kedua tombol di dalam div.action-buttons --}}
  <div class="action-buttons col-md-6">
    <!-- <button class="btn btn-outline-primary btn-small font-secondary add-to-cart w-100 mb-3" data-id-product="{{$dataProductContent->id}}" data-quantity="1">
      Add to cart
    </button> -->
    <button class="btn btn-primary btn-small font-secondary checkout-now w-100" data-id-product="{{$dataProductContent->id}}" data-quantity="1">
      Checkout
    </button>
  </div>
  @endif
</div>
@push('js')
<script>
  autosize();

  function autosize() {
    var text = $('.autosize');
    text.each(function() {
      $(this).attr('rows', 1);
      resize($(this));
      this.style.overflow = 'hidden';
      this.style.backgroundColor = 'transparent';
      this.style.padding = '0';
      this.style.border = 'none';
      this.style.resize = 'none';
    });
    text.on('input', function() {
      resize($(this));
    });

    function resize($text) {
      $text.css('height', 'auto');
      $text.css('height', $text[0].scrollHeight + 'px');
    }
  }

  var count = 1;

  function plus() {
    let stok = $('#plus').attr('data-stok');
    if (stok == 0) {
      count++;
      $('#count').val(count); // Pakai jQuery agar anti-error
      $('.add-to-cart, .checkout-now').attr('data-quantity', count);
    } else {
      if (count < stok) {
        count++;
        $('#count').val(count); // Pakai jQuery agar anti-error
        $('.add-to-cart, .checkout-now').attr('data-quantity', count);
      }
    }
  }

  function minus() {
    if (count > 1) {
      count--;
      $('#count').val(count); // Pakai jQuery agar anti-error
      $('.add-to-cart, .checkout-now').attr('data-quantity', count);
    }
  }

  // --- SCRIPT ADD TO CART ---
  $(".add-to-cart").click(function(e) {
    let product_id = $(this).attr("data-id-product");
    let quantity = 1;

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
      },
      type: "POST",
      dataType: "json",
      data: {
        "_token": "{{ csrf_token() }}",
        product_id: product_id,
        quantity: quantity
      },
      url: '{{ route("clientAddToCart") }}',
      success: function(data) {
        $('#cartCount').text(data.cartCount);

        // Reset value dengan cara yang aman
        $('#count').val(1);
        count = 1;
      },
      statusCode: {
        200: () => {
          Toastify({
            text: "Success Add To Cart",
            duration: 3000,
            close: true,
            gravity: "top",
            position: "right",
            backgroundColor: "#4fbe87",
          }).showToast();
        },
        201: () => {
          Toastify({
            text: "Success Updated Quantity To Cart",
            duration: 3000,
            close: true,
            gravity: "top",
            position: "right",
            backgroundColor: "#4fbe87",
          }).showToast();
        },
        202: () => {
          Toastify({
            text: data.message,
            duration: 3000,
            close: true,
            gravity: "top",
            position: "right",
            backgroundColor: "#f3616d",
          }).showToast();
        }
      }
    });
  });

  // --- SCRIPT CHECKOUT SEKARANG ---
  $(".checkout-now").click(function(e) {
    let product_id = $(this).attr("data-id-product");
    let quantity = $(this).attr("data-quantity");

    let btn = $(this);
    btn.prop('disabled', true).text('Loading...'); // Cegah klik dobel

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
      },
      type: "POST",
      dataType: "json",
      data: {
        "_token": "{{ csrf_token() }}",
        product_id: product_id,
        quantity: quantity
      },
      url: '{{ route("clientAddToCart") }}',
      success: function(data) {
        console.log(data);
        if (data.status == 'success') {
          window.location.href = "{{ route('clientCheckout') }}";
        } else {
          Toastify({
            text: data.message,
            duration: 3000,
            close: true,
            gravity: "top",
            position: "right",
            backgroundColor: "#f3616d",
          }).showToast();
          btn.prop('disabled', false).text('Checkout');
        }
        // Jika sukses, langsung arahkan ke halaman checkout
        // 
      },
      error: function() {
        // Jika error jaringan dll, kembalikan tombol ke semula
        btn.prop('disabled', false).text('Checkout');
        alert("Terjadi kesalahan sistem, silakan coba lagi.");
      }
    });
  });
</script>
@endpush