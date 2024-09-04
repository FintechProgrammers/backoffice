<div class="text-center mb-3">
    <span class="avatar avatar-xl avatar-rounded">
        <img src="{{ $user->profile_picture }}" alt="">
    </span>
    <div class="mt-2">
        <p class="mb-0 fw-semibold "><span class="text-capitalize">{{ $user->name }}</span> ({{ $user->username }})</p>
        <p class="fs-12 op-7 mb-1 text-muted">{{ $user->email }}</p>
        <p class="fs-12 op-7 mb-1 text-muted">
            <b>Joined Date:</b>{{ $user->created_at->format('jS, M Y') }}
        </p>
        @if (!empty($user->sponsor))
            <p class="fs-12 op-7 mb-1 fw-semibold"><b>Sponsor:</b> {{ $user->sponsor->username }}
            </p>
        @endif

    </div>
</div>
@if ($user->is_ambassador)
    <div class="row mb-3">
        <div class="col-lg-12">
            <div class="rounded p-3 bg-light mb-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="lh-1">
                        <span class="avatar bg-secondary-transparent">
                            <i class="ti ti-wallet fs-20"></i>
                        </span>
                    </div>
                    <div>
                        <span class="d-block text-muted">Sales</span>
                        <span class="d-block fw-semibold">{{ number_format($totalSales, 2, '.', ',') }} <span
                                class="fs-12 text-muted fw-normal">BV</span></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="rounded p-3 bg-light mb-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="lh-1">
                        <span class="avatar bg-primary-transparent">
                            <i class="ti ti-wallet fs-20"></i>
                        </span>
                    </div>
                    <div>
                        <span class="d-block text-muted">Current Month Sales</span>
                        <span class="d-block fw-semibold">{{ number_format($currentCycleSales, 2, '.', ',') }} <span
                                class="fs-12 text-muted fw-normal">BV</span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-lg-12">
            <div class="card card-body">
                @include('partials._rank', ['user' => $user])
            </div>
        </div>
    </div>
@endif


<div class="row mb-3">
    <h6>Subscriptions:</h6>
    <div class="col-lg-12">
        @if (!empty($user->subscriptions))
            <ul class="list-group">
                @forelse ($user->subscriptions as $item)
                    <li class="list-group-item  mb-3">
                        <div class="d-sm-flex">
                            <span class="avatar avatar-sm">
                                <img src="{{ $item->service->image }}" alt="img">
                            </span>
                            <div class="ms-sm-2 ms-0 mt-sm-0 mt-1 fw-semibold flex-fill">
                                <p class="mb-0 lh-1">{{ $item->service->name }}</p>
                                <span class="fs-11 text-muted op-7">
                                    @if ($item->service->serviceProduct->isNotEmpty())
                                        {{ $item->service->serviceProduct->pluck('product.name')->implode(', ') }}
                                    @else
                                        No products available.
                                    @endif
                                </span>
                            </div>
                            @if ($item->end_date->isPast())
                                <span class="bg-warning">{{ __('Expired') }}</span>
                            @else
                                <span class="text-success">{{ __('Running') }}</span>
                            @endif
                        </div>
                    </li>
                @empty
                @endforelse
            </ul>
        @else
            <div class="d-flex justify-content-center">
                <h6 class="text-warning">no subscriptions</h6>
            </div>
        @endif
    </div>
</div>
