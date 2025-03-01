<?php
session_start();
require_once '../database/DatabaseConnection.php';

// Lấy tất cả sản phẩm từ cơ sở dữ liệu theo tên sản phẩm
function getSanPhamTheoTen($conn, $ten_san_pham) {
    $sql = "SELECT sp.id, sp.ten_san_pham
            FROM san_pham sp WHERE sp.ten_san_pham LIKE :ten_san_pham";
    $stmt = $conn->prepare($sql); // tạo đối tượng thực thi câu lệnh sql
    $stmt->execute([':ten_san_pham' => '%'.$ten_san_pham.'%']); // thực thi câu lệnh sql
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC); // lấy tất cả kết quả trả về từ câu lệnh sql
    return $result; // trả về kết quả
}

// Trả về dữ liệu dưới dạng JSON
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (!isset($_GET['ten_san_pham'])) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng cung cấp tên sản phẩm.']);
        exit();
    }

    $ten_san_pham = $_GET['ten_san_pham'];
    $san_pham = getSanPhamTheoTen($conn, $ten_san_pham);
    header('Content-Type: application/json'); // xác định kiểu dữ liệu trả về
    echo json_encode($san_pham); // trả về dữ liệu dưới dạng JSON
}
?>