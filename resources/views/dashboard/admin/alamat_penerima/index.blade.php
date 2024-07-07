@extends('layouts.main')
@section('container')

<div class="wrapper d-flex align-items-stretch">
  @include('partials.sidebar')
  <div id="content" class="p-md-3">
    @include('partials.navbar_dashboard')


    <div class="container my-4">
      <div class="row">
        <div class="col">
          <a href="#" class="btn btn-primary">tambah data</a>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <table class="table table-bordered table-hover mt-4">
            <tr>
              <th class="table-dark">no.</th>
              <th class="table-dark">kurir</th>
              <th class="table-dark">alamat penerima</th>
              <th class="table-dark">koordinat <br> ((lat) - (lng))</th>
              <th class="table-dark">action</th>
            </tr>
            @foreach ($alamat_penerimas as $ap)
              <tr>
                <td>{{ $loop->iteration + ($alamat_penerimas->perPage() * ($alamat_penerimas->currentPage() - 1)) }}</td>
                <td>{{ $ap->user->name }}</td>
                <td>{{ $ap->alamat_penerima }}</td>
                <td>{{ "(".$ap->latitude.") - (".$ap->longitude.")" }}</td>
                <td>
                  <a href="#" class="btn btn-sm btn-primary m-1"><i class="bi bi-eye"></i></a>
                  <a href="#" class="btn btn-sm btn-warning m-1"><i class="bi bi-pencil"></i></a>
                  <a href="#" class="btn btn-sm btn-danger m-1"><i class="bi bi-trash"></i></a>
                </td>
              </tr>
            @endforeach
          </table>
          <div class="d-flex justify-content-end">
            {{ $alamat_penerimas->withQueryString()->links() }}
        </div>
        </div>
      </div>
    </div>

  </div>
</div>

@endsection