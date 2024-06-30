<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Map with Geocoding</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
  <style>
    #map {
      height: 100vh;
    }

    .leaflet-routing-container {
      max-height: 400px;
      overflow-y: auto;
    }
  </style>
</head>
<body>
  <div id="map"></div>
  <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
  <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
  <script>
    var map = L.map('map').setView([-7.3216, 112.7612], 13); // Pusat peta di Kecamatan Rungkut

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Fungsi untuk mendapatkan koordinat dari alamat menggunakan Nominatim
    function getCoordinates(address, callback) {
      var url = 'https://nominatim.openstreetmap.org/search?format=json&limit=3&q=' + encodeURIComponent(address);
      fetch(url)
        .then(response => response.json())
        .then(data => {
          if (data.length > 0) {
            var latLng = L.latLng(data[0].lat, data[0].lon);
            callback(latLng);
          } else {
            alert('Alamat tidak ditemukan: ' + address);
          }
        })
        .catch(error => console.error('Error:', error));
    }

    // Array dinamis alamat
    var addresses = @json($addresses); // Mengambil data dari controller

    // Mendapatkan koordinat untuk setiap alamat dan menambahkan rute ke peta
    function addRoutesToMap() {
      var waypoints = [];

      addresses.forEach(function(item) {
        getCoordinates(item.address, function(latLng) {
          waypoints.push(latLng);

          if (waypoints.length === addresses.length) {
            var control = L.Routing.control({
              waypoints: waypoints,
              router: L.Routing.osrmv1({
                serviceUrl: 'https://router.project-osrm.org/route/v1',
                profile: 'driving',
                alternatives: true // Meminta rute alternatif
              }),
              routeWhileDragging: true,
              showAlternatives: true,
              altLineOptions: {
                styles: [
                  {color: 'blue', opacity: 0.7, weight: 4},
                  {color: 'green', opacity: 0.7, weight: 4},
                  {color: 'purple', opacity: 0.7, weight: 4}
                ]
              }
            }).addTo(map);

            // Event listener untuk menangani hasil perhitungan rute
            control.on('routesfound', function(e) {
              var routes = e.routes;
              var summaryContainer = L.DomUtil.create('div', 'summary');

              routes.forEach(function(route, i) {
                var summary = '<h3>Rute ' + (i + 1) + ':</h3>';
                summary += '<p>Jarak: ' + (route.summary.totalDistance / 1000).toFixed(2) + ' km, ';
                summary += 'Waktu: ' + Math.round(route.summary.totalTime / 60) + ' menit</p>';
                summaryContainer.innerHTML += summary;

                // Tambahkan garis rute ke peta dengan gaya yang berbeda berdasarkan indeks (i)
                var routeLine = L.polyline(route.coordinates, { color: control.options.altLineOptions.styles[i].color }).addTo(map);
              });

              L.DomUtil.get('map').appendChild(summaryContainer);
            });
          }
        });
      });
    }

    // Panggil fungsi untuk menambahkan rute ke peta
    addRoutesToMap();

  </script>
</body>
</html>
