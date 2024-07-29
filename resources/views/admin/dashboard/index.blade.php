@extends('layouts.app')

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">Welcome back, {{ Auth::guard('admin')->user()->name }} !</p>
        </div>
    </div>
    <div class="row">
        @foreach ($stats as $item)
            <div class="col-lg-4">
                @include('admin.dashboard._stats')
            </div>
        @endforeach
    </div>
    <div class="row">
        <div class="col-xxl-12 col-xl-12">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">Top Selling Products</div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table text-nowrap mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-center">S.no</th>
                                        <th scope="col">Service Name</th>
                                        <th scope="col" class="text-center">Price</th>
                                        <th scope="col" class="text-center">Amount</th>
                                        <th scope="col" class="text-center">BV</th>
                                    </tr>
                                </thead>
                                <tbody class="top-selling">
                                    @if (count($topSellingServices) > 0)
                                        @foreach ($topSellingServices as $key => $item)
                                            <tr>
                                                <td class="text-center">{{ $key + 1 }}</td>
                                                <td>
                                                    <x-package-title title="{{ $item->name }}" image="{{ $item->image }}"
                                                        price="{{ $item->price }}" />
                                                </td>
                                                <td class="text-center">
                                                    <span class="fw-semibold">${{ number_format($item->price, 2) }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span
                                                        class="fw-semibold">${{ number_format($item->totalSales, 2) }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="fw-semibold">{{ number_format($item->bv_amount, 2) }}
                                                        BV</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4">No top-selling services found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Active User</div>
                </div>
                <div class="card-body">
                    <div id="activeUsersChart"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">inactive Users</div>
                </div>
                <div class="card-body">
                    <div id="inactiveUsersChart"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Ambassadors</div>
                </div>
                <div class="card-body">
                    <div id="ambassadorsC"></div>
                </div>
            </div>
        </div>
        <div class="col-xxl-6 col-xl-6">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Sales Per Service</div>
                </div>
                <div class="card-body">
                    <div id="sales-per-service"></div>
                </div>
            </div>
        </div>
        <div class="col-xxl-6 col-xl-6">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Sales</div>
                </div>
                <div class="card-body">
                    <div id="earnings"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        Users By Country
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between mb-5">
                        <div class="me-5 d-flex align-items-center">
                            <div class="me-2">
                                <span class="avatar avatar-rounded bg-primary-transparent text-primary"><i
                                        class="ri-user-3-line fs-16"></i></span>
                            </div>
                            <div class="flex-fill">
                                <p class="fs-18 mb-0 text-primary fw-semibold">25,350</p>
                                <span class="text-muted fs-12">This month</span>
                            </div>
                        </div>
                        <div class="me-3 d-flex align-items-center">
                            <div class="me-2">
                                <span class="avatar avatar-rounded bg-light text-default"><i
                                        class="ri-user-3-line fs-16"></i></span>
                            </div>
                            <div class="flex-fill">
                                <p class="fs-18 mb-0 fw-semibold">19,200</p>
                                <span class="text-muted fs-12">Last month</span>
                            </div>
                        </div>
                        <div class="me-3 d-flex align-items-center">
                            <div class="me-2">
                                <span class="avatar avatar-rounded bg-success-transparent"><i
                                        class="ri-user-3-line fs-16"></i></span>
                            </div>
                            <div class="flex-fill">
                                <p class="fs-18 mb-0 text-success fw-semibold">1,24,890</p>
                                <span class="text-muted fs-12">This Year</span>
                            </div>
                        </div>
                        <div class="me-3 d-flex align-items-center">
                            <div class="me-2">
                                <span class="avatar avatar-rounded bg-secondary-transparent"><i
                                        class="ri-user-3-line fs-16"></i></span>
                            </div>
                            <div class="flex-fill">
                                <p class="fs-18 mb-0 text-secondary fw-semibold">97,799</p>
                                <span class="text-muted fs-12">Last Year</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-5">
                            <div class="h-100 my-auto">
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                                            <div><i
                                                    class="ri-checkbox-blank-circle-fill text-primary fs-8 me-1 align-middle d-inline-block"></i>Brazil
                                            </div>
                                            <div>1,290</div>
                                            <div class="text-success"><i
                                                    class="ri-arrow-up-s-line align-middle me-1 d-inline-block"></i>2.90%
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                                            <div><i
                                                    class="ri-checkbox-blank-circle-fill text-secondary fs-8 me-1 align-middle d-inline-block"></i>Greenland
                                            </div>
                                            <div>2,596</div>
                                            <div class="text-danger"><i
                                                    class="ri-arrow-down-s-line align-middle me-1 d-inline-block"></i>1.1%
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                                            <div><i
                                                    class="ri-checkbox-blank-circle-fill text-success fs-8 me-1 align-middle d-inline-block"></i>Russia
                                            </div>
                                            <div>3,710</div>
                                            <div class="text-success"><i
                                                    class="ri-arrow-up-s-line align-middle me-1 d-inline-block"></i>0.8%
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                                            <div><i
                                                    class="ri-checkbox-blank-circle-fill text-warning fs-8 me-1 align-middle d-inline-block"></i>Palestine
                                            </div>
                                            <div>1,116</div>
                                            <div class="text-danger"><i
                                                    class="ri-arrow-up-s-line align-middle me-1 d-inline-block"></i>10.06%
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                                            <div><i
                                                    class="ri-checkbox-blank-circle-fill text-danger fs-8 me-1 align-middle d-inline-block"></i>Canada
                                            </div>
                                            <div>12,150</div>
                                            <div class="text-success"><i
                                                    class="ri-arrow-up-s-line align-middle me-1 d-inline-block"></i>9.05%
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-xl-7">
                            <div id="users-map"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <!-- Apex Charts JS -->
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jsvectormap/js/jsvectormap.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jsvectormap/maps/world-merc.js') }}"></script>

    @include('admin.dashboard.scripts._earning-charts')
    @include('admin.dashboard.scripts._sales-per-service')
    @include('admin.dashboard.scripts._avaerage_sub')
    @include('admin.dashboard.scripts._user-by_country-map')
@endpush
