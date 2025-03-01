<?php
session_start();
require_once '../database/DatabaseConnection.php';

// Hàm cập nhật thông tin người dùng
function updateUser($conn, $id, $ho_ten, $email, $so_dien_thoai, $dia_chi) {
    $sql = "UPDATE users SET ho_ten = :ho_ten, email = :email, so_dien_thoai = :so_dien_thoai, dia_chi = :dia_chi WHERE id = :id";
    $stmt = $conn->prepare($sql); // tạo đối tượng thực thi câu lệnh sql
    $stmt->execute([
        ':id' => $id,
        ':ho_ten' => $ho_ten,
        ':email' => $email,
        ':so_dien_thoai' => $so_dien_thoai,
        ':dia_chi' => $dia_chi
    ]); // thực thi câu lệnh sql
    return $stmt->rowCount(); // trả về số dòng bị ảnh hưởng
}

// Kiểm tra phương thức yêu cầu
if ($_SERVER["REQUEST_METHOD"] == "PUT") {
    // Đọc dữ liệu từ php://input
    $data = json_decode(file_get_contents("php://input"), true);

    // Kiểm tra xem các trường dữ liệu có tồn tại không
    if (!isset($data['ho_ten']) || !isset($data['email']) || !isset($data['so_dien_thoai']) || !isset($data['dia_chi'])) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng cung cấp đầy đủ thông tin.']);
        exit();
    }

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập.']);
        exit();
    }

    $id = $_SESSION['user_id'];
    $ho_ten = $data['ho_ten'];
    $email = $data['email'];
    $so_dien_thoai = $data['so_dien_thoai'];
    $dia_chi = $data['dia_chi'];

    updateUser($conn, $id, $ho_ten, $email, $so_dien_thoai, $dia_chi);

    header('Content-Type: application/json'); // xác định kiểu dữ liệu trả về
    echo json_encode(['success' => true, 'message' => 'Cập nhật thông tin thành công.']); // trả về dữ liệu dưới dạng JSON
}
?>