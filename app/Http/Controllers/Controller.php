<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
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
        // dd($address);
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

        // dd($addresses);
        
        return view('try.multi_address', [
            'addresses' => $addresses
        ]);
    }
}
