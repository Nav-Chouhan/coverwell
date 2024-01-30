<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ config('backpack.base.html_direction') }}">

<head>
    @include(backpack_view('inc.head'))
    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">    
</head>

<body class="app flex-row align-items-center">
    @yield('header')
        <JaMemberPage title="{{ $title }}" class="container animate__animated animate__fadeIn"></JaMemberPage>
    @yield('before_scripts')
    @stack('before_scripts')
    @include(backpack_view('inc.scripts'))
    @yield('after_scripts')
    @stack('after_scripts')
    <script src="{{ mix('/js/app.js') }}"></script>    
</body>

</html>
