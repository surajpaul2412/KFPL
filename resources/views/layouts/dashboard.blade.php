<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Meta -->
    <meta name="description" content="">
    <meta name="author" content="TheHonestCo">

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/favicon.png') }}">
    @yield('meta')

    <title>DashByte - Premium Dashboard Template</title>

    <!-- Vendor CSS -->
    <link rel="stylesheet" href="{{ asset('lib/remixicon/fonts/remixicon.css') }}">
    <link rel="stylesheet" href="{{ asset('lib/apexcharts/apexcharts.css') }} ">

    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.min.css') }}">
    @yield('style')
  </head>
  <body>
    @include('layouts.sidebar')
    @include('layouts.header')

    <div class="main main-app p-3 p-lg-4">
        @yield('content')
        @include('layouts.footer')
    </div><!-- main -->

    <script src="{{ asset('lib/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('lib/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('lib/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('lib/apexcharts/apexcharts.min.js') }}"></script>

    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="{{ asset('assets/js/db.product.js') }}"></script>
    @yield('script')
  </body>
</html>
