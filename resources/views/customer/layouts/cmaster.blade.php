<!DOCTYPE html>
<html lang="en-US" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Toyspace')</title>
    
    <!-- 3D Model Viewer Script -->
    <script type="module" src="https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js"></script>
    
    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('assets/images/favicons/apple-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('assets/images/favicons/apple-icon-60x60.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/favicons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/favicons/favicon-16x16.png') }}">
    
    <!-- Stylesheets -->
    <link href="{{ asset('assets/lib/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto+Condensed:400,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Volkhov:400i" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
    <link href="{{ asset('assets/lib/animate.css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/lib/components-font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/lib/et-line-font/et-line-font.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/lib/flexslider/flexslider.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/lib/owl.carousel/dist/assets/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/lib/owl.carousel/dist/assets/owl.theme.default.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/lib/magnific-popup/dist/magnific-popup.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/lib/simple-text-rotator/simpletextrotator.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet">
    <link id="color-scheme" href="{{ asset('assets/css/colors/default.css') }}" rel="stylesheet">

    <style>
        .wallet-icon {
            color: #ffd700;
            font-size: 18px;
        }
        
        .wallet-btn {
            background: transparent;
            border: none;
            color: #fff;
            padding: 15px 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            text-decoration: none;
        }
        
        .wallet-btn:hover {
            color: #ffd700;
            text-decoration: none;
        }

        .chat-icon {
            color: #ffffff;
            font-size: 18px;
        }

        .chat-btn {
            background: transparent;
            border: none;
            color: #fff;
            padding: 15px 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .chat-btn:hover {
            color: #4CAF50;
            text-decoration: none;
        }

        .chat-indicator {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 8px;
            height: 8px;
            background-color: #ff4444;
            border-radius: 50%;
            display: none;
        }

        .chat-btn-wrapper {
            position: relative;
        }
    </style>

    @yield('styles')
    @stack('styles')
  </head>
  <body data-spy="scroll" data-target=".onpage-navigation" data-offset="60">
    <main>
      <!-- Page Loader -->
      <div class="page-loader">
        <div class="loader">Loading...</div>
      </div>

      <!-- Navigation Header -->
      <nav class="navbar navbar-custom navbar-fixed-top" role="navigation">
        <div class="container">
          <div class="navbar-header">
            <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#custom-collapse">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{ route('chome') }}">Toyspace</a>
          </div>
          <div class="collapse navbar-collapse" id="custom-collapse">
            <ul class="nav navbar-nav navbar-right">
              <!-- Virtual Wallet Button -->
              <li>
                <a href="{{ route('customer.wallet') }}" class="wallet-btn">
                  <i class="fa fa-diamond wallet-icon"></i>
                </a>
              </li>
              
              <!-- Chat Icon -->
              <li class="chat-btn-wrapper">
                <a href="{{ route('customer.chat') }}" class="chat-btn">
                  <i class="fa fa-comments chat-icon"></i>
                  <span class="chat-indicator" id="new-message-indicator"></span>
                </a>
              </li>
              
              <li><a href="{{ route('chome') }}">Home</a></li>
              <li><a href="{{ route('cproduct') }}">Products</a></li>
              <li><a href="{{ route('customer.auction') }}">Auction</a></li>
              <li><a href="{{ route('customer.trading') }}">Trade</a></li>
              <li class="dropdown">
                <a class="dropdown-toggle" href="#" data-toggle="dropdown">Account</a>
                <ul class="dropdown-menu">
                  <li><a href="{{ route('my_profile') }}">My profile</a></li>
                  <li><a href="{{ route('profile.settings') }}">Settings</a></li>
                  <li>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                      @csrf
                    </form>
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Log Out</a>
                  </li>
                </ul>
              </li>
            </ul>
          </div>
        </div>
      </nav>

      @yield('content')

      <div class="scroll-up"><a href="#totop"><i class="fa fa-angle-double-up"></i></a></div>
    </main>

    <!-- JavaScripts -->
    <script src="{{ asset('assets/lib/jquery/dist/jquery.js') }}"></script>
    <script src="{{ asset('assets/lib/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/lib/wow/dist/wow.js') }}"></script>
    <script src="{{ asset('assets/lib/jquery.mb.ytplayer/dist/jquery.mb.YTPlayer.js') }}"></script>
    <script src="{{ asset('assets/lib/isotope/dist/isotope.pkgd.js') }}"></script>
    <script src="{{ asset('assets/lib/imagesloaded/imagesloaded.pkgd.js') }}"></script>
    <script src="{{ asset('assets/lib/flexslider/jquery.flexslider.js') }}"></script>
    <script src="{{ asset('assets/lib/owl.carousel/dist/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('assets/lib/smoothscroll.js') }}"></script>
    <script src="{{ asset('assets/lib/magnific-popup/dist/jquery.magnific-popup.js') }}"></script>
    <script src="{{ asset('assets/lib/simple-text-rotator/jquery.simple-text-rotator.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>

    @yield('scripts')
    @stack('scripts')
  </body>
</html>