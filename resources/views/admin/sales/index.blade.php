@extends('layouts.app')

@push('styles')
@endpush

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">Sales</p>
        </div>
        <div class="btn-list mt-md-0 mt-2">
            @if (Auth::guard('admin')->user()->can('give commission'))
                <a href="{{ route('admin.commission.pay.create') }}" class="btn btn-primary btn-wave">
                    Pay Commission
                </a>
            @endif
        </div>
    </div>
    <div class="card custom-card">
        <div class="p-3">
            <div class="row align-items-end ">
                <div class="col-lg-3 mb-lg-0 mb-4">
                    <label for="searchInputSearch">Search User</label>
                    <input type="search" class="form-control" placeholder="Search by email,name and account type"
                        id="search" aria-describedby="emailHelp">
                </div>
                <div class="col-lg-3 mb-lg-0 mb-4">
                    <label for="searchInputSearch">Search Sponsor</label>
                    <input type="search" class="form-control" placeholder="Search by email,name and account type"
                        id="search_sponsor" aria-describedby="emailHelp">
                </div>
                <div class="col-lg-2 mb-lg-0 mb-4">
                    <label for="searchInputSearch">Date Joined</label>
                    <input type="text" name="datepicker" id="search-date" class="form-control" value="" />
                </div>
                <div class="col-lg-2 mb-lg-0">
                    <button id="filter"
                        class="btn btn-size btn-primary btn-hover-effect-1 rounded-pill make-text-bold w-100">Filter</button>
                </div>
                <div class="col-lg-2 mb-lg-0">
                    <button id="reset"
                        class="btn btn-size btn-outline-dark btn-hover-effect-1 rounded-pill make-text-bold w-100">Reset</button>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Sponsor</th>
                                    <th>Service Name</th>
                                    <th class="text-center">Amount</th>
                                    <th class="text-center">Date</th>
                                </tr>
                            </thead>
                            <tbody id="table-body">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
    </div>
@endsection
@push('scripts')
    @include('admin.sales.scritps._load-table')
    @include('admin.sales.scritps._submit-form')
@endpush
