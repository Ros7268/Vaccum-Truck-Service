<link href="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.min.js"></script>

<?php
require_once '../library/functions.php';


// Get all outsourced bookings
$allOutsourcedBookings = getOutsourcedBookings();

// Pagination settings
$itemsPerPage = 10;
$totalItems = count($allOutsourcedBookings);
$totalPages = ceil($totalItems / $itemsPerPage);

// Get current page from URL parameter, default to 1 if not set
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

// Ensure current page doesn't exceed total pages
$currentPage = min($currentPage, max(1, $totalPages));

// Calculate the starting index for items on the current page
$startIndex = ($currentPage - 1) * $itemsPerPage;

// Get the subset of bookings for the current page
$outsourcedBookings = array_slice($allOutsourcedBookings, $startIndex, $itemsPerPage);

// Get base URL (remove any existing page parameter)
$baseUrl = strtok($_SERVER["REQUEST_URI"], '?');
$queryParams = $_GET;
unset($queryParams['page']);
$queryString = http_build_query($queryParams);
$pageUrl = $baseUrl . ($queryString ? "?$queryString&" : "?");
?>



<div class="box box-primary">
  <div class="box-header with-border">
    <h3 class="box-title"><i class="fa fa-exchange"></i> Outsourced Bookings</h3>
    <button type="button" class="btn btn-info pull-right" data-toggle="modal" data-target="#outsourcingModal">
      <i class="fa fa-plus"></i>&nbsp;Add Outsourced Booking
    </button>
  </div>

  <div class="box-body">
    <!-- Stats Cards -->
    <div class="row mb-4">
      <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
          <span class="info-box-icon bg-aqua"><i class="fa fa-calendar"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Total Bookings</span>
            <span class="info-box-number"><?php echo $totalItems; ?></span>
          </div>
        </div>
      </div>
      
      <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
          <span class="info-box-icon bg-green"><i class="fa fa-money"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Total Value</span>
            <span class="info-box-number">
              <?php 
                $total = array_sum(array_column($allOutsourcedBookings, 'price'));
                echo number_format($total, 2) . " ฿"; 
              ?>
            </span>
          </div>
        </div>
      </div>
      
      <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
          <span class="info-box-icon bg-yellow"><i class="fa fa-clock-o"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Recent Booking</span>
            <span class="info-box-number">
              <?php 
                if ($totalItems > 0) {
                  $dates = array_column($allOutsourcedBookings, 'rdate');
                  rsort($dates);
                  echo (new DateTime($dates[0]))->format('d/m/Y');
                } else {
                  echo "None";
                }
              ?>
            </span>
          </div>
        </div>
      </div>
      
      <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
          <span class="info-box-icon bg-red"><i class="fa fa-image"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">With Images</span>
            <span class="info-box-number">
              <?php 
                $withImages = array_filter($allOutsourcedBookings, function($booking) {
                  return !empty($booking['attachment']);
                });
                echo count($withImages);
              ?>
            </span>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Filter Options -->
    <div class="row mb-3">
      <div class="col-md-12">
        <div class="box box-default collapsed-box">
          <div class="box-header with-border">
            <h3 class="box-title">Filter Options</h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse">
                <i class="fa fa-plus"></i>
              </button>
            </div>
          </div>
          <div class="box-body">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>Date Range:</label>
                  <div class="input-group">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control pull-right" id="daterange">
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label>Price Range:</label>
                  <div class="input-group">
                    <input type="number" class="form-control" placeholder="Min" id="minPrice">
                    <span class="input-group-addon">to</span>
                    <input type="number" class="form-control" placeholder="Max" id="maxPrice">
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label>&nbsp;</label>
                  <button type="button" class="btn btn-primary form-control" id="applyFilter">
                    <i class="fa fa-filter"></i> Apply Filters
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Bookings Table -->
    <div class="table-responsive">
      <table class="table table-hover" id="bookingsTable">
        <thead>
          <tr>
            <th style="width: 40px;" class="text-center">#</th>
            <th>Booking ID</th>
            <th>Date</th>
            <th class="text-right">Price</th>
            <th class="text-center">Image</th>
            <th style="width: 100px;" class="text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
            <?php 
            // Calculate row number for current page
            $rowNumber = $startIndex + 1;
            
            if (count($outsourcedBookings) > 0) {
              foreach ($outsourcedBookings as $booking) { ?>
              <tr data-id="<?php echo $booking['outsourced_booking_id']; ?>"> <!-- ✅ เพิ่ม data-id ตรงนี้ -->
                <td class="text-center"><?php echo $rowNumber++; ?></td>
                <td><?php echo $booking['outsourced_booking_id']; ?></td>
                <td>
                  <?php echo (new DateTime($booking['rdate']))->format('d/m/Y H:i'); ?>
                </td>
                <td class="text-right price">
                  <?php echo number_format($booking['price'], 2) . " ฿"; ?>
                </td>
                <td class="text-center attachment">
                  <?php if (!empty($booking['attachment'])) { ?>
                    <a href="../uploads/outsourcing/<?php echo $booking['attachment']; ?>" data-toggle="lightbox">
                      <img src="../uploads/outsourcing/<?php echo $booking['attachment']; ?>" width="50" height="50" class="img-thumbnail">
                    </a>
                  <?php } else { ?>
                    <span class="text-muted">-</span>
                  <?php } ?>
                </td>
                <td class="text-center">
                  <div class="btn-group">
                    <button class="btn btn-default btn-sm edit-btn" title="Edit" data-id="<?php echo $booking['outsourced_booking_id']; ?>">
                      <i class="fa fa-pencil"></i>
                    </button>
                    <button class="btn btn-default btn-sm delete-btn" title="Delete" data-id="<?php echo $booking['outsourced_booking_id']; ?>">
                      <i class="fa fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            <?php } 
            } else { ?>
              <tr>
                <td colspan="6" class="text-center py-4">
                  <p class="text-muted">No outsourced bookings found.</p>
                </td>
              </tr>
            <?php } ?>
          </tbody>

      </table>
    </div>
    
    <!-- Pagination - Simplified to match the image shown -->
    <?php if ($totalPages > 1): ?>
    <div class="text-center">
      <ul class="pagination">
        <!-- Previous page link -->
        <li <?php echo ($currentPage <= 1) ? 'class="disabled"' : ''; ?>>
          <a href="<?php echo ($currentPage > 1) ? $pageUrl.'page='.($currentPage-1) : '#'; ?>">&laquo;</a>
        </li>
        
        <!-- First page -->
        <li <?php echo ($currentPage == 1) ? 'class="active"' : ''; ?>>
          <a href="<?php echo $pageUrl; ?>page=1">1</a>
        </li>
        
        <!-- Page 2 -->
        <?php if ($totalPages >= 2): ?>
        <li <?php echo ($currentPage == 2) ? 'class="active"' : ''; ?>>
          <a href="<?php echo $pageUrl; ?>page=2">2</a>
        </li>
        <?php endif; ?>
        
        <!-- More pages if needed -->
        <?php if ($totalPages > 2): ?>
        <li <?php echo ($currentPage > 2) ? 'class="active"' : ''; ?>>
          <a href="<?php echo $pageUrl; ?>page=<?php echo ($currentPage > 2) ? $currentPage : '3'; ?>">
            <?php echo ($currentPage > 2) ? $currentPage : '3'; ?>
          </a>
        </li>
        <?php endif; ?>
        
        <!-- Next page link -->
        <li <?php echo ($currentPage >= $totalPages) ? 'class="disabled"' : ''; ?>>
          <a href="<?php echo ($currentPage < $totalPages) ? $pageUrl.'page='.($currentPage+1) : '#'; ?>">&raquo;</a>
        </li>
      </ul>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- Include Outsourcing Form -->
<?php include('outsourcing_form.php'); ?>

<!-- Edit Booking Modal -->
<div class="modal fade" id="editBookingModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title"><i class="fa fa-pencil"></i> Edit Booking</h4>
      </div>
      <div class="modal-body">
        <form id="editBookingForm" enctype="multipart/form-data">
        <input type="hidden" id="edit_outsourced_booking_id" name="reservation_id" value="">
          
          <div class="form-group">
            <label for="edit_price">Price (฿)</label>
            <input type="number" class="form-control" id="edit_price" name="price" step="0.01" required>
          </div>
          
          <div class="form-group">
            <label for="edit_attachment">Attachment</label>
            <div class="input-group">
              <div class="custom-file">
                <input type="file" class="custom-file-input" id="edit_attachment" name="attachment">
                <label class="custom-file-label" for="edit_attachment">Choose file</label>
              </div>
            </div>
            <div id="current_attachment" class="mt-2"></div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="saveEditBooking">Save Changes</button>
      </div>
      <!-- Date Range Picker Dependencies -->
      <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
      <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
      <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
      <!-- Toastr Notification Library -->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
      <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    </div>
  </div>
</div>

<script>

  // ใช้ event delegation เพื่อให้รองรับปุ่มที่โหลดแบบไดนามิก
document.addEventListener("DOMContentLoaded", function() {
    document.body.addEventListener("click", function(event) {
        let target = event.target;

        // กดปุ่มลบ
        if (target.closest(".delete-btn")) {
            let bookingId = target.closest(".delete-btn").dataset.id;
            deleteBooking(bookingId);
        }

        // กดปุ่มแก้ไข
        if (target.closest(".edit-btn")) {
            let bookingId = target.closest(".edit-btn").dataset.id;
            editBooking(bookingId);
        }
    });
});

// ฟังก์ชันลบข้อมูล
// ฟังก์ชันลบข้อมูลโดยไม่ต้องรีเฟรช
function deleteBooking(bookingId) {
    if (confirm("Are you sure you want to delete this record?")) {
        fetch(`../api/process.php?cmd=deleteOutsourcedBooking&bookingId=${bookingId}`, {
            method: 'GET'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toastr.success("Booking deleted successfully!");
                
                // ลบแถวออกจากตารางโดยตรง
                const row = document.querySelector(`button.delete-btn[data-id="${bookingId}"]`).closest('tr');
                $(row).fadeOut(400, function() {
                    row.remove();
                    
                    // อัพเดทเลขแถว
                    const rows = document.querySelectorAll('#bookingsTable tbody tr');
                    rows.forEach((row, index) => {
                        const numCell = row.querySelector('td:first-child');
                        if (numCell) numCell.textContent = index + 1;
                    });
                    
                    // อัพเดทข้อมูลสถิติถ้าต้องการ (ไม่แสดงทั้งหมดเพื่อความเรียบง่าย)
                });
            } else {
                toastr.error("Error: " + data.message);
            }
        })
        .catch(error => {
            console.error("Fetch Error:", error);
            toastr.error("Error processing request.");
        });
    }
}





