<!DOCTYPE html>
<html>

<head>
    <title>Onstru | Contact Us</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/images/Favicon.png') }}" sizes="32*32" type="image/png">

    <!-- Bootstrap CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <!-- Font / Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/buttons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/contactus.css') }}">

</head>

<body>

    <div class="main">

        <!-- Backgorund -->
        <div class="background">
            <div class="bgct">
                <h1>CONTACT US</h1>
            </div>
        </div>

        <!-- Contact -->
        <div class="content">
            <div class="contact-div">
                <!-- Contact Left -->
                <div class="contact-map">
                    <div class="contact-info-div">
                        <div class="contact-map-div">
                            <div
                                style="overflow:hidden;max-width:100%;width:100%;height:275px; border: 2px solid var(--border);">
                                <div id="google-maps-canvas" style="height:100%; width:100%;max-width:100%;"><iframe
                                        style="height:100%;width:100%;border:0;" frameborder="0"
                                        src="https://www.google.com/maps/embed/v1/place?q=B2,+VIT+Collage+Opposite+Kelambakkam+-+Vandalur+Rd,+Keezhakottaiyur,+Chennai,+TamilNadu+-+600127&key=AIzaSyBFw0Qbyq9zTFTd-tUY6dZWTgaQzuU17R8"></iframe>
                                </div>
                            </div>
                        </div>
                        <div class="contact-info-cnt my-4">
                            <i class="fas fa-location-dot"></i>
                            <h6>
                                B2, VIT Collage Opposite Kelambakkam - Vandalur Rd, Keezhakottaiyur, Chennai, TamilNadu
                                -
                                600127.
                            </h6>
                        </div>
                        <div class="contact-info-cnt mb-4">
                            <i class="fas fa-phone"></i>
                            <h6>
                                <a href="tel:+91 89880 79880">+91 89880 79880</a>
                            </h6>
                        </div>
                        <div class="contact-info-cnt">
                            <i class="fas fa-envelope"></i>
                            <h6>
                                <a href="mailto:sales@technomerates.com">sales@technomerates.com</a>
                            </h6>
                        </div>
                    </div>
                </div>

                <!-- Contact Right -->
                <div class="contact-form">
                    <form action="" method="" class="row">
                        <div class="col-sm-12 col-md-12 col-xl-12 mb-4">
                            <label for="name">Full Name <span>*</span></label>
                            <input type="text" name="name" id="name" class="form-control"
                                placeholder="Enter Your Full Name" required>
                        </div>
                        <div class="col-sm-12 col-md-12 col-xl-12 mb-4">
                            <label for="email">Email <span>*</span></label>
                            <input type="email" name="email" id="email" class="form-control"
                                placeholder="Enter Your Email" required>
                        </div>
                        <div class="col-sm-12 col-md-12 col-xl-12 mb-4">
                            <label for="contact">Contact Number <span>*</span></label>
                            <input type="text" name="contact" id="contact" class="form-control"
                                oninput="validate_contact(this)" min="6000000000" max="9999999999"
                                placeholder="Enter Your Contact Number" required>
                        </div>
                        <div class="col-sm-12 col-md-12 col-xl-12 mb-4">
                            <label for="subject">Subject</label>
                            <textarea name="subject" id="subject" class="form-control" rows="3"
                                placeholder="Enter Your Subject"></textarea>
                        </div>
                        <div
                            class="col-sm-12 col-md-12 col-xl-12 mb-3 d-flex justify-content-center align-items-center">
                            <button type="submit" name="contact_us" class="loginbtn w-100">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>

</html>