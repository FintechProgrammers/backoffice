    <div class="card custom-card overflow-hidden">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-center">
                <div class="filemanager-upgrade-storage d-flex align-items-center"
                    style="flex-direction: column; justify-content: space-between">

                    <img src="https://img.icons8.com/cute-clipart/64/diamond.png" alt="">

                    <div class="text-default text-center mt-3">
                        <span class="fs-15 fw-semibold">Pay ${{ systemSettings()->ambassador_fee }} and upgrade to
                            Ambassador
                            Package to get access to Full
                            system</span>
                    </div>
                    <div class="mt-3 d-grid">
                        <a type="button" href="{{ route('package.index') }}" {{-- data-bs-toggle="modal" data-bs-target="#primaryModal" --}}
                            class="btn btn-primary-gradient ">{{ __('Upgrade') }}</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
