<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Map 1 Point</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
    <style>
        #map {
            height: 600px;
        }
    </style>
</head>
<body>
    <div id="map"></div>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
    <script>
        var map = L.map('map').setView([-7.3216, 112.7612], 13); // Pusat peta di Kecamatan Rungkut, Surabaya

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Menggunakan Leaflet Routing Machine untuk menambahkan rute
        L.Routing.control({
            waypoints: [
                L.latLng(-7.3216, 112.7624), // Titik A (Terminal Rungkut)
                L.latLng(-7.3367, 112.7661)  // Titik B (Universitas Pembangunan Nasional "Veteran" Jawa Timur)
            ],
            routeWhileDragging: true
        }).addTo(map);
    </script>
</body>
</html>
