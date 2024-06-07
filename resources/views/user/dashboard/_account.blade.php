@if (Auth::user()->is_ambassador)
    <div class="card custom-card">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-4">
                <div class="me-1">
                    <h6 class="fs-15 mg-b-3">Bonus Wallet</h6>
                </div>
            </div>
            <p class="fs-24 fw-semibold">{{ number_format(Auth::user()->bonuWallet? Auth::user()->bonuWallet->amount : 0.00) }} BV</p>
            <p class="mb-1 fs-14">${{ number_format(Auth::user()->bonuWallet? Auth::user()->bonuWallet->amount * systemSettings()->bv_equivalent : 0.00) }}</p>
        </div>
    </div>
@endif
