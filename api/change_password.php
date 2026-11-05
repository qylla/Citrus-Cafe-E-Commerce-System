<?php
    header("Content-Type: application/json");
    require_once __DIR__ . '/../config/auth.php';
    require_once __DIR__ . '/../dao/UserDAO.php';

    api_require_login();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            "success" => false,
            "message" => "Invalid request method"
        ]);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $oldPassword = $_POST['oldPassword'] ?? '';
    $newPassword = $_POST['newPassword'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    if (empty($user_id) || empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
        echo json_encode([
            "success" => false,
            "message" => "All fields are required"
        ]);
        exit;
    }

    if ($newPassword !== $confirmPassword) {
        echo json_encode([
            "success" => false,
            "message" => "New password and confirm password do not match"
        ]);
        exit;
    }

    if (strlen($newPassword) < 8) {
        echo json_encode([
            "success" => false,
            "message" => "New password must have at least 8 characters"
        ]);
        exit;
    }

    $dao = new UserDAO();
    $result = $dao->changePassword($user_id, $oldPassword, $newPassword);

    if ($result === "wrong_old") {
        echo json_encode([
            "success" => false,
            "message" => "Old password is incorrect"
        ]);
    } elseif ($result === "not_found") {
        echo json_encode([
            "success" => false,
            "message" => "User not found"
        ]);
    } elseif ($result === true) {
        echo json_encode([
            "success" => true,
            "message" => "Password changed successfully"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Failed to change password"
        ]);
    }
?>