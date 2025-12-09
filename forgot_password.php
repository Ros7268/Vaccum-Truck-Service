<?php
require_once './library/config.php';
require './vendor/autoload.php'; // ใช้ PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$errorMessage = null;
$successMessage = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = 'Invalid email format.';
    } else {
        // ตรวจสอบว่าอีเมลมีอยู่ในระบบ
        $sql = "SELECT user_id FROM tbl_users WHERE email = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // ตั้งค่า timezone และสร้าง Token + เวลาหมดอายุ
            date_default_timezone_set('Asia/Bangkok'); 
            $token = bin2hex(random_bytes(32));
            $expire_time = date('Y-m-d H:i:s', strtotime('+5 minutes')); // Token มีอายุ 5 นาที

            // ลบ Token เก่าออกก่อน
            $stmt = $db->prepare("DELETE FROM tbl_password_resets WHERE email = ?");
            $stmt->execute([$email]);

            // บันทึก Token ลงฐานข้อมูล
            $sql = "INSERT INTO tbl_password_resets (email, token, expire_at) VALUES (?, ?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->execute([$email, $token, $expire_time]);

            // สร้างลิงก์รีเซ็ตรหัสผ่าน
            $reset_link = "http://localhost/event-management/reset_password.php?token=$token";

            // ตั้งค่า PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'warakorn.chsm@gmail.com'; // Email ที่ใช้ส่ง
                $mail->Password = 'eiip hlzv crnp bzoa'; // ใช้ App Password ของ Gmail
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('warakorn.chsm@gmail.com', 'Thanawat Service');
                $mail->addAddress($email);
                $mail->Subject = 'Password Reset Request';
                $mail->Body = "Click this link to reset your password: $reset_link";

                $mail->send();
                $successMessage = 'Reset link has been sent to your email.';
            } catch (Exception $e) {
                $errorMessage = 'Mail error: ' . $mail->ErrorInfo;
            }
        } else {
            $errorMessage = 'Email not found.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .card {
            max-width: 400px;
            width: 100%;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            background: #fff;
        }
        .btn-primary {
            background: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<div class="card">
    <h3 class="text-center text-primary">Forgot Password</h3>
    <p class="text-center text-muted">Enter your email to reset your password</p>

    <?php if ($errorMessage): ?>
        <div class="alert alert-danger" role="alert"><?php echo $errorMessage; ?></div>
    <?php endif; ?>
    <?php if ($successMessage): ?>
        <div class="alert alert-success" role="alert"><?php echo $successMessage; ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
        <div class="text-center mt-3">
            <a href="login.php" class="text-decoration-none">Back to Login</a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
