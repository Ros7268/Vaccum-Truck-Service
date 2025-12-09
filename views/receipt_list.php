<?php 
require_once '../library/functions.php';

// ตรวจสอบว่ามีผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['calendar_fd_user'])) {
    header("Location: ../login.php");
    exit();
}

// รับค่าหน้าปัจจุบัน (ถ้าไม่มีให้เป็นหน้า 1)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// ดึงข้อมูลผู้ใช้จาก Session
$user = $_SESSION['calendar_fd_user'];
$user_id = $user['user_id'];
$user_type = $user['type'];

// ถ้าเป็น Admin หรือ Owner ให้ดูข้อมูลทั้งหมด ถ้าเป็น User ให้ดูแค่ของตัวเอง
$receiptData = getReceiptRecords($page, ($user_type === 'admin' || $user_type === 'owner' || $user_type === 'employee') ? null : $user_id);
$records = $receiptData['data'];
$total_records = $receiptData['total'];
$per_page = $receiptData['per_page'];
$total_pages = ceil($total_records / $per_page);
?>

<div class="container-fluid">
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">Receipts List</h3>
    </div>

    <div class="box-body">
      <div class="row">
        <div class="col-md-12">
          <table class="table table-bordered table-hover">
            <thead>
              <tr class="bg-light">
                <th>#</th>
                <th>Booking ID</th>
                <th>User Name</th>
                <th>Amount</th>
                <th>Receipt Date</th>
                <th>Download</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $idx = ($page - 1) * $per_page + 1;
              foreach ($records as $rec) {
                extract($rec);
              ?>
              <tr>
                <td><?php echo $idx++; ?></td>
                <td><?php echo $reservation_id; ?></td>
                <td><?php echo htmlspecialchars($user_name ?? ''); ?></td>
                <td><?php echo number_format($updated_amount, 2) . " ฿"; ?></td>
                <td><?php echo (new DateTime($updated_at))->format('d/m/Y H:i:s'); ?></td>
                <td>
                  <a href="generate_receipt.php?booking_id=<?php echo $reservation_id; ?>" class="btn btn-primary btn-sm">
                    <i class="fa fa-download"></i> Download PDF
                  </a>
                </td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div class="box-footer clearfix">
      <div class="row">
        <div class="col-md-12 text-right">
          <ul class="pagination pagination-sm no-margin">
            <?php if ($page > 1) { ?>
              <li><a href="?v=RECEIPT&page=<?php echo ($page - 1); ?>">« Previous</a></li>
            <?php } ?>
            
            <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
              <li class="<?php echo ($i == $page) ? 'active' : ''; ?>">
                <a href="?v=RECEIPT&page=<?php echo $i; ?>"><?php echo $i; ?></a>
              </li>
            <?php } ?>

            <?php if ($page < $total_pages) { ?>
              <li><a href="?v=RECEIPT&page=<?php echo ($page + 1); ?>">Next »</a></li>
            <?php } ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
