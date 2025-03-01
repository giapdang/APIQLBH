<?php 
require_once '../database/DatabaseConnection.php';

// Lấy tất cả danh mục từ cơ sở dữ liệu
function getAllCategories($conn)
{
    $sql = "SELECT id, ten_danh_muc FROM danh_muc";
    $stmt = $conn->prepare($sql); // tạo đối tượng thực thi câu lệnh sql
    $stmt->execute(); // thực thi câu lệnh sql
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC); // lấy tất cả kết quả trả về từ câu lệnh sql
    return $result; // trả về kết quả
}

// tra ve du lieu dang JSON
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $categories = getAllCategories($conn);
    header('Content-Type: application/json'); // xác định kiểu dữ liệu trả về
    echo json_encode($categories); // trả về dữ liệu dưới dạng JSON
}
?>