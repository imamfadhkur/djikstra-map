<!DOCTYPE html>
<html>
<head>
    <title>Leaflet Kurir</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
        #map { height: 600px; }
    </style>
</head>
<body>
    <div id="map"></div>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        // Inisialisasi peta
        var map = L.map('map').setView([-7.32076430, 112.79239950], 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Data koordinat kurir
        var kurirData = {
            1: [
                ['Jalan Medokan Asri Utara XIV Blok Q 10, Rungkut, Surabaya, Jawa Timur', -7.32076430, 112.79239950],
                ['Jalan Medokan Asri Utara XIV Blok Q No. 29, Rungkut, Surabaya, Jawa Timur', -7.32076430, 112.79239950]
            ],
            3: [
                ['Jalan Medokan Asri Utara 13 No. Q61, Rungkut, Surabaya, Jawa Timur', -7.32447190, 112.79396570]
            ],
            4: [
                ['Jalan Medokan Asri Utara XIII Blok P No. 1-G, Medokan Ayu, Rungkut, Surabaya, Jawa Timur', -7.32057080, 112.79439410],
                ['Jalan Medokan Asri Utara XII Blok P/24, Rungkut, Surabaya, Jawa Timur', -7.32105860, 112.79408210],
                ['Jalan Medokan Asri Utara X Blok N No. 26, Medokan Ayu, Rungkut, Surabaya, Jawa Timur', -7.32213560, 112.79318970]
            ],
            5: [
                ['Jalan Medokan Asri Utara X Blok N Nomor 26, Kelurahan Medokan Ayu, Kecamatan Rungkut, Kota Surabaya, Provinsi Jawa Timur', -7.32213560, 112.79318970]
            ],
            7: [
                ['Jalan Medokan Asri Utara X Blok M Nomor 11, Kelurahan Medokan Ayu, Kecamatan Rungkut, Kota Surabaya, Provinsi Jawa Timur', -7.32246870, 112.79272920],
                ['Jalan Medokan Asri Utara V Nomor C3, Kelurahan Medokan Ayu, Kecamatan Rungkut, Kota Surabaya, Provinsi Jawa Timur', -7.32430030, 112.79367670]
            ],
            8: [
                ['Jalan Medokan Asri Utara V Nomor 26, Kelurahan Medokan Ayu, Kecamatan Rungkut, Kota Surabaya, Provinsi Jawa Timur', -7.32433620, 112.79384870],
                ['Jalan Medokan Asri Utara IV Nomor 31, Kelurahan Medokan Ayu, Kecamatan Rungkut, Kota Surabaya, Provinsi Jawa Timur, Indonesia', -7.32478810, 112.79413030]
            ]
        };

        // Warna untuk setiap kurir
        var kurirColors = {
            1: 'red',
            3: 'blue',
            4: 'green',
            5: 'orange',
            7: 'purple',
            8: 'brown'
        };

        // Tambahkan marker dan polyline untuk setiap kurir
        for (var kurirId in kurirData) {
            var locations = kurirData[kurirId];
            var latlngs = [];
            var color = kurirColors[kurirId];

            locations.forEach(function(location) {
                var marker = L.marker([location[1], location[2]], {
                    icon: L.icon({
                        iconUrl: `https://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=${kurirId}|${color}|000000`,
                        iconSize: [21, 34],
                        iconAnchor: [10, 34],
                        popupAnchor: [0, -34]
                    })
                }).addTo(map).bindPopup(location[0]);

                latlngs.push([location[1], location[2]]);
            });

            // Tambahkan polyline untuk menghubungkan semua titik
            if (latlngs.length > 1) {
                L.polyline(latlngs, {color: color}).addTo(map);
            }
        }
    </script>
</body>
</html>
