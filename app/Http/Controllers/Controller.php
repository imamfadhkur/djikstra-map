<?php

namespace App\Http\Controllers;

use App\Models\Edge;
use App\Models\Dataset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    public function login_action(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect('/dashboard');
        }
        $request->session()->flash('loginError', 'Login gagal, username atau password salah!');
        return redirect('/login');
    }

    public function logout_action(Request $request)
    {
        Auth::logout();
        return redirect()->route('index');
    }

    public function dashboard()
    {
        $role = Auth::user()->role;
        $view = match($role) {
            'admin' => view('dashboard.admin.index', ['title' => 'admin dashboard']),
            'kurir' => view('dashboard.kurir.index', ['title' => 'kurir dashboard']),
            default => redirect()->route('not-found'),
        };
        return $view;
    }

    public function lat_n_long(Request $request)
    {
        return view('try.lat_n_long', [
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ]);
    }

    public function one_address(Request $request)
    {
        $address = $request->nama_jalan.", ".$request->kelurahan.", ".$request->kecamatan.", ".$request->kab_kota.", ".$request->negara;
        // dd($address);
        return view('try.one_address', [
            'address' => $address
        ]);
    }

    public function two_address(Request $request)
    {
        $address1 = $request->nama_jalan_1.", ".$request->kelurahan_1.", ".$request->kecamatan_1.", ".$request->kab_kota_1.", ".$request->negara_1;
        $address2 = $request->nama_jalan_2.", ".$request->kelurahan_2.", ".$request->kecamatan_2.", ".$request->kab_kota_2.", ".$request->negara_2;
        return view('try.two_address', [
            'address1' => $address1,
            'address2' => $address2,
        ]);
    }

    public function multi_address(Request $request)
    {
        $addresses = [];

        // Mengambil data alamat dari request
        $count = count($request->nama_jalan);
        for ($i = 0; $i < $count; $i++) {
            $address = [
                'address' => $request->nama_jalan[$i].", ".$request->kelurahan[$i].", ".$request->kecamatan[$i].", ".$request->kab_kota[$i].", ".$request->negara[$i],
            ];
            array_push($addresses, $address);
        }
        return view('try.multi_address', [
            'addresses' => $addresses
        ]);
    }

    public function get_lat_long_view()
    {
        return view('try.get_lat_long');
    }
        
    public function get_test(Request $request)
    {
        $address = "Griya Pesona Asri, Jalan Medayu Pesona Iii Blok C-10 Rumah, Rungkut, Surabaya, Jawa Timur";
        // $address = "Jalan Medokan Asri Utara XIV Blok Q No. 29, Rungkut, Surabaya, Jawa Timur";
        $address = str_replace(" ", "+", $address);
        $url_input = "https://www.google.com/maps/place/".$address;
        // $url_input = "https://www.google.com/maps/place/Griya+Pesona+Asri,+Jalan+Medayu+Pesona+Iii+Blok+C-10+Rumah,+Rungkut,+Surabaya,+Jawa+Timur";
        $example_url_output = "https://www.google.com/maps/place/Jl.+Medokan+Asri+Utara+XIII,+Medokan+Ayu,+Kec.+Rungkut,+Surabaya,+Jawa+Timur/@-7.3205708,112.7943941,17z/data=!3m1!4b1!4m6!3m5!1s0x2dd7fa942323dea5:0xfed4a91a84f158c2!8m2!3d-7.3205708!4d112.7943941!16s%2Fg%2F11b6b4r3t7?entry=ttu";

        ini_set('max_execution_time', 3600);

        // Jika tidak ada atau belum memiliki latitude dan longitude, ambil dari Google Maps
        $latitude = 0;
        $longitude = 0;

        // Ambil halaman HTML dari URL
        $response = Http::get($url_input);
        $html = $response->body();

        // Cari koordinat di dalam HTML
        preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $html, $matches);

        if (count($matches) >= 3) {
            $latitude = $matches[1];
            $longitude = $matches[2];
        }
        // dd($latitude, $longitude, $html);
        dd($latitude, $longitude);
    }
    
    public function get_lat_long_data(Request $request)
    {
        $address = "Jalan Medokan Asri Utara XIV Blok Q 10, Rungkut, Surabaya, Jawa Timur";
        $address = str_replace(" ", "+", $address);
        $url_input = "https://www.google.com/maps/place/".$address;
        $example_url_output = "https://www.google.com/maps/place/Jl.+Medokan+Asri+Utara+XIII,+Medokan+Ayu,+Kec.+Rungkut,+Surabaya,+Jawa+Timur/@-7.3205708,112.7943941,17z/data=!3m1!4b1!4m6!3m5!1s0x2dd7fa942323dea5:0xfed4a91a84f158c2!8m2!3d-7.3205708!4d112.7943941!16s%2Fg%2F11b6b4r3t7?entry=ttu";

        ini_set('max_execution_time', 7200);

        // Baca file CSV dari request
        $file = $request->file('address');
        $filePath = $file->getRealPath();

        // Membaca isi file CSV dengan delimiter titik koma
        $csvData = array_map('str_getcsv', file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES), array_fill(0, count(file($filePath)), ';'));

        // Proses setiap baris data, mulai dari baris kedua
        for ($i = 1; $i < count($csvData); $i++) {
            $delivery = $csvData[$i][0];
            $alamatPenerima = $csvData[$i][1];

            // Cari data delivery dan alamatPenerima apakah sudah ada di database
            $existingData = Dataset::where('delivery', $delivery)->where('alamat_penerima', $alamatPenerima)->first();
            
            // Jika tidak ada atau belum memiliki latitude dan longitude, ambil dari Google Maps
            $latitude = 0;
            $longitude = 0;

            $address = urlencode($alamatPenerima);
            $url = "https://www.google.com/maps/place/" . $address;

            // Ambil halaman HTML dari URL
            $response = Http::get($url);
            $html = $response->body();

            // Cari koordinat di dalam HTML
            preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $html, $matches);

            if (count($matches) >= 3) {
                $latitude = $matches[1];
                $longitude = $matches[2];
            }

            if ((isset($existingData->latitude) && $existingData->latitude == 0.00000000) && $latitude !== 0) {
                $existingData->update([
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                ]);
            } else {
                Dataset::create([
                    'delivery' => $delivery,
                    'alamat_penerima' => $alamatPenerima,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                ]);
            }
        }

        // Redirect atau tampilkan pesan sukses jika perlu
        return redirect('/get-lat-long')->with('success', 'Data berhasil diimpor dan diperbarui.');
    }

    function normalizeAddress($address) {
        // Convert to lowercase
        $address = strtolower($address);
        
        // Replace common abbreviations and fix "jalan" keyword
        $address = preg_replace('/\b(jl|jln|jalan)\b/', 'jalan', $address);
        
        // Standardize keywords
        $address = str_replace(['no', 'blok', 'rt', 'rw', 'kel', 'kec', 'sby'], 
                               ['nomor', 'blok', 'rt', 'rw', 'kelurahan', 'kecamatan', 'surabaya'], $address);
        
        // Add missing parts if not present
        if (!preg_match('/rungkut/i', $address)) {
            $address .= ', rungkut';
        }
        if (!preg_match('/surabaya/i', $address)) {
            $address .= ', surabaya';
        }
        if (!preg_match('/jawa timur/i', $address)) {
            $address .= ', jawa timur';
        }
    
        // Standardize format for various cases
        $patterns = [
            '/medokan\s+asri\s+(timur|barat)\s+(\w+)\s+no\s+(\d+)/',
            '/medokan\s+asri\s+(timur|barat)\s+(\w+)\s+(\w+)\s+no\s+(\d+)/',
            '/ma\s+(\d+)[^\d\w]*(\w*)/'
        ];
    
        $replacements = [
            'medokan asri $1 $2 nomor $3',
            'medokan asri $1 $2 $3 nomor $4',
            'medokan asri barat ma $1 $2'
        ];
    
        $address = preg_replace($patterns, $replacements, $address);
        
        // Capitalize the first letter of each word
        $address = ucwords($address);
        
        return $address;
    }
    
    public function normalize()
    {
        $addresses = [
            "Rungkut Asri II RL 1-A no 7",
            "Blok AD, Jl Rungkut Mejoyo Utara VI No 30, Kali Rungkut, Kec Rungkut, Surabaya, Jawa Timur 60293 (Dwin kost)",
            "Jl. Rungkut Mejoyo Utara VI No.22  Blok AD 22",
            "Rungkut Mejoyo Utara V No.15  AF 15-16",
            "...",
            "jl. Pandugo Timur III/3. Blok A/54",
            "YKP Pandugo 2 Blok T no 5, Jalan Pandugo Timur XII, Penjaringan Sari ",
        ];
        
        foreach ($addresses as $address) {
            echo $this->normalizeAddress($address) . "dxdx";
        }
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

    // antar data distance masing-masing koordinat dari table edges
    public function uji_coba()
    {
        // Retrieve coordinates from the Dataset table where latitude and longitude are not equal to 0, and limit the result to 30 records
        $coordinates = Dataset::where('latitude', '!=', 0)->where('longitude', '!=', 0)->limit(30)->get();
        
        // Initialize an empty array to store the non-Dijkstra data
        $dataNonDijkstra = [];
        
        // Iterate through each coordinate and extract the latitude and longitude values
        foreach ($coordinates as $key => $value) {
            $dataNonDijkstra[] = [
            'lat' => $value->latitude,
            'lng' => $value->longitude,
            ];
        }
        $dataDijkstra10 = 
        [
            [
                "lat" => "-7.31211370",
                "lng" => "112.70387780"
            ],
                [
                "lat" => "-7.32076430",
                "lng" => "112.79239950"
            ],
                [
                "lat" => "-7.32076430",
                "lng" => "112.79239950"
            ],
                [
                "lat" => "-7.32246870",
                "lng" => "112.79272920"
            ],
                [
                "lat" => "-7.32213560",
                "lng" => "112.79318970"
            ],
                [
                "lat" => "-7.32213560",
                "lng" => "112.79318970"
            ],
                [
                "lat" => "-7.32430030",
                "lng" => "112.79367670"
            ],
                [
                "lat" => "-7.32105860",
                "lng" => "112.79408210"
            ],
                [
                "lat" => "-7.32057080",
                "lng" => "112.79439410"
            ],
                [
                "lat" => "-7.32447190",
                "lng" => "112.79396570"
            ]
        ];
        $dataDijkstra20 = 
        [
            [
                "lat" => "-7.31211370",
                "lng" => "112.70387780"
            ],
            [
                "lat" => "-7.32973610",
                "lng" => "112.79088490"
            ],
            [
                "lat" => "-7.32973610",
                "lng" => "112.79088490"
            ],
            [
                "lat" => "-7.32076430",
                "lng" => "112.79239950"
            ],
            [
                "lat" => "-7.32076430",
                "lng" => "112.79239950"
            ],
            [
                "lat" => "-7.32366720",
                "lng" => "112.79250350"
            ],
            [
                "lat" => "-7.32246870",
                "lng" => "112.79272920"
            ],
            [
                "lat" => "-7.32213560",
                "lng" => "112.79318970"
            ],
            [
                "lat" => "-7.32213560",
                "lng" => "112.79318970"
            ],
            [
                "lat" => "-7.32400420",
                "lng" => "112.79351570"
            ],
            [
                "lat" => "-7.32430030",
                "lng" => "112.79367670"
            ],
            [
                "lat" => "-7.32105860",
                "lng" => "112.79408210"
            ],
            [
                "lat" => "-7.32433620",
                "lng" => "112.79384870"
            ],
            [
                "lat" => "-7.32057080",
                "lng" => "112.79439410"
            ],
            [
                "lat" => "-7.32447190",
                "lng" => "112.79396570"
            ],
            [
                "lat" => "-7.32478810",
                "lng" => "112.79413030"
            ],
            [
                "lat" => "-7.32323980",
                "lng" => "112.79438840"
            ],
            [
                "lat" => "-7.32341700",
                "lng" => "112.79444370"
            ],
            [
                "lat" => "-7.32341700",
                "lng" => "112.79444370"
            ],
            [
                "lat" => "-7.32491670",
                "lng" => "112.79483400"
            ]
        ];
        $dataDijkstra30 = 
        [
            [
                "lat" => "-7.31211370",
                "lng" => "112.70387780"
            ],
            [
                "lat" => "-7.33050460",
                "lng" => "112.75174540"
            ],
            [
                "lat" => "-7.33053130",
                "lng" => "112.75713720"
            ],
            [
                "lat" => "-7.33048470",
                "lng" => "112.75828710"
            ],
            [
                "lat" => "-7.33501770",
                "lng" => "112.75721040"
            ],
            [
                "lat" => "-7.32712690",
                "lng" => "112.76416320"
            ],
            [
                "lat" => "-7.33596750",
                "lng" => "112.76159340"
            ],
            [
                "lat" => "-7.32969400",
                "lng" => "112.76866500"
            ],
            [
                "lat" => "-7.33106480",
                "lng" => "112.78915540"
            ],
            [
                "lat" => "-7.33034580",
                "lng" => "112.78981160"
            ],
            [
                "lat" => "-7.33019660",
                "lng" => "112.79003020"
            ],
            [
                "lat" => "-7.32973610",
                "lng" => "112.79088490"
            ],
            [
                "lat" => "-7.32973610",
                "lng" => "112.79088490"
            ],
            [
                "lat" => "-7.32076430",
                "lng" => "112.79239950"
            ],
            [
                "lat" => "-7.32076430",
                "lng" => "112.79239950"
            ],
            [
                "lat" => "-7.32366720",
                "lng" => "112.79250350"
            ],
            [
                "lat" => "-7.32246870",
                "lng" => "112.79272920"
            ],
            [
                "lat" => "-7.32213560",
                "lng" => "112.79318970"
            ],
            [
                "lat" => "-7.32213560",
                "lng" => "112.79318970"
            ],
            [
                "lat" => "-7.32400420",
                "lng" => "112.79351570"
            ],
            [
                "lat" => "-7.32430030",
                "lng" => "112.79367670"
            ],
            [
                "lat" => "-7.32105860",
                "lng" => "112.79408210"
            ],
            [
                "lat" => "-7.32433620",
                "lng" => "112.79384870"
            ],
            [
                "lat" => "-7.32057080",
                "lng" => "112.79439410"
            ],
            [
                "lat" => "-7.32447190",
                "lng" => "112.79396570"
            ],
            [
                "lat" => "-7.32478810",
                "lng" => "112.79413030"
            ],
            [
                "lat" => "-7.32323980",
                "lng" => "112.79438840"
            ],
            [
                "lat" => "-7.32341700",
                "lng" => "112.79444370"
            ],
            [
                "lat" => "-7.32341700",
                "lng" => "112.79444370"
            ],
            [
                "lat" => "-7.32491670",
                "lng" => "112.79483400"
            ]
        ];

        $data = $dataNonDijkstra;
        $alamats = $dataNonDijkstra;
        $data_uji_coba = [];

        // Menginisialisasi matriks jarak antar koordinat
        for ($i=0; $i < count($data); $i++) { 
            $temp = [];
            $koordinat_a = Dataset::where('latitude', $data[$i]['lat'])->where('longitude', $data[$i]['lng'])->first()->id;
            for ($j=0; $j < count($data); $j++) { 
                $koordinat_b = Dataset::where('latitude', $data[$j]['lat'])->where('longitude', $data[$j]['lng'])->first()->id;
                if ($koordinat_a == $koordinat_b) {
                    $temp[$koordinat_b] = 0;
                }
                else {
                    // Mengambil jarak antara dua koordinat menggunakan model Edge
                    $temp[$koordinat_b] = number_format(Edge::where(function ($query) use ($koordinat_a, $koordinat_b) {
                        $query->where(function ($q) use ($koordinat_a, $koordinat_b) {
                            $q->where('id_node_a', $koordinat_a)->where('id_node_b', $koordinat_b);
                        })->orWhere(function ($q) use ($koordinat_a, $koordinat_b) {
                            $q->where('id_node_b', $koordinat_a)->where('id_node_a', $koordinat_b);
                        });
                    })->first()->distance, 2, '.', '');
                }
            }
            $data_uji_coba[$koordinat_a] = $temp;
        }

        // Mengirim data ke view 'riset.uji_coba'
        return view('riset.uji_coba', [
            'data' => $data_uji_coba,
            'alamats' => $alamats,
        ]);
    }
}
