<?php 
$employeeData = getEmployeeRecords();
$records = $employeeData['data'];
$total_records = $employeeData['total'];
$per_page = $employeeData['per_page'];
$current_page = $employeeData['current_page'];
$total_pages = ceil($total_records / $per_page);
?>

<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title">Employee List</h3>
    <button type="button" class="btn btn-info pull-right" data-toggle="modal" data-target="#employeeModal">
      <i class="fa fa-user-plus"></i>&nbsp;Add Employee
    </button>
  </div>

  <div class="box-body">
    <table class="table table-bordered">
      <tr>
        <th>#</th>
        <th>Employee Code</th>
        <th>Name</th>
        <th>Last Name</th>
        <th>Phone</th>
        <th>Address</th>
        <th>Position</th>
        <th>Status</th>
        <th>Action</th>
      </tr>

      <?php
      $idx = ($current_page - 1) * $per_page + 1;
      foreach ($records as $rec) {
        extract($rec);
        $statusLabel = ($status == 'active') ? 'success' : 'warning';
      ?>
      <tr>
        <td><?php echo $idx++; ?></td>
        <td><?php echo $employee_code; ?></td>
        <td><?php echo $name; ?></td>
        <td><?php echo $last_name; ?></td>
        <td><?php echo $phone; ?></td>
        <td><?php echo $address; ?></td>
        <td><?php echo $position; ?></td>
        <td><span class="label label-<?php echo $statusLabel; ?>"><?php echo ucfirst($status); ?></span></td>
        <td>
          <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#editEmployeeModal<?php echo $employee_id; ?>">Edit</button>
          <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#viewEmployeeModal<?php echo $employee_id; ?>">View</button>
          <?php if ($status == 'active') { ?>
            <a href="javascript:updateStatus('<?php echo $employee_id; ?>', 'inactive');" class="btn btn-warning btn-sm">Inactive</a>
          <?php } else { ?>
            <a href="javascript:updateStatus('<?php echo $employee_id; ?>', 'active');" class="btn btn-success btn-sm">Active</a>
          <?php } ?>
          <a href="javascript:deleteEmployee('<?php echo $employee_id; ?>');" class="btn btn-danger btn-sm">Delete</a>
        </td>
      </tr>

      <!-- Modal View -->
      <div class="modal fade" id="viewEmployeeModal<?php echo $employee_id; ?>" tabindex="-1" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Employee Details</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              <?php if (!empty($profile_pic)) { ?>
                <div class="text-center">
                  <img src="../uploads/employees/<?php echo $profile_pic; ?>" alt="Profile Picture" width="100" class="img-thumbnail">
                </div>
              <?php } ?>
              <p><strong>Employee Code:</strong> <?php echo $employee_code; ?></p>
              <p><strong>Citizen ID:</strong> <?php echo $citizen_id; ?></p>
              <p><strong>Name:</strong> <?php echo $name . ' ' . $last_name; ?></p>
              <p><strong>Gender:</strong> <?php echo ucfirst($gender); ?></p>
              <p><strong>Birth Date:</strong> <?php echo $birth_date; ?></p>
              <p><strong>Age:</strong> <?php echo $age; ?></p>
              <p><strong>Start Date:</strong> <?php echo $start_date; ?></p>
              <p><strong>Phone:</strong> <?php echo $phone; ?></p>
              <p><strong>Emergency Phone:</strong> <?php echo $emergency_phone; ?></p> 
              <p><strong>Address:</strong> <?php echo $address; ?></p>
              <p><strong>Permanent Address:</strong> <?php echo $permanent_address; ?></p> 
              <p><strong>Nationality:</strong> <?php echo $nationality; ?></p>
              <p><strong>Religion:</strong> <?php echo $religion; ?></p>
              <p><strong>Position:</strong> <?php echo $position; ?></p>
              <p><strong>Status:</strong> <span class="label label-<?php echo $statusLabel; ?>"><?php echo ucfirst($status); ?></span></p>
            </div>

          </div>
        </div>
      </div>

      <!-- Modal Edit -->
      <div class="modal fade" id="editEmployeeModal<?php echo $employee_id; ?>" tabindex="-1" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Edit Employee</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?php echo WEB_ROOT; ?>api/process.php?cmd=updateEmployee" method="post" enctype="multipart/form-data">
              <div class="modal-body">
                <input type="hidden" name="employeeId" value="<?php echo $employee_id; ?>">

                <div class="form-group">
                  <label>Citizen ID</label>
                  <input type="text" class="form-control" name="citizenId" value="<?php echo $citizen_id; ?>" readonly>
                </div>

                <div class="form-group">
                  <label>Name</label>
                  <input type="text" class="form-control" name="name" value="<?php echo $name; ?>" required>
                </div>

                <div class="form-group">
                  <label>Last Name</label>
                  <input type="text" class="form-control" name="lastname" value="<?php echo $last_name; ?>" required>
                </div>

                <div class="form-group">
                  <label>Gender</label>
                  <select name="gender" class="form-control" required>
                    <option value="male" <?php echo ($gender == 'male') ? 'selected' : ''; ?>>Male</option>
                    <option value="female" <?php echo ($gender == 'female') ? 'selected' : ''; ?>>Female</option>
                    <option value="other" <?php echo ($gender == 'other') ? 'selected' : ''; ?>>Other</option>
                  </select>
                </div>

                <div class="form-group">
                  <label>Birth Date</label>
                  <input type="date" class="form-control" name="birthDate" value="<?php echo $birth_date; ?>">
                </div>

                <div class="form-group">
                  <label>Age</label>
                  <input type="number" class="form-control" name="age" value="<?php echo $age; ?>" min="0" max="100" required>
                </div>

                <div class="form-group">
                  <label>Start Date</label>
                  <input type="date" class="form-control" name="startDate" value="<?php echo $start_date; ?>">
                </div>

                <div class="form-group">
                  <label>Phone</label>
                  <input type="text" class="form-control" name="phone" value="<?php echo $phone; ?>" required>
                </div>

                <div class="form-group">
                  <label>Emergency Phone</label>
                  <input type="text" class="form-control" name="emergency_phone" value="<?php echo $emergency_phone; ?>" required>
                </div>

                <div class="form-group">
                  <label>Address</label>
                  <textarea name="address" class="form-control" required><?php echo $address; ?></textarea>
                </div>

                <div class="form-group">
                  <label>Permanent Address</label>
                  <textarea name="permanent_address" class="form-control" required><?php echo $permanent_address; ?></textarea>
                </div>

                <div class="form-group">
                  <label>Nationality</label>
                  <input type="text" class="form-control" name="nationality" value="<?php echo $nationality; ?>">
                </div>

                <div class="form-group">
                  <label>Religion</label>
                  <input type="text" class="form-control" name="religion" value="<?php echo $religion; ?>">
                </div>

                <div class="form-group">
                  <label>Position</label>
                  <input type="text" class="form-control" name="position" value="<?php echo $position; ?>" required>
                </div>

                <div class="form-group">
                  <label>Status</label>
                  <select name="status" class="form-control" required>
                    <option value="active" <?php echo ($status == 'active') ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo ($status == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                  </select>
                </div>

                <div class="form-group">
                  <label>Profile Picture</label>
                  <input type="file" name="profile_pic" class="form-control" accept="image/*">
                </div>

              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-success">Update</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <?php } ?>
    </table>

    <ul class="pagination pagination-sm no-margin pull-right">
      <?php if ($current_page > 1) { ?>
        <li><a href="?v=EMPLOYEES&page=<?php echo ($current_page - 1); ?>">« Previous</a></li>
      <?php } ?>
      <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
        <li class="<?php echo ($i == $current_page) ? 'active' : ''; ?>">
          <a href="?v=EMPLOYEES&page=<?php echo $i; ?>"><?php echo $i; ?></a>
        </li>
      <?php } ?>
      <?php if ($current_page < $total_pages) { ?>
        <li><a href="?v=EMPLOYEES&page=<?php echo ($current_page + 1); ?>">Next »</a></li>
      <?php } ?>
    </ul>
  </div>
</div>

<script>
function updateStatus(employeeId, status) {
  if (confirm('Are you sure you want to change status to ' + status + '?')) {
    window.location.href = '<?php echo WEB_ROOT; ?>api/process.php?cmd=updateEmployeeStatus&employeeId=' + employeeId + '&status=' + status;
  }
}

function deleteEmployee(employeeId) {
  if (confirm('Are you sure you want to delete this employee?')) {
    window.location.href = '<?php echo WEB_ROOT; ?>api/process.php?cmd=deleteEmployee&employeeId=' + employeeId;
  }
}
</script>

<?php include('employeeform.php'); ?>