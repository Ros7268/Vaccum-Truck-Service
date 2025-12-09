<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once '../library/config.php';
require_once '../library/functions.php';
require_once '../library/mail.php';

$cmd = isset($_GET['cmd']) ? $_GET['cmd'] : '';

switch($cmd) {
    case 'create':
        createUser();
        break;

    case 'change':
        changeStatus();
        break;

    case 'addEmployee': // กรณีเพิ่มพนักงาน
        addEmployee();
        break;

    case 'changeEmployeeStatus': // กรณีเปลี่ยนสถานะพนักงาน
        changeEmployeeStatus();
        break;

    default:
        echo "Invalid command.";
        break;
}

// ฟังก์ชันเพิ่มผู้ใช้งาน
function createUser() {
    global $db;

    // รับค่าจากฟอร์ม
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $name = trim($_POST['name']);
    $pwd = trim($_POST['password']);
    $confirmPwd = trim($_POST['confirm_password']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $type = trim($_POST['type']);

    // ตรวจสอบว่ากรอกข้อมูลครบหรือไม่
    if (!$first_name || !$last_name || !$name || !$pwd || !$confirmPwd || !$address || !$phone || !$email || !$type) {
        header('Location: ../views/?v=CREATE&err=' . urlencode('All fields are required.'));
        exit();
    }

    // ตรวจสอบว่า Password และ Confirm Password ตรงกันหรือไม่
    if ($pwd !== $confirmPwd) {
        header('Location: ../views/?v=CREATE&err=' . urlencode('Passwords do not match.'));
        exit();
    }

    // ตรวจสอบอีเมลให้ถูกต้อง
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: ../views/?v=CREATE&err=' . urlencode('Invalid email format.'));
        exit();
    }

    // ตรวจสอบชื่อผู้ใช้ซ้ำ
    $checkSql = "SELECT user_id FROM tbl_users WHERE name = ?";
    $stmt = $db->prepare($checkSql);
    $stmt->execute([$name]);

    if ($stmt->rowCount() > 0) {
        header('Location: ../views/?v=CREATE&err=' . urlencode('User with the same username already exists.'));
        exit();
    }

    // บันทึกลงฐานข้อมูล
    $sql = "INSERT INTO tbl_users (first_name, last_name, name, pwd, address, phone, email, type, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $db->prepare($sql);
    $success = $stmt->execute([$first_name, $last_name, $name, $pwd, $address, $phone, $email, $type, 'active']);

    if ($success) {
        header('Location: ../views/?v=USERS&msg=' . urlencode('User successfully registered.'));
    } else {
        header('Location: ../views/?v=CREATE&err=' . urlencode('Failed to register user.'));
    }
    exit();
}


// ฟังก์ชันเปลี่ยนสถานะผู้ใช้งาน
function changeStatus() {
    $action = $_GET['action'];
    $userId = (int)$_GET['userId'];

    $sql = "UPDATE tbl_users SET status = '$action' WHERE user_id = $userId";	
    dbQuery($sql);

    header('Location: ../views/?v=USERS&msg=' . urlencode('User status successfully updated.'));
    exit();
}

// ฟังก์ชันเพิ่มพนักงาน
function addEmployee() {
    $employeeId = trim($_POST['employeeId']);
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $status = trim($_POST['status']);

    if (!$employeeId || !$name || !$phone || !$address || !$status) {
        header('Location: ../views/?v=EMPLOYEES&err=' . urlencode('All fields are required.'));
        exit();
    }

    $sql = "INSERT INTO tbl_employees (employee_id, name, phone, address, status) 
            VALUES ('$employeeId', '$name', '$phone', '$address', '$status')";
    
    if (dbQuery($sql)) {
        header('Location: ../views/?v=EMPLOYEES&msg=' . urlencode('Employee added successfully.'));
    } else {
        header('Location: ../views/?v=EMPLOYEES&err=' . urlencode('Failed to add employee.'));
    }
    exit();
}

// ฟังก์ชันเปลี่ยนสถานะพนักงาน
function changeEmployeeStatus() {
    $action = $_GET['action'];
    $employeeId = $_GET['employeeId'];

    $sql = "UPDATE tbl_employees SET status = '$action' WHERE employee_id = '$employeeId'";
    if (dbQuery($sql)) {
        header('Location: ../views/?v=EMPLOYEES&msg=' . urlencode('Employee status successfully updated.'));
    } else {
    }
    exit();
}

?>
