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
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th>User</th>
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
