<?php
session_start();
require_once '../database/DatabaseConnection.php';

// Lấy tất cả sản phẩm từ cơ sở dữ liệu theo id
function getSanPham($conn, $id) {
    $sql = "SELECT sp.id, sp.hinh_anh , sp.ten_san_pham , sp.gia , sp.so_luong , sp.mo_ta FROM san_pham sp WHERE sp.id = :id";
    $stmt = $conn->prepare($sql); // tạo đối tượng thực thi câu lệnh sql
    $stmt->execute([':id' => $id]); // thực thi câu lệnh sql
    $result = $stmt->fetch(PDO::FETCH_ASSOC); // lấy tất cả kết quả trả về từ câu lệnh sql
    return $result; // trả về kết quả
}

// Trả về dữ liệu dưới dạng JSON
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = $_GET['id'];
    $san_pham = getSanPham($conn, $id);
    header('Content-Type: application/json'); // xác định kiểu dữ liệu trả về
    echo json_encode($san_pham); // trả về dữ liệu dưới dạng JSON
}
 ?>