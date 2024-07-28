@extends('layouts.user.app')

@section('title', 'Package History')

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Package history </h1>
    </div>
    <div class="container">
        @if (count($packages) > 0)
            <ul class="timeline list-unstyled">
                @foreach ($packages as $item)
                    <li>
                        <div class="timeline-time text-end">
                            <span class="date">{{ $item->created_at->format('jS,M Y') }}</span>
                            <span class="time d-inline-block">{{ $item->created_at->format('H:i A') }}</span>
                        </div>
                        <div class="timeline-icon">
                            <a href="javascript:void(0);"></a>
                        </div>
                        <div class="timeline-body">
                            <div class="d-flex align-items-top timeline-main-content flex-wrap mt-0">
                                <div class="avatar avatar-md  me-3 avatar-rounded mt-sm-0 mt-4">
                                    <img alt="avatar" src="{{ $item->service->image }}">
                                </div>
                                <div class="flex-fill">
                                    <div class="d-flex align-items-center">
                                        <div class="mt-sm-0 mt-2">
                                            @if ($item->service->serviceProduct->isNotEmpty())
                                                @foreach ($item->service->serviceProduct as $service)
                                                    <p class="mb-0 fs-14 fw-semibold">{{ $service->product->name }}</p>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </li>
                @endforeach
            </ul>
        @else
            <x-no-datacomponent />
        @endif
    </div>
@endsection
