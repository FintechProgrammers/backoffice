 <div class="row gy-3">
     @foreach ($providers as $item)
         <div class="col-xl-6 form-group">
             <div class="form-check shipping-method-container mb-0" onclick="selectPaymentProvider(this)"
                 style="cursor: pointer;">
                 <input name="payment_provider" hidden value="{{ $item->uuid }}" type="radio" class="form-check-input">
                 <div class="form-check-label">
                     <div class="d-sm-flex align-items-center justify-content-between">
                         {{-- <div class="me-2">
                    <span class="avatar avatar-md">
                        <img src="{{ $item->image_url }}" alt="">
                    </span>
                </div> --}}
                         <div class="shipping-partner-details me-sm-5 me-0 row align-items-center">
                             {{-- <p class="text-muted fs-11 mb-0">{{ $item->name }}</p> --}}
                             <div class="col-6 d-flex justify-content-center">
                                 @if ($item->is_crypto)
                                     <img src="{{ asset('assets/images/crypto-wallet.png') }}" alt="Crypto Payments"
                                         class="img-fluid" style="max-width: 70px; margin-right: 10px;">
                                 @else
                                     <img src="{{ asset('assets/images/debit-card.webp') }}" alt="Card Payment"
                                         class="img-fluid" style="max-width: 60px; margin-right: 10px;">
                                 @endif
                             </div>
                             <div class="col-6">
                                 @if ($item->is_crypto)
                                     <p class="fw-semibold mb-0">Pay with Crypto</p>
                                 @else
                                     <p class="fw-semibold mb-0">Pay with Card </p>
                                 @endif
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     @endforeach
     @if (Auth::check() && Auth::user()->is_ambassador)
         {{-- <div class="col-xl-6 form-group">
             <div class="form-check shipping-method-container mb-0" style="cursor: pointer;" onclick="selectPaymentProvider(this)">
                 <input name="payment_provider" hidden value="commission_wallet" type="radio" class="form-check-input">
                 <div class="form-check-label">
                     <div class="d-sm-flex align-items-center justify-content-between">
                         <div class="shipping-partner-details me-sm-5 me-0">
                             <p class="mb-0 fw-semibold">Commission Wallet</p>
                             <p class="text-muted fs-11 mb-0">Pay from your commission wallet</p>
                         </div>
                     </div>
                 </div>
             </div>
         </div> --}}
     @endif
 </div>
