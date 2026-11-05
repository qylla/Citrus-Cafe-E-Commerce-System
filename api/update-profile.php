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
$full_name = $_POST['fullname'] ?? '';
$phone = $_POST['phone'] ?? '';
$birthday = $_POST['birthday'] ?? '';

if (empty($user_id) || empty($full_name) || empty($phone)) {
    echo json_encode([
        "success" => false,
        "message" => "User ID, full name, and phone are required"
    ]);
    exit;
}

$dao = new UserDAO();
$result = $dao->updateProfile($user_id, $full_name, $phone, $birthday);

if ($result) {
    $updatedUser = $dao->getProfile($user_id);

    echo json_encode([
        "success" => true,
        "message" => "Profile updated successfully",
        "profile" => $updatedUser
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Failed to update profile"
    ]);
}
?>