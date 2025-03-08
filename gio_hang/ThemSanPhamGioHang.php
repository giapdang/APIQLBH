<?php
session_start();
require_once '../database/DatabaseConnection.php';

// Hàm thêm sản phẩm vào giỏ hàng
function themSanPhamGioHang($conn, $nguoi_dung_id, $san_pham_id, $so_luong) {
    $sql = "INSERT INTO gio_hang (nguoi_dung_id, san_pham_id, so_luong) VALUES (:nguoi_dung_id, :san_pham_id, :so_luong)";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([
        ':nguoi_dung_id' => $nguoi_dung_id,
        ':san_pham_id' => $san_pham_id,
        ':so_luong' => $so_luong
    ]);
}

// Kiểm tra phương thức yêu cầu
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Đọc dữ liệu từ php://input
    $data = json_decode(file_get_contents("php://input"), true);

    // Kiểm tra xem các trường dữ liệu có tồn tại không
    if (!isset($data['user_id']) || !isset($data['san_pham_id']) || !isset($data['so_luong'])) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng cung cấp đầy đủ thông tin.']);
        exit();
    }

    $nguoi_dung_id = trim($data['user_id']);
    $san_pham_id = trim($data['san_pham_id']);
    $so_luong = trim($data['so_luong']);

    // Thêm sản phẩm vào giỏ hàng
    if (themSanPhamGioHang($conn, $nguoi_dung_id, $san_pham_id, $so_luong)) {
        echo json_encode(['success' => true, 'message' => 'Thêm sản phẩm vào giỏ hàng thành công.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Đã xảy ra lỗi, vui lòng thử lại sau.']);
    }
}
?>