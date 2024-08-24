<div data-collapse="medium" data-animation="default" data-duration="400" data-easing="ease" data-easing2="ease" role="banner"
    class="navigation w-nav">
    <div class="navigation-menu">
        <a href="{{ route('home') }}" class="nav-logo w-nav-brand"><img src="{{ asset('frontend/assets/logo.webp') }}"
                alt="" /></a>
        <nav role="navigation" class="nav-menu w-nav-menu">
            <a href="{{ route('home') }}" class="nav-link w-nav-link">Home</a>
            <a href="{{ route('service') }}" class="nav-link w-nav-link">Service</a>
            <a href="{{ route('owntheFuture') }}" class="nav-link w-nav-link">OwntheFuture</a>
            <a href="{{ route('contact') }}" class="nav-link w-nav-link">Contact</a>

        </nav>
    </div>
    @if (Auth::check())
        <div class="navigation-button-wrap"><a href="{{ route('dashboard') }}"
                data-w-id="60d834ed-3efd-413a-952d-2f8691b71dd4" target="_blank"
                class="button black hide-on-phone w-inline-block">
                <div class="button-arrow-wrap">
                    <div class="button-arrow-circle"><img
                            src="https://assets.website-files.com/6145e7f1b0d13ee4320a5163/6145e7f1b0d13e7e4e0a523e_arrow_forward_black_24dp.svg"
                            loading="lazy" alt="" class="button-arrow" /></div>
                </div>
                <div class="button-text">Dashboard</div>
            </a>
            <div class="menu-button w-nav-button">
                <div class="w-icon-nav-menu"></div>
            </div>
        </div>
    @else
        <div class="navigation-button-wrap"><a href="{{ route('login') }}"
                data-w-id="60d834ed-3efd-413a-952d-2f8691b71dd4" target="_blank"
                class="button black hide-on-phone w-inline-block">
                <div class="button-arrow-wrap">
                    <div class="button-arrow-circle"><img
                            src="https://assets.website-files.com/6145e7f1b0d13ee4320a5163/6145e7f1b0d13e7e4e0a523e_arrow_forward_black_24dp.svg"
                            loading="lazy" alt="" class="button-arrow" /></div>
                </div>
                <div class="button-text">Get started</div>
            </a>
            <div class="menu-button w-nav-button">
                <div class="w-icon-nav-menu"></div>
            </div>
        </div>
    @endif


</div>
