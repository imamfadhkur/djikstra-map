<?php

namespace App\Http\Controllers;

use App\Models\Edge;
use App\Models\Dataset;
use Illuminate\Http\Request;

class DijkstraV3Controller extends Controller
{
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

    public function shortestPath()
    {
        $limit = 30;
        $data = Dataset::where('latitude', '!=', 0)->where('longitude', '!=', 0)->limit($limit)->get();
        $coordinates = [];
        foreach ($data as $key => $value) {
            $coordinates[] = [
                'lat' => $value->latitude,
                'lng' => $value->longitude,
            ];
        }

        $numNodes = count($coordinates);
        $distances = array_fill(0, $numNodes, INF);
        $previous = array_fill(0, $numNodes, null);
        $visited = array_fill(0, $numNodes, false);
        $path = [];

        $distances[0] = 0;
        $totalDistance = 0;

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
            $path[] = $currentNode; // Simpan node yang dikunjungi

            foreach ($coordinates as $neighborId => $coord) {
                if (!$visited[$neighborId]) {
                    $alt = $distances[$currentNode] + $this->haversine(
                        $coordinates[$currentNode]['lat'], $coordinates[$currentNode]['lng'],
                        $coord['lat'], $coord['lng']
                    );
                    if ($alt < $distances[$neighborId]) {
                        $distances[$neighborId] = $alt;
                        $previous[$neighborId] = $currentNode;
                    }
                }
            }
            $totalDistance += $alt;
        }

        // Build output path manually
        $output = [];
        foreach ($path as $index) {
            $output[] = [
                'lat' => $coordinates[$index]['lat'],
                'lng' => $coordinates[$index]['lng'],
            ];
        }

        $totalDistanceToEndNode = $distances[$numNodes - 1];

        $hasil = [
            'coordinates' => $output,
            'totalDistance' => $totalDistanceToEndNode,
            'totalNode' => $limit,
        ];

        $diss = 0;
        $dissArr = [];
        // dd($hasil, $distances);
        for ($i=0; $i < count($output)-1; $i++) { 
            $koordinat_a = Dataset::where('latitude', $output[$i]['lat'])->where('longitude', $output[$i]['lng'])->first()->id;
            $koordinat_b = Dataset::where('latitude', $output[$i+1]['lat'])->where('longitude', $output[$i+1]['lng'])->first()->id;
            $diss += Edge::where(function ($query) use ($koordinat_a, $koordinat_b) {
                $query->where(function ($q) use ($koordinat_a, $koordinat_b) {
                    $q->where('id_node_a', $koordinat_a)->where('id_node_b', $koordinat_b);
                })->orWhere(function ($q) use ($koordinat_a, $koordinat_b) {
                    $q->where('id_node_b', $koordinat_a)->where('id_node_a', $koordinat_b);
                });
            })->first()->distance;
            $dissArr[] = $diss;
        }

        // dd($output, $diss, $dissArr);

        return view('riset.path', [
            'coordinates' => $output,
            'totalDistance' => $totalDistanceToEndNode
        ]);
    }
}
