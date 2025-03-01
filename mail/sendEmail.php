<?php
require_once '../PHPMailer-master/src/Exception.php';
require_once '../PHPMailer-master/src/PHPMailer.php';
require_once '../PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendOTPEmail($email, $otp_code) {
    $mail = new PHPMailer(true);
    try {
        // Cấu hình thông tin email
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'nguyenbathanh1322004@gmail.com'; // email của SMTP server
        $mail->Password = 'wqduijtnsdzdnqzj'; // password của SMTP server
        $mail->SMTPSecure = 'tls'; // Phương thức mã hóa kết nối
        $mail->Port = 587; // pỏt của SMTP server

        // Nội dung email
        $mail->setFrom('nguyenbathanh1322004@gmail.com'); // email người gửi
        $mail->addAddress($email);
        $mail->Subject = 'OTP Code đặt lại mật khẩu';
        $mail->isHTML(true); 
        $mail->Body = "Mã OTP của bạn là: $otp_code. Mã này sẽ hết hạn trong 2 phút.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

?>;