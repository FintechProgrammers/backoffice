<div class="card custom-card card-bg-success text-fixed-white">
    <div class="card-body p-0">
        <div class="d-flex align-items-top p-4 flex-wrap">
            <div class="me-3 lh-1">
                <span class="avatar avatar-md avatar-rounded bg-white text-primary shadow-sm">
                    <i class="ri-wallet-2-line fs-18"></i>
                </span>
            </div>
            <div class="flex-fill">
                <h5 class="fw-semibold mb-1 text-fixed-white">${{ !empty(Auth::user()->wallet)? number_format(Auth::user()->wallet->amount,2, '.', ','): number_format(0.00,2, '.', ',') }}</h5>
                <p class="op-7 mb-0 fs-12">Current Balance</p>
            </div>
        </div>
    </div>
</div>
<div class="card custom-card card-bg-danger text-fixed-white">
    <div class="card-body p-0">
        <div class="d-flex align-items-top p-4 flex-wrap">
            <div class="me-3 lh-1">
                <span class="avatar avatar-md avatar-rounded bg-white text-primary shadow-sm">
                    <i class="ri-wallet-2-line fs-18"></i>
                </span>
            </div>
            <div class="flex-fill">
                <h5 class="fw-semibold mb-1 text-fixed-white">${{ Auth::user()->totalWithdrawalsAmount }}</h5>
                <p class="op-7 mb-0 fs-12">Total Withdrawals</p>
            </div>
        </div>
    </div>
</div>
<div class="card custom-card card-bg-warning text-fixed-white">
    <div class="card-body p-0">
        <div class="d-flex align-items-top p-4 flex-wrap">
            <div class="me-3 lh-1">
                <span class="avatar avatar-md avatar-rounded bg-white text-primary shadow-sm">
                    <i class="ri-wallet-2-line fs-18"></i>
                </span>
            </div>
            <div class="flex-fill">
                <h5 class="fw-semibold mb-1 text-fixed-white">${{ Auth::user()->totalPendingWithdrawalsAmount }}</h5>
                <p class="op-7 mb-0 fs-12">Pending Withdrawals</p>
            </div>
        </div>
    </div>
</div>
