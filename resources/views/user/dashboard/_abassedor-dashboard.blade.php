@include('user.dashboard._benner')
<div class="row">
    <div class="col-lg-3">
        @include('user.dashboard._profile-card')
        <div class="card custom-card text-fixed-white">
            <div class="card-body p-3">
                <div class="text-center">
                    <div class="flex-fill">
                        <div class="d-flex justify-content-between">
                            <small>Next rank progress</small>
                            <p class="mb-0">Delta 500</p>
                        </div>
                        <div class="progress progress-xs">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 24%" aria-valuenow="24"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <small>3000</small>
                            <p class="mb-0">5000</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('user.dashboard._activities')
    </div>
    <div class="col-lg-9">
        <div class="row">
            <div class="col-lg-4">
                <div class="card custom-card card-bg-primary text-fixed-white">
                    <div class="card-body p-0">
                        <div class="d-flex align-items-top p-4 flex-wrap">
                            <div class="me-3 lh-1">
                                <span class="avatar avatar-md avatar-rounded bg-white text-primary shadow-sm">
                                    <i class="las la-medal fs-18"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <h5 class="fw-semibold mb-1 text-fixed-white">Delta 3000</h5>
                                <p class="op-7 mb-0 fs-12">Highest Rank</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card custom-card card-bg-success text-fixed-white">
                    <div class="card-body p-0">
                        <div class="d-flex align-items-top p-4 flex-wrap">
                            <div class="me-3 lh-1">
                                <span class="avatar avatar-md avatar-rounded bg-white text-primary shadow-sm">
                                    <i class="ri-wallet-2-line fs-18"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <h5 class="fw-semibold mb-1 text-fixed-white">$15,800</h5>
                                <p class="op-7 mb-0 fs-12">Commisions Wallet</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card custom-card card-bg-warning text-fixed-white">
                    <div class="card-body p-0">
                        <div class="d-flex align-items-top p-4 flex-wrap">
                            <div class="me-3 lh-1">
                                <span class="avatar avatar-md avatar-rounded bg-white text-primary shadow-sm">
                                    <i class="ri-wallet-2-line fs-18"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <h5 class="fw-semibold mb-1 text-fixed-white">$3,000</h5>
                                <p class="op-7 mb-0 fs-12">Lifetime Earnings</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex flex-wrap align-items-top justify-content-between">
                            <div class="flex-fill">
                                <p class="mb-0 text-muted">Current Circle Direct Volumn</p>
                                <div class="d-flex align-items-center">
                                    <span class="fs-5 fw-semibold">10.0000 BV</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex flex-wrap align-items-top justify-content-between">
                            <div class="flex-fill">
                                <p class="mb-0 text-muted">Current Cycle Commissions</p>
                                <div class="d-flex align-items-center">
                                    <span class="fs-5 fw-semibold">$21,520</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card custom-card card-bg-primary text-fixed-white">
                    <div class="card-body p-0">
                        <div class="d-flex align-items-top p-4 flex-wrap">
                            <div class="me-3 lh-1">
                                <span class="avatar avatar-md avatar-rounded bg-white text-primary shadow-sm">
                                    <i class="las la-medal fs-18"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <h5 class="fw-semibold mb-1 text-fixed-white">Delta 3000</h5>
                                <p class="op-7 mb-0 fs-12">Current Rank</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="card custom-card text-fixed-white">
                    <div class="card-body p-3">
                        <div class="text-center">
                            <p class="fs-14 fw-semibold mb-2">Current Week Remaining Time</p>
                            <div class="d-flex align-items-center justify-content-center flex-wrap mb-2">
                                <div class="clock">
                                    <div class="clock-segment">
                                        <span class="clock-number" id="days">00</span>
                                        <span class="clock-label">Days</span>
                                    </div>
                                    <div class="clock-segment">
                                        <span class="clock-number" id="hours">00</span>
                                        <span class="clock-label">Hours</span>
                                    </div>
                                    <div class="clock-segment">
                                        <span class="clock-number" id="minutes">00</span>
                                        <span class="clock-label">Minutes</span>
                                    </div>
                                    <div class="clock-segment">
                                        <span class="clock-number" id="seconds">00</span>
                                        <span class="clock-label">Seconds</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-fill">
                                <div class="d-flex justify-content-between">
                                    <small>Rank Advancement period</small>
                                    <h6 class="mb-0">Week 2</h6>
                                </div>
                                <div class="progress progress-xs">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 24%"
                                        aria-valuenow="24" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-xl-3">
                <div class="card custom-card border-top-card border-top-primary">
                    <div class="card-body p-3">
                        <div class="text-center">
                            <span class="avatar avatar-md bg-primary shadow-sm avatar-rounded mb-2">
                                <i class="bx bx-doughnut-chart fs-16"></i>
                            </span>
                            <p class="fs-14 fw-semibold mb-2">Subscriptions</p>
                            <div class="d-flex align-items-center justify-content-center flex-wrap">
                                <h5 class="mb-0 fw-semibold">0</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3">
                <div class="card custom-card border-top-card border-top-primary">
                    <div class="card-body p-3">
                        <div class="text-center">
                            <span class="avatar avatar-md bg-primary shadow-sm avatar-rounded mb-2">
                                <i class="bx bx-support fs-16"></i>
                            </span>
                            <p class="fs-14 fw-semibold mb-2">Tickets</p>
                            <div class="d-flex align-items-center justify-content-center flex-wrap">
                                <h5 class="mb-0 fw-semibold">0</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                @include('user.dashboard._revenue-stats')
            </div>
        </div>
        @if (!empty(auth()->user()->subscriptions))
            @include('user.dashboard._purchases')
        @endif
    </div>
</div>
