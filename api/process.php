<?php 
// ต้นไฟล์ process.php (เพิ่มไว้ด้านบนสุด)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// เพิ่ม global error handler
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => "PHP Error: [$errno] $errstr in $errfile on line $errline"
    ]);
    exit();
});

require_once 'Booking.php'; // ไฟล์ที่เก็บคลาส Booking
require_once '../library/config.php'; // ไฟล์การตั้งค่าการเชื่อมต่อฐานข้อมูล
require_once '../library/mail.php'; // ไฟล์สำหรับการส่งอีเมล

// รับค่าคำสั่งจาก URL
$cmd = isset($_GET['cmd']) ? $_GET['cmd'] : '';

// ตรวจสอบคำสั่งและเรียกใช้ฟังก์ชันที่เกี่ยวข้อง
switch($cmd) {
	
	// ฟังก์ชันจอง Event
	case 'book':
		bookCalendar();
	break;

	// ฟังก์ชันดูข้อมูลปฏิทิน
	case 'calview':
		calendarView();
	break;

	// ฟังก์ชันยืนยันการจอง
	case 'regConfirm':
		regConfirm();
	break;

	// ฟังก์ชันลบข้อมูลการจอง
	case 'delete':
		regDelete();
	break;

	// ฟังก์ชันดึงข้อมูลผู้ใช้
	case 'user':
		userDetails();
	break;

	// ฟังก์ชันเพิ่มพนักงาน
	case 'addEmployee':
        addEmployee();
        break;

	// ฟังก์ชันลบพนักงาน
    case 'deleteEmployee':
        deleteEmployee();
        break;

	// ฟังก์ชันอัปเดตสถานะของพนักงาน
    case 'updateEmployeeStatus':
        updateEmployeeStatus();
        break;

    case 'updateEmployee':
        updateEmployee();
        break;    

	// กรณีไม่มีคำสั่งที่รองรับ
	default:
	break;

	case 'addTruck':
		addTruck();
		break;
	
	case 'deleteTruck':
		deleteTruck();
		break;
	
	case 'updateTruckStatus':
		updateTruckStatus();
		break;
     
    case 'updateRevenue':
         updateRevenue();
        break;

    
    case 'deleteRevenue':
        deleteRevenue();
        break;   

    case 'addRevenue':
        addRevenue();
        break;
     
    case 'getTotalRevenue':
        getTotalRevenue();

    case 'getMonthlyRevenueJSON':
        echo json_encode(getMonthlyRevenueRecords());
        exit;

    case 'outsourceBooking':
        outsourceBooking();
        break;

    case 'assignTask':
        assignTask();
        exit();
            
    case 'getAssignedRecords':
        echo json_encode(getAssignedRecords());
        exit();
            
    case 'getAssignedTaskDetail':
        getAssignedTaskDetail();
        break;
            
    case 'deleteAssignedTask':
        deleteAssignedTask();
        break;

    case 'updateBooking':
        updateBooking();
        break;
            
    case 'change':
        if ($_GET['action'] === 'delete') {
        deleteUser();
        } else {
        }
        break;
        
    case 'deleteRevenueByBooking':
        deleteRevenueByBooking();
        break;


}

function deleteRevenueByBooking() {
    $bookingId = isset($_GET['bookingId']) ? $_GET['bookingId'] : 0;
    
    if($bookingId) {
        $sql = "DELETE FROM tbl_revenue_updates WHERE reservation_id = '$bookingId'";
        $result = dbQuery($sql);
        
        if($result) {
            $response = array('success' => true);
        } else {
            $response = array('success' => false, 'message' => 'เกิดข้อผิดพลาดในการลบข้อมูลรายได้');
        }
    } else {
        $response = array('success' => false, 'message' => 'ไม่พบ Booking ID');
    }
    
    echo json_encode($response);
    exit;
}

function deleteUser() {
    global $db;

    $userId = isset($_GET['userId']) ? intval($_GET['userId']) : 0;
    if ($userId <= 0) {
        header('Location: ../views/?v=USERS&err=' . urlencode('Invalid user ID'));
        exit();
    }

    try {
        $stmt = $db->prepare("DELETE FROM tbl_users WHERE user_id = ?");
        $stmt->execute([$userId]);

        header('Location: ../views/?v=USERS&msg=' . urlencode('User deleted successfully.'));
    } catch (Exception $e) {
        header('Location: ../views/?v=USERS&err=' . urlencode('Error: ' . $e->getMessage()));
    }
    exit();
}

function updateBooking() {
    global $db;

    $resId = $_POST['resId'];
    $rdate = $_POST['rdate'];
    $rtime = $_POST['rtime'];
    $ucount = $_POST['ucount'];

    // รวมวันที่และเวลา
    $startTime = "$rdate $rtime";
    $endTime = date("Y-m-d H:i", strtotime($startTime) + 90 * 60);

    // UPDATE แค่ที่ tbl_reservations
    $sql = "UPDATE tbl_reservations SET rdate = ?, end_time = ?, ucount = ? WHERE reservation_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$startTime, $endTime, $ucount, $resId]);

    header('Location: ../views/?v=LIST&msg=' . urlencode('Booking updated successfully.'));
    exit();
}


