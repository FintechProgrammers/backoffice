<!DOCTYPE html>

<html>

<head>
    @include('partials.frontend._meta')
</head>

<body>
    @include('partials.frontend._nav')

    <div class="section">
        <div class="contact-wrap">
            <div class="contact-row">
                <div class="contact-left-side">
                    <h1 class="h1-title _2vw-margin">Get In Touch</h1>
                    <div class="black-square">
                        <p class="paragraph _1vw-margin">General inquiries</p>
                        <p class="large-paragraph _2vw-margin">We’re always open to fresh conversations, new
                            opportunities, partnership, and more.</p>
                        <p class="large-paragraph _2vw-margin">Email:<a href="mailto:support@deltadigital.pro"
                                class="contact-email text-white">Support@deltadigital.pro</a></p>
                        <p class="large-paragraph _2vw-margin">Support Number: <a href="tel:+1 (888) 216-2132"
                                class="contact-email">+1 (888) 216-2132</a></p>
                        <p class="large-paragraph _2vw-margin">Office: <br />3343 Peachtree Rd NE Ste 145-688 Atlanta,
                            GA 30326</p>


                    </div>
                </div>
                <div class="gray-square">
                    <p class="paragraph _1vw-margin">Business inquiries</p>
                    <p class="large-paragraph _2vw-margin">We’re always open to collaborations and business
                        partnerships. Contact us and we will reply in a few hours.</p>
                    <div class="form w-form">
                        <form id="email-form" name="email-form" data-name="Email Form" method="get">
                            <input type="text" class="contact-input _2vw-margin w-input" maxlength="256"
                                name="name" data-name="Name" placeholder="Your Name" id="name" required="" />
                            <input type="email" class="contact-input _2vw-margin w-input" maxlength="256"
                                name="email" data-name="Email" placeholder="Your Email" id="email"
                                required="" />
                            <textarea data-name="Message" maxlength="5000" id="Message" name="Message" required="" placeholder="Your Message"
                                class="contact-input message _4vw-margin w-input"></textarea>
                            <input type="submit" value="Submit" data-wait="Please wait..."
                                class="form-button w-button" />
                        </form>
                        <div class="success-message w-form-done">
                            <div>Thank you! Your submission has been received!</div>
                        </div>
                        <div class="error-message w-form-fail">
                            <div>Oops! Something went wrong while submitting the form.</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @include('partials.frontend._footer')

    @include('partials.frontend._scripts')
</body>

</html>
