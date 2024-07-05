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
        // Ambil latitude dan longitude dari PHP (contoh menggunakan Blade)
        var latitude = {{ $latitude }};
        var longitude = {{ $longitude }};
    
        // Buat peta dengan peta awal
        var map = L.map('map').setView([latitude, longitude], 18); // Atur level zoom sesuai kebutuhan
    
        // Tambahkan tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
    
        // Tambahkan routing control dengan waypoints dari data yang diterima
        L.Routing.control({
            waypoints: [
                L.latLng(latitude, longitude), // Titik A (Latitude dan Longitude dari controller)
                // Tambahkan titik B sesuai kebutuhan
            ],
            routeWhileDragging: true
        }).addTo(map);
    </script>    
</body>
</html>
