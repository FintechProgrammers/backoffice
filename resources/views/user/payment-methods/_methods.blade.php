<div class="p-sm-3 p-0">
    <div class="d-flex justify-content-between mb-3">
        <h6 class="mb-0">Payment Methods</h6>
        <a href="{{ route('payment-method.create') }}" class="btn btn-sm btn-primary">Add a New Card</a>
    </div>
    <div class="table-responsive">
        <table class="table table-borderless">
            <thead>
                <th>#</th>
                <th>Card</th>
                <th>Status</th>
            </thead>
            <tbody>
                <tr>
                    <td></td>
                    <td><span class="badge bg-success-transparent"><i
                                class="ri-check-fill align-middle me-1"></i>Active</span></td>
                    <td>
                        <div class="hstack gap-2 fs-15">
                            <a href="javascript:void(0);"
                                class="btn btn-icon btn-sm btn-primary-transparent rounded-pill">
                                <i class="ri-check-fill"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
