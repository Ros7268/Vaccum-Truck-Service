<?php
require_once '../library/functions.php';

$type = $_SESSION['calendar_fd_user']['type'] ?? '';
if ($type === 'admin' || $type === 'employee' || $type === 'owner') { // เพิ่ม owner

    // ดึงข้อมูลจำนวนการ Assigned ต่อพนักงานในแต่ละเดือน
    $assignmentStats = calculateMonthlyAssignments();
?>

<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title">Monthly Assignment Statistics</h3>
  </div>
  <div class="box-body">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Month</th>
          <th>Name</th>
          <th>Assignments</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if (!empty($assignmentStats)) {
            foreach ($assignmentStats as $month => $data) {
                foreach ($data as $person => $count) {
                    $name = str_replace(['Driver:', 'Employee:'], '', $person); // ตัด prefix ออก
                    echo "<tr>";
                    echo "<td>$month</td>";
                    echo "<td>$name</td>";
                    echo "<td>$count</td>";
                    echo "</tr>";
                }
            }
        } else {
            echo '<tr><td colspan="3" class="text-center">No data found.</td></tr>';
        }
        ?>
      </tbody>
    </table>
    </div>
    </div>
</div>
<?php
} // ปิด if
?>