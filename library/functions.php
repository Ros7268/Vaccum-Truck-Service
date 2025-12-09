<?php
require_once('mail.php');
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';



function checkFDUser()
{
	// if the session id is not set, redirect to login page
	if (!isset($_SESSION['calendar_fd_user'])) {
		header('Location: ' . WEB_ROOT . 'login.php');
		exit;
	}
	// the user want to logout
	if (isset($_GET['logout'])) {
		doLogout();
	}
}

function doLogin()
{
	$name 	= $_POST['name'];
	$pwd 	= $_POST['pwd'];
	
	$errorMessage = '';

	$sql 	= "SELECT * FROM tbl_users WHERE name = '$name' AND pwd = '$pwd'";
	$result = dbQuery($sql);
	
	if (dbNumRows($result) == 1) {
		$row = dbFetchAssoc($result);
		$_SESSION['calendar_fd_user'] = $row;
		$_SESSION['calendar_fd_user_name'] = $row['username'];
		header('Location: index.php');
		exit();
	}
	else {
		$errorMessage = 'Invalid username / passsword. Please try again or contact to support.';
	}
	return $errorMessage;
}


/*
	Logout a user
*/
function doLogout()
{
	if (isset($_SESSION['calendar_fd_user'])) {
		unset($_SESSION['calendar_fd_user']);
		//session_unregister('hlbank_user');
	}
	header('Location: index.php');
	exit();
}

function calculateMonthlyAssignments() {
    // Query แยกดึงข้อมูล employee และ driver แล้วรวมด้วย UNION
    $sql = "
        (SELECT 
            DATE_FORMAT(r.rdate, '%Y-%m') AS month,
            e.name AS person,
            COUNT(a.assigned_task_id) AS assignments
        FROM 
            tbl_assigned_tasks a
        LEFT JOIN 
            tbl_reservations r ON a.reservation_id = r.reservation_id
        LEFT JOIN 
            tbl_employees e ON a.employee_id = e.employee_id
        WHERE 
            e.name IS NOT NULL
        GROUP BY 
            month, e.name)
        
        UNION ALL
        
        (SELECT 
            DATE_FORMAT(r.rdate, '%Y-%m') AS month,
            e.name AS person,
            COUNT(a.assigned_task_id) AS assignments
        FROM 
            tbl_assigned_tasks a
        LEFT JOIN 
            tbl_reservations r ON a.reservation_id = r.reservation_id
        LEFT JOIN 
            tbl_employees e ON a.driver_id = e.employee_id
        WHERE 
            e.name IS NOT NULL
        GROUP BY 
            month, e.name)
        
        ORDER BY 
            month DESC, person ASC
    ";
    
    $result = dbQuery($sql);
    
    // รวมชื่อที่เหมือนกันในแต่ละเดือน
    $data = [];
    while ($row = dbFetchAssoc($result)) {
        $month = $row['month'];
        $person = $row['person'];
        $assignments = $row['assignments'];
        
        // รวม Assignments โดยสนใจเฉพาะชื่อพนักงาน
        if (!isset($data[$month][$person])) {
            $data[$month][$person] = 0;
        }
        $data[$month][$person] += $assignments;
    }
    
    return $data;
}

function getAssignedWorksWithUser() {
    $sql = "
        SELECT 
            at.reservation_id, 
            r.rdate AS date, 
            u.user_id,
            u.name AS user_name,
            r.address AS customer_address,
            u.phone AS customer_phone,
            d.name AS driver_name,
            d.phone AS driver_phone,
            GROUP_CONCAT(e.name SEPARATOR ', ') AS employee_names,
            GROUP_CONCAT(e.phone SEPARATOR ', ') AS employee_phones,
            t.model AS truck_model,
            t.plate_number AS truck_plate_number
        FROM tbl_assigned_tasks at
        JOIN tbl_reservations r ON at.reservation_id = r.reservation_id
        JOIN tbl_users u ON r.user_id = u.user_id
        LEFT JOIN tbl_employees d ON at.driver_id = d.employee_id
        LEFT JOIN tbl_employees e ON at.employee_id = e.employee_id
        LEFT JOIN tbl_trucks t ON at.truck_id = t.truck_id
        GROUP BY at.reservation_id, r.rdate, u.user_id, u.name, r.address, u.phone, d.name, d.phone, t.model, t.plate_number
        ORDER BY r.rdate DESC
    ";
    $result = dbQuery($sql);
    $assignedWorks = [];
    while ($row = dbFetchAssoc($result)) {
        $assignedWorks[] = $row;
    }
    return $assignedWorks;
}


