<script>
    $(document).ready(function() {
        $('#profile-change').on('change', function(event) {
            event.preventDefault();

            var file = event.target.files[0];

            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var content = e.target.result;
                    $('#profile-img').attr('src', content);
                }
                reader.readAsDataURL(file);
            }
        });

        $('#profileImageForm').on('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            // Remove any existing error messages
            $('.error-message').remove();

            // Serialize form data
            var formData = new FormData(this);

            const button = $(this).find('button');
            const spinner = button.find('.spinner-border');
            const buttonText = button.find('#text');

            // Send AJAX request
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    buttonText.hide();
                    spinner.show();
                    button.attr('disabled', true);
                },
                success: function(response) {
                    // Handle success response

                    $('#profileImageForm')[0].reset();
                    displayMessage(response.message, "success");

                    setTimeout(function() {
                        spinner.hide();
                        buttonText.show();
                        button.attr('disabled', false);
                    }, 2000); // 2000 milliseconds = 2 seconds
                },
                error: function(xhr, status, error) {
                    spinner.hide();
                    buttonText.show();
                    button.attr('disabled', false); // Enable the button again

                    // Handle error response
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;

                        $.each(errors, function(field, messages) {
                            // Find the corresponding fields
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
                    } else if (xhr.status === 419) {
                        location.reload();
                    } else {
                        // Handle other error statuses
                        displayMessage(xhr.responseJSON.message, "error");
                    }
                }
            });
        });
    });
</script>
