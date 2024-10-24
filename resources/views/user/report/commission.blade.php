@extends('layouts.user.app')

@section('title', 'Commission History')

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">Commissions</p>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="card custom-card card-bg-primary">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-top justify-content-between">
                        <div class="flex-fill">
                            <p class="mb-0 text-light">Total Commission</p>
                            <div class="d-flex align-items-center">
                                <span class="fs-5 fw-semibold" id="totalCommission">
                                    0.00
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="p-3">
                    <div class="row align-items-end ">
                        <div class="col-lg-3 mb-lg-0 mb-4">
                            <label for="searchInputSearch">Search</label>
                            <input type="search" class="form-control" placeholder="Search by email,name and account type"
                                id="search" aria-describedby="emailHelp">
                        </div>
                        <div class="col-lg-2 mb-lg-0 mb-4">
                            <label for="searchInputSearch">Status</label>
                            <select class="form-control" id="status">
                                <option value="">--select--</option>
                                <option value="1">Settled</option>
                                <option value="0">Pending</option>
                            </select>
                        </div>
                        <div class="col-lg-2 mb-lg-0 mb-4">
                            <label for="searchInputSearch">Level</label>
                            <select class="form-control" id="level">
                                <option value="">--select--</option>
                                <option value="direct">Direct</option>
                                <option value="indirect">Indirect</option>
                            </select>
                        </div>
                        <div class="col-lg-3 mb-lg-0 mb-4">
                            <label for="searchInputSearch">Date</label>
                            <input type="text" name="datepicker" id="search-date" class="form-control" value="" />
                        </div>
                        <div class="col-lg-1 mb-lg-0">
                            <button id="filter"
                                class="btn btn-size btn-primary btn-hover-effect-1 rounded-pill make-text-bold w-100">Filter</button>
                        </div>
                        <div class="col-lg-1 mb-lg-0">
                            <button id="reset"
                                class="btn btn-size btn-outline-dark btn-hover-effect-1 rounded-pill make-text-bold w-100">Reset</button>
                        </div>
                    </div>
                </div>
                <hr />
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="file-export" class="table table-bordered table-striped text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th>Assocaite</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Status</th>
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
    @include('user.report.script._load-table')
@endpush