function getAssignedRecords() {
    global $db;

    $per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $start = ($current_page - 1) * $per_page;

    $sql = "
        SELECT 
            r.reservation_id AS res_id, 
            r.rdate AS res_date, 
            r.address, 
            u.phone AS user_phone,
            u.name AS user_name, 
            GROUP_CONCAT(e.name SEPARATOR ', ') AS employee_names,
            d.name AS driver_name,
            t.plate_number AS truck_plate_number,
            at.assigned_by
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


function getBookingRecords() {
    global $db;
    
    $per_page = 15;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $start = ($page - 1) * $per_page;
    
    $type = $_SESSION['calendar_fd_user']['type'] ?? '';
    $currentUserId = $_SESSION['calendar_fd_user']['user_id'] ?? 0;
    
    if ($type === 'admin' || $type == 'owner' || $type === 'employee' || $type === 'driver') {
        $sql = "
            SELECT 
                r.reservation_id AS res_id, 
                u.user_id,
                u.name AS user_name,
                u.first_name,
                u.last_name,
                u.phone AS user_phone,
                u.email AS user_email,
                r.address,
                r.ucount AS count, 
                r.rdate AS res_date, 
                r.status,
                r.service_type
            FROM tbl_users u
            INNER JOIN tbl_reservations r ON u.user_id = r.user_id
            ORDER BY r.rdate DESC 
            LIMIT $start, $per_page
        ";
    } else {
        $sql = "
            SELECT 
                r.reservation_id AS res_id, 
                u.user_id,
                u.name AS user_name,
                u.first_name,
                u.last_name,
                u.phone AS user_phone,
                u.email AS user_email,
                r.address,
                r.ucount AS count, 
                r.rdate AS res_date, 
                r.status,
                r.service_type
            FROM tbl_users u
            INNER JOIN tbl_reservations r ON u.user_id = r.user_id
            WHERE u.user_id = ?
            ORDER BY r.rdate DESC 
            LIMIT $start, $per_page
        ";
    }    
    
    $stmt = $db->prepare($sql);
    if ($type !== 'admin' && $type !== 'owner' && $type !== 'employee' && $type !== 'driver') {
        $stmt->execute([$currentUserId]);
    } else {
        $stmt->execute();
    }
    
    $records = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $records[] = [
            "res_id" => $row['res_id'],
            "user_id" => $row['user_id'],
            "user_name" => $row['user_name'],
            "first_name" => $row['first_name'],
            "last_name" => $row['last_name'],
            "user_phone" => $row['user_phone'],
            "user_email" => $row['user_email'],
            "address" => $row['address'],
            "count" => $row['count'],
            "res_date" => $row['res_date'],
            "status" => $row['status'],
            "service_type" => $row['service_type'],
           
        ];
    }
    
    $count_sql = ($type === 'admin' || $type === 'owner' || $type === 'employee' || $type === 'driver') 
        ? "SELECT COUNT(*) AS total FROM tbl_reservations"
        : "SELECT COUNT(*) AS total FROM tbl_reservations WHERE user_id = ?";
        
    $stmt_count = $db->prepare($count_sql);
    if ($type !== 'admin' && $type !== 'owner' && $type !== 'employee' && $type !== 'driver') {
        $stmt_count->execute([$currentUserId]);
    } else {
        $stmt_count->execute();
    }
    $total_records = $stmt_count->fetch(PDO::FETCH_ASSOC)['total'];
    
    return [
        'data' => $records,
        'total_records' => $total_records
    ];
}


function getUserRecords() {
    global $db;
    
    $per_page = 15;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $start = ($page - 1) * $per_page;
    
    $type = $_SESSION['calendar_fd_user']['type'] ?? null;
    $type = trim(strtolower($type));
    
    if ($type === 'user') {
        $user_id = $_SESSION['calendar_fd_user']['id'];
        $sql = "SELECT * FROM tbl_users WHERE type != 'admin' AND user_id = $user_id AND status != 'delete' ORDER BY user_id DESC";
        $count_sql = "SELECT COUNT(*) AS total FROM tbl_users WHERE type != 'admin' AND user_id = $user_id AND status != 'delete'";
    } else {
        $sql = "SELECT * FROM tbl_users WHERE status != 'delete' ORDER BY user_id DESC LIMIT $start, $per_page";
        $count_sql = "SELECT COUNT(*) AS total FROM tbl_users WHERE status != 'delete'" ;
    }
    
    $result = dbQuery($sql);
    $records = [];
    while ($row = dbFetchAssoc($result)) {
        $records[] = [
            "user_id" => $row['user_id'],
            "first_name" => $row['first_name'],
            "last_name" => $row['last_name'],
            "user_name" => $row['name'],
            "user_phone" => $row['phone'],
            "user_email" => $row['email'],
            "type" => $row['type'],
            "status" => $row['status'],
        ];
    }
    
    $count_result = dbQuery($count_sql);
    $total_records = dbFetchAssoc($count_result)['total'];
    $total_pages = ceil($total_records / $per_page);
    
    return [
        'data' => $records,
        'total_records' => $total_records,
        'total_pages' => $total_pages,
        'current_page' => $page
    ];
}


