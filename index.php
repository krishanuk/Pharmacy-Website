<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    if (!empty($name) && !empty($email) && !empty($message)) {
        $stmt = $conn->prepare("INSERT INTO contactUs (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message);
        $stmt->execute();
        $stmt->close();

        $success_msg = "Thank you for contacting us!";
    } else {
        $error_msg = "All fields are required!";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DEXCARE Pharmacy </title>
    <!-- Bootstrap CSS -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="CSS/styles.css">


    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
</head>

<body>
    <header>
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="container d-flex justify-content-between align-items-center">
                <div class="center-section">
                    <span class="centered-text">Your health is our priority—delivering personalized care and innovative solutions for a healthier tomorrow.</span>
                </div>
                <div class="right-section d-flex align-items-center">
                    <div class="location"><i class="fas fa-map-marker-alt"></i> Sri Lanka </div>
                    <select class="custom-select">
                        <option selected>English</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <a class="navbar-brand" href="#">
                    <img src="images/logo3.png" alt="DexCare Logo">
                </a>
                <form class="row mx-auto w-50 g-0" action="search_results.php" method="GET" style="max-width: 600px;">
                    <div class="col-9">
                        <input class="form-control w-100" type="search" name="search" placeholder="Search products..." aria-label="Search">
                    </div>
                    <div class="col-3">
                        <button class="btn btn-outline-success w-100" type="submit">Search</button>
                    </div>
                </form>


                <div class="contact-and-cart d-flex align-items-center">
                    <a href="tel:+94 70 440 4404" class="phone-number"><i class="fas fa-phone"></i> +94 70 440 4404</a>
                    <div class="cart-icon ml-3">
                        <a href="cart.php"><i class="fas fa-shopping-cart"></i></a>
                    </div>


                    <div class="cart-icon ml-3">
                        <a href="updateuser.php"><i class="fas fa-user-circle"></i></a>
                    </div>
                </div>
            </div>
            </div>
        </nav>

        <!-- Main Navigation -->
        <nav class="main-nav navbar navbar-expand-lg navbar-light bg-lightblue">
            <div class="container">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="#top">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#branches">Branches</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="content.html">Content</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/Dexcare1/ChatC/pre.html">Prescription</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#about-us">About Us</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#contact-us">Contact Us</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Log-out</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Slider Area -->
    <section class="custom-slider" id="home">
        <div class="swiper-container">
            <div class="swiper-wrapper">
                <!-- Start Single Slide -->
                <div class="swiper-slide" style="background-image:url('images/slider.jpg')">
                    <div class="content">
                        <h1>Your Trusted <span>Healthcare</span> Partner!</h1>
                        <p>Experience exceptional medical services tailored to your needs.</p>
                        <div class="buttons">
                            <a href="login.php" class="btn">Login</a>
                            <a href="content.html" class="btn primary">Healthy Advices</a>
                        </div>
                    </div>
                </div>
                <!-- End Single Slide -->
                <!-- Start Single Slide -->
                <div class="swiper-slide" style="background-image:url('images/slider2.jpg')">
                    <div class="content">
                        <h1>Quality <span>Medical</span> Care with <span>Compassion</span></h1>
                        <p>Your health and well-being are our top priority. Learn more about our services.</p>
                        <div class="buttons">
                            <a href="#" class="btn">Prescription</a>
                            <a href="#about-us" class="btn primary">About Us</a>
                        </div>
                    </div>
                </div>

                <!-- Start Single Slide -->
                <div class="swiper-slide" style="background-image:url('images/slider3.jpg')">
                    <div class="content">
                        <h1>Get the <span>Care</span> You <span>Deserve</span></h1>
                        <p>Contact us today for personalized medical solutions.</p>
                        <div class="buttons">
                            <a href="#" class="btn">Schedule a Visit</a>
                            <a href="#contact-us" class="btn primary">Contact Us</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </section>

    <!-- Custom Schedule Area -->
    <section class="custom-schedule">
        <div class="container">
            <div class="schedule-grid">
                <!-- Prescription Services -->
                <div class="schedule-item emergency">
                    <div class="schedule-icon">
                        <i class="fas fa-prescription-bottle-alt"></i>
                    </div>
                    <div class="schedule-content">
                        <h5>Prescription Services</h5>
                        <p1>Get your prescriptions filled quickly and accurately. We provide personalized consultations to ensure you understand your medication and dosage.</p1>
                        <a href="#">Learn More <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
                <!-- Product Catalog -->
                <div class="schedule-item catalog">
                    <div class="schedule-icon">
                        <i class="fas fa-list-alt"></i>
                    </div>
                    <div class="schedule-content">
                        <h5>Product Catalog</h5>
                        <p1>Explore our wide range of health and wellness products, including over-the-counter medications, supplements, and personal care items.</p1>
                        <a href="#">Browse Products <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
                <!-- Pharmacy Hours -->
                <div class="schedule-item hours">
                    <div class="schedule-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="schedule-content">
                        <h5>Store Hours</h5>
                        <p1>We're here to serve you! Visit us during our convenient hours:</p1>
                        <ul class="hours-list">
                            <li>Monday - Friday: 8:00 AM - 8:00 PM</li>
                            <li>Saturday: 9:00 AM - 6:30 PM</li>
                            <li>Sunday: 9:00 AM - 3:00 PM</li>
                        </ul>
                        <a href="#">Learn More <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="chat-icon" onclick="toggleChat()">
        💬
    </div>


    <!-- Pharmacies Display Section -->


    <div class="background-container" id="branches">
        <div class="glass-overlay">
            <div class="pharmacies-container">
                <h2>Our Pharmacies</h2>
                <div class="pharmacies-grid">

                    <?php
                    // Query to get the list of pharmacies

                    $pharmacies = $conn->query("SELECT * FROM Pharmacies");
                    while ($row = $pharmacies->fetch_assoc()) {
                        echo "<div class='pharmacy-card'>";
                        echo "<div class='pharmacy-content'>";
                        echo "<h5 class='pharmacy-title'>" . htmlspecialchars($row['PharmacyName']) . "</h5>";
                        echo "<p class='pharmacy-address'>" . htmlspecialchars($row['Address']) . "</p>";
                        echo "<a href='branch_page.php?id=" . intval($row['PharmacyID']) . "' class='visit-branch-btn'>Visit Branch</a>";
                        echo "</div>";
                        echo "</div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <section class="services-section" id="services-dexcare">
        <div class="container1">
            <h2 class="section-heading">Our Services</h2>
            <div class="services-grid">
                <div class="services-content">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-pills"></i>
                        </div>
                        <h3><a href="#">Prescription Services</a></h3>
                        <p1>Fill and manage your prescriptions with ease and accuracy.</p1>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-syringe"></i>
                        </div>
                        <h3><a href="#">Vaccinations</a></h3>
                        <p1>Get your vaccinations done conveniently at our pharmacy.</p1>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-stethoscope"></i>
                        </div>
                        <h3><a href="#">Health Screenings</a></h3>
                        <p1>Regular health check-ups and screenings for early detection.</p1>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <h3><a href="#">Medication Counseling</a></h3>
                        <p1>Expert advice on medication usage and potential interactions.</p1>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-warehouse"></i>
                        </div>
                        <h3><a href="#">Over-the-Counter Medications</a></h3>
                        <p1>A wide range of over-the-counter medications available.</p1>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <h3><a href="#">Blood Pressure Monitoring</a></h3>
                        <p1>Monitor and manage your blood pressure with our services.</p1>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-capsules"></i>
                        </div>
                        <h3><a href="#">Nutritional Supplements</a></h3>
                        <p1>Find quality nutritional supplements tailored to your needs.</p1>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-first-aid"></i>
                        </div>
                        <h3><a href="#">First Aid Supplies</a></h3>
                        <p1>Get essential first aid supplies for home and travel.</p1>
                    </div>
                </div>

                <div class="services-image">
                    <img src="images/pharma-5.jpg" alt="Pharmacy Services" /> <!-- Replace with your image -->
                </div>
            </div>
        </div>
    </section>


    <style>
        .contact-section {
            padding: 50px 0;
            background-image: url('images/contact.jpeg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
        }

        .contact-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }

        .container {
            position: relative;
            z-index: 2;
        }

        .contact-form {
            max-width: 600px;
            margin: 50px auto;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            padding: 40px;
            background-color: rgba(206, 235, 255, 0.185);
            color: white;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-control {
            border-radius: 4px;
            padding: 10px;
        }

        .btn-submit {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-submit:hover {
            background-color: #218838;
        }

        .message {
            margin-top: 20px;
        }
    </style>


    <div class="about-container" id="about-us">
        <div class="about-content">
            <h1>About Dexcare Pharmacy</h1>
            <p>Dexcare Pharmacy is dedicated to providing the best quality healthcare services to our community. Our mission is to ensure that every individual has access to the medications and advice they need to live a healthy life. With our team of qualified pharmacists, we offer a wide range of services, including prescription fulfillment, medication management, and wellness consultations. We strive to make healthcare more accessible, convenient, and affordable.</p>
            <p>Our vision is to be the leading pharmacy provider by offering personalized care and exceptional customer service. Whether it's helping you manage chronic conditions or providing expert advice on health and wellness, Dexcare Pharmacy is here for you every step of the way.</p>
        </div>
        <div class="about-image">
            <img src="images/aboutus.jpg" alt="Dexcare Pharmacy">
        </div>
    </div>


    <section class="contact-section" id="contact-us">
        <div class="container">
            <div class="contact-form">
                <h2>Contact Us</h2>
                <?php if (isset($success_msg)): ?>
                    <div class="alert alert-success message"><?= htmlspecialchars($success_msg); ?></div>
                <?php elseif (isset($error_msg)): ?>
                    <div class="alert alert-danger message"><?= htmlspecialchars($error_msg); ?></div>
                <?php endif; ?>

                <form action="" method="POST">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" class="form-control" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn-submit">Submit</button>
                </form>
            </div>
        </div>

    </section>


    <style>
        .about-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: rgba(206, 235, 255, 0.185);
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            max-width: 1500px;
            margin: auto;
        }

        .about-content {
            flex: 1;
            padding: 50px;
            margin-top: -10px;
        }

        .about-content h1 {
            text-align: center;
            font-size: 2.5rem;
            font-weight: bold;
            color: #335e90;
            margin-bottom: 40px;
            position: relative;
            padding-bottom: 20px;
            padding-left: 75px;
            white-space: nowrap;
        }

        .about-content h1::after {
            content: "";
            display: block;
            width: 80px;
            height: 4px;
            background: #5a9bd4;
            /* Underline color */
            margin: 10px auto 0;
        }

        .about-content p {
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: #555;
            width: 130%;
            padding-left: 75px;
        }

        .about-image {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .about-image img {
            width: 100%;
            height: 100%;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin-right: 200px;
        }

        @media (max-width: 768px) {
            .about-container {
                flex-direction: column;
            }

            .about-content {
                padding-right: 0;
                text-align: center;
            }

            .about-image {
                margin-top: 20px;
            }
        }
    </style>

    <footer>
        <div class="footer-container">
            <div class="footer-section about">
                <h2>DexCare Pharmacy</h2>
                <p>DexCare Pharmacy is your trusted source for quality medications and healthcare solutions, dedicated to improving your well-being with reliable, safe, and personalized care for all your health needs.</p>
            </div>

            <div class="footer-section links">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="/about-us">About Us</a></li>
                    <li><a href="/services">Services</a></li>
                    <li><a href="/contact">Contact</a></li>
                    <li><a href="/privacy-policy">Privacy Policy</a></li>
                </ul>
            </div>

            <div class="footer-section contact">
                <h3>Contact Us</h3>
                <p><i class="fas fa-phone"></i> +94 70 440 4404</p>
                <p><i class="fas fa-envelope"></i> info@dexcarepharmacy.com</p>
                <p><i class="fas fa-map-marker-alt"></i> 123 Health St, Wellness City, USA</p>
            </div>

            <div class="footer-section social">
                <h3>Follow Us</h3>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            <!-- Embedded Google Map -->
            <div class="map-container">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3958.625183979583!2d79.85431631477164!3d6.927079695067761!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae2591e8f9a52ff%3A0x5c6f7f9c7d1b9c2!2sColombo%2007!5e0!3m2!1sen!2slk!4v1632921538724!5m2!1sen!2sus" width="100%" height="150" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>

            <div class="footer-section newsletter">
                <h3>Subscribe to Our Newsletter</h3>
                <form action="#" method="post">
                    <input type="email" name="email" placeholder="Enter your email" required>
                    <button type="submit">Subscribe</button>
                </form>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2024 DexCare Pharmacy. All rights reserved.</p>
        </div>
    </footer>










    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <!-- <script src="JS/script.js"></script>    -->
    <script src="JS/indexswipper.js"> </script>

    <script>
        var swiper = new Swiper('.swiper-container', {
            slidesPerView: 1,
            spaceBetween: 10,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
        });
    </script>
</body>

</html>