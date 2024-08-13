@extends('layouts.user.app')

@section('title', 'Package')

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">Packages</p>
        </div>
    </div>
    <div class="row">
        @forelse ($services as $item)
            <div class="col-xxl-3 col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12">
                <div class="card custom-card">
                    <div class="card-body p-3">
                        <div class="mb-3 overflow-hidden position-relative">
                            <a href="javascript:void(0);" class="stretched-link"></a>
                            <img src="{{ $item->image }}" alt="img" class="nft-img img-fluid">
                            <span class="nft-timer-container">
                                <i class="ti ti-bolt fs-14"></i>
                                <span
                                    class="nft-timer ms-1 fs-11">{{ convertDaysToUnit($item->duration, $item->duration_unit) . ' ' . $item->duration_unit }}</span>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap">
                            <div class="d-inline-flex align-items-start position-relative">
                                <a href="{{ route('package.details', $item->uuid) }}" class="stretched-link"></a>
                                <div class="flex-grow-1">
                                    <span class="mb-0 d-block fs-14 fw-semibold">{{ $item->name }}</span>
                                    <span class="fs-13 text-muted">
                                        <i class="bi bi-patch-check-fill text-success ms-1 fs-15"></i>
                                        @if ($item->ambassadorship)
                                            Ambassadorship
                                        @else
                                            @if ($item->serviceProduct->isNotEmpty())
                                                {{ $item->serviceProduct->pluck('product.name')->implode(', ') }}
                                            @else
                                                No products available.
                                            @endif
                                        @endif
                                    </span>
                                </div>
                            </div>
                            @if (auth()->user()->is_ambassador)
                                <a href="javascript:void(0);" class="btn rounded-circle btn-sm text-light copy-btn btn-dark"
                                    data-bs-toggle="tooltip" aria-label="Share package link"
                                    copy_value="{{ route('checkout.index') }}?amb={{ auth()->user()->uuid }}&service={{ $item->uuid }}"><i
                                        class="bx bx-share"></i>
                                </a>
                            @endif
                        </div>
                        <div class="d-flex align-items-end flex-wrap gap-2">
                            <div class="flex-fill">
                                <p class="fs-14 mb-1 text-muted">
                                    {{ convertDaysToUnit($item->duration, $item->duration_unit) . ' ' . $item->duration_unit }}
                                </p>
                                <div class="fs-16 mb-0 d-flex align-items-center fw-semibold">
                                    ${{ number_format($item->price, 2, '.', ',') }}
                                </div>
                            </div>
                            <a href="{{ route('package.details', $item->uuid) }}"
                                class="btn btn-primary-light btn-wave">{{ __('Purchase') }}</a>
                        </div>
                    </div>
                </div>
            </div>

        @empty
            <div class="col-lg-12">
                <x-no-datacomponent title="no package available" />
            </div>
        @endforelse

    </div>
@endsection
