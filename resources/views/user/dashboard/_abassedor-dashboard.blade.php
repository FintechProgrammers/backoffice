@include('user.dashboard._benner')
<div class="row">
    <div class="col-lg-3">
        @include('user.dashboard._profile-card')
        <div class="card custom-card card-bg-primary text-fixed-white">
            <div class="card-body p-0">
                <div class="d-flex align-items-top p-4 flex-wrap">
                    <div class="me-3 lh-1">
                        <span class="avatar avatar-md avatar-rounded bg-white text-primary shadow-sm">
                            <i class="ri-wallet-2-line fs-18"></i>
                        </span>
                    </div>
                    <div class="flex-fill">
                        <h5 class="fw-semibold mb-1 text-fixed-white">
                            ${{ number_format($walletBalance, 2, '.', ',') }}
                        </h5>
                        <p class="op-7 mb-0 fs-12">Commisions Wallet</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card custom-card card-bg-dark text-fixed-white">
            <div class="card-body p-0">
                <div class="d-flex align-items-top p-4 flex-wrap">
                    <div class="me-3 lh-1">
                        <span class="avatar avatar-md avatar-rounded bg-white text-primary shadow-sm">
                            <i class="ri-wallet-2-line fs-18"></i>
                        </span>
                    </div>
                    <div class="flex-fill">
                        <h5 class="fw-semibold mb-1 text-fixed-white">
                            ${{ number_format($lifeTimeEarnings, 2, '.', ',') }}</h5>
                        <p class="op-7 mb-0 fs-12">Lifetime Earnings</p>
                    </div>
                </div>
            </div>
        </div>
        @include('user.dashboard._activities')
    </div>
    <div class="col-lg-9">
        <div class="row">
            <div class="col-lg-4">
                <div class="card custom-card card-bg-white text-fixed-white">
                    <div class="card-body p-4">
                        <p class="op-7  text-dark">Highest Rank</p>
                        <div class="d-flex align-items-center w-100">
                            <div class="me-2">
                                @if (!empty(Auth::user()->highestRank->highest_criteria))
                                    <span class="avatar avatar-rounded">
                                        <img src="{{ Auth::user()->highestRank->file_url }}" alt="img">
                                    </span>
                                @else
                                    <span class="avatar avatar-md avatar-rounded bg-white text-dark shadow-sm">
                                        <i class="las la-medal fs-18"></i>
                                    </span>
                                @endif
                            </div>
                            <div class="flex-fill">
                                <h3 class="fw-semibold mb-0 text-fixed-white">
                                    @if (!empty(Auth::user()->highestRank->highest_criteria))
                                        {{ Auth::user()->highestRank->highest_criteria }}
                                    @else
                                        <small class="text-dark">no rank</small>
                                    @endif
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                @include('user.dashboard._clock')
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card custom-card card-bg-primary">
                            <div class="card-body">
                                <div class="d-flex flex-wrap align-items-top justify-content-between">
                                    <div class="flex-fill">
                                        <p class="mb-0 text-light">Current week direct BV</p>
                                        <div class="d-flex align-items-center">
                                            <span
                                                class="fs-5 fw-semibold">{{ number_format($currentWeekDirectVolume, 2, '.', ',') }}
                                                BV</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card custom-card">
                            <div class="card-body">
                                <div class="d-flex flex-wrap align-items-top justify-content-between">
                                    <div class="flex-fill">
                                        <p class="mb-0 text-muted">Current week direct Commissions</p>
                                        <div class="d-flex align-items-center">
                                            <span
                                                class="fs-5 fw-semibold">${{ number_format($currentMonthDirectVolume, 2, '.', ',') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card custom-card">
                            <div class="card-body">
                                <div class="d-flex flex-wrap align-items-top justify-content-between">
                                    <div class="flex-fill">
                                        <p class="mb-0">Team Volume</p>
                                        <div class="d-flex align-items-center">
                                            <span
                                                class="fs-5 fw-semibold">{{ number_format($teamVolume, 2, '.', ',') }}BV</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card custom-card">
                            <div class="card-body">
                                <div class="d-flex flex-wrap align-items-top justify-content-between">
                                    <div class="flex-fill">
                                        <p class="mb-0 text-muted">Team Commissions</p>
                                        <div class="d-flex align-items-center">
                                            <span
                                                class="fs-5 fw-semibold">${{ number_format($teamCommissions, 2, '.', ',') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="card custom-card card-bg-primary">
                                <div class="card-body">
                                    <div class="d-flex flex-wrap align-items-top justify-content-between">
                                        <div class="flex-fill">
                                            <p class="mb-0 text-light">Commissions this week</p>
                                            <div class="d-flex align-items-center">
                                                <span
                                                    class="fs-5 fw-semibold">${{ number_format($currentWeekCommissions, 2, '.', ',') }}</span>
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
                                            <p class="mb-0 text-muted">Total BV this month</p>
                                            <div class="d-flex align-items-center">
                                                <span
                                                    class="fs-5 fw-semibold">{{ number_format($currentMonthDirectVolume, 2, '.', ',') }}BV</span>
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
                                            <p class="mb-0 text-muted">Commissions this month</p>
                                            <div class="d-flex align-items-center">
                                                <span
                                                    class="fs-5 fw-semibold">${{ number_format($currentMonthCommissions, 2, '.', ',') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @include('user.dashboard._revenue-stats')
            </div>
            <div class="col-xl-4">
                <div class="col-lg-12">
                    <div class="card custom-card card-bg-dark text-fixed-white">
                        <div class="card-body p-0">
                            <div class="d-flex align-items-top p-4 flex-wrap">
                                <div class="me-3 lh-1">
                                    <span class="avatar avatar-md avatar-rounded bg-white text-primary shadow-sm">
                                        <i class="las la-medal fs-18"></i>
                                    </span>
                                </div>
                                <div class="flex-fill">
                                    <h5 class="fw-semibold mb-1 text-fixed-white">
                                        @if (!empty(Auth::user()->rank))
                                            {{ Auth::user()->rank->name }}
                                        @else
                                            <small class="text-muted">no rank</small>
                                        @endif
                                    </h5>
                                    <p class="op-7 mb-0 fs-12">Current Rank</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="card custom-card text-fixed-white">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-center flex-wrap">
                                <div class="flex-fill text-center">
                                    <p class="mb-0 text-muted">Ambassadors</p>
                                    <div class="text-center">
                                        <span
                                            class="fs-5 fw-semibold">{{ number_format($teamAmbassadors, 0, '.', ',') }}</span>
                                    </div>
                                </div>
                                <div class="flex-fill">
                                    <p class="mb-0 text-muted">New Customers</p>
                                    <div class="text-center">
                                        <span
                                            class="fs-5 fw-semibold">{{ number_format($teamCustomers, 0, '.', ',') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <p class="mb-1 text-muted">Next rank Delta500</p>
                            <div class="d-flex justify-content-between">
                                <p>300</p>
                                <div id="circular-semi"></div>
                                <p>500</p>
                            </div>
                        </div>
                    </div>
                </div>
                @include('user.dashboard._last_enrollment')
            </div>
        </div>
        <div class="row">
            <div class="col-xl-4">
                <div class="card custom-card card-bg-dark">
                    <div class="card-body">
                        <div class="text-center">
                            <p class="fs-14 fw-semibold mb-2 text-light">Active User</p>
                            <div class="d-flex align-items-center justify-content-center flex-wrap">
                                <h5 class="mb-0 fw-semibold  text-light">{{ $activeUsers, 0, '.', ',' }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card custom-card card-bg-dark">
                    <div class="card-body">
                        <div class="text-center">
                            <p class="fs-14 fw-semibold mb-2 text-light">Total User</p>
                            <div class="d-flex align-items-center justify-content-center flex-wrap">
                                <h5 class="mb-0 fw-semibold  text-light">{{ $totalUsers, 0, '.', ',' }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card custom-card card-bg-dark">
                    <div class="card-body">
                        <div class="text-center">
                            <p class="fs-14 fw-semibold mb-2 text-light">Upcoming Renewals</p>
                            <div class="d-flex align-items-center justify-content-center flex-wrap">
                                <h5 class="mb-0 fw-semibold  text-light">{{ $upcomingRenewals, 0, '.', ',' }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('user.dashboard._user_country')
        <div class="row">
            <div class="col-lg-6">
                @include('user.dashboard._top-team-sellars')
            </div>
            <div class="col-lg-6">
                @include('user.dashboard._top-team-sellars')
            </div>
        </div>
    </div>
</div>
