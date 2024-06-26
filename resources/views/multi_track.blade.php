<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Map with Multiple Points</title>
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
        var map = L.map('map').setView([-7.030915, 112.752922], 13); // Pusat peta di Kecamatan Rungkut, Surabaya

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        L.Routing.control({
            waypoints: [
                L.latLng(-7.030915, 112.752922), // Titik A (J&T)
                L.latLng(-7.026203, 112.757749),
                L.latLng(-7.025558, 112.760351),
                L.latLng(-7.024485, 112.761464),
                L.latLng(-7.020831, 112.761066)
            ],
            routeWhileDragging: true
        }).addTo(map);
    </script>
</body>
</html>
