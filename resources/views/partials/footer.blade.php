<!-- Remove the container if you want to extend the Footer to full width. -->
<footer class="bg-dark text-center text-white">
    <!-- Grid container -->
    <div class="p-4 pb-0">
      <!-- Section: Social media -->
      <section class="mb-4">
        <!-- Facebook -->
        {{-- <a class="btn btn-outline-light btn-floating m-1" href="#!" role="button"
          ><i class="bi bi-facebook"></i></a> --}}
  
        <!-- Twitter -->
        {{-- <a class="btn btn-outline-light btn-floating m-1" href="#!" role="button"
          ><i class="bi bi-twitter"></i></a> --}}
  
        <!-- Google -->
        {{-- <a class="btn btn-outline-light btn-floating m-1" href="#!" role="button"
          ><i class="bi bi-google"></i></a> --}}
  
        <!-- Instagram -->
        {{-- <a class="btn btn-outline-light btn-floating m-1" href="#!" role="button"
          ><i class="bi bi-instagram"></i></a> --}}
      </section>
      <!-- Section: Social media -->
    </div>
    <!-- Grid container -->

    <!-- Copyright -->
    <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
      @isset($profil->footer)  
        <a class="text-white text-decoration-none" href="/">{{ $profil->footer }}</a>
      @endisset
      <a href="/login" class="text-light">login</a>
    </div>
    <!-- Copyright -->
  </footer>
  <!-- End of .container -->