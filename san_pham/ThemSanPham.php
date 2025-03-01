<?php
require_once '../database/DatabaseConnection.php';

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Kiểm tra xem các trường dữ liệu có tồn tại không
    if (!isset($_POST['danh_muc_id']) || !isset($_POST['ten_san_pham']) || !isset($_POST['gia']) || !isset($_POST['so_luong'])) {
        echo json_encode(["error" => "Vui lòng cung cấp đầy đủ thông tin."]);
        exit();
    }

    $danhMucId = trim($_POST['danh_muc_id']);
    $tenSanPham = trim($_POST['ten_san_pham']);
    $moTa = trim($_POST['mo_ta'] ?? '');
    $gia = trim($_POST['gia']);
    $soLuong = trim($_POST['so_luong']);

    // Kiểm tra xem các trường dữ liệu có bị trống không
    if (empty($danhMucId) || empty($tenSanPham) || empty($gia) || empty($soLuong)) {
        echo json_encode(["error" => "Vui lòng điền đầy đủ thông tin."]);
        exit();
    }

    // Xử lý file upload
    $hinhAnh = handleFileUpload();

    // Thêm sản phẩm vào cơ sở dữ liệu
    if (addSanPham($conn, $danhMucId, $tenSanPham, $moTa, $gia, $soLuong, $hinhAnh)) {
        echo json_encode(["success" => "Thêm sản phẩm thành công!"]);
    } else {
        echo json_encode(["error" => "Lỗi hệ thống, vui lòng thử lại sau."]);
    }
}

// Hàm xử lý file upload
function handleFileUpload() {
    if (isset($_FILES['hinh_anh']) && $_FILES['hinh_anh']['error'] == 0) {
        $targetDir = "../uploads/";
        
        // Kiểm tra và tạo thư mục nếu chưa tồn tại
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = basename($_FILES['hinh_anh']['name']);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        // Kiểm tra định dạng file
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array(strtolower($fileType), $allowedTypes)) {
            echo json_encode(["error" => "Chỉ chấp nhận các định dạng JPG, JPEG, PNG, GIF."]);
            exit();
        }

        // Upload file
        if (!move_uploaded_file($_FILES['hinh_anh']['tmp_name'], $targetFilePath)) {
            echo json_encode(["error" => "Đã xảy ra lỗi khi upload file."]);
            exit();
        }

        return $targetFilePath;
    }

    return null;
}

// Hàm thêm sản phẩm vào cơ sở dữ liệu
function addSanPham($conn, $danhMucId, $tenSanPham, $moTa, $gia, $soLuong, $hinhAnh) {
    try {
        // Chuẩn bị câu lệnh SQL
        $sql = "INSERT INTO san_pham (danh_muc_id, ten_san_pham, mo_ta, gia, so_luong, hinh_anh) VALUES (:danh_muc_id, :ten_san_pham, :mo_ta, :gia, :so_luong, :hinh_anh)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':danh_muc_id', $danhMucId, PDO::PARAM_INT);
        $stmt->bindParam(':ten_san_pham', $tenSanPham, PDO::PARAM_STR);
        $stmt->bindParam(':mo_ta', $moTa, PDO::PARAM_STR);
        $stmt->bindParam(':gia', $gia, PDO::PARAM_STR);
        $stmt->bindParam(':so_luong', $soLuong, PDO::PARAM_INT);
        $stmt->bindParam(':hinh_anh', $hinhAnh, PDO::PARAM_STR);

        // Thực thi câu lệnh SQL
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Lỗi thêm sản phẩm: " . $e->getMessage());
        return false;
    }
}
?>