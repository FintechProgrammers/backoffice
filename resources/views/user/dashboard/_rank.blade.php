@if (Auth::user()->is_ambassador)
    <div class="card custom-card crm-highlight-card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="fw-semibold fs-18 text-fixed-white mb-2">Your target is incomplete</div>
                    <span class="d-block fs-12 text-fixed-white"><span class="op-7">You have completed</span> <span
                            class="fw-semibold text-warning">48%</span> <span class="op-7">of the given target,
                            you can also check your status</span>.</span>
                    <span class="d-block fw-semibold mt-1"><a class="text-fixed-white" href="javascript:void(0);"><u>Click
                                here</u></a></span>
                </div>
                <div>
                    <div id="crm-main"></div>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="card custom-card upgrade-card text-fixed-white">
        <div class="card-body text-fixed-white">
            {{-- <span class="avatar avatar-xxl">
            <img src="{{ asset('assets/images/media/media-84.png') }}" alt="">
        </span> --}}
            <div class="upgrade-card-content">
                <span class="fw-semibol mb-1 upgrade-text">Upgrade to Ambassador!</span>
                <span class="d-block fw-normal mb-5 op-7">
                    As an ambassador, you qualify for a sales bonus.
                </span>
                <button type="button" data-bs-toggle="modal" data-bs-target="#primaryModal"
                    data-url="{{ route('ambassedor.payment.confirm') }}"
                    class="btn btn-sm btn-light btn-wave waves-effect waves-light trigerModal">{{ __('Upgrade Now') }}</button>
            </div>
        </div>
    </div>
@endif
