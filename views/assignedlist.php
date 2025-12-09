<?php 
$type = $_SESSION['calendar_fd_user']['type'] ?? '';
if ($type !== 'admin' && $type !== 'employee' && $type !== 'owner') {
    // ถ้าไม่ใช่ admin, employee หรือ owner จะไม่แสดงข้อมูลหรือเปลี่ยนเส้นทาง
    header('Location: ' . WEB_ROOT . 'views/?v=DB');
    exit;
}
$assignedData = getAssignedRecords();
$records = $assignedData['data'];
$total_records = $assignedData['total_records'];
$per_page = $assignedData['per_page'];
$current_page = $assignedData['current_page'];
$total_pages = $assignedData['total_pages'];
?>


<div class="box">
    <div class="box-header with-border">
    <h3 class="box-title">Assigned Task List</h3>
    <button id="openAssignModal" type="button" class="btn btn-info pull-right">
        <i class="fa fa-plus"></i>&nbsp;Assign New Task
    </button>
    </div>

  <div class="box-body">
    <table class="table table-bordered">
    <thead>
        <tr>
        <th>#</th>
        <th>Reservation ID</th>
        <th>User Name</th>
        <th>Date</th>
        <th>Address</th>
        <th>Phone</th>
        <th>Driver</th>
        <th>Employees</th>
        <th>Truck Plate Number</th>
        <th>Assigned By</th>
        <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $idx = ($current_page - 1) * $per_page + 1;
        foreach($records as $rec) {
        extract($rec);
        ?>
        <tr>
        <td><?php echo $idx++; ?></td>
        <td><?php echo $res_id; ?></td>
        <td><?php echo $user_name; ?></td>
        <td><?php echo $res_date; ?></td>
        <td><?php echo $address; ?></td>
        <td><?php echo $user_phone; ?></td>
        <td><?php echo !empty($rec['driver_name']) ? $rec['driver_name'] : 'N/A'; ?></td>
        <td><?php echo !empty($employee_names) ? $employee_names : 'No employees assigned'; ?></td>
        <td><?php echo $truck_plate_number; ?></td>
        <td><?php echo $rec['assigned_by']; ?></td>
        <td>
            <a href="javascript:editAssignedTask('<?php echo $res_id; ?>');" class="btn btn-info btn-sm">Edit</a>
            <a href="javascript:deleteAssignedTask('<?php echo $res_id; ?>');" class="btn btn-danger btn-sm">Delete</a>
        </td>
        </tr>
        <?php } ?>
    </tbody>
    </table>
  </div>

  <!-- Alert Message -->
  <div id="alertMessage" class="alert alert-info" style="display:none;">
    <strong>Success!</strong> Task assigned successfully.
  </div>

  <!-- Pagination -->
  <div class="box-footer clearfix">
    <ul class="pagination pagination-sm no-margin pull-right">
      <?php if ($current_page > 1) { ?>
        <li><a href="?v=ASSIGNED&page=<?php echo ($current_page - 1); ?>">« Previous</a></li>
      <?php } ?>

      <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
        <li class="<?php echo ($i == $current_page) ? 'active' : ''; ?>">
          <a href="?v=ASSIGNED&page=<?php echo $i; ?>"><?php echo $i; ?></a>
        </li>
      <?php } ?>

      <?php if ($current_page < $total_pages) { ?>
        <li><a href="?v=ASSIGNED&page=<?php echo ($current_page + 1); ?>">Next »</a></li>
      <?php } ?>
    </ul>
  </div>
</div>

<!-- Include the form for assigning a new task -->
<?php include('assignedform.php'); ?>

<script>
    
$(document).ready(function() {
    console.log("Web Root Path: <?php echo WEB_ROOT; ?>");
    console.log("API URL: <?php echo WEB_ROOT; ?>api/process.php");

    // ปุ่มเปิด Assign Modal
    $('#openAssignModal').on('click', function() {
        resetAssignForm(); // ฟังก์ชันรีเซ็ตฟอร์ม
        $('#assignTaskModal').modal('show');
    });

    // ปุ่ม Submit ฟอร์ม
    $('#submitAssignTask').on('click', function() {
        submitAssignTaskForm();
    });
});

