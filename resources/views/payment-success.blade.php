@extends('layouts.user.app')

@section('title', 'Stripe Success')

@section('content')
    <div class="p-5 checkout-payment-success my-3">

        <div class="mb-5">
            <h5 class="text-success fw-semibold">{{ $title }}</h5>
        </div>
        @if (Session::has('success') && Session::get('success'))
            <div class="me-2">
                <span class="avatar avatar-lg avatar-rounded">
                    <img src="{{ asset('assets/images/success.png') }}" alt="">
                </span>
            </div>
        @else
            <div class="me-2">
                <span class="avatar avatar-lg avatar-rounded">
                    <img src="{{ asset('assets/images/pending.png') }}" alt="">
                </span>
            </div>
        @endif

        @if (Session::has('message'))
            <div class="mb-4 d-flex flex-column align-items-center">
                <p class="mb-1 fs-14 w-25">
                    {{ Session::get('message') }}
                </p>
            </div>
        @endif
        <a href="{{ route('dashboard') }}" class="btn btn-success">Continue to Dashboard</a>
    </div>
@endsection
