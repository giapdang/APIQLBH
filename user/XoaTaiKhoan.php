<?php
require_once '../database/DatabaseConnection.php';

header("Content-Type: application/json");

// Hàm xóa tài khoản người dùng
function xoaTaiKhoan($conn, $user_id) {
    $sql = "DELETE FROM users WHERE id = :user_id";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([':user_id' => $user_id]);
}

// Kiểm tra phương thức yêu cầu
if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
    // Đọc dữ liệu từ query parameters
    if (!isset($_GET['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng cung cấp user_id.']);
        exit();
    }

    $user_id = trim($_GET['user_id']);

    // Xóa tài khoản người dùng
    if (xoaTaiKhoan($conn, $user_id)) {
        echo json_encode(['success' => true, 'message' => 'Xóa tài khoản thành công.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Đã xảy ra lỗi, vui lòng thử lại sau.']);
    }
}
?>