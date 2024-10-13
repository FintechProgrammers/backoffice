@extends('layouts.app')

@push('styles')
@endpush

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">Update Package</p>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card custom-card card-body" id="modalBody">
                <form action="{{ route('admin.package.update', $service->uuid) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')
                    @include('admin.services._form')
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script>
        $(document).ready(function() {

            function formatState(state) {
                if (!state.id) {
                    return state.text; // Return text for the placeholder option.
                }

                var profileUrl = $(state.element).data('profile'); // Get profile picture URL.
                var $state = $(
                    '<span><img src="' + profileUrl +
                    '" class="img-circle" style="width: 30px; height: 30px; margin-right: 10px;" /> ' + state
                    .text + '</span>'
                );
                return $state;
            }

            $('.sponsors').select2({
                templateResult: formatState,
                templateSelection: formatState,
                placeholder: "Select streamers", // Updated placeholder for multi-select.
                allowClear: true, // Allow clearing the selection.
                multiple: true // Enable multiple selection.
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            // Function to show/hide the products div based on the selected value
            function toggleProductsDiv() {
                var selectedValue = $('#packageType').val();
                if (selectedValue === 'service') {
                    $('#products').show();
                } else {
                    $('#products').hide();
                }
            }

            // Initial check when the form is loaded
            toggleProductsDiv();

            // Event listener for change in the select element
            $('#modalBody').on('change', '#packageType', function() {
                toggleProductsDiv();
            });

            $('#modalBody').on('change', '#photo', function(event) {
                event.preventDefault();

                const contentBody = $('#modalBody').find('#photoContent');

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
            $('#modalBody').on('change', '#banner', function(event) {
                event.preventDefault();

                const contentBody = $('#modalBody').find('#bannerContent');

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
            $('#modalBody').on('change', '#product-image', function(event) {
                event.preventDefault();

                const contentBody = $('#modalBody').find('#productImageContent');

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

            $('#modalBody').on('submit', 'form', function(event) {
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

                        displayMessage(response.message, "success")

                        setTimeout(function() {
                            location.href = "{{ route('admin.package.index') }}"
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
    </script>
@endpush
