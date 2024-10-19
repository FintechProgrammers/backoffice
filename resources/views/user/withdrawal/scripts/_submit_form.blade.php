<script>
    $(document).ready(function() {

        const modal = $('#modalBody')

        // Listen for input events on token fields
        $('#modalBody').on('input', '#tokenContainer .token-input', function() {
            checkTokenFields();
        });

        // Move to next input when filled and previous input when backspace
        $('#modalBody').on('keyup', '#tokenContainer .token-input', function(e) {
            let key = e.keyCode || e.charCode;
            let $this = $(this);
            let index = $('.token-input').index(this);

            if ($this.val().length === 1 && key !== 8 && key !==
                46) { // If input is filled and not backspace/delete
                if (index < 3) { // Move to the next input field if not the last field
                    $('.token-input').eq(index + 1).focus();
                }
            } else if (key === 8 || key === 46) { // If backspace/delete
                if ($this.val().length === 0 && index >
                    0) { // If field is empty and not the first field
                    $('.token-input').eq(index - 1).focus();
                }
            }
        });

        // Ensure only numeric input
        $('#modalBody').on('keydown', '#tokenContainer .token-input', function(e) {
            if (e.key.match(/[0-9]/) || e.keyCode === 8 || e.keyCode === 46 || e.keyCode === 37 || e
                .keyCode === 39) {
                return true;
            } else {
                e.preventDefault();
                return false;
            }
        });

        // Function to check if all token fields are filled
        function checkTokenFields() {

            let allFilled = true;
            modal.find('.token-input').each(function() {
                if (!$(this).val()) {
                    allFilled = false;
                    return false; // Exit loop early if any field is empty
                }
            });

            const btn = modal.find('button')

            // Enable/disable the proceed button based on allFilled status
            btn.prop('disabled', !allFilled);
        }


        $('#modalBody').on('click', '#procees', function(event) {
            event.preventDefault(); // Prevent default form submission

            const token1 = modal.find('#token1').val();
            const token2 = modal.find('#token2').val();
            const token3 = modal.find('#token3').val();
            const token4 = modal.find('#token4').val();

            // Check if all token fields are filled
            if (!token1 || !token2 || !token3 || !token4) {
                // Display error message
                var errorMessage =
                    '<div class="error-message text-danger text-center">All token fields are required.</div>';
                $('.pin-card-text').append(errorMessage);
                return;
            }


            // Concatenate token values into a single string
            $('#tokenInput').val(token1 + token2 + token3 + token4);

            // Remove any existing error messages
            $('.error-message').remove();

            const form = $('#withdrawalForm')

            // Serialize form data
            let formData = form.serialize();

            const button = $(this)

            const spinner = button.find('.spinner-border')
            const buttonTest = button.find('#text')

            // Send AJAX request
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                beforeSend: function() {
                    buttonTest.hide()
                    spinner.show()
                    button.attr('disabled', true)
                },
                success: function(response) {

                    form[0].reset();

                    $('#primaryModal').modal('hide')

                    spinner.hide()
                    buttonTest.show()

                    setTimeout(function() {
                        displayMessage(response.message, "success")
                    }, 2000); // 2000 milliseconds = 2 seconds

                },
                error: function(xhr, status, error) {
                    $('#primaryModal').modal('hide')
                    spinner.hide()
                    buttonTest.show()
                    button.attr('disabled', false)
                    // Handle error response

                    // Handle other error statuses
                    // console.log(xhr.responseJSON)
                    displayMessage(xhr.responseJSON.message, "error")

                }
            });
        });

        $('#modalBody').on('submit', '#addressForm', function(event) {
            event.preventDefault(); // Prevent default form submission

            // Remove any existing error messages
            $('.error-message').remove();

            const form = $(this)

            // Serialize form data
            let formData = form.serialize();

            const button = $(this).find('button')

            const spinner = button.find('.spinner-border')
            const buttonTest = button.find('#text')

            // Send AJAX request
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                beforeSend: function() {
                    buttonTest.hide()
                    spinner.show()
                    button.attr('disabled', true)
                },
                success: function(response) {

                    form[0].reset();

                    $('#primaryModal').modal('hide')

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
</script>
