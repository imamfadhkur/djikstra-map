<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>data View</title>
    <style>
        table {
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            text-align: center;
            padding: 2px;
        }
    </style>
</head>
<body>
    <table id="dataTable">
        <tr>
            <th colspan="{{ count($data)+1 }}">Data Shortest Path dalam Kilometer</th>
        </tr>
        <tr>
            <th></th>
            @php
                $x = 0;
                $key = [];
                $data2 = $data;
            @endphp
            @foreach ($data as $i => $array)
                <th>{{ $i }}</th>
                @php
                    $x++;
                    $key[] = $i;
                @endphp
            @endforeach
        </tr>
        @php
            $y = 0;
        @endphp
        @foreach ($data as $i => $array)
            <tr>
                <th style="padding-right: 5px; padding-left: 5px">{{ $i }}</th>
                @php
                    $y++;
                @endphp
                @php
                    $arrayValues = array_values($array); // Mengambil nilai array sebagai daftar numerik
                    $totalItems = count($arrayValues);
                    $foundZero = false; // Variabel untuk melacak apakah 0 telah ditemukan
                @endphp
                
                @foreach ($arrayValues as $j => $item)
                    @php
                        $nextItem = ($j < $totalItems - 1) ? $arrayValues[$j + 1] : null; // Ambil elemen berikutnya atau null jika tidak ada
                        if ($item == 0) {
                            $foundZero = true; // Jika item saat ini adalah 0, tandai bahwa 0 telah ditemukan
                        }
                    @endphp
                    <td style="background-color: {{ $foundZero ? 'yellow' : ($nextItem === 0 ? 'rgb(55, 255, 55)' : 'white') }};">{{ $item }}</td>
                @endforeach
            </tr>
        @endforeach
    </table>
    <br>
    @php
        $j = 0;
        $data3 = $data;
        sort($data3);
        // print_r("<pre>");
        // print_r($data3);
        // print_r("</pre>");
        $sor = [];
    @endphp
    @foreach ($data3[0] as $key => $value)
        {{ $j." => ".$key }} <br>
        @php
        $sor[$j] = $key;
            $j++;
        @endphp
    @endforeach
    @php
        print_r("<pre>");
        sort($sor);
        print_r($sor);
        print_r("</pre>");
    @endphp
    <br>
    {{-- <table>
        <tr>
            <td>
                @php
                    print_r("<pre>");
                    // ksort($data);
                    print_r($data);
                    print_r("</pre>");
                @endphp
            </td>
            <td>
                @php
                    print_r("<pre>");
                    ksort($data);
                    print_r($data);
                    print_r("</pre>");
                @endphp
            </td>
            <td>
                @php
                    foreach ($data as &$subArray) {
                        ksort($subArray);
                    }
                    print_r("<pre>");
                    print_r($data);
                    print_r("</pre>");
                @endphp
            </td>
        </tr>
    </table> --}}
    {{-- <table>
        <tr>
            <th colspan="{{ count($data)+1 }}">Data Shortest Path dalam Kilometer</th>
        </tr>
        <tr>
            <th>0</th>
            @php
                $x = 0;
                $key = [];
            @endphp
            @foreach ($data as $i => $array)
                <th>{{ $x }}</th>
                @php
                    $x++;
                    $key[] = $i;
                @endphp
            @endforeach
        </tr>
        @php
            $y = 0;
        @endphp
        @foreach ($data as $i => $array)
            <tr>
                <th style="padding-right: 5px; padding-left: 5px">{{ $y }}</th>
                @php
                    $y++;
                @endphp
                @php
                    $arrayValues = array_values($array); // Mengambil nilai array sebagai daftar numerik
                    $totalItems = count($arrayValues);
                    $foundZero = false; // Variabel untuk melacak apakah 0 telah ditemukan
                @endphp
                
                @foreach ($arrayValues as $j => $item)
                    @php
                        $nextItem = ($j < $totalItems - 1) ? $arrayValues[$j + 1] : null; // Ambil elemen berikutnya atau null jika tidak ada
                        if ($item == 0) {
                            $foundZero = true; // Jika item saat ini adalah 0, tandai bahwa 0 telah ditemukan
                        }
                    @endphp
                    <td style="background-color: {{ $foundZero ? 'yellow' : ($nextItem === 0 ? 'rgb(55, 255, 55)' : 'white') }};">{{ $item }}</td>
                @endforeach
            </tr>
        @endforeach
    </table> --}}
    <br>
    <table>
        <tr>
            <td>No.</td>
            <td>Alamat</td>
            <td>Kode</td>
            <td>Latitude</td>
            <td>Longitude</td>
        </tr>
        @foreach ($alamats as $key => $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ "Alamat ".$loop->iteration }}</td>
                <td>{{ ($loop->iteration)-1 }}</td>
                <td>{{ $item["lat"] }}</td>
                <td>{{ $item["lng"] }}</td>
            </tr>
        @endforeach
    </table>
    <p>
        {{-- @php
            print_r("<pre>");
            print_r($data2);
            print_r("</pre>");
        @endphp --}}
    </p>
    <p>Total nilai dengan background rgb(55, 255, 55): <span id="totalValue"></span></p>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var table = document.getElementById('dataTable');
            var cells = table.getElementsByTagName('td');
            var total = 0;

            for (var i = 0; i < cells.length; i++) {
                var cell = cells[i];
                var backgroundColor = window.getComputedStyle(cell).getPropertyValue('background-color');
                
                if (backgroundColor === 'rgb(55, 255, 55)') {
                    var cellValue = parseFloat(cell.textContent.trim());
                    total += isNaN(cellValue) ? 0 : cellValue;
                }
            }

            document.getElementById('totalValue').textContent = total.toFixed(2);
        });
    </script>
</body>
</html>
