<h6>Change Email</h6>
<form method="POST" action="{{ route('admin.users.change-email.post', $user->uuid) }}">
    @csrf
    <div class="mb-3">
        <label for="form-text1" class="form-label fs-14 text-dark">Email</label>
        <div class="input-group">
            <div class="input-group-text"><i class="ri-user-line"></i></div>
            <input type="text" class="form-control" name="email" id="form-text1" value=""
                placeholder="Enter Email">
        </div>
    </div>
    <button class="btn btn-primary" type="submit">
        <div class="spinner-border" style="display: none" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <span id="text">Submit</span>
    </button>
</form>
