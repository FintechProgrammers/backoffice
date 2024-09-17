<h6>Setup Nexio ID</h6>
<form method="POST" action="{{ route('admin.users.nexioSetUp.post', $user->uuid) }}">
    @csrf
    <div class="mb-3">
        <label for="form-text1" class="form-label fs-14 text-dark">Nexio ID</label>
        <div class="input-group">
            <div class="input-group-text"><i class="ri-user-line"></i></div>
            <input type="text" class="form-control" name="nexio_id" id="form-text1"
                value="{{ optional($user->nexio)->recipient_id }}" placeholder="Enter nexio id">
        </div>
    </div>
    <button class="btn btn-primary" type="submit">
        <div class="spinner-border" style="display: none" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <span id="text">Submit</span>
    </button>
</form>
