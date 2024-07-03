<!doctype html>
<html lang="en" data-bs-theme="auto">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.122.0">
    <title>Traveling Salesman Problem - Dijkstra</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/5.3/examples/album/">

    

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3">

    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />

    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }

      .b-example-divider {
        width: 100%;
        height: 3rem;
        background-color: rgba(0, 0, 0, .1);
        border: solid rgba(0, 0, 0, .15);
        border-width: 1px 0;
        box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
      }

      .b-example-vr {
        flex-shrink: 0;
        width: 1.5rem;
        height: 100vh;
      }

      .bi {
        vertical-align: -.125em;
        fill: currentColor;
      }

      .nav-scroller {
        position: relative;
        z-index: 2;
        height: 2.75rem;
        overflow-y: hidden;
      }

      .nav-scroller .nav {
        display: flex;
        flex-wrap: nowrap;
        padding-bottom: 1rem;
        margin-top: -1px;
        overflow-x: auto;
        text-align: center;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
      }

      .btn-bd-primary {
        --bd-violet-bg: #712cf9;
        --bd-violet-rgb: 112.520718, 44.062154, 249.437846;

        --bs-btn-font-weight: 600;
        --bs-btn-color: var(--bs-white);
        --bs-btn-bg: var(--bd-violet-bg);
        --bs-btn-border-color: var(--bd-violet-bg);
        --bs-btn-hover-color: var(--bs-white);
        --bs-btn-hover-bg: #6528e0;
        --bs-btn-hover-border-color: #6528e0;
        --bs-btn-focus-shadow-rgb: var(--bd-violet-rgb);
        --bs-btn-active-color: var(--bs-btn-hover-color);
        --bs-btn-active-bg: #5a23c8;
        --bs-btn-active-border-color: #5a23c8;
      }

      .bd-mode-toggle {
        z-index: 1500;
      }

      .bd-mode-toggle .dropdown-menu .active .bi {
        display: block !important;
      }

      #map {
            height: 250px;
        }

      #map_1_address {
            height: 250px;
        }
      
        #map_2_address {
            height: 250px;
        }
      
        #multi_address {
            height: 250px;
        }
    </style>

    
  </head>
  <body>
    <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
      <symbol id="check2" viewBox="0 0 16 16">
        <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
      </symbol>
      <symbol id="circle-half" viewBox="0 0 16 16">
        <path d="M8 15A7 7 0 1 0 8 1v14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16z"/>
      </symbol>
      <symbol id="moon-stars-fill" viewBox="0 0 16 16">
        <path d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278z"/>
        <path d="M10.794 3.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387a1.734 1.734 0 0 0-1.097 1.097l-.387 1.162a.217.217 0 0 1-.412 0l-.387-1.162A1.734 1.734 0 0 0 9.31 6.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387a1.734 1.734 0 0 0 1.097-1.097l.387-1.162zM13.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.156 1.156 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.156 1.156 0 0 0-.732-.732l-.774-.258a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732L13.863.1z"/>
      </symbol>
      <symbol id="sun-fill" viewBox="0 0 16 16">
        <path d="M8 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"/>
      </symbol>
    </svg>

    {{-- <div class="dropdown position-fixed bottom-0 end-0 mb-3 me-3 bd-mode-toggle">
      <button class="btn btn-bd-primary py-2 dropdown-toggle d-flex align-items-center"
              id="bd-theme"
              type="button"
              aria-expanded="false"
              data-bs-toggle="dropdown"
              aria-label="Toggle theme (auto)">
        <svg class="bi my-1 theme-icon-active" width="1em" height="1em"><use href="#circle-half"></use></svg>
        <span class="visually-hidden" id="bd-theme-text">Toggle theme</span>
      </button>
      <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="bd-theme-text">
        <li>
          <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light" aria-pressed="false">
            <svg class="bi me-2 opacity-50" width="1em" height="1em"><use href="#sun-fill"></use></svg>
            Light
            <svg class="bi ms-auto d-none" width="1em" height="1em"><use href="#check2"></use></svg>
          </button>
        </li>
        <li>
          <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark" aria-pressed="false">
            <svg class="bi me-2 opacity-50" width="1em" height="1em"><use href="#moon-stars-fill"></use></svg>
            Dark
            <svg class="bi ms-auto d-none" width="1em" height="1em"><use href="#check2"></use></svg>
          </button>
        </li>
        <li>
          <button type="button" class="dropdown-item d-flex align-items-center active" data-bs-theme-value="auto" aria-pressed="true">
            <svg class="bi me-2 opacity-50" width="1em" height="1em"><use href="#circle-half"></use></svg>
            Auto
            <svg class="bi ms-auto d-none" width="1em" height="1em"><use href="#check2"></use></svg>
          </button>
        </li>
      </ul>
    </div> --}}

    
