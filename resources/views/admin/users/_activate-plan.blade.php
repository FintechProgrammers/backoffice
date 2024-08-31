<div class="product-checkout">
    <h6 class="text-center">Activate Subscription</h6>
    <div class="mb-3">
        <x-profile-component name="{{ $user->full_name }}" email="{{ $user->email }}"
            image="{{ $user->profile_picture }}" />
    </div>
    <form method="POST" action="{{ route('admin.users.plan.create', $user->uuid) }}" id="planForm">
        @csrf
        <p class="text-center">Choose a package to create your account.</p>
        <div class="mb-3">
            <div class="row scrollable-container" style="max-height: 300px; overflow-y: auto;">
                @forelse($packages as $item)
                    <div class="col-lg-12 mb-2">
                        <div class="form-check shipping-method-container mb-0 bxi-package" style="cursor: pointer;">
                            <input class="service d-none" name="package" value="{{ $item->uuid }}" type="radio"
                                class="form-check-input" data-name="{{ $item->name }}"
                                data-image="{{ $item->image }}" data-price="{{ $item->price }}">
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
            @error('package')
                <div class="text-danger">{{ $message }}</div>
            @enderror
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

    </form>
</div>
