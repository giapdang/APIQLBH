<?php
//dành cho admin
require_once '../database/DatabaseConnection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Đọc dữ liệu JSON từ request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Kiểm tra xem dữ liệu JSON có tồn tại và có các trường cần thiết không
    if (isset($data['ten_danh_muc']) && isset($data['mo_ta'])) {
        $ten_danh_muc = $data['ten_danh_muc'];
        $mo_ta = $data['mo_ta'];

        try {
            // Chuẩn bị câu lệnh SQL
            $sql = "INSERT INTO danh_muc (ten_danh_muc, mo_ta) VALUES (:ten_danh_muc, :mo_ta)";
            $stmt = $conn->prepare($sql);
            
            // Thực thi câu lệnh với các tham số
            $stmt->execute([
                ':ten_danh_muc' => $ten_danh_muc,
                ':mo_ta' => $mo_ta
            ]);

            echo json_encode(["success" => "Thêm danh mục thành công!"]);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Error: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["error" => "Vui lòng cung cấp đầy đủ thông tin."]);
    }
}
?>