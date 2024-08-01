<div class="p-sm-3 p-0">
    <div class="d-flex justify-content-between mb-3">
        <h6 class="mb-0">Payment Methods</h6>
        <a href="#" data-url="{{ route('payment-method.create') }}" class="btn btn-sm btn-primary" id="add-card">
            <div class="spinner-border" style="display: none" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <span id="text">Add a New Card</span>
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-borderless">
            <thead>
                <th>#</th>
                <th>Card</th>
                <th></th>
                <th></th>
            </thead>
            <tbody>
                @forelse ($user->paymentMethods as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <b>xxxx-{{ $item->last4 }}</b> - <span
                                class="text-uppercase bold">{{ $item->card_brand }}</span>
                        </td>
                        <td>

                        </td>
                        <td>
                            @if (!$item->is_default)
                                <a href="#" class="btn btn-primary btn-sm make-default"
                                    data-url="{{ route('payment-method.mark.default', $item->uuid) }}">
                                    <div class="spinner-border" style="display: none" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                    <span id="text">Make Default</span>
                                </a>
                            @else
                                <div class="hstack gap-2 fs-15">
                                    <a href="javascript:void(0);"
                                        class="btn btn-icon btn-sm btn-success-transparent rounded-pill">
                                        <i class="ri-check-fill"></i>
                                    </a>
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center"><span class="text-warning">no methods created</span></td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
