@extends('layouts.user.app')

@section('title', 'Customers')

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">Customers</p>
        </div>
    </div>
    {{-- <div id="content"></div> --}}
    <div class="card custom-card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-nowrap w-100">
                    {{-- id="scroll-vertical" --}}
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Sponsor</th>
                            {{-- <th>Curent Package</th> --}}
                            <th>Date Join</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="content">
                    </tbody>
                </table>
            </div>
        </div>
        <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
    </div>
    @include('user.team._user-details-modal')
@endsection
@push('scripts')
    @include('user.customers.scripts._load-table')
    @include('user.team.scripts._user-details')
@endpush
