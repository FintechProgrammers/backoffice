<div class="mb-3">
    <label for="form-text" class="form-label fs-14 text-dark">{{ __('Name') }}</label>
    <input type="text" class="form-control" id="form-text" value="{{ isset($commission) ? $commission->name : '' }}"
        placeholder="Enter plan name" name="name">
</div>
<div class="mb-3">
    <label for="">{{ __('Commision Percentage') }}</label>
    <div class="input-group ">
        <span class="input-group-text">%</span>
        <input min="0" step="any" name="commission_percentage" class="form-control"
            value="{{ isset($commission) ? $commission->commission_percentage : '' }}"
            aria-label="Amount (to the nearest dollar)">
    </div>
</div>
<div class="mb-3">
    <label for="">Commission Type</label>
    <select name="commission_type" class="form-control" id="commissionType">
        <option value="">--select--</option>
        <option value="direct" @selected(isset($commission) && $commission->is_direct ?: false)>Direct</option>
        <option value="indirect" @selected(isset($commission) && !$commission->is_direct ?: false)>Indirect</option>
    </select>
</div>
<div id="confBlock" style="display:{{ isset($commission) && !$commission->is_direct ? 'block' : 'none' }}">
    <div class="mb-3">
        <label for="form-text" class="form-label fs-14 text-dark">{{ __('Level') }}</label>
        <input type="number" class="form-control" id="form-text" min="1"
            value="{{ isset($commission) ? $commission->level : '' }}" placeholder="Enter plan name" name="level">
    </div>

    <div class="form-check form-check-lg form-switch mb-3">
        <input class="form-check-input" type="checkbox" role="switch" name="has_requirement"
            @checked(isset($commission) ? $commission->has_requirement: false) id="switch-lg">
        <label class="form-check-label" for="switch-lg">Activate Requirements</label>
    </div>
</div>
<div id="requirementBlock" style="display:{{ isset($commission) && !$commission->has_requirement ? 'block' : 'none' }}">
    <h6><b>Requirement Configuration</b></h6>
    <div class="mb-3">
        <label for="form-text" class="form-label fs-14 text-dark">{{ __('Required Direct BV') }}</label>
        <div class="input-group ">
            <span class="input-group-text">%</span>
            <input type="number" class="form-control" id="form-text" min="1"
                value="{{ isset($requirement) ? $requirement->direct_bv : '' }}" name="direct_bv">
        </div>
    </div>
    <div class="mb-3">
        <label for="form-text" class="form-label fs-14 text-dark">{{ __('Required Sponsored BV') }}</label>
        <div class="input-group ">
            <span class="input-group-text">%</span>
            <input type="number" class="form-control" id="form-text" min="1"
                value="{{ isset($requirement) ? $requirement->sponsored_bv : '' }}" name="sponsored_bv">
        </div>

    </div>
    <div class="mb-3">
        <label for="form-text" class="form-label fs-14 text-dark">{{ __('Required Sponsored Count') }}</label>
        <input type="number" class="form-control" id="form-text" min="1"
            value="{{ isset($requirement) ? $requirement->sponsored_count : '' }}" name="sponsored_count">
    </div>
</div>
<div class="d-grid gap-2 col-6 mx-auto mt-3">
    <button class="btn btn-primary btn-block" type="submit">
        <div class="spinner-border spinner-border-sm align-middle" style="display: none" aria-hidden="true">
            <span class="visually-hidden">Loading...</span>
        </div>
        <span id="text">Submit</span>
    </button>
</div>
