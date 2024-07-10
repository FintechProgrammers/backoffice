<script>
    $(document).ready(function() {
        loadTable()
    })

    $('body').on('click', '.pagination a', function(event) {
        event.preventDefault();
        let page = $(this).attr('href').split('page=')[1];
        $('#hidden_page').val(page);

        loadTable()
    });

    $('#filter').click(function(e) {
        e.preventDefault()
        $('#hidden_page').val(1);
        loadTable()
    })

    $('#reset').click(function(e) {
        e.preventDefault();

        $('#search').val('')
        $('#account_type').val('')
        $('#status').val('')
        $("#search-date").val('');

        $('#hidden_page').val(1)

        loadTable()
    })


    function loadTable() {

        const table = $('#content')

        const page = $('#hidden_page').val();

        $.ajax({
            url: `/team/filter?page=${page}`,
            type: 'GET',
            beforeSend: function() {
                table.html(`
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                 `)
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
