@extends('layouts.user.app')

@section('title', 'Wallet')

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">{{ __('Wallet') }}</p>
        </div>
        <div class="btn-list mt-md-0 mt-2">
            <a type="button" class="btn btn-primary btn-wave" href="{{ route('wallet.create') }}">{{ __('Make Withdrawal') }}
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4">
            <div class="card custom-card card-bg-primary text-fixed-white">
                <div class="card-body p-0">
                    <div class="d-flex align-items-top p-4 flex-wrap">
                        <div class="me-3 lh-1">
                            <span class="avatar avatar-md avatar-rounded bg-white text-primary shadow-sm">
                                <i class="ri-wallet-2-line fs-18"></i>
                            </span>
                        </div>
                        <div class="flex-fill">
                            <h5 class="fw-semibold mb-1 text-fixed-white">
                                ${{ !empty(Auth::user()->wallet) ? number_format(Auth::user()->wallet->balance, 2, '.', ',') : number_format(0.0, 2, '.', ',') }}
                            </h5>
                            <p class="op-7 mb-0 fs-12">Wallet Balance</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card custom-card card-bg-dark text-fixed-white">
                <div class="card-body p-0">
                    <div class="d-flex align-items-top p-4 flex-wrap">
                        <div class="me-3 lh-1">
                            <span class="avatar avatar-md avatar-rounded bg-white text-primary shadow-sm">
                                <i class="ri-wallet-2-line fs-18"></i>
                            </span>
                        </div>
                        <div class="flex-fill">
                            <h5 class="fw-semibold mb-1 text-fixed-white">
                                ${{ number_format(Auth::user()->total_earnings, 2, '.', ',') }}</h5>
                            <p class="op-7 mb-0 fs-12">Lifetime Earnings</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card custom-card card-bg-danger text-fixed-white">
                <div class="card-body p-0">
                    <div class="d-flex align-items-top p-4 flex-wrap">
                        <div class="me-3 lh-1">
                            <span class="avatar avatar-md avatar-rounded bg-white text-primary shadow-sm">
                                <i class="ri-wallet-2-line fs-18"></i>
                            </span>
                        </div>
                        <div class="flex-fill">
                            <h5 class="fw-semibold mb-1 text-fixed-white">${{ Auth::user()->totalWithdrawalsAmount }}</h5>
                            <p class="op-7 mb-0 fs-12">Total Withdrawals</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                            <option value="customer">Customer</option>
                            <option value="ambassador">Ambassador</option>
                        </select>
                    </div>
                    <div class="col-lg-2 mb-lg-0 mb-4">
                        <label for="searchInputSearch">Status</label>
                        <select class="form-control" id="status">
                            <option value="">--select--</option>
                            <option value="active">Active</option>
                            <option value="suspended">Suspended</option>
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
                                <th scope="col">Associated User</th>
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
    @endsection
    @push('scripts')
        @include('user.withdrawal.scripts._load-table')
        @include('user.withdrawal.scripts._submit_form')
    @endpush
