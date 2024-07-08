<div class="container">
    <div class="row justify-content-center align-items-center">
        <div class="col-xxl-8 col-xl-8 col-lg-8 col-md-8 col-sm-8 col-12">
            @if (!empty($kyc))
                @if ($kyc->status == 'pending')
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="text-center mb-3" id="frontContent">
                                <img src="{{ asset('assets/images/kyc-review.png') }}" class="img-fluid rounded"
                                    width="400px" height="300px" alt="...">
                            </div>
                            <h6 class="text-uppercase text-center"><b>Your submitted document is currently under
                                    review.</b></h6>
                        </div>
                    </div>
                @elseif ($kyc->status == 'verified')
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="text-center mb-3" id="frontContent">
                                <img src="{{ asset('assets/images/kyc-verified.png') }}" class="img-fluid rounded"
                                    width="400px" height="300px" alt="...">
                            </div>
                            <h6 class="text-uppercase text-center"><b>Your document verified</b></h6>
                        </div>
                    </div>
                @else
                    <div class="alert alert-danger">
                        <h6>Document Not Approved</h6>
                        <p class="">
                            Your document was not approved. Please review the feedback, make necessary corrections, and
                            re-upload it.
                        </p>
                    </div>
                    @include('user.kyc._form')
                @endif
            @else
                @include('user.kyc._form')
            @endif
        </div>
    </div>
</div>
