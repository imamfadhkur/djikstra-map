<?php

namespace App\Http\Controllers;

use App\Models\Dataset;
use Fhaculty\Graph\Graph;
use Illuminate\Http\Request;
use Graphp\Algorithms\ShortestPath\Dijkstra;

class DijkstraController extends Controller
{
    public function ShortestPath(Request $request)
    {
        // Meningkatkan batas memori PHP
        ini_set('memory_limit', '4048M'); // Sesuaikan dengan kebutuhan Anda
        ini_set('max_execution_time', 480);

        // Koordinat input dari request atau contoh data koordinat
        $coordinates = [
            ['lat' => -7.32076430, 'lng' => 112.79239950],
            ['lat' => -7.32076430, 'lng' => 112.79239950],
            ['lat' => -7.32447190, 'lng' => 112.79396570],
            ['lat' => -7.32057080, 'lng' => 112.79439410],
            ['lat' => -7.32105860, 'lng' => 112.79408210],
            ['lat' => -7.32213560, 'lng' => 112.79318970],
            ['lat' => -7.32213560, 'lng' => 112.79318970],
        ];

        $data = Dataset::where('latitude', '!=', 0)->where('longitude', '!=', 0)->limit(11)->get();
            $coordinates = [];
            foreach ($data as $key => $value) {
                $coordinates[] = [
                    'lat' => $value->latitude,
                    'lng' => $value->longitude,
                ];
            }

        // Membuat graph dan node
        $graph = new Graph();
        $nodes = [];
        foreach ($coordinates as $index => $coordinate) {
            $nodes[$index] = $graph->createVertex($index);
        }

        // Menghitung jarak antar node dan menambah edge ke graph
        for ($i = 0; $i < count($coordinates); $i++) {
            for ($j = $i + 1; $j < count($coordinates); $j++) {
                $distance = $this->calculateDistance($coordinates[$i], $coordinates[$j]);
                $nodes[$i]->createEdgeTo($nodes[$j])->setWeight($distance);
                $nodes[$j]->createEdgeTo($nodes[$i])->setWeight($distance);
            }
        }

        // Menyelesaikan TSP untuk menemukan rute terpendek
        $shortestRoute = $this->solveTSP($nodes);

        // Mengonversi rute ke koordinat
        $routeCoordinates = array_map(function($node) use ($coordinates) {
            return $coordinates[$node->getId()];
        }, $shortestRoute);

        // Kirim hasil ke view
        return view('riset.dijkstra', ['coordinates' => $routeCoordinates]);
    }

    private function calculateDistance($coord1, $coord2)
    {
        $lat1 = $coord1['lat'];
        $lng1 = $coord1['lng'];
        $lat2 = $coord2['lat'];
        $lng2 = $coord2['lng'];

        $earthRadius = 6371000; // Radius bumi dalam meter
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return $distance;
    }

    private function solveTSP($nodes)
    {
        $permutations = $this->permute(array_slice($nodes, 1)); // Get permutations of all nodes except the first one
        $shortestDistance = PHP_INT_MAX;
        $shortestPath = [];

        foreach ($permutations as $permutation) {
            array_unshift($permutation, $nodes[0]); // Add the start node at the beginning
            $permutation[] = $nodes[0]; // Add the start node at the end to return to the start

            $totalDistance = 0;
            for ($i = 0; $i < count($permutation) - 1; $i++) {
                $algorithm = new Dijkstra($permutation[$i]);
                $totalDistance += $algorithm->getDistance($permutation[$i + 1]);
            }

            if ($totalDistance < $shortestDistance) {
                $shortestDistance = $totalDistance;
                $shortestPath = $permutation;
            }
        }

        return $shortestPath;
    }

    private function permute($items, $perms = [], &$collect = [])
    {
        if (empty($items)) {
            $collect[] = $perms;
        } else {
            for ($i = count($items) - 1; $i >= 0; --$i) {
                $newitems = $items;
                $newperms = $perms;
                list($foo) = array_splice($newitems, $i, 1);
                array_unshift($newperms, $foo);
                $this->permute($newitems, $newperms, $collect);
            }
        }
        return $collect;
    }
}
