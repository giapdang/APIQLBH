<?php
session_start();
require_once '../database/DatabaseConnection.php';

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "PUT") {
    // Đọc dữ liệu JSON từ request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Kiểm tra xem dữ liệu JSON có tồn tại và có các trường cần thiết không
    if (!isset($data['email']) || !isset($data['otp'])) {
        echo json_encode(["error" => "Vui lòng cung cấp email và OTP."]);
        exit();
    }

    $email = trim($data['email']);
    $otp = trim($data['otp']);

    // Kiểm tra xem trường email và OTP có bị trống không
    if (empty($email) || empty($otp)) {
        echo json_encode(["error" => "Vui lòng nhập email và OTP."]);
        exit();
    }

    try {
        // Kiểm tra xem email có tồn tại không
        $sql = "SELECT id FROM users WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $userId = $user['id'];

            // Kiểm tra xem OTP có hợp lệ không
            $sql = "SELECT * FROM otp WHERE nguoi_dung_id = :user_id AND ma_otp = :otp AND thoi_gian_het_han > NOW() AND trang_thai = 'unused'";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':otp', $otp, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                // Cập nhật trạng thái OTP thành 'used'
                $sql = "UPDATE otp SET trang_thai = 'used' WHERE nguoi_dung_id = :user_id AND ma_otp = :otp";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
                $stmt->bindParam(':otp', $otp, PDO::PARAM_STR);
                $stmt->execute();

                echo json_encode(["success" => "OTP hợp lệ."]);
            } else {
                echo json_encode(["error" => "OTP không hợp lệ hoặc đã hết hạn."]);
            }
        } else {
            echo json_encode(["error" => "Email không tồn tại."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["error" => "Lỗi hệ thống, vui lòng thử lại sau."]);
        error_log("Lỗi xác thực OTP: " . $e->getMessage());
    }
}
?>