// ฟังก์ชัน assignTask
function assignTask() {
    global $db;
    header('Content-Type: application/json');

    $reservationId = $_POST['reservationId'] ?? '';
    $employeeIds = $_POST['employeeIds'] ?? [];
    $truckId = $_POST['truckId'] ?? '';
    $driverId = $_POST['driverId'] ?? '';
    $assignedBy = $_POST['assigned_by'] ?? '';

        // เช็คเฉพาะว่ามีการส่งข้อมูลที่จำเป็นครบถ้วนหรือไม่ ไม่เช็คประเภทผู้ใช้
        if (empty($reservationId) || empty($employeeIds) || empty($truckId) || empty($driverId)) {
            echo json_encode(['success' => false, 'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน']);
            exit();
        }

        // อนุญาตให้ assigned_by เป็นค่าว่างได้ และเติมข้อมูลผู้ใช้ปัจจุบันใส่แทน
        if (empty($assignedBy)) {
            $assignedBy = $_SESSION['calendar_fd_user']['name'];
        }

        if (!is_array($employeeIds)) {
            $employeeIds = explode(',', $employeeIds);
        }

    try {
        $db->beginTransaction();

        $sqlDelete = "DELETE FROM tbl_assigned_tasks WHERE reservation_id = ?";
        $stmtDelete = $db->prepare($sqlDelete);
        $stmtDelete->execute([$reservationId]);

        foreach ($employeeIds as $employeeId) {
            $sql = "INSERT INTO tbl_assigned_tasks (reservation_id, employee_id, truck_id, driver_id, assigned_by, created_at) 
                    VALUES (?, ?, ?, ?, ?, NOW())";
            $stmt = $db->prepare($sql);
            $stmt->execute([$reservationId, $employeeId, $truckId, $driverId, $assignedBy]);
        }

        $db->commit();

        echo json_encode(['success' => true, 'message' => 'The assignment has been successfully assigned.']);
    } catch (Exception $e) {
        $db->rollBack();
        echo json_encode(['success' => false, 'message' => 'ERROR: ' . $e->getMessage()]);
    }
    exit();
}



// ฟังก์ชัน getAssignedRecords สำหรับดึงข้อมูลมาแสดงในตาราง
function getAssignedRecords() {
    global $db;

    $per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

    $start = ($current_page - 1) * $per_page;

    // ไม่มีการจำกัดข้อมูลตามประเภทผู้ใช้
    $sql = "
        SELECT 
            r.reservation_id AS res_id,
            r.rdate AS res_date,
            r.address,
            u.phone AS user_phone,
            u.name AS user_name,
            d.name AS driver_name,            
            at.assigned_by,                       
            GROUP_CONCAT(e.name SEPARATOR ', ') AS employee_names,
            t.plate_number AS truck_plate_number
        FROM tbl_assigned_tasks at
        INNER JOIN tbl_reservations r ON at.reservation_id = r.reservation_id
        LEFT JOIN tbl_users u ON r.user_id = u.user_id
        LEFT JOIN tbl_employees d ON at.driver_id = d.employee_id  
        LEFT JOIN tbl_employees e ON at.employee_id = e.employee_id
        LEFT JOIN tbl_trucks t ON at.truck_id = t.truck_id
        GROUP BY r.reservation_id
        ORDER BY r.rdate DESC
        LIMIT $start, $per_page
    ";

    $result = dbQuery($sql);
    $records = [];
    while ($row = dbFetchAssoc($result)) {
        $records[] = $row;
    }

    $count_sql = "SELECT COUNT(DISTINCT reservation_id) AS total FROM tbl_assigned_tasks";
    $count_result = dbQuery($count_sql);
    $total_records = dbFetchAssoc($count_result)['total'];
    $total_pages = $total_records > 0 ? ceil($total_records / $per_page) : 1;

    return [
        'data' => $records,
        'total_records' => $total_records,
        'per_page' => $per_page,
        'current_page' => $current_page,
        'total_pages' => $total_pages
    ];
}


// เพิ่มฟังก์ชัน deleteAssignedTask
function deleteAssignedTask() {
    global $db;
    header('Content-Type: application/json');
    
    $reservationId = $_GET['resId'] ?? '';
    
    if (empty($reservationId)) {
        echo json_encode(['success' => false, 'message' => 'ไม่พบรหัสการจอง']);
        exit();
    }
    
    try {
        // เริ่ม Transaction
        $db->beginTransaction();
        
        // ลบข้อมูลการมอบหมายงาน
        $sql = "DELETE FROM tbl_assigned_tasks WHERE reservation_id = ?";
        $stmt = $db->prepare($sql);
        $result = $stmt->execute([$reservationId]);
        
        // ตรวจสอบว่าลบสำเร็จหรือไม่
        if (!$result) {
            throw new Exception("การลบข้อมูลล้มเหลว");
        }
        
        // อัปเดตสถานะการจองเป็น APPROVED แทนที่จะเป็น ASSIGNED (ถ้ามีฟิลด์นี้)
        // หากในระบบของคุณมีฟิลด์สถานะที่แสดงว่าการจองถูก assign แล้วหรือไม่
        $updateSql = "UPDATE tbl_reservations SET status = 'APPROVED' WHERE reservation_id = ? AND status = 'ASSIGNED'";
        $updateStmt = $db->prepare($updateSql);
        $updateStmt->execute([$reservationId]);
        
        // Commit Transaction
        $db->commit();
        
        echo json_encode(['success' => true, 'message' => 'The assignment has been successfully deleted.']);
    } catch (Exception $e) {
        // Rollback ในกรณีที่เกิดข้อผิดพลาด
        $db->rollBack();
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
    }
    exit();
}


