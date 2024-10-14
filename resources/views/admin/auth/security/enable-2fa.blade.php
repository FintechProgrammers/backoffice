@extends('layouts.auth')

@section('title', 'Enable 2fa')

@section('content')
    <form action="{{ route('admin.security.enable-2fa.post', $admin->uuid) }}" method="POST">
        @csrf
        <p class="text-justify">
            You can set up your two-factor authentication by either scanning the barcode below or using the code:
            <strong>{{ $secret }}</strong>.
            Choose the method that is more convenient for you.
        </p>

        <div class="d-flex justify-content-center mb-3">
            {!! $QR_Image !!}
        </div>

        <p class="text-justify">
            In order to proceed, it is essential to set up your Google Authenticator app.
            Without completing this setup, you will be unable to log in.
        </p>

        <div class="mt-4">
            <label for="">One Time Password.</label>
            <input type="text" class="form-control" name="one_time_password" id=""
                placeholder="Enter OTP from Your authenticator app">
        </div>

        <div class="form-group mt-4">
            <input type="hidden" name="google2fa_secret" value="{{ $secret }}">

            <button type="submit" class="btn btn-lg btn-primary">Continue</button>
        </div>
    </form>
@endsection
