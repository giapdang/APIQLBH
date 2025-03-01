<?php
session_start();
require_once '../database/DatabaseConnection.php';

// Hàm lấy giỏ hàng của người dùng
function getGioHang($conn, $nguoi_dung_id) {
    $sql = "SELECT gh.id,sp.id as id_san_pham, sp.ten_san_pham, sp.gia, gh.so_luong, sp.hinh_anh
            FROM gio_hang gh
            JOIN san_pham sp ON gh.san_pham_id = sp.id
            WHERE gh.nguoi_dung_id = :nguoi_dung_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':nguoi_dung_id' => $nguoi_dung_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Kiểm tra phương thức yêu cầu
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập.']);
        exit();
    }

    $nguoi_dung_id = $_SESSION['user_id'];
    $gio_hang = getGioHang($conn, $nguoi_dung_id);
    header('Content-Type: application/json');
    echo json_encode($gio_hang);
}
?>