// ฟังก์ชันสำหรับดึงข้อมูล assigned task เมื่อต้องการแก้ไข
function getAssignedTaskDetail() {
    global $db;
    
    // ส่ง header เป็น JSON
    header('Content-Type: application/json');
    
    // รับค่า resId จาก GET
    $reservationId = isset($_GET['resId']) ? trim($_GET['resId']) : '';
    
    if (empty($reservationId)) {
        echo json_encode(['success' => false, 'message' => 'ไม่พบรหัสการจอง']);
        return;
    }

    // ใช้ Try-Catch เพื่อดักจับข้อผิดพลาด
    try {
        // ดึงข้อมูล truck จากการ assignment
        $sql1 = "SELECT DISTINCT truck_id FROM tbl_assigned_tasks 
                WHERE reservation_id = '$reservationId' LIMIT 1";
        $result1 = dbQuery($sql1);
        
        if (dbNumRows($result1) == 0) {
            echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลการมอบหมายงาน']);
            return;
        }
        
        $row1 = dbFetchAssoc($result1);
        $truckId = $row1['truck_id'];
        
        // ดึงข้อมูลพนักงานทั้งหมดที่ได้รับมอบหมายงานนี้
        $sql2 = "SELECT at.employee_id, e.name, e.last_name 
                FROM tbl_assigned_tasks at
                JOIN tbl_employees e ON at.employee_id = e.employee_id
                WHERE at.reservation_id = '$reservationId'";
        $result2 = dbQuery($sql2);
        
        $employees = [];
        while ($row2 = dbFetchAssoc($result2)) {
            $employees[] = $row2;
        }
        
        // ส่งผลลัพธ์กลับเป็น JSON
        $response = [
            'success' => true,
            'data' => [
                'reservation_id' => $reservationId,
                'truck_id' => $truckId,
                'employees' => $employees
            ]
        ];
        
        echo json_encode($response);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
    }
    
    // ไม่ต้องใช้ exit() หรือใส่ไว้ตรงนี้
    exit();
}

function outsourceBooking() {
    global $db;

    $bookingId = $_POST['bookingId'];
    $price = floatval($_POST['price']);
    $attachment = null;

    // ตรวจสอบการอัปโหลดไฟล์
    if (!empty($_FILES['attachment']['name'])) {
        $uploadDir = '../uploads/outsourcing/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileName = time() . '_' . basename($_FILES['attachment']['name']);
        $targetFilePath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetFilePath)) {
            $attachment = $fileName;
        } else {
            echo json_encode(["success" => false, "message" => "File upload failed"]);
            exit();
        }
    }

    // บันทึกข้อมูลลงตาราง tbl_outsourced_bookings
    $sql = "INSERT INTO tbl_outsourced_bookings (reservation_id, price, attachment) VALUES (?, ?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->execute([$bookingId, $price, $attachment]);

    // อัปเดตสถานะของการจองเป็น 'outsource'
    $sqlUpdate = "UPDATE tbl_reservations SET status = 'OUTSOURCE' WHERE reservation_id = ?";
    $stmtUpdate = $db->prepare($sqlUpdate);
    $stmtUpdate->execute([$bookingId]);

    echo json_encode(["success" => true, "message" => "Booking outsourced successfully"]);
    exit();
}


