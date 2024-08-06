@extends('layouts.user.app')

@section('title', 'Withdrawal')

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">{{ __('Withdrawals') }}</p>
        </div>
        <div class="btn-list mt-md-0 mt-2">
            <a type="button" class="btn btn-primary btn-wave"
                href="{{ route('withdrawal.create') }}">{{ __('Make Withdrawal') }}
            </a>
        </div>
    </div>
    <div class="card custom-card">
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
    </div>
@endsection
@push('scripts')
    @include('user.withdrawal.scripts._load-table')
    @include('user.withdrawal.scripts._submit_form')
@endpush
