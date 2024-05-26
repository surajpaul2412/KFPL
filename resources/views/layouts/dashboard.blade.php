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

    <title>KFPL</title>

    <!-- Vendor CSS -->
    <link rel="stylesheet" href="{{ asset('lib/remixicon/fonts/remixicon.css') }}">
    <link rel="stylesheet" href="{{ asset('lib/apexcharts/apexcharts.css') }} ">
    <link rel="stylesheet" href="{{ asset('lib/prismjs/themes/prism.min.css') }}">
    <link rel="stylesheet" href="{{ asset('lib/select2/css/select2.min.css') }}">

    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    @yield('style')
  </head>
  <body>
    @include('layouts.sidebar')
    @include('layouts.header')

    <div class="main main-app px-3 px-lg-4 py-2 py-lg-2">
        @yield('content')
        @include('layouts.footer')
    </div><!-- main -->

    <script src="{{ asset('lib/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('lib/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('lib/prismjs/prism.js') }}"></script>
    <script src="{{ asset('lib/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('lib/chart.js/chart.min.js') }}"></script>
    <script src="{{ asset('lib/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('lib/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="{{ asset('assets/js/db.product.js') }}"></script>
    <script src="{{ asset('assets/js/db.data.js') }}"></script>
    <!-- <script src="{{ asset('assets/js/db.helpdesk.js') }}"></script> -->
    <script>
      'use strict'

      // Basic usage
      $('#select2A').select2({
        placeholder: 'Choose one',
        minimumResultsForSearch: Infinity
      });

      // With search
      $('#select2B').select2({
        placeholder: 'Choose one'
      });

      $('#select2B').one('select2:open', function(e) {
        $('input.select2-search__field').prop('placeholder', 'Search...');
      });

      // Disabled
      $('#select2C').select2({
        placeholder: 'Choose one',
        minimumResultsForSearch: Infinity
      });

      // Multiple
      $('#select2D').select2({
        placeholder: 'Choose Department',
        minimumResultsForSearch: Infinity
      });

      // Clearable
      $('#select2E').select2({
        placeholder: 'Choose one',
        allowClear: true,
        minimumResultsForSearch: Infinity
      });

      // Limit selection
      $('#select2F').select2({
        placeholder: 'Choose one or two',
        maximumSelectionLength: 2,
        minimumResultsForSearch: Infinity
      });

    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.7.6/lottie.min.js"></script>
    <script>
      // Function to load animation
      function loadLottieAnimation(containerSelector, animationPath) {
        return bodymovin.loadAnimation({
          container: document.querySelector(containerSelector),
          renderer: 'svg', // Choose renderer: svg/canvas/html
          loop: true,
          autoplay: true,
          path: animationPath // Path to your JSON animation file
        });
      }

      // Load animation for .lottie-animation
      loadLottieAnimation('.lottie-animation', '{{ asset('ticket_blink.json') }}');

      // Load animation for .lottie-animation-2
      loadLottieAnimation('.lottie-animation-2', '{{ asset('ticket_blink.json') }}');
    </script>

