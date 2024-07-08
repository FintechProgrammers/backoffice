@extends('layouts.user.app')

@section('title', 'Kyc')

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">KYC</p>
        </div>
    </div>
    <div id="kycContent"></div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {

            loadContent()

            $('#kycContent').on('change', '#front-photo', function(event) {
                event.preventDefault();

                const contentBody = $('#kycContent').find('#frontContent');

                var file = event.target.files[0];

                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var content = e.target.result;
                        contentBody.html(
                            `<img src="${content}" class="img-fluid rounded" alt="Uploaded Image" width="150px" height="50px">`
                        );
                    }
                    reader.readAsDataURL(file);
                } else {
                    contentBody.text('No file selected');
                }
            });
            $('#kycContent').on('change', '#back-photo', function(event) {
                event.preventDefault();

                const contentBody = $('#kycContent').find('#backContent');

                var file = event.target.files[0];

                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var content = e.target.result;
                        contentBody.html(
                            `<img src="${content}" class="img-fluid rounded" alt="Uploaded Image" width="150px" height="50px">`
                        );
                    }
                    reader.readAsDataURL(file);
                } else {
                    contentBody.text('No file selected');
                }
            });
            $('#kycContent').on('submit', 'form', function(event) {
                event.preventDefault(); // Prevent default form submission

                // Remove any existing error messages
                $('.error-message').remove();

                // Serialize form data
                var formData = new FormData(this);

                const button = $(this).find('button')
                const spinner = button.find('.spinner-border')
                const buttonTest = button.find('#text')

                // Send AJAX request
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        buttonTest.hide()
                        spinner.show()
                        button.attr('disabled', true)
                    },
                    success: function(response) {
                        // Handle success response

                        loadContent()
                        spinner.hide()
                        buttonTest.show()

                        setTimeout(function() {
                            displayMessage(response.message, "success")
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
                                var fieldContainer = fieldInput.closest('.mb-3');

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

        function loadContent() {

            const kycContent = $('#kycContent')

            $.ajax({
                url: '/kyc/load-data',
                type: 'GET',
                beforeSend: function() {
                    kycContent.html(`<div class="d-flex justify-content-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>`)
                },
                success: function(response) {
                    kycContent.empty().html(response);
                },
                error: function(xhr, status, error) {
                    // Handle error response
                    console.log(xhr.responseJSON)
                }
            });
        }
    </script>
@endpush
