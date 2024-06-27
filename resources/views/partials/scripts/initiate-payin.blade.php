<script>
    $('#modalBody').on('click', '#payments a', function(event) {
        event.preventDefault(); // Prevent default action

        const methodBody = $(this).closest('#modalBody'); // Ensure the proper scope

        const loader = methodBody.find('#process-loader');
        const payments = methodBody.find('#payments');

        // Send AJAX request
        $.ajax({
            url: $(this).data('url'),
            type: 'GET',
            processData: true, // Set this to true for default processing
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8', // Default content type
            beforeSend: function() {
                payments.hide();
                loader.show();
            },
            success: function(response) {
                // Handle success response
                setTimeout(function() {
                    location.href = response.data.route;
                }, 2000);
            },
            error: function(xhr, status, error) {
                loader.hide();
                payments.show();

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    displayMessage(xhr.responseJSON.message, "error");
                } else {
                    displayMessage("An error occurred", "error");
                }

                console.log(xhr.responseJSON);
            }
        });
    });
</script>