// ฟังก์ชันจอง Event
function bookCalendar() {
    global $db;

    date_default_timezone_set('Asia/Bangkok');

    $userId = (int)$_POST['userId'];
    $address = htmlspecialchars($_POST['address'], ENT_QUOTES, 'UTF-8');
    $phone = htmlspecialchars($_POST['phone'], ENT_QUOTES, 'UTF-8');
    $rdate = $_POST['rdate'];
    $rtime = $_POST['rtime'];
    $startTime = "$rdate $rtime";
    $endTime = date("Y-m-d H:i", strtotime($startTime) + 90 * 60);
    $serviceType = $_POST['service_type'] ?? '';
    $ucount = strval($_POST['ucount']);

    $now = time();
    $bookingTimestamp = strtotime($startTime);

    if ($bookingTimestamp < $now + 3600) {
        header("Location: ../index.php?err=" . urlencode("Booking must be made at least 1 hour ahead of current time."));
        exit();
    }

    $sqlCheckDaily = "SELECT COUNT(*) FROM tbl_reservations WHERE DATE(rdate) = ?";
    $stmtDaily = $db->prepare($sqlCheckDaily);
    $stmtDaily->execute([$rdate]);
    $totalDailyBookings = $stmtDaily->fetchColumn();

    if ($totalDailyBookings >= 6) {
        header("Location: ../index.php?err=" . urlencode("Daily limit reached (Max 6 bookings)"));
        exit();
    }

    $sqlCheckSlot = "SELECT COUNT(*) FROM tbl_reservations WHERE DATE(rdate) = ? AND HOUR(rdate) = HOUR(?) AND MINUTE(rdate) = MINUTE(?)";
    $stmtSlot = $db->prepare($sqlCheckSlot);
    $stmtSlot->execute([$rdate, $startTime, $startTime]);
    $totalSlotBookings = $stmtSlot->fetchColumn();

    if ($totalSlotBookings >= 3) {
        header("Location: ../index.php?err=" . urlencode("This time slot is full (Max 3 bookings)"));
        exit();
    }

    $sql = "INSERT INTO tbl_reservations (user_id, ucount, rdate, end_time, status, address, service_type, bdate) 
        VALUES (?, ?, ?, ?, 'PENDING', ?, ?, NOW())";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(1, $userId, PDO::PARAM_INT);
    $stmt->bindValue(2, $ucount, PDO::PARAM_STR);
    $stmt->bindValue(3, $startTime, PDO::PARAM_STR);
    $stmt->bindValue(4, $endTime, PDO::PARAM_STR);
    $stmt->bindValue(5, $address, PDO::PARAM_STR);
    $stmt->bindValue(6, $serviceType, PDO::PARAM_STR);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        header("Location: ../index.php?msg=" . urlencode("Booking successful"));
    } else {
        header("Location: ../index.php?err=" . urlencode("Failed to add booking"));
    }
    exit();
}


// ฟังก์ชันยืนยันการจอง
function regConfirm() {
    $resId = $_GET['resId']; 
    $action = $_GET['action'];

    if ($action == 'approve') {
        $stat = 'APPROVED';
    } elseif ($action == 'success') {
        $stat = 'SUCCESS';
    } else {
        $stat = 'DENIED';
    }

    $sql = "UPDATE tbl_reservations SET status = '$stat' WHERE reservation_id = $resId";
    dbQuery($sql);

    header('Location: ../views/?v=DB&msg=' . urlencode('Reservation status successfully changed.'));
    exit();
}

// ฟังก์ชันลบข้อมูลการจอง
function regDelete() {
    // ป้องกัน SQL Injection
    $resId = intval($_GET['resId']);
    
    $sql = "DELETE FROM tbl_revenue_updates WHERE reservation_id = '$resId'";
    dbQuery($sql);

    // ลบข้อมูลจากตารางลูกก่อน
    $sqlDeleteTasks = "DELETE FROM tbl_assigned_tasks WHERE reservation_id = $resId"; 
    dbQuery($sqlDeleteTasks);

    // จากนั้นลบจากตารางแม่
    $sqlDeleteReservation = "DELETE FROM tbl_reservations WHERE reservation_id = $resId"; 
    dbQuery($sqlDeleteReservation);

    // redirect พร้อมข้อความ
    header('Location: ../views/?v=LIST&msg=' . urlencode('Reservation record successfully deleted.'));
    exit();
}


// ฟังก์ชันดูข้อมูลปฏิทิน
function calendarView() {
    global $db;

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $start = $_POST['start'];
    $end = $_POST['end'];

    $userType = $_SESSION['calendar_fd_user']['type'] ?? '';
    $username = $_SESSION['calendar_fd_user']['name'] ?? '';

    $extraWhere = '';

    // ถ้าเป็น driver ให้แสดงเฉพาะงานของตัวเอง
    if ($userType === 'driver') {
        $stmt = $db->prepare("SELECT employee_id FROM tbl_employees WHERE username = ?");
        $stmt->execute([$username]);
        $employeeId = $stmt->fetchColumn();
    
        if ($employeeId) {
            $extraWhere = "AND r.reservation_id IN (
                SELECT reservation_id 
                FROM tbl_assigned_tasks 
                WHERE driver_id = '$employeeId' OR employee_id = '$employeeId'
            )";
        }
    }
    

    $sql = "SELECT u.name AS u_name, u.user_id AS user_id, r.rdate, r.status 
            FROM tbl_users u, tbl_reservations r 
            WHERE u.user_id = r.user_id 
            AND r.rdate BETWEEN ? AND ?
            $extraWhere";

    $stmt = $db->prepare($sql);
    $stmt->execute([$start, $end]);

    $bookings = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $book = new Booking();
        $book->title = $u_name;
        $book->start = $rdate;

        $book->backgroundColor = match($status) {
            'APPROVED' => '#00cc00',
            'SUCCESS' => '#007bff',
            'DENIED' => '#ff0000',
            'OUTSOURCE' => '#AA38A6',
            default => '#f39c12'
        };

        $book->url = WEB_ROOT . 'views/?v=USER&ID=' . $user_id;

        
        $bookings[] = $book;
    }

    echo json_encode($bookings);
    exit();
}


