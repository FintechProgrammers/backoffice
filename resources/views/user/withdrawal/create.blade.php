@extends('layouts.user.app')

@section('title', 'Withdrawal')

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">{{ __('Request Withdrawal') }}</h1>
    </div>
    <div class="container">
        @include('user.withdrawal._wallet-summary')
        <div class="card custom-card">
            <div class="card-body">
                <div class="row">
                    <div class="col-xl-5 col-lg-5 col-md-6 border-end">
                        <form action="{{ route('withdrawal.store') }}" class="payoutForm" method="POST" id="withdrawalForm">
                            @csrf
                            <input type="hidden" name="token" id="tokenInput">
                            <div class="mb-3 form-group">
                                <label for="amount">Amount</label>
                                <input type="text" class="form-control" placeholder="0.00" name="amount" id="amount"
                                    required>
                            </div>
                            <div class="mb-3 form-group">
                                <label for="payment-method">Payment Method</label>
                                <select class="form-control" name="payment_method" id="payment-method" required>
                                    <option value="">--select--method--</option>
                                    @if (!empty($bankTransferProvider))
                                        <option value="bank_transfer">Bank Transfer</option>
                                    @endif
                                    @if (!empty($cryptoProvider))
                                        <option value="crypto">Crypto</option>
                                    @endif
                                </select>
                            </div>
                            <div class="mb-3 form-group" id="crypto" style="display: none">
                                <label for="wallet_address" class="form-label">Address (USDTTRC20)</label>
                                <input type="text" class="form-control" name="wallet_address" id="wallet_address"
                                    placeholder="Enter destination address">
                            </div>
                            <div class="px-4 py-3 border-top border-block-start-dashed d-sm-flex">
                                <button type="submit" class="btn btn-primary m-1 trigerModal" data-bs-toggle="modal"
                                    data-bs-target="#primaryModal" id="continue"
                                    data-url="{{ route('withdrawal.send.otp') }}" disabled>
                                    Continue
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="col-xl-7 col-lg-7 col-md-6">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item fw-semibold">Minimum withdrawable amount:<span
                                    class="ms-1 text-muted fw-normal d-inline-block">${{ number_format(!empty(systemSettings()) ? systemSettings()->minimum_withdrawal_amount : 0, 2) }}</span>
                            </li>
                            <li class="list-group-item fw-semibold">Maximum withdrawable amount:<span
                                    class="ms-1 text-muted fw-normal d-inline-block">${{ number_format(!empty(systemSettings()) ? systemSettings()->maximum_withdrawal_amount : 0, 2) }}</span>
                            </li>
                            <li class="list-group-item fw-semibold">Charge:<span
                                    class="ms-1 text-muted fw-normal d-inline-block text-red-500">${{ number_format(!empty(systemSettings()) ? systemSettings()->withdrawal_fee : 0, 2) }}</span>
                            </li>
                            {{-- <li class="list-group-item fw-semibold">Current Rate:<span
                                    class="ms-1 text-muted fw-normal d-inline-block">1 USD = 1USD</span></li> --}}
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('user.withdrawal.scripts._submit_form')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('withdrawalForm');
            const continueButton = document.getElementById('continue');
            const amountInput = document.getElementById('amount');
            const paymentMethodSelect = document.getElementById('payment-method');
            const walletAddressInput = document.getElementById('wallet_address');
            const cryptoSection = document.getElementById('crypto');

            function validateForm() {
                const amount = amountInput ? amountInput.value.trim() : '';
                const paymentMethod = paymentMethodSelect ? paymentMethodSelect.value.trim() : '';
                const walletAddress = walletAddressInput ? walletAddressInput.value.trim() : '';

                if (amount && paymentMethod && (paymentMethod !== 'crypto' || walletAddress)) {
                    continueButton.disabled = false;
                } else {
                    continueButton.disabled = true;
                }
            }

            form.addEventListener('input', validateForm);

            paymentMethodSelect.addEventListener('change', function() {
                if (this.value === 'crypto') {
                    cryptoSection.style.display = 'block';
                    walletAddressInput.setAttribute('required', 'required');
                } else {
                    cryptoSection.style.display = 'none';
                    walletAddressInput.removeAttribute('required');
                }
                validateForm(); // Validate form after showing/hiding the crypto section
            });

            // Initial validation in case fields are pre-filled
            validateForm();
        });
    </script>
@endpush
