<?php
session_start();
require_once '../database/DatabaseConnection.php';

// Lấy tất cả user từ cơ sở dữ liệu
function getAllUser($conn)
{
    $sql = "SELECT id, ho_ten, email, so_dien_thoai, dia_chi , ngay_tao FROM users";
    $stmt = $conn->prepare($sql); // tạo đối tượng thực thi câu lệnh sql
    $stmt->execute(); // thực thi câu lệnh sql
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC); // lấy tất cả kết quả trả về từ câu lệnh sql
    return $result; // trả về kết quả
}

// Trả về dữ liệu dưới dạng JSON
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $user = getAllUser($conn);
    header('Content-Type: application/json'); // xác định kiểu dữ liệu trả về
    echo json_encode($user); // trả về dữ liệu dưới dạng JSON
}
 ?>