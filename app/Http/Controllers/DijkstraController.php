<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Fhaculty\Graph\Graph;
use Graphp\Algorithms\ShortestPath\Dijkstra;

class DijkstraController extends Controller
{
    public function findShortestPath(Request $request)
    {
        // Validasi request untuk memastikan format data koordinat yang benar
        // $request->validate([
        //     'coordinates' => 'required|array',
        //     'coordinates.*.lat' => 'required|numeric',
        //     'coordinates.*.lng' => 'required|numeric',
        // ]);

        // Koordinat input dari request
        // $coordinates = $request->input('coordinates');

        ini_set('max_execution_time', 360);

        $coordinates = [
            ['lat' => -7.32076430, 'lng' => 112.79239950],
            ['lat' => -7.32076430, 'lng' => 112.79239950],
            ['lat' => -7.32447190, 'lng' => 112.79396570],
            ['lat' => -7.32057080, 'lng' => 112.79439410],
            ['lat' => -7.32105860, 'lng' => 112.79408210],
            ['lat' => -7.32213560, 'lng' => 112.79318970],
            ['lat' => -7.32213560, 'lng' => 112.79318970],
            // ['lat' => -7.32246870, 'lng' => 112.79272920],
            // ['lat' => -7.32430030, 'lng' => 112.79367670],
            // ['lat' => -7.32433620, 'lng' => 112.79384870],
        ];

        $graph = new Graph();
        $nodes = [];
        foreach ($coordinates as $index => $coordinate) {
            $nodes[$index] = $graph->createVertex($index);
        }

        for ($i = 0; $i < count($coordinates); $i++) {
            for ($j = $i + 1; $j < count($coordinates); $j++) {
                $distance = $this->calculateDistance($coordinates[$i], $coordinates[$j]);
                $nodes[$i]->createEdgeTo($nodes[$j])->setWeight($distance);
                $nodes[$j]->createEdgeTo($nodes[$i])->setWeight($distance);
            }
        }

        // Solve TSP
        $shortestRoute = $this->solveTSP($nodes);

        // Convert route to coordinates
        $routeCoordinates = array_map(function($node) use ($coordinates) {
            return $coordinates[$node->getId()];
        }, $shortestRoute);

        // dd($routeCoordinates);

        // Kirim hasil ke view
        return view('riset.path', ['coordinates' => $routeCoordinates]);
    }

    private function calculateDistance($coord1, $coord2)
    {
        $lat1 = $coord1['lat'];
        $lng1 = $coord1['lng'];
        $lat2 = $coord2['lat'];
        $lng2 = $coord2['lng'];

        $earthRadius = 6371000; // Meter
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
