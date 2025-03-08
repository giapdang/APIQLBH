<?php
require_once '../database/DatabaseConnection.php';

// Lấy thông tin user từ cơ sở dữ liệu theo id user
function getUser($conn, $id)
{
    $sql = "SELECT id, ho_ten, email, so_dien_thoai, dia_chi FROM users WHERE id = :id";
    $stmt = $conn->prepare($sql); // tạo đối tượng thực thi câu lệnh sql
    $stmt->execute([':id' => $id]); // thực thi câu lệnh sql
    $result = $stmt->fetch(PDO::FETCH_ASSOC); // lấy kết quả trả về từ câu lệnh sql
    return $result; // trả về kết quả
}

// Trả về dữ liệu dưới dạng JSON
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (!isset($_GET['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng cung cấp user_id.']);
        exit();
    }

    $id = trim($_GET['user_id']);
    $user = getUser($conn, $id);
    header('Content-Type: application/json'); // xác định kiểu dữ liệu trả về
    echo json_encode($user); // trả về dữ liệu dưới dạng JSON
}
?>