<div class="d-flex align-items-center gap-3"> @auth
  <div class="dropdown">
    <button class="btn btn-link text-decoration-none dropdown-toggle d-flex align-items-center bg-light rounded-pill px-3 py-1 border shadow-sm" type="button" id="userMenuDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="color: #333; border-color: #e0e0e0 !important;">
      <i class="bi bi-wallet2 text-warning me-2" style="font-size: 0.9rem;"></i>
      <span class="fw-bold me-1" style="font-size: 0.85rem;">{{ number_format(auth()->user()->credits, 0, ',', '.') }}</span>
      <span class="text-muted fw-normal" style="font-size: 0.85rem;">CR</span>
      <i class="bi bi-chevron-down ms-2 text-muted" style="font-size: 0.7rem;"></i>
    </button>

    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-2 rounded-3" style="min-width: 180px;">
      <li>
        <h6 class="dropdown-header text-uppercase text-muted fw-bold small pb-1">Akun Saya</h6>
      </li>
      <li><a class="dropdown-item d-flex align-items-center py-2" href="{{ route('topup.index') }}"><i class="bi bi-plus-circle text-primary me-2"></i>Top Up</a></li>
      <li>
        <hr class="dropdown-divider">
      </li>
      <li>
        <a class="dropdown-item d-flex align-items-center py-2 text-danger fw-bold" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
          <i class="bi bi-box-arrow-right me-2"></i>LOGOUT
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
      </li>
    </ul>
  </div>
  @endauth

  @guest
  <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary rounded-pill px-4 fw-bold d-inline-flex align-items-center" style="height: 36px; font-size: 0.75rem;">
    <i class="bi bi-box-arrow-in-right me-2"></i>LOGIN
  </a>
  @endguest

  <form action="{{ route('clientProductSearch') }}" class="search m-0" method="GET">
    <input class="search__input" type="search" placeholder="Search" id="searchInput" name="product" onfocus="Onfocus(this)" onblur="Onblur(this)">
    <div class="search__icon-container">
      <label for="searchInput" class="search__label">
        <svg width="20" height="20" viewBox="0 0 29 29" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M28 28L21.8613 21.8503L28 28ZM25.2632 13.6316C25.2632 16.7165 24.0377 19.675 21.8563 21.8563C19.675 24.0377 16.7165 25.2632 13.6316 25.2632C10.5467 25.2632 7.58816 24.0377 5.40681 21.8563C3.22547 19.675 2 16.7165 2 13.6316C2 10.5467 3.22547 7.58816 5.40681 5.40681C7.58816 3.22547 10.5467 2 13.6316 2C16.7165 2 19.675 3.22547 21.8563 5.40681C24.0377 7.58816 25.2632 10.5467 25.2632 13.6316V13.6316Z" stroke="black" stroke-opacity="0.8" stroke-width="2.5" stroke-linecap="round" />
        </svg>
      </label>
    </div>
  </form>

  <a href="{{ route('clientCarts') }}" class="text-decoration-none text-dark">
    <div class="cart position-relative d-flex align-items-center">
      <i class="bi bi-cart2 fs-4"></i>
      <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle" style="font-size: 0.6rem;">
        {{ count((array) session('cart')) }}
      </span>
    </div>
  </a>

</div>