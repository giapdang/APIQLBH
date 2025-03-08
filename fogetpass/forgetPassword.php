<?php
session_start();
require_once '../database/DatabaseConnection.php';

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "PUT") {
    // Đọc dữ liệu JSON từ request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Kiểm tra xem dữ liệu JSON có tồn tại và có các trường cần thiết không
    if (!isset($data['email']) || !isset($data['new_password']) || !isset($data['confirm_password'])) {
        echo json_encode(["error" => "Vui lòng cung cấp email, mật khẩu mới và xác nhận mật khẩu."]);
        exit();
    }

    $email = trim($data['email']);
    $newPassword = trim($data['new_password']);
    $confirmPassword = trim($data['confirm_password']);

    // Kiểm tra xem các trường dữ liệu có bị trống không
    if (empty($email) || empty($newPassword) || empty($confirmPassword)) {
        echo json_encode(["error" => "Vui lòng nhập email, mật khẩu mới và xác nhận mật khẩu."]);
        exit();
    }

    // Kiểm tra xem mật khẩu mới và xác nhận mật khẩu có khớp không
    if ($newPassword !== $confirmPassword) {
        echo json_encode(["error" => "Mật khẩu mới và xác nhận mật khẩu không khớp."]);
        exit();
    }

    try {
        // Kiểm tra xem email có tồn tại không
        $sql = "SELECT id FROM users WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $userId = $user['id'];

            // Hash mật khẩu mới
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Cập nhật mật khẩu mới
            $sql = "UPDATE users SET mat_khau = :new_password WHERE id = :user_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':new_password', $hashedPassword, PDO::PARAM_STR);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(["success" => "Mật khẩu đã được cập nhật thành công."]);
        } else {
            echo json_encode(["error" => "Email không tồn tại."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["error" => "Lỗi hệ thống, vui lòng thử lại sau."]);
        error_log("Lỗi đặt lại mật khẩu: " . $e->getMessage());
    }
}
?>