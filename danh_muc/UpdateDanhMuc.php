<?php
require_once '../database/DatabaseConnection.php';

// Hàm cập nhật danh mục
function updateDanhMuc($conn, $id, $ten_danh_muc, $mo_ta) {
    $sql = "UPDATE danh_muc SET ten_danh_muc = :ten_danh_muc, mo_ta = :mo_ta WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':ten_danh_muc' => $ten_danh_muc,
        ':mo_ta' => $mo_ta,
        ':id' => $id
    ]);
    return $stmt->rowCount(); // Trả về số hàng bị ảnh hưởng
}

// Kiểm tra phương thức yêu cầu
if ($_SERVER["REQUEST_METHOD"] == "PUT") {
    // Đọc dữ liệu JSON từ request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Kiểm tra xem dữ liệu JSON có tồn tại và có các trường cần thiết không
    if (!isset($data['id']) || !isset($data['ten_danh_muc']) || !isset($data['mo_ta'])) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng cung cấp đầy đủ thông tin.']);
        exit();
    }

    $id = $data['id'];
    $ten_danh_muc = $data['ten_danh_muc'];
    $mo_ta = $data['mo_ta'];

    // Gọi hàm cập nhật thông tin danh mục
    $result = updateDanhMuc($conn, $id, $ten_danh_muc, $mo_ta);

    // Kiểm tra kết quả và trả về phản hồi
    if ($result > 0) {
        echo json_encode(['success' => true, 'message' => 'Cập nhật thông tin danh mục thành công.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không có thay đổi nào được thực hiện.']);
    }
}
?>