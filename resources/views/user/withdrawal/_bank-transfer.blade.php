<form action="{{ route('payout.bank.transfer',$bankTransferProvider->uuid) }}" class="payoutForm" method="POST">
    @csrf
    <div class="p-4">
        <div class="row gy-4">
            <div class="col-xl-12 form-group">
                <label for="amount" class="form-label">Amount</label>
                <input type="text" class="form-control" name="amount" id="amount" placeholder="Enter Amount"
                    min="1">
            </div>
            <div class="col-xl-12 form-group">
                <label for="amount" class="form-label">Address</label>
                <input type="text" class="form-control" name="address" id="amount"
                    placeholder="Enter distination address">
            </div>
        </div>
    </div>
    <div class="px-4 py-3 border-top border-block-start-dashed d-sm-flex justify-content-end">
        <button type="submit" class="btn btn-primary m-1">
            <div class="spinner-border" style="display: none" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <span id="text">Submit</span>
        </button>
    </div>
</form>
