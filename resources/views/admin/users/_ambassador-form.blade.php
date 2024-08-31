<h4>Setup User as Ambassador</h4>
<form method="POST" action="{{ route('admin.users.mark.ambassador', $user->uuid) }}" id="ambassadorForm">
    @csrf
    @if (!empty($plan))
        <div class="col-lg-12 mb-2 product-checkout">
            <div class="form-check shipping-method-container mb-0 bxi-package" style="cursor: pointer;">
                <input class="service d-none" name="package" value="{{ $plan->uuid }}" type="radio"
                    class="form-check-input" data-name="{{ $plan->name }}" data-image="{{ $plan->image }}"
                    data-price="{{ $plan->price }}" checked>
                <div class="form-check-label">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <div class="d-flex">
                            <div class="me-2">
                                <span class="avatar avatar-md">
                                    <img src="{{ $plan->image }}" alt="">
                                </span>
                            </div>
                            <div class="shipping-partner-details me-sm-5 me-0">
                                <p class="mb-0 fw-semibold">{{ $plan->name }}</p>
                                <p class="text-muted fs-11 mb-0">
                                    {{ convertDaysToUnit($plan->duration, $plan->duration_unit) . ' ' . $plan->duration_unit }}
                                </p>
                            </div>
                        </div>
                        <div class="fw-semibold me-sm-5 me-0">
                            ${{ number_format($plan->price, 2, '.', ',') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="mb-3">
                    <label for="form-text" class="form-label fs-14 text-dark">Duration</label>
                    <input type="number" class="form-control" id="form-text" placeholder="Enter duration"
                        value="{{ isset($service) ? convertDaysToUnit($service->duration, $service->duration_unit) : '' }}"
                        name="duration">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="mb-3">
                    <label for="form-text" class="form-label fs-14 text-dark">Duration Unit</label>
                    <select id="inputCountry" class="form-select" name="duration_unit">
                        <option value="">--select--</option>
                        @foreach (durationUnit() as $item)
                            <option value="{{ $item }}" @selected(isset($service) && $service->duration_unit == $item)>{{ Str::upper($item) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <button class="btn btn-primary" type="submit">
            <div class="spinner-border" style="display: none" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <span id="text">Submit</span>
        </button>
    @else
        <div class="alert alert-warning">
            There is no active ambassador plan
        </div>
    @endif

</form>
