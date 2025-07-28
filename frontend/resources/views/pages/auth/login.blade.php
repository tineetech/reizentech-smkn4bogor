@extends('layouts.auth')

@section('title', $title)

@push('style')
    <!-- CSS Libraries -->
@endpush

@section('main')

<div class="row g-0 flex-fill">
    <div class="col-12 col-lg-6 col-xl-4 border-top-wide border-primary d-flex flex-column justify-content-center">
        <div class="container container-tight my-5 px-lg-5">
            <div class="text-center mb-4">
                <a href="." aria-label="Tabler" class="navbar-brand navbar-brand-autodark">
                    <img src="{{ asset('static/smkn4bogor-500x500.svg') }}" class="navbar-brand-image logo-large-login" >
                </a>
            </div>
            <h2 class="h3 text-center mb-3">Login {{ config('app.name') }}</h2>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    <div class="alert-icon">
                        <i class="icon ti ti-check"></i>
                    </div>
                    {{ session('success') }}
                    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                </div>
            @endif

            @error('errors')
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <div class="alert-icon">
                        <i class="icon ti ti-alert-circle"></i>
                    </div>
                    {{ $message }}
                    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                </div>
            @enderror

            <form action="{{ route('auth.login') }}" method="POST" autocomplete="off" >
                @csrf
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" autocomplete="off" value="{{ old('username') }}" required />
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-2">
                    <label class="form-label">
                        Password
                    </label>
                    <div class="input-group input-group-flat">
                        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="off" required/>
                        <span class="input-group-text">
                            <a href="#" class="link-secondary" onclick="myFunction()" data-bs-toggle="tooltip" aria-label="Show password" data-bs-original-title="Show password">
                                <i id="toogleIcon" class="icon ti ti-eye"></i>
                            </a>
                        </span>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">Sign in</button>
                </div>
            </form>

            <div class="text-center text-secondary mt-3"><a href="" tabindex="-1">Lupa password?</a></div>

            <div class="text-center text-muted mt-3">
                Copyright &copy; {{ config('app.year_created') }}
                {{ date('Y') > config('app.year_created') ? ' - ' . date('Y') : '' }}
                <a href="#" class="link-secondary">SMKN 4 Bogor.</a>
            </div>
        </div>
        </div>
        <div class="col-12 col-lg-6 col-xl-8 d-none d-lg-block">
        <!-- Photo -->
        <div class="bg-cover h-100 min-vh-100" style="background-image: url({{ asset('static/b-smkn4bogor.svg') }})"></div>
    </div>
</div>


@endsection

@push('scripts')
    <script>
        function myFunction() {
            let x = document.getElementById("password");
            let icon = document.getElementById("toogleIcon");
            if (x.type === "password") {
				x.type = "text";
				icon.classList.remove('ti-eye');

				icon.classList.add('ti-eye-off');
            } else {
				x.type = "password";
				icon.classList.remove('ti-eye-off');
				icon.classList.add('ti-eye');
            }
        }
    </script>
@endpush

