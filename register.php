<?php
require_once './library/config.php';
require_once './library/functions.php';

$errorMessage = null;
$successMessage = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $name = trim($_POST['name']);
    $pwd = trim($_POST['pwd']);
    $confirmPwd = trim($_POST['confirm_pwd']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);

    if ($firstName && $lastName && $name && $pwd && $confirmPwd && $address && $phone && $email) {
        if (strlen($pwd) < 6 || strlen($pwd) > 50) {
            $errorMessage = 'Password must be between 6 and 50 characters.';
        } elseif ($pwd !== $confirmPwd) {
            $errorMessage = 'Passwords do not match.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorMessage = 'Invalid email format.';
        } elseif (!preg_match('/^\d{10}$/', $phone)) {
            $errorMessage = 'Phone number must be exactly 10 digits.';
        } else {
            try {
                $checkSql = "SELECT COUNT(*) FROM tbl_users WHERE email = ? OR name = ?";
                $checkStmt = $db->prepare($checkSql);
                $checkStmt->execute([$email, $name]);
                $exists = $checkStmt->fetchColumn();
            
                if ($exists) {
                    $errorMessage = 'Username or Email already registered.';
                } else {
                    $sql = "INSERT INTO tbl_users (first_name, last_name, name, pwd, address, phone, email, type, status) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, 'user', 'active')";
                    $stmt = $db->prepare($sql);
                    $result = $stmt->execute([$firstName, $lastName, $name, $pwd, $address, $phone, $email]);
            
                    if ($result) {
                        $successMessage = 'Registration successful! Redirecting to login...';
                        echo "<script>
                            setTimeout(function() {
                                window.location.href = 'login.php';
                            }, 2000);
                        </script>";
                    } else {
                        $errorMessage = 'Registration failed. Please try again.';
                    }
                }
            } catch (PDOException $e) {
                $errorMessage = 'Database error: ' . $e->getMessage();
            }
        }
    } else {
        $errorMessage = 'All fields are required.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .register-box {
            max-width: 500px;
            margin: 50px auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .register-logo a {
            font-size: 1.8rem;
            font-weight: bold;
            color: #007bff;
            text-decoration: none;
        }
        #password-length-status, #password-match-status {
            font-size: 0.9rem;
            margin-top: 5px;
        }
        #password-length-status {
            color: red;
        }
        #password-match-status {
            color: red;
        }
        #password-match-status.match {
            color: green;
        }
    </style>
</head>
<body>
<div class="register-box">
    <div class="register-logo text-center mb-4">
        <a href="#">Thanawat Service</a>
    </div>
    <h5 class="text-center mb-4">Register a New Account</h5>

    <?php if ($successMessage): ?>
        <div class="alert alert-success" id="success-alert">
            <?php echo $successMessage; ?>
        </div>
    <?php endif; ?>
    <?php if ($errorMessage): ?>
        <div class="alert alert-danger" id="error-alert">
            <?php echo $errorMessage; ?>
        </div>
    <?php endif; ?>

    <form action="" method="post" id="register-form">
        <div class="mb-3">
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" name="first_name" id="first_name" class="form-control" placeholder="Enter first name" required>
        </div>
        <div class="mb-3">
            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Enter last name" required>
        </div>
        <div class="mb-3">
            <label for="name" class="form-label">Username</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Enter username" required>
        </div>
        <div class="mb-3">
            <label for="pwd" class="form-label">Password</label>
            <input type="password" name="pwd" id="pwd" class="form-control" placeholder="Enter password" minlength="6" maxlength="50" required>
            <div id="password-length-status" style="display: none;">Password must be at least 6 characters.</div>
        </div>
        <div class="mb-3">
            <label for="confirm_pwd" class="form-label">Confirm Password</label>
            <input type="password" name="confirm_pwd" id="confirm_pwd" class="form-control" placeholder="Re-enter password" required>
            <div id="password-match-status" style="display: none;">Passwords do not match.</div>
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <input type="text" name="address" id="address" class="form-control" placeholder="Enter address" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" name="phone" id="phone" class="form-control" placeholder="Enter phone number" pattern="[0-9]{10}" maxlength="10" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="Enter email" required>
        </div>
        <button type="submit" class="btn btn-primary w-100" id="register-btn" disabled>Register</button>
        <div class="text-center mt-3">
            <a href="login.php" class="btn btn-link">Back to Login</a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const passwordField = document.getElementById('pwd');
    const confirmPasswordField = document.getElementById('confirm_pwd');
    const lengthStatus = document.getElementById('password-length-status');
    const matchStatus = document.getElementById('password-match-status');
    const registerButton = document.getElementById('register-btn');

    function validatePasswordLength() {
        const password = passwordField.value;
        if (password.length < 6 || password.length > 50) {
            lengthStatus.style.display = 'block';
            registerButton.disabled = true;
        } else {
            lengthStatus.style.display = 'none';
        }
    }

    function checkPasswordMatch() {
        const password = passwordField.value;
        const confirmPassword = confirmPasswordField.value;
        if (confirmPassword === '') {
            matchStatus.style.display = 'none';
            registerButton.disabled = true;
            return;
        }
        matchStatus.style.display = 'block';
        if (password === confirmPassword) {
            matchStatus.textContent = 'Passwords match.';
            matchStatus.classList.add('match');
            registerButton.disabled = false;
        } else {
            matchStatus.textContent = 'Passwords do not match.';
            matchStatus.classList.remove('match');
            registerButton.disabled = true;
        }
    }

    passwordField.addEventListener('input', () => {
        validatePasswordLength();
        checkPasswordMatch();
    });

    confirmPasswordField.addEventListener('input', checkPasswordMatch);
</script>
</body>
</html>
