<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DijkstraV2Controller extends Controller
{
    // Fungsi untuk menghitung jarak antara dua koordinat menggunakan rumus Haversine
    private function haversine($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // Radius bumi dalam kilometer

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    // dijkstra algorithm
    public function shortestPath(Request $request)
    {
        $coordinates = $request->input('coordinates');
        // Pastikan koordinat yang sama digunakan di kedua metode
        $coordinates = [
            ['lat' => -7.32076430, 'lng' => 112.79239950],
            ['lat' => -7.32076430, 'lng' => 112.79239950],
            ['lat' => -7.32447190, 'lng' => 112.79396570],
            ['lat' => -7.32057080, 'lng' => 112.79439410],
            ['lat' => -7.32105860, 'lng' => 112.79408210],
            ['lat' => -7.32213560, 'lng' => 112.79318970],
            ['lat' => -7.32213560, 'lng' => 112.79318970],
            ['lat' => -7.32246870, 'lng' => 112.79272920],
            ['lat' => -7.32430030, 'lng' => 112.79367670],
            ['lat' => -7.32433620, 'lng' => 112.79384870],
            ['lat' => -7.32478810, 'lng' => 112.79413030],
            ['lat' => -7.32400420, 'lng' => 112.79351570],
            ['lat' => -7.32366720, 'lng' => 112.79250350],
            ['lat' => -7.32341700, 'lng' => 112.79444370],
            ['lat' => -7.32341700, 'lng' => 112.79444370],
            ['lat' => -7.32323980, 'lng' => 112.79438840],
            ['lat' => -7.32491670, 'lng' => 112.79483400],
            ['lat' => -7.32973610, 'lng' => 112.79088490],
            ['lat' => -7.32973610, 'lng' => 112.79088490],
            ['lat' => -7.33034580, 'lng' => 112.78981160],
            ['lat' => -7.33019660, 'lng' => 112.79003020],
            ['lat' => -7.33106480, 'lng' => 112.78915540],
            ['lat' => -7.32712690, 'lng' => 112.76416320],
            ['lat' => -7.33050460, 'lng' => 112.75174540],
        ];

        $distances = [];
        $previous = [];
        $unvisited = [];
        $numNodes = count($coordinates);

        for ($i = 0; $i < $numNodes; $i++) {
            $distances[$i] = INF; // Set initial distance to infinity
            $previous[$i] = null;
            $unvisited[$i] = true;
        }

        $distances[0] = 0; // Distance from start to start is 0

        while (!empty($unvisited)) {
            // Ambil node dengan jarak terdekat
            $currentNode = null;
            foreach ($unvisited as $nodeId => $value) {
                if ($currentNode === null || $distances[$nodeId] < $distances[$currentNode]) {
                    $currentNode = $nodeId;
                }
            }

            if ($distances[$currentNode] === INF) {
                break;
            }

            // Analisis tetangga
            for ($i = 0; $i < $numNodes; $i++) {
                if (isset($unvisited[$i])) {
                    $alt = $distances[$currentNode] + $this->haversine(
                        $coordinates[$currentNode]['lat'], $coordinates[$currentNode]['lng'],
                        $coordinates[$i]['lat'], $coordinates[$i]['lng']
                    );
                    if ($alt < $distances[$i]) {
                        $distances[$i] = $alt;
                        $previous[$i] = $currentNode;
                    }
                }
            }

            unset($unvisited[$currentNode]);
        }

        // Menentukan total jarak rute terpendek
        $totalDistance = 0;
        $currentNode = $numNodes - 1; // node akhir
        while ($previous[$currentNode] !== null) {
            $totalDistance += $this->haversine(
                $coordinates[$currentNode]['lat'], $coordinates[$currentNode]['lng'],
                $coordinates[$previous[$currentNode]]['lat'], $coordinates[$previous[$currentNode]]['lng']
            );
            $currentNode = $previous[$currentNode];
        }

        $output = [];
        foreach ($coordinates as $index => $coordinate) {
            $output[] = [
                'lat' => $coordinate['lat'],
                'lng' => $coordinate['lng'],
                'distance' => $distances[$index]
            ];
        }
        dd($output, $totalDistance);
        return view('riset.path', ['coordinates' => $output, 'totalDistance' => $totalDistance]);
    }
}
