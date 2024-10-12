@extends('layouts.app')

@push('styles')
@endpush

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">Create Package</p>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-7">
            <div class="card custom-card card-body">
                <form action="{{ route('admin.package.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @include('admin.services._form')
                </form>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card">
                <div class="card-body">
                    <h5>Streamers</h5>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    @include('admin.services.scritps._load-table')
    @include('admin.services.scritps._submit-form')
    @include('admin.services.scritps._actions')
@endpush
