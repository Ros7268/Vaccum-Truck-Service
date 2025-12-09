<link href="<?php echo WEB_ROOT; ?>library/spry/textfieldvalidation/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<script src="<?php echo WEB_ROOT; ?>library/spry/textfieldvalidation/SpryValidationTextField.js" type="text/javascript"></script>

<!-- 🔹 Modal for Add Truck -->
<div id="truckModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add Truck</h4>
      </div>
      <form class="form-horizontal" action="<?php echo WEB_ROOT; ?>api/process.php?cmd=addTruck" method="post">
        <div class="modal-body">
          <div class="form-group">
            <label class="col-sm-4 control-label">Truck ID</label>
            <div class="col-sm-8">
              <input type="text" class="form-control" name="truckId" required>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-4 control-label">Model</label>
            <div class="col-sm-8">
              <input type="text" class="form-control" name="model" required>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-4 control-label">Plate Number</label>
            <div class="col-sm-8">
              <input type="text" class="form-control" name="plateNumber" required>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-4 control-label">Capacity (kg)</label>
            <div class="col-sm-8">
              <input type="number" class="form-control" name="capacity" required>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-4 control-label">Status</label>
            <div class="col-sm-8">
              <select name="status" class="form-control">
                <option value="available">Available</option>
                <option value="unavailable">Unavailable</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-info">Add Truck</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>
