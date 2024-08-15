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
