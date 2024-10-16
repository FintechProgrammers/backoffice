@extends('layouts.app')

@push('styles')
@endpush

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">Subscriptions</p>
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
                                    <th>Service</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th class="text-center">Status</th>
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
    @include('admin.subscriptions.scritps._load-table')
@endpush
