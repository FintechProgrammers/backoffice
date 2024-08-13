<div class="team-groups">
    <div class="card custom-card" style="height: 410px">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="fw-semibold mb-0">Last Enrollments</h6>
        </div>
        <div class="card-body p-0">
            <div class="teams-nav" id="teams-nav-enrol">
                <ul class="list-unstyled mb-0 mt-2">
                    @forelse ($enrollments as $item)
                        <li>
                            <a href="javascript:void(0);">
                                <div class="d-flex align-items-center">
                                    <div class="me-2 d-flex align-items-center">
                                        <span class="avatar avatar-sm avatar-rounded online">
                                            <img src="{{ $item->profile_picture }}" alt="">
                                        </span>
                                    </div>
                                    <div class="flex-fill">
                                        <span>{{ $item->full_name }}</span>
                                    </div>
                                    <div>
                                        <span class="fs-10 fw-semibold text-muted">
                                            {{-- <p class="mb-0">Date Joined:</p> --}}
                                            <p class="mb-0">{{ $item->created_at->format('jS,M Y') }}</p>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li>
                            <h6 class="text-center text-warning">no records</h6>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
