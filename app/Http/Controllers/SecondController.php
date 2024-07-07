<?php

namespace App\Http\Controllers;

use App\Models\Dataset;
use Illuminate\Support\Facades\Auth;

class SecondController extends Controller
{
    public function alamat_penerima()
    {
        $data = Dataset::where("kurir_id", "!=", 0)->paginate(25);
        return view('dashboard.admin.alamat_penerima.index', [
            'alamat_penerimas' => $data,
            'title' => 'Alamat Penerima'
        ]);
    }

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
    
    public function rute_path()
    {
        $data = Dataset::where('kurir_id', Auth::id())->where('latitude', '!=', 0)->where('longitude', '!=', 0)->get();
        $first = Dataset::where('latitude', '!=', 0)->where('longitude', '!=', 0)->first();
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

        return view('riset.path', [
            'coordinates' => $coordinates,
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
