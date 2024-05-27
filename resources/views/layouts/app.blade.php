<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light"
    data-menu-styles="dark" data-toggled="close">

<head>

    <!-- Meta Data -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ config('app.name', 'Laravel') }} - @yield('title')</title>
    <meta name="Description" content="">
    <meta name="Author" content="">
    <meta name="keywords" content="">

    @include('partials._styles')

    <link href="{{ asset('assets/libs/node-waves/waves.min.css') }}" rel="stylesheet">

    <!-- Simplebar Css -->
    <link href="{{ asset('assets/libs/simplebar/simplebar.min.css') }}" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    @stack('styles')

</head>

<body>
    {{-- @include('partials._switcher') --}}
    @include('partials._loader')
    <div class="page">
        @include('partials.headers')
        @include('partials._sidebar')

        <div class="main-content app-content">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
    </div>

    @include('partials._modal')

    @include('partials._js')

    @include('partials._alert_messages')

    <!-- Defaultmenu JS -->
    <script src="{{ asset('assets/js/defaultmenu.min.js') }}"></script>

    <!-- Node Waves JS-->
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>

    <!-- Sticky JS -->
    <script src="{{ asset('assets/js/sticky.js') }}"></script>

    <!-- Simplebar JS -->
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>

    <script src="{{ asset('assets/js/simplebar.js') }}"></script>

    <script src="{{ asset('assets/libs/@simonwep/pickr/pickr.es5.min.js') }}"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="{{ asset('assets/js/custom.js') }}"></script>

    <script>
        $('input[name="datepicker"]').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            }
        });

        $('input[name="datepicker"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate
                .format('YYYY-MM-DD'));
        });

        $('input[name="datepicker"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        const modalBody = $('#modalBody')

        $('body').on('click', '.trigerModal', function(e) {
            e.preventDefault();

            const url = $(this).data('url');

            $.ajax({
                url: url,
                method: "GET",
                beforeSend: function() {
                    modalBody.html(`<div class="d-flex justify-content-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>`)
                },
                success: function(result) {
                    modalBody.empty().html(result);

                    // const select2 = modalBody.find('#select')

                    // if (select2) {
                    //     select2.select2({
                    //         allowClear: true,
                    //         // dir: "ltr"
                    //     });
                    // }

                },
                error: function(jqXHR, testStatus, error) {
                    console.log(jqXHR.responseText, testStatus, error);
                    displayMessage("An error occurred", "error")
                },
                timeout: 8000,
            });

        })
    </script>

    @stack('scripts')

</body>

</html>
