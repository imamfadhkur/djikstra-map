<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>upload dokumen</title>
</head>
<body>
    
    @if (session('success'))
        <div style="margin-top: 16px; color: green;">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div style="margin-top: 16px; color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ url('get-lat-long') }}" method="post" enctype="multipart/form-data" style="margin: 24px">
        @csrf
        <label style="margin-top: 8px" for="address">file excel untuk dicari latitude dan longitude nya</label> <br>
        <input style="margin-top: 8px" type="file" name="address" id="address"> <br>
        <button style="margin-top: 8px" type="submit">Submit</button>
    </form>
</body>
</html>