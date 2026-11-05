<?php
require_once __DIR__ . '/dao/UserDAO.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name = trim($_POST["fullname"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($full_name === "" || $phone === "") {
        $error = "Please fill in every field.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($password) < 8) {
        $error = "Password must have at least 8 characters.";
    } else {
        // Public sign-ups are always customer accounts.
        $result = (new UserDAO())->register($full_name, $email, $phone, $password, "user");

        if ($result === true) {
            header("Location: login.php?success=1");
            exit;
        }

        $error = $result === "exists" ? "That email already has an account." : "Registration failed. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Register - The Citrus Cafe</title>

        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/style.css">


        <style>
            body{
                font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                margin:0;
                background:url("img/wallpaper-citrus.jpg") no-repeat center center fixed;
                background-size:cover;
            }

            .nav-wrapper {
                padding-top: 25px;
                padding-bottom: 10px;
            }

            .custom-nav {
                width: 92%;
                margin: auto;
                background: #6b3f24;
                border-radius: 18px;
                padding: 15px 30px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                box-shadow: 0 8px 20px rgba(0,0,0,0.25);
            }

            .nav-left {
                display: flex;
                align-items: center;
            }

            .logo {
                height: 48px;
                margin-right: 12px;
            }

            .brand {
                color: white;
                font-size: 20px;
                font-weight: 600;
            }

            .auth-card{
                background:#f8ead8;
                border-radius:20px;
                padding:40px;
                box-shadow:0 12px 30px rgba(0,0,0,0.18);
                max-width:600px;
                margin:auto;
            }

            .btn-orange{
                background:linear-gradient(to right,#ff8c00,#ff6a00);
                color:white;
                border-radius:30px;
                padding:8px 20px;
                border:none;
                text-decoration:none;
            }

            .btn-orange:hover{
                opacity:0.9;
                color:white;
            }
        </style>
    </head>

    <body>

        <div class="nav-wrapper">
            <div class="custom-nav">
                <div class="nav-left">
                    <img src="img/logonav.png" class="logo" alt="Logo">
                    <span class="brand">The Citrus Cafe</span>
                </div>

                <a href="login.php" class="btn-orange">Login</a>
            </div>
        </div>

        <div class="container mt-5">
            <div class="auth-card">
                <h3>Create Account</h3>

                <form method="post">
                    <input type="text" name="fullname" class="form-control mb-3" placeholder="Full Name" required>
                    <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>
                    <input type="text" name="phone" class="form-control mb-3" placeholder="Phone" required>
                    <input type="password" name="password" class="form-control mb-3" placeholder="Password (at least 8 characters)" minlength="8" required>
                    <button type="submit" class="btn btn-orange w-100">Register</button>
                </form>

                <?php if (!empty($error)) { ?>
                    <p class="text-danger mt-3"><?php echo htmlspecialchars($error); ?></p>
                <?php } ?>

                <?php if (!empty($success)) { ?>
                    <p class="text-success mt-3"><?php echo htmlspecialchars($success); ?></p>
                    <a href="login.php" class="btn btn-dark mt-2">Go to Login</a>
                <?php } ?>
            </div>
        </div>

    </body>
</html>