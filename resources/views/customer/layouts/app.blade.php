<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Toyspace')</title>

  {{-- CSS (Bootstrap, Titan CSS, Custom CSS) --}}
  <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
  
  {{-- Extra head content if needed --}}
  @stack('head')
</head>
<body>
  {{-- Titan Navbar (shared across pages) --}}
  @include('partials.navbar')

  {{-- Page-specific content --}}
  <main>
    @yield('content')
  </main>

  {{-- Global Fade Transition Script --}}
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      // Fade in
      document.body.classList.add("fade-in");

      // Handle fade out on link click
      document.querySelectorAll("a").forEach(function (link) {
        link.addEventListener("click", function (e) {
          const href = this.getAttribute("href");

          if (href && !href.startsWith("#") && !href.startsWith("javascript:")) {
            e.preventDefault();
            document.body.classList.remove("fade-in");
            document.body.classList.add("fade-out");
            setTimeout(() => {
              window.location.href = href;
            }, 600); // match transition duration
          }
        });
      });
    });
  </script>

  {{-- Page-specific scripts (auction.blade.php, checkout.blade.php, etc.) --}}
  @yield('scripts')
</body>
</html>
