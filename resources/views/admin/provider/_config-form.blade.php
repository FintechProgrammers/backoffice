<h5>Provider Configuration</h5>
<form action="{{ route('admin.provider.config.post', $provider->uuid) }}" method="POST">
    @csrf

    @if ($provider->short_name === 'nowpayment')
        <h6 class="text-capitalize">Setup {{ $provider->name }}</h6>
        {{-- <div class="mb-3">
            <label for="form-text" class="form-label fs-14 text-dark">API KEY</label>
            <input type="password" class="form-control" id="form-text" value="{{ !empty($config) ? $config->api_key : '' }}"
                placeholder="Enter Api Key" name="api_key">
        </div>
        <div class="mb-3">
            <label for="form-text" class="form-label fs-14 text-dark">API SECRET</label>
            <input type="password" class="form-control" id="form-text"
                value="{{ !empty($config) ? $config->secret : '' }}" placeholder="Enter Api Secret" name="secret">
        </div>
        <div class="mb-3">
            <label for="form-text" class="form-label fs-14 text-dark">IPN KEY</label>
            <input type="password" class="form-control" id="form-text"
                value="{{ !empty($config) ? $config->webhook_secret : '' }}" placeholder="Enter IPN KEY"
                name="webhook_secret">
        </div> --}}
        <div class="mb-3">
            <label for="form-text" class="form-label fs-14 text-dark">EMAIL</label>
            <input type="password" class="form-control" id="form-text"
                value="{{ !empty($config) ? $config->username : '' }}" placeholder="Enter Email" name="username">
        </div>
        <div class="mb-3">
            <label for="form-text" class="form-label fs-14 text-dark">PASSWORD</label>
            <input type="password" class="form-control" id="form-text"
                value="{{ !empty($config) ? $config->password : '' }}" placeholder="Enter Password" name="password">
        </div>

        <button class="btn btn-primary" type="submit">
            <div class="spinner-border" style="display: none" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <span id="text">Submit</span>
        </button>
    @else
        <div class="alert alert-info">
            No Configuration Available
        </div>
    @endif
</form>
