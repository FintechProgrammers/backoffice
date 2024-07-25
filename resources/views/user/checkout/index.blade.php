<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light"
    data-menu-styles="dark" data-toggled="close">

<head>

    <!-- Meta Data -->
    @include('partials._meta')

    @include('partials._styles')
    @include('partials._dashboard_styles')
    @stack('styles')

</head>

<body>
    {{-- @include('partials._loader') --}}
    <div class="page">
        <div class="main-content app-content">
            <div class="container">
                <div class="row">
                    <div class="col-xl-9">
                        <div class="card custom-card">
                            <div class="card-body p-0 product-checkout">
                                <h4 class="p-3 text-uppercase">Checkout</h4>
                                <form action="{{ route('checkout.store') }}" method="POST" id="checkoutForm">
                                    @csrf
                                    <input type="hidden" name="referral_id" value="{{ $ambassador->uuid }}">
                                    <input type="hidden" name="package_id" value="{{ $package->uuid }}">
                                    @include('user.checkout.package')
                                    <div class="p-4">
                                        <p class="mb-1 fw-semibold text-muted op-5 fs-20">02</p>
                                        <div
                                            class="fs-15 fw-semibold d-sm-flex d-block align-items-center justify-content-between mb-3">
                                            <div>Personal Details :</div>
                                        </div>
                                        @include('user.team._user-info')
                                    </div>
                                    @include('user.checkout.payment-methods')
                                    <div
                                        class="px-4 py-3 border-top border-block-start-dashed d-sm-flex justify-content-between">
                                        <button type="submit" class="btn btn-success-light m-1">
                                            <div class="spinner-border" style="display: none" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                            <span id="text">Continue To Payment <i
                                                    class="bi bi-credit-card-2-front align-middle ms-2 d-inline-block"></i></span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('partials._js')


    <script script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


    <script src="{{ asset('assets/js/custom.js') }}">
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


</body>

</html>
