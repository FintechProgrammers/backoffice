@if (!empty($cryptoProvider) || !empty($bankTransferProvider))
    <ul class="nav nav-tabs tab-style-2 d-sm-flex d-block border-bottom border-block-end-dashed" id="myTab1"
        role="tablist">
        @if (!empty($bankTransferProvider))
            <li class="nav-item" role="presentation">
                <button
                    class="nav-link {{ (empty($cryptoProvider) && !empty($bankTransferProvider)) || (!empty($bankTransferProvider) && !empty($cryptoProvider)) ? 'active' : '' }}"
                    id="order-tab" data-bs-toggle="tab" data-bs-target="#order-tab-pane" type="button" role="tab"
                    aria-controls="order-tab" aria-selected="true"><i
                        class="ri-wallet-2-fill me-2 align-middle"></i>Bank
                    Transfer</button>
            </li>
        @endif
        @if (!empty($cryptoProvider))
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ !empty($cryptoProvider) && empty($bankTransferProvider) ? 'active' : '' }}"
                    id="confirmed-tab" data-bs-toggle="tab" data-bs-target="#confirm-tab-pane" type="button"
                    role="tab" aria-controls="confirmed-tab" aria-selected="false"><i
                        class="ri-wallet-3-line me-2 align-middle"></i>Crypto
                    Wallet</button>
            </li>
        @endif
    </ul>


    <div class="tab-content" id="myTabContent">
        @if (!empty($bankTransferProvider))
            <div class="tab-pane fade {{ (empty($cryptoProvider) && !empty($bankTransferProvider)) || (!empty($bankTransferProvider) && !empty($cryptoProvider)) ? 'show active' : '' }} border-0 p-0"
                id="order-tab-pane" role="tabpanel" aria-labelledby="order-tab-pane" tabindex="0">
                @include('user.withdrawal._bank-transfer')
            </div>
        @endif
        @if (!empty($cryptoProvider))
            <div class="tab-pane fade border-0 p-0 {{ !empty($cryptoProvider) && empty($bankTransferProvider) ? 'show active' : '' }}"
                id="confirm-tab-pane" role="tabpanel" aria-labelledby="confirm-tab-pane" tabindex="0">
                @include('user.withdrawal._crypto-wallet')
            </div>
        @endif
    </div>
@else
    <div class="d-flex flex-column align-items-center h-50">
        <h4>Withdrawals not available at the momment </h4>
    </div>
@endif