// ฟังก์ชันรีเซ็ตฟอร์มเมื่อเปิด Assign ใหม่
function resetAssignForm() {
    $('#assignTaskForm')[0].reset();
    $('.modal-title').text('Assign New Task');
    $('#submitAssignTask').text('Assign Task');

    // เปลี่ยนกลับจาก input hidden เป็น select
    if ($('input[name="reservationId"]').length) {
        $('input[name="reservationId"]').replaceWith('<select name="reservationId" class="form-control"><option value="">-- Select Reservation --</option></select>');
        updateReservationDropdown();
    }

    // ลบ employee fields ที่เกิน 1
    $('#employeeFields').find('.input-group:not(:first)').remove();
}

// ส่งข้อมูลการมอบหมาย
function submitAssignTaskForm() {
    var formData = new FormData($('#assignTaskForm')[0]);

    $.ajax({
        url: '<?php echo WEB_ROOT; ?>api/process.php?cmd=assignTask',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(response) {
            $('#assignTaskModal').modal('hide');
            if (response.success) {
                $('#alertMessage').text(response.message)
                    .removeClass('alert-danger')
                    .addClass('alert-info')
                    .show();

                $('#assignTaskForm')[0].reset();
                updateAssignedTable(1);
                updateReservationDropdown();
            } else {
                $('#alertMessage').text(response.message || 'เกิดข้อผิดพลาดในการมอบหมายงาน')
                    .removeClass('alert-info')
                    .addClass('alert-danger')
                    .show();
            }
            setTimeout(() => $('#alertMessage').fadeOut(), 5000);
        },
        error: function(xhr, status, error) {
            $('#alertMessage').text('เกิดข้อผิดพลาดจากเซิร์ฟเวอร์: ' + error)
                .removeClass('alert-info')
                .addClass('alert-danger')
                .show();
        }
    });
}

// อัปเดตดร็อปดาวน์ reservation
function updateReservationDropdown() {
    $.ajax({
        url: '<?php echo WEB_ROOT; ?>api/process.php?cmd=getAvailableReservations',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var dropdown = $('select[name="reservationId"]');
                dropdown.empty().append('<option value="">-- Select Reservation --</option>');
                $.each(response.reservations, function(index, res) {
                    dropdown.append(`<option value="${res.reservation_id}">${res.reservation_id} - ${res.user_name} (${res.rdate})</option>`);
                });
            }
        }
    });
}

// อัปเดตตารางงานที่มอบหมาย
function updateAssignedTable(page = 1) {
    $.ajax({
        url: '<?php echo WEB_ROOT; ?>api/process.php?cmd=getAssignedRecords&page=' + page,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var tableBody = $('table.table tbody');
                tableBody.empty();
                var idx = ((response.current_page - 1) * response.per_page) + 1;

                if (response.records.length > 0) {
                    response.records.forEach(function(rec) {
                        var rowHtml = `
                            <tr>
                                <td>${idx++}</td>
                                <td>${rec.res_id}</td>
                                <td>${rec.user_name}</td>
                                <td>${rec.res_date}</td>
                                <td>${rec.address}</td>
                                <td>${rec.user_phone}</td>
                                <td>${rec.driver_name || 'N/A'}</td>
                                <td>${rec.employee_names || 'No employees assigned'}</td>
                                <td>${rec.truck_plate_number}</td>
                                <td>${rec.assigned_by}</td>
                                <td>
                                    <a href="javascript:editAssignedTask('${rec.res_id}');" class="btn btn-info btn-sm">Edit</a>
                                    <a href="javascript:deleteAssignedTask('${rec.res_id}');" class="btn btn-danger btn-sm">Delete</a>
                                </td>
                            </tr>`;
                        tableBody.append(rowHtml);
                    });
                } else {
                    tableBody.append(`<tr><td colspan="11" class="text-center">No assigned tasks found</td></tr>`);
                }

                updatePagination(response.current_page, response.total_pages);
            }
        }
    });
}


// อัปเดต pagination
function updatePagination(currentPage, totalPages) {
    var html = '';
    if (currentPage > 1) {
        html += `<li><a href="javascript:goToPage(${currentPage - 1});">« Previous</a></li>`;
    }
    for (var i = 1; i <= totalPages; i++) {
        html += `<li class="${i == currentPage ? 'active' : ''}">
                    <a href="javascript:goToPage(${i});">${i}</a>
                </li>`;
    }
    if (currentPage < totalPages) {
        html += `<li><a href="javascript:goToPage(${currentPage + 1});">Next »</a></li>`;
    }
    $('.box-title:contains("Assigned Task List")').closest('.box').find('.pagination').html(html);
}

