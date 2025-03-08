<?php
require_once '../database/DatabaseConnection.php';

// Hàm lấy chi tiết đơn hàng
function getChiTietDonHang($conn, $don_hang_id) {
    $sql = "SELECT ctdh.id, sp.ten_san_pham, sp.gia, ctdh.so_luong, ctdh.thanh_tien , sp.hinh_anh
            FROM chi_tiet_don_hang ctdh
            JOIN san_pham sp ON ctdh.san_pham_id = sp.id
            WHERE ctdh.don_hang_id = :don_hang_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':don_hang_id' => $don_hang_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Kiểm tra phương thức yêu cầu
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (!isset($_GET['don_hang_id'])) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng cung cấp ID đơn hàng.']);
        exit();
    }

    $don_hang_id = trim($_GET['don_hang_id']);
    $chi_tiet_don_hang = getChiTietDonHang($conn, $don_hang_id);
    header('Content-Type: application/json');
    echo json_encode($chi_tiet_don_hang);
}
?>