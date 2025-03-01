<?php
require_once '../database/DatabaseConnection.php';

// Lấy chi tiết danh mục từ cơ sở dữ liệu
function getCategoryDetail($conn, $id)
{
    $sql = "SELECT id, ten_danh_muc, mo_ta FROM danh_muc WHERE id = :id";
    $stmt = $conn->prepare($sql); // tạo đối tượng thực thi câu lệnh sql
    $stmt->execute([':id' => $id]); // thực thi câu lệnh sql
    $result = $stmt->fetch(PDO::FETCH_ASSOC); // lấy kết quả trả về từ câu lệnh sql
    return $result; // trả về kết quả
}

// Trả về dữ liệu dưới dạng JSON
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = $_GET['id'];
    $category = getCategoryDetail($conn, $id);
    header('Content-Type: application/json'); // xác định kiểu dữ liệu trả về
    echo json_encode($category); // trả về dữ liệu dưới dạng JSON
}
?>