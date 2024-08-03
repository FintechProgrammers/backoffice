@extends('layouts.user.app')

@section('title', 'Package')

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">{{ __('Package Details') }}</p>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-body p-0 product-checkout">
                    <h4 class="p-3 text-uppercase">Checkout</h4>
                    <form action="{{ route('checkout.store') }}" method="POST" id="checkoutForm">
                        @csrf
                        <input type="hidden" name="package_id" value="{{ $package->uuid }}">
                        @include('user.checkout.package')
                        @include('user.checkout.payment-methods')

                        @php
                            $user = Auth::user();
                            $subscription = $user->subscription;
                        @endphp

                        @if (is_null($subscription) ||
                                $subscription->service_id !== $package->id ||
                                ($subscription->service_id === $package->id && $subscription->end_date->isPast()))
                            <div class="px-4 py-3 border-top border-block-start-dashed d-sm-flex justify-content-between">
                                <button type="submit" class="btn btn-success-light m-1">
                                    <div class="spinner-border" style="display: none" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                    <span id="text">Continue To Payment <i
                                            class="bi bi-credit-card-2-front align-middle ms-2 d-inline-block"></i></span>
                                </button>
                            </div>
                        @endif

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    @include('partials.scripts.initiate-payin')
    <script>
        $(document).ready(function() {
            $('#checkoutForm').on('submit', function(event) {
                event.preventDefault(); // Prevent default form submission

                // Remove any existing error messages
                $('.error-message').remove();

                // Serialize form data
                var formData = $(this).serialize();

                const button = $(this).find('button')

                const spinner = button.find('.spinner-border')
                const buttonTest = button.find('#text')

                // Send AJAX request
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    beforeSend: function() {
                        buttonTest.hide()
                        spinner.show()
                        button.attr('disabled', true)
                    },
                    success: function(response) {
                        // Handle success response

                        setTimeout(function() {
                            location.href = response.data.route
                        }, 2000); // 2000 milliseconds = 2 seconds

                    },
                    error: function(xhr, status, error) {
                        spinner.hide()
                        buttonTest.show()
                        button.attr('disabled', false)
                        // Handle error response
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;

                            $.each(errors, function(field, messages) {
                                // Find the corresponding field
                                var fieldInput = $('[name="' + field + '"]');
                                var fieldContainer = fieldInput.closest('.form-group');

                                // Append error messages under the field container
                                $.each(messages, function(index, message) {
                                    var errorMessage =
                                        '<div class="error-message text-danger">' +
                                        message + '</div>';
                                    fieldContainer.append(errorMessage);
                                });
                            });
                        } else {
                            // Handle other error statuses
                            // console.log(xhr.responseJSON)
                            displayMessage(xhr.responseJSON.message, "error")
                        }
                    }
                });
            });
        });
    </script>
@endpush
