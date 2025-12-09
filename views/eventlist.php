<?php 
$bookingData = getBookingRecords(); 
$records = $bookingData['data'];
$total_records = $bookingData['total_records'];

$per_page = 15; 
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$total_pages = ceil($total_records / $per_page);

$utype = '';
$type = $_SESSION['calendar_fd_user']['type'];
$currentUserId = $_SESSION['calendar_fd_user']['user_id'];

if ($type == 'admin' || $type == 'owner' || $type == 'employee' ||$type == 'driver') {
  $utype = 'on';
  } else {
    $records = array_filter($records, function ($record) use ($currentUserId) {
        return $record['user_id'] == $currentUserId;
    });
  }
?>

<div class="col-md-12">
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">Event Booking Details</h3>
    </div>
    <div class="box-body">
      <table class="table table-bordered">
        <tr>
          <th>#</th>
          <th>User Name</th>
          <th>First Name</th>
          <th>Last Name</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Booking Date</th>
          <th>Spare Number</th>
          <th>Service Type</th>
          <th>Status</th>
          <?php if ($utype == 'on' || $type == 'user') { ?>
          <th>Action</th>
          <?php } ?>
        </tr>
        <?php
        $idx = ($current_page - 1) * $per_page + 1;
        foreach ($records as $rec) {
            extract($rec);
            $status_class = match ($status) {
              "PENDING" => 'warning',
              "APPROVED" => 'success',
              "DENIED" => 'danger',
              "SUCCESS" => 'primary',
              "OUTSOURCE" => 'label-info outsource-status',
              default => 'secondary'
          };
        ?>
        <tr>
          <td><?php echo $idx++; ?></td>
          <td><a href="<?php echo WEB_ROOT; ?>views/?v=USER&ID=<?php echo $user_id; ?>"><?php echo strtoupper($user_name); ?></a></td>
          <td><?php echo htmlspecialchars($first_name); ?></td>
          <td><?php echo htmlspecialchars($last_name); ?></td>
          <td><?php echo htmlspecialchars($user_email); ?></td>
          <td><?php echo htmlspecialchars($user_phone); ?></td>
          <td><?php echo (new DateTime($res_date))->format('d/m/Y H:i'); ?></td>
          <td><?php echo $count; ?></td>
          <td><?php echo htmlspecialchars($rec['service_type'] ?? ''); ?></td>
          <td><span class="label label-<?php echo $status_class; ?>"><?php echo $status; ?></span></td>
          <?php if ($utype == 'on' || $type == 'user') { ?>
          <td>
            <?php if ($type == 'user' && $rec['status'] == "PENDING") { ?>
              <a href="javascript:deleteUser('<?php echo $rec['res_id']; ?>');">Delete</a>
            <?php } elseif ($type != 'user') { ?>
              <?php if ($rec['status'] == "PENDING") { ?>
                <a href="javascript:approve('<?php echo $rec['res_id']; ?>');">Approve</a> /
                <a href="javascript:decline('<?php echo $rec['res_id']; ?>');">Denied</a> /
                <a href="javascript:deleteUser('<?php echo $rec['res_id']; ?>');">Delete</a> /
                <a href="javascript:editBooking('<?php echo $rec['res_id']; ?>','<?php echo addslashes($user_phone); ?>','<?php echo (new DateTime($res_date))->format('Y-m-d'); ?>','<?php echo (new DateTime($res_date))->format('H:i'); ?>','<?php echo $count; ?>');">Edit</a>
              <?php } elseif ($rec['status'] == "APPROVED") { ?>
                <a href="javascript:success('<?php echo $rec['res_id']; ?>');">Success</a> /
                <a href="javascript:deleteUser('<?php echo $rec['res_id']; ?>');">Delete</a> /
                <a href="javascript:editBooking('<?php echo $rec['res_id']; ?>','<?php echo addslashes($user_phone); ?>','<?php echo (new DateTime($res_date))->format('Y-m-d'); ?>','<?php echo (new DateTime($res_date))->format('H:i'); ?>','<?php echo $count; ?>');">Edit</a>
              <?php } elseif ($rec['status'] == "SUCCESS" || $rec['status'] == "OUTSOURCE") { ?>
                <a href="javascript:deleteUser('<?php echo $rec['res_id']; ?>');">Delete</a> /
                <a href="javascript:editBooking('<?php echo $rec['res_id']; ?>','<?php echo addslashes($user_phone); ?>','<?php echo (new DateTime($res_date))->format('Y-m-d'); ?>','<?php echo (new DateTime($res_date))->format('H:i'); ?>','<?php echo $count; ?>');">Edit</a>
              <?php } else { ?>
                <a href="javascript:deleteUser('<?php echo $rec['res_id']; ?>');">Delete</a> /
                <a href="javascript:editBooking('<?php echo $rec['res_id']; ?>','<?php echo addslashes($user_phone); ?>','<?php echo (new DateTime($res_date))->format('Y-m-d'); ?>','<?php echo (new DateTime($res_date))->format('H:i'); ?>','<?php echo $count; ?>');">Edit</a>
              <?php } ?>
            <?php } ?>
          </td>
          <?php } ?>
        </tr>
        <?php } ?>
      </table>
    </div>

    <div class="box-footer clearfix">
        <ul class="pagination pagination-sm no-margin pull-right">
            <?php if ($current_page > 1) { ?>
                <li><a href="?v=LIST&page=<?php echo ($current_page - 1); ?>">« Previous</a></li>
            <?php } ?>
            <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                <li class="<?php echo ($i == $current_page) ? 'active' : ''; ?>">
                    <a href="?v=LIST&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php } ?>
            <?php if ($current_page < $total_pages) { ?>
                <li><a href="?v=LIST&page=<?php echo ($current_page + 1); ?>">Next »</a></li>
            <?php } ?>
        </ul>
    </div>
  </div>
