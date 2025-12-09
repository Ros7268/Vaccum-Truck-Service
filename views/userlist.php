<?php 

$userSession = $_SESSION['calendar_fd_user'] ?? null;
$type = $userSession['type'] ?? null;
$type = trim(strtolower($type));

$usersData = getUserRecords();
$records = $usersData['data'];
$total_records = $usersData['total_records'];

$per_page = 15;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$total_pages = ceil($total_records / $per_page);

$utype = ($type === 'admin') ? 'on' : '';
?>

<div class="col-md-12">
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">User details</h3>
    </div>
    <div class="box-body">
      <table class="table table-bordered">
        <tr>
          <th>#</th>
          <th>Username</th>
          <th>First Name</th>
          <th>Last Name</th>
          <th>Email</th>
          <th>Phone</th>
          <th>User Role</th>
          <th>Status</th>
          <?php if ($utype == 'on') { ?>
          <th>Action</th>
          <?php } ?>
        </tr>
        <?php
        $idx = ($current_page - 1) * $per_page + 1;
        foreach ($records as $rec) {
            extract($rec);
            $status_class = match ($status) {
                "active" => 'success',
                "lock", "inactive" => 'warning',
                "delete" => 'danger',
                default => 'secondary'
            };
        ?>
        <tr>
          <td><?php echo $idx++; ?></td>
          <td><a href="<?php echo WEB_ROOT; ?>views/?v=USER&ID=<?php echo $user_id; ?>"><?php echo strtoupper($user_name); ?></a></td>
          <td><?php echo htmlspecialchars($first_name); ?></td>
          <td><?php echo htmlspecialchars($last_name); ?></td>
          <td><?php echo $user_email; ?></td>
          <td><?php echo $user_phone; ?></td>
          <td>
            <i class="fa <?php echo $type == 'teacher' ? 'fa-user' : 'fa-users'; ?>" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo strtoupper($type); ?>
          </td>
          <td><span class="label label-<?php echo $status_class; ?>"><?php echo strtoupper($status); ?></span></td>
          <?php if ($utype == 'on') { ?>
          <td>
            <?php if ($status == "active") { ?>
              <a href="javascript:status('<?php echo $user_id ?>', 'inactive');">Inactive</a> /
              <a href="javascript:status('<?php echo $user_id ?>', 'lock');">Account Lock</a> /
              <a href="javascript:status('<?php echo $user_id ?>', 'delete');">Delete</a>
            <?php } else { ?>
              <a href="javascript:status('<?php echo $user_id ?>', 'active');">Active</a>
            <?php } ?>
          </td>
          <?php } ?>
        </tr>
        <?php } ?>
      </table>
    </div>

    <div class="box-footer clearfix">
      <?php if ($utype == 'on') { ?>
        <button type="button" class="btn btn-info" onclick="javascript:createUserForm();" style="display: inline-block;">
            <i class="fa fa-user-plus" aria-hidden="true"></i>&nbsp;Create a new User
        </button>
      <?php } ?>

      <ul class="pagination pagination-sm no-margin pull-right">
        <?php if ($current_page > 1) { ?>
            <li><a href="?v=USERS&page=<?php echo ($current_page - 1); ?>">« Previous</a></li>
        <?php } ?>

        <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
            <li class="<?php echo ($i == $current_page) ? 'active' : ''; ?>">
                <a href="?v=USERS&page=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php } ?>

        <?php if ($current_page < $total_pages) { ?>
            <li><a href="?v=USERS&page=<?php echo ($current_page + 1); ?>">Next »</a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
</div>

<script>
function createUserForm() {
    window.location.href = '<?php echo WEB_ROOT; ?>views/?v=CREATE';
}
function status(userId, status) {
    if (confirm('Are you sure you want to ' + status + ' this user?')) {
        window.location.href = '<?php echo WEB_ROOT; ?>views/process.php?cmd=change&action=' + status + '&userId=' + userId;
    }
}
</script>
