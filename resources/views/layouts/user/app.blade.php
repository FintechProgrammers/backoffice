<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light"
    data-menu-styles="dark" data-toggled="close">

<head>

    <!-- Meta Data -->
    @include('partials._meta')

    @include('partials._styles')
    @include('partials._dashboard_styles')
    @stack('styles')

</head>

<body>
    {{-- @include('partials._switcher') --}}
    @include('partials._loader')
    <div class="page">
        @include('partials._user_header')
        @include('partials._user_navs')

        <div class="main-content app-content">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
    </div>

    @include('partials._modal')

    @include('partials._js')

    @include('partials._dashbaord-js')

    <script>
        function selectPaymentProvider(element) {
            var radioButton = element.querySelector('input[type="radio"]');
            radioButton.checked = true;
            highlightContainer(radioButton);
        }

        function highlightContainer(radioButton) {
            // Remove highlight from all containers
            document.querySelectorAll('.shipping-method-container').forEach(function(container) {
                container.classList.remove('highlighted');
            });
            // Add highlight to the selected container
            if (radioButton.checked) {
                radioButton.closest('.shipping-method-container').classList.add('highlighted');
            }
        }
    </script>

    @stack('scripts')

</body>

</html>
