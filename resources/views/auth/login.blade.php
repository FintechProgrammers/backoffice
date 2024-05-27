@extends('layouts.auth')

@section('content')
    <p class="h5 fw-semibold mb-2 text-center">Sign In</p>
    <form action="{{ route('login') }}" method="POST">
        @csrf
        <div class="row gy-3">
            <div class="col-xl-12">
                <label for="signin-username" class="form-label text-default">User Name</label>
                <input type="text" class="form-control form-control-lg" name="username" id="signin-username"
                    placeholder="user name">
                @error('username')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="col-xl-12 mb-2">
                <label for="signin-password" class="form-label text-default d-block">Password
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                            class="float-end text-danger">{{ __('Forgot your password?') }}</a>
                </label>
                @endif
                <div class="input-group">
                    <input type="password" class="form-control form-control-lg" id="signin-password" placeholder="password">
                    <button class="btn btn-light" type="button" onclick="createpassword('signin-password',this)"
                        id="button-addon2"><i class="ri-eye-off-line align-middle"></i></button>
                </div>
                <div class="mt-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
                        <label class="form-check-label text-muted fw-normal" for="defaultCheck1">
                            {{ __('Remember me') }}
                        </label>
                    </div>
                </div>
                @error('password')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="col-xl-12 d-grid mt-2">
                <button type="submit" class="btn btn-lg btn-primary">    {{ __('Log in') }}</button>
            </div>
        </div>
    </form>
@endsection
