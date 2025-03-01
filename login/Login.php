<?php
session_start();
require_once '../database/DatabaseConnection.php';

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Đọc dữ liệu JSON từ request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Kiểm tra xem dữ liệu JSON có tồn tại và có các trường cần thiết không
    if (!isset($data['email']) || !isset($data['mat_khau'])) {
        echo json_encode(["error" => "Vui lòng cung cấp đầy đủ thông tin."]);
        exit();
    }

    $email = trim($data['email']);
    $password = trim($data['mat_khau']);

    // Kiểm tra xem các trường dữ liệu có bị trống không
    if (empty($email) || empty($password)) {
        echo json_encode(["error" => "Vui lòng nhập email và mật khẩu."]);
        exit();
    }

    try {
        // Kiểm tra xem email có tồn tại không
        $sql = "SELECT id, mat_khau FROM users WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Kiểm tra xem mật khẩu có được hash không
            if (password_verify($password, $user['mat_khau'])) {
                $_SESSION['user_id'] = $user['id']; // Lưu user_id vào session
                echo json_encode(["success" => "Đăng nhập thành công!", "user_id" => $user['id']]);
            } else {
                echo json_encode(["error" => "Mật khẩu không chính xác."]);
            }
        } else {
            echo json_encode(["error" => "Email không tồn tại."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["error" => "Lỗi hệ thống, vui lòng thử lại sau."]);
        error_log("Lỗi đăng nhập: " . $e->getMessage());
    }
}
?>
