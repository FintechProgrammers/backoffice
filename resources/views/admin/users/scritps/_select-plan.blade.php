<script>
    $('#modalBody').on('click', '.bxi-package', function() {
        const radio = $(this).find('input[name="package"]');

        if (radio.length) {
            radio.prop('checked', true);

            // Remove highlight from all containers
            $('.bxi-package').removeClass('highlighted');

            // Add highlight to the selected container
            $(this).addClass('highlighted');
        }
    });
</script>
