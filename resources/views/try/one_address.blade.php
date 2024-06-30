<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Map with Geocoding</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
  <style>
    #map {
      height: 100vh;
    }
  </style>
</head>
<body>
  <div id="map"></div>
  <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
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

    // format alamat: nama jalan, kelurahan, kecamatan, kab/kota, negara
    var address = "{{ $address }}";

    // Mendapatkan koordinat untuk alamat dan menambahkan penanda ke peta
    getCoordinates(address, function(latLng) {
      var marker = L.marker(latLng).addTo(map);
      map.setView(latLng, 15); // Mengatur pusat peta pada koordinat yang ditemukan
      marker.bindPopup('<b>Lokasi:</b><br>' + address).openPopup();
    });
  </script>
</body>
</html>
