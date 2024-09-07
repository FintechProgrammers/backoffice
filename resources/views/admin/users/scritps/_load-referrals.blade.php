<script>
    $(document).ready(function() {
        loadReferrals()
    })

    $('body').on('click', '.pagination a', function(event) {
        event.preventDefault();
        let page = $(this).attr('href').split('page=')[1];
        $('#hidden_page').val(page);

        loadReferrals()
    });

    function loadReferrals() {

        const table = $('#referrals-body')

        const page = $('#hidden_page').val();

        $.ajax({
            url: `{{ route('admin.users.filter.referrals', $user->uuid) }}?page=${page}`,
            type: 'GET',
            beforeSend: function() {
                table.html(`<tr>
                    <td class="text-center" colspan="5">
                        <div class="d-flex justify-content-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                        </td>
                    </tr>`)
            },
            success: function(response) {
                table.empty().html(response);
            },
            error: function(xhr, status, error) {
                // Handle error response
                console.log(xhr.responseJSON)
            }
        });
    }
</script>
