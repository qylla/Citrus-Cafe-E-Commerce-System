<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>The Citrus Kitchen</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="css/style.css">

<style>

/* 🔥 RESET & FIX SCROLL */
html, body {
    height: 100%;
    margin: 0;
    overflow: hidden; /* ❌ BUANG SCROLL TERUS */
}

/* ===============================
   HERO FULLSCREEN FIX
=============================== */

.hero-container {
    width: 70%;
    
    /* 🔥 CENTER VERTICALLY */
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);

    margin: 0; /* buang margin lama */
}

.hero-box {
    background: white;
    border-radius: 35px;

    /* 🔥 SHADOW FULL (tak clip sebab dah center) */
    box-shadow: 0 40px 100px rgba(0, 0, 0, 0.25);

    padding: 50px;
    position: relative;

    display: flex;
    align-items: center;
    justify-content: space-between;
}

/* ===============================
   IMAGE
=============================== */

.hero-image {
    position: relative;
    width: 50%;
    height: 350px;
}

.hero-image img {
    position: absolute;
    width: 420px;

    left: -40px;
    top: -60px;

    opacity: 0;
    transform: scale(0.85);

    animation: drinkSlide 12s infinite;

    filter: drop-shadow(0 25px 40px rgba(0, 0, 0, 0.4));
}

.hero-image img:nth-child(2) {
    animation-delay: 4s;
}

.hero-image img:nth-child(3) {
    animation-delay: 8s;
}

@keyframes drinkSlide {
    0% {
        opacity: 0;
        transform: scale(0.85) translateY(30px);
    }
    8% {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
    30% {
        opacity: 1;
    }
    38% {
        opacity: 0;
        transform: scale(0.9) translateY(-20px);
    }
    100% {
        opacity: 0;
    }
}

/* ===============================
   TEXT
=============================== */

.hero-text {
    width: 50%;
    padding-left: 40px;
}

.hero-text h1 {
    font-size: 48px;
    font-weight: 700;
    font-family: 'Playfair Display', serif;
}

.hero-text p {
    font-size: 18px;
    margin-top: 15px;
    color: #444;
}

/* BUTTON */
.btn-orange {
    background: #6b3f24;
    color: white;
    border-radius: 30px;

    padding: 12px 30px;
    border: none;

    margin-top: 25px;
    font-weight: 500;

    transition: 0.3s;
}

.btn-orange:hover {
    background: #4e2e1a;
    transform: translateY(-2px);
}

</style>
</head>

<body>

    <!-- NAVBAR -->
    <div class="nav-wrapper">
        <div class="custom-nav">

            <div class="nav-left">
                <img src="img/logonav.png" class="logo" alt="Logo">
                <span class="brand">The Citrus Cafe</span>
            </div>

            <div class="nav-center">
                <a href="home.php" class="active">Home</a>
                <a href="products.php">Products</a>
                <a href="cart.php">Cart</a>
                <a href="about-us.php">About Us</a>
                <a href="profile.php">Profile</a>
            </div>

            <div class="nav-right">
                <a href="logout.php" class="register-btn">LogOut</a>
            </div>

        </div>
    </div>

    <!-- HERO -->
    <div class="hero-container">

        <div class="hero-box">

            <div class="hero-image">
                <img src="img/NoBgAir11.png">
                <img src="img/NoBgAir22.png">
                <img src="img/NoBgAir3.png">
            </div>

            <div class="hero-text">

                <h1>Freshly Squeezed Every Day</h1>

                <p>Enjoy our natural signature orange juice made with love.</p>

<a href="products.php" class="btn btn-orange">
    Order Now
</a>

            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>