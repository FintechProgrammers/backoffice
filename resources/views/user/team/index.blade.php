@extends('layouts.user.app')

@section('title', 'Team')

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">Customer Downline</p>
        </div>
    </div>
    <div id="content"></div>
    <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
@endsection
@push('scripts')
    @include('user.team.scripts._load-table')
@endpush