// ฟังก์ชันดึงข้อมูลผู้ใช้
function userDetails() {
	$userId = $_GET['userId']; // รหัสผู้ใช้
	$hsql = "SELECT * FROM tbl_users WHERE id = $userId"; // ดึงข้อมูลจากฐานข้อมูล
	$hresult = dbQuery($hsql);
	$user = array();
	while($hrow = dbFetchAssoc($hresult)) {	
		extract($hrow);
		$user['user_id'] = $id;
		$user['address'] = $address;
		$user['phone_no'] = $phone;
		$user['email'] = $email;
	}
	echo json_encode($user);
}

function generateEmployeeCode($startDate) {
    global $db;

    $date = new DateTime($startDate);
    $dateCode = $date->format('dmy');

    // Get the highest sequence number from all employee codes, not just for this date
    $sql = "SELECT MAX(CAST(SUBSTRING_INDEX(employee_code, '-', -1) AS UNSIGNED)) as max_seq 
            FROM tbl_employees";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $sequence = 1;
    if ($result && $result['max_seq'] !== null) {
        $sequence = intval($result['max_seq']) + 1;
    }

    return $dateCode . '-' . str_pad($sequence, 2, '0', STR_PAD_LEFT);
}


// ฟังก์ชันเพิ่มพนักงาน
function addEmployee() {
    global $db; 

    $citizenId = trim($_POST['citizenId'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $lastName = trim($_POST['lastname'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $religion = trim($_POST['religion'] ?? '');
    $birthDate = trim($_POST['birthDate'] ?? '');
    $startDate = trim($_POST['startDate'] ?? '');
    $age = intval($_POST['age'] ?? 0); 
    $phone = trim($_POST['phone'] ?? '');
    $emergency_phone = trim($_POST['emergency_phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $permanent_address = trim($_POST['permanent_address'] ?? '');
    $position = trim($_POST['position'] ?? '');
    $status = 'active';
    $nationality = trim($_POST['nationality'] ?? '');
    
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $type = trim($_POST['type'] ?? 'employee');

    if (!$citizenId || !$name || !$lastName || !$phone || !$address || !$position || !$startDate || !$email || !$password || !$emergency_phone || !$permanent_address || !$username || !$type) {
        header('Location: ../views/?v=EMPLOYEES&err=' . urlencode('กรุณากรอกข้อมูลให้ครบถ้วน'));
        exit();
    }

    $profilePic = null;
    if (!empty($_FILES['profile_pic']['name'])) {
        $uploadDir = '../uploads/employees/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $fileName = time() . '_' . basename($_FILES['profile_pic']['name']);
        $targetFilePath = $uploadDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
        $allowTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileType, $allowTypes) && move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetFilePath)) {
            $profilePic = $fileName;
        }
    }

    $employeeId = null;
    $employeeCode = generateEmployeeCode($startDate);

    try {
        $db->beginTransaction();
        
        $sql = "INSERT INTO tbl_employees (employee_id, employee_code, citizen_id, name, last_name, gender, religion, birth_date, start_date, age, phone, emergency_phone, address, permanent_address, nationality, position, status, profile_pic, username, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            $employeeId, $employeeCode, $citizenId, $name, $lastName, $gender, $religion,
            $birthDate, $startDate, $age, $phone, $emergency_phone, $address, $permanent_address,
            $nationality, $position, $status, $profilePic, $username
        ]);


        // ✅ ใช้ $username ที่รับมาจากฟอร์ม
        $userSql = "INSERT INTO tbl_users (name, first_name, last_name, pwd, address, phone, email, type, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active')";
        $userStmt = $db->prepare($userSql);
        $userStmt->execute([$username, $name, $lastName, $password, $address, $phone, $email, $type]);

        $db->commit();
        header('Location: ../views/?v=EMPLOYEES&msg=' . urlencode('เพิ่มพนักงานสำเร็จ'));
    } catch (Exception $e) {
        $db->rollBack();
        header('Location: ../views/?v=EMPLOYEES&err=' . urlencode('เกิดข้อผิดพลาด: ' . $e->getMessage()));
    }
    exit();
}


// ฟังก์ชันลบพนักงาน
function deleteEmployee() {
    global $db;
    $employeeId = $_GET['employeeId']; // รหัสพนักงาน

    try {
        // เริ่ม transaction
        $db->beginTransaction();

        // ดึงชื่อพนักงานเพื่อใช้ลบใน tbl_users
        $employeeSql = "SELECT name FROM tbl_employees WHERE employee_id = ?";
        $employeeStmt = $db->prepare($employeeSql);
        $employeeStmt->execute([$employeeId]);
        $employeeData = $employeeStmt->fetch(PDO::FETCH_ASSOC);

        if ($employeeData) {
            $userSql = "DELETE FROM tbl_users WHERE name LIKE ? AND type='employee'";
            $userStmt = $db->prepare($userSql);
            $userStmt->execute([$employeeData['name'] . '%']);
        }

        $taskSql = "DELETE FROM tbl_assigned_tasks WHERE employee_id = ?";
        $taskStmt = $db->prepare($taskSql);
        $taskStmt->execute([$employeeId]);

        // ลบพนักงาน
        $sql = "DELETE FROM tbl_employees WHERE employee_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$employeeId]);

        // ยืนยัน transaction
        $db->commit();
        $msg = 'ลบข้อมูลพนักงานสำเร็จ';
    } catch (Exception $e) {
        $db->rollBack();
        $msg = 'เกิดข้อผิดพลาดในการลบข้อมูลพนักงาน: ' . $e->getMessage();
    }

    header('Location: ../views/?v=EMPLOYEES&msg=' . urlencode($msg));
    exit();
}


function updateEmployee() {
    global $db;

    $employeeId = trim($_POST['employeeId']);
    $name = trim($_POST['name']);
    $lastName = trim($_POST['lastname']);
    $gender = trim($_POST['gender']);
    $age = intval($_POST['age'] ?? 0); 
    $birthDate = trim($_POST['birthDate']);
    $startDate = trim($_POST['startDate']);
    $phone = trim($_POST['phone']);
    $emergency_phone = trim($_POST['emergency_phone'] ?? '');
    $address = trim($_POST['address']);
    $permanent_address = trim($_POST['permanent_address'] ?? '');
    $position = trim($_POST['position']);
    $status = trim($_POST['status']);
    $nationality = trim($_POST['nationality']);
    $religion = trim($_POST['religion']);

    $profilePic = null;
    if (!empty($_FILES['profile_pic']['name'])) {
        $uploadDir = '../uploads/employees/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $fileName = time() . '_' . basename($_FILES['profile_pic']['name']);
        $targetFilePath = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetFilePath)) {
            $profilePic = $fileName;
        }
    }

    try {
        $db->beginTransaction();

        $sql = "UPDATE tbl_employees 
        SET name=?, last_name=?, gender=?, age=?, birth_date=?, start_date=?, phone=?, emergency_phone=?, 
        address=?, permanent_address=?, position=?, status=?, nationality=?, religion=?, 
        profile_pic=IFNULL(?, profile_pic)
            WHERE employee_id=?";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            $name, $lastName, $gender, $age, $birthDate, $startDate, $phone, $emergency_phone,
            $address, $permanent_address, $position, $status, $nationality, $religion,
            $profilePic, $employeeId
        ]);

        $db->commit();
        header('Location: ../views/?v=EMPLOYEES&msg=อัปเดตข้อมูลพนักงานสำเร็จ');
    } catch (Exception $e) {
        $db->rollBack();
        header('Location: ../views/?v=EMPLOYEES&err=' . urlencode('เกิดข้อผิดพลาด: ' . $e->getMessage()));
    }
    exit();
}


