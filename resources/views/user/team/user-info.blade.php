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
@endif

<div class="row mb-3">
    <div class="col-lg-12">
        <div class="card card-body">
            @if (!empty($user->rank))
                <div class="d-flex align-items-center">
                    <span class="avatar avatar-md avatar-rounded me-3">
                        <img src="{{ $user->rank->file_url }}" alt="">
                    </span>
                    <div>
                        <h6 class="mb-0 fw-semibold text-muted text-capitalize">{{ $user->rank->name }}</h6>
                    </div>
                </div>
            @else
                <div class="d-flex align-items-center">
                    <span class="avatar avatar-md avatar-rounded me-3">
                        <img src="{{ asset('assets/images/no-rank.jpg') }}" alt="">
                    </span>
                    <div>
                        <h6 class="mb-0 fw-semibold text-muted">No Rank</h6>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="row mb-3">
    <h6>Subscriptions:</h6>
    <div class="col-lg-12">
        @if (!empty($subscription))
            <div class="d-flex align-items-center mb-3">
                <span class="avatar avatar-md avatar-rounded me-3">
                    <img src="{{ $subscription->service->image_url }}" alt="">
                </span>
                <div>
                    <p class="mb-0 fw-semibold text-muted">{{ $subscription->service->name }}</p>
                </div>
            </div>
        @else
            <div class="d-flex flex-column align-items-center mb-3 align-content-center">
                <h4 class="mb-0 fs-10 fw-semibold text-warning text-center">No Subscription</h4>
            </div>
        @endif
    </div>
</div>
