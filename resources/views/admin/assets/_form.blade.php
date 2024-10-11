<div class="mb-3">
    <label for="form-text" class="form-label fs-14 text-dark">Category</label>
    <select name="category" class="form-select" id="">
        <option value="">--select-category--</option>
        @foreach ($categories as $item)
            <option value="{{ $item->id }}" @selected(isset($asset) && $asset->category_id === $item->id)>{{ $item->name }}</option>
        @endforeach
    </select>
</div>
<div class="mb-3">
    <label for="form-text" class="form-label fs-14 text-dark">Name</label>
    <input type="text" class="form-control" id="form-text" value="{{ isset($asset) ? $asset->name : '' }}"
        placeholder="Enter asset name" name="asset_name">
</div>

<div class="mb-3">
    <label for="form-text" class="form-label fs-14 text-dark">Symbol</label>
    <input type="text" class="form-control" id="form-text" value="{{ isset($asset) ? $asset->symbol : '' }}"
        placeholder="Enter symbol" name="symbol">
</div>


<div class="text-center" id="photoContent">
    <img src="{{ isset($asset) ? $asset->image : asset('assets/images/default.jpg') }}" class="img-fluid rounded"
        width="150px" height="50px" alt="...">
</div>
<div class="mb-3">
    <label for="form-text" class="form-label fs-14 text-dark">Photo</label>
    <input type="file" name="photo" id="photo" class="form-control">
</div>
<button class="btn btn-primary" type="submit">
    <div class="spinner-border" style="display: none" role="status">
        <span class="sr-only">Loading...</span>
    </div>
    <span id="text">Submit</span>
</button>
