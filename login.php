<?php
require_once './library/config.php';
require_once './library/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errorMessage = '&nbsp;';

// ตรวจสอบว่ามีการส่งข้อมูลล็อกอินหรือไม่
if (isset($_POST['name']) && isset($_POST['pwd'])) {
    $result = doLogin();
    if ($result != '') {
        $errorMessage = $result;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-box {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 400px;
            width: 100%;
        }
        .login-logo {
            text-align: center;
            font-size: 1.8rem;
            font-weight: 700;
            color: #007bff;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0px 0px 5px rgba(0, 123, 255, 0.5);
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            transition: background-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .alert {
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="login-logo mb-4">Thanawat Service</div>
        <h5 class="text-center mb-4">Sign in</h5>

        <!-- แสดงข้อความแจ้งเตือนหากมีข้อผิดพลาด -->
        <?php if ($errorMessage != "&nbsp;") { ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Alert!</strong> <?php echo $errorMessage; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php } ?>

        <form action="" method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Username</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Enter your username" required>
            </div>
            <div class="mb-3">
                <label for="pwd" class="form-label">Password</label>
                <input type="password" name="pwd" id="pwd" class="form-control" placeholder="Enter your password" required>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <a href="register.php" class="text-decoration-none">Register</a>
                <a href="forgot_password.php" class="text-decoration-none">Forgot Password?</a>
                <button type="submit" class="btn btn-primary">Sign In</button>
            </div>

        </form>
    </div>
    

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
