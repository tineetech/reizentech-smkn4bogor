<header class="navbar navbar-expand-md d-print-none">
    <div class="container-xl">
        <!-- BEGIN NAVBAR TOGGLER -->
        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbar-menu"
            aria-controls="navbar-menu"
            aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- END NAVBAR TOGGLER -->

        <!-- BEGIN NAVBAR LOGO -->
            <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
                <a href="." aria-label="Tabler" >
                    <img src="{{ asset('static/smkn4bogor-500x500.svg') }}" class="navbar-brand-image">
                    <span class="tracking-wide">{{ config('app.name') }}</span>
                </a>
            </h1>
          <!-- END NAVBAR LOGO -->

          <div class="navbar-nav flex-row order-md-last">
            <div class="d-none d-md-flex">
              <div class="nav-item">
                <a href="#" id="enable-dark" class="nav-link px-0 hide-theme-dark" title="Enable dark mode" data-bs-toggle="tooltip" data-bs-placement="bottom">
                  <i class="icon ti ti-moon"></i>
                </a>
                <a href="#" id="enable-light" class="nav-link px-0 hide-theme-light" title="Enable light mode" data-bs-toggle="tooltip" data-bs-placement="bottom">
                  <i class="icon ti ti-brightness-down"></i>
                </a>
              </div>
            </div>
            <div class="nav-item dropdown">
              <a href="#" class="nav-link d-flex lh-1 p-0 px-2" data-bs-toggle="dropdown" aria-label="Open user menu">
                <span class="avatar avatar-sm" >
                    <i class="icon ti ti-user"></i>
                </span>
                <div class="d-none d-xl-block ps-2">
                  <div>Paweł Kuna</div>
                  <div class="mt-1 small text-secondary">UI Designer</div>
                </div>
              </a>
              <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <a href="#" class="dropdown-item">Status</a>
                <a href="./profile.html" class="dropdown-item">Profile</a>
                <a href="#" class="dropdown-item">Feedback</a>
                <div class="dropdown-divider"></div>
                <a href="./settings.html" class="dropdown-item">Settings</a>
                <form method="POST" action="{{ route('auth.logout') }}">
                    @csrf
                    <button class="dropdown-item" type="submit">Logout</button>
                </form>
              </div>
            </div>
          </div>
    </div>
</header>

{{-- MENU DINAMIS --}}

<header class="navbar-expand-md">
    <div class="collapse navbar-collapse" id="navbar-menu">
        <div class="navbar">
        <div class="container-xl">
            <div class="row flex-column flex-md-row flex-fill align-items-center">
                <div class="col">
                    <!-- BEGIN NAVBAR MENU -->
                    <ul class="navbar-nav">
                        {!! $menuDinamis !!}
                    </ul>
                    <!-- END NAVBAR MENU -->
                </div>
                <div class="col col-md-auto">
                    <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSettings">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                           <i class="icon ti ti-settings"></i>
                        </span>
                        <span class="nav-link-title"> Settings </span>
                        </a>
                    </li>
                </ul>
            </div>
            </div>
        </div>
        </div>
    </div>
</header>

{{-- END MENU DINAMIS --}}


