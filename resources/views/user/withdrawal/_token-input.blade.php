<div class="d-flex flex-column align-items-center justify-content-center">
    <div class="pin-card-text text-center">
        <h5 class="make-text-medium mb-1">Transaction Token</h5>
        <p class="ft-md">{{ __('check your email to token to complete your withdrawal request.') }}</p>

        <div class="pin-box-grid-2 my-3" id="tokenContainer">
            <input type="text"
                class="form-control form-control-lg make-text-bold border-radius-12 text-center token-input"
                id="token1" required maxlength="1" />

            <input type="text"
                class="form-control form-control-lg make-text-bold border-radius-12 text-center token-input"
                id="token2" required maxlength="1" />

            <input type="text"
                class="form-control form-control-lg make-text-bold border-radius-12 text-center token-input"
                id="token3" required maxlength="1" />

            <input type="text"
                class="form-control form-control-lg make-text-bold border-radius-12 text-center token-input"
                id="token4" required maxlength="1" />
        </div>

        <button type="submit" class="btn btn-primary m-1" id="procees" disabled>
            <div class="spinner-border" style="display: none" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <span id="text">Proceed</span>
        </button>
    </div>
</div>
