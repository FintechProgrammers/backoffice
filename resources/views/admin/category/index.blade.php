@extends('layouts.app')

@section('title', 'Categories')

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">Categories</p>
        </div>
        <div class="btn-list mt-md-0 mt-2">
            @if (Auth::guard('admin')->user()->can('create category'))
                <button type="button" class="btn btn-primary btn-wave trigerModal"
                    data-url="{{ route('admin.category.create') }}" data-title="Create Category" data-bs-toggle="modal"
                    data-bs-target="#primaryModal">
                    Create Category
                </button>
            @endif
        </div>
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
                                <th>Type</th>
                                <th width="10"></th>
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
    @include('admin.category.scritps._load-table')
    @include('admin.category.scritps._actions')
    @include('admin.category.scritps._submit-form')
@endpush