// ฟังก์ชันอัปเดตสถานะพนักงาน
function updateEmployeeStatus() {
    $employeeId = $_GET['employeeId']; // รหัสพนักงาน
    $status = $_GET['status']; // สถานะใหม่

    // อัปเดตสถานะในฐานข้อมูล
    $sql = "UPDATE tbl_employees SET status = '$status' WHERE employee_id = '$employeeId'";
    dbQuery($sql);

    $msg = 'Employee status successfully updated.';
    header('Location: ../views/?v=EMPLOYEES&msg=' . urlencode($msg));
    exit();
}

function addTruck() {
    $truckId = $_POST['truckId'];
    $model = $_POST['model'];
    $plateNumber = $_POST['plateNumber'];
    $capacity = (int)$_POST['capacity'];
    $status = $_POST['status'];

    $sqlCheck = "SELECT * FROM tbl_trucks WHERE truck_id = '$truckId'";
    $resultCheck = dbQuery($sqlCheck);

    if (dbNumRows($resultCheck) > 0) {
        header('Location: ../views/?v=TRUCKS&err=Truck ID already exists.');
        exit();
    }

    $sql = "INSERT INTO tbl_trucks (truck_id, model, plate_number, capacity, status) 
            VALUES ('$truckId', '$model', '$plateNumber', $capacity, '$status')";
    dbQuery($sql);

    header('Location: ../views/?v=TRUCKS&msg=Truck added successfully.');
    exit();
}

function deleteTruck() {
    global $db;
    $truckId = $_GET['truckId'];

    try {
        $db->beginTransaction();

        // ลบงานที่อ้างถึงรถบรรทุกนี้ก่อน
        $sqlDeleteTasks = "DELETE FROM tbl_assigned_tasks WHERE truck_id = ?";
        $stmtTasks = $db->prepare($sqlDeleteTasks);
        $stmtTasks->execute([$truckId]);

        // ลบรถบรรทุก
        $sqlDeleteTruck = "DELETE FROM tbl_trucks WHERE truck_id = ?";
        $stmtTruck = $db->prepare($sqlDeleteTruck);
        $stmtTruck->execute([$truckId]);

        $db->commit();
        $msg = "ลบรถบรรทุกสำเร็จ";
    } catch (Exception $e) {
        $db->rollBack();
        $msg = "เกิดข้อผิดพลาดในการลบรถบรรทุก: " . $e->getMessage();
    }

    header('Location: ../views/?v=TRUCKS&msg=' . urlencode($msg));
    exit();
}


