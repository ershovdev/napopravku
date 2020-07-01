@extends('layouts.app')

@section('content')
    <h3>Welcome to your personal cloud storage!</h3>
    <a href="{{ route('login') }}">Sign In</a>
    or
    <a href="{{ route('register') }}">Sign Up</a>
    for using it.
@endsection
