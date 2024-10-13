<div class="mb-3">
    <label for="form-text" class="form-label fs-14 text-dark">Name</label>
    <input type="text" class="form-control" id="form-text" value="{{ isset($service) ? $service->name : '' }}"
        placeholder="Enter package name" name="name">
</div>
<div class="row">
    <div class="col-lg-6">
        <div class="mb-3">
            <label for="form-text" class="form-label fs-14 text-dark">Price ($)</label>
            <input type="number" min="0" step="any" class="form-control" id="form-text"
                value="{{ isset($service) ? $service->price : '' }}" placeholder="Enter package price" name="price">
        </div>
    </div>
    <div class="col-lg-6">
        <div class="mb-3">
            <label for="form-text" class="form-label fs-14 text-dark">BV</label>
            <input type="number" min="0" step="any" class="form-control" id="form-text"
                value="{{ isset($service) ? $service->bv_amount : '' }}" placeholder="Enter BV" name="bv">
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-6">
        <div class="mb-3">
            <label for="form-text" class="form-label fs-14 text-dark">Duration</label>
            <input type="number" class="form-control" id="form-text" placeholder="Enter duration"
                value="{{ isset($service) ? convertDaysToUnit($service->duration, $service->duration_unit) : '' }}"
                name="duration">
        </div>
    </div>
    <div class="col-lg-6">
        <div class="mb-3">
            <label for="form-text" class="form-label fs-14 text-dark">Duration Unit</label>
            <select id="inputCountry" class="form-select" name="duration_unit">
                <option value="">--select--</option>
                @foreach (durationUnit() as $item)
                    <option value="{{ $item }}" @selected(isset($service) && $service->duration_unit == $item)>{{ Str::upper($item) }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<div class="col-lg-12">
    <div class="mb-3">
        <label for="packageType" class="form-label fs-14 text-dark">Package Type</label>
        <select id="packageType" class="form-select" name="package_type">
            <option value="">--select--</option>
            <option value="service" @selected(isset($service) && !$service->ambassadorship)>Service</option>
            <option value="ambassadorship" @selected(isset($service) && $service->ambassadorship)>Ambassadorship</option>
        </select>
    </div>
</div>
<div class="mb-3" id="products"
    style="display: {{ isset($service) && !$service->ambassadorship ? 'block' : 'none' }}">
    <label for="form-text" class="form-label fs-14 text-dark">Products</label>
    <div class="row" data-bs-spy="scroll" data-bs-offset="0" data-bs-smooth-scroll="true" tabindex="0">
        @foreach ($products as $key => $item)
            <div class="col-xl-12">
                <div class="custom-toggle-switch d-flex align-items-center mb-4">
                    <input id="toggleswitch{{ $key }}" name="products[]" value="{{ $item->id }}"
                        @checked(isset($serviceProducts) && isset($service) && in_array($item->id, $serviceProducts)) type="checkbox">
                    <label for="toggleswitch{{ $key }}" class="label-primary"></label><span
                        class="ms-3">{{ ucfirst($item->name) }}</span>
                </div>
            </div>
        @endforeach
    </div>
    <small>{{ __('Select product the package is serviced for') }}</small>
</div>
<div class="mb-3">
    <label for="form-text" class="form-label fs-14 text-dark">Description</label>
    <textarea class="form-control" name="description">
        {{ isset($service) ? $service->description : '' }}
    </textarea>
</div>
<div class="mb-3">
    <h6>Streamers</h6>
    <hr />
    <select class="form-control sponsors" data-trigger name="streamers[]" multiple>
        <option value="">Select</option>
        @foreach ($streamers as $item)
            <option value="{{ $item->id }}" data-streamerid="{{ $item->id }}"
                data-profile="{{ $item->profile_picture }}" @if (isset($service) && in_array($item->id, $service->streamers->pluck('id')->toArray())) selected @endif>
                {{ $item->full_name }}
            </option>
        @endforeach
    </select>
</div>
<hr />
<div class="row">
    <div class="col-lg-4">
        <div class="text-center" id="photoContent">
            <img src="{{ isset($service) ? $service->image : asset('assets/images/default.jpg') }}"
                class="img-fluid rounded" width="150px" height="50px" alt="...">
        </div>
        <div class="mb-3">
            <label for="form-text" class="form-label fs-14 text-dark">Icon</label>
            <input type="file" name="icon" id="photo" class="form-control">
        </div>
    </div>
    <div class="col-lg-4">
        <div class="text-center" id="bannerContent">
            <img src="{{ isset($service) ? $service->banner_url : asset('assets/images/default.jpg') }}"
                class="img-fluid rounded" width="150px" height="50px" alt="...">
        </div>
        <div class="mb-3">
            <label for="form-text" class="form-label fs-14 text-dark">Banner</label>
            <input type="file" name="banner" id="banner" class="form-control">
        </div>
    </div>
    <div class="col-lg-4">
        <div class="text-center" id="productImageContent">
            <img src="{{ isset($service) ? $service->product_image_url : asset('assets/images/default.jpg') }}"
                class="img-fluid rounded" width="150px" height="50px" alt="...">
        </div>
        <div class="mb-3">
            <label for="form-text" class="form-label fs-14 text-dark">Product Image</label>
            <input type="file" name="product_image" id="product-image" class="form-control">
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-6">
        <div class="form-check form-check-lg form-switch mb-3">
            <input class="form-check-input" type="checkbox" role="switch" id="switch-lg" name="auto_renewal"
                @checked(isset($service) && $service->auto_renewal)>
            <label class="form-check-label" for="switch-lg">Auto Renewal</label>
        </div>

    </div>
    <div class="col-lg-6">
        <div class="form-check form-check-lg form-switch mb-3">
            <input class="form-check-input" type="checkbox" role="switch" id="switch-lg" name="is_published"
                @checked(isset($service) && $service->is_published)>
            <label class="form-check-label" for="switch-lg">Publish</label>
        </div>
    </div>
</div>


<button class="btn btn-primary" type="submit">
    <div class="spinner-border" style="display: none" role="status">
        <span class="sr-only">Loading...</span>
    </div>
    <span id="text">Submit</span>
</button>
