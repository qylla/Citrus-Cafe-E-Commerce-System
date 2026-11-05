<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/dao/UserDAO.php';

require_login_page();

$errorPopup = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $oldPassword = $_POST["oldPassword"] ?? "";
    $newPassword = $_POST["newPassword"] ?? "";
    $confirmPassword = $_POST["confirmPassword"] ?? "";

    if (!csrf_valid()) {
        $errorPopup = "Your session expired. Please try again.";
    } elseif ($newPassword !== $confirmPassword) {
        $errorPopup = "New password and confirm password do not match";
    } elseif (strlen($newPassword) < 8) {
        $errorPopup = "New password must have at least 8 characters";
    } else {
        $result = (new UserDAO())->changePassword($_SESSION["user_id"], $oldPassword, $newPassword);

        if ($result === true) {
            session_regenerate_id(true);
            header("Location: profile.php?password_changed=1");
            exit;
        }

        $errorPopup = $result === "wrong_old" ? "Old password is incorrect" : "Failed to change password";
    }
}
?>
    <!DOCTYPE html>
    <html>

    <head>
        <meta charset="UTF-8">
        <title>Change Password - The Citrus Cafe</title>

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
                color: #2f1d12;
                margin-bottom: 8px;
                font-size: 36px;
                font-weight: 700;
            }

            .form-card p {
                color: #7a6558;
                margin-bottom: 24px;
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

            .password-field {
                position: relative;
            }

            .password-field span {
                position: absolute;
                right: 15px;
                top: 50%;
                transform: translateY(-50%);
                cursor: pointer;
            }

            .password-rules {
                font-size: 14px;
                margin-top: 12px;
                padding-left: 20px;
            }

            .password-rules li {
                margin-bottom: 5px;
                color: #999;
            }

            .password-rules li.active {
                color: #28a745;
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
                <h2>Change Password</h2>
                <p>Keep your account secure by updating your password.</p>

                <form method="post">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label class="form-label">Old Password</label>
                        <div class="password-field">
                            <input type="password" name="oldPassword" id="oldPassword" class="form-control" required>
                            <span onclick="togglePassword('oldPassword')">👁</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <div class="password-field">
                            <input type="password" name="newPassword" id="newPassword" class="form-control" onkeyup="checkPassword()" required>
                            <span onclick="togglePassword('newPassword')">👁</span>
                        </div>
                    </div>

                    <ul class="password-rules">
                        <li id="length">Minimum 8 characters</li>
                        <li id="upper">One uppercase character</li>
                        <li id="lower">One lowercase character</li>
                        <li id="number">One number</li>
                    </ul>

                    <div class="mb-4">
                        <label class="form-label">Confirm New Password</label>
                        <div class="password-field">
                            <input type="password" name="confirmPassword" id="confirmPassword" class="form-control" required>
                            <span onclick="togglePassword('confirmPassword')">👁</span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-orange">Update Password</button>
                </form>
            </div>
        </div>

        <script>
            function checkPassword() {
                var password = document.getElementById("newPassword").value;
                toggle("length", /^.{8,}$/.test(password));
                toggle("upper", /[A-Z]/.test(password));
                toggle("lower", /[a-z]/.test(password));
                toggle("number", /[0-9]/.test(password));
            }

            function toggle(id, valid) {
                var el = document.getElementById(id);
                if (valid) {
                    el.classList.add("active");
                } else {
                    el.classList.remove("active");
                }
            }

            function togglePassword(id) {
                var input = document.getElementById(id);
                input.type = input.type === "password" ? "text" : "password";
            }

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