@if(auth()->user()->isAdmin())
<script>
$(document).ready(function(){
    $('.lottie-animation').hide();
    $('.lottie-animation-2').hide();
    var prevUpdatedAt = new Date("{{ lastTicket() }}");
    var prevQuickUpdatedAt = new Date("{{ lastQuickTicket() }}");
    var audio = new Audio('/notification.mp3');
    
    function checkNewTicket() {
        $.ajax({
            url: '/check-new-ticket',
            type: 'GET',
            success: function(response) {
                var latestUpdatedAt = new Date(response.updated_at);
                
                // Check if the updated_at value has changed
                if (latestUpdatedAt > prevUpdatedAt) {                    
                    $('#lottie-animation-admin').show();
                    audio.play();
                    alert("New ticket inserted!");
                    
                    // Update prevUpdatedAt with the latestUpdatedAt
                    prevUpdatedAt = latestUpdatedAt;
                }
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }

    function checkNewQuickTicket() {
        $.ajax({
            url: '/check-new-quick-ticket',
            type: 'GET',
            success: function(response) {
                var latestQuickUpdatedAt = new Date(response.updated_at);
                
                // Check if the updated_at value has changed
                if (latestQuickUpdatedAt > prevQuickUpdatedAt) {
                    $('#lottie-animation-admin-quick').show();  // Assuming you have a different element for quick tickets
                    audio.play();
                    alert("New quick ticket inserted!");
                    
                    // Update prevQuickUpdatedAt with the latestQuickUpdatedAt
                    prevQuickUpdatedAt = latestQuickUpdatedAt;
                }
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }

    setInterval(checkNewTicket, 10000);
    setInterval(checkNewQuickTicket, 10000);
});
</script>
@endif

@if(auth()->user()->isTrader())
<script>
$(document).ready(function(){
    $('.lottie-animation').hide();
    $('.lottie-animation-2').hide();
    var prevQuickUpdatedAt = new Date("{{ lastQuickTicket() }}");
    var audio = new Audio('/notification.mp3');

    function checkNewQuickTicket() {
        $.ajax({
            url: '/check-new-quick-ticket',
            type: 'GET',
            success: function(response) {
                var latestQuickUpdatedAt = new Date(response.updated_at);
                
                // Check if the updated_at value has changed
                if (latestQuickUpdatedAt > prevQuickUpdatedAt) {
                    $('#lottie-animation-trader-quick').show();  // Assuming you have a different element for quick tickets
                    audio.play();
                    alert("New quick ticket inserted!");
                    
                    // Update prevQuickUpdatedAt with the latestQuickUpdatedAt
                    prevQuickUpdatedAt = latestQuickUpdatedAt;
                }
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }

    setInterval(checkNewQuickTicket, 10000);
});
</script>
@endif

@if(auth()->user()->isOps())
<script>
$(document).ready(function(){
    $('.lottie-animation').hide();
    var prevCount = {{ opsCount(auth()->user()->id) }};
    var audio1 = new Audio('/notification.mp3');
    
    function checkNewTicket() {
        $.ajax({
            url: '/check-ops-ticket',
            type: 'GET',
            success: function(response) {
                var latestCount = response.ticket_count;
                
                // Check if the latest ticket count is greater than the previous count
                if (latestCount > prevCount) {
                    $('#lottie-animation-ops').show();
                    audio1.play();
                    alert("New ticket(s) updated!");
                    
                    // Update prevCount with the latest count
                    prevCount = latestCount;
                }
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }
    
    // Call checkNewTicket every 5 seconds (you can adjust the interval as needed)
    setInterval(checkNewTicket, 5000);
});
</script>
@endif


@if(auth()->user()->isAccounts())
<script>
$(document).ready(function(){
    $('.lottie-animation').hide();
    var prevCount = {{ accountsCount(auth()->user()->id) }};
    var audio2 = new Audio('/notification.mp3');
    
    function checkNewTicket() {
        $.ajax({
            url: '/check-accounts-ticket',
            type: 'GET',
            success: function(response) {
                var latestCount = response.ticket_count;
                
                // Check if the latest ticket count is greater than the previous count
                if (latestCount > prevCount) {
                    $('#lottie-animation-accounts').show();
                    audio2.play();
                    alert("New ticket(s) updated!");
                    
                    // Update prevCount with the latest count
                    prevCount = latestCount;
                }
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }
    
    // Call checkNewTicket every 5 seconds (you can adjust the interval as needed)
    setInterval(checkNewTicket, 5000);
});
</script>
@endif

@if(auth()->user()->isDealer())
<script>
$(document).ready(function(){
    $('.lottie-animation').hide();
    var prevCount = {{ dealerCount(auth()->user()->id) }};
    var audio3 = new Audio('/notification.mp3');
    
    function checkNewTicket() {
        $.ajax({
            url: '/check-dealer-ticket',
            type: 'GET',
            success: function(response) {
                var latestCount = response.ticket_count;
                
                // Check if the latest ticket count is greater than the previous count
                if (latestCount > prevCount) {
                    $('#lottie-animation-dealer').show();
                    audio3.play();
                    alert("New ticket(s) updated!");
                    
                    // Update prevCount with the latest count
                    prevCount = latestCount;
                }
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }
    
    // Call checkNewTicket every 5 seconds (you can adjust the interval as needed)
    setInterval(checkNewTicket, 5000);
});
</script>
@endif

    @yield('script')
  </body>
</html>
