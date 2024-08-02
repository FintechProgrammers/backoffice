<div class="d-flex align-items-center lh-1">
    <div class="me-2">
        <span class="avatar avatar-md avatar-rounded">
            <img src="{{ !empty($rank) ? $rank->image : asset('assets/images/no-rank.jpg') }}" alt="">
        </span>
    </div>
    <div>
        <span class="d-block fw-semibold mb-0">{{ !empty($rank) ? $rank->name : 'No rank' }}</span>
    </div>
</div>
