@extends('layouts.main')
@section('container')

<div class="container">
    <div class="row justify-content-center my-5">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-body">
                    <h2 class="text-center mb-4">Login</h2>
                    <form action="/login" method="POST">
                        @csrf
                        @if (session('loginError'))
                            <div class="alert alert-danger">
                                {{ session('loginError') }}
                            </div>
                        @endif
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        <div class="form-group mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input name="username" type="text" class="form-control" id="username" placeholder="Enter username" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input name="password" type="password" class="form-control" id="password" placeholder="Password" required>
                        </div>
                        <div class="form-group mb-3 form-check">
                            <input type="checkbox" class="form-check-input" onclick="showPassword()" id="remember"> Show Password
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary text-center">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>    
</div>

@endsection