<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="horizontal" data-theme-mode="light" data-header-styles="dark" data-menu-styles="dark" loader="disable" data-nav-style="menu-click" style="">

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

    @stack('scripts')

</body>

</html>
