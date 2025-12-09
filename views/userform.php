<div class="col-md-8">

<link href="<?php echo WEB_ROOT; ?>library/spry/textfieldvalidation/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<script src="<?php echo WEB_ROOT; ?>library/spry/textfieldvalidation/SpryValidationTextField.js" type="text/javascript"></script>

<link href="<?php echo WEB_ROOT; ?>library/spry/textareavalidation/SpryValidationTextarea.css" rel="stylesheet" type="text/css" />
<script src="<?php echo WEB_ROOT; ?>library/spry/textareavalidation/SpryValidationTextarea.js" type="text/javascript"></script>

<link href="<?php echo WEB_ROOT; ?>library/spry/selectvalidation/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
<script src="<?php echo WEB_ROOT; ?>library/spry/selectvalidation/SpryValidationSelect.js" type="text/javascript"></script>

<div class="box box-primary">
  <div class="box-header with-border">
    <h3 class="box-title"><b>User Registration</b></h3>
  </div>

  <form role="form" action="<?php echo WEB_ROOT; ?>views/process.php?cmd=create" method="post">
    <div class="box-body">

      <div class="form-group">
        <label for="first_name">First Name</label>
        <span id="sprytf_first_name">
          <input type="text" name="first_name" class="form-control input-sm" placeholder="First Name">
          <span class="textfieldRequiredMsg">First name is required.</span>
          <span class="textfieldMinCharsMsg">First name must be at least 2 characters.</span>
        </span>
      </div>

      <div class="form-group">
        <label for="last_name">Last Name</label>
        <span id="sprytf_last_name">
          <input type="text" name="last_name" class="form-control input-sm" placeholder="Last Name">
          <span class="textfieldRequiredMsg">Last name is required.</span>
          <span class="textfieldMinCharsMsg">Last name must be at least 2 characters.</span>
        </span>
      </div>

      <div class="form-group">
        <label for="name">Username</label>
        <span id="sprytf_name">
          <input type="text" name="name" class="form-control input-sm" placeholder="Username">
          <span class="textfieldRequiredMsg">Username is required.</span>
          <span class="textfieldMinCharsMsg">Username must be at least 6 characters.</span>
        </span>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <span id="sprytf_password">
          <input type="password" name="password" class="form-control input-sm" placeholder="Enter password">
          <span class="textfieldRequiredMsg">Password is required.</span>
          <span class="textfieldMinCharsMsg">Password must be at least 6 characters.</span>
        </span>
      </div>

      <div class="form-group">
        <label for="confirm_password">Confirm Password</label>
        <span id="sprytf_confirm_password">
          <input type="password" name="confirm_password" class="form-control input-sm" placeholder="Confirm password">
          <span class="textfieldRequiredMsg">Confirm password is required.</span>
          <span class="textfieldInvalidFormatMsg">Passwords do not match.</span>
        </span>
      </div>

      <div class="form-group">
        <label for="address">Address</label>
        <span id="sprytf_address">
          <textarea name="address" class="form-control input-sm" placeholder="Address"></textarea>
          <span class="textareaRequiredMsg">Address is required.</span>
          <span class="textareaMinCharsMsg">Address must specify at least 10 characters.</span>
        </span>
      </div>

      <div class="form-group">
        <label for="phone">Phone</label>
        <span id="sprytf_phone">
          <input type="text" name="phone" class="form-control input-sm" placeholder="Phone number" maxlength="10" onkeypress="return isNumberKey(event)">
          <span class="textfieldRequiredMsg">Phone number is required.</span>
        </span>
      </div>

      <div class="form-group">
        <label for="email">Email address</label>
        <span id="sprytf_email">
          <input type="text" name="email" class="form-control input-sm" placeholder="Enter email">
          <span class="textfieldRequiredMsg">Email is required.</span>
          <span class="textfieldInvalidFormatMsg">Please enter a valid email (user@domain.com).</span>
        </span>
      </div>

      <div class="form-group">
        <label for="type">User Type</label>
        <span id="sprytf_type">
          <select name="type" class="form-control input-sm">
            <option value=""> -- Select User Type --</option>
            <option value="admin">Admin</option>
            <option value="owner">Owner</option>
            <option value="user">User</option>
          </select>
          <span class="selectRequiredMsg">Please select User Type.</span>
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
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}

var sprytf_first_name = new Spry.Widget.ValidationTextField("sprytf_first_name", 'none', {minChars:2, validateOn:["blur", "change"]});
var sprytf_last_name = new Spry.Widget.ValidationTextField("sprytf_last_name", 'none', {minChars:2, validateOn:["blur", "change"]});
var sprytf_name = new Spry.Widget.ValidationTextField("sprytf_name", 'none', {minChars:6, validateOn:["blur", "change"]});
var sprytf_password = new Spry.Widget.ValidationTextField("sprytf_password", 'none', {minChars:6, validateOn:["blur", "change"]});
var sprytf_confirm_password = new Spry.Widget.ValidationTextField("sprytf_confirm_password", 'none', {minChars:6, validateOn:["blur", "change"]});
var sprytf_address = new Spry.Widget.ValidationTextarea("sprytf_address", {minChars:10, isRequired:true, validateOn:["blur", "change"]});
var sprytf_phone = new Spry.Widget.ValidationTextField("sprytf_phone", 'integer', {validateOn:["blur", "change"]});
var sprytf_email = new Spry.Widget.ValidationTextField("sprytf_email", 'email', {validateOn:["blur", "change"]});
var sprytf_type = new Spry.Widget.ValidationSelect("sprytf_type");

document.querySelector("form").addEventListener("submit", function(event) {
    var password = document.querySelector("input[name='password']").value;
    var confirmPassword = document.querySelector("input[name='confirm_password']").value;
    var userType = document.querySelector("select[name='type']").value;

    if (password !== confirmPassword) {
        alert("Passwords do not match!");
        event.preventDefault();
    }

    if (userType === "") {
        alert("Please select a user type.");
        event.preventDefault();
    }
});
</script>

</div>