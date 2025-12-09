<?php 
$truckData = getTruckRecords();
$records = $truckData['data'];
$total_records = $truckData['total'];
$per_page = $truckData['per_page'];
$current_page = $truckData['current_page'];
$total_pages = ceil($total_records / $per_page);
?>

<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title">Truck List</h3>
    <button type="button" class="btn btn-info pull-right" data-toggle="modal" data-target="#truckModal">
      <i class="fa fa-truck"></i>&nbsp;Add Truck
    </button>
  </div>

  <div class="box-body">
    <table class="table table-bordered">
      <tr>
        <th>#</th>
        <th>Truck ID</th>
        <th>Model</th>
        <th>Plate Number</th>
        <th>Capacity (kg)</th>
        <th>Status</th>
        <th>Action</th>
      </tr>

      <?php
      $idx = ($current_page - 1) * $per_page + 1;
      foreach($records as $rec) {
        extract($rec);
        $statusLabel = ($status == 'available') ? 'success' : 'warning';
      ?>
      <tr>
        <td><?php echo $idx++; ?></td>
        <td><?php echo $truck_id; ?></td>
        <td><?php echo $model; ?></td>
        <td><?php echo $plate_number; ?></td>
        <td><?php echo $capacity; ?></td>
        <td><span class="label label-<?php echo $statusLabel; ?>"><?php echo ucfirst($status); ?></span></td>
        <td>
          <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#viewTruckModal<?php echo $truck_id; ?>">View</button>
          <?php if ($status == 'available') { ?>
            <a href="javascript:updateTruckStatus('<?php echo $truck_id; ?>', 'unavailable');" class="btn btn-warning btn-sm">Unavailable</a>
          <?php } else { ?>
            <a href="javascript:updateTruckStatus('<?php echo $truck_id; ?>', 'available');" class="btn btn-success btn-sm">Available</a>
          <?php } ?>
          <a href="javascript:deleteTruck('<?php echo $truck_id; ?>');" class="btn btn-danger btn-sm">Delete</a>
        </td>
      </tr>

      <!-- 🔹 Modal for Viewing Truck Details -->
      <div class="modal fade" id="viewTruckModal<?php echo $truck_id; ?>" tabindex="-1" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Truck Details</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              <p><strong>Truck ID:</strong> <?php echo $truck_id; ?></p>
              <p><strong>Model:</strong> <?php echo $model; ?></p>
              <p><strong>Plate Number:</strong> <?php echo $plate_number; ?></p>
              <p><strong>Capacity:</strong> <?php echo $capacity; ?> kg</p>
              <p><strong>Status:</strong> <span class="label label-<?php echo $statusLabel; ?>"><?php echo ucfirst($status); ?></span></p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>

      <?php } ?>
    </table>
  </div>

  <!-- Pagination -->
  <div class="box-footer clearfix">
    <ul class="pagination pagination-sm no-margin pull-right">
      <?php if ($current_page > 1) { ?>
        <li><a href="?v=TRUCKS&page=<?php echo ($current_page - 1); ?>">« Previous</a></li>
      <?php } ?>

      <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
        <li class="<?php echo ($i == $current_page) ? 'active' : ''; ?>">
          <a href="?v=TRUCKS&page=<?php echo $i; ?>"><?php echo $i; ?></a>
        </li>
      <?php } ?>

      <?php if ($current_page < $total_pages) { ?>
        <li><a href="?v=TRUCKS&page=<?php echo ($current_page + 1); ?>">Next »</a></li>
      <?php } ?>
    </ul>
  </div>
</div>

<script>
function updateTruckStatus(truckId, status) {
  if (confirm('Are you sure you want to change status to ' + status + '?')) {
    window.location.href = '<?php echo WEB_ROOT; ?>api/process.php?cmd=updateTruckStatus&truckId=' + truckId + '&status=' + status;
  }
}

function deleteTruck(truckId) {
  if (confirm('Are you sure you want to delete this truck?')) {
    window.location.href = '<?php echo WEB_ROOT; ?>api/process.php?cmd=deleteTruck&truckId=' + truckId;
  }
}
</script>

<!-- Modal for Add Truck -->
<?php include('truckform.php'); ?>