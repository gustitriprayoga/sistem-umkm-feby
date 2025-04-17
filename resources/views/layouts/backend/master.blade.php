<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="dark" data-color-theme="Blue_Theme" data-layout="vertical">

<head>
    <!-- Required meta tags -->
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>{{ $settings->name }} | @yield('title')</title>

    <!-- Favicon icon-->
    <link rel="shortcut icon" type="image/png" href="{{ 'backend/dist/assets/images/logos/favicon.png' }}" />

    @include('layouts.backend.styles')
</head>

<body>
    @include('layouts.backend.partial.toast')
    <!-- Preloader -->
    <div class="preloader">
        <img src="{{ asset('backend/dist/assets/images/logos/favicon.png') }}" alt="loader"
            class="lds-ripple img-fluid" />
    </div>
    <div id="main-wrapper">
        <!-- Sidebar Start -->
        @include('layouts.backend.sidebar')
        <!--  Sidebar End -->
        <div class="page-wrapper">
            <!--  Header Start -->
            @include('layouts.backend.partial.header')
            <!--  Header End -->

            @include('layouts.backend.left-sidebar')

            <div class="body-wrapper">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
            @include('layouts.backend.partial.script')

            @include('layouts.backend.partial.canvas')
        </div>

        <!--  Search Bar -->
        @include('layouts.backend.partial.search')

        <!--  Shopping Cart -->
        @include('layouts.backend.partial.cart')
    </div>
    <div class="dark-transparent sidebartoggler"></div>

    @include('layouts.backend.scripts')
</body>

</html>
