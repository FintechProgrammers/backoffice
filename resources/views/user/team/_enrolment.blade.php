 <h4 class="text-center">
     Enrollment Packages
 </h4>
 <p class="text-center">Choose a package to create your account.</p>
 <div class="row mb-3">
     @forelse($packages as $item)
         <div class="col-lg-12 mb-2">
             <div class="form-check shipping-method-container mb-0 bxi-package" style="cursor: pointer;">
                 <input class="service d-none" name="package_id" value="{{ $item->uuid }}" type="radio"
                     class="form-check-input" data-name="{{ $item->name }}" data-image="{{ $item->image }}"
                     data-price="{{ $item->price }}" required>
                 <div class="form-check-label">
                     <div class="d-sm-flex align-items-center justify-content-between">
                         <div class="d-flex">
                             <div class="me-2">
                                 <span class="avatar avatar-md">
                                     <img src="{{ $item->image }}" alt="">
                                 </span>
                             </div>
                             <div class="shipping-partner-details me-sm-5 me-0">
                                 <p class="mb-0 fw-semibold">{{ $item->name }}</p>
                                 <p class="text-muted fs-11 mb-0">
                                     {{ convertDaysToUnit($item->duration, $item->duration_unit) . ' ' . $item->duration_unit }}
                                 </p>
                             </div>
                         </div>
                         <div class="fw-semibold me-sm-5 me-0">
                             ${{ number_format($item->price, 2, '.', ',') }}
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     @empty
         <div class="col-lg-12">
             <x-no-datacomponent title="no package available" />
         </div>
     @endforelse
 </div>
