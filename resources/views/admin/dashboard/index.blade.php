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
        <div class="col-xxl-6 col-xl-6">
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
                                        <th scope="col">Product Name</th>
                                        <th scope="col">Category</th>
                                        <th scope="col">Stock</th>
                                        <th scope="col">Total Sales</th>
                                    </tr>
                                </thead>
                                <tbody class="top-selling">
                                    <tr>
                                        <td class="text-center lh-1">
                                            <span class="avatar avatar-md">
                                                <img src="../assets/images/ecommerce/png/36.png"
                                                    class="p-2 rounded-pill bg-light" alt="">
                                            </span>
                                        </td>
                                        <td>Ethnic School bag for children (24L)</td>
                                        <td>Bags</td>
                                        <td><span class="badge badge-sm bg-success-transparent text-success">In Stock</span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold">5,093</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center lh-1">
                                            <span class="avatar avatar-md">
                                                <img src="../assets/images/ecommerce/png/38.png"
                                                    class="p-2 rounded-pill bg-light" alt="">
                                            </span>
                                        </td>
                                        <td>Leather jacket for men (S,M,L,XL)</td>
                                        <td>Clothing</td>
                                        <td><span class="badge badge-sm bg-success-transparent text-success">In Stock</span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold">6,890</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center lh-1">
                                            <span class="avatar avatar-md">
                                                <img src="../assets/images/ecommerce/png/39.png"
                                                    class="p-2 rounded-pill bg-light" alt="">
                                            </span>
                                        </td>
                                        <td>Childrens Teddy toy of high quality</td>
                                        <td>Toys</td>
                                        <td><span class="badge badge-sm bg-danger-transparent text-danger">Out Of
                                                Stock</span></td>
                                        <td>
                                            <span class="fw-semibold">5,423</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center lh-1">
                                            <span class="avatar avatar-md">
                                                <img src="../assets/images/ecommerce/png/40.png"
                                                    class="p-2 rounded-pill bg-light" alt="">
                                            </span>
                                        </td>
                                        <td>Orange smart watch with square dial (24mm)</td>
                                        <td>Fashion</td>
                                        <td><span class="badge badge-sm bg-danger-transparent text-danger">Out Of
                                                Stock</span></td>
                                        <td>
                                            <span class="fw-semibold">10,234</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-6 col-xl-6">
            <div class="card custom-card">
                <div class="card-header justify-content-between">
                    <div class="card-title">Earnings</div>
                    <div class="dropdown">
                        <a href="javascript:void(0);" class="p-2 fs-12 text-muted" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            View All<i class="ri-arrow-down-s-line align-middle ms-1 d-inline-block"></i>
                        </a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a class="dropdown-item" href="javascript:void(0);">Download</a></li>
                            <li><a class="dropdown-item" href="javascript:void(0);">Import</a></li>
                            <li><a class="dropdown-item" href="javascript:void(0);">Export</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row ps-lg-5 mb-4 pb-4 gy-sm-0 gy-3">
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4">
                            <div class="mb-1 earning first-half ms-3">First Half</div>
                            <div class="mb-0">
                                <span class="mt-1 fs-16 fw-semibold">$51.94k</span>
                                <span class="text-success"><i class="fa fa-caret-up mx-1"></i>
                                    <span
                                        class="badge bg-success-transparent text-success px-1 py-2 fs-10">+0.9%</span></span>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4">
                            <div class="mb-1 earning top-gross ms-3">Top Gross</div>
                            <div class="mb-0">
                                <span class="mt-1 fs-16 fw-semibold">$18.32k</span>
                                <span class="text-success"><i class="fa fa-caret-up mx-1"></i>
                                    <span
                                        class="badge bg-success-transparent text-success px-1 py-2 fs-10">+0.39%</span></span>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4">
                            <div class="mb-1 earning second-half ms-3">Second Half</div>
                            <div class="mb-0">
                                <span class="mt-1 fs-16 fw-semibold">$38k</span>
                                <span class="text-danger"><i class="fa fa-caret-up mx-1"></i>
                                    <span
                                        class="badge bg-danger-transparent text-danger px-1 py-2 fs-10">-0.15%</span></span>
                            </div>
                        </div>
                    </div>
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
    @include('admin.dashboard.scripts._user-by_country-map')
@endpush
