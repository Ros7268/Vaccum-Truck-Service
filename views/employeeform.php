<link href="<?php echo WEB_ROOT; ?>library/spry/textfieldvalidation/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<script src="<?php echo WEB_ROOT; ?>library/spry/textfieldvalidation/SpryValidationTextField.js" type="text/javascript"></script>

<style>
/* สไตล์เพิ่มเติมสำหรับฟอร์ม */
.required-field::after {
  content: "*";
  color: #e74c3c;
  margin-left: 4px;
  font-weight: bold;
}

.field-icon {
  color: #3498db;
  margin-right: 5px;
}

.form-control:focus {
  border-color: #3498db;
  box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
}

.alert-validation {
  display: none;
  background-color: #e74c3c;
  color: white;
  padding: 5px 10px;
  border-radius: 3px;
  margin-top: 5px;
  font-size: 12px;
  animation: fadeIn 0.3s;
}

.alert-validation.show {
  display: inline-block;
}

.alert-validation i {
  background-color: white;
  color: #e74c3c;
  border-radius: 50%;
  width: 16px;
  height: 16px;
  display: inline-block;
  text-align: center;
  line-height: 16px;
  margin-right: 5px;
  font-style: normal;
  font-weight: bold;
}

.input-with-icon {
  position: relative;
}

.input-with-icon i {
  position: absolute;
  right: 10px;
  top: 10px;
  color: #7f8c8d;
}

.btn-info {
  background-color: #3498db;
  border-color: #3498db;
}

.btn-info:hover {
  background-color: #2980b9;
  border-color: #2980b9;
}

.modal-header {
  background-color: #f8f9fa;
  border-bottom: 2px solid #3498db;
}

.modal-footer {
  background-color: #f8f9fa;
  border-top: 1px solid #e9ecef;
}

.form-control.error {
  border-color: #e74c3c;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.file-upload {
  display: flex;
  align-items: center;
}

.file-upload-btn {
  background-color: #3498db;
  color: white;
  padding: 6px 12px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  margin-right: 10px;
}

.file-upload-text {
  color: #7f8c8d;
  font-size: 12px;
}

/* เพิ่มการเปลี่ยนแปลงเมื่อโฮเวอร์บนปุ่ม */
.btn {
  transition: all 0.3s ease;
}

.form-group {
  margin-bottom: 15px;
}

/* เพิ่มเอฟเฟกต์โฟกัสสำหรับฟิลด์อินพุต */
.form-control {
  transition: border-color 0.3s ease, box-shadow 0.3s ease;
}
</style>