</div>

<!-- Modal สำหรับแก้ไข -->
<div class="modal fade" id="editBookingModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form method="POST" action="<?php echo WEB_ROOT; ?>api/process.php?cmd=updateBooking">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Edit Booking</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" name="resId" id="edit_resId">
          <div class="form-group">
            <label>Phone</label>
            <input type="text" class="form-control" name="phone" id="edit_phone">
          </div>
          <div class="form-group">
            <label>Reservation Date</label>
            <input type="date" class="form-control" name="rdate" id="edit_rdate">
          </div>
          <div class="form-group">
            <label>Reservation Time</label>
            <input type="time" class="form-control" name="rtime" id="edit_rtime">
          </div>
          <div class="form-group">
            <label>Spare Number</label>
            <input type="text" class="form-control" name="ucount" id="edit_ucount">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save Changes</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
function approve(resId) {
    if (confirm('Are you sure you want to Approve this booking?')) {
        window.location.href = '<?php echo WEB_ROOT; ?>api/process.php?cmd=regConfirm&action=approve&resId=' + resId;
    }
}

function decline(resId) {
    if (confirm('Are you sure you want to Decline this booking?')) {
        window.location.href = '<?php echo WEB_ROOT; ?>api/process.php?cmd=regConfirm&action=denied&resId=' + resId;
    }
}

function success(resId) {
    if (confirm('Are you sure you want to mark this booking as SUCCESS?')) {
        window.location.href = '<?php echo WEB_ROOT; ?>api/process.php?cmd=regConfirm&action=success&resId=' + resId;
    }
}

function deleteUser(resId) {
    if (confirm('This will remove the reservation from the calendar and delete its revenue data. Are you sure?')) {
        // ลบข้อมูลรายได้ก่อน
        fetch(`../api/process.php?cmd=deleteRevenueByBooking&bookingId=${resId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // จากนั้นลบข้อมูลการจอง
                window.location.href = '<?php echo WEB_ROOT; ?>api/process.php?cmd=delete&resId=' + resId;
            } else {
                alert("เกิดข้อผิดพลาดในการลบข้อมูลรายได้: " + data.message);
            }
        })
        .catch(error => {
            console.error("Fetch Error:", error);
            // หากมีข้อผิดพลาด ก็ยังลบข้อมูลการจองต่อไป
            window.location.href = '<?php echo WEB_ROOT; ?>api/process.php?cmd=delete&resId=' + resId;
        });
    }
}

function editBooking(resId, phone, rdate, rtime, ucount) {
    $('#edit_resId').val(resId);
    $('#edit_phone').val(phone);
    $('#edit_rdate').val(rdate);
    $('#edit_rtime').val(rtime);
    $('#edit_ucount').val(ucount);
    $('#editBookingModal').modal('show');
}
</script>

<style>
.outsource-status {
    background-color: #AA38A6 !important;
    color: white !important;
}
</style>