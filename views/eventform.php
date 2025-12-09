<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userName = $_SESSION['calendar_fd_user']['name'];
$userId = $_SESSION['calendar_fd_user']['user_id'];
$userEmail = $_SESSION['calendar_fd_user']['email'];
$userAddress = $_SESSION['calendar_fd_user']['address'];
$userPhone = $_SESSION['calendar_fd_user']['phone'];
?>

<link href="<?php echo WEB_ROOT; ?>library/spry/textfieldvalidation/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<script src="<?php echo WEB_ROOT; ?>library/spry/textfieldvalidation/SpryValidationTextField.js" type="text/javascript"></script>

<link href="<?php echo WEB_ROOT; ?>library/spry/textareavalidation/SpryValidationTextarea.css" rel="stylesheet" type="text/css" />
<script src="<?php echo WEB_ROOT; ?>library/spry/textareavalidation/SpryValidationTextarea.js" type="text/javascript"></script>

<link href="<?php echo WEB_ROOT; ?>library/spry/selectvalidation/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
<script src="<?php echo WEB_ROOT; ?>library/spry/selectvalidation/SpryValidationSelect.js" type="text/javascript"></script>

<div class="box box-primary">
  <div class="box-header with-border">
    <h3 class="box-title"><b>Book Event</b></h3>
  </div>

  <form role="form" action="<?php echo WEB_ROOT; ?>api/process.php?cmd=book" method="post">
    <div class="box-body">
      
      <div class="form-group">
        <label>Name</label>
        <input type="hidden" name="userId" value="<?php echo $userId; ?>" />
        <input type="text" class="form-control input-sm" name="name" value="<?php echo htmlspecialchars($userName); ?>" readonly>
      </div>
      
      <div class="form-group">
        <label>Address</label>
        <span id="sprytf_address">
          <textarea name="address" class="form-control input-sm" placeholder="Address" id="address"><?php echo htmlspecialchars($userAddress); ?></textarea>
        </span>
      </div>

      <div class="form-group">
        <label>Phone</label>
        <span id="sprytf_phone">
          <input type="text" name="phone" class="form-control input-sm" placeholder="Phone number" id="phone" value="<?php echo htmlspecialchars($userPhone); ?>" maxlength="10" onkeypress="return isNumberKey(event)">
        </span>
      </div>

      <div class="form-group">
        <label>Email address</label>
        <span id="sprytf_email">
          <input type="text" name="email" class="form-control input-sm" placeholder="Enter email" id="email" value="<?php echo htmlspecialchars($userEmail); ?>" readonly>
        </span>
      </div>

      <div class="form-group">
        <div class="row">
          <div class="col-xs-6">
            <label>Reservation Date</label>
            <input type="date" name="rdate" class="form-control" required>
          </div>
          <div class="col-xs-6">
            <label>Reservation Time</label>
            <input type="text" name="rtime" id="rtime" class="form-control" placeholder="เช่น 09:00" maxlength="5" required>
          </div>
        </div>
      </div>

      <div class="form-group">
        <label>Service Type</label>
        <span id="sprytf_service">
          <select name="service_type" class="form-control input-sm" required>
            <option value="">-- Select Service --</option>
            <option value="ดูดปฏิกูล">ดูดปฏิกูล</option>
            <option value="ดูดตะกอน">ดูดตะกอน</option>
            <option value="ดูดบ่อดักไขมัน">ดูดบ่อดักไขมัน</option>
          </select>
        </span>
      </div>

      <div class="form-group">
        <label>Spare Number</label>
        <span id="sprytf_ucount">
          <input type="text" name="ucount" class="form-control input-sm" placeholder="Spare Number" maxlength="10" onkeypress="return isNumberKey(event)">
        </span>
      </div>

    </div>

    <div class="box-footer">
      <button type="submit" class="btn btn-primary">Submit</button>
    </div>
  </form>
</div>

<script type="text/javascript">
function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    return !(charCode > 31 && (charCode < 48 || charCode > 57));
}

var sprytf_address = new Spry.Widget.ValidationTextarea("sprytf_address", {minChars:6, isRequired:true, validateOn:["blur", "change"]});
var sprytf_phone = new Spry.Widget.ValidationTextField("sprytf_phone", "integer", {validateOn:["blur", "change"], isRequired: false});
var sprytf_ucount = new Spry.Widget.ValidationTextField("sprytf_ucount", "integer", {validateOn:["blur", "change"], isRequired: false});
var sprytf_service = new Spry.Widget.ValidationSelect("sprytf_service", {validateOn:["blur", "change"], isRequired: true});

document.querySelector('form').addEventListener('submit', function(event) {
    var spareNumber = document.querySelector('[name="ucount"]').value;
    if (spareNumber && spareNumber.length === 1) {
        document.querySelector('[name="ucount"]').value = '0' + spareNumber;
    }
});

document.getElementById('rtime').addEventListener('input', function (e) {
    var val = e.target.value.replace(/[^0-9]/g, '');
    if (val.length > 2) {
        val = val.slice(0, 2) + ':' + val.slice(2, 4);
    } else if (val.length === 2) {
        val += ':';
    }
    e.target.value = val;
});


</script>