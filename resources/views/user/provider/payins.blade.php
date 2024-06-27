<div>
    <h4 class="text-center mb-3">Select Method for Payment </h4>
</div>
<div id="methods-body">
    <div id="process-loader" style="display: none">
        <div class="d-flex justify-content-center" >
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>
    <div class="row" id="payments">
        <div class="col-lg-6 mb-3">
            <a href="#"
                data-url="{{ route('provider.card.initiate') }}?service_id={{ !empty($service) ? $service->uuid : '' }}">
                <div class="card custom-card text-center card-bg-light">
                    <div class="card-body pt-5">
                        <span class="avatar avatar-xl avatar-rounded me-2 mb-2">
                            <img src="{{ asset('assets/images/debit-card.webp') }}" alt="img">
                        </span>
                        <div class="fw-semibold fs-16 text-uppercase">Pay with Card</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-6 mb-3">
            <a href="#"
                data-url="{{ route('provider.crypto.initiate') }}?service_id={{ !empty($service) ? $service->uuid : '' }}">
                <div class="card custom-card text-center card-bg-light">
                    <div class="card-body pt-5">
                        <span class="avatar avatar-xl avatar-rounded me-2 mb-2">
                            <img src="{{ asset('assets/images/crypto-wallet.png') }}" alt="img">
                        </span>
                        <div class="fw-semibold fs-16 text-uppercase">Pay with Crypto</div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