function updateTruckStatus() {
    $truckId = $_GET['truckId'];
    $status = $_GET['status'];

    $sql = "UPDATE tbl_trucks SET status = '$status' WHERE truck_id = '$truckId'";
    dbQuery($sql);

    header('Location: ../views/?v=TRUCKS&msg=Truck status updated successfully.');
    exit();
}



function addRevenue() {
    global $db;
    header('Content-Type: application/json');

    if (!isset($_POST['bookingDate'], $_POST['amount'], $_POST['item_description'], $_POST['quantity'], $_POST['unit_price'], $_POST['issued_by'])) {
        echo json_encode(["success" => false, "message" => "Missing required fields"]);
        exit();
    }

    $bookingDate = $_POST['bookingDate'];
    $newAmount = floatval($_POST['amount']);
    $description = trim($_POST['item_description']);
    $quantity = intval($_POST['quantity']);
    $unitPrice = floatval($_POST['unit_price']);
    $withholdingTax = isset($_POST['withholding_tax']) ? 1 : 0;
    $issuedBy = intval($_POST['issued_by']);
    $receiptImage = null;

    // ดึง booking_id จาก tbl_reservations
    $stmt = $db->prepare("SELECT reservation_id FROM tbl_reservations WHERE rdate = ?");
    $stmt->execute([$bookingDate]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode(["success" => false, "message" => "Booking date not found"]);
        exit();
    }

    $bookingId = $row['reservation_id'];

    // อัปโหลดรูปภาพ (ถ้ามี)
    if (!empty($_FILES['receipt_image']['name'])) {
        $uploadDir = '../uploads/revenue/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = time() . '_' . basename($_FILES['receipt_image']['name']);
        $targetFilePath = $uploadDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
        $allowTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileType, $allowTypes) && move_uploaded_file($_FILES['receipt_image']['tmp_name'], $targetFilePath)) {
            $receiptImage = $fileName;
        }
    }

    // INSERT ข้อมูลใหม่เข้า tbl_revenue_updates
    $sql = "INSERT INTO tbl_revenue_updates (
                reservation_id, booking_date, updated_amount, item_description, quantity, unit_price,
                withholding_tax, issued_by, updated_at, receipt_image
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";

    $stmt = $db->prepare($sql);
    $success = $stmt->execute([
        $bookingId, $bookingDate, $newAmount, $description, $quantity, $unitPrice,
        $withholdingTax, $issuedBy, $receiptImage
    ]);

    echo json_encode([
        "success" => $success,
        "message" => $success ? "Revenue added successfully" : "Failed to add revenue"
    ]);
    exit();
}


function updateRevenue() {
    header('Content-Type: application/json');
    global $db;

    if (!isset($_POST['bookingId']) || !isset($_POST['amount'])) {
        echo json_encode(["success" => false, "message" => "Missing required fields"]);
        exit();
    }

    $bookingId = $_POST['bookingId'];
    $newAmount = floatval($_POST['amount']);
    $receiptImage = null;

    if ($newAmount <= 0) {
        echo json_encode(["success" => false, "message" => "Invalid amount"]);
        exit();
    }

    // ดึงค่าภาพล่าสุดจากฐานข้อมูล
    $stmt = $db->prepare("SELECT receipt_image FROM tbl_revenue_updates WHERE reservation_id = ?");
    $stmt->execute([$bookingId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $currentReceiptImage = $row['receipt_image'];

    // อัปโหลดรูปภาพใหม่ (ถ้ามี)
    if (!empty($_FILES['receipt_image']['name'])) {
        $uploadDir = '../uploads/revenue/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = time() . '_' . basename($_FILES['receipt_image']['name']);
        $targetFilePath = $uploadDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
        $allowTypes = array('jpg', 'jpeg', 'png', 'gif');

        if (in_array($fileType, $allowTypes) && move_uploaded_file($_FILES['receipt_image']['tmp_name'], $targetFilePath)) {
            $receiptImage = $fileName;
        }
    } else {
        // ไม่มีไฟล์ใหม่ ให้ใช้รูปเก่า ถ้า `currentReceiptImage` เป็นค่าว่างหรือ null ให้ใช้ค่าล่าสุด
        $receiptImage = !empty($currentReceiptImage) ? $currentReceiptImage : null;
    }

    // อัปเดตค่าที่ฐานข้อมูล
    $sql = "UPDATE tbl_revenue_updates 
            SET updated_amount = ?, receipt_image = ?, updated_at = NOW() 
            WHERE reservation_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$newAmount, $receiptImage, $bookingId]);

    // ตรวจสอบว่ามีการอัปเดตจริงหรือไม่
    if ($stmt->rowCount() === 0) {
        echo json_encode(["success" => false, "message" => "No changes were made."]);
        exit();
    }

    // ดึงค่าล่าสุดจากฐานข้อมูล
    $stmt = $db->prepare("SELECT updated_amount, receipt_image, updated_at FROM tbl_revenue_updates WHERE reservation_id = ?");
    $stmt->execute([$bookingId]);
    $updatedData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$updatedData) {
        echo json_encode(["success" => false, "message" => "Failed to retrieve updated data"]);
        exit();
    }

    echo json_encode(["success" => true, "message" => "Revenue updated successfully", "updatedData" => $updatedData]);
    exit();
}


