<ul class="nav-list d-flex align-items-center mb-0 list-unstyled gap-3">
  <li class="nav-item">
    <a href="/" class="nav-link">HOME</a>
  </li>
  <li class="nav-item">
    <a href="{{ route('clientProducts') }}" class="nav-link">PRODUCTS</a>
  </li>
  <li class="nav-item">
    <a href="{{ route('clientAbout') }}" class="nav-link">ABOUT</a>
  </li>
  <li class="nav-item">
    <a href="{{ route('clientCheckOrder') }}" class="nav-link text-nowrap">CHECK ORDER</a>
  </li>

  @auth
  <li class="nav-item ms-lg-2">

  </li>
  @endauth
</ul>