@extends('layouts.user.app')

@push('scripts')
@endpush

@section('title', 'Dashboard')

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">Welcome back, {{ ucfirst(auth()->user()->name) }}!</p>
            <span class="fs-semibold text-muted">Track your sales activity, leads and deals here.</span>
        </div>
    </div>

    <div class="row">
        @include('user.dashboard._rank')
        @include('user.dashboard._package')
        @include('user.dashboard._account')
    </div>
    <div class="col-xxl-6 col-xl-6 col-lg-12">
        <x-user.dashboard.stats-component />
        @include('user.dashboard._revenue-stats')
        @include('user.dashboard._purchases')
    </div>
    <div class="col-xxl-3 col-xl-3 col-lg-12">
        @include('user.dashboard._profile-card')
        @include('user.dashboard._activities')
    </div>
    @include('profile.partials._profile-modal')
@endsection
@push('scripts')
    @if (Auth::user()->profile_completion_percentage !== 100)
        <script>
            showProfileModal()
        </script>
    @endif
    <script>
        showProfileModal()
        function showProfileModal() {
            var myModal = new bootstrap.Modal(document.getElementById('profileUpdateModal'), {
                keyboard: false
            });
            myModal.show();
        }
    </script>
@endpush
