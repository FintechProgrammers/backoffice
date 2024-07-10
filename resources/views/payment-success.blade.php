@extends('layouts.auth')

@section('title', 'Stripe Success')

@section('content')
    <div class="p-5 checkout-payment-success my-3">
        <div class="mb-3">
            <h5 class="text-success fw-semibold">Successful Payment</h5>
        </div>
        <div class="me-2">
            <span class="avatar avatar-lg avatar-rounded">
                <img src="{{ asset('assets/images/success.png') }}" alt="">
            </span>
        </div>

        <div class="mb-4 d-flex flex-column align-items-center">
            <p class="mb-1 fs-14 ">
                Your Payment has been executed successfully.
            </p>
        </div>
        @if (auth()->check())
            <a href="{{ route('dashboard') }}" class="btn btn-success">Continue to Dashboard</a>
        @else
            <a href="{{ route('login') }}" class="btn btn-success">Login</a>
        @endif
    </div>
@endsection
