<?php
session_start();
require_once '../database/DatabaseConnection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Đọc dữ liệu JSON từ request body
    $data = json_decode(file_get_contents("php://input"), true);



    // Kiểm tra xem dữ liệu JSON có tồn tại và có các trường cần thiết không
    if (isset($data['ho_ten']) && isset($data['mat_khau']) && isset($data['xac_nhan_mat_khau']) && isset($data['email'])) {
        $username = trim($data['ho_ten']);
        $password = trim($data['mat_khau']);
        $comfirmPassword = trim($data['xac_nhan_mat_khau']);
        $email = trim($data['email']);
        $phone = trim($data['so_dien_thoai'] ?? '');
        $address = trim($data['dia_chi'] ?? '');

        // kiem tra xem cac truong du lieu co bi trong hay khong
        if (empty($username) || empty($password) || empty($email)) {
            echo json_encode(["error" => "Vui lòng điền đầy đủ thông tin."]);
            exit();
        }

        // kiem tra xem email da ton tai hay chua
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bindValue(1, $email, PDO::PARAM_STR);

            // Execute the statement
            if ($stmt->execute()) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    echo json_encode(["error" => "Email đã tồn tại."]);
                    error_log("Error: Email đã tồn tại.");
                    exit();
                }
            } else {
                echo json_encode(["error" => "Đã xảy ra lỗi khi kiểm tra email."]);
                error_log("Error: Đã xảy ra lỗi khi kiểm tra email.");
                exit();
            }

            $stmt->closeCursor();
        } else {
            echo json_encode(["error" => "Đã xảy ra lỗi khi chuẩn bị truy vấn."]);
            error_log("Error: Đã xảy ra lỗi khi chuẩn bị truy vấn.");
            exit();
        }

        // kiem tra xem mat khau va xac nhan mat khau co trung khop hay khong
        if ($password != $comfirmPassword) {
            echo json_encode(["error" => "Mật khẩu và xác nhận mật khẩu không trùng khớp."]);
            error_log("Error: Mật khẩu và xác nhận mật khẩu không trùng khớp.");
            exit();
        }

        // hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // chuan bi truy van
        $sql = "INSERT INTO users (ho_ten, mat_khau, email, so_dien_thoai, dia_chi) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bindValue(1, $username, PDO::PARAM_STR);
            $stmt->bindValue(2, $hashedPassword, PDO::PARAM_STR);
            $stmt->bindValue(3, $email, PDO::PARAM_STR);
            $stmt->bindValue(4, $phone, PDO::PARAM_STR);
            $stmt->bindValue(5, $address, PDO::PARAM_STR);

            // Execute the statement
            if ($stmt->execute()) {
                echo json_encode(["success" => "Đăng ký tài khoản thành công."]);
                error_log("Success: Đăng ký tài khoản thành công.");
            } else {
                echo json_encode(["error" => "Đã xảy ra lỗi khi đăng ký tài khoản."]);
                error_log("Error: Đã xảy ra lỗi khi đăng ký tài khoản. " . implode(" ", $stmt->errorInfo()));
            }
            $stmt = null;
        } else {
            echo json_encode(["error" => "Đã xảy ra lỗi khi chuẩn bị truy vấn."]);
            error_log("Error: Đã xảy ra lỗi khi chuẩn bị truy vấn.");
        }
    } else {
        echo json_encode(["error" => "Vui lòng cung cấp đầy đủ thông tin."]);
    }
    exit();
}
?>