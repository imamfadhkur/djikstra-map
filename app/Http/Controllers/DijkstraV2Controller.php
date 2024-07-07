<?php

namespace App\Http\Controllers;

use App\Models\Dataset;

class DijkstraV2Controller extends Controller
{
    // Fungsi untuk menghitung jarak antara dua koordinat menggunakan rumus Haversine
    private function haversine($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // Radius bumi dalam kilometer

        // Menghitung perbedaan lintang (latitude) antara dua titik dalam radian
        $dLat = deg2rad($lat2 - $lat1);

        // Menghitung perbedaan bujur (longitude) antara dua titik dalam radian
        $dLng = deg2rad($lng2 - $lng1);

        // Menghitung kuadrat dari setengah chord panjang lintang
        $a = sin($dLat / 2) * sin($dLat / 2) +
            // Mengalikan cosinus dari lintang titik pertama dan kedua, dan menghitung kuadrat dari setengah chord panjang bujur
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng / 2) * sin($dLng / 2);

        // Menghitung jarak angular dalam radian menggunakan dua kali arctangent dari akar a dan akar dari (1 - a)
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    // Algoritma Dijkstra untuk menemukan rute terpendek
    public function shortestPath()
    {
        $limit = 1;
        $hasil = [];
        while ($limit <= 201) {
            $limit += 10;
            $data = Dataset::where('latitude', '!=', 0)->where('longitude', '!=', 0)->limit($limit)->get();
            $coordinates = [];
            foreach ($data as $key => $value) {
                $coordinates[] = [
                    'lat' => $value->latitude,
                    'lng' => $value->longitude,
                ];
            }

            $numNodes = count($coordinates);
            $distances = [];
            $previous = [];
            $visited = [];

            for ($i = 0; $i < $numNodes; $i++) {
                $distances[$i] = INF;
                $previous[$i] = null;
            }

            $distances[0] = 0;

            while (count($visited) < $numNodes) {
                $currentNode = null;
                foreach ($distances as $nodeId => $distance) {
                    if (!isset($visited[$nodeId]) && ($currentNode === null || $distance < $distances[$currentNode])) {
                        $currentNode = $nodeId;
                    }
                }

                if ($distances[$currentNode] === INF) {
                    break;
                }

                foreach ($coordinates as $i => $coord) {
                    if (!isset($visited[$i])) {
                        $alt = $distances[$currentNode] + $this->haversine(
                            $coordinates[$currentNode]['lat'], $coordinates[$currentNode]['lng'],
                            $coord['lat'], $coord['lng']
                        );
                        if ($alt < $distances[$i]) {
                            $distances[$i] = $alt;
                            $previous[$i] = $currentNode;
                        }
                    }
                }

                $visited[$currentNode] = true;
            }

            $totalDistance = 0;
            $path = [];
            $currentNode = $numNodes - 1;
            while ($previous[$currentNode] !== null) {
                $totalDistance += $this->haversine(
                    $coordinates[$currentNode]['lat'], $coordinates[$currentNode]['lng'],
                    $coordinates[$previous[$currentNode]]['lat'], $coordinates[$previous[$currentNode]]['lng']
                );
                array_unshift($path, $currentNode);
                $currentNode = $previous[$currentNode];
            }
            array_unshift($path, $currentNode);

            $totalDistanceToEndNode = $distances[$numNodes - 1];

            $output = [];
            foreach ($path as $index) {
                $output[] = [
                    'lat' => $coordinates[$index]['lat'],
                    'lng' => $coordinates[$index]['lng'],
                ];
            }
            $hasil[] = [
                'coordinates' => $output,
                'totalDistance' => $totalDistanceToEndNode,
                'totalNode' => $limit,
            ];
        }

        // dd($hasil);

        return view('riset.path', [
            'coordinates' => $output,
            'totalDistance' => $totalDistanceToEndNode
        ]);
    }
}
