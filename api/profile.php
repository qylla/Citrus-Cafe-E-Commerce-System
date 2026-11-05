<?php
    header("Content-Type: application/json");

    require_once __DIR__ . '/../config/auth.php';
    require_once __DIR__ . '/../dao/UserDAO.php';

    api_require_login();

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        echo json_encode([
            "success" => false,
            "message" => "Invalid request method"
        ]);
        exit;
    }

    $user_id = $_SESSION['user_id'];

    if (empty($user_id)) {
        echo json_encode([
            "success" => false,
            "message" => "User ID is required"
        ]);
        exit;
    }

    $dao = new UserDAO();
    $user = $dao->getProfile($user_id);

    if ($user != null) {
        echo json_encode([
            "success" => true,
            "profile" => $user
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "User not found"
        ]);
    }
?>