{{-- <div class="card custom-card overflow-hidden">
    <div class="card-header justify-content-between">
        <div class="card-title">
            Recent Activity
        </div>
    </div>
    <div class="card-body">
        <div>
            <ul class="list-unstyled mb-0 crm-recent-activity" id="recent-activity">
                @foreach (Auth::user()->activities as $item)
                    <li class="crm-recent-activity-content">
                        <div class="d-flex align-items-top">
                            <div class="me-3">
                                <span class="avatar avatar-xs bg-primary-transparent avatar-rounded">
                                    <i class="bi bi-circle-fill fs-8"></i>
                                </span>
                            </div>
                            <div class="crm-timeline-content">
                                <span class="fw-semibold">{{ $item->log }}</span>
                            </div>
                            <div class="flex-fill text-end">
                                <span
                                    class="d-block text-muted fs-11 op-7">{{ $item->created_at->format('H:i A') }}</span>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div> --}}
<div class="card custom-card overflow-hidden">
    <div class="card-header justify-content-between">
        <div class="card-title">Activities</div>
    </div>
    <div class="card-body mt-0 latest-timeline" id="latest-timeline">
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
