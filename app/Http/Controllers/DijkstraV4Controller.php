<?php

namespace App\Http\Controllers;

use App\Models\Edge;
use App\Models\Dataset;
use Location\Coordinate;
use Illuminate\Http\Request;
use Location\Distance\Haversine;

class DijkstraV4Controller extends Controller
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

        $path = $this->buildPath($previous, $numNodes - 1);

        $output = [];
        foreach ($path as $index) {
            $output[] = [
                'lat' => $coordinates[$index]['lat'],
                'lng' => $coordinates[$index]['lng'],
                'distance' => $distances[$index], // in meters
            ];
        }

        $totalDistanceToEndNode = $distances[$numNodes - 1];

        dd($distance, $previous, $visited, $output);

        return view('riset.path', [
            'coordinates' => $output,
            'totalDistance' => $totalDistanceToEndNode
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
                        'kilometer' // change to 'kilometer' if needed
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

    private function getDistanceFromDB($idNodeA, $idNodeB)
    {
        $distance = Edge::where(function ($query) use ($idNodeA, $idNodeB) {
            $query->where(function ($q) use ($idNodeA, $idNodeB) {
                $q->where('id_node_a', $idNodeA)->where('id_node_b', $idNodeB);
            })->orWhere(function ($q) use ($idNodeA, $idNodeB) {
                $q->where('id_node_b', $idNodeA)->where('id_node_a', $idNodeB);
            });
        })->first()->distance ?? INF; // Jika tidak ditemukan, kembalikan INF

        return $distance;
    }
}
