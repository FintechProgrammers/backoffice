<div class="mb-3">
    <label for="form-text" class="form-label fs-14 text-dark">Name</label>
    <input type="text" class="form-control" id="form-text" value="{{ isset($category) ? $category->name : '' }}"
        placeholder="Enter catogory name" name="name">
</div>
<div class="mb-3">
    <label for="form-text" class="form-label fs-14 text-dark">Type</label>
    <select name="type" class="form-select" id="">
        <option value="">--select-type--</option>
        @foreach (\App\Models\Category::TYPES as $item)
            <option value="{{ $item }}" @selected(isset($category) && $category->type === $item)>{{ $item }}</option>
        @endforeach
    </select>
</div>
<div class="text-center" id="photoContent">
    <img src="{{ isset($category) ? $category->photo : asset('assets/images/default.jpg') }}" class="img-fluid rounded"
        width="150px" height="50px" alt="...">
</div>
<div class="mb-3">
    <label for="form-text" class="form-label fs-14 text-dark">Icon</label>
    <input type="file" name="photo" id="photo" class="form-control">
</div>
<button class="btn btn-primary" type="submit">
    <div class="spinner-border" style="display: none" role="status">
        <span class="sr-only">Loading...</span>
    </div>
    <span id="text">Submit</span>
</button>
