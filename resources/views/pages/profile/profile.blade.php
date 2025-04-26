@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1>Profile Page</h1>
        <p>Welcome to your profile page!</p>
    </div>
    <div class="container mt-4">
        <h2>Your Information</h2>
        <ul class="list-group">
            <li class="list-group-item">Name: {{ Auth::user()->name}}</li>
            <li class="list-group-item">Email: {{Auth::user()->email}}</li>
            <li class="list-group-item">Created At: {{Auth::user()->created_at}}</li>
            <li class="list-group-item">Updated At: {{ $user->updated_at }}</li>
        </ul>
        <a href="#" class="btn btn-primary mt-3">Edit Profile</a>
    </div>

@endsection
