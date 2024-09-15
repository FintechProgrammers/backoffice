@extends('layouts.app')

@push('styles')
@endpush

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">Transactions</p>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="p-3">
                    <div class="row align-items-end ">
                        <div class="col-lg-2 mb-lg-0 mb-4">
                            <label for="searchInputSearch">Search</label>
                            <input type="search" class="form-control" placeholder="Search by reference" id="search">
                        </div>
                        <div class="col-lg-2 mb-lg-0 mb-4">
                            <label for="searchInputSearch">Type</label>
                            <select class="form-control" id="type">
                                <option value="">--select--</option>
                                <option value="commission">Commission</option>
                                <option value="withdrawal">Withdrawal</option>
                                <option value="purchase">Purchase</option>
                            </select>
                        </div>
                        <div class="col-lg-2 mb-lg-0 mb-4">
                            <label for="searchInputSearch">Status</label>
                            <select class="form-control" id="status">
                                <option value="">--select--</option>
                                <option value="pending">Pending</option>
                                <option value="completed">Completed</option>
                                <option value="failed">Failed</option>
                            </select>
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
                <hr />
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table text-nowrap table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">Reference</th>
                                    <th scope="col">User</th>
                                    {{-- <th scope="col">Associated User</th> --}}
                                    <th scope="col">Amount</th>
                                    <th scope="col">Balance</th>
                                    <th scope="col">Action</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">status</th>
                                    <th scope="col" width="30%">Date</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody id="table-body">
                            </tbody>
                        </table>
                    </div>
                </div>
                <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    @include('admin.withdrawals.scritps._load-table')
@endpush
