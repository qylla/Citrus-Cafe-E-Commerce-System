<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>About Us - The Citrus Cafe</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f3e3cf;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background:
                linear-gradient(rgba(0, 0, 0, 0.15), rgba(0, 0, 0, 0.45)),
                url("img/wallpaper-citrus.jpg") no-repeat center center fixed;
            background-size: cover;
        }
    </style>
</head>

<body>

    <!-- ===============================
            NAVBAR (TOP MENU)
        =============================== -->
    <div class="nav-wrapper">
        <div class="custom-nav">

            <!-- Logo + Brand -->
            <div class="nav-left">
                <img src="img/logonav.png" class="logo" alt="Logo">
                <span class="brand">The Citrus Cafe</span>
            </div>

            <!-- Navigation Links -->
            <div class="nav-center">
                <a href="home.php" >Home</a>
                <a href="products.php">Products</a>
                <a href="cart.php">Cart</a>
                <a href="about-us.php"class="active">About Us</a>
                <a href="profile.php">Profile</a>
            </div>

            <!-- Logout -->
            <div class="nav-right">
                <a href="logout.php" class="register-btn">LogOut</a>
            </div>

        </div>
    </div>

    <!-- HERO -->
    <div class="hero-section">
        <h1>About The Citrus Cafe</h1>
        <p>
            Welcome to The Citrus Cafe, a cozy and refreshing place where every drink, dessert, and meal
            is prepared with care. We are passionate about creating a warm dining experience with delicious
            flavors, quality ingredients, and friendly service for everyone.
        </p>
    </div>

    <!-- ABOUT CONTENT -->
    <div class="container mb-5">
        <div class="row g-4">

            <div class="col-md-6">
                <div class="about-card">
                    <h2>Our Story</h2>
                    <p>
                        The Citrus Cafe was founded with the idea of bringing people together through food
                        and drinks in a relaxing environment. We wanted to build a cafe that feels welcoming,
                        comfortable, and memorable for students, families, and friends who want to enjoy good
                        moments together.
                    </p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="about-card">
                    <h2>Our Mission</h2>
                    <p>
                        Our mission is to provide high-quality menu items, excellent customer service, and
                        a pleasant atmosphere that keeps customers coming back. We always strive to deliver
                        freshness, taste, and satisfaction in every order.
                    </p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="about-card">
                    <h2>Our Vision</h2>
                    <p>
                        Our vision is to become a favorite destination for customers who love delicious food,
                        refreshing beverages, and a cozy cafe environment. We hope to continue growing while
                        maintaining the quality and warmth that define The Citrus Cafe.
                    </p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="about-card">
                    <h2>What We Offer</h2>
                    <p>
                        We offer a delightful selection of drinks, desserts, and cafe-style meals that are
                        suitable for all kinds of customers. From light snacks to refreshing beverages, our
                        menu is designed to satisfy cravings and create enjoyable dining experiences.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- WHY CHOOSE US -->
    <div class="container mb-4">
        <div class="section-title">
            <h2>Why Choose Us</h2>
            <p>Here are some reasons why customers love The Citrus Cafe.</p>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="feature-box">
                    <h5>Fresh Ingredients</h5>
                    <p>We use quality ingredients to ensure every menu item tastes fresh and satisfying.</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="feature-box">
                    <h5>Delicious Variety</h5>
                    <p>Our menu includes a range of drinks, desserts, and meals for different preferences.</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="feature-box">
                    <h5>Friendly Service</h5>
                    <p>We value every customer and always aim to provide a welcoming and pleasant experience.</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="feature-box">
                    <h5>Cozy Atmosphere</h5>
                    <p>Our cafe offers a comfortable space for relaxing, dining, and spending time with others.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- BOTTOM HIGHLIGHT -->
    <div class="bottom-highlight">
        <h2>Experience the Taste of Comfort</h2>
        <p>
            At The Citrus Cafe, we believe that good food and drinks can brighten your day. Whether you are
            here for a quick refreshment or a relaxing meal, we are ready to serve you with warmth, flavor,
            and care.
        </p>
    </div>
    <div class="footer">
        <div class="footer-content">
            <div class="footer-brand">
                <h3>The Citrus Cafe</h3>
                <p>Fresh flavors, cozy moments, and delightful experiences for every customer.</p>
            </div>

            <div class="footer-links">
                <h5>Quick Links</h5>
                <a href="home.php">Home</a>
                <a href="products.php">Products</a>
                <a href="cart.php">Cart</a>
                <a href="about-us.php">About Us</a>
            </div>

            <div class="footer-contact">
                <h5>Contact</h5>
                <p>Email: hello@thecitruscafe.com</p>
                <p>Phone: +60 12-345 6789</p>
                <p>Kuala Lumpur, Malaysia</p>
            </div>
        </div>

        <div class="footer-bottom">
            © 2026 The Citrus Cafe. All Rights Reserved.
        </div>
    </div>

</body>

</html>