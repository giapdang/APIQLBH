<?php
session_start();
require_once '../database/DatabaseConnection.php';

// Hàm xóa mục giỏ hàng theo id giỏ hàng
function xoaMucGioHang($conn, $gio_hang_id, $nguoi_dung_id) {
    $sql = "DELETE FROM gio_hang WHERE id = :gio_hang_id AND nguoi_dung_id = :nguoi_dung_id";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([
        ':gio_hang_id' => $gio_hang_id,
        ':nguoi_dung_id' => $nguoi_dung_id
    ]);
}

// Kiểm tra phương thức yêu cầu
if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
    // Đọc dữ liệu từ query parameters
    if (!isset($_GET['gio_hang_id']) || !isset($_GET['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng cung cấp ID giỏ hàng và user_id.']);
        exit();
    }

    $gio_hang_id = trim($_GET['gio_hang_id']);
    $nguoi_dung_id = trim($_GET['user_id']);

    // Xóa mục giỏ hàng theo id giỏ hàng
    if (xoaMucGioHang($conn, $gio_hang_id, $nguoi_dung_id)) {
        echo json_encode(['success' => true, 'message' => 'Xóa mục giỏ hàng thành công.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Đã xảy ra lỗi, vui lòng thử lại sau.']);
    }
}
?>