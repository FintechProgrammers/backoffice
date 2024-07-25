 <div class="row gy-3">
     @foreach ($providers as $item)
         <div class="col-xl-6 form-group">
             <div class="form-check shipping-method-container mb-0">
                 <input name="payment_provider" value="{{ $item->uuid }}" type="radio" class="form-check-input">
                 <div class="form-check-label">
                     <div class="d-sm-flex align-items-center justify-content-between">
                         {{-- <div class="me-2">
                                <span class="avatar avatar-md">
                                    <img src="{{ $item->image_url }}" alt="">
                                </span>
                            </div> --}}
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
     @if (Auth::check() && Auth::user()->is_ambassador)
         <div class="col-xl-6 form-group">
             <div class="form-check shipping-method-container mb-0">
                 <input name="payment_provider" value="commission_wallet" type="radio" class="form-check-input">
                 <div class="form-check-label">
                     <div class="d-sm-flex align-items-center justify-content-between">
                         {{-- <div class="me-2">
                                <span class="avatar avatar-md">
                                    <img src="{{ $item->image_url }}" alt="">
                                </span>
                            </div> --}}
                         <div class="shipping-partner-details me-sm-5 me-0">
                             <p class="mb-0 fw-semibold">Commission Wallet</p>
                             <p class="text-muted fs-11 mb-0">Pay from your commission wallet</p>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     @endif
 </div>
