<div class="mb-3">
    <label for="form-text1" class="form-label fs-14 text-dark">Fullname</label>
    <div class="input-group">
        <div class="input-group-text"><i class="ri-user-line"></i></div>
        <input type="text" class="form-control" name="fullname" id="form-text1"
            value="{{ $user->name }}" placeholder="Enter fullname">
    </div>
</div>
<div class="mb-3">
    <label for="form-text1" class="form-label fs-14 text-dark">Username</label>
    <div class="input-group">
        <div class="input-group-text">@</div>
        <input type="text" class="form-control" name="username" id="form-text1"
            value="{{ $user->username }}" placeholder="Enter username">
    </div>
</div>
<div class="mb-3">
    <label for="form-text1" class="form-label fs-14 text-dark">Email</label>
    <div class="input-group">
        <div class="input-group-text"><i class="ri-mail-line"></i></div>
        <input type="text" class="form-control" id="form-text1" name="email"
            value="{{ $user->email }}" placeholder="Enter email">
    </div>
</div>
