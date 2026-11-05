<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/dao/UserDAO.php';

require_login_page();

$name = $_SESSION["name"] ?? "";
$email = $_SESSION["email"] ?? "";
$phone = $_SESSION["phone"] ?? "";
$birthday = $_SESSION["birthday"] ?? "";
$errorPopup = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $newName = trim($_POST["fullname"] ?? "");
    $newPhone = trim($_POST["phone"] ?? "");
    $newBirthday = trim($_POST["birthday"] ?? "");

    if (!csrf_valid()) {
        $errorPopup = "Your session expired. Please try again.";
    } elseif ($newName === "" || $newPhone === "") {
        $errorPopup = "Full name and phone are required.";
    } else {
        $dao = new UserDAO();

        if ($dao->updateProfile($_SESSION["user_id"], $newName, $newPhone, $newBirthday)) {
            $updated = $dao->getProfile($_SESSION["user_id"]);
            $_SESSION["name"] = $updated["full_name"] ?? $newName;
            $_SESSION["phone"] = $updated["phone"] ?? $newPhone;
            $_SESSION["birthday"] = $updated["birthday"] ?? "";

            header("Location: profile.php?updated=1");
            exit;
        }

        $errorPopup = "Failed to update profile";
    }
}
?>
    <!DOCTYPE html>
    <html>

    <head>
        <meta charset="UTF-8">
        <title>Update Profile - The Citrus Cafe</title>

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

            .form-card {
                max-width: 900px;
                margin: auto;
                background: rgba(255, 255, 255, 0.78);
                border: 1px solid rgba(255, 255, 255, 0.65);
                border-radius: 28px;
                padding: 34px;
                box-shadow: 0 18px 38px rgba(0, 0, 0, 0.08);
                backdrop-filter: blur(10px);
            }

            .form-card h2 {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                font-size: 36px;
                font-weight: 700;
                color: #2f1d12;
                margin-bottom: 8px;
            }

            .form-card p {
                color: #7a6558;
                margin-bottom: 24px;
            }

            .mini-user {
                display: flex;
                align-items: center;
                gap: 16px;
                margin-bottom: 25px;
                padding-bottom: 20px;
                border-bottom: 1px solid #ead8ca;
            }

            .avatar {
                width: 78px;
                height: 78px;
                border-radius: 50%;
                background: linear-gradient(135deg, #ff9800, #ff7600);
                color: white;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 30px;
                font-weight: 700;
            }

            .mini-user h5 {
                margin: 0;
                color: #2f1d12;
                font-size: 24px;
                font-weight: 700;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }

            .mini-user span {
                color: #7d6c61;
            }

            .form-label {
                font-weight: 600;
                color: #5d473b;
            }

            .form-control {
                border-radius: 14px;
                min-height: 48px;
                border: 1px solid #e8d6ca;
                box-shadow: none;
            }

            .form-control:focus {
                border-color: #ffad42;
                box-shadow: 0 0 0 0.15rem rgba(255, 140, 0, 0.15);
            }

            .btn-orange {
                background: linear-gradient(to right, #ff8c00, #ff6a00);
                color: white;
                border: none;
                border-radius: 30px;
                padding: 12px 24px;
                font-weight: 600;
            }

            .btn-orange:hover {
                color: white;
                opacity: 0.95;
            }

            #popupNotification {
                position: fixed;
                top: 95px;
                right: 30px;
                color: white;
                padding: 14px 22px;
                border-radius: 12px;
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.18);
                z-index: 9999;
                font-weight: 600;
                min-width: 280px;
            }

            .popup-error {
                background: #dc3545;
            }
        </style>
    </head>

    <body>

        <?php if (!empty($errorPopup)) { ?>
            <div id="popupNotification" class="popup-error">
                <?php echo htmlspecialchars($errorPopup); ?>
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
                    <a href="about-us.php">About Us</a>
                    <a href="profile.php" class="active">Profile</a>
                </div>

                <!-- Logout -->
                <div class="nav-right">
                    <a href="logout.php" class="register-btn">LogOut</a>
                </div>

            </div>
        </div>

        <div class="page-wrapper">
            <div class="form-card">
                <h2>Update Profile</h2>
                <p>Edit your personal information below.</p>

                <div class="mini-user">
                    <div class="avatar"><?php echo htmlspecialchars(strtoupper(substr($name, 0, 1))); ?></div>
                    <div>
                        <h5><?php echo htmlspecialchars($name); ?></h5>
                        <span><?php echo htmlspecialchars($email); ?></span>
                    </div>
                </div>

                <form method="post">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="fullname" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($phone); ?>" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Birthday</label>
                        <input type="date" name="birthday" class="form-control" value="<?php echo htmlspecialchars($birthday); ?>">
                    </div>

                    <button type="submit" class="btn btn-orange">Save Changes</button>
                </form>
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