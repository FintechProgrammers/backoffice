<div class="mb-3">
    <label for="form-text" class="form-label fs-14 text-dark">Name</label>
    <input type="text" class="form-control" id="form-text" value="{{ isset($provider) ? $provider->name : '' }}"
        placeholder="Enter package name" name="name">
</div>
<div class="form-check form-check-lg form-switch mb-3">
    <input class="form-check-input" type="checkbox" role="switch" id="switch-lg" name="can_payin"
        @checked(isset($provider) && $provider->can_payin)>
    <label class="form-check-label" for="switch-lg">Handles Payin</label>
</div>
<div class="form-check form-check-lg form-switch mb-3">
    <input class="form-check-input" type="checkbox" role="switch" id="switch-lg" name="can_payout"
        @checked(isset($provider) && $provider->can_payout)>
    <label class="form-check-label" for="switch-lg">Handles Payout</label>
</div>
<div class="form-check form-check-lg form-switch mb-3">
    <input class="form-check-input" type="checkbox" role="switch" id="switch-lg" name="is_crypto"
        @checked(isset($provider) && $provider->is_crypto)>
    <label class="form-check-label" for="switch-lg">Handles Crypto Payments</label>
</div>
<div class="text-center" id="photoContent">
    <img src="{{ isset($provider) ? $provider->image_url : asset('assets/images/default.jpg') }}"
        class="img-fluid rounded" width="150px" height="50px" alt="...">
</div>
<div class="mb-3">
    <label for="form-text" class="form-label fs-14 text-dark">Minimum Amount</label>
    <input type="number" step="any" class="form-control" id="form-text"
        value="{{ isset($provider) ? $provider->min_amount : '' }}" min="0" placeholder="Enter mininum amount"
        name="mininum_amount">
</div>
<div class="mb-3">
    <label for="form-text" class="form-label fs-14 text-dark">Maximum Amount</label>
    <input type="number" step="any" class="form-control" id="form-text"
        value="{{ isset($provider) ? $provider->max_amount : '' }}" min="0" placeholder="Enter maxinum amount"
        name="maximum_amount">
</div>
<div class="mb-3">
    <label for="form-text" class="form-label fs-14 text-dark">Charge</label>
    <input type="number" step="any" class="form-control" id="form-text"
        value="{{ isset($provider) ? $provider->charge : '' }}" min="0" placeholder="charge" name="charge">
</div>
<div class="mb-3">
    <label for="form-text" class="form-label fs-14 text-dark">Image</label>
    <input type="file" name="image" id="photo" class="form-control">
</div>
<button class="btn btn-primary" type="submit">
    <div class="spinner-border" style="display: none" role="status">
        <span class="sr-only">Loading...</span>
    </div>
    <span id="text">Submit</span>
</button>
