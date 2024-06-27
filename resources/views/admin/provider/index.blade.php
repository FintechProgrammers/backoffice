@extends('layouts.app')

@push('styles')

@endpush

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">Manage Providers</p>
        </div>
        {{-- <div class="btn-list mt-md-0 mt-2">
            <button type="button" class="btn btn-primary btn-wave trigerModal"
                data-url="{{ route('admin.provider.create') }}" data-bs-toggle="modal" data-bs-target="#primaryModal">
                <i class="las la-user-plus me-2 align-middle d-inline-block"></i>Create Provider
            </button>
        </div> --}}
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-body">
                    <table class="table table-bordered text-nowrap w-100">
                        {{-- id="scroll-vertical" --}}
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th class="text-center">Handles Payin</th>
                                <th class="text-center">Handles Payout</th>
                                <th class="text-center">Handles Crypto</th>
                                <th class="text-center">Status</th>
                                <th width="10">Action</th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    @include('admin.provider.scritps._load-table')
    @include('admin.provider.scritps._submit-form')
    @include('admin.provider.scritps._actions')
@endpush