// ฟังก์ชันดึงข้อมูล Employees
function getEmployeeRecords() {
    $per_page = 10; // จำนวนรายการต่อหน้า
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $start = ($page - 1) * $per_page;

    // ดึงข้อมูลพนักงานจากฐานข้อมูล
    $sql = "SELECT * FROM tbl_employees ORDER BY employee_id DESC LIMIT $start, $per_page";
    $result = dbQuery($sql);

    // ดึงจำนวนทั้งหมดเพื่อใช้ใน Pagination
    $countSql = "SELECT COUNT(*) as total FROM tbl_employees";
    $countResult = dbQuery($countSql);
    $total_records = dbFetchAssoc($countResult)['total'];

    $records = [];
    while ($row = dbFetchAssoc($result)) {
        $records[] = $row;
    }

    return [
        'data' => $records,
        'total' => $total_records,
        'per_page' => $per_page,
        'current_page' => $page
    ];
}


function getTruckRecords() {
    $per_page = 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $start = ($page - 1) * $per_page;

    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM tbl_trucks ORDER BY truck_id DESC LIMIT $start, $per_page";
    $result = dbQuery($sql);

    $records = [];
    while ($row = dbFetchAssoc($result)) {
        $records[] = $row;
    }

    $total_result = dbQuery("SELECT FOUND_ROWS() AS total");
    $total_row = dbFetchAssoc($total_result);
    $total_records = $total_row['total'];

    return [
        'data' => $records,
        'total' => $total_records,
        'per_page' => $per_page,
        'current_page' => $page
    ];
}


function getRevenueUpdateRecords($page = 1, $per_page = 5) {
    global $db;
    $offset = ($page - 1) * $per_page;
    
    $sql = "
        SELECT 
            ru.revenue_id,
            ru.reservation_id,
            ru.booking_date,
            ru.updated_amount,
            ru.updated_at,
            ru.receipt_image,
            u.first_name AS user_first_name,
            u.last_name AS user_last_name,
            r.address AS booking_address,
            iu.first_name AS issued_first_name,
            iu.last_name AS issued_last_name
        FROM tbl_revenue_updates ru
        LEFT JOIN tbl_reservations r ON ru.reservation_id = r.reservation_id
        LEFT JOIN tbl_users u ON r.user_id = u.user_id
        LEFT JOIN tbl_users iu ON ru.issued_by = iu.user_id
        ORDER BY ru.updated_at DESC
        LIMIT :offset, :per_page
    ";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->bindValue(':per_page', (int)$per_page, PDO::PARAM_INT);
    $stmt->execute();
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // ดึงจำนวนรายการทั้งหมด
    $countSql = "SELECT COUNT(*) AS total FROM tbl_revenue_updates";
    $countResult = $db->query($countSql);
    $total_records = $countResult->fetch(PDO::FETCH_ASSOC)['total'];
    
    return [
        'data' => $records,
        'total' => $total_records,
        'per_page' => $per_page,
        'current_page' => $page
    ];
}





function getTotalRevenue() {
    $sql = "SELECT SUM(updated_amount) AS total FROM tbl_revenue_updates";
    $result = dbQuery($sql);
    $row = dbFetchAssoc($result);
    return $row['total'] ?? 0;
}