<!-- Modal สำหรับเพิ่มพนักงานใหม่ -->
<div class="modal fade" id="employeeModal" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">เพิ่มพนักงานใหม่</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form id="employeeForm" action="<?php echo WEB_ROOT; ?>api/process.php?cmd=addEmployee" method="post" enctype="multipart/form-data">
        <div class="modal-body">
          
      <!-- ข้อมูลทั่วไป -->
      <h5 class="mb-3 text-primary">ข้อมูลพนักงาน</h5>
      <div class="row">
        
        <div class="col-md-6 form-group">
          <label class="required-field">เลขบัตรประชาชน</label>
          <input type="text" class="form-control" id="citizenId" name="citizenId" maxlength="13" required>
          <div id="citizenId-error" class="alert-validation"></div>
        </div>

        <div class="col-md-6 form-group">
          <label class="required-field">ชื่อ</label>
          <input type="text" class="form-control" id="firstName" name="name" required>
        </div>

        <div class="col-md-6 form-group">
          <label class="required-field">นามสกุล</label>
          <input type="text" class="form-control" id="lastName" name="lastname" required>
        </div>

        <div class="col-md-6 form-group">
          <label>เพศ</label>
          <select name="gender" class="form-control">
            <option value="male">ชาย</option>
            <option value="female">หญิง</option>
          </select>
        </div>

        <div class="col-md-6 form-group">
          <label>วันเกิด</label>
          <input type="date" class="form-control" name="birthDate">
        </div>

        <div class="col-md-6 form-group">
          <label class="required-field">อายุ</label>
          <input type="number" class="form-control" name="age" min="1" required>
        </div>

        <div class="col-md-6 form-group">
          <label class="required-field">วันที่เริ่มงาน</label>
          <input type="date" class="form-control" name="startDate" required>
        </div>

        <div class="col-md-6 form-group">
          <label class="required-field">เบอร์โทรศัพท์</label>
          <input type="text" class="form-control" id="phone" name="phone" maxlength="10" required>
          <div id="phone-error" class="alert-validation"></div>
        </div>

        <div class="col-md-6 form-group">
          <label class="required-field">เบอร์โทรฉุกเฉิน</label>
          <input type="text" class="form-control" name="emergency_phone" maxlength="10" required>
        </div>

        <div class="col-md-12 form-group">
          <label class="required-field">ที่อยู่ปัจจุบัน</label>
          <textarea name="address" id="address" class="form-control" required></textarea>
        </div>

        <div class="col-md-12 form-group">
          <label class="required-field">ที่อยู่ภูมิลำเนา</label>
          <textarea name="permanent_address" class="form-control" required></textarea>
        </div>

        <div class="col-md-6 form-group">
          <label>สัญชาติ</label>
          <input type="text" class="form-control" name="nationality">
        </div>

        <div class="col-md-6 form-group">
          <label>ศาสนา</label>
          <input type="text" class="form-control" name="religion">
        </div>

        <div class="col-md-6 form-group">
          <label>ตำแหน่ง</label>
          <select name="position" class="form-control" required>
            <option value="">-- เลือกตำแหน่ง --</option>
            <option value="employee">Employee</option>
            <option value="driver">Driver</option>
          </select>
        </div>


        <div class="col-md-6 form-group">
          <label>สถานะ</label>
          <select name="status" class="form-control">
            <option value="active">ใช้งาน</option>
            <option value="inactive">ไม่ใช้งาน</option>
          </select>
        </div>
      </div>

      <hr>
      <h5 class="mb-3 text-primary">ข้อมูลเข้าระบบ</h5>
      <div class="row">
        <div class="col-md-6 form-group">
          <label class="required-field">Username</label>
          <input type="text" class="form-control" name="username" required>
        </div>
        <div class="col-md-6 form-group">
          <label class="required-field">Password</label>
          <input type="password" class="form-control" name="password" required>
        </div>
        <div class="col-md-6 form-group">
          <label class="required-field">Email</label>
          <input type="email" class="form-control" name="email" required>
        </div>
        <div class="col-md-6 form-group">
          <label class="required-field">User Type</label>
          <select name="type" class="form-control" required>
            <option value="employee">Employee</option>
            <option value="driver">Driver</option>
          </select>
        </div>
        <div class="col-md-12 form-group">
          <label>รูปประจำตัว</label>
          <input type="file" id="profile_pic" name="profile_pic" class="form-control" accept="image/*">
        </div>
      </div>
    </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">เพิ่มพนักงาน</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // เพิ่มไอคอน Font Awesome หากไม่มี
  if (!document.querySelector('link[href*="font-awesome"]')) {
    var link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css';
    document.head.appendChild(link);
  }

  // จัดการกับปุ่มเลือกไฟล์
  document.getElementById('uploadBtn').addEventListener('click', function() {
    document.getElementById('profile_pic').click();
  });

  document.getElementById('profile_pic').addEventListener('change', function() {
    var fileName = this.files[0] ? this.files[0].name : 'ไม่มีไฟล์ที่เลือก';
    document.getElementById('fileName').textContent = fileName;
  });

  // ฟังก์ชันตรวจสอบเฉพาะตัวเลขสำหรับ Citizen ID และ Phone
  var numericInputs = ['citizenId', 'phone'];
  numericInputs.forEach(function(id) {
    document.getElementById(id).addEventListener('input', function(e) {
      this.value = this.value.replace(/[^0-9]/g, '');
    });
  });

  // ตรวจสอบความถูกต้องเมื่อฟิลด์ขาดการโฟกัส
  var requiredFields = ['citizenId', 'firstName', 'lastName', 'phone', 'address'];
  requiredFields.forEach(function(id) {
    var element = document.getElementById(id);
    element.addEventListener('blur', function() {
      validateField(this);
    });

    element.addEventListener('input', function() {
      if (this.classList.contains('error')) {
        validateField(this);
      }
    });
  });

  // ตรวจสอบฟิลด์ก่อนส่งฟอร์ม
  document.getElementById('employeeForm').addEventListener('submit', function(event) {
    var isValid = true;
    
    requiredFields.forEach(function(id) {
      if (!validateField(document.getElementById(id))) {
        isValid = false;
      }
    });

    // เพิ่มการตรวจสอบพิเศษสำหรับ Citizen ID
    var citizenId = document.getElementById('citizenId');
    if (citizenId.value.length !== 13) {
      document.getElementById('citizenId-error').textContent = 'เลขบัตรประชาชนต้องมี 13 หลัก';
      document.getElementById('citizenId-error').classList.add('show');
      citizenId.classList.add('error');
      isValid = false;
    }

    // เพิ่มการตรวจสอบพิเศษสำหรับเบอร์โทรศัพท์
    var phone = document.getElementById('phone');
    if (phone.value.length < 9 || phone.value.length > 10) {
      document.getElementById('phone-error').textContent = 'หมายเลขโทรศัพท์ต้องมี 9-10 หลัก';
      document.getElementById('phone-error').classList.add('show');
      phone.classList.add('error');
      isValid = false;
    }

    if (!isValid) {
      event.preventDefault();
      // เลื่อนไปยังฟิลด์แรกที่มีข้อผิดพลาด
      var firstErrorField = document.querySelector('.form-control.error');
      if (firstErrorField) {
        firstErrorField.focus();
      }
    }
  });

  // ฟังก์ชันตรวจสอบความถูกต้องของฟิลด์
  function validateField(field) {
    var errorElement = document.getElementById(field.id + '-error');
    
    if (!field.value.trim()) {
      field.classList.add('error');
      errorElement.classList.add('show');
      return false;
    } else {
      field.classList.remove('error');
      errorElement.classList.remove('show');
      return true;
    }
  }
});
</script>