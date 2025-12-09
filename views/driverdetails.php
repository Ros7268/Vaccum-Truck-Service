<?php
require_once '../library/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentUserType = $_SESSION['calendar_fd_user']['type'] ?? null;
$currentUserId = $_SESSION['calendar_fd_user']['user_id'] ?? null;

$assignedWorksWithUser = getAssignedWorksWithUser();

if ($currentUserType === 'user' && $currentUserId) {
    $assignedWorksWithUser = array_filter($assignedWorksWithUser, function ($work) use ($currentUserId) {
        return isset($work['user_id']) && $work['user_id'] == $currentUserId;
    });
}

if ($currentUserType === 'user') {
?>
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title">Driver & Employee Details</h3>
  </div>
  <div class="box-body">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>#</th>
          <th>Booking ID</th>
          <th>User Name</th>
          <th>Date</th>
          <th>Driver Name</th>
          <th>Driver Phone</th>
          <th>Employee Names</th>
          <th>Employee Phones</th>
          <th>Truck Plate Number</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $idx = 1;
        if (!empty($assignedWorksWithUser)) {
            foreach ($assignedWorksWithUser as $work) {
                ?>
                <tr>
                  <td><?php echo $idx++; ?></td>
                  <td><?php echo htmlspecialchars($work['reservation_id'] ?? '-'); ?></td>
                  <td><?php echo htmlspecialchars($work['user_name'] ?? '-'); ?></td>
                  <td><?php echo htmlspecialchars($work['date'] ?? '-'); ?></td>
                  <td><?php echo htmlspecialchars($work['driver_name'] ?? '-'); ?></td>
                  <td><?php echo htmlspecialchars($work['driver_phone'] ?? '-'); ?></td>
                  <td><?php echo htmlspecialchars($work['employee_names'] ?? '-'); ?></td>
                  <td><?php echo htmlspecialchars($work['employee_phones'] ?? '-'); ?></td>
                  <td><?php echo htmlspecialchars($work['truck_plate_number'] ?? '-'); ?></td>
                </tr>
                <?php
            }
        } else {
            ?>
            <tr>
              <td colspan="9" class="text-center">Please wait for assignment</td>
            </tr>
            <?php
        }
        ?>
      </tbody>
    </table>
  </div>
</div>
<?php
}
?>