function updateRevenue() {
    if (!isset($_POST['bookingDate']) || !isset($_POST['amount'])) {
        header('Location: ../views/?v=REVENUE&err=' . urlencode('Missing required fields.'));
        exit();
    }

    $selectedDate = $_POST['bookingDate'];
    $newAmount = floatval($_POST['amount']);

    if ($newAmount <= 0) {
        header('Location: ../views/?v=REVENUE&err=' . urlencode('Invalid amount.'));
        exit();
    }

    // อัปเดต Revenue ใน tbl_reservations
    $sql = "UPDATE tbl_reservations 
            SET amount = $newAmount 
            WHERE rdate = '$selectedDate' AND status = 'SUCCESS'";
    dbQuery($sql);

    // ดึง booking_id ของวันที่ที่เลือก
    $sql = "SELECT id FROM tbl_reservations WHERE rdate = '$selectedDate' LIMIT 1";
    $result = dbQuery($sql);
    $row = dbFetchAssoc($result);

    if ($row) {
        $bookingId = $row['id'];

        // บันทึกข้อมูลการอัปเดตลง tbl_revenue_updates
        $insertSql = "INSERT INTO tbl_revenue_updates (booking_id, booking_date, updated_amount)
                      VALUES ($bookingId, '$selectedDate', $newAmount)";
        dbQuery($insertSql);
    }

    header('Location: ../views/?v=REVENUE&msg=' . urlencode('Revenue updated successfully.'));
    exit();
}


function getMonthlyRevenueRecords() {
    $sql = "
        SELECT 
            DATE_FORMAT(ru.booking_date, '%Y-%m') AS month, 
            SUM(ru.updated_amount) AS total_revenue
        FROM tbl_revenue_updates ru
        GROUP BY DATE_FORMAT(ru.booking_date, '%Y-%m')
        ORDER BY DATE_FORMAT(ru.booking_date, '%Y-%m') DESC
    ";

    $result = dbQuery($sql);
    $records = [];

    while ($row = dbFetchAssoc($result)) {
        $records[] = $row;
    }

    return $records;
}


function getApprovedBookings() {
    $sql = "
        SELECT r.reservation_id, r.rdate, u.name AS username
        FROM tbl_reservations r
        LEFT JOIN tbl_users u ON r.user_id = u.user_id
        LEFT JOIN tbl_assigned_work aw ON r.reservation_id = aw.reservation_id
        WHERE r.status = 'APPROVED' AND aw.reservation_id IS NULL
        ORDER BY r.rdate ASC
    ";

    $result = dbQuery($sql);
    $bookings = [];
    while ($row = dbFetchAssoc($result)) {
        $bookings[] = $row;
    }
    return $bookings;
}



function getEmployees() {
    $sql = "
        SELECT employee_id, name, phone
        FROM tbl_employees
        WHERE status = 'active'
    ";
    $result = dbQuery($sql);
    $employees = [];
    while ($row = dbFetchAssoc($result)) {
        $employees[] = $row;
    }
    return $employees;
}


function getUnupdatedBookingDates() {
    $sql = "
        SELECT r.rdate 
        FROM tbl_reservations r
        WHERE r.status = 'SUCCESS' 
        AND NOT EXISTS (
            SELECT 1 FROM tbl_revenue_updates ru WHERE ru.booking_date = r.rdate
        )
        ORDER BY r.rdate ASC
    ";
    $result = dbQuery($sql);

    $dates = [];
    while ($row = dbFetchAssoc($result)) {
        $dates[] = $row;
    }
    return $dates;
}

function getReceiptRecords($page = 1, $user_id = null) {
    $per_page = 10;
    $start = ($page - 1) * $per_page;

    // เชื่อม `tbl_revenue_updates` → `tbl_reservations` → `tbl_users`
    $whereCondition = ($user_id !== null) ? "WHERE r.user_id = $user_id" : "";

    $sql = "
        SELECT ru.reservation_id, u.name AS user_name, ru.updated_amount, ru.updated_at 
        FROM tbl_revenue_updates ru
        LEFT JOIN tbl_reservations r ON ru.reservation_id = r.reservation_id
        LEFT JOIN tbl_users u ON r.user_id = u.user_id
        $whereCondition
        ORDER BY ru.updated_at DESC
        LIMIT $start, $per_page
    ";
    
    $result = dbQuery($sql);
    $records = [];
    while ($row = dbFetchAssoc($result)) {
        $records[] = $row;
    }

    // นับจำนวนทั้งหมดสำหรับ Pagination
    $countSql = "SELECT COUNT(*) AS total FROM tbl_revenue_updates ru LEFT JOIN tbl_reservations r ON ru.reservation_id = r.reservation_id $whereCondition";
    $countResult = dbQuery($countSql);
    $total_records = dbFetchAssoc($countResult)['total'];

    return [
        'data' => $records,
        'total' => $total_records,
        'per_page' => $per_page,
        'current_page' => $page
    ];
}

// นับจำนวน Transactions
function getTotalRevenueTransactions() {
    $sql = "SELECT COUNT(*) AS total FROM tbl_revenue_updates";
    $result = dbQuery($sql);
    $row = dbFetchAssoc($result);
    return $row['total'] ?? 0;
}

