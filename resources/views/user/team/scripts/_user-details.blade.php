<script>
    const canBody = $('#user-details')

    $('#content').on('click', '.show-detail', function(e) {
        e.preventDefault();

        const url = $(this).data('url');

        $.ajax({
            url: url,
            method: "GET",
            beforeSend: function() {
                canBody.html(`<div class="d-flex justify-content-center h-75">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>`)
            },
            success: function(result) {
                canBody.empty().html(result);
            },
            error: function(jqXHR, testStatus, error) {
                console.log(jqXHR.responseText, testStatus, error);
                displayMessage("An error occurred", "error")
            },
            timeout: 8000,
        });
    })
</script>
