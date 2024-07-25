<div class="mb-3">
    <label for="country">Country</label>
    <select class="country-select form-control" data-trigger name="country" required>
        <option value="">Select</option>
        @foreach ($countries as $item)
            <option value="{{ $item->iso2 }}" data-countryName="{{ $item->name }}">{{ $item->name }}</option>
        @endforeach
    </select>
    <div class="invalid-feedback">Please select a country.</div>
</div>
<input type="hidden" name="referral_id" value="{{ isset($ambassador) ? $ambassador->uuid : auth()->user()->uuid }}" />
<div class="row mb-3">
    <div class="col-lg-6">
        <label for="first_name">First Name</label>
        <input type="text" class="form-control" name="first_name" required>
    </div>
    <div class="col-lg-6">
        <label for="last_name">Last Name</label>
        <input type="text" class="form-control" name="last_name" required>
    </div>
</div>
<div class="mb-3">
    <label for="username">Username</label>
    <input type="text" class="form-control" name="username" required>
</div>
<div class="mb-3">
    <label for="email">Email</label>
    <input type="email" class="form-control" name="email" required>
</div>
<div class="mb-3">
    <label for="phone_number">Phone Number</label>
    <input type="text" class="form-control" name="phone_number" required>
</div>
