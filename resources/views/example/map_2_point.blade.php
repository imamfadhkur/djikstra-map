<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Map 2 Point</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
    <style>
        #map {
            height: 600px;
        }

        .leaflet-routing-container {
            max-height: 300px; /* Set the maximum height for the routing container */
            overflow-y: auto;  /* Enable vertical scrolling */
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

        // Rute pertama dari A ke B
        L.Routing.control({
            waypoints: [
                L.latLng(-7.3216, 112.7624), // Titik A (Terminal Rungkut)
                L.latLng(-7.3278, 112.7632)  // Titik B (Lokasi lain di Rungkut)
            ],
            lineOptions: {
                styles: [{ color: 'red', weight: 4, opacity: 0.7 }]
            },
            createMarker: function(i, waypoint, n) {
                return L.marker(waypoint.latLng).bindPopup('Waypoint ' + (i + 1));
            },
            routeWhileDragging: true
        }).addTo(map);

        // Rute kedua dari C ke D
        L.Routing.control({
            waypoints: [
                L.latLng(-7.3380, 112.7680), // Titik E (Lokasi lain di Rungkut)
                L.latLng(-7.3400, 112.7700)  // Titik F (Lokasi lain di Rungkut)
            ],
            lineOptions: {
                styles: [{ color: 'green', weight: 4, opacity: 0.7 }]
            },
            createMarker: function(i, waypoint, n) {
                return L.marker(waypoint.latLng).bindPopup('Waypoint ' + (i + 1));
            },
            routeWhileDragging: true
        }).addTo(map);
    </script>
</body>
</html>
