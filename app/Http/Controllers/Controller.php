<?php

namespace App\Http\Controllers;

use App\Models\Dataset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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

            if ($existingData->latitude == 0.00000000 && $latitude !== 0.00000000) {
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
            "Rungkut asri  RL 1 b no. 3, Rungkut l ",
            "Rungkut madya no 119 toko roti cake boss  toko roti Cakeboss no 119 Rungkut madya",
            "JL RUNGKUT ASRI BLOK RL 1A/24 NO 17 SURABAYA",
            "Jl. Rungkut Asri No.4  (bengkel mobil rungkut asri motor)",
            "Kost Putri, Jalan Rungkut Mejoyo Utara V Blok Af No 16, Kali Rungkut, Rungkut (Kamar F)",
            "Cake Boss, Jalan Rungkut Madya No.119, Rungkut Kidul, Surabaya, Jawa Timur, Indonesia ",
            "APOTIK K 24 RUNGKUT MADYA JL RUNGKUT MADYA NO 85 SURABAYA ",
            "Rumah asri 18 kos eksekutif Surabaya Jalan Rungkut Asri nomor 18 Surabaya",
            "PT. Mandala Finance, Tbk, Jalan Rungkut Madya No.81d, Rungkut Kidul  PT Mandala Multi finance, Jalan Rungkut madya no. 81d, Rungkut Kidul l, Rungkut",
            "jalan rungkut madya no 79 (pentol gila) deretan bebek palupi  pentol gila",
            "Rungkut Mejoyo Utara V blok AF No.16 (kost putri)  Rungkut Mejoyo Utara V blok AF No.16 (kost putri)",
            "jalan rungkut madya no 79 (pentol gila) deretan bebek palupi  pentol gila",
            "Jl Rungkut Mejoyo Utara Blok AF 16",
            "Rungkut Asri Tengah XIII No.31. Rungkut Asri Tengah XIII, Kec. Rungkut, Kota SBY, Jawa Timur, 60293 [Tokopedia Note: Rungkut asri tengah XIII No 31 surabaya]",
            "Rungkut Mejoyo Utara V / AF-12",
            "Kantor Kelurahan Rungkut Kidul, Jalan Rungkut Asri No.3B, Rungkut Kidul  RT 4/RW 8 Rungkut Kidul, Kota Surabaya, Jawa Timur ID 60295",
            "Jl. Rungkut Mejoyo Utara V/AF 4 Surabaya ",
            "Jl. Rungkut Asri No.87  Rungkut asri no 87 kalirungkut Rungkut Surabaya jawatimur ID 60293",
            "Ceria Residence, Jalan Rungkut Mejoyo Utara IV Blok ag No 1, Kali Rungkut, Rungkut (Kost Abu-abu kuning) (Kamar 20)",
            "Jalan Rungkut Mejoyo Utara IV  AG-1 Kamar 16",
            "pagar warna hitam  rungkut asri tengah xiii no 27",
            "Jl Rungkut Asri XIV No 1 (RL 3D No 14)",
            "pagar warna hitam  rungkut asri tengah xiii no 27",
            "Jalan Rungkut Mejoyo Utara IV Blok Ag No 6, Kali Rungkut, Rungkut (Reddoorz)",
            "Jl Rungkut Mejoyo Utara IV No 19, Kali Rungkut (KOS PUTRI AF-39)",
            "Rungkut Asri Tengah XI No 16, Rungkut Kidul",
            "Jl. Rungkut Asri XV No.26 ",
            "Jl Rungkut Mejoyo Utara IV No 19, Kali Rungkut (KOS PUTRI AF-39)",
            "Kost Af 37, Jalan Rungkut Mejoyo Utara IV Blok Af No. 37 ",
            "Jalan Rungkut Asri Tengah VIII, Rungkut Kidul, Rungkut (SDN RUNGKUT KIDUL 2)",
            "Jl. Rungkut Mejoyo Utara IV No.33  michaeldesanto80@gmail.com(Jalan Rungkut Mejoyo Utara IV Blok af No.33, Kali Rungkut, Rungkut)",
            "PERUM RUNGKUT ASRI XV NO 05 RUNGKUT LOR RL III - C NO 12 SURABAYA",
            "PERUM RUNGKUT ASRI XV NO 05 RUNGKUT LOR RL III - C NO 12 SURABAYA",
            "Rungkut Asri Tengah gang12 no 18",
            "jl. rungkut mejoyo utara IV no. 33  michaeldesanto80@gmail.com(jalan rungkut mejoyo utara IV blok af no. 33,kali rungkut, rungkut",
            "Jl. Rungkut Asri XVI No.1 ",
            "Kost Putri AF 29, Jalan Rungkut Mejoyo Utara IV No 41, Kali Rungkut, Kalirungkut, Rungkut",
            "RUNGKUT ASRI TENGAH XV NO 12 SURABAYA ",
            "Waroeng Blue Sky, Kali Rungkut, Kota Surabaya, Jawa Timur, Indonesia  Af 29 (Sebelah waroeng blue sky)",
            "Jalan raya Rungkut Asri Tengah  No.6, Rungkut Kidul, Surabaya, Jawa Timur, Indonesia Tina bika samping palupi",
            "Rungkut asri tengah 20 no 17",
            "Jalan raya Rungkut Asri Tengah  No.6, Rungkut Kidul, Surabaya, Jawa Timur, Indonesia Tina bika samping palupi ",
            "Rungkut Mejoyo Utara III blok AG no 46",
            "Jalan Rungkut Asri Tengah XIX No 11, Rungkut (Pgr hitam, mbl putih)",
            "Jl. Rungkut Asri Tengah XVIII No.2 ",
            "RUNGKUT MADYA 33 SURABAYA",
            "Jl. Rungkut Mejoyo Utara IV No.46 ",
            "JL RUNGKUT MEJOYO UTARA BLOK AH NO 30A RT002 / RW 004 KEL KALIRUNGKUT KEC RUNGKUT SURABAYA -60293 (JOYO KOST)",
            "Rungkut asri tengah 8 no.76 surabaya ",
            "Scoob telur, Kali Rungkut  tenggilis mejoyo utara blok Ai no 8 surabay",
            "Rungkut Mejoyo Utara Blok AN 32 No 53  Depan Ubaya",
            "Jl. Rungkut Madya No.67  reziq laundry",
            "Koskita Residence, Jalan Rungkut Mejoyo Utara Blok An 44 No 29, Rungkut (Kamar 49)",
            "Kostkita Residence, Jalan Rungkut Mejoyo Utara No.29, Kali Rungkut  Kamar 49",
            "Rungkut Asri Tengah I No.22 ",
            "Kos Putri AN - 2, Jalan Tenggilis Mejoyo No.22, Kali Rungkut  AN 2",
            "rungkut asri tengah 6 nomor 22  surabaya jatim",
            "Jl. Rungkut Asri Tengah VI No.22  60293",
            "tenggilis mejoyo blok AN 4, Kali Rungkut, Kec. Rungkut, Kota SBY, Jawa Timur 60292  Depan Kampus UBAYA",
            "Kos Putri AN - 2, Jalan Tenggilis Mejoyo No.22, Kali Rungkut  AN 2",
            "Jl. Rungkut Asri Tengah VI No.29  pager ijo sebelah pohon cabe",
            "Rungkut asri tengah VII no 37-39  ",
            "jl.kaliwaru 1 no 19 c samping tower  samping tower",
            "JL. KALIWARU I NO.18 ( SEBELAH ATM MANDIRI KAMPUS UBAYA TENGGILIS ) KALIRUNGKIUT SURABAYA",
            "Roengkoet asri tgh tujuh no tiga lima",
            "Jl rungkut asri tengah VII no 35",
            "Pagarr Putih, Jalan Rungkut Asti Tengah VII No 35, Rungkut",
            "Jalan Rungkut Asri Tengah V No 18, Rungkut Kidul, Rungkut",
            "Jalan Rungkut Asri Tengah IV No 21, Rungkut Kidul, Rungkut",
            "Kaliwaru 1 no 12 Rt 3 Rw 8 Rungkut-Surabaya",
            "Jalan Rungkut Asri Tengah IV No 21, Rungkut Kidul, Rungkut",
            "Jalan Kaliwaru I No 12, RT 3/RW 8, Kali Rungkut, Rungkut (Kaliwaru 1 no 12)",
            "Jalan Rungkut Asri Tengah IV No 21, Rungkut Kidul, Rungkut",
            "kalii waru gang masjid nmr 17 kalirungkut ",
            "Rungkut Asri Barat XIV no. 20 Surabaya",
            "kalii waru gang masjid nmr 17 kalirungkut ",
            "Jl. Kaliwaru I No.27  Depan kos 88 /rmh kosong (rmh pagar hijau)",
            "Rungkut Asri Barat 12 No 33 Surabaya",
            "Rungkut Asri Barat 12 No 33 Surabaya",
            "Jl. Kaliwaru I No.27  Depan kos 88 /rmh kosong (rmh pagar hijau)",
            "Jalan Rungkut Asri Barat XII  No 17",
            "JLN RUNGKUT ASRI BARAT XII NO 12",
            "Jl. Kaliwaru I No.42  gang kecil samping smile grafik",
            "Rungkut Asri Barat XI No. 30 ",
            "jln Rungkut asri barat XI no 15 surabaya City, East Java, Indonesia ",
            "JALAN RAYA KALIRUNGKUT NO 42 B RT 3 RW 8 KALIWARU",
            "jln raya rungkut kali waru g 1 nmer 42  rumah pak derjo nmer 5",
            "RUNGKUT ASRI BARAT XI / 12 SURABAYA ",
            "Jalan Raya Kalirungkut 42B, Kali Rungkut, Kalirungkut, Rungkut Gang Samping Smile Grafika, Kos Pak Dhirjo No 7",
            "Rungkut Asri Barat  IX no 24",
            "Jl. Rungkut Asri Barat VIII no.26",
            "Jl. Raya Kalirungkut No.42  Kopkar Sampoerna (pagar biru sebelah bengkel AAM)",
            "Jl. Rungkut Asri Barat VII No.31 ",
            "JLN. RAYA KALIRUNGKUT NO.69 RUNGKUT KIDUL KEC. RUGKUT, SURABAYA, JAWA TIMUR",
            "Jalan Raya Kalirungkut nomor 70 Surabaya seberang Transmart Rungkut bebek ayam rempah",
            "Jl. Rungkut Asri Barat V No.4  RT. 003/ RW. 012, Perumahan, 60293, Sebelahnya Dr. Metha Saelan Surabaya",
            "Rungkut Asri Barat 1 no 10 (Setelah tianglistrik)",
            "Jl. Rungkut Asri Barat I No.5  pagar putih",
            "Showroom Mobil Suzuki Sejahtera Buana Trada, Jalan Kalirungkut No 80, Rungkut (Showroom mobil szuki)",
            "Rungkut lor gg33 a no 88 ",
            "Jl. Raya Kalirungkut No.86  (warkop aw rungkut depan deler Honda)",
            "rungkut lor 3 29/i kampung baru ",
            "Rungkut lor gang 3 kampung baru KAV 5 (Depan rmh ada tangga)",
            "Jl. Rungkut Lor Gg. III No.30 ",
            "Rungkut Lor Gg. III-B No.22A ",
            "Jalan Rungkut Lor Gang 3 B No 8(kamar belakang Sendiri), Kali Rungkut, Rungkut (Masuk Kos)",
            "Gg. III-B No.22  jl.rungkut lor gag 3b no 22",
            "rungkut lor gang 3a no 20a( gang depan indomaret pas)",
            "jln.raya kalirungkut gang 7 mesjid no 138c toko helem ",
            "Jl.Rungkut Lor Gang.VII No. 21, /RW.14, Rungkut Lor, Rungkut",
            "Jl Rungkut Lor Gang VII No 21, /RW 14, Rungkut Lor, Rungkut",
            "Rungkut Lor Gang VII Raya No 14 Surabaya kode pos 60293",
            "rungkut lor gang Vll raya no 12 surabaya ",
            "Kos Tepatnya Di Belakang Rumah, Jalan Rungkut Lor Gang VII No 17, Kali Rungkut, Rungkut",
            "Rungkut, Surabaya, East Java, Indonesia  rungkut lor gg7 masjid 22",
            "Jl. Rungkut Lor VII Masjid No.38  pertigaan",
            "Rungkut lor gang 7 masjid no 26(Barat pos rt 3) ",
            "Rungkut lor Gg 7 masjid No.48. Dari Gapura Gg 7 masjid yang barat masuk,rumah sebelah kanan depan rumah/kos tingkat ",
            "Rungkut lor gang 7 no 36 RT 001 RW 014",
            "Rungkut lor gang 7 Mulya  a no 1 belakang musholla an nur pas  Surabaya, Rungkut lor gang 7 Mulya a no 1 belakang musholla an nur pas",
            "Jl. Rungkut Lor VII No.39 a (belakang warung) ",
            "Jl. Rungkut Lor Gg. V No.31 ",
            "Jl. Rungkut Lor VII Mulya No.22  gang depan tower",
            "Rungkut Lor gang 7 mulia no 20 kec rungkut sby jatim Wa",
            "Rungkut lor gang 3 no.106,  warung miser mercon deket tower",
            "Rungkut lor gang 3 no.106,  warung miser mercon deket tower",
            "Jl. Rungkut Lor VII No.60  lantai 2",
            "Rungkut Lor ,gang perdamaian (nomer 73B lantai 2 ) RT 01 / RW 14 (Rumah Bp, sudarno)",
            "Rungkut Lor ,gang perdamaian (nomer 73B lantai 2 ) RT 01 / RW 14 (Rumah Bp, sudarno)",
            "Rungkut lor gg 7 perdamaian no 77,rungkut surabaya",
            "Rungkut Lor, Gang 7 Perdamaian No 76, Kali Rungkut, Rungkut (rumah paling pojok)",
            "rungkut lor gang 10 sentosa no 77z rt 02 rw 15",
            "Jl rungkut lor X no 77O ( SENTOSA)",
            "Jl rungkut lor gang 10 no 77o(sentosa) ",
            "Jalan medokan sawah timur ix A no. 62  sebelah cv powerking",
            "Rungkut Lor Gg X No 77M masuk gang sentosa dekat TK Tunas Bangsa RT02 RW15",
            "Jl. RUNGKUT LOR X NO. 77 H  lantai tetas biru pagar putih",
            "Jalan Medokan Sawah Timur Gang IXB No.5 Kel. Medokan Ayu, Rungkut Surabaya  Workshop PT. Bioteknologi Surabaya",
            "Rungkut Lor X/79 Kalirungkut (Kali Rungkut), Jawa Timur, Kota Surabaya, Rungkut",
            "Rungkut Lor X/79 Kalirungkut (Kali Rungkut), Jawa Timur, Kota Surabaya, Rungkut",
            "Jln Medokan Sawah Timur Gg VI B kav C-6 RT 06 RW 01",
            "Kecamatan Rungkut  rungkut lor gg x no.81A Rt 02 RW 15",
            "Jalan Rungkut Lor Gang X No 81a, Kali Rungkut, Kalirungkut, Rungkut (kos bu sujini)",
            "Rungkut Lor 10/78 Rungkut Kota Surabaya Jawa Timur",
            "jln medokan sawah timur 9d kav 244A ",
            "Jl.Rungkut lor X no.111-D,RT 01/RW 15  Dekat musholla Al-Ashro",
            "Jl.Rungkut lor X no.111-D,RT 01/RW 15  Dekat musholla Al-Ashro",
            "Jl Medokan Sawah Timur VIII A No 275 RT 6 RW 1 Rungkut Surabaya (RmhKmbrPgrCnopyPutih)",
            "Rungkut Lor x No 82",
            "JL. RUNGKUT LOR 10 NO. 107 SURABAYA",
            "Rungkut Lor, Gang X No 95B, Kali Rungkut, Rungkut (Sebelah toko bu yuli)",
            "Kos Pak Maryamin, Jl. Rungkut Lor Gg. X No.62, RT 01 RW 015, Kode Pos 60293 ",
            "Jalan Medokan Sawah Timur Gang VIII A  Jl. Medokan Sawah Timur 8-A/No. 275A",
            "Jl Medokan Sawah Timur Gang VIIC Kav 35 seberang Mushola Al Muhajirin (BUKAN NOMOR 35) Kavling 35 ya Pak",
            "Jalan Rungkut Lor Gang X No 62, RT 1/RW 15, Kalirungkut, Rungkut (Titipkan ke Warkop Abah Maryamin)",
            "Rungkut lor GG 10 Gang kelinci no.16H1 Surabaya _jawa timur",
            "medokan sawah timur gang 8 no 7 rt 13/rw 1 kel medokan ayu kec rungkut surabaya  pagar rumah warna merah",
            "Jl Rungkut Lor Gg X No 16k, Kali Rungkut",
            "medokan sawah timur gang 8 no 7 rt 13/rw 1 kel medokan ayu kec rungkut surabaya  pagar rumah warna merah",
            "jl. rungkut Lor Gg X no 16B1 Surabaya  gang kelinci (pagar kuning)",
            "Rungkut Lor Gang X Makmur No.63D Surabaya Kalirungkut (Kali Rungkut), Jawa Timur, Kota Surabaya, Rungkut",
            "Jl. Rungkut Lor Gg. X No.53 ",
            "Jl. Rungkut Lor Gg. X No.53 ",
            "Jl. Rungkut Lor Gg. X No.29, Kali Rungkut, Kota Surabaya, Jawa Timur, Indonesia (kmr no.8) ",
            "Jl.medokan sawah timur GG 7a,no 01,kav 159, Rungkut Surabaya",
            "Jalan Rungkut Lor Gang X no 2D (kos an), Kali Rungkut",
            "Jalan Rungkut Lor Gang X no 2D (kos an), Kali Rungkut",
            "Jl. Medokan Sawah Timur Gang 7A No. 01 Kav. 159",
            "Jalan Rungkut Lor Gang X no 2D (kos an), Kali Rungkut",
            "medokan sawah timur gang 6B  depan masjid Raudhatul Jannah",
            "Jalan Medokan Sawah Timur Gang 6B No 144, Rungkut",
            "medokan sawah timur gang 6a no 07  medokan sawah timur gang 6a no 07",
            "Jl. Raya Medokan Sawah Timur No.14  Medokan sawah timur gang V nomor 14 pagar merah",
            "Jl Medokan Sawah Timur Gg V No 8C",
            "Jl Medokan Sawah Timur Gg V No 8C",
            "medokan sawah timur gang 5 no 25 surabaya(kosan lantai 1 kamar no 3) ",
            "jl.medokan sawah timur GG V no 25 (kamar no.7)",
            "jl pandugo gg 2 no 12 ( H. Selan ) rt 03 rw 01  jl.pandugo gg 2 no 12 ( H. Selan ) rt 03 rw 01",
            "pandugo GG. 2 no 12 B  pertigaan toko kelontongan cak Man klopo",
            "Medokan Sawah Timur V no 38 RT. 06  RW. 01 Rungkut Medokan ayu Surabaya  Pagar hitam ada motif cat keemasan",
            "Jalan Raya Medokan Sawah Timur  medokan sawah timur gg5 no.44",
            "jl pandugo gg 4 no 15 a ",
            "Jalan Medokan Sawah Timur Gg VI No 38, Medokan Ayu, Rungkut (depan toko taufik)",
            "Jl. Pandugo Gg. IV No.17  Rt.001 Rw.001 kel.penjaringan sari Rungkut,kota surabaya 60297 jawa timur Indonesia",
            "jl pandugo VI no 1  warung pitulungan",
            "Medokan Sawah Timur Gg 7-C / Nomer 35 (YANG BANYAK KANDANG KELINCINYA / ADA KERE BAMBUNYA) (LANGSUNG LEMPAR KE DALAM LAGAR) (hp 08999017117)",
            "Jl. pandugo GG 5a ",
            "Jalan Medokan Sawah Timur Gang VII medokan sawah timur VII D KAV 103 Medokan Ayu, Jawa Timur, Kota Surabaya, Rungkut",
            "jln medokan sawah timur gang 7D ",
            "pandugo kampung gang 6 no 30 blok B penjaringan ",
            "pandugo gg VII no.2 penjaringan sari rungkut surabaya ",
            "Jalan Pandugo Gang I No 85b, RT 4/RW 1, Kelurahan Penjaringansari, , , , Indonesia ((Pagar Hitam))",
            "Jl. Pandugo Gg. I No.36  cat warna biru",
            "medokan sawah timur VI D NO 115 RT 06 RW 01 ",
            "Jl. Pandugo Gg. I No.54 ",
            "Jl. Pandugo Gg. I No. 9, Penjaringan Sari, Kota Surabaya, Jawa Timur, Indonesia  Depan TK Al-Fitroh",
            "Jalan Pandugo Gang I  jl. pandugo gang 1 no 2",
            "Medokan Sawah Timur gang 4D no. 21A ",
            "medokansawahTimur gg IV no 70  kost pagar hitam,kamar no 7(Nia)",
            "Jalan Raya Pandugo no. 3 jualan roti goreng ",
            "Jalan Raya Pandugo no. 3 jualan roti goreng ",
            "Penjaringan Sari, Surabaya, Jawa Timur, Indonesia  gang 1 no 1 RT 001 RW 002",
            "jl. penjaringan gg 1 no 29a 01/02 Penjaringansari (Penjaringan Sari), Jawa Timur, Kota Surabaya, Rungkut",
            "JLN. MEDOKAN SAWAH TIMUR IV C / 37 MEDOKAN AYU, RUNGKUT SURABAYA",
            "kav 27, Jl. Penjaringan Tim. No.9 ",
            "Jl Medokan sawah timur gg IV C / 37 ",
            "Jl. Penjaringan Gg. 1B No. 1 Kav. 13 RT. 01 RW. 02 Kel. Penjaringan Sari",
            "Penjaringan kampung gang 2/10  kost pak Ripto belakang makam  depan blok 1 b",
            "medokan sawah timur gang II tembusan  Rumah lantai 2 cat putih ( paling pojok )",
            "penjaringan 1 a kav 4 ( belakang makam penjaringan kampung )",
            "medokan sawah timur gang II tembusan  Rumah lantai 2 cat putih ( paling pojok )",
            "pager abu abu  penjaringan sari , gg 3a, no 24c",
            "jalan penjaringan sari no 14  RT.2 RW.2  depan masjid At-Taqwa,mebel",
            "Jl. Penjaringan Sari no 18 ",
            "medokan sawah timur . gang 4 A no.61 A. RUNGKUT SURABAYA",
            "Jl. Penjaringan No.18 RT 03 RW 02 Rungkut Surabaya Jawa timur 60297  rumah pak Rt",
            "Koleksikaktus id, Jalan Raya Medokan Sawah Timur, Jalan Medokan Sawah Timur Gang IV B, Medokan Ayu (Gang 4B no 16)",
            "Jalan Penjaringan Sari No 39b, Penjaringan Sari, Rungkut",
            "Koleksikaktus id, Jalan Raya Medokan Sawah Timur, Jalan Medokan Sawah Timur Gang IV B, Medokan Ayu (Gang 4B no 16)",
            "jalan penjaringan kampung no47 penjaringan sari ",
            "Rungkut  Penjaringan Sari 39A (kos Lt 2 A/N mb ivin)",
            "jl penjaringan gang baru 49 a rumah bpk rijan ",
            "Jl. Medokan Sawah Timur Gg. IVA No.50e  Kos-Kos an Pagar Putih, Whatsapp dulu tolong",
            "Medokan sawah timur 4a kav 50h",
            "JL Raya Kendalsari no 18 A  JL raya Kendalsari no 18 A ( pagar hijau, sebelah counter Surya Cell,)",
            "rusun penjaringan sari 3 lantai 4 no:16,",
            "rusunawa penjaringan sari 3(rusunawa penjaringan sari 3/lantai 5 nomer 19)",
            "MEDOKAN  SAWAH  TIMUR  3  NO.38B  , RUNGKUT  , SURABAYA  , ID 60298",
            "rusunawa penjaringansari 3 lantai 4 no 02",
            "Rusunawa Penjaringansari 3 lt 4-07 Rt 07 Rw 10",
            "raya wonorejo permai rk 39",
            "JL.PENJARINGAN SARI NO.40B DEPAN TAMAN KUNANG KUNANG ( BELAKANG WARUNG PODOMORO) ",
            "jln penjaringan timur 40 Surabaya Jawa timur(jln raya)",
            "Wonorejo Permai Selatan  RK-11",
            "raya wonorejo permai rk 39",
            "Medokan Sawah Timur 2 NO 106 B  Masuk Gang tembusan sebelah langgar Djafar Husen (rumah pagar hitam)",
            "Kost Gerbang Putih, depan taman kunang, penjaringan timur no.38, Rungkut , Surabaya, Jawa Timur, Indonesia  samping java print",
            "Jl. Raya Medokan Sawah Timur No.2  no 110A",
            "Jl. Penjaringan Timur No.15C((Depan Rusunawa 4 , Toko Listrik Cahaya Purnama)) ",
            "jl. medokan sawah timur 2 kav 106 a ",
            "Indomaret Penjaringan Timur  depan rusun penjaringan timur",
            "Jl. Medokan Sawah Timur Gang 1b No. 11(Pager Warna Putih)",
            "Medokan Sawah Timur Gang 1A Kav.59A ",
            "Wonorejo Permai Selatan  RK-11",
            "Rusun Penjaringansari Blok FA no.416 ,kel. Penjaringansari, Kec. Rungkut , Surabaya 60297  Rusun Penjaringansari Blok FA no.416 ,kel. Penjaringansari, Kec. Rungkut , Surabaya 60297",
            "jl.Raya WONOREJO PERMAI BLOK  RK.02  SURABAYA  Dekat  ALFA MART",
            "rusunawa penjaringan sari blok FA No 404",
            "rusun penjaringansari 2 blok Fa 402 ",
            "jl.Raya WONOREJO PERMAI BLOK  RK.02  SURABAYA  Dekat  ALFA MART",
            "Wonorejo Permai VI no 6 (Nirwana Eksekutif blok AA), Wonorejo. Rungkut, Surabaya, Jawa Timur, 60296",
            "Rusun Penjaringansari Blok FA no.416 ,kel. Penjaringansari, Kec. Rungkut , Surabaya 60297  Rusun Penjaringansari Blok FA no.416 ,kel. Penjaringansari, Kec. Rungkut , Surabaya 60297",
            "rusun penjaringan sari blok FA 312  Rusun Blok FA nomor 312",
            "taman rivera blok H5 medokan ayu rungkut ( rumah mainan ) (081331042226)",
            "Perumahan Taman Rivera Regency, blok H No 11  Jalan utama, bunderan air mancur, rumah pojok",
            "Rusun Penjaringan Sari, Jalan Penjaringan Sari Blok Ea No 206, Penjaringan Sari, Rungkut (Blok EA - 206)",
            "Rusun Penjaringan Sari, Jalan Penjaringan Sari Blok Ea No 206, Penjaringan Sari, Rungkut (Blok EA - 206)",
            "rusun penjaringan sari  blok DA 301",
            "wonorejo permai XII / CC601 perum nirwana eksekutif, Kec. Rungkut, Kota SBY, Jawa Timur, 60298  Tokopedia Note  B2916 ",
            "Wonorejo permai Selatan blok CC Gang XI No 683",
            "rusunawa penjaringan sari 2 blok EA 216  depan Indomaret",
            "Wonorejo permai Selatan blok CC Gang XI No 683",
            "Taman Rivera Regency E4 , Jl.Medayu Indah Regency V/8 Rungkut ",
            "Wonorejo Permai Selatan VII BLOK CC 616",
            "Perum Nirwana Eksekutif CC 420, Jl Wonorejo Permai Selatan IX no 34",
            "Jl. Wonorejo Permai Selatan 6 No. 30, Nirwana Executive blok CC 436, Kel. Wonorejo",
            "rusun penjaringan sari blok D lantai db 202 Rt 04 rw 10 kel penjaringan sari . Kec rungkut surabaya, JAWA TIMUR, SURABAYA, RUNGKUT, PENJARINGAN SARI",
            "Rusun penjaringan sari blok c no 118 (lantai 1 blok c no 118)",
            "Jalan Wonorejo Permai Selatan VI  blok CC no 399",
            "Medayu pesona XI blok M no. 18 ",
            "RUSUN PENJARINGAN SARI BLOK B 313 RUNGKUT SURABAYA",
            "griya pesona asri blok L19 jl medokan ayu rungkut surabaya ",
            "griya pesona asri L 35 medokan ayu rungkut",
            "rusun penjaringansari blok A 108",
            "Wonorejo Permai Selatan  blok cc,389 nirwana executive rungkut",
            "Rusun penjaringan sari  blok A/415",
            "JL WONOREJO PERMAI SELATAN V NO 17 BLOK CC 331 NIRWANA EKSEKUTIF RUNGKUT SURABAYA",
            "Griya Pesona Asri Ykp, Jalan Medayu Pesona I, Medokan Ayu, Rungkut (Blok L no 31)",
            "Jalan Penjaringan Timur no.02  masakan Padang Ampera Minang jaya",
            "Perum Griya Pesona Asri Jl Medayu Pesona XIII Blok K No 11 (Rumah Pagar Hijau)",
            "JL. WONOREJO PERMAI SELATAN 5 / 17 BLOK CC 331 NIRWANA EKSEKUTIF RUNGKUT, SURABAYA ",
            "GRIYA Pesona Asri (YKP), Jalan Medayu Pesona  Blok L no. 23",
            "Pandugo GG 3-2A  Pagar Hitam",
            "Perumahan Nirwana Executive CC-356, Jalan Wonorejo Permai Selatan V/64",
            "GRIYA PESONA ASRI BLOK J 41MEDOKAN AYU RUNGKUT ",
            "Jl. Wonorejo Permai Selatan IX No.95  wonorejo permai selatan blok CC IX nomer 95,perumahan nirwana eksekutif,surabaya,jawa timur",
            "Villa Eidelweis, Jalan Raya Pandugo, Penjaringan Sari, Surabaya, East Java, Indonesia  no A3",
            "TOKO CAT TIRTA WARNA JAYA, Jl. Raya Pandugo no. 226A (Depan Taman Pandugo)",
            "Jl. Raya Pandugo No.155  taman pandugo",
            "Jl Medayu Pesona XIV Blok J No 19 Perumahan Griya Pesona Asri RT 04 RW 10 Medokan Ayu",
            "Jl. Wonorejo Permai Selatan IX No.95  wonorejo permai selatan blok CC IX nomer 95,perumahan nirwana eksekutif,surabaya,jawa timur",
            "Seberang Warkop super mantep  Mebel dilla",
            "Jalan Wonorejo Permai Selatan III Blok cc No 208, Wonorejo, Rungkut (Pagar putih)",
            "Wonorejo permai selatan 3/32 (perumahan nirwana eksekutif),rungkut.",
            "Griya Pesona Asri blok i no.19(Blok i no.19)",
            "perumahan nirwana eksekutif blok CC gg 3 no 223A",
            "Wonorejo Permai Selatan gg 2 No.55  nirwana eksekutif blok cc no 55",
            "Jl , raya pandugo NO.31,RT.01,RW.IX, medayu utara,Kec.Rungkut,surabaya.  Jawa timur (DEPAN INDOMARET) toko madura",
            "GRIYA PESONA ASRI, JALAN MEDAYU PESONA BLOK H NO 18, MEDOKAN AYU, RUNGKUT (BLOK H NO 18) KOTA SURABAYA, RUNGKUT, JATIM ID 60295",
            "Jl Wonorejo Permai Selatan, Blok CC, gg 1 no 50 (CC-79)Perum Nirwana Executive",
            "Perum griya pesona asri ykp, medayu pesona xvii/H-37",
            "Jl Wonorejo Permai Selatan, blok CC gang 1 no 50 (79)Perum Nirwana Eksekutif - Rungkut(Rumah Pagar Hitam)",
            "Medayu Pesona VI Blok E No. 31  Perum GPA",
            "Jl Wonorejo Permai Selatan, blok CC gang 1 no 50 (79)Perum Nirwana Eksekutif - Rungkut(Rumah Pagar Hitam)",
            "Griya Pesona Asri (YKP), Jalan Medayu Pesona VI Medokan Ayu, Rungkut, Kota Surabaya, Jawa Timur, Indonesia  perum YKP Griyo Pesona Asri Blok F.14 Medokan Ayu Rungkut Surabaya",
            "Jl. Wonorejo Permai Selatan CC 1 / 35, Kec. Rungkut, Kota SBY, Jawa Timur",
            "Jl. Wonorejo Permai Selatan I No.126 Nirwana Eksekutif Blok CC 1 / 126 ",
            "Griya Pesona Asri D44-45 , Jl Medayu Pesona IV No D44-45",
            "NIRWANA EKSEKUTIF CC 155 JALAN WONOREJO PERMAI SELATAN  GANG 2 NO 37 SURABAYA 60296",
            "Wonorejo Permai Selatan X/2-C Blok CC No. 57-A3  AW Victory (Google Maps)",
            "Jl Medayu Pesona V blok E 1, Griya Pesona Asri , Medokan Ayu,",
            "griya pesona asri blok b no 31 , 60295 , rungkut medokan ayu surabayaKecamatan  rungkut",
            "Griya Pesona Asri, Jalan Medayu Pesona III Blok C-10  Rumah",
            "Jalan Griya Pesona Asri  Griya Pesona Asri Blok B no 8",
            "Jalan Griya Pesona Asri  Griya Pesona Asri Blok B no 8",
            "Perumahan Ykp Kota Surabaya, Jalan Medokan Ayu Selatan, Medokan Ayu  RT 01 RW 10 Blok B2",
            "Jalan Griya Pesona Asri  blok B no.3",
            "Perumahan Nirwana Eksekutif, Jalan Wonorejo Permai Selatan Gang X No 63, Rungkut",
            "perumahan nirwana eksekutif block ee gang 8 no 163  perumahan nirwana eksekutif jln wonorejo permai timur block ee gang 8 no 163",
            "Jl , raya pandugo NO.31,RT.01,RW.IX, medayu utara,Kec.Rungkut,surabaya.  Jawa timur (DEPAN INDOMARET) toko madura",
            "Jalan Wonorejo Permai Timur VII Blok ee No 132, Wonorejo, Rungkut (Gang 7/7 blok ee 132)",
            "perumahan kosagrha  blok III no.30",
            "perumahan kosagrha  blok III no.30",
            "Jl. Medayu Selatan VI, Kec. Rungkut, Kota SBY, Jawa Timur, 60295 [Tokopedia Note: Blok F No 01 RT 03 RW 04]",
            "Raya Pandugo no 145 kavling 19-21 ",
            "Jalan Medayu Selatan VI No.11, Medokan Ayu, Rungkut (KOS AGRA), KOTA SURABAYA, RUNGKUT, JAWA TIMUR, ID, 60295 ",
            "RAYA WONOREJO 17 RT1 RW1 WONOREJO RUNGKUT SURABAYA",
            "Jalan Medayu Selatan VI No.11, Medokan Ayu, Rungkut (KOS AGRA), KOTA SURABAYA, RUNGKUT, JAWA TIMUR, ID, 60295 ",
            "Jl. Raya Pandugo No.kAV 26 Wonorejo, Rungkut, Surabaya, Jawa Timur, Indonesia ",
            "PT.CANDI ARTHA JL.PENJARINGAN ASRI X NO.9, JAWA TIMUR, SURABAYA, RUNGKUT",
            "Jl. Penjaringan Asri X, Penjaringan Sari 1i No.18 (pagar hijau panda), Kec. Rungkut, Kota Surabaya, Jawa Timur 60297  pagar hijau panda no 18",
            "Jalan Medayu Selatan VII  Medayu Selatan VIII /18",
            "Jl Taman Wonorejo Permai Timur EE-115 Wonorejo rungkut Surabaya",
            "Jl. Rungkut Lor Gg. X No.21 ",
            "PERUMAHAN NIRWANA EXECUTIVE, JL TAMAN WONOREJO PERMAI TIMUR 2/EE115 (DEPAN TAMAN) WONOREJO, RUNGKUT, SURABAYA 60296",
            "Jl. Rungkut Lor Gg. X No.21b, Kali Rungkut, Kota Surabaya, Jawa Timur, Indonesia ",
            "PERUMAHAN NIRWANA EKSEKUTIF BLOK EE/115 - WONOREJO SURABAYA",
            "jln Medayu Selatan IX / H 2 RT 04 RW 04 (Perumahan Kosagrha, Gang 9 Blok H Nomer 2)",
            "Jl. Wonorejo Permai Tim. X No.31 Blok EE29  perumahan nirwana eksekutif",
            "Jl penjaringan asri XVII blok ps 1E(No 34-36)",
            "Jalan Penjaringan Asri I  ps 1 blok c no 16 - 17  surabaya.. samping masjid baitudz dzikri",
            "Kosagrha, Jalan Medokan Ayu Selatan, Medokan Ayu, Surabaya, East Java, Indonesia  jl medayu selatan XXI blok V /27",
            "Jalan Wonorejo Permai Timur gg V / 17 Perumahan Nirwana Executive blok EE - 88",
            "Jl. Penjaringan Asri XVI No.14, Rungkut, Surabaya ,(up.Bu Linda, (085730100080), (CV.TRI DAYA PUTRA),",
            "NIRWANA EKSEKUTIF , JL WONOREJO PERMAI TIMUR V/11  ( BLOK EE -86 ) RUNGKUT , SBY",
            "jl.penjaringan asri XIV PS.1a no.23 Rungkut Surabaya ",
            "Jl. Medayu Selatan XIII No.24  Jl. Medayu Selatan XIII L. no 24",
            "WONOREJO PERMAI SELATAN GANG 10 NO 7 PERUMAHAN NIRWANA EKSEKUTIF EE-240 KEC RUNGKUT SURABAYA, PROVINSI JAWA TIMUR, KAB/KOTA SURABAYA, KECAMATAN RUNGKUT",
            "Jl. Medayu Selatan XIII Blok O No.7  Perum kosagrha, Kel Rungkut Kec Medokan Ayu",
            "Perumahan kosa Graha Jalan medayu Selatan 4 nomor 41 Kecamatan Rungkut Surabaya",
            "Jalan Pandugo Gg VII No 22, Penjaringan Sari, Rungkut",
            "medayu selatan XI/11 (perum kosagrha) ",
            "Jalan Pandugo Gg VII No 22, Penjaringan Sari, Rungkut",
            "Jalan Medayu Selatan V No 6, Medokan Ayu, Rungkut (Blok M Nomor 6)",
            "Wonorejo permai Timur nirwana eksekutif blok ee raya nmr 11 5 a ngrungkut Surabaya ",
            "JL MEDAYU SELATAN 2 NO 5 RT 02 RW 04 MEDOKAN AYU , RUNGKUT , SURABAYA",
            "Perum Kosaghra, Jalan Medayu Selatan IV No 15, Medokan Ayu, Rungkut",
            "Perum Kosaghra, Jalan Medayu Selatan IV No 15, Medokan Ayu, Rungkut",
            "Klinik Dokter eko Jalan Medayu Selatan IV No 15, Medokan Ayu, Rungkut (perumahan kosagrha)",
            "perum kosaghra jl medayu Selatan 4 no 15 klinik dr eko",
            "Jl. Wonorejo Permai Timur II, Kec. Rungkut, Kota SBY, Jawa Timur, 60296 [Tokopedia Note: jl. wonorejo permai timur ii no. 18 blok dd-67]",
            "Nirwana Eksekutif blok DD NO 51(Wonorejo Permai timur ll), KOTA SURABAYA, RUNGKUT, JAWA TIMUR, ID, 60296",
            "JL MEDAYU SELATAN  III NO 4 RT 1 RW 4 PERUM KOSAGRAH KEL MEDOKAN AYU KEC RUNGKUT SURABAYA 60295",
            "Wonorejo Permai utara X/2 blok DD26",
            "jl medokan sawah timur 4D no 21A RT 007 RW 001 ",
            "Jl Medokan Sawah Timur IVD Kav 13 (Sebelah tanah kosong)",
            "JALAN RAYA  WONOREJO  PERMAI  UTARA  BLOK BB 575 PERUMAHAN   NIRWANA   EXECUTIE  SURABAYA  6029",
            "jl wonorejo permai utara 7/ BB-503  Perum. Nirwana Eksekutif",
            "jl wonorejo permai utara 7/ BB-503  Perum. Nirwana Eksekutif",
            "Wonorejo Permai Utara VII/16, Nirwana Eksekutif BB-508",
            "Jl. Wonorejo Permai Utara VII/BB 555, Kec. Rungkut, Kota SBY, Jawa Timur, 60296",
            "Jalan Wonorejo Permai Utara IX  Nirwana Eksekutif Blok BB Gg.IX No.85",
            "Jalan Wonorejo Permai Utara VI/BB394 no 38  Pagarnya warna putih, sebelah kanan",
            "Wonorejo permai Utara 6 no 15(perumahan nirwana esekutif blok BB gang 6 no 15) ",
            "Wonorejo permai Utara 6 no 15(perumahan nirwana esekutif blok BB gang 6 no 15) ",
            "Jl Wonorejo Permai Utara V/55 blok BB-330 nirwana executive rungkut sby",
            "jln wonerejo permai utara v no 55/330 nirwana eksekutif ",
            "Jl. Wonorejo Permai Utara 1V Blok BB 62 ",
            "Perum nirwana executive Block BB 260 jln wonorejo permai utara Gg IV no 25  pagar hitam",
            "Perum nirwana executive Block BB 260 jln wonorejo permai utara Gg IV no 25  pagar hitam",
            "Jalan Wonorejo Permai Utara Gang 3 No. 24 (Perumahan Nirwana Eksekutif Blok BB-245)",
            "jl.kendalsari gang ll no 22B  gang ll no 22B dekat pos",
            "jl kendalsari gang 1 no 67 ( kamar kos no 8 )  jl kendalsari gang 1 no 67 ( kamar kos no 8 )",
            "Jl. Kendalsari Gg. I No.47B ",
            "Jl. Kendalsari Gg. I No.47B ",
            "Jl kendalsari gang 1 no 47",
            "Jl. Kendalsari Gg. I No.28 ",
            "Jl. Kendalsari Gg. I No.18  Rumah kos h.fatkhur rohman",
            "Jalan Kendalsari Gang II No.18a, Penjaringan Sari, Rungkut, Surabaya City(kendalsari gg2 no 18a)",
            "Jalan Kendalsari Gang Masjid  Kendalsari gg. 2 No. 9",
            "Jl. Kendalsari Gg. II No.9  gang masjid",
            "Jl. Kendalsari Gg. II No.9A ",
            "Jl Kendalsari Gg I No 16, Penjaringan Sari (gg masjid, no 16)",
            "Jl Kendalsari Gg I No 16, Penjaringan Sari (gg masjid, no 16)",
            "Jl. Kendalsari Gg. I No.16  gang masjid al murtadho",
            "Jln.Kendalsari gang I.G Rt.03 Penjaringansari (Penjaringan Sari), Jawa Timur, Kota Surabaya, Rungkut",
            "Jl. Kendalsari Gg. I No.20  masjid Al Murtadho",
            "Jalan Kendal Sari Gang I no.12, Jawa Timur, Kota Surabaya, Rungkut",
            "Jalan Kendalsari Gang I  kos pak cipto",
            "Medayu Utara 5 No 11 A Rungkut Surabaya",
            "Jl. Medayu Utara V No.17 ",
            "Jl. Medayu Utara V No.17  tambal ban wisnu",
            "Jalan Medayu Utara V No 23, RW 11, Medokan Ayu, Rungkut (No 23)",
            "Medayu Utara Gg. XI No.60 ",
            "Medayu Utara XI No.27, Medayu Utara XI, Kec. Rungkut, Kota SBY, Jawa Timur, 60295  Tokopedia Note  Medayu Utara XI No 27, RT 01, RW 12 ",
            "Medayu Utara XI No.37  Rumah pojok, cat pink, pager item",
            "JALAN MEDAYU UTARA XII NO 44 RT 1 RT 14 MEDOKAN AYU RUNGKUT RUNGKUT SURABAYA",
            "medayu utara gang 12 no 17 rt:02-rw12 jawa timur sby rungkut  ada pohon cemara tinggi pas dpn rumah no 37",
            "Jalan Medayu Utara XII No 35A, Medokan Ayu, Rungkut (Pagar hitam & kanopi)",
            "medayu Utara RT 02 RW 12 Medokan Ayu, Jawa Timur, Kota Surabaya, Rungkut",
            "jl medayu utara 12/38 ",
            "Gg. XII No.10  medayu utara gg XII No.10",
            "Gg. XII No.10  medayu utara gg XII No.10",
            "jl medayu utara XII no 04 ",
            "jl medayu utara XII no 04 ",
            "Jl. Medayu utara 5 no 54 kelurahan medokan ayu ",
            "MEDAYU UTARA GG 5 NO 54 ",
            "Medayu Utara 13/4",
            "medayubutara gang 13 no8 ",
            "jl. medayu utara gg 13 no. 10 ",
            "Jalan Medayu Utara XIII No 9, RW 12, Medokan Ayu, Rungkut",
            "medayu utara 13 No 13",
            "Jl. Medayu Utara XII No.11 ",
            "Jl. Medayu Utara XIII No.29 ",
            "Medayu Utara, Gang XIII No 30a, RT 3/RW 12, Medokan Ayu, Rungkut (pager biru)",
            "medayu Utara gang 13 no.31A ",
            "jln. ir hj soekarno merr 2c pandugo rungkut surabaya (bebek goreng harisa) ",
            "PT BERHASIL INDONESIA GEMILANG JLN. IR H. SOEKARNO NO 531 A 531 D, KEC. RUNGKUT, KOTA SURABAYA 60293",
            "jl Ir Soekarno MERR pandugo",
            "Medayu Utara,Gg XIV a,No.42a, Medokan ayu, Rungkut. ",
            "Medayu Utara,Gg XIV a,No.42a, Medokan ayu, Rungkut.",
            "PT. BERHASIL INDONESIA GEMILANG RUKO MANHATTAN NO.531 A - 531 D JL. IR SOEKARNO (MERR) PANDUGO RUNGKUT SURABAYA ",
            "Jl. Medayu Utara XIV No.39  tembok warna hijau",
            "medayu utara gg14A no41 ",
            "PT BERHASIL INDONESIA GEMILANG BIGLOG RUKO MANHATTAN NO. 531A-531D JL.IR SOEKARNO (MERR)  PANDUGO RUNGKUT SURABYA ",
            "PT BERHASIL INDONESIA GEMILANG JLN IR H. SOEKARNO NO 531 A 531 D, KEC. RUNGKUT, KOTA SURABAYA 60293",
            "Jalan Penjaringan Sari II  Blok B No.9 (kost wanita)",
            "Jl Penjaringan Asri II (PS 2B no 26 ) Rungkut Surabaya (rumah putih 3 dari pojok )",
            "jl. penjaringan asri VII NO 8(rumah)",
            "Jl. Penjaringan Asri VII No.14 ",
            "Jl. Medayu Utara XIV No.2  rumah pagar warna hitam",
            "Es Degan Shafeera, Jalan Wonorejo Timur, Rungkut (Warkop kopen)",
            "Jl. Wonorejo Timur 25, Wonorejo, Kec. Rungkut, Kota Surabaya, Jawa Timur 60296.",
            "jln.medayu utara gg XV no.4a (belakang SD Nurul Faizah) ",
            "Jalan Raya Wonorejo Timur No 25, Kel Wonorejo RT 03 RW 07 ((pagar biru))",
            "Jalan Raya Wonorejo Timur no.25 rt.03/rw.07 kost pagar biru Wonorejo, Jawa Timur, Kota Surabaya, Rungkut",
            "Jalan Penjaringan Asri VII,perumahan penjaringan sari,surabaya ,rungkut ,jawa timur ",
            "Jl. Penjaringan Asri V No.41 ",
            "Jl. Wonorejo Timur No.153  warung gendok 01",
            "Jl Wonorejo timur rt 04 rw 07 no 15 rungkut,surabaya (depan warung Bu Yem)",
            "Arah Wisata Mangrove, Jalan Wonorejo Timur No.46 RT.4/RW.7, Wono Rejo, Rungkut Wonorejo, Jawa Timur, Kota Surabaya, Rungkut",
            "Wisata Semanggi K-24, kel Wonorejo, kec Rungkut, surabaya",
            "PERUMAHAN WISATA SEMANGGI, Jalan Wonorejo Indah Timur No.30, Wonorejo  perumahan wisata semanggi BLOK K30",
            "Perumahan Wisata Semanggi Mangrove, Jalan Wonorejo Timur Surabaya, Wono Rejo, Rungkut (Blok P 8)",
            "WISATA SEMANGGI BLOK K NO 36 JL WONOREJO RUNGKUT SBY",
            "Jl. Penjaringan Asri VI no 47, Penjaringan Sari, Kec. Rungkut, Surabaya",
            "Penjaringan Asri VI no 20 (PS 2E / 09), Rungkut, Surabaya",
            "Perumahan YKP Penjaringan Sari II, Jalan Penjaringan Asri II Jalan Penjaringan Asri VI No.1, Penjaringan Sari  rumah pojok depan masjid",
            "Perum PS 2 Jl. Penjaringan Asri IV Blok C no. 34 Kel. Penjaringan Asri ",
            "Wisata Semanggi Cluster Avicennia, Wisata Semanggi, Cluster Avicennia, Wonorejo  Blok P26",
            "Jalan Penjaringan Asri IV No 30, RW 7, Penjaringan Sari Kel , Rungkut (Blok A-15)",
            "Mbok Duren, Jalan Penjaringan Asri IV No.24, Penjaringan Sari  jl.penjaringan asri lv no. 24",
            ", Jl. Penjaringan Asri IV No.25 A, Penjaringan Sari, Kota Surabaya, Jawa Timur, Indonesia ",
            "JL PENJARINGAN SARI IIA/33 SURABAYA",
            "Wisata Semanggi Blok M1 No. 49 ",
            "Jalan Pandugo Sari X, PS 2 blok i 9  masuk perumahan ykp depan graha ykp",
            "Wisata Semanggi Mangrove Blok E1 no 11 (BUKAN GREEN SEMANGGI)",
            "Jalan Pandugo Sari VII  blok L NO 34",
            "Surabaya Rungkut Perumahan ykp penjaringan sari 2 jalan pandugo sari Vlll blok penjaringan sari Rungkut ( blok L no 32",
            "Perumahan Wisata Semanggi Mangrove Blok E1 no 11 (sebelas)",
            "jalan pandugo sari VIII blok L/32 (opsi penerima atas nama  adinda tri ayu)",
            "Jalan Medayu Utara XVI No 10, Medokan Ayu",
            "Jl. Pandugo Sari X Blok K No.33  Nomor 25",
            "Perum Wisata Semanggi Blok C No4 ",
            "PERUMAHAN WISATA SEMANGGI BLOK C48, CLUSTER RHIZOPHORA, WONOREJO",
            "Perumahan wisata semanggi mangrove h1-7 (H1 no 7)",
            "Wisata Semanggi F1 no 3",
            "Jl. Raya Pandugo No.52  Warung bakso tenes MU",
            "Toko abadi jaya, Jalan raya pandugo 101, rungkut, surabaya",
            "Toko abadi jaya, Jalan raya pandugo 101, rungkut, surabaya",
            "PERUMAHAN WISATA SEMANGGI, Jl. Wonorejo Indah Timur, Wonorejo, Rungkut Surabaya, Jawa Timur, Indonesia  blok F1 NO 1",
            "Medayu Utara XVII no. 04  Bengkel Mobil pak MuL",
            "Perumahan Wisata Semanggi, Blok A1 No 33, RT 6/RW 7, Kelurahan Wonorejo, Rungkut",
            "pandugo timur IV / B 23(rumah pojok)",
            "pandugo timur 6/6.puri indah B-34  depan graha YKP",
            "jl. medayu utara 17 raya, no 68.  depan bengkel motor gass yang sebelahnya makam, .",
            "Jl. Medayu Utara No.14 ",
            "Wisata Semanggi Blok A No. 76",
            "medayu utara gang IV no 57 kavling 61A ",
            "Perumhan Wisata Semanggi, Blok A No 50, RT 6/RW 7, Wonorejo, Rungkut",
            "Jl Medayu utara gang IV/No.43(Jl Medayu Utara gang IV/No.43, Rt 4 Rw 9)",
            "Jl Medayu utara gang IV/No.43(Jl Medayu Utara gang IV/No.43, Rt 4 Rw 9)",
            "Permahan wisata semanggi mangrove blok a no 38  Perumahan wisata semanggi mangrove blok A no 38 ",
            "Medayu Utara IV kav.89 No.37",
            "perumahan puri indah jl pandugo timur VIII blok C No:43 rungkut,kota Surabaya jawa timur ",
            "jl. Pandugo Timur III/3. Blok A/54",
            "YKP Pandugo 2 Blok T no 5, Jalan Pandugo Timur XII, Penjaringan Sari ",
        ];
        
        foreach ($addresses as $address) {
            echo $this->normalizeAddress($address) . "dxdx";
        }
    }
    
}
