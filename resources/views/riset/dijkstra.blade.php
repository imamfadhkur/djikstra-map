<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dijkstra Path</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map { height: 100vh; }
    </style>
</head>
<body>
    <div id="map"></div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        const coordinates = @json($coordinates);

        const map = L.map('map').setView([coordinates[0].lat, coordinates[0].lng], 16);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        const latlngs = coordinates.map(coord => [coord.lat, coord.lng]);

        L.polyline(latlngs, { color: 'blue' }).addTo(map);

        coordinates.forEach(coord => {
            L.marker([coord.lat, coord.lng]).addTo(map);
        });
    </script>
</body>
</html>