<header data-bs-theme="dark">
  <div class="collapse text-bg-dark" id="navbarHeader">
    <div class="container">
      <div class="row">
        <div class="col-sm-8 col-md-7 py-4">
          <h4>About</h4>
          <p class="text-body-secondary">Add some information about the album below, the author, or any other background context. Make it a few sentences long so folks can pick up some informative tidbits. Then, link them off to some social networking sites or contact information.</p>
        </div>
        <div class="col-sm-4 offset-md-1 py-4">
          <h4>Contact</h4>
          <ul class="list-unstyled">
            <li><a href="#" class="text-white">Follow on Twitter</a></li>
            <li><a href="#" class="text-white">Like on Facebook</a></li>
            <li><a href="#" class="text-white">Email me</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="navbar navbar-dark bg-dark shadow-sm">
    <div class="container">
      <a href="#" class="navbar-brand d-flex align-items-center">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-map" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M15.817.113A.5.5 0 0 1 16 .5v14a.5.5 0 0 1-.402.49l-5 1a.5.5 0 0 1-.196 0L5.5 15.01l-4.902.98A.5.5 0 0 1 0 15.5v-14a.5.5 0 0 1 .402-.49l5-1a.5.5 0 0 1 .196 0L10.5.99l4.902-.98a.5.5 0 0 1 .415.103M10 1.91l-4-.8v12.98l4 .8zm1 12.98 4-.8V1.11l-4 .8zm-6-.8V1.11l-4 .8v12.98z"/>
        </svg>
        <strong>&nbsp; Maps</strong>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
    </div>
  </div>
</header>

