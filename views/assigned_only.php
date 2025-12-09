<?php
$showError = false;
if (!isset($_SESSION['user_id'])) {
    $showError = true;
}

$assignedData = getMyAssignedTasks();
$records = $assignedData['data'];
$total_records = $assignedData['total_records'];
$per_page = $assignedData['per_page'];
$current_page = $assignedData['current_page'];
$total_pages = $assignedData['total_pages'];
?>

<div class="col-md-12">
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">My Assigned Tasks</h3>
    </div>

    <div class="box-body">
      <?php if (empty($records)) : ?>
        <div class="alert alert-info">
          คุณยังไม่มีงานที่ได้รับมอบหมายในขณะนี้
        </div>
      <?php else : ?>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>#</th>
              <th>Reservation ID</th>
              <th>User Name</th>
              <th>Date</th>
              <th>Address</th>
              <th>Phone</th>
              <th>Truck Plate Number</th>
              <th>Driver</th>
              <th>Employees</th>
              <th>Assigned By</th>
              <th>Export</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $idx = ($current_page - 1) * $per_page + 1;
            foreach ($records as $rec) {
              extract($rec);
            ?>
            <tr>
              <td><?php echo $idx++; ?></td>
              <td><?php echo htmlspecialchars($res_id); ?></td>
              <td><?php echo htmlspecialchars($user_name); ?></td>
              <td><?php echo htmlspecialchars($res_date); ?></td>
              <td><?php echo htmlspecialchars($address); ?></td>
              <td><?php echo htmlspecialchars($user_phone); ?></td>
              <td><?php echo htmlspecialchars($truck_plate_number); ?></td>
              <td><?php echo htmlspecialchars($driver_name); ?></td>
              <td><?php echo htmlspecialchars($employee_names); ?></td>
              <td><?php echo htmlspecialchars($assigned_by); ?></td>
              <td>
                <button class="btn btn-xs btn-success" onclick="exportTask('<?php echo $res_id; ?>')">
                  <i class="fa fa-file-pdf-o"></i> Export
                </button>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>

    <?php if ($total_pages > 0) : ?>
    <div class="box-footer clearfix">
      <ul class="pagination pagination-sm no-margin pull-right">
        <?php if ($current_page > 1) { ?>
          <li><a href="?v=ASSIGNED_ONLY&page=<?php echo ($current_page - 1); ?>">« หน้าก่อน</a></li>
        <?php } ?>
        <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
          <li class="<?php echo ($i == $current_page) ? 'active' : ''; ?>">
            <a href="?v=ASSIGNED_ONLY&page=<?php echo $i; ?>"><?php echo $i; ?></a>
          </li>
        <?php } ?>
        <?php if ($current_page < $total_pages) { ?>
          <li><a href="?v=ASSIGNED_ONLY&page=<?php echo ($current_page + 1); ?>">หน้าถัดไป »</a></li>
        <?php } ?>
      </ul>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- 🔒 Hidden iframe for print preview -->
<iframe id="printFrame" style="display:none;"></iframe>

<!-- 🧠 Auto-print script -->
<script>
function exportTask(resId) {
  const iframe = document.getElementById('printFrame');
  iframe.src = 'export_pdf_view.php?resId=' + encodeURIComponent(resId); // ❗ ลบ 'views/' ออก

  iframe.onload = function () {
    setTimeout(() => {
      iframe.contentWindow.focus();
      iframe.contentWindow.print();
    }, 400);
  };
}

</script>