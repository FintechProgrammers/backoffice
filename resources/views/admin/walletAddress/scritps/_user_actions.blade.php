<script>
    $(document).ready(function() {
        $(document).on('click', '#table-body a.btn-action', function(e) {
            e.preventDefault();

            var actionUrl = $(this).data('url');

            const button = $(this).find('button')

            const spinner = button.find('.spinner-border')
            const buttonTest = button.find('#text')

            Swal.fire({
                title: 'Are you sure?',
                text: 'You want to mark address as whitelisted?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "Yes"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Perform AJAX request based on action type
                    $.ajax({
                        url: actionUrl,
                        type: 'POST', // Assuming you're using GET method
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        beforeSend: function() {
                            buttonTest.hide()
                            spinner.show()
                            // button.attr('disabled', true)
                        },
                        success: function(response) {
                            spinner.hide()
                            buttonTest.show()
                            // Assuming success message is returned from the server
                            loadTable()
                            displayMessage(response.message, "success")
                            // Assuming you want to reload the table after action is performed
                            // You can customize this part based on your requirement
                            // window.location.reload();
                        },
                        error: function(xhr, status, error) {
                            spinner.hide()
                            buttonTest.show()
                            // button.attr('disabled', false)
                            displayMessage(
                                'An error occurred while performing the action.',
                                "error")
                            // Handle error response
                            // You can display error messages or handle the error based on your requirement
                        }
                    });
                }
            });
        });
    });
</script>