<main>

  <section class="py-5 text-center container">
    <div class="row py-lg-5">
      <div class="col-lg-8 col-md-10 mx-auto">
        <h1 class="fw-light">Traveling Salesman Problem - Dijkstra</h1>
        <h4 class="fw-light">Open Street Maps</h4>
        <p class="lead text-body-secondary">Lorem ipsum dolor sit, amet consectetur adipisicing elit. Repellendus dicta magnam tempore velit nam quasi id neque tenetur quidem ab.</p>
        {{-- <p>
          contoh <br>
          <a href="{{ url('map-1-point') }}" target="_blank" class="btn btn-primary my-2">map 1 node</a>
          <a href="{{ url('map-2-point') }}" target="_blank" class="btn btn-primary my-2">map 2 node</a>
          <a href="{{ url('map-3-point') }}" target="_blank" class="btn btn-primary my-2">map 3 node</a>
          <a href="{{ url('multi-track') }}" target="_blank" class="btn btn-primary my-2">multi node</a>
        </p> --}}
        <p>
          coba sendiri :) <br>
          <a href="#page_lat_n_long" class="btn btn-primary my-2">lat. & long.</a>
          <a href="#page_1_alamat" class="btn btn-primary my-2">1 node</a>
          <a href="#page_2_alamat" class="btn btn-primary my-2">2 node</a>
          <a href="#multi_alamat" class="btn btn-primary my-2">multi node</a>
        </p>
        <p>
          hasil penelitian <br>
          {{-- <a href="{{ url('get-lat-long') }}" class="btn btn-primary my-2">get langitude longitude</a> --}}
          <a href="{{ url('non-dijkstra') }}" class="btn btn-primary my-2">non dijkstra</a>
          <a href="{{ url('dijkstra') }}" class="btn btn-primary my-2">dijkstra</a>
        </p>
      </div>
    </div>
  </section>

  <div class="album py-5 bg-body-tertiary">
    <div class="container">
      <div class="row">
        <div id="page_lat_n_long" class="col pt-4">
          <div class="card shadow-sm">
            {{-- <svg xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: Thumbnail" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Placeholder</title><rect width="100%" height="100%" fill="#55595c"/><text x="50%" y="50%" fill="#eceeef" dy=".3em">Thumbnail</text></svg> --}}
            <div class="bd-placeholder-img card-img-top" width="100%" id="map"></div>
            <div class="card-body">
              <h4>input latitude dan longitude</h4>
              <p class="card-text">Lorem ipsum dolor sit amet consectetur adipisicing elit. Repellendus dicta magnam tempore velit nam quasi id neque tenetur quidem ab.</p>
              <form action="{{ url('lat_n_long') }}" method="get">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group mb-1">
                      <label for="latitude">Latitude <span class="text-danger">(*)</span></label>
                      <input type="text" class="form-control" id="latitude" name="latitude" placeholder="Latitude">
                    </div>
                    <div class="form-group mb-1">
                      <label for="longitude">Longitude <span class="text-danger">(*)</span></label>
                      <input type="text" class="form-control" id="longitude" name="longitude" placeholder="Longitude">
                    </div>
                    <i><span class="text-danger">(*)</span> : wajib diisi.</i> <br>
                    <button type="submit" class="btn btn-primary px-5 mt-2">cek</button>
                  </div>
              </form>
            </div>
          </div>
        </div>
        <div id="page_1_alamat" class="col mt-5 pt-4">
          <div class="card shadow-sm">
            {{-- <svg xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: Thumbnail" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Placeholder</title><rect width="100%" height="100%" fill="#55595c"/><text x="50%" y="50%" fill="#eceeef" dy=".3em">Thumbnail</text></svg> --}}
            <div class="bd-placeholder-img card-img-top" width="100%" id="map_1_address"></div>
            <div class="card-body">
              <h4>Input 1 node</h4>
              {{-- nama jalan, kelurahan, kecamatan, kab/kota, negara --}}
              <p class="card-text">Lorem ipsum dolor sit amet consectetur adipisicing elit. Repellendus dicta magnam tempore velit nam quasi id neque tenetur quidem ab.</p>
              <form action="{{ url('one_address') }}" method="get">
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group mb-1">
                      <label for="nama_jalan">nama jalan</label>
                      <input type="text" class="form-control" id="nama_jalan" name="nama_jalan" placeholder="nama jalan">
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group mb-1">
                      <label for="kelurahan">kelurahan <span class="text-danger">(*)</span></label>
                      <input type="text" class="form-control" id="kelurahan" name="kelurahan" placeholder="kelurahan" required>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group mb-1">
                      <label for="kecamatan">kecamatan <span class="text-danger">(*)</span></label>
                      <input type="text" class="form-control" id="kecamatan" name="kecamatan" placeholder="kecamatan" required>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group mb-1">
                      <label for="kab_kota">kabupaten/kota <span class="text-danger">(*)</span></label>
                      <input type="text" class="form-control" id="kab_kota" name="kab_kota" placeholder="kabupaten atau kota" required>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group mb-1">
                      <label for="negara">negara <span class="text-danger">(*)</span></label>
                      <input type="text" class="form-control" id="negara" name="negara" placeholder="negara" required>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col">
                      <i><span class="text-danger">(*)</span> : wajib diisi. </i><br>
                      <button type="submit" class="btn btn-primary px-5 mt-2">cek</button>
                    </div>
                  </div>
              </form>
            </div>
          </div>
        </div>
        <div id="page_2_alamat" class="col mt-5 pt-4">
          <div class="card shadow-sm">
            {{-- <svg xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: Thumbnail" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Placeholder</title><rect width="100%" height="100%" fill="#55595c"/><text x="50%" y="50%" fill="#eceeef" dy=".3em">Thumbnail</text></svg> --}}
            <div class="bd-placeholder-img card-img-top" width="100%" id="map_2_address"></div>
            <div class="card-body">
              <h4>Input 2 node</h4>
              {{-- nama jalan, kelurahan, kecamatan, kab/kota, negara --}}
              <p class="card-text">Lorem ipsum dolor sit amet consectetur adipisicing elit. Repellendus dicta magnam tempore velit nam quasi id neque tenetur quidem ab.</p>
              <form action="{{ url('two_address') }}" method="get">
                <div class="row">
                  <div class="col">
                   <b>alamat 1</b>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group mb-1">
                      <label for="nama_jalan_1">nama jalan</label>
                      <input type="text" class="form-control" id="nama_jalan_1" name="nama_jalan_1" placeholder="nama jalan">
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group mb-1">
                      <label for="kelurahan_1">kelurahan <span class="text-danger">(*)</span></label>
                      <input type="text" class="form-control" id="kelurahan_1" name="kelurahan_1" placeholder="kelurahan" required>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group mb-1">
                      <label for="kecamatan_1">kecamatan <span class="text-danger">(*)</span></label>
                      <input type="text" class="form-control" id="kecamatan_1" name="kecamatan_1" placeholder="kecamatan" required>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group mb-1">
                      <label for="kab_kota_1">kabupaten/kota <span class="text-danger">(*)</span></label>
                      <input type="text" class="form-control" id="kab_kota_1" name="kab_kota_1" placeholder="kabupaten atau kota" required>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group mb-1">
                      <label for="negara_1">negara <span class="text-danger">(*)</span></label>
                      <input type="text" class="form-control" id="negara_1" name="negara_1" placeholder="negara" required>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col">
                   <b>alamat 2</b>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group mb-1">
                      <label for="nama_jalan_2">nama jalan</label>
                      <input type="text" class="form-control" id="nama_jalan_2" name="nama_jalan_2" placeholder="nama jalan">
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group mb-1">
                      <label for="kelurahan_2">kelurahan <span class="text-danger">(*)</span></label>
                      <input type="text" class="form-control" id="kelurahan_2" name="kelurahan_2" placeholder="kelurahan" required>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group mb-1">
                      <label for="kecamatan_2">kecamatan <span class="text-danger">(*)</span></label>
                      <input type="text" class="form-control" id="kecamatan_2" name="kecamatan_2" placeholder="kecamatan" required>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group mb-1">
                      <label for="kab_kota_2">kabupaten/kota <span class="text-danger">(*)</span></label>
                      <input type="text" class="form-control" id="kab_kota_2" name="kab_kota_2" placeholder="kabupaten atau kota" required>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group mb-1">
                      <label for="negara_2">negara <span class="text-danger">(*)</span></label>
                      <input type="text" class="form-control" id="negara_2" name="negara_2" placeholder="negara" required>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col">
                    <i><span class="text-danger">(*)</span> : wajib diisi. </i><br>
                    <button type="submit" class="btn btn-primary px-5 mt-2">cek</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
        <div id="multi_alamat" class="col mt-5 pt-4">
          <div class="card shadow-sm">
            <div class="bd-placeholder-img card-img-top" width="100%" id="multi_address"></div>
            <div class="card-body">
              <h4>Input Multi node</h4>
              <p class="card-text">Lorem ipsum dolor sit amet consectetur adipisicing elit. Repellendus dicta magnam tempore velit nam quasi id neque tenetur quidem ab.</p>
              <form action="{{ url('multi_address') }}" method="get" id="addressForm">
                <div id="addressFields">
                  <div class="address-group">
                    <div class="row">
                      <div class="col">
                       <b>Alamat 1</b>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-3">
                        <div class="form-group mb-1">
                          <label for="nama_jalan_1">Nama Jalan</label>
                          <input type="text" class="form-control" name="nama_jalan[]" placeholder="Nama Jalan">
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group mb-1">
                          <label for="kelurahan_1">Kelurahan <span class="text-danger">(*)</span></label>
                          <input type="text" class="form-control" name="kelurahan[]" placeholder="Kelurahan" required>
                        </div>
                      </div>
                      <div class="col-md-2">
                        <div class="form-group mb-1">
                          <label for="kecamatan_1">Kecamatan <span class="text-danger">(*)</span></label>
                          <input type="text" class="form-control" name="kecamatan[]" placeholder="Kecamatan" value="rungkut" required>
                        </div>
                      </div>
                      <div class="col-md-2">
                        <div class="form-group mb-1">
                          <label for="kab_kota_1">Kabupaten/Kota <span class="text-danger">(*)</span></label>
                          <input type="text" class="form-control" name="kab_kota[]" placeholder="Kabupaten atau Kota" value="surabaya" required>
                        </div>
                      </div>
                      <div class="col-md-2">
                        <div class="form-group mb-1">
                          <label for="negara_1">Negara <span class="text-danger">(*)</span></label>
                          <input type="text" class="form-control" name="negara[]" placeholder="Negara" value="indonesia" required>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col">
                    <i><span class="text-danger">(*)</span> : wajib diisi. </i><br>
                    <button type="button" class="btn btn-secondary px-5 mt-2" onclick="addAddress()">Tambah node</button>
                    <button type="submit" class="btn btn-primary px-5 mt-2">Cek</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>

        
      </div>
    </div>
  </div>

</main>

<footer class="text-body-secondary py-5">
  <div class="container">
    <p class="float-end mb-1">
      <a href="#">Back to top</a> <br>
      <a href="{{ url('login') }}">Login</a>
    </p>
    <p class="mb-1">Lorem ipsum dolor sit amet consectetur adipisicing.</p>
    <p class="mb-0">Lorem, ipsum. <a href="/">Visit the homepage</a> Lorem ipsum dolor sit amet consectetur.</p>
  </div>
</footer>
      <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
      <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
      <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
      <script>
        // Fungsi untuk menambahkan input node baru
        function addAddress() {
          var addressFields = document.getElementById('addressFields');
          var newAddressGroup = document.createElement('div');
          newAddressGroup.className = 'address-group';
          var addressIndex = addressFields.getElementsByClassName('address-group').length + 1;
          newAddressGroup.innerHTML = `
            <div class="row">
              <div class="col">
              <b>Alamat ${addressIndex}</b>
              </div>
            </div>
            <div class="row">
              <div class="col-md-3">
                <div class="form-group mb-1">
                  <label for="nama_jalan_${addressIndex}">Nama Jalan</label>
                  <input type="text" class="form-control" name="nama_jalan[]" placeholder="Nama Jalan">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group mb-1">
                  <label for="kelurahan_${addressIndex}">Kelurahan <span class="text-danger">(*)</span></label>
                  <input type="text" class="form-control" name="kelurahan[]" placeholder="Kelurahan" required>
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group mb-1">
                  <label for="kecamatan_${addressIndex}">Kecamatan <span class="text-danger">(*)</span></label>
                  <input type="text" class="form-control" name="kecamatan[]" placeholder="Kecamatan" value="rungkut" required>
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group mb-1">
                  <label for="kab_kota_${addressIndex}">Kabupaten/Kota <span class="text-danger">(*)</span></label>
                  <input type="text" class="form-control" name="kab_kota[]" placeholder="Kabupaten atau Kota" value="surabaya" required>
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group mb-1">
                  <label for="negara_${addressIndex}">Negara <span class="text-danger">(*)</span></label>
                  <input type="text" class="form-control" name="negara[]" placeholder="Negara" value="indonesia" required>
                </div>
              </div>
            </div>`;
          addressFields.appendChild(newAddressGroup);
        }
        
          // Fungsi untuk membuat peta dan menambahkan tile layer
          function createMap(containerId, centerCoords) {
              var map = L.map(containerId).setView(centerCoords, 7);
              
              L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                  attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
              }).addTo(map);
      
              return map;
          }
      
          // Pusat peta di Kecamatan Rungkut, Surabaya
          var centerCoords = [-7.030915, 112.752922];
      
          // Buat peta pertama
          var map = createMap('map', centerCoords);
      
          // Buat peta kedua dengan ID yang berbeda
          var map_1_address = createMap('map_1_address', centerCoords);
          var map_2_address = createMap('map_2_address', centerCoords);
          var multi_address = createMap('multi_address', centerCoords);
      </script>    
    </body>
</html>
