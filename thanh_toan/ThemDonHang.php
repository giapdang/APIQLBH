<?php
session_start();
require_once '../database/DatabaseConnection.php';

// Hàm thêm đơn hàng
function themDonHang($conn, $nguoi_dung_id, $tong_tien) {
    $sql = "INSERT INTO don_hang (nguoi_dung_id, tong_tien) VALUES (:nguoi_dung_id, :tong_tien)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':nguoi_dung_id' => $nguoi_dung_id,
        ':tong_tien' => $tong_tien
    ]);
    return $conn->lastInsertId();
}

// Hàm thêm chi tiết đơn hàng
function themChiTietDonHang($conn, $don_hang_id, $san_pham_id, $so_luong, $don_gia) {
    $sql = "INSERT INTO chi_tiet_don_hang (don_hang_id, san_pham_id, so_luong, don_gia) VALUES (:don_hang_id, :san_pham_id, :so_luong, :don_gia)";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([
        ':don_hang_id' => $don_hang_id,
        ':san_pham_id' => $san_pham_id,
        ':so_luong' => $so_luong,
        ':don_gia' => $don_gia
    ]);
}

// Hàm xóa giỏ hàng theo danh sách ID
function xoaGioHangTheoId($conn, $danh_sach_gio_hang_id) {
    if (!empty($danh_sach_gio_hang_id)) {
        $placeholders = implode(',', array_fill(0, count($danh_sach_gio_hang_id), '?'));
        $sql = "DELETE FROM gio_hang WHERE id IN ($placeholders)";
        $stmt = $conn->prepare($sql);
        return $stmt->execute($danh_sach_gio_hang_id);
    }
    return false;
}

// Kiểm tra phương thức yêu cầu
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($data['san_pham']) || !is_array($data['san_pham'])) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng cung cấp đầy đủ thông tin.']);
        exit();
    }
    
    $nguoi_dung_id = $_SESSION['user_id']; // Giả sử user_id được lưu trong session
    $tong_tien = 0;
    $danh_sach_gio_hang_id = [];

    // Tính tổng tiền đơn hàng
    foreach ($data['san_pham'] as $san_pham) {
        $tong_tien += $san_pham['so_luong'] * $san_pham['don_gia'];
    }

    // Thêm đơn hàng
    $don_hang_id = themDonHang($conn, $nguoi_dung_id, $tong_tien);

    if ($don_hang_id) {
        foreach ($data['san_pham'] as $san_pham) {
            themChiTietDonHang($conn, $don_hang_id, $san_pham['san_pham_id'], $san_pham['so_luong'], $san_pham['don_gia']);
            $danh_sach_gio_hang_id[] = $san_pham['gio_hang_id'];
        }

        // Xóa các mục giỏ hàng đã mua
        xoaGioHangTheoId($conn, $danh_sach_gio_hang_id);
        
        echo json_encode(['success' => true, 'message' => 'Đơn hàng đã được tạo và giỏ hàng liên quan đã bị xóa.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Đã xảy ra lỗi, vui lòng thử lại sau.']);
    }
}
?>
