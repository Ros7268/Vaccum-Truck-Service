<link href="<?php echo WEB_ROOT; ?>library/spry/textfieldvalidation/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<script src="<?php echo WEB_ROOT; ?>library/spry/textfieldvalidation/SpryValidationTextField.js" type="text/javascript"></script>

<!-- Modal Assign Task -->
<div class="modal fade" id="assignTaskModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        <h4 class="modal-title">Assign New Task</h4>
      </div>
      <div class="modal-body">
        <form id="assignTaskForm" method="post">
          
          <!-- Reservation -->
          <div class="form-group">
            <label>Reservation ID</label>
            <select name="reservationId" class="form-control" required>
              <option value="">-- Select Reservation --</option>
              <?php 
              $resQuery = "SELECT r.reservation_id, r.rdate, u.name as user_name 
                           FROM tbl_reservations r 
                           JOIN tbl_users u ON r.user_id = u.user_id 
                           WHERE r.status='APPROVED' 
                           AND r.reservation_id NOT IN (
                             SELECT DISTINCT reservation_id FROM tbl_assigned_tasks
                           )
                           ORDER BY r.rdate DESC";
              $resResult = dbQuery($resQuery);
              while($row = dbFetchAssoc($resResult)) {
                echo "<option value='{$row['reservation_id']}'>เลขที่การจอง {$row['reservation_id']} - {$row['user_name']} ({$row['rdate']})</option>";

              }
              ?>
            </select>
          </div>


          <!-- Driver -->
          <div class="form-group">
            <label>Select Driver</label>
            <select name="driverId" class="form-control" required>
              <option value="">-- Select Driver --</option>
              <?php 
              $driverQuery = "SELECT employee_id, name, last_name FROM tbl_employees WHERE status='ACTIVE' AND position='driver'";
              $driverResult = dbQuery($driverQuery);
              while($row = dbFetchAssoc($driverResult)) {
                  echo "<option value='{$row['employee_id']}'>{$row['name']} {$row['last_name']}</option>";
              }
              ?>
            </select>
          </div>

          <!-- Employees -->
          <div class="form-group">
            <label>Select Employees</label>
            <div id="employeeFields">
              <div class="input-group">
                <select name="employeeIds[]" class="form-control" required>
                  <option value="">-- Select Employee --</option>
                  <?php 
                  $empQuery = "SELECT employee_id, name, last_name FROM tbl_employees WHERE status='ACTIVE' AND position='driver'";
                  $empResult = dbQuery($empQuery);
                  while($row = dbFetchAssoc($empResult)) {
                      echo "<option value='{$row['employee_id']}'>{$row['name']} {$row['last_name']}</option>";
                  }
                  ?>
                </select>
                <div class="input-group-btn">
                  <button type="button" class="btn btn-success" onclick="addEmployeeField()">
                    <i class="fa fa-plus"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Hidden assigned_by -->
          <input type="hidden" name="assigned_by" value="<?php echo $_SESSION['calendar_fd_user']['name']; ?>">

          <!-- Truck -->
          <div class="form-group">
            <label>Select Truck</label>
            <select name="truckId" class="form-control" required>
              <option value="">-- Select Truck --</option>
              <?php 
              $truckQuery = "SELECT truck_id, plate_number FROM tbl_trucks WHERE status='AVAILABLE'";
              $truckResult = dbQuery($truckQuery);
              while($row = dbFetchAssoc($truckResult)) {
                  echo "<option value='{$row['truck_id']}'>{$row['plate_number']}</option>";
              }
              ?>
            </select>
          </div>

        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" id="submitAssignTask" class="btn btn-primary">Assign Task</button>
      </div>
    </div>
  </div>
</div>

<script>
function addEmployeeField() {
  const employeeField = `
    <div class="input-group" style="margin-top:5px;">
      <select name="employeeIds[]" class="form-control" required>
        <option value="">-- Select Employee --</option>
        <?php 
        $empQuery = "SELECT employee_id, name, last_name FROM tbl_employees WHERE status='ACTIVE'";
        $empResult = dbQuery($empQuery);
        while($row = dbFetchAssoc($empResult)) {
            echo "<option value='{$row['employee_id']}'>{$row['name']} {$row['last_name']}</option>";
        }
        ?>
      </select>
      <div class="input-group-btn">
        <button type="button" class="btn btn-danger" onclick="removeEmployeeField(this)">
          <i class="fa fa-minus"></i>
        </button>
      </div>
    </div>
  `;
  document.getElementById('employeeFields').insertAdjacentHTML('beforeend', employeeField);
}

function removeEmployeeField(button) {
  button.closest('.input-group').remove();
}
</script>
