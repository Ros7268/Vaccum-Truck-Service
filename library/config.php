<?php
// เปิดการแสดงผลข้อผิดพลาดสำหรับ Debugging (ปิดใน Production)
ini_set('display_errors', 'on');
error_reporting(E_ALL);

// เริ่มต้นเซสชัน
session_start();

// การตั้งค่าการเชื่อมต่อฐานข้อมูล
$dbHost = 'localhost';        // ชื่อโฮสต์ของฐานข้อมูล
$dbUser = 'root';             // ชื่อผู้ใช้ฐานข้อมูล
$dbPass = '';                 // รหัสผ่านฐานข้อมูล
$dbName = 'db_event_management'; // ชื่อฐานข้อมูล

// ข้อมูลโปรเจกต์
$site_title = 'Event Management System';
$email_id = 'support@eventmanagement.com';

// กำหนดเส้นทางของไฟล์
$thisFile = str_replace('\\', '/', __FILE__);
$docRoot = $_SERVER['DOCUMENT_ROOT'];

$webRoot = str_replace(array($docRoot, 'library/config.php'), '', $thisFile);
$srvRoot = str_replace('library/config.php', '', $thisFile);

define('WEB_ROOT', $webRoot);
define('SRV_ROOT', $srvRoot);
define('DB_HOST', $dbHost);
define('DB_USER', $dbUser);
define('DB_PASSWORD', $dbPass);
define('DB_DATABASE', $dbName);

// การจัดการข้อมูลใน POST และ GET เพื่อความปลอดภัย
// การจัดการข้อมูลใน POST และ GET เพื่อความปลอดภัย
if (!empty($_POST)) {
    foreach ($_POST as $key => $value) {
        // ตรวจสอบว่า $value เป็น array หรือไม่ ถ้าเป็น array ให้ใช้ implode หรือข้ามไป
        if (is_array($value)) {
            // ถ้าเป็น array ให้ใช้ implode เพื่อแปลงเป็น string ก่อน
            $_POST[$key] = implode(', ', $value); // ปรับให้เหมาะสมกับการใช้งาน
        } else {
            $_POST[$key] = trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
        }
    }
}

if (!empty($_GET)) {
    foreach ($_GET as $key => $value) {
        // ตรวจสอบว่า $value เป็น array หรือไม่ ถ้าเป็น array ให้ใช้ implode หรือข้ามไป
        if (is_array($value)) {
            // ถ้าเป็น array ให้ใช้ implode เพื่อแปลงเป็น string ก่อน
            $_GET[$key] = implode(', ', $value); // ปรับให้เหมาะสมกับการใช้งาน
        } else {
            $_GET[$key] = trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
        }
    }
}


// การเชื่อมต่อฐานข้อมูลด้วย PDO
try {
    $db = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8", $dbUser, $dbPass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // เปิดใช้งานโหมดข้อผิดพลาดแบบ Exception
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// รวมไลบรารีเพิ่มเติม
require_once 'database.php';
require_once 'common.php';
?>
