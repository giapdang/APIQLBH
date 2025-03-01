<?php
session_start();
require_once '../database/DatabaseConnection.php';
require_once '../mail/sendEmail.php';
date_default_timezone_set('Asia/Ho_Chi_Minh');

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Đọc dữ liệu JSON từ request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Kiểm tra xem dữ liệu JSON có tồn tại và có các trường cần thiết không
    if (!isset($data['email'])) {
        echo json_encode(["error" => "Vui lòng cung cấp email."]);
        exit();
    }

    $email = trim($data['email']);

    // Kiểm tra xem trường email có bị trống không
    if (empty($email)) {
        echo json_encode(["error" => "Vui lòng nhập email."]);
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

            // Generate OTP
            $otp = rand(100000, 999999);
            $expiryTime = date("Y-m-d H:i:s", strtotime('+22 minutes'));

            // Insert OTP into database
            $sql = "INSERT INTO otp (nguoi_dung_id, ma_otp, thoi_gian_het_han) VALUES (:user_id, :otp, :expiry_time)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':otp', $otp, PDO::PARAM_STR);
            $stmt->bindParam(':expiry_time', $expiryTime, PDO::PARAM_STR);
            $stmt->execute();

            // Send OTP to email
            sendOTPEmail($email, $otp);

            // Lưu email vào session
            $_SESSION['email'] = $email;
            echo json_encode(["success" => "OTP đã được gửi đến email của bạn."]);
        } else {
            echo json_encode(["error" => "Email không tồn tại."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["error" => "Lỗi hệ thống, vui lòng thử lại sau."]);
        error_log("Lỗi kiểm tra email: " . $e->getMessage());
    }
}
?>