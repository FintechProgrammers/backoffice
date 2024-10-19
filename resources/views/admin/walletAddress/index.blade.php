@extends('layouts.app')

@section('title', 'Wallet Addreses')

@push('styles')
@endpush

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">Wallet Addresses</p>
        </div>
    </div>
    <div class="card custom-card">
        <div class="p-3">
            <div class="row align-items-end ">
                <div class="col-lg-8 mb-lg-0 mb-4">
                    <label for="searchInputSearch">Search</label>
                    <input type="search" class="form-control" placeholder="Search by wallet address" id="search"
                        aria-describedby="emailHelp">
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
                <table class="table table-bordered text-nowrap w-100">
                    {{-- id="scroll-vertical" --}}
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Address</th>
                            <th>Date Join</th>
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
    @include('admin.walletAddress.scritps._load-table')
    @include('admin.walletAddress.scritps._user_actions')
@endpush
