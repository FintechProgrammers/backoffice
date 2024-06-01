@extends('layouts.user.app')

@section('title', 'Plans')

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">Services</p>
        </div>
    </div>
    <div class="card custom-card overflow-hidden shadow-none">
        <div class="card-body p-0">
            <form action="">
                @csrf
                <div class="row">
                    @foreach ($services as $item)
                        <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-4 col-sm-12 border-end border-inline-end-dashed">
                            <div class="p-4">
                                <div class="py-4 d-flex flex-column align-items-center text-center">
                                    <h6 class="fw-semibold text-center">{{ ucfirst($item->name) }}</h6>
                                    <div class="mt-3">
                                        <p class="fs-25 fw-semibold mb-0">${{ $item->price }}</p>
                                        <p class="text-muted fs-11 fw-semibold mb-0">
                                            {{ convertDaysToUnit($item->duration, $item->duration_unit) . ' ' . $item->duration_unit }}
                                        </p>
                                    </div>
                                </div>
                                <ul class="list-unstyled text-center fs-12 px-3 pt-3 mb-0">
                                    @foreach ($item->serviceProduct as $product)
                                        <li class="mb-3">
                                            <span class="text-muted">
                                                {{ ucfirst($product->product->name) }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="d-grid">
                                    <button class="btn btn-primary-light btn-wave">Get Started</button>
                                </div>
                            </div>
                        </div>
                    @endforeach


                </div>
            </form>
        </div>
    </div>
@endsection
