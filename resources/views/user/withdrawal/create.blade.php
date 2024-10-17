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
                        <form action="{{ route('wallet.store') }}" class="payoutForm" method="POST" id="withdrawalForm">
                            @csrf
                            <input type="hidden" name="token" id="tokenInput">
                            <input type="hidden" name="provider_id" value="" id="provider">
                            <div class="mb-3 form-group">
                                <label for="payment-method">Payment Method</label>
                                <select class="form-control" name="payment_method" id="payment-method" required>
                                    <option value="">--select--method--</option>
                                    @foreach ($paymentMethods as $item)
                                        <option value="{{ $item->uuid }}" data-type="{{ $item->type }}"
                                            data-min-amount="{{ number_format($item->min_amount, 2) }}"
                                            data-max-amount="{{ number_format($item->max_amount, 2) }}"
                                            data-charge-fee="{{ number_format($item->charge, 2) }}">
                                            {{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 form-group">
                                <label for="amount">Amount</label>
                                <input type="text" class="form-control" placeholder="0.00" name="amount" id="amount"
                                    required>
                            </div>

                            <div class="mb-3 form-group" id="crypto" style="display: none">
                                <label for="wallet_address" class="form-label">Address (USDTTRC20)</label>
                                <input type="text" class="form-control" name="wallet_address" id="wallet_address"
                                    placeholder="Enter destination address">
                            </div>
                            <div class="px-4 py-3 border-top border-block-start-dashed d-sm-flex">
                                <button type="submit" class="btn btn-primary m-1 trigerModal" data-bs-toggle="modal"
                                    data-bs-target="#primaryModal" id="continue" data-url="{{ route('wallet.send.otp') }}"
                                    disabled>
                                    Continue
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="col-xl-7 col-lg-7 col-md-6">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item fw-semibold">Minimum withdrawable amount:<span
                                    class="ms-1 text-muted fw-normal d-inline-block">$<span
                                        id="minAmount">0.00</span></span>
                            </li>
                            <li class="list-group-item fw-semibold">Maximum withdrawable amount:<span
                                    class="ms-1 text-muted fw-normal d-inline-block">$<span
                                        id="maxAmount">0.00</span></span>
                            </li>
                            <li class="list-group-item fw-semibold">Charge:<span
                                    class="ms-1 text-muted fw-normal d-inline-block text-red-500">$<span
                                        id="charge">0.00</span></span>
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
                const amount = parseFloat(amountInput.value.trim());
                const paymentMethod = paymentMethodSelect ? paymentMethodSelect.value.trim() : '';
                const walletAddress = walletAddressInput ? walletAddressInput.value.trim() : '';
                const selectedOption = paymentMethodSelect.options[paymentMethodSelect.selectedIndex];
                const dataType = selectedOption ? selectedOption.getAttribute('data-type') : '';

                // Get the min and max amounts for the selected payment method
                const minAmount = parseFloat(selectedOption.getAttribute('data-min-amount'));
                const maxAmount = parseFloat(selectedOption.getAttribute('data-max-amount'));

                // Validate if the amount falls within the specified range
                const isAmountValid = !isNaN(amount) && (amount < minAmount || (maxAmount > 0 && amount >
                    maxAmount));

                // Disable the button if the amount is invalid or if payment method or wallet address is missing
                if (isAmountValid && paymentMethod && (dataType !== 'crypto' || walletAddress)) {
                    continueButton.disabled = false;
                } else {
                    continueButton.disabled = true;
                }
            }

            form.addEventListener('input', validateForm);

            paymentMethodSelect.addEventListener('change', function() {

                var selectedOption = this.options[this.selectedIndex];
                var dataType = selectedOption.getAttribute('data-type');
                var minAmount = selectedOption.getAttribute('data-min-amount');
                var maxAmount = selectedOption.getAttribute('data-max-amount');
                var chargeFee = selectedOption.getAttribute('data-charge-fee');

                if (dataType === 'crypto') {
                    cryptoSection.style.display = 'block';
                    walletAddressInput.setAttribute('required', 'required');
                } else {
                    cryptoSection.style.display = 'none';
                    walletAddressInput.removeAttribute('required');
                }

                $('#minAmount').html(minAmount)
                $('#maxAmount').html(maxAmount)
                $('#charge').html(chargeFee)

                $('#provider').val(this.value);

                validateForm(); // Validate form after showing/hiding the crypto section
            });

            const errorMessage = document.createElement('div');
            errorMessage.classList.add('text-danger'); // Bootstrap class for red text
            amountInput.parentNode.appendChild(errorMessage); // Add error message after the input field

            // Add validation message display (optional)
            amountInput.addEventListener('input', function() {
                const amount = parseFloat(amountInput.value.trim());
                const selectedOption = paymentMethodSelect.options[paymentMethodSelect.selectedIndex];
                const minAmount = parseFloat(selectedOption.getAttribute('data-min-amount'));
                const maxAmount = parseFloat(selectedOption.getAttribute('data-max-amount'));

                // Check if amount is within the valid range
                if (!isNaN(amount) && (amount < minAmount || (maxAmount > 0 && amount > maxAmount))) {
                    // Set custom message below input
                    if (maxAmount > 0) {
                        errorMessage.textContent =
                            `Amount must be between $${minAmount} and $${maxAmount}.`;
                    } else {
                        errorMessage.textContent = `Amount must not be less than $${minAmount}.`;
                    }
                    continueButton.disabled = true;
                } else {
                    errorMessage.textContent = ''; // Clear message
                    continueButton.disabled = false;
                }
            });

            // Initial validation in case fields are pre-filled
            validateForm();
        });
    </script>
@endpush
