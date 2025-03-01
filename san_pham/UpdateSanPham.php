<?php
session_start();
require_once '../database/DatabaseConnection.php';

// Cập nhật sản phẩm theo id
function updateSanPham($conn, $id, $ten_san_pham, $gia, $so_luong, $mo_ta, $hinh_anh, $danh_muc_id){
    $sql = "UPDATE san_pham SET ten_san_pham = :ten_san_pham, gia = :gia, so_luong = :so_luong, mo_ta = :mo_ta, hinh_anh = :hinh_anh, danh_muc_id = :danh_muc_id WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':ten_san_pham' => $ten_san_pham,
        ':gia' => $gia,
        ':so_luong' => $so_luong,
        ':mo_ta' => $mo_ta,
        ':hinh_anh' => $hinh_anh,
        ':danh_muc_id' => $danh_muc_id,
        ':id' => $id
    ]);
    return $stmt->rowCount(); // Trả về số hàng bị ảnh hưởng
}

// Kiểm tra phương thức yêu cầu
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Kiểm tra xem các trường dữ liệu có tồn tại không
    if (!isset($_POST['id']) || !isset($_POST['ten_san_pham']) || !isset($_POST['gia']) || !isset($_POST['so_luong']) || !isset($_POST['mo_ta']) || !isset($_POST['danh_muc_id'])) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng cung cấp đầy đủ thông tin.']);
        exit();
    }

    $id = trim($_POST['id']);
    $ten_san_pham = trim($_POST['ten_san_pham']);
    $gia = trim($_POST['gia']);
    $so_luong = trim($_POST['so_luong']);
    $mo_ta = trim($_POST['mo_ta']);
    $danh_muc_id = trim($_POST['danh_muc_id']);

    // Xử lý file upload
    $hinh_anh = handleFileUpload();

    // Gọi hàm cập nhật thông tin sản phẩm
    $result = updateSanPham($conn, $id, $ten_san_pham, $gia, $so_luong, $mo_ta, $hinh_anh, $danh_muc_id);

    // Kiểm tra kết quả và trả về phản hồi
    if ($result > 0) {
        echo json_encode(['success' => true, 'message' => 'Cập nhật thông tin sản phẩm thành công.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không có thay đổi nào được thực hiện.']);
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
?>