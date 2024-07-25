<div class="p-4">
    <p class="mb-1 fw-semibold text-muted op-5 fs-20">03</p>
    <div class="fs-15 fw-semibold d-sm-flex d-block align-items-center justify-content-between mb-3">
        <div>Payment Methods:</div>
    </div>
    {{-- <div class="row gy-3">
        @foreach ($providers as $item)
            <div class="col-xl-6 form-group">
                <div class="form-check shipping-method-container mb-0">
                    <input name="payment_provider" value="{{ $item->uuid }}" type="radio" class="form-check-input">
                    <div class="form-check-label">
                        <div class="d-sm-flex align-items-center justify-content-between">
                            <div class="me-2">
                                <span class="avatar avatar-md">
                                    <img src="{{ $item->image_url }}" alt="">
                                </span>
                            </div>
                            <div class="shipping-partner-details me-sm-5 me-0">
                                <p class="mb-0 fw-semibold">{{ $item->name }}</p>
                                @if ($item->is_crypto)
                                    <p class="text-muted fs-11 mb-0">Crypto Payments</p>
                                @else
                                    <p class="text-muted fs-11 mb-0">Card Payment</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

    </div> --}}
    <x-payment-method />
</div>
