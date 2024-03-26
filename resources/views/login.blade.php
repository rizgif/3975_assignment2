@extends('layouts.master') {{-- Make sure you have a layout file --}}

@section('content')
<div class="container text-center">
    @if (!session('email'))
        <button class="btn btn-primary" onclick="window.location.href = '{{ route('login') }}'">Login</button>
        <button class="btn btn-success" onclick="window.location.href = '{{ route('register') }}'">Register</button>
    @else
        <div class="text-center">
            <h4>Logged in as {{ session('email') }}</h4>
            {{-- Adapt the rest of your PHP logic here using Blade syntax --}}
        </div>
    @endif
</div>
@endsection
