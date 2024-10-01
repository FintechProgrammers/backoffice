<div class="product-checkout">
    <h6 class="text-center">Update Subscription</h6>
    <div class="mb-3">
        <x-profile-component name="{{ $user->full_name }}" email="{{ $user->email }}"
            image="{{ $user->profile_picture }}" />
    </div>
    <div class="mb-3">
        <x-package-title title="{{ $subscription->service->name }}" image="{{ $subscription->service->image }}"
            price="{{ $subscription->service->price }}" />
    </div>
    <div class="mb-1">
        <h6><strong>Start Date :</strong> {{ $subscription->start_date->format('jS,M Y H:i A') }}</h5>
    </div>
    <div class="mb-1">
        <h6><strong>End Date :</strong> {{ $subscription->end_date->format('jS,M Y H:i A') }}</h6>
    </div>
    <div class="mb-1">
        <h6>
            <strong>Status :</strong>
            @if ($subscription->end_date->isPast())
                <span class="badge bg-warning-transparent">{{ __('Expired') }}</span>
            @else
                <span class="badge bg-success-transparent">{{ __('Running') }}</span>
            @endif
        </h6>
    </div>
    <form method="POST" action="{{ route('admin.users.membership.update', $subscription->uuid) }}" id="planForm">
        @csrf
        <p class="text-center">Choose a package to create your account.</p>
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
