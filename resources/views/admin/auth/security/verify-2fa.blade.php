@extends('layouts.auth')

@section('title', 'Verify 2fa')

@section('content')
    <form action="{{ route('admin.security.verify2fa.post') }}" method="POST">
        @csrf

        <p class="text-justify">
            To proceed, please enter the current OTP (One-Time Password) generated on your Authenticator App.
            Keep in mind that the password refreshes every 30 seconds, so make sure to submit the most recent one. Thank
            you.
        </p>

        <div class="mt-4">
            <label for="">One Time Password</label>
            <input type="text" class="form-control" name="one_time_password" placeholder="Enter OTP code">
        </div>

        <div class="form-group mt-4">
            <button type="submit" class="btn btn-lg btn-primary">Continue</button>
        </div>
    </form>
@endsection
