<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYfZ8osu1Nd6Z89ify25QV9guujx43ITvfi12/QExE" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js" integrity="sha384-Y4oOpwW3duJdCWv5ly8SCFYWqFDsfob/3GkgExXKV4idmbt98QcxXYs9UoXAB7BZ" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css">

    @auth    
      <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
      <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    @endauth
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <title>{{ $title }}</title>

    <style>
      .box-with-shadow {
        box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.1);
        transition: box-shadow 0.3s ease; /* Transisi efek bayangan untuk smooth transition */
      }

      .box-with-shadow:hover {
        box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.3); /* Aturan tambahan saat di hover */
      }
    </style>
  </head>
  <body class="d-flex flex-column" style="height: fit-content">

    @unless(auth()->check() || Request::is('materi*'))
      @include('partials.navbar')
    @endunless
    
    <div id="content">
      @yield('container')
    </div>

    @unless (auth()->check() || Request::is('bab*') || Request::is('sub_bab*'))
        @include('partials.footer')
    @endunless
    
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/popper.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <script>
      function btnConfirmLogout() {
        let text = "Yakin logout?";
        if (confirm(text) == true) {
          return true;
        } else {
          return false;
        }
        document.getElementById("demo").innerHTML = text;
      }
      function showPassword() {
        var x = document.getElementById("password");
        var y = document.getElementById("password_confirmation");
        var z = document.getElementById("current_password");
        if (x.type === "password") {
          x.type = "text";
          y.type = "text";
          z.type = "text";
        } else {
          x.type = "password";
          y.type = "password";
          z.type = "password";
        }
      }
      </script>
  </body>
</html>