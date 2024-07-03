@extends('layouts.user.app')

@section('title', 'Withdrawal')

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">{{ __('Request Withdrawal') }}</h1>
    </div>
    <div class="container">

        <!-- Start::row-1 -->
        <div class="row">
            <div class="col-xl-9">
                <div class="card custom-card">
                    <div class="card-body p-0 product-checkout">
                        @if (!empty($cryptoProvider) || !empty($bankTransferProvider))
                            <ul class="nav nav-tabs tab-style-2 d-sm-flex d-block border-bottom border-block-end-dashed"
                                id="myTab1" role="tablist">
                                @if (!empty($bankTransferProvider))
                                    <li class="nav-item" role="presentation">
                                        <button
                                            class="nav-link {{ (empty($cryptoProvider) && !empty($bankTransferProvider)) || (!empty($bankTransferProvider) && !empty($cryptoProvider)) ? 'active' : '' }}"
                                            id="order-tab" data-bs-toggle="tab" data-bs-target="#order-tab-pane"
                                            type="button" role="tab" aria-controls="order-tab" aria-selected="true"><i
                                                class="ri-wallet-2-fill me-2 align-middle"></i>Bank
                                            Transfer</button>
                                    </li>
                                @endif
                                @if (!empty($cryptoProvider))
                                    <li class="nav-item" role="presentation">
                                        <button
                                            class="nav-link {{ !empty($cryptoProvider) && empty($bankTransferProvider) ? 'active' : '' }}"
                                            id="confirmed-tab" data-bs-toggle="tab" data-bs-target="#confirm-tab-pane"
                                            type="button" role="tab" aria-controls="confirmed-tab"
                                            aria-selected="false"><i class="ri-wallet-3-line me-2 align-middle"></i>Crypto
                                            Wallet</button>
                                    </li>
                                @endif
                            </ul>
                            {{-- <div class="row gy-3">
                                <p class="fs-15 fw-semibold mb-1">Select Payout Method :</p>
                                @if (!empty($bankTransferProvider))
                                    <div class="col-xl-6">
                                        <a href="#">
                                            <div class="form-check payment-card-container mb-0 lh-1">
                                                <div class="form-check-label">
                                                    <div
                                                        class="d-sm-flex d-block align-items-center justify-content-between">
                                                        <div class="me-2 lh-1">
                                                            <span class="avatar avatar-md">
                                                                <img src="{{ asset('assets/images/debit-card.webp') }}"
                                                                    alt="">
                                                            </span>
                                                        </div>
                                                        <div class="saved-card-details">
                                                            <p class="mb-0 fw-semibold">Bank Transfer</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endif
                                @if (!empty($cryptoProvider))
                                    <div class="col-xl-6">
                                        <a href="#">
                                            <div class="form-check payment-card-container mb-0 lh-1">
                                                <div class="form-check-label">
                                                    <div
                                                        class="d-sm-flex d-block align-items-center justify-content-between">
                                                        <div class="me-2 lh-1">
                                                            <span class="avatar avatar-md">
                                                                <img src="{{ asset('assets/images/crypto-wallet.png') }}"
                                                                    alt="">
                                                            </span>
                                                        </div>
                                                        <div class="saved-card-details">
                                                            <p class="mb-0 fw-semibold">Crypto Wallet</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>

                                    </div>
                                @endif
                            </div> --}}

                            <div class="tab-content" id="myTabContent">
                                @if (!empty($bankTransferProvider))
                                    <div class="tab-pane fade {{ (empty($cryptoProvider) && !empty($bankTransferProvider)) || (!empty($bankTransferProvider) && !empty($cryptoProvider)) ? 'show active' : '' }} border-0 p-0"
                                        id="order-tab-pane" role="tabpanel" aria-labelledby="order-tab-pane" tabindex="0">
                                        @include('user.withdrawal._bank-transfer')
                                    </div>
                                @endif
                                @if (!empty($cryptoProvider))
                                    <div class="tab-pane fade border-0 p-0 {{ !empty($cryptoProvider) && empty($bankTransferProvider) ? 'show active' : '' }}"
                                        id="confirm-tab-pane" role="tabpanel" aria-labelledby="confirm-tab-pane"
                                        tabindex="0">
                                        @include('user.withdrawal._crypto-wallet')
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="d-flex flex-column align-items-center h-50">
                                <h4>Withdrawals not available at the momment </h4>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
            <div class="col-xl-3">
                @include('user.withdrawal._wallet-summary')
            </div>
        </div>
        <!--End::row-1 -->
    </div>
@endsection
@push('scripts')
    @include('user.withdrawal.scripts._submit_form')
@endpush
