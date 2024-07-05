<?php

namespace App\Http\Controllers;

use Location\Coordinate;
use Illuminate\Http\Request;
use Location\Distance\Haversine;

class DijkstraV5Controller extends Controller
{
    public function ShortestPath()
    {
        $coordinates = [
            ['lat' => -7.030915, 'lng' => 112.752922],
            ['lat' => -7.031105, 'lng' => 112.745559],
            ['lat' => -7.025105, 'lng' => 112.749559],
            ['lat' => -7.026203, 'lng' => 112.757749],
            ['lat' => -7.025558, 'lng' => 112.760351],
            ['lat' => -7.024485, 'lng' => 112.761464],
            ['lat' => -7.020831, 'lng' => 112.761066],
        ];

        $numNodes = count($coordinates);
        $distances = array_fill(0, $numNodes, INF);
        $previous = array_fill(0, $numNodes, null);
        $visited = array_fill(0, $numNodes, false);
        $distances[0] = 0;

        $graph = $this->buildGraph($coordinates);

        for ($i = 0; $i < $numNodes; $i++) {
            $minDistance = INF;
            $currentNode = null;

            foreach ($distances as $nodeId => $distance) {
                if (!$visited[$nodeId] && $distance < $minDistance) {
                    $minDistance = $distance;
                    $currentNode = $nodeId;
                }
            }

            if ($currentNode === null) {
                break;
            }

            $visited[$currentNode] = true;

            foreach ($graph[$currentNode] as $neighborId => $distance) {
                if (!$visited[$neighborId]) {
                    $alt = $distances[$currentNode] + $distance;
                    if ($alt < $distances[$neighborId]) {
                        $distances[$neighborId] = $alt;
                        $previous[$neighborId] = $currentNode;
                    }
                }
            }
        }

        // Build output path
        $path = $this->buildPath($previous, $numNodes - 1);

        // Generate output with distances
        $output = [];
        $totalDistance = 0;
        for ($i = 0; $i < count($path) - 1; $i++) {
            $start = $path[$i];
            $end = $path[$i + 1];
            $distance = $this->haversine(
                $coordinates[$start]['lat'], $coordinates[$start]['lng'],
                $coordinates[$end]['lat'], $coordinates[$end]['lng'],
                'meter'
            );
            $output[] = [
                'lat' => $coordinates[$end]['lat'],
                'lng' => $coordinates[$end]['lng'],
                'distance' => $distance, // Distance to next node
            ];
            $totalDistance += $distance;
        }

        // dd($output, $totalDistance);

        return view('riset.path', [
            'coordinates' => $output,
            'totalDistance' => $totalDistance
        ]);
    }

    private function buildGraph($coordinates)
    {
        $numNodes = count($coordinates);
        $graph = [];

        for ($i = 0; $i < $numNodes; $i++) {
            $graph[$i] = [];
            for ($j = 0; $j < $numNodes; $j++) {
                if ($i != $j) {
                    $graph[$i][$j] = $this->haversine(
                        $coordinates[$i]['lat'], $coordinates[$i]['lng'],
                        $coordinates[$j]['lat'], $coordinates[$j]['lng'],
                        'meter' // change to 'kilometer' if needed
                    );
                }
            }
        }

        return $graph;
    }

    private function buildPath($previous, $endNode)
    {
        $path = [];
        $current = $endNode;

        while ($current !== null) {
            array_unshift($path, $current);
            $current = $previous[$current];
        }

        return $path;
    }

    private function haversine($lat1, $lng1, $lat2, $lng2, $unit = 'meter')
    {
        $coordinate1 = new Coordinate($lat1, $lng1);
        $coordinate2 = new Coordinate($lat2, $lng2);
        $calculator = new Haversine();
        $distanceInMeters = $calculator->getDistance($coordinate1, $coordinate2);

        if ($unit === 'kilometer') {
            return $distanceInMeters / 1000;
        }

        return $distanceInMeters;
    }
}
