@extends('layouts.app')

@section('title', 'Users Management')

@push('styles')
@endpush

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">Streamers</p>
        </div>
        <div class="btn-list mt-md-0 mt-2">
            @if (Auth::guard('admin')->user()->can('create user'))
                <button type="button" class="btn btn-primary btn-wave trigerModal"
                    data-url="{{ route('admin.users.create') }}" data-bs-toggle="modal" data-bs-target="#primaryModal">
                    <i class="las la-user-plus me-2 align-middle d-inline-block"></i>Add Streamer
                </button>
            @endif
        </div>
    </div>
    <div class="card custom-card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-nowrap w-100">
                    {{-- id="scroll-vertical" --}}
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Username</th>
                            <th class="text-center">Account Type</th>
                            <th>Sponsor</th>
                            <th>Date Join</th>
                            <th>Status</th>
                            <th width="10">Action</th>
                        </tr>
                    </thead>
                    <tbody id="table-body">
                    </tbody>
                </table>
            </div>
        </div>
        <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
    </div>
@endsection
@push('scripts')
    <!-- Datatables Cdn -->

    @include('admin.users.scritps._load-table')
    @include('admin.users.scritps._submit-form')
    @include('admin.users.scritps._user_actions')
    @include('admin.users.scritps._select-plan')
@endpush
