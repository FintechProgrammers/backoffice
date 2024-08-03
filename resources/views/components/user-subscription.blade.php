<div class="card custom-card text-center">
    <div class="card-body">
        @if ($subscriptions)
            <span class="avatar avatar-xl avatar-rounded me-2 mb-2">
                <img src="{{ $subscriptions->service->image }}" alt="img">
            </span>
            <div class="fw-semibold fs-16">{{ $subscriptions->service->name }}</div>
            @if ($subscriptions->service->serviceProduct->isNotEmpty())
                <p class="mb-4 text-muted fs-11">
                    {{ $subscriptions->service->serviceProduct->pluck('product.name')->implode(', ') }}</p>
            @endif

            @if ($subscriptions->end_date->isPast())
                <h6 class="badge bg-danger">Expired</h6>
            @else
                <h6 class="badge bg-success">Running</h6>
            @endif

            <h6>
                <b>Expiry Date:</b> {{ $subscriptions->end_date->format('jS,M Y') }}
            </h6>

            @if ($subscriptions->end_date->isPast())
                <p class="text-danger">Your subscription has expired on
                    {{ $subscriptions->end_date->format('d F, Y') }}.</p>
                <a href="{{ route('package.details', $subscriptions->uuid) }}" class="btn btn-primary">Renew
                    Subscription</a>
            @endif


            @php
                $higherPackages = $packages->filter(function ($package) use ($subscriptions) {
                    return $package->price > $subscriptions->service->price;
                });
            @endphp

            @if ($higherPackages->isNotEmpty())
                <a href="{{ route('package.index') }}" class="btn btn-success btn-sm">Upgrade Package</a>
            @endif
        @else
            <div class="d-flex flex-column align-items-center h-100">
                <img src="{{ asset('assets/images/referral.png') }}" width="100px" height="100px" alt="">
                {{-- <h5 class="text-uppercase text-center">You Haven't Subscribed Yet</h5> --}}
                <p class="text-center">Enjoy exclusive benefits by choosing a subscription plan that suits your needs.
                </p>
                <a href="{{ route('package.index') }}" class="btn btn-primary btn-sm">Explore Plans</a>
            </div>
        @endif
    </div>
</div>
