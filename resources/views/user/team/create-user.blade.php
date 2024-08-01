@extends('layouts.user.app')

@section('title', 'Add New Registration')

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">Add New Registration</p>
        </div>
    </div>
    <form method="POST" action="{{ route('checkout.store') }}" id="checkoutForm">
        @csrf
        <div class="d-flex  justify-content-center align-items-center">
            <div class="col-lg-6">
                <div class="card card-body product-checkout">
                    <div class="form-step form-step-active">
                        <h4 class="text-center">
                            Add New Registration
                        </h4>
                        <p class="text-center">Please provide the Registration details to create a new account</p>
                        @include('user.team._user-info')
                        <button type="button" class="btn btn-primary btn-next">Next</button>
                    </div>
                    <div class="form-step ">
                        @include('user.team._enrolment')
                        <div class="p-3">
                            <button type="button" class="btn btn-dark btn-prev">Previous</button>
                            <button type="button" class="btn btn-primary btn-next">Next</button>
                        </div>
                    </div>
                    <div class="form-step">
                        <div>
                            @include('user.team._summary')
                        </div>
                        <div class="p-3">
                            <button type="button" class="btn btn-dark btn-prev">Previous</button>
                            <button type="submit" class="btn btn-primary">
                                <div class="spinner-border" style="display: none" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <span id="text">Proceed</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formSteps = document.querySelectorAll('.form-step');
            const nextButtons = document.querySelectorAll('.btn-next');
            const prevButtons = document.querySelectorAll('.btn-prev');
            const serviceRadios = document.querySelectorAll('input[name="package_id"]');
            const previewImage = document.getElementById('preview-image');
            const previewName = document.getElementById('preview-name');
            const previewPrice = document.getElementById('preview-price');
            const previewTotalPrice = document.getElementById('preview-total-price');
            const countrySelect = document.querySelector('.country-select');

            let currentStep = 0;

            nextButtons.forEach(button => {
                button.addEventListener('click', () => {
                    if (validateStep(currentStep)) {
                        formSteps[currentStep].classList.remove('form-step-active');
                        currentStep++;
                        formSteps[currentStep].classList.add('form-step-active');
                        updateSummary();
                    }
                });
            });

            prevButtons.forEach(button => {
                button.addEventListener('click', () => {
                    formSteps[currentStep].classList.remove('form-step-active');
                    currentStep--;
                    formSteps[currentStep].classList.add('form-step-active');
                });
            });

            function validateStep(step) {
                let isValid = true;
                const inputs = formSteps[step].querySelectorAll('input, select, radio');

                inputs.forEach(input => {
                    if (!input.checkValidity()) {
                        input.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        input.classList.remove('is-invalid');
                    }
                });

                return isValid;
            }

            function updateSummary() {
                const country = document.querySelector('[name="country"]').value;
                const countryOption = countrySelect.options[countrySelect.selectedIndex];
                const countryName = countryOption.getAttribute('data-countryName');
                const firstName = document.querySelector('[name="first_name"]').value;
                const lastName = document.querySelector('[name="last_name"]').value;
                const username = document.querySelector('[name="username"]').value;
                const email = document.querySelector('[name="email"]').value;
                const phoneNumber = document.querySelector('[name="phone_number"]').value;
                const selectedService = document.querySelector('input[name="service"]:checked');
                let packageDetails = "None";


                if (selectedService) {
                    const name = selectedService.getAttribute('data-name');
                    const image = selectedService.getAttribute('data-image');
                    const price = selectedService.getAttribute('data-price');

                    packageDetails = `${name} - $${price}`;

                    // Update preview section
                    previewImage.src = image;
                    previewName.innerHTML = name;
                    previewPrice.innerHTML = `$${price}`;
                    previewTotalPrice.innerHTML = `$${price}`
                    // `$${parseFloat(price) + parseFloat(previewDeliveryFee.textContent.replace('+$', ''))}`;
                }

                // document.getElementById('summary-country').textContent = countryName;
                // document.getElementById('summary-first_name').textContent = firstName;
                // document.getElementById('summary-last_name').textContent = lastName;
                // document.getElementById('summary-username').textContent = username;
                // document.getElementById('summary-email').textContent = email;
                // document.getElementById('summary-phone_number').textContent = phoneNumber;
                document.getElementById('summary-country').value = country;
                document.getElementById('summary-first_name').value = firstName;
                document.getElementById('summary-last_name').value = lastName;
                document.getElementById('summary-username').value = username;
                document.getElementById('summary-email').value = email;
                document.getElementById('summary-phone_number').value = phoneNumber;
            }

            document.querySelectorAll('.bxi-package').forEach(container => {
                container.addEventListener('click', function() {
                    const radio = this.querySelector('input[name="package_id"]');
                    if (radio) {
                        radio.checked = true;

                        // Remove highlight from all containers
                        document.querySelectorAll('.bxi-package').forEach(function(container) {
                            container.classList.remove('highlighted');
                        });

                        // Add highlight to the selected container
                        this.classList.add('highlighted');

                        // Update preview section
                        const name = radio.getAttribute('data-name');
                        const image = radio.getAttribute('data-image');
                        const price = radio.getAttribute('data-price');

                        previewImage.src = image;
                        previewName.innerHTML = name;
                        previewPrice.innerHTML = `$${price}`;
                        previewTotalPrice.innerHTML = `$${price}`;
                    }
                });
            });
        });
    </script>
    <style>
        .form-step {
            display: none;
        }

        .form-step-active {
            display: block;
        }

        .is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            display: none;
            color: #dc3545;
        }

        .is-invalid~.invalid-feedback {
            display: block;
        }
    </style>
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
