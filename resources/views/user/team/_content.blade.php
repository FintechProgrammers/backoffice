@if (count($customers) > 0)
    <div class="row">
        @forelse ($customers as $item)
            <div class="col-xxl-4 col-xl-6 col-lg-6 col-md-6 col-sm-12">
                <div class="card custom-card team-member-card">
                    <div class="teammember-cover-image">
                        <img src="{{ asset('assets/images/media/team-covers/1.jpg') }}" class="card-img-top"
                            alt="...">
                        <span class="avatar avatar-xl avatar-rounded">
                            <img src="{{ $item->profile_picture }}" alt="">
                        </span>
                        {{-- <a href="javascript:void(0);" class="team-member-star text-warning">
                        <i class="ri-star-fill fs-16"></i>
                    </a> --}}
                    </div>
                    <div class="card-body p-0">
                        <div
                            class="d-flex flex-wrap align-item-center mt-sm-0 mt-5 justify-content-between border-bottom border-block-end-dashed p-3">
                            <div class="team-member-details flex-fill">
                                <p class="mb-0 fw-semibold fs-16 text-truncate">
                                    <a href="javascript:void(0);">{{ Str::ucfirst($item->full_name) }}</a>
                                </p>
                                <p class="mb-0 fs-11 text-muted text-break">{{ $item->email }}</p>
                            </div>
                        </div>
                        <div class="team-member-stats d-sm-flex justify-content-evenly">
                            <div class="text-center p-3 my-auto">
                                <p class="fw-semibold mb-0">Register On</p>
                                <span class="text-muted fs-12">{{ $item->created_at->format('jS, m Y') }}</span>
                            </div>
                            <div class="text-center p-3 my-auto">
                                <p class="fw-semibold mb-0">Sales</p>
                                <span class="text-muted fs-12">${{ $item->total_sales }}</span>
                            </div>
                            <div class="text-center p-3 my-auto">
                                <p class="fw-semibold mb-0">Role</p>
                                <span class="text-muted fs-12">
                                    @if ($item->is_ambassador)
                                        <span class="badge bg-blue">Ambassador</span>
                                    @else
                                        <span class="badge bg-black">Customer</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        {{ $customers->links('vendor.pagination.theme') }}
    </div>
@else
    <div class="d-flex flex-column align-items-center h-100 ">
        <img src="{{ asset('assets/images/referral.png') }}" width="400px" height="400px" alt="">
        <h5 class="text-uppercase text-center"><b>Refer a friend</b></h5>
        <p class="text-center"> and earn commissions on each of their purchases!<br /> Share
            your referral link today and start earning rewards.</p>
        <div class="input-group mb-3 w-25">
            <input type="text" class="form-control" placeholder="Recipient's username"
                value="{{ auth()->user()->referral_code }}" readonly aria-label="Recipient's username"
                aria-describedby="button-addon2">
            <button class="btn btn-primary copy_btn"
                copy_value="{{ route('register') }}?code={{ auth()->user()->referral_code }}" type="button"
                id="button-addon2">Copy</button>
        </div>
    </div>
@endif
