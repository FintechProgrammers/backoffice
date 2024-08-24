<!DOCTYPE html>

<html>

<head>
    @include('partials.frontend._meta')


    <link href="{{ asset('frontend/style.css') }}" rel="stylesheet" />
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="{{ asset('frontend/img/logo.png') }}" class="img-fluid" />
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02"
                aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a href="{{ route('home') }}" class="nav-link" style="color: white">Home</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('service') }}" class="nav-link" style="color: white">Service</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('owntheFuture') }}" class="nav-link" style="color: white">OwntheFuture</a>
                    </li>
                    <!--<li class="nav-item">-->
                    <!--    <a href="team.html" class="nav-link"  style="color: white">Teams </a>-->
                    <!--</li>-->
                    <li class="nav-item">
                        <a href="{{ route('contact') }}" class="nav-link" style="color: white">Contact</a>
                    </li>
                </ul>

                @if (Auth::check())
                    <a href="{{ route('dashboard') }}" data-w-id="9b2d96e0-f058-3fc4-eedf-e29d6f3c8eb9" target="_blank"
                        class="button black w-inline-block">
                        <div class="button-arrow-wrap">
                            <div class="button-arrow-circle">
                                <img src="{{ asset('frontend/6145e7f1b0d13ee4320a5163/6145e7f1b0d13e7e4e0a523e_arrow_forward_black_24dp.svg') }}"
                                    loading="lazy" alt="" class="button-arrow" />
                            </div>
                        </div>
                        <div class="button-text">Dashboard</div>
                    </a>
                @else
                    <a href="{{ route('login') }}" data-w-id="9b2d96e0-f058-3fc4-eedf-e29d6f3c8eb9" target="_blank"
                        class="button black w-inline-block">
                        <div class="button-arrow-wrap">
                            <div class="button-arrow-circle">
                                <img src="{{ asset('frontend/6145e7f1b0d13ee4320a5163/6145e7f1b0d13e7e4e0a523e_arrow_forward_black_24dp.svg') }}"
                                    loading="lazy" alt="" class="button-arrow" />
                            </div>
                        </div>
                        <div class="button-text">Get started</div>
                    </a>
                @endif


            </div>
        </div>
    </nav>
    <div class="hero-section">
        <div class="text-wrap _5vw-margin">
            <h1 class="h1-title _2vw-margin" style="color: #ffffff; text-align: left">
                Delta Digital: Own The Future
            </h1>
            <p class="large-paragraph center _3vw-margin" style="color: #ffffff; text-align: left">
                Unlock your potential with our tailored educational resources and personalized support. We’re dedicated
                to equipping you with the insights and tools you need for personal growth and success. Empower yourself
                to confidently navigate and thrive in your journey toward self-improvement and achievement. Join us
                today and start transforming your future!
            </p>
            <div class="buttons-container">
                <a href="{{ route('login') }}" data-w-id="9b2d96e0-f058-3fc4-eedf-e29d6f3c8eb9" target="_blank"
                    class="button black w-inline-block">
                    <div class="button-arrow-wrap">
                        <div class="button-arrow-circle">
                            <img src="{{ asset('frontend/6145e7f1b0d13ee4320a5163/6145e7f1b0d13e7e4e0a523e_arrow_forward_black_24dp.svg') }}"
                                loading="lazy" alt="" class="button-arrow" />
                        </div>
                    </div>
                    <div class="button-text">Get started</div>
                </a>
                <a href="{{ route('contact') }}" data-w-id="9b2d96e0-f058-3fc4-eedf-e29d6f3c8ebf"
                    class="button w-inline-block">
                    <div class="button-arrow-wrap">
                        <div class="button-arrow-circle">
                            <img src="{{ asset('frontend/6145e7f1b0d13ee4320a5163/6145e7f1b0d13e7e4e0a523e_arrow_forward_black_24dp.svg') }}"
                                loading="lazy" alt="" class="button-arrow" />
                        </div>
                    </div>
                    <div class="button-text">Contact us</div>
                </a>
            </div>
        </div>
        <div class="left-text-wrap _5vw-margin"></div>

        <div class="hero-img">
            <img src="{{ asset('frontend/img/Bg.svg') }}" class="img-fluid" />
        </div>
    </div>

    <div class="section">
        <div class="_4-columns-grid _7-5vw-columns-gap">
            <div id="w-node-_6593b220-72e9-87ec-9ad3-8148bb83aec4-300a5180" class="align-left-and-vertical">
                <div class="badge">
                    <img src="{{ asset('frontend/6145e7f1b0d13ee4320a5163/6145e7f1b0d13e4e690a527c_play-button-white.svg') }}"
                        loading="lazy" alt="" class="small-icon" />
                    <p class="medium-paragraph">Start with ease!</p>
                </div>
                <h2 class="h2-title _1vw-margin">
                    Gain Insights Thoughtfully
                </h2>
                <p class="large-paragraph _2vw-margin">
                    Unlock the power of revolutionary technology that just 2% of the population currently utilizes. Take
                    advantage of today's opportunity to join the forefront of this digital revolution.
                </p>
                <div class="check-wrapper">
                    <img src=".{{ asset('frontend/6145e7f1b0d13ee4320a5163/6145e7f1b0d13e2d9c0a52cb_Green_Check.svg') }}"
                        alt="" class="medium-icon" />
                    <p class="medium-paragraph">Signup with a Personalized Link</p>
                </div>
                <div class="check-wrapper">
                    <img src="{{ asset('frontend/6145e7f1b0d13ee4320a5163/6145e7f1b0d13e2d9c0a52cb_Green_Check.svg') }}"
                        alt="" class="medium-icon" />
                    <p class="medium-paragraph">Connect to Delta Digital System</p>
                </div>
                <div class="check-wrapper">
                    <img src="{{ asset('frontend/6145e7f1b0d13ee4320a5163/6145e7f1b0d13e2d9c0a52cb_Green_Check.svg') }}"
                        alt="" class="medium-icon" />
                    <p class="medium-paragraph">
                        Start your educational Journey for an empowered future
                    </p>
                </div>
            </div>
            <div id="w-node-_6593b220-72e9-87ec-9ad3-8148bb83aed9-300a5180" class="relative grey"
                style="
            align-content: center;
            justify-content: center;
            display: flex;
            flex-direction: row;
            overflow: hidden;
          ">
                <img src="{{ asset('frontend/assets/mockups/phone4.webp') }}" loading="eager" alt=""
                    style="max-width: 300px; margin-bottom: -60px" class="cover static-on-phone" />
            </div>
        </div>
    </div>
    <div class="section">
        <div class="_4-columns-grid _7-5vw-columns-gap">
            <div id="w-node-_7e24cf19-641b-4039-311b-9f4bb8280a2b-300a5180" class="relative grey"
                style="
            align-content: center;
            justify-content: center;
            display: flex;
            flex-direction: row;
            overflow: hidden;
          ">
                <img src="{{ asset('frontend/assets/mockups/frame(academy).webp') }}" loading="eager"
                    style="object-fit: cover" alt="" />
            </div>

            <div id="w-node-_7e24cf19-641b-4039-311b-9f4bb8280a16-300a5180" class="align-left-and-vertical">
                <h2 class="h2-title _1vw-margin">Delta Academy</h2>
                <p class="large-paragraph _2vw-margin">
                    Delta Academy provides many educational resources to enhance your financial well-being, personal
                    growth, and health knowledge. Our platform features pre-recorded videos that deliver in-depth
                    information and step-by-step guidance. <br />
                    With our expertly curated coursework, you'll cultivate a winning mindset and master practical
                    techniques for effective personal finance management. Our content is informative and actionable,
                    empowering you to achieve your goals and improve your life.
                </p>
                <div class="check-wrapper">
                    <img src="{{ asset('frontend/6145e7f1b0d13ee4320a5163/6145e7f1b0d13e2d9c0a52cb_Green_Check.svg') }}"
                        alt="" class="medium-icon" />
                    <p class="medium-paragraph">Comprehensive Learning Resources: Access various topics to build
                        knowledge and skills</p>
                </div>
                <div class="check-wrapper">
                    <img src="{{ asset('frontend/6145e7f1b0d13ee4320a5163/6145e7f1b0d13e2d9c0a52cb_Green_Check.svg') }}"
                        alt="" class="medium-icon" />
                    <p class="medium-paragraph">
                        Mindset Development: Learn to foster a winning mindset and implement daily self-improvement
                        techniques
                    </p>
                </div>
                <div class="check-wrapper">
                    <img src="{{ asset('frontend/6145e7f1b0d13ee4320a5163/6145e7f1b0d13e2d9c0a52cb_Green_Check.svg') }}"
                        alt="" class="medium-icon" />
                    <p class="medium-paragraph">
                        Step-by-Step Guidance: Follow clear, structured instructions to navigate your growth journey
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="section overflow-hidden">
        <div class="large-card black relative">
            <div class="large-number center _1vw-margin">
                <strong># 533 718 </strong>
            </div>
            <h4 class="h4-title center">
                Join the global community inspired by Delta Digital's transformative tools. Elevate your personal and
                financial growth with us today.
            </h4>
            <img src="{{ asset('frontend/6145e7f1b0d13ee4320a5163/6145e7f1b0d13ea2a90a5243_R_Pricing_Arrow.svg') }}"
                loading="eager" alt="" class="savings-arrows" />
        </div>
    </div>

    <div class="section">
        <div class="_6-columns-grid">
            <div id="w-node-_97974888-3e39-12c4-88fa-baba6d91b4a8-300a5180" class="small-feature only-right-padding">
                <div id="w-node-_97974888-3e39-12c4-88fa-baba6d91b4aa-300a5180" class="text-wrap inside-card">
                    <p class="eyebrow">Our Intention</p>
                    <h3 class="h3-title _2vw-margin">We help <br />drives growth</h3>
                    <p class="paragraph _4vw-margin">
                        We aim to spark personal growth and build transformative habits, equipping individuals with
                        crucial financial and self-improvement skills to elevate their quality of life.
                    </p>
                    <a href="{{ route('service') }}" data-w-id="97974888-3e39-12c4-88fa-baba6d91b4b3"
                        class="button black w-inline-block">
                        <div class="button-arrow-wrap">
                            <div class="button-arrow-circle">
                                <img src="{{ asset('frontend/6145e7f1b0d13ee4320a5163/6145e7f1b0d13e7e4e0a523e_arrow_forward_black_24dp.svg') }}"
                                    loading="lazy" alt="" class="button-arrow" />
                            </div>
                        </div>
                        <div class="button-text">Learn More</div>
                    </a>
                </div>
            </div>
            <div id="w-node-_97974888-3e39-12c4-88fa-baba6d91b4b9-300a5180" class="small-feature no-botton-padding">
                <div class="text-wrap inside-card _4vw-margin">
                    <p class="eyebrow">About us</p>
                    <h3 class="h3-title _2vw-margin">
                        Get to know how fantastic we are
                    </h3>
                </div>
                <img src="{{ asset('frontend/assets/mockups/stream.webp') }}" loading="eager"
                    style="max-width: 300px; object-fit: contain"
                    sizes="(max-width: 479px) 47vw, (max-width: 767px) 21vw, (max-width: 991px) 14vw, 11vw"
                    srcset="
              assets/mockups/stream.webp  500w,
              assets/mockups/stream.webp  800w,
              assets/mockups/stream.webp 1000w
            "
                    alt="" class="_100-width" />
            </div>
            <div id="w-node-_97974888-3e39-12c4-88fa-baba6d91b4c8-300a5180" class="small-feature no-botton-padding">
                <div class="text-wrap inside-card _4vw-margin">
                    <p class="eyebrow">Get in touch</p>
                    <h3 class="h3-title _2vw-margin">
                        Questions? Get in touch for help!
                    </h3>
                </div>
                <img src="{{ asset('frontend/assets/mockups/signal-mockup.webp') }}" loading="eager"
                    style="max-width: 300px; object-fit: contain"
                    sizes="(max-width: 479px) 62vw, (max-width: 767px) 27vw, (max-width: 991px) 17vw, 14vw"
                    srcset="
              assets/mockups/signal-mockup.webp  500w,
              assets/mockups/signal-mockup.webp  800w,
              assets/mockups/signal-mockup.webp 1000w
            "
                    alt="" class="_100-width" />
            </div>
        </div>
    </div>

    @include('partials.frontend._footer')

    <!-- <script src="../js/jquery-3.5.1.min.dc5e7f18c8.js" type="text/javascript"></script> -->
    <script src="{{ asset('frontend/6145e7f1b0d13ee4320a5163/js/webflow.e42a438b7.js') }}" type="text/javascript"></script>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Get the navbar element
        const navbar = document.querySelector(".navbar");

        // Function to toggle the background color of the navbar on scroll
        function toggleNavbarBackground() {
            if (window.scrollY > 0) {
                navbar.classList.add("navbar-scrolled"); // Add the class when scrolled
            } else {
                navbar.classList.remove("navbar-scrolled"); // Remove the class when scrolled to top
            }
        }

        // Event listener for scroll event
        window.addEventListener("scroll", toggleNavbarBackground);
    </script>
</body>

</html>
