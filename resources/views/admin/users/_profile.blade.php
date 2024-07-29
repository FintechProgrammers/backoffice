<form action="{{ route('admin.users.update', $user->uuid) }}" method="POST" id="update-profile">
    @csrf
    <div class="row">
        <div class="mb-3 col-lg-6">
            <label for="form-text1" class="form-label fs-14 text-dark">First Name</label>
            <div class="input-group">
                <div class="input-group-text"><i class="ri-user-line"></i></div>
                <input type="text" class="form-control" name="first_name" id="form-text1"
                    value="{{ $user->first_name }}" placeholder="Enter First">
            </div>
        </div>
        <div class="mb-3 col-lg-6">
            <label for="form-text1" class="form-label fs-14 text-dark">Last Name</label>
            <div class="input-group">
                <div class="input-group-text"><i class="ri-user-line"></i></div>
                <input type="text" class="form-control" name="last_name" id="form-text1"
                    value="{{ $user->last_name }}" placeholder="Enter Last Name">
            </div>
        </div>
    </div>
    <div class="mb-3">
        <label for="form-text1" class="form-label fs-14 text-dark">Username</label>
        <div class="input-group">
            <div class="input-group-text">@</div>
            <input type="text" class="form-control" name="username" id="form-text1" value="{{ $user->username }}"
                placeholder="Enter username" readonly>
        </div>
    </div>
    <div class="mb-3">
        <label for="form-text1" class="form-label fs-14 text-dark">Email</label>
        <div class="input-group">
            <div class="input-group-text"><i class="ri-mail-line"></i></div>
            <input type="text" class="form-control" id="form-text1" name="email" value="{{ $user->email }}"
                placeholder="Enter email" readonly>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-4 mb-3">
            <label for="">Country</label>
            <x-country-select value="{{ $user->userProfile->country_code }}" />
        </div>
        <div class="col-xl-4 mb-3">
            <label for="">State</label>
            <input type="text" name="state" class="form-control" placeholder="State"
                value="{{ $user->userProfile->state }}" />
        </div>
        <div class="col-xl-4 mb-3">
            <label for="">City</label>
            <input type="text" name="city" class="form-control" placeholder="City"
                value="{{ $user->userProfile->city }}" />
        </div>
    </div>
    <div class="row">
        <div class="col-xl-6 mb-3">
            <label for="">Date Of Birth</label>
            <input type="date" name="date_of_birth" class="form-control" placeholder="Date of Birth"
                value="{{ $user->userProfile->date_of_birth }}" />
        </div>
        <div class="col-xl-6 mb-3">
            <label for="">Zip Code</label>
            <input type="number" name="zip_code" class="form-control" placeholder="Zip code"
                value="{{ $user->userProfile->zip_code }}" />
        </div>
    </div>
    <div class="col-lg-12 mb-3">
        <label class="form-label">Address:</label>
        <textarea class="form-control" name="address">{{ $user->userProfile->address }}</textarea>
    </div>
    <button class="btn btn-primary" type="submit">
        <div class="spinner-border" style="display: none" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <span id="text">Submit</span>
    </button>
</form>
