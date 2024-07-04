<?php

namespace App\Http\Controllers;

use App\Models\Dataset;
use Illuminate\Http\Request;

class NonDijkstraController extends Controller
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
    
    public function nonDijkstra(Request $request)
    {
        // $coordinates = $request->input('coordinates');
        // $coordinates = [
        //     ['lat' => -7.32076430, 'lng' => 112.79239950],
        //     ['lat' => -7.32076430, 'lng' => 112.79239950],
        //     ['lat' => -7.32447190, 'lng' => 112.79396570],
        //     ['lat' => -7.32057080, 'lng' => 112.79439410],
        //     ['lat' => -7.32105860, 'lng' => 112.79408210],
        //     ['lat' => -7.32213560, 'lng' => 112.79318970],
        //     ['lat' => -7.32213560, 'lng' => 112.79318970],
        //     ['lat' => -7.32246870, 'lng' => 112.79272920],
        //     ['lat' => -7.32430030, 'lng' => 112.79367670],
        //     ['lat' => -7.32433620, 'lng' => 112.79384870],
        //     ['lat' => -7.32478810, 'lng' => 112.79413030],
        //     ['lat' => -7.32400420, 'lng' => 112.79351570],
        //     ['lat' => -7.32366720, 'lng' => 112.79250350],
        //     ['lat' => -7.32341700, 'lng' => 112.79444370],
        //     ['lat' => -7.32341700, 'lng' => 112.79444370],
        //     ['lat' => -7.32323980, 'lng' => 112.79438840],
        //     ['lat' => -7.32491670, 'lng' => 112.79483400],
        //     ['lat' => -7.32973610, 'lng' => 112.79088490],
        //     ['lat' => -7.32973610, 'lng' => 112.79088490],
        //     ['lat' => -7.33034580, 'lng' => 112.78981160],
        //     ['lat' => -7.33019660, 'lng' => 112.79003020],
        //     ['lat' => -7.33106480, 'lng' => 112.78915540],
        //     ['lat' => -7.32712690, 'lng' => 112.76416320],
        //     ['lat' => -7.33050460, 'lng' => 112.75174540],
        // ];

        // buatkan array koordinat dari tabel datasets
        $hasil = [];
        $limit = 101;
        $totalDistance = 0;
        $data = Dataset::where('latitude', '!=', 0)->where('longitude', '!=', 0)->limit($limit)->get();
        $jumlah_node = count($data);
        $coordinates = [];
        foreach ($data as $key => $value) {
            $coordinates[] = [
                'lat' => $value->latitude,
                'lng' => $value->longitude,
            ];
        }
        // dd($coordinates);
        // $coordinates[1] = [
        //     'lat' => "-7.33050460",
        //     'lng' => "112.75174540",
        // ];
        
        $numNodes = count($coordinates);

        // Hitung jarak total dengan mengunjungi setiap titik dalam urutan yang diberikan
        for ($i = 0; $i < $numNodes - 1; $i++) {
            $distance = $this->haversine(
                $coordinates[$i]['lat'], $coordinates[$i]['lng'],
                $coordinates[$i + 1]['lat'], $coordinates[$i + 1]['lng']
            );
            $totalDistance += $distance;
            // dd($coordinates[0],$coordinates[1], $totalDistance);
            // Tambahkan totalDistance ke array saat ini sebagai 'totalDistance'
            $coordinates[$i]['totalDistance'] = $totalDistance;
        }

        // Tambahkan jarak dari titik terakhir ke titik pertama untuk membentuk siklus
        $distance = $this->haversine(
            $coordinates[$numNodes - 1]['lat'], $coordinates[$numNodes - 1]['lng'],
            $coordinates[0]['lat'], $coordinates[0]['lng']
        );
        $totalDistance += $distance;
        // Pastikan untuk menambahkan totalDistance ke elemen terakhir juga
        $coordinates[$numNodes - 1]['totalDistance'] = $totalDistance;

        $output = [
            'coordinates' => $coordinates,
            'totalDistance' => $totalDistance,
            'totalNode' => $jumlah_node,
        ];
        $hasil[] = $output;

        dd($hasil, $totalDistance);
        return view('riset.path', [
            'coordinates' => $coordinates,
            'totalDistance' => $totalDistance,
        ]);
    }
}
