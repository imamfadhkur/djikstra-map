@extends('layouts.main')
@section('container')

<div class="wrapper d-flex align-items-stretch">
  @include('partials.sidebar')
  <div id="content" class="p-md-3">
    @include('partials.navbar_dashboard')


    <div class="container my-4">
      <div class="row">
        <div class="col">
          <h2>Welcome {{ Auth::user()->name }}!</h2>
        </div>
      </div>
      <div class="row">
        <div class="col">
          {{-- your code --}}
        </div>
      </div>
    </div>

  </div>
</div>

@endsection