// ดึงวันที่อัปเดตล่าสุด
function getLastRevenueUpdateDate() {
    $sql = "SELECT MAX(updated_at) AS last_update FROM tbl_revenue_updates";
    $result = dbQuery($sql);
    $row = dbFetchAssoc($result);
    return $row['last_update'] ? (new DateTime($row['last_update']))->format('d/m/Y H:i') : 'N/A';
}

function getOutsourcedBookings() {
    global $db;
    $sql = "SELECT ob.outsourced_booking_id, ob.reservation_id, ob.price, ob.attachment, r.rdate 
            FROM tbl_outsourced_bookings ob
            JOIN tbl_reservations r ON ob.reservation_id = r.reservation_id
            ORDER BY ob.outsourced_booking_id DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPendingBookings() {
    global $db;
    $sql = "SELECT reservation_id, rdate FROM tbl_reservations WHERE status = 'pending' ORDER BY rdate ASC";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function getMyAssignedTasks() {
    global $conn, $dbHost, $dbUser, $dbPass, $dbName;

    if (!$conn) {
        require_once __DIR__ . '/config.php';
        $conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

        if (!$conn) {
            die('Database Connection Failed: ' . mysqli_connect_error());
        }
    }

    $perPage = 10;
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $start = ($currentPage - 1) * $perPage;

    if (!isset($_SESSION['calendar_fd_user']['name'])) {
        return [];
    }

    $userName = $_SESSION['calendar_fd_user']['name'];

    // ดึงรหัสพนักงานจากชื่อผู้ใช้
    $empQuery = "SELECT employee_id FROM tbl_employees WHERE username = '$userName'";
    $empResult = mysqli_query($conn, $empQuery);

    if (!$empResult || mysqli_num_rows($empResult) == 0) {
        return [];
    }

    $empRow = mysqli_fetch_assoc($empResult);
    $employeeId = $empRow['employee_id'];

    // ดึงรายการงานที่ได้รับมอบหมาย
    $query = "
        SELECT 
            r.reservation_id as res_id,
            r.rdate as res_date,
            u.first_name, 
            u.last_name,
            r.address,
            u.phone as user_phone,
            t.plate_number as truck_plate_number,
            d.name as driver_name,
            GROUP_CONCAT(DISTINCT e.name SEPARATOR ', ') as employee_names,
            a.assigned_by
        FROM tbl_assigned_tasks a
        LEFT JOIN tbl_reservations r ON a.reservation_id = r.reservation_id
        LEFT JOIN tbl_users u ON r.user_id = u.user_id
        LEFT JOIN tbl_employees d ON a.driver_id = d.employee_id
        LEFT JOIN tbl_employees e ON a.employee_id = e.employee_id
        LEFT JOIN tbl_trucks t ON a.truck_id = t.truck_id
        WHERE a.driver_id = '$employeeId' OR a.employee_id = '$employeeId'
        GROUP BY a.reservation_id
        ORDER BY a.created_at DESC
        LIMIT $start, $perPage
    ";

    $result = mysqli_query($conn, $query);
    $records = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $row['user_name'] = $row['first_name'] . ' ' . $row['last_name'];
            unset($row['first_name'], $row['last_name']);
            $records[] = $row;
        }
    }

    // นับจำนวนทั้งหมดเพื่อทำ pagination
    $countQuery = "
        SELECT COUNT(DISTINCT a.reservation_id) AS total 
        FROM tbl_assigned_tasks a
        WHERE a.driver_id = '$employeeId' OR a.employee_id = '$employeeId'
    ";
    $countResult = mysqli_query($conn, $countQuery);
    $totalRecords = 0;
    if ($countResult) {
        $countRow = mysqli_fetch_assoc($countResult);
        $totalRecords = $countRow['total'];
    }

    return [
        'data' => $records,
        'total_records' => $totalRecords,
        'per_page' => $perPage,
        'current_page' => $currentPage,
        'total_pages' => $totalRecords > 0 ? ceil($totalRecords / $perPage) : 1
    ];
}


function getEmployeesOrAdmins(): array {
    $sql = "SELECT user_id, first_name, last_name 
            FROM tbl_users 
            WHERE type IN ('employee', 'admin') AND status = 'active' 
            ORDER BY first_name ASC";
    $result = dbQuery($sql);
    
    $users = [];
    while ($row = dbFetchAssoc($result)) {
        $users[] = $row;
    }
    return $users;
}


?>