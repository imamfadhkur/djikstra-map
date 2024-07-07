<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shortest Path</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />
    <style>
        #map {
            height: 900px;
        }
        .leaflet-routing-container.leaflet-control {
            display: none; /* Ini akan menyembunyikan seluruh container instruksi rute */
        }
    </style>
</head>
<body>
    <div id="map"></div>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>
    <script>
        var map = L.map('map').setView([{{ $coordinates[0]['lat'] }}, {{ $coordinates[0]['lng'] }}], 15);
        // document.getElementById('route-instructions').style.display = 'none';
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19
        }).addTo(map);

        var waypoints = @json($coordinates).map(function(waypoint) {
            return L.latLng(waypoint.lat, waypoint.lng);
        });

        L.Routing.control({
            waypoints: waypoints,
            routeWhileDragging: true,
            showAlternatives: false,
            createMarker: function(i, waypoint, n) {
                return L.marker(waypoint.latLng);
            }
        }).addTo(map);
    </script>
</body>
</html>
