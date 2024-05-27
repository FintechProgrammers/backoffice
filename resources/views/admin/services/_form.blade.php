<div class="mb-3">
    <label for="form-text" class="form-label fs-14 text-dark">Name</label>
    <input type="text" class="form-control" id="form-text" value="{{ isset($service) ? $service->name : '' }}"
        placeholder="Enter service name" name="name">
</div>
<div class="mb-3">
    <label for="form-text" class="form-label fs-14 text-dark">Price</label>
    <input type="number" step="any" class="form-control" id="form-text"
        value="{{ isset($service) ? $service->price : '' }}" placeholder="Enter service price" name="price">
</div>
<div class="row">
    <div class="col-lg-6">
        <div class="mb-3">
            <label for="form-text" class="form-label fs-14 text-dark">Duration</label>
            <input type="number" class="form-control" id="form-text" placeholder="Enter duration"
                value="{{ isset($service) ? convertDaysToUnit($service->duration, $service->duration_unit) : '' }}" name="duration">
        </div>
    </div>
    <div class="col-lg-6">
        <div class="mb-3">
            <label for="form-text" class="form-label fs-14 text-dark">Duration Unit</label>
            <select id="inputCountry" class="form-select" name="duration_unit">
                <option value="">--select--</option>
                @foreach (durationUnit() as $item)
                    <option value="{{ $item }}" @selected(isset($service) && $service->duration_unit == $item)>{{ Str::upper($item) }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<div class="mb-3">
    <label for="form-text" class="form-label fs-14 text-dark">Description</label>
    <textarea class="form-control" name="description">
        {{ isset($service) ? $service->description : '' }}
    </textarea>
</div>

<div class="form-check form-check-lg form-switch mb-3">
    <input class="form-check-input" type="checkbox" role="switch" id="switch-lg" name="auto_renewal"
        @checked(isset($service) && $service->auto_renewal)>
    <label class="form-check-label" for="switch-lg">Auto Renewal</label>
</div>

<div class="form-check form-check-lg form-switch mb-3">
    <input class="form-check-input" type="checkbox" role="switch" id="switch-lg" name="is_published"
        @checked(isset($service) && $service->is_published)>
    <label class="form-check-label" for="switch-lg">Publish</label>
</div>

<button class="btn btn-primary" type="submit">
    <div class="spinner-border" style="display: none" role="status">
        <span class="sr-only">Loading...</span>
    </div>
    <span id="text">Submit</span>
</button>
