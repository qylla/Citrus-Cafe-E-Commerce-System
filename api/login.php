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

    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    if (empty($login) || empty($password) || empty($role)) {
        echo json_encode([
            "success" => false,
            "message" => "Login, password, and role are required"
        ]);
        exit;
    }

    $dao = new UserDAO();
    $user = $dao->login($login, $password, $role);

    if ($user != null) {
        echo json_encode([
            "success" => true,
            "message" => "Login successful",
            "user" => [
                "user_id" => $user["user_id"],
                "full_name" => $user["full_name"],
                "email" => $user["email"],
                "phone" => $user["phone"],
                "birthday" => $user["birthday"],
                "role" => $user["role"]
            ]
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Invalid email, phone, password, or role"
        ]);
    }
?>