<div class="row">
    <h4 class="text-center mb-3">Select Method for Payment </h4>
    @forelse ($providers as $item)
        <div class="col-lg-6 mb-3">
            <a href="#" data-="">
                <div class="card custom-card text-center card-bg-light">
                    <div class="card-body pt-5">
                        <span class="avatar avatar-xl avatar-rounded me-2 mb-2">
                            <img src="{{ asset('assets/images/debit-card.webp') }}" alt="img">
                        </span>
                        <div class="fw-semibold fs-16 text-uppercase">Pay with Card</div>
                    </div>
                </div>
            </a>
        </div>
    @empty
    @endforelse
</div>
