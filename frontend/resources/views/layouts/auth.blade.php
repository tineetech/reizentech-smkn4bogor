<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <meta name="robots" content="noindex">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>

    <!-- General CSS Files -->
    <link rel="stylesheet" href="{{ asset('css/tabler.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tabler-socials.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tabler-vendors.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tabler-themes.min.css') }}">
    <link rel="stylesheet" href="{{ asset('libs/Inter-4.1/web/inter.css') }}">
    <link rel="stylesheet" href="{{ asset('libs/tabler-icons-3.20.0/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    @stack('style')
</head>

<body class="d-flex flex-column " >
    <script src="{{ asset('js/tabler-theme.min.js') }}"></script>

    @yield('main')

    <!-- General JS Scripts -->
    <script src="{{ asset('js/tabler.min.js') }}"></script>
    <script src="{{ asset('js/scripts.js') }}"></script>

    @stack('scripts')
</body>

</html>
