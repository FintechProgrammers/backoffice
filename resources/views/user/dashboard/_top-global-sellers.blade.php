<div class="team-groups">
    <div class="card custom-card card-bg-dark text-white" style="height: 500px">
        <div class="card-body p-0 ">
            <h6 class="fw-semibold mb-0 text-light p-3">Top 20 Global</h6>
            <div class="teams-nav" id="teams-nav">
                <ul class="list-unstyled mb-0 mt-2 text-white">
                    @forelse ($topGlobalSellers as $item)
                        <li>
                            <a href="javascript:void(0);">
                                <div class="d-flex align-items-center">
                                    <div class="me-2 d-flex align-items-center">
                                        <span class="avatar avatar-sm avatar-rounded online">
                                            <img src="{{ $item->profile_picture }}" alt="">
                                        </span>
                                    </div>
                                    <div class="flex-fill text-white">
                                        <span>{{ $item->full_name }}</span>
                                        <p class="mb-0">{{ $item->email }}</p>
                                    </div>
                                    <div>
                                        <h4 class="fs-10 fw-semibold text-white mb-0">
                                            ${{ number_format($item->total_sales, 2, '.', ',') }}
                                        </h4>
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
