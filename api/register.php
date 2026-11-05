<?php
    header("Content-Type: application/json");

    require_once __DIR__ . '/../dao/UserDAO.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            "success" => false,
            "message" => "Invalid request method"
        ]);
        exit;
    }

    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($full_name) || empty($email) || empty($phone) || empty($password)) {
        echo json_encode([
            "success" => false,
            "message" => "All fields are required"
        ]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
        echo json_encode([
            "success" => false,
            "message" => "Enter a valid email and a password of at least 8 characters"
        ]);
        exit;
    }

    // Public sign-ups are always customer accounts.
    $dao = new UserDAO();
    $result = $dao->register($full_name, $email, $phone, $password, "user");

    if ($result === "exists") {
        echo json_encode([
            "success" => false,
            "message" => "Email already exists"
        ]);
    } elseif ($result === true) {
        echo json_encode([
            "success" => true,
            "message" => "User registered successfully"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Registration failed"
        ]);
    }
?>