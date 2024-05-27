<h6 class="fw-semibold mb-3 text-center">
    Photo :
</h6>
<form method="POST" action="{{ route('admin.profile.update.image') }}" enctype="multipart/form-data" class="d-flex flex-column align-items-center">
    @csrf
    <div class="d-flex flex-column align-items-center">
        <div class="mb-3">
            <span class="avatar avatar-xxl avatar-rounded">
                <img src="{{ $user->profile_picture }}" alt="Profile Picture" id="profile-img">
            </span>
        </div>
        <div class="mb-3">
            <input type="file" name="image" class="form-control" id="profile-change">
            @error('image')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Upload</button>
    </div>
</form>
