<h5>List Address for Whitelisting</h5>
<form action="{{ route('wallet.address.store') }}" method="POST" id="addressForm">
    @csrf
    <div class="mb-3">
        <label for="form-text" class="form-label fs-14 text-dark">Address (USDTTRC20)</label>
        <input type="text" class="form-control" id="form-text" placeholder="Enter USDTTRC20 wallet Addres"
            name="address">
    </div>
    <button class="btn btn-primary" type="submit">
        <div class="spinner-border" style="display: none" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <span id="text">Submit</span>
    </button>
</form>
