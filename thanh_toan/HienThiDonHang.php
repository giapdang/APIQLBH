<?php
session_start();
require_once '../database/DatabaseConnection.php';

// Hàm lấy đơn hàng của người dùng
function getDonHangByUser($conn, $nguoi_dung_id) {
    $sql = "SELECT dh.id, dh.tong_tien, dh.trang_thai, dh.ngay_tao
            FROM don_hang dh
            WHERE dh.nguoi_dung_id = :nguoi_dung_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':nguoi_dung_id' => $nguoi_dung_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Kiểm tra phương thức yêu cầu
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Đọc dữ liệu từ query parameters
    if (!isset($_GET['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng cung cấp user_id.']);
        exit();
    }

    $nguoi_dung_id = trim($_GET['user_id']);

    // Lấy đơn hàng của người dùng
    $don_hang = getDonHangByUser($conn, $nguoi_dung_id);

    if ($don_hang) {
        header('Content-Type: application/json');
        echo json_encode($don_hang);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy đơn hàng.']);
    }
}
?>