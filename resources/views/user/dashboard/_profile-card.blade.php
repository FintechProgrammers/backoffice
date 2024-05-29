<div class="card custom-card">
    <div class="card-body">
        <div class="d-flex align-items-top justify-content-between mb-4">
            <div>
                <span class="d-block fs-15 fw-semibold">My Profile</span>
                <span class="d-block fs-12 text-muted">67% Completed - <a href="{{ route('profile.edit') }}"
                        class="text-center text-primary">Click Here<i
                            class="bi bi-box-arrow-up-right fs-10 ms-2 align-middle"></i></a></span>
            </div>
        </div>
        <div class="text-center mb-4">
            <div class="mb-3">
                <span class="avatar avatar-xxl avatar-rounded circle-progress p-1">
                    <img src="{{ auth()->user()->profile_picture }}" alt="">
                </span>
            </div>
            <div>
                <h5 class="fw-semibold mb-0">{{ ucfirst(auth()->user()->name) }}</h5>
                <span class="fs-13 text-muted">{{ auth()->user()->email }}</span>
            </div>
        </div>
        <div class="btn-list text-center">
            <a href="profile.edit" class="btn btn-primary-light btn-sm">
                Profile
            </a>
        </div>
    </div>
</div>
