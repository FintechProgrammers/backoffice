<div class="card custom-card overflow-hidden">
    <div class="card-header justify-content-between">
        <div class="card-title">Activities</div>
    </div>
    <div class="card-body mt-0 latest-timeline scrollable-card" id="latest-timeline">
        <ul class="timeline-main mb-0 list-unstyled">
            @foreach (Auth::user()->activities as $item)
                <li>
                    <div class="featured_icon1 featured-success"></div>
                </li>
                <li class="mt-0 activity">
                    <a href="javascript:void(0);" class="fs-12">
                        <p class="mb-0"><span class="fw-semibold">
                                {{ $item->log }}
                        </p>
                    </a>
                    <small class="text-muted mt-0 mb-0 fs-10">{{ $item->created_at->format('H:i A') }}</small>
                </li>
            @endforeach

        </ul>
    </div>
</div>
