<?php
// เพิ่มการตรวจสอบประเภทผู้ใช้ (role) ที่ dashboard.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ดึงประเภทผู้ใช้จากเซสชัน
$userType = $_SESSION['calendar_fd_user']['type'];

// ตรวจสอบว่าผู้ใช้มีสิทธิ์ในการจองหรือไม่
$canBook = ($userType === 'admin' || $userType === 'user');
?>

<?php if ($canBook): ?>
<!-- สำหรับผู้ใช้ที่มีสิทธิ์จอง (admin, user) แสดงทั้ง calendar และ eventform -->
<div class="col-md-8">
  <!-- แสดง Calendar -->
  <?php include('calendar.php'); ?>
</div>
<!-- /.col -->
<div class="col-md-4">
  <!-- แสดง Event Form -->
  <?php include('eventform.php'); ?>
</div>
<!-- /.col -->
<?php else: ?>
<!-- สำหรับผู้ใช้ที่ไม่มีสิทธิ์จอง (owner, employee) แสดงเฉพาะ calendar เต็มหน้าจอ -->
<div class="col-md-12">
  <!-- แสดง Calendar เต็มหน้าจอ -->
  <?php include('calendar.php'); ?>
</div>
<!-- /.col -->
<?php endif; ?>