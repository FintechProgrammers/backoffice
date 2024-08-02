@extends('layouts.user.app')

@section('title', 'Sales')

@section('content')
    <div class="card custom-card">
        <div class="card-header justify-content-between">
            <div class="card-title">
                Sales History
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table text-nowrap table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">User</th>
                            <th scope="col">Account Type</th>
                            <th scope="col">Sponsor</th>
                            <th scope="col">Package</th>
                            <th scope="col">Amount</th>
                            <th scope="col">BV Point</th>
                            <th scope="col" width="30%">Date</th>
                        </tr>
                    </thead>
                    <tbody id="content">

                    </tbody>
                </table>
            </div>
        </div>
        <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
    </div>
@endsection
@push('scripts')
    @include('user.sales.scripts._load-table')
@endpush