function deleteRevenue() {
    global $db;

    if (!isset($_GET['bookingId'])) {
        error_log("Missing bookingId in request");
        header('Content-Type: application/json');
        echo json_encode(["success" => false, "message" => "Missing bookingId"]);
        exit();
    }

    $bookingId = $_GET['bookingId'];

    // ตรวจสอบว่ามีข้อมูลนี้อยู่ในฐานข้อมูลหรือไม่
    $sqlCheck = "SELECT * FROM tbl_revenue_updates WHERE reservation_id = ?";
    $stmtCheck = $db->prepare($sqlCheck);
    $stmtCheck->execute([$bookingId]);

    if ($stmtCheck->rowCount() == 0) {
        error_log("Record not found for bookingId: " . $bookingId);
        header('Content-Type: application/json');
        echo json_encode(["success" => false, "message" => "Record not found"]);
        exit();
    }

    // ลบข้อมูล
    $sqlDelete = "DELETE FROM tbl_revenue_updates WHERE reservation_id = ?";
    $stmtDelete = $db->prepare($sqlDelete);
    $success = $stmtDelete->execute([$bookingId]);

    error_log("Delete status: " . json_encode($success)); //Debug

    header('Content-Type: application/json');
    echo json_encode(["success" => $success, "message" => $success ? "Deleted successfully" : "Delete failed"]);
    exit();
}

function getTotalRevenue() {
    global $db;

    // คำนวณผลรวมจาก tbl_revenue_updates
    $stmt = $db->prepare("SELECT SUM(updated_amount) AS total FROM tbl_revenue_updates");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $totalRevenue = $row['total'] ?? 0;

    echo json_encode(["success" => true, "totalRevenue" => $totalRevenue]);
    exit();
}





if (isset($_GET['cmd']) && $_GET['cmd'] === 'deleteOutsourcedBooking' && isset($_GET['bookingId'])) {
    header('Content-Type: application/json');
    global $db;
    
    $bookingId = intval($_GET['bookingId']);
    
    $stmt = $db->prepare("DELETE FROM tbl_outsourced_bookings WHERE outsourced_booking_id = ?");
    $result = $stmt->execute([$bookingId]);

    echo json_encode(["success" => $result, "message" => $result ? "Booking deleted successfully" : "Failed to delete"]);
    exit();
}

if (isset($_GET['cmd']) && $_GET['cmd'] === 'getOutsourcedBookingDetails' && isset($_GET['bookingId'])) {
    header('Content-Type: application/json');
    global $db;

    $bookingId = intval($_GET['bookingId']);
    
    $stmt = $db->prepare("SELECT * FROM tbl_outsourced_bookings WHERE outsourced_booking_id = ?");
    $stmt->execute([$bookingId]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode(["success" => (bool)$booking, "booking" => $booking ?: null, "message" => $booking ? "Booking found" : "Booking not found"]);
    exit();
}

if (isset($_POST['cmd']) && $_POST['cmd'] === 'updateOutsourcedBooking' && isset($_POST['reservation_id'], $_POST['price'])) {
    header('Content-Type: application/json');
    global $db;

    $bookingId = intval($_POST['reservation_id']);
    $price = floatval($_POST['price']);
    $attachment = null;

    // ตรวจสอบว่ามีไฟล์แนบใหม่หรือไม่
    if (!empty($_FILES['attachment']['name'])) {
        $targetDir = "../uploads/outsourcing/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        
        $fileName = time() . "_" . basename($_FILES["attachment"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        
        if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $targetFilePath)) {
            $attachment = $fileName;
        }
    }

    // อัปเดตฐานข้อมูล
    $sql = "UPDATE tbl_outsourced_bookings SET price = ? ";
    $params = [$price];

    if ($attachment) {
        $sql .= ", attachment = ?";
        $params[] = $attachment;
    }

    $sql .= " WHERE outsourced_booking_id = ?";
    $params[] = $bookingId;

    $stmt = $db->prepare($sql);
    $result = $stmt->execute($params);

    // ส่งข้อมูลการจองที่อัปเดตแล้วกลับไป
    $booking = null;
    if ($result) {
        $stmt = $db->prepare("SELECT * FROM tbl_outsourced_bookings WHERE outsourced_booking_id = ?");
        $stmt->execute([$bookingId]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    echo json_encode([
        "success" => $result, 
        "message" => $result ? "Booking updated successfully" : "Failed to update",
        "booking" => $booking
    ]);
    exit();
}


?>