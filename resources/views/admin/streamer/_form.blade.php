<div class="mb-3">
    <label for="form-text1" class="form-label fs-14 text-dark">First name</label>
    <div class="input-group">
        <div class="input-group-text"><i class="ri-user-line"></i></div>
        <input type="text" class="form-control" name="first_name"
            value="{{ isset($streamer) ? $streamer->first_name : '' }}" id="form-text1" placeholder="Enter first name">
    </div>
</div>
<div class="mb-3">
    <label for="form-text1" class="form-label fs-14 text-dark">Last name</label>
    <div class="input-group">
        <div class="input-group-text"><i class="ri-user-line"></i></div>
        <input type="text" class="form-control" name="last_name" id="form-text1"
            value="{{ isset($streamer) ? $streamer->last_name : '' }}" placeholder="Enter last name">
    </div>
</div>
<div class="mb-3">
    <label for="form-text1" class="form-label fs-14 text-dark">Username</label>
    <div class="input-group">
        <div class="input-group-text"><i class="ri-mail-line"></i></div>
        <input type="text" class="form-control" id="form-text1" name="username"
            value="{{ isset($streamer) ? $streamer->username : '' }}" placeholder="Enter username">
    </div>
</div>
<div class="mb-3">
    <label for="form-text1" class="form-label fs-14 text-dark">Email</label>
    <div class="input-group">
        <div class="input-group-text"><i class="ri-mail-line"></i></div>
        <input type="text" class="form-control" id="form-text1" name="email"
            value="{{ isset($streamer) ? $streamer->email : '' }}" placeholder="Enter email">
    </div>
</div>

<div class="mb-3">
    <label for="form-text" class="form-label fs-14 text-dark">Category</label>
    <div class="row" data-bs-spy="scroll" data-bs-offset="0" data-bs-smooth-scroll="true" tabindex="0">
        @foreach ($categories as $key => $item)
            <div class="col-xl-12">
                <div class="custom-toggle-switch d-flex align-items-center mb-4">
                    <input id="toggleswitch{{ $key }}" name="categories[]" value="{{ $item->id }}"
                        @checked(isset($streamerCategories) && isset($streamer) && in_array($item->id, $streamerCategories)) type="checkbox">
                    <label for="toggleswitch{{ $key }}" class="label-primary"></label><span
                        class="ms-3">{{ ucfirst($item->name) }}</span>
                </div>
            </div>
        @endforeach
    </div>
    <small>{{ __('Select Categories for Steamer') }}</small>
</div>

<button class="btn btn-primary" type="submit">
    <div class="spinner-border" style="display: none" role="status">
        <span class="sr-only">Loading...</span>
    </div>
    <span id="text">Submit</span>
</button>
