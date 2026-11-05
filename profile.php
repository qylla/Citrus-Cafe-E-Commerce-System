<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/dao/UserDAO.php';

require_login_page();

$name = $_SESSION["name"] ?? "User";
$email = $_SESSION["email"] ?? "-";
$phone = $_SESSION["phone"] ?? "-";
$birthday = $_SESSION["birthday"] ?? "";

$successPopup = "";

if (isset($_GET["password_changed"])) {
    $successPopup = "Password changed successfully!";
}

if (isset($_GET["updated"])) {
    $successPopup = "Profile updated successfully!";
}

$profile = (new UserDAO())->getProfile($_SESSION["user_id"]);

if ($profile !== null) {
    $name = $profile["full_name"] ?? $name;
    $email = $profile["email"] ?? $email;
    $phone = $profile["phone"] ?? $phone;
    $birthday = $profile["birthday"] ?? $birthday;
}

$initial = strtoupper(substr(trim($name), 0, 1));
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Profile - The Citrus Cafe</title>

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

        .page-wrapper {
            width: 92%;
            margin: 30px auto 50px;
        }

        .page-title {
            margin-bottom: 22px;
        }

        .page-title h2 {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 42px;
            font-weight: 700;
            color: #2f1d12;
            margin-bottom: 8px;
        }

        .page-title p {
            color: #000000;
            margin: 0;
            font-size: 15px;
        }

        .profile-layout {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 28px;
            align-items: start;
        }

        .side-panel {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .highlight-card {
            background: linear-gradient(135deg, #ff9800, #ff7600);
            color: white;
            border-radius: 24px;
            padding: 28px 24px;
            box-shadow: 0 14px 30px rgba(255, 140, 0, 0.25);
        }

        .highlight-card h4 {
            font-size: 32px;
            margin: 0 0 10px 0;
            font-weight: 700;
        }

        .highlight-card p {
            margin: 0;
            opacity: 0.95;
            line-height: 1.6;
        }

        .menu-link {
            text-decoration: none;
            color: inherit;
        }

        .menu-card {
            background: rgba(255, 255, 255, 0.82);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 20px;
            padding: 20px 18px;
            box-shadow: 0 10px 22px rgba(0, 0, 0, 0.06);
            transition: 0.25s ease;
        }

        .menu-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 28px rgba(0, 0, 0, 0.09);
            border-color: #ffca8a;
        }

        .menu-title {
            font-size: 20px;
            font-weight: 700;
            color: #2f1d12;
            margin-bottom: 5px;
        }

        .menu-sub {
            color: #7b6c62;
            font-size: 14px;
        }

        .profile-card {
            background: rgba(255, 255, 255, 0.78);
            border: 1px solid rgba(255, 255, 255, 0.65);
            border-radius: 28px;
            padding: 34px;
            box-shadow: 0 18px 38px rgba(0, 0, 0, 0.08);
            backdrop-filter: blur(10px);
        }

        .profile-card h3 {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 34px;
            font-weight: 700;
            color: #2f1d12;
            margin-bottom: 28px;
        }

        .user-top {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 28px;
            padding-bottom: 22px;
            border-bottom: 1px solid #ead8ca;
        }

        .avatar {
            width: 95px;
            height: 95px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff9800, #ff7600);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 38px;
            font-weight: 700;
            box-shadow: 0 10px 22px rgba(255, 140, 0, 0.22);
            flex-shrink: 0;
        }

        .user-text h4 {
            margin: 0;
            font-size: 30px;
            font-weight: 700;
            color: #2f1d12;
        }

        .user-text p {
            margin: 7px 0 0 0;
            color: #7d6c61;
            font-size: 16px;
        }

        .details-grid {
            display: grid;
            gap: 16px;
        }

        .detail-box {
            background: #fffaf5;
            border: 1px solid #f0e1d5;
            border-radius: 18px;
            padding: 18px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .detail-label {
            color: #8a776b;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .detail-value {
            color: #2f1d12;
            font-size: 18px;
            font-weight: 600;
            text-align: right;
        }

        #popupNotification {
            position: fixed;
            top: 95px;
            right: 30px;
            background: #28a745;
            color: white;
            padding: 14px 22px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.18);
            z-index: 9999;
            font-weight: 600;
            min-width: 260px;
        }
    </style>


</head>

<body>

    <?php if (!empty($successPopup)) { ?>
        <div id="popupNotification">
            <?php echo $successPopup; ?>
        </div>
    <?php } ?>

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
                <a href="home.php">Home</a>
                <a href="products.php">Products</a>
                <a href="cart.php">Cart</a>
                <a href="about-us.php" >About Us</a>
                <a href="profile.php"class="active">Profile</a>
            </div>

            <!-- Logout -->
            <div class="nav-right">
                <a href="logout.php" class="register-btn">LogOut</a>
            </div>

        </div>
    </div>

    <div class="page-wrapper">
        <div class="page-title">
            <h2>My Profile</h2>
            <p>Manage your account information and keep your details up to date.</p>
        </div>

        <div class="profile-layout">
            <div class="side-panel">
                <div class="highlight-card">
                    <h4>Hello, <?php echo htmlspecialchars($initial); ?></h4>
                    <p>Welcome back to The Citrus Cafe. You can view your profile details or update your account information here.</p>
                </div>

                <a href="update-profile.php" class="menu-link">
                    <div class="menu-card">
                        <div class="menu-title">Account Information</div>
                        <div class="menu-sub">Edit your name, phone number and birthday</div>
                    </div>
                </a>

                <a href="change_password.php" class="menu-link">
                    <div class="menu-card">
                        <div class="menu-title">Password</div>
                        <div class="menu-sub">Change your account password securely</div>
                    </div>
                </a>
            </div>

            <div class="profile-card">
                <h3>Personal Information</h3>

                <div class="user-top">
                    <div class="avatar"><?php echo htmlspecialchars($initial); ?></div>
                    <div class="user-text">
                        <h4><?php echo htmlspecialchars($name); ?></h4>
                        <p><?php echo htmlspecialchars($email); ?></p>
                    </div>
                </div>

                <div class="details-grid">
                    <div class="detail-box">
                        <div class="detail-label">Full Name</div>
                        <div class="detail-value"><?php echo htmlspecialchars($name); ?></div>
                    </div>

                    <div class="detail-box">
                        <div class="detail-label">Email Address</div>
                        <div class="detail-value"><?php echo htmlspecialchars($email); ?></div>
                    </div>

                    <div class="detail-box">
                        <div class="detail-label">Phone Number</div>
                        <div class="detail-value"><?php echo htmlspecialchars($phone); ?></div>
                    </div>

                    <div class="detail-box">
                        <div class="detail-label">Birthday</div>
                        <div class="detail-value"><?php echo empty($birthday) ? "Not Updated" : htmlspecialchars($birthday); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmLogout() {
            return confirm("Are you sure you want to logout?");
        }

        setTimeout(function() {
            var popup = document.getElementById("popupNotification");
            if (popup) {
                popup.style.transition = "0.4s";
                popup.style.opacity = "0";
                setTimeout(function() {
                    popup.remove();
                }, 400);
            }
        }, 2500);
    </script>

</body>

</html>