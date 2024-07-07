<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Dataset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SecondController extends Controller
{
    public function alamat_penerima()
    {
        $data = Dataset::where("kurir_id", "!=", 0)->paginate(25);
        // dd($data[0]->user);
        return view('dashboard.admin.alamat_penerima.index', [
            'alamat_penerimas' => $data,
            'title' => 'Alamat Penerima'
        ]);
    }

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
    
    public function rute_path()
    {
        $totalDistance = 0;
        $data = Dataset::where('kurir_id', Auth::id())->where('latitude', '!=', 0)->where('longitude', '!=', 0)->get();
        $first = Dataset::where('latitude', '!=', 0)->where('longitude', '!=', 0)->first();
        $jumlah_node = count($data);
        $coordinates = [];
        $coordinates[] = [
            'lat' => $first->latitude,
            'lng' => $first->longitude,
        ];
        foreach ($data as $key => $value) {
            $coordinates[] = [
                'lat' => $value->latitude,
                'lng' => $value->longitude,
            ];
        }
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

        // dd($hasil);
        return view('riset.path', [
            'coordinates' => $coordinates,
            'totalDistance' => $totalDistance,
        ]);
    }
    
    public function rute_dijkstra()
    {
        // code
    }

    public function kurirs()
    {
        $totalDistance = 0;
        $data = Dataset::where('kurir_id', Auth::id())->where('latitude', '!=', 0)->where('longitude', '!=', 0)->get();
        $coordinates = [];
        foreach ($data as $key => $value) {
            $coordinates[] = [
                'lat' => $value->latitude,
                'lng' => $value->longitude,
            ];
        }
        return view('dashboard.admin.kurirs', [
            'coordinates' => $coordinates,
            'totalDistance' => $totalDistance,
        ]);
    }
}
