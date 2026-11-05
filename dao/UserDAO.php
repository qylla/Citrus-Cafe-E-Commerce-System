<?php
require_once __DIR__ . '/../config/database.php';

class UserDAO {

    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function login($login, $password, $role) {
        $sql = "SELECT * FROM users WHERE (email = ? OR phone = ?) AND role = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $login, $login, $role);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                return $user;
            }
        }

        return null;
    }

    public function register($full_name, $email, $phone, $password, $role = "user") {
        $checkSql = "SELECT * FROM users WHERE email = ?";
        $checkStmt = $this->conn->prepare($checkSql);
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            return "exists";
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (full_name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssss", $full_name, $email, $phone, $hashedPassword, $role);

        return $stmt->execute();
    }

    public function getProfile($user_id) {
        $sql = "SELECT user_id, full_name, email, phone, birthday, role, created_at
                FROM users
                WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return null;
    }

    public function updateProfile($user_id, $full_name, $phone, $birthday) {
        $birthday = $birthday === '' ? null : $birthday;

        $sql = "UPDATE users 
                SET full_name = ?, phone = ?, birthday = ?
                WHERE user_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssi", $full_name, $phone, $birthday, $user_id);

        return $stmt->execute();
    }

    public function changePassword($user_id, $oldPassword, $newPassword) {
        $sql = "SELECT password FROM users WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return "not_found";
        }

        $user = $result->fetch_assoc();
        $currentPassword = $user["password"];

        $matched = false;

        if (password_verify($oldPassword, $currentPassword)) {
            $matched = true;
        }

        if (!$matched) {
            return "wrong_old";
        }

        $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $updateSql = "UPDATE users SET password = ? WHERE user_id = ?";
        $updateStmt = $this->conn->prepare($updateSql);
        $updateStmt->bind_param("si", $hashedNewPassword, $user_id);

        if ($updateStmt->execute()) {
            return true;
        }

        return false;
    }
}
?>