// ฟังก์ชันโหลดข้อมูลสำหรับแก้ไข
function editBooking(bookingId) {
    fetch(`../api/process.php?cmd=getOutsourcedBookingDetails&bookingId=${bookingId}`)
    .then(response => {
        if (!response.ok) {
            throw new Error('การตอบสนองเครือข่ายมีปัญหา: ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // ลบช่องว่างหลังชื่อ ID
            document.getElementById('edit_outsourced_booking_id').value = data.booking.outsourced_booking_id;
            document.getElementById('edit_price').value = data.booking.price;
            
            let attachmentContainer = document.getElementById('current_attachment');
            if (data.booking.attachment) {
                attachmentContainer.innerHTML = `
                    <div class="mt-2">
                        <p>ไฟล์แนบปัจจุบัน:</p>
                        <img src="../uploads/outsourcing/${data.booking.attachment}" class="img-thumbnail" style="max-height: 100px">
                        <p class="small text-muted">${data.booking.attachment}</p>
                    </div>
                `;
            } else {
                attachmentContainer.innerHTML = `<p class="text-muted">ไม่มีไฟล์แนบ</p>`;
            }

            $('#editBookingModal').modal('show');
        } else {
            console.error("API Error:", data);
            toastr.error("เกิดข้อผิดพลาดในการโหลดรายละเอียดการจอง: " + (data.message || "ข้อผิดพลาดที่ไม่ทราบสาเหตุ"));
        }
    })
    .catch(error => {
        console.error("Fetch Error:", error);
        toastr.error("เกิดข้อผิดพลาดในการโหลดรายละเอียดการจอง: " + error.message);
    });
}

// ฟังก์ชันบันทึกข้อมูลหลังจากแก้ไข
document.getElementById('saveEditBooking').addEventListener('click', function() {
    let formData = new FormData(document.getElementById('editBookingForm'));
    formData.append('cmd', 'updateOutsourcedBooking');
    
    // บันทึกเพื่อการแก้ไขข้อผิดพลาด
    console.log("ข้อมูลที่กำลังส่ง:", Object.fromEntries(formData));
    
    fetch('../api/process.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('การตอบสนองเครือข่ายมีปัญหา: ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            $('#editBookingModal').modal('hide');
            toastr.success("อัปเดตการจองสำเร็จ!");
            
            // อัพเดทข้อมูลในแถวโดยตรงแทนการรีเฟรช
            const bookingId = document.getElementById('edit_outsourced_booking_id').value;
            const row = document.querySelector(`button.edit-btn[data-id="${bookingId}"]`).closest('tr');
            
            // อัพเดทราคา
            const priceCell = row.querySelector('td:nth-child(4)');
            const newPrice = document.getElementById('edit_price').value;
            priceCell.textContent = Number(newPrice).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + " ฿";
            
            // อัพเดทรูปภาพถ้ามีการเปลี่ยนแปลง
            const imageInput = document.getElementById('edit_attachment');
            if (imageInput.files.length > 0) {
                if (data.booking && data.booking.attachment) {
                    const imageCell = row.querySelector('td:nth-child(5)');
                    imageCell.innerHTML = `
                        <a href="../uploads/outsourcing/${data.booking.attachment}" data-toggle="lightbox">
                            <img src="../uploads/outsourcing/${data.booking.attachment}" 
                                width="50" height="50" 
                                class="img-thumbnail">
                        </a>`;
                }
            }
        } else {
            toastr.error("ข้อผิดพลาด: " + (data.message || "ข้อผิดพลาดที่ไม่ทราบสาเหตุ"));
            console.error("API Error:", data);
        }
    })
    .catch(error => {
        console.error("Fetch Error:", error);
        console.log("URL ที่ร้องขอ:", '../api/process.php');
        console.log("ข้อมูลฟอร์ม:", Object.fromEntries(formData));
        toastr.error("เกิดข้อผิดพลาดในการอัปเดตการจอง: " + error.message);
    });
});
// ฟังก์ชันสำหรับดึงข้อมูลและอัพเดทตาราง
function fetchAndUpdateTable() {
    fetch(window.location.href)
    .then(response => response.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        // อัพเดทเฉพาะส่วนของตาราง
        const newTable = doc.getElementById('bookingsTable');
        const currentTable = document.getElementById('bookingsTable');
        if (newTable && currentTable) {
            currentTable.innerHTML = newTable.innerHTML;
        }
        
        // อัพเดท stats cards ด้วย
        const infoDivs = doc.querySelectorAll('.info-box-content');
        const currentInfoDivs = document.querySelectorAll('.info-box-content');
        if (infoDivs.length === currentInfoDivs.length) {
            for (let i = 0; i < infoDivs.length; i++) {
                currentInfoDivs[i].innerHTML = infoDivs[i].innerHTML;
            }
        }
    })
    .catch(error => {
        console.error("Error updating table:", error);
        toastr.warning("Could not refresh data automatically. Please reload the page.");
    });
}





// Edit booking handler
document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', function() {
        let bookingId = this.dataset.id;
        $('#editBookingModal').modal('show');
        
        // Fetch booking details for editing
        fetch(`../api/process.php?cmd=getOutsourcedBookingDetails&bookingId=${bookingId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Fill the form with booking details
                document.getElementById('edit_outsourced_booking_id').value = data.booking.id;
                document.getElementById('edit_price').value = data.booking.price;
                
                // Show current attachment if exists
                let attachmentContainer = document.getElementById('current_attachment');
                if (data.booking.attachment) {
                    attachmentContainer.innerHTML = `
                        <div class="mt-2">
                            <p>Current attachment:</p>
                            <img src="../uploads/outsourcing/${data.booking.attachment}" class="img-thumbnail" style="max-height: 100px">
                            <p class="small text-muted">${data.booking.attachment}</p>
                        </div>
                    `;
                } else {
                    attachmentContainer.innerHTML = `<p class="text-muted">No current attachment</p>`;
                }
            } else {
                toastr.error("Error loading booking details");
            }
        })
        .catch(error => {
            console.error("Fetch Error:", error);
            toastr.error("Error loading booking details");
        });
    });
});


// Initialize datepicker for filter
$(function() {
    $('#daterange').daterangepicker({
        locale: {
            format: 'DD/MM/YYYY'
        }
    });
    
    // Initialize lightbox for images
    $(document).on('click', '[data-toggle="lightbox"]', function(event) {
        event.preventDefault();
        $(this).ekkoLightbox();
    });
    
    // Add filter parameter preservation for pagination
    $('#applyFilter').on('click', function() {
        // Get current filter values
        const dateRange = $('#daterange').val();
        const minPrice = $('#minPrice').val();
        const maxPrice = $('#maxPrice').val();
        
        // Build query string with filters
        let queryParams = new URLSearchParams(window.location.search);
        queryParams.set('page', '1'); // Reset to first page when filtering
        
        if (dateRange) queryParams.set('dateRange', dateRange);
        if (minPrice) queryParams.set('minPrice', minPrice);
        if (maxPrice) queryParams.set('maxPrice', maxPrice);
        
        // Redirect with filter parameters
        window.location.href = window.location.pathname + '?' + queryParams.toString();
    });
});
</script>

<style>
/* Box and Layout Styles */
.box-primary {
    border-top-color: #3c8dbc;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.mb-3 {
    margin-bottom: 15px;
}

.mb-4 {
    margin-bottom: 20px;
}

/* Info Box Styles */
.info-box {
    min-height: 90px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    margin-bottom: 15px;
}

.info-box-icon {
    height: 90px;
    width: 90px;
    line-height: 90px;
}

.info-box-content {
    padding: 5px 10px;
    margin-left: 90px;
}

.info-box-text {
    text-transform: uppercase;
    display: block;
    font-size: 14px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.info-box-number {
    display: block;
    font-weight: bold;
    font-size: 18px;
}

/* Table Styles */
#bookingsTable {
    border-collapse: collapse;
}

#bookingsTable th {
    background-color: #f9f9f9;
    border-bottom: 2px solid #ddd;
    font-weight: 600;
    padding: 10px;
}

#bookingsTable td {
    padding: 12px 10px;
    border-bottom: 1px solid #f0f0f0;
    vertical-align: middle;
}

#bookingsTable tr:hover {
    background-color: #f5f5f5;
}

/* Button Styles */
.btn-group .btn {
    border-radius: 3px;
    margin: 0 2px;
}

/* Image Styles */
.img-thumbnail {
    border-radius: 4px;
    object-fit: cover;
}

/* Table Row Height */
#bookingsTable tr {
    height: 60px;
}

/* Pagination Styles */
.pagination {
    display: inline-block;
    margin: 20px 0;
}

.pagination > li {
    display: inline;
}

.pagination > li > a {
    position: relative;
    float: left;
    padding: 6px 12px;
    margin-left: -1px;
    text-decoration: none;
    background-color: #fff;
    border: 1px solid #ddd;
    color: #337ab7;
}

.pagination > .active > a {
    z-index: 3;
    color: #fff;
    cursor: default;
    background-color: #337ab7;
    border-color: #337ab7;
}

.pagination > .disabled > a {
    color: #777;
    cursor: not-allowed;
    background-color: #fff;
    border-color: #ddd;
}

.pagination > li:first-child > a {
    margin-left: 0;
    border-top-left-radius: 4px;
    border-bottom-left-radius: 4px;
}

.pagination > li:last-child > a {
    border-top-right-radius: 4px;
    border-bottom-right-radius: 4px;
}
</style>