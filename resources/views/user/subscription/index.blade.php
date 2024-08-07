@extends('layouts.user.app')

@section('title', 'Subscriptions')

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">Subscriptions</p>
        </div>
    </div>
    <x-user-subscription />
@endsection