// ไปยังหน้าที่เลือก
function goToPage(page) {
    $.ajax({
        url: '<?php echo WEB_ROOT; ?>api/process.php?cmd=getAssignedRecords&page=' + page,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                updateAssignedTable(page);
                updatePagination(response.current_page, response.total_pages);
            }
        }
    });
}


// ลบการมอบหมาย
function deleteAssignedTask(resId) {
    if (confirm('คุณแน่ใจหรือไม่ที่จะลบการมอบหมายงานนี้?')) {
        $.ajax({
            url: '<?php echo WEB_ROOT; ?>api/process.php?cmd=deleteAssignedTask&resId=' + resId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#alertMessage').text(response.message)
                        .removeClass('alert-danger')
                        .addClass('alert-info')
                        .show();

                    updateAssignedTable(1);
                    updateReservationDropdown();
                    setTimeout(() => $('#alertMessage').fadeOut(), 5000);
                } else {
                    $('#alertMessage').text(response.message || 'เกิดข้อผิดพลาดในการลบ')
                        .removeClass('alert-info')
                        .addClass('alert-danger')
                        .show();
                }
            },
            error: function(xhr, status, error) {
                $('#alertMessage').text('เกิดข้อผิดพลาดจากเซิร์ฟเวอร์: ' + error)
                    .removeClass('alert-info')
                    .addClass('alert-danger')
                    .show();
            }
        });
    }
}

// แก้ไขการมอบหมาย
function editAssignedTask(resId) {
    $('#alertMessage').text('กำลังโหลดข้อมูล...').removeClass('alert-danger').addClass('alert-info').show();
    var apiUrl = '<?php echo WEB_ROOT; ?>api/process.php?cmd=getAssignedTaskDetail&resId=' + resId;


    $.ajax({
        url: apiUrl,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            $('#alertMessage').hide();
            if (response.success && response.data) {
                $('#assignTaskForm')[0].reset();

                // แทนที่ dropdown ด้วย input hidden + label
                var resHtml = `<input type="hidden" name="reservationId" value="${response.data.reservation_id}">
                               <strong>${response.data.reservation_id}</strong>`;
                $('select[name="reservationId"]').replaceWith(resHtml);
                $('select[name="driverId"]').val(response.data.driver_id);
                $('select[name="truckId"]').val(response.data.truck_id);
                $('#employeeFields').find('.input-group:not(:first)').remove();

                if (response.data.employees.length > 0) {
                    $('select[name="employeeIds[]"]').first().val(response.data.employees[0].employee_id);
                    for (let i = 1; i < response.data.employees.length; i++) {
                        addEmployeeField();
                        $('select[name="employeeIds[]"]').last().val(response.data.employees[i].employee_id);
                    }
                }

                $('.modal-title').text('Edit Assigned Task');
                $('#submitAssignTask').text('Update Task');
                $('#assignTaskModal').modal('show');
            } else {
                $('#alertMessage').text(response.message || 'เกิดข้อผิดพลาดในการโหลดข้อมูล')
                    .removeClass('alert-info')
                    .addClass('alert-danger')
                    .show();
            }
        },
        error: function(xhr, status, error) {
            $('#alertMessage').html(`เกิดข้อผิดพลาดจากเซิร์ฟเวอร์: ${error}<br>สถานะ: ${xhr.status}<br>${xhr.responseText.substring(0,100)}...`)
                .removeClass('alert-info')
                .addClass('alert-danger')
                .show();
        }
    });
}

function resetAssignForm() {
    $('#assignTaskForm')[0].reset();
    $('.modal-title').text('Assign New Task');
    $('#submitAssignTask').text('Assign Task');

    // เปลี่ยนจาก input+strong กลับเป็น select
    if ($('input[name="reservationId"]').length) {
        $('input[name="reservationId"]').next('strong').remove(); // ลบ <strong>
        $('input[name="reservationId"]').replaceWith(`
            <select name="reservationId" class="form-control">
                <option value="">-- Select Reservation --</option>
            </select>
        `);
        updateReservationDropdown();
    }

    // ลบ employee fields ที่เกิน 1
    $('#employeeFields').find('.input-group:not(:first)').remove();
}


$('#openAssignModal').on('click', function() {
    resetAssignForm();
    $('#assignTaskModal').modal('show');
});


</script>
