<?php
session_start();
require_once '../database/DatabaseConnection.php';

// Hàm xác thực đơn hàng
function xacThucDonHang($conn, $don_hang_id, $trang_thai) {
    $sql = "UPDATE don_hang SET trang_thai = :trang_thai WHERE id = :don_hang_id";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([
        ':trang_thai' => $trang_thai,
        ':don_hang_id' => $don_hang_id
    ]);
}

// Hàm cập nhật số lượng sản phẩm
function capNhatSoLuongSanPham($conn, $san_pham_id, $so_luong) {
    $sql = "UPDATE san_pham SET so_luong = so_luong - :so_luong WHERE id = :san_pham_id";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([
        ':so_luong' => $so_luong,
        ':san_pham_id' => $san_pham_id
    ]);
}

// Hàm lấy chi tiết đơn hàng
function layChiTietDonHang($conn, $don_hang_id) {
    $sql = "SELECT san_pham_id, so_luong FROM chi_tiet_don_hang WHERE don_hang_id = :don_hang_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':don_hang_id' => $don_hang_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Kiểm tra phương thức yêu cầu
if ($_SERVER["REQUEST_METHOD"] == "PUT") {
    // Đọc dữ liệu từ php://input
    $data = json_decode(file_get_contents("php://input"), true);

    // Kiểm tra xem đơn hàng có tồn tại không
    $sql = "SELECT * FROM don_hang WHERE id = :don_hang_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':don_hang_id' => $data['don_hang_id']]);
    $don_hang = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$don_hang) {
        echo json_encode(['success' => false, 'message' => 'Đơn hàng không tồn tại.']);
        exit();
    }

    // Kiểm tra xem các trường dữ liệu có tồn tại không
    if (!isset($data['don_hang_id']) || !isset($data['trang_thai'])) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng cung cấp ID đơn hàng và trạng thái.']);
        exit();
    }

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập.']);
        exit();
    }

    $don_hang_id = $data['don_hang_id'];
    $trang_thai = $data['trang_thai'];
    $nguoi_dung_id = $_SESSION['user_id']; // Giả sử user_id được lưu trong session

    // Xác thực đơn hàng
    if (xacThucDonHang($conn, $don_hang_id, $trang_thai)) {
        // Nếu trạng thái là 'completed', cập nhật số lượng sản phẩm
        if ($trang_thai == 'completed') {
            $chi_tiet_don_hang = layChiTietDonHang($conn, $don_hang_id);
            foreach ($chi_tiet_don_hang as $chi_tiet) {
                capNhatSoLuongSanPham($conn, $chi_tiet['san_pham_id'], $chi_tiet['so_luong']);
            }
        }
        echo json_encode(['success' => true, 'message' => 'Xác thực đơn hàng thành công.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Đã xảy ra lỗi, vui lòng thử lại sau.']);
    }
}
?>