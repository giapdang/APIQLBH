<?php
session_start();
require_once '../database/DatabaseConnection.php';

// Hàm kiểm tra mật khẩu cũ
function checkMatKhau($conn, $userId, $oldPassword) {
    $sql = "SELECT mat_khau FROM users WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return password_verify($oldPassword, $user['mat_khau']);
}

// Hàm cập nhật mật khẩu mới
function updateMatKhau($conn, $userId, $newPassword) {
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $sql = "UPDATE users SET mat_khau = :mat_khau WHERE id = :id";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([':mat_khau' => $hashedPassword, ':id' => $userId]);
}

// Kiểm tra phương thức yêu cầu
if ($_SERVER["REQUEST_METHOD"] == "PUT") {
    // Đọc dữ liệu từ php://input
    $data = json_decode(file_get_contents("php://input"), true);

    // Kiểm tra xem các trường dữ liệu có tồn tại không
    if (!isset($data['old_password']) || !isset($data['new_password']) || !isset($data['confirm_password'])) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng cung cấp đầy đủ thông tin.']);
        exit();
    }

    $userId = $_SESSION['user_id']; // Giả sử user_id được lưu trong session
    $oldPassword = trim($data['old_password']);
    $newPassword = trim($data['new_password']);
    $confirmPassword = trim($data['confirm_password']);

    // Kiểm tra xem mật khẩu mới và xác nhận mật khẩu mới có khớp không
    if ($newPassword !== $confirmPassword) {
        echo json_encode(['success' => false, 'message' => 'Mật khẩu mới và xác nhận mật khẩu không khớp.']);
        exit();
    }

    // Kiểm tra mật khẩu cũ
    if (!checkMatKhau($conn, $userId, $oldPassword)) {
        echo json_encode(['success' => false, 'message' => 'Mật khẩu cũ không đúng.']);
        exit();
    }

    // Cập nhật mật khẩu mới
    if (updateMatKhau($conn, $userId, $newPassword)) {
        echo json_encode(['success' => true, 'message' => 'Đổi mật khẩu thành công.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Đã xảy ra lỗi, vui lòng thử lại sau.']);
    }
}
?>