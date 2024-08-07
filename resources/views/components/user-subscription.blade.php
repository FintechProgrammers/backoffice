{{-- <div class="card custom-card text-center">
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
                <p class="text-center">Enjoy exclusive benefits by choosing a subscription plan that suits your needs.
                </p>
                <a href="{{ route('package.index') }}" class="btn btn-primary btn-sm">Explore Plans</a>
            </div>
        @endif
    </div>
</div> --}}

<div class="card custom-card">
    <div class="card-header justify-content-between">
        <div class="card-title">
            Subscriptions
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table text-nowrap table-bordered">
                <thead>
                    <tr>
                        <th scope="col">Service</th>
                        <th scope="col">End Date</th>
                        <th scope="col">Status</th>
                        <th scope="col">Progress</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody id="content">
                    @forelse (auth()->user()->subscriptions as $item)
                        <tr>
                            <td>
                                <x-package-title title="{{ $item->service->name }}" image="{{ $item->service->image }}"
                                    price="{{ $item->service->price }}" />
                            </td>
                            <td>
                                {{ $item->end_date->format('jS,M Y H:i A') }}
                            </td>
                            <td>
                                @if ($item->active())
                                    <span class="badge bg-success-transparent">Running</span>
                                @else
                                    <span class="badge bg-warning-transparent">Expired</span>
                                @endif
                            </td>
                            <td>
                                <div class="progress">
                                    @php
                                        $progress = $item->progressPercentage();
                                    @endphp
                                    <div class="progress-bar" role="progressbar" style="width: {{ $progress }}%;"
                                        aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                                        {{ number_format($progress, 2) }}%</div>
                                </div>
                            </td>
                            <td>
                                @if (!$item->active())
                                    <a href="{{ route('package.details', $item->service->uuid) }}"
                                        class="btn btn-sm btn-primary">Renew</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">
                                <span class="text-warning">no subscription available</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
