<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/dao/UserDAO.php';

secure_session_start();

$error = "";
$success = isset($_GET["success"]) ? "Account created successfully. Please login." : "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $lockedUntil = $_SESSION["login_locked_until"] ?? 0;

    if ($lockedUntil > time()) {
        $error = "Too many failed attempts. Please try again in a few minutes.";
    } else {
        $login = trim($_POST["login"] ?? "");
        $password = $_POST["password"] ?? "";
        $role = ($_POST["role"] ?? "") === "admin" ? "admin" : "user";

        $user = (new UserDAO())->login($login, $password, $role);

        if ($user !== null) {
            session_regenerate_id(true);
            unset($_SESSION["login_failures"], $_SESSION["login_locked_until"]);

            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION["name"] = $user["full_name"];
            $_SESSION["email"] = $user["email"];
            $_SESSION["phone"] = $user["phone"];
            $_SESSION["birthday"] = $user["birthday"];
            $_SESSION["role"] = $user["role"];

            header("Location: " . ($user["role"] === "admin" ? "Admin/dashboard.php" : "home.php"));
            exit;
        }

        $_SESSION["login_failures"] = ($_SESSION["login_failures"] ?? 0) + 1;
        if ($_SESSION["login_failures"] >= 5) {
            $_SESSION["login_locked_until"] = time() + 300;
            $_SESSION["login_failures"] = 0;
        }

        $error = "Invalid email, phone, password, or role.";
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Login - The Citrus Cafe</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">

    <style>
        * {
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f3e3cf;
            margin: 0;
            background:
                linear-gradient(rgba(0, 0, 0, 0.15), rgba(0, 0, 0, 0.45)),
                url("img/wallpaper-citrus.jpg") no-repeat center center fixed;
            background-size: cover;
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
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
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

        .btn-orange {
            background: linear-gradient(to right, #ff8c00, #ff6a00);
            color: white;
            border: none;
            border-radius: 30px;
            padding: 10px 22px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }

        .btn-orange:hover {
            color: white;
            opacity: 0.95;
        }

        .main-wrapper {
            width: 90%;
            max-width: 1250px;
            margin: 50px auto 70px;
            background: #ffe3c7;
            border-radius: 28px;
            padding: 55px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 50px;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.14);
        }

        .login-left {
            flex: 1;
            max-width: 470px;
        }

        .login-left h1 {
            font-size: 48px;
            font-weight: 700;
            color: #2f1d12;
            margin-bottom: 10px;
            line-height: 1.15;
        }

        .login-left p {
            color: #6e625b;
            margin-bottom: 28px;
            font-size: 15px;
        }

        .auth-card {
            background: white;
            border-radius: 22px;
            padding: 28px;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08);
        }

        .form-control {
            border-radius: 12px;
            min-height: 48px;
            border: 1px solid #e2d5c8;
        }

        .role-box {
            background: #fff7ef;
            border: 1px solid #f0d4b8;
            border-radius: 14px;
            padding: 12px 14px;
            margin-bottom: 18px;
        }

        .role-box label.title {
            font-weight: 600;
            color: #5b3720;
            display: block;
            margin-bottom: 8px;
        }

        .role-option {
            margin-right: 18px;
            color: #4c4037;
        }

        .error-msg {
            margin-top: 15px;
            color: #dc3545;
            font-size: 14px;
            font-weight: 500;
        }

        .login-illustration {
            flex: 1;
            text-align: center;
        }

        .login-illustration img {
            width: 100%;
            max-width: 430px;
            border-radius: 24px;
            object-fit: cover;
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

            <a href="register.php" class="btn-orange">Register</a>
        </div>
    </div>

    <div class="main-wrapper">

        <div class="login-left">
            <h1>Hello,<br>Welcome Back</h1>
            <p>Login to continue into The Citrus Cafe system.</p>

            <div class="auth-card">
                <form method="post">
                    <input type="text" name="login" class="form-control mb-3" placeholder="Email or Phone" required>
                    <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>

                    <div class="role-box">
                        <label class="title">Login As</label>
                        <label class="role-option">
                            <input type="radio" name="role" value="user" checked> User
                        </label>
                        <label class="role-option">
                            <input type="radio" name="role" value="admin"> Admin
                        </label>
                    </div>

                    <button type="submit" class="btn btn-orange w-100">Login</button>
                </form>

                <?php if (!empty($success)) { ?>
                    <div class="text-success mt-3"><?php echo htmlspecialchars($success); ?></div>
                <?php } ?>

                <?php if (!empty($error)) { ?>
                    <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
                <?php } ?>
            </div>
        </div>

        <div class="login-illustration">
            <img src="img/cafe-img.png" alt="Cafe Image">
        </div>

    </div>

</body>

</html>