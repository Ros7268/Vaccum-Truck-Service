<?php
require_once '../library/functions.php';

$resId = $_GET['resId'] ?? null;
if (!$resId) {
    echo "Missing reservation ID";
    exit;
}

global $conn, $dbHost, $dbUser, $dbPass, $dbName;
if (!$conn) {
    $conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);
    if (!$conn) die("Database connection failed.");
}

$query = "
    SELECT 
        u.first_name, u.last_name,
        u.phone as user_phone,
        r.address,
        r.rdate as res_date
    FROM tbl_reservations r
    LEFT JOIN tbl_users u ON r.user_id = u.user_id
    WHERE r.reservation_id = $resId
    LIMIT 1
";
$result = mysqli_query($conn, $query);
if (!$result || mysqli_num_rows($result) === 0) {
    echo "Reservation not found.";
    exit;
}

$row = mysqli_fetch_assoc($result);
$customerName = $row['first_name'] . ' ' . $row['last_name'];
$address = $row['address'];
$date = $row['res_date'];
$phone = $row['user_phone'];
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>Assigned Task #<?php echo $resId; ?></title>
  <style>
    body {
      font-family: 'Tahoma', sans-serif;
      padding: 30px;
      color: #333;
    }

    .header {
      text-align: center;
      margin-bottom: 30px;
    }

    .header img {
      width: 120px;
    }

    .header h2 {
      margin-top: 10px;
    }

    table.details {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    table.details td {
      padding: 12px;
      border: 1px solid #aaa;
    }

    table.details td.label {
      font-weight: bold;
      width: 30%;
      background-color: #f0f0f0;
    }

    .footer {
      margin-top: 50px;
      text-align: center;
      font-size: 13px;
      color: #999;
    }

    @media print {
      .no-print {
        display: none;
      }
    }

    .print-button {
      display: block;
      margin: 30px auto;
      padding: 10px 20px;
      font-size: 16px;
      background-color: #4CAF50;
      color: white;
      border: none;
      cursor: pointer;
      border-radius: 5px;
    }

    .print-button:hover {
      background-color: #45a049;
    }
  </style>
</head>
<body>

  <div class="header">
    <img src="../assets/logo.jpg" alt="Logo">
    <h2>รายละเอียดงานที่ได้รับมอบหมาย</h2>
  </div>

  <table class="details">
    <tr>
      <td class="label">ชื่อลูกค้า</td>
      <td><?php echo htmlspecialchars($customerName); ?></td>
    </tr>
    <tr>
      <td class="label">ที่อยู่ลูกค้า</td>
      <td><?php echo htmlspecialchars($address); ?></td>
    </tr>
    <tr>
      <td class="label">วันและเวลา</td>
      <td><?php echo htmlspecialchars($date); ?></td>
    </tr>
    <tr>
      <td class="label">เบอร์โทรลูกค้า</td>
      <td><?php echo htmlspecialchars($phone); ?></td>
    </tr>
  </table>

  <div class="footer">
    Tanawat Service &copy; <?php echo date("Y"); ?>
  </div>

  <div class="no-print" style="text-align:center;">
    <button class="print-button" onclick="window.print()">📄 พิมพ์ / บันทึกเป็น PDF</button>
  </div>

</body>
</html>