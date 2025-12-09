<?php
require_once './library/config.php';

$errorMessage = null;
$successMessage = null;
$token = $_GET['token'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if (strlen($newPassword) < 6) {
        $errorMessage = 'Password must be at least 6 characters.';
    } elseif ($newPassword !== $confirmPassword) {
        $errorMessage = 'Passwords do not match.';
    } else {
        // ตรวจสอบ Token
        $sql = "SELECT email FROM tbl_password_resets WHERE token = ? AND expire_at > NOW()";
        $stmt = $db->prepare($sql);
        $stmt->execute([$token]);
        $reset = $stmt->fetch();

        if ($reset) {
            $email = $reset['email'];

            // อัปเดตรหัสผ่านใหม่
            $sql = "UPDATE tbl_users SET pwd = ? WHERE email = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$newPassword, $email]);

            // ลบ Token ออก
            $db->prepare("DELETE FROM tbl_password_resets WHERE email = ?")->execute([$email]);

            $successMessage = 'Password has been reset. You can now log in.';
        } else {
            $errorMessage = 'Invalid or expired token.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
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
        .password-toggle {
            cursor: pointer;
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
        }
    </style>
</head>
<body>

<div class="card">
    <h3 class="text-center text-primary">Reset Password</h3>
    <p class="text-center text-muted">Enter a new password to reset your account</p>

    <?php if ($errorMessage): ?>
        <div class="alert alert-danger" role="alert"><?php echo $errorMessage; ?></div>
    <?php endif; ?>
    <?php if ($successMessage): ?>
        <div class="alert alert-success" role="alert"><?php echo $successMessage; ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        
        <div class="mb-3 position-relative">
            <label for="new_password" class="form-label">New Password</label>
            <input type="password" name="new_password" id="new_password" class="form-control" placeholder="Enter new password" required>
            <span class="password-toggle" onclick="togglePassword('new_password')"></span>
        </div>

        <div class="mb-3 position-relative">
            <label for="confirm_password" class="form-label">Confirm Password</label>
            <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm new password" required>
            <span class="password-toggle" onclick="togglePassword('confirm_password')"></span>
        </div>

        <button type="submit" class="btn btn-primary w-100">Reset Password</button>
        <div class="text-center mt-3">
            <a href="login.php" class="text-decoration-none">Back to Login</a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function togglePassword(fieldId) {
        var field = document.getElementById(fieldId);
        field.type = field.type === "password" ? "text" : "password";
    }
</script>
</body>
</html>
