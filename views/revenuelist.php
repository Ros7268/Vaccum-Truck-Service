<?php
require_once '../library/functions.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$revenueData = getRevenueUpdateRecords($page);
$records = $revenueData['data'];
$total_records = $revenueData['total'];
$per_page = $revenueData['per_page'];
$total_pages = ceil($total_records / $per_page);
$totalRevenueAll = getTotalRevenue();
?>

<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title">Revenue Update List</h3>
    
    <?php 
    // Check if current user is NOT owner type, then show the Add Revenue button
    if (!isset($_SESSION['calendar_fd_user']) || $_SESSION['calendar_fd_user']['type'] !== 'owner') { 
    ?>
      <button type="button" class="btn btn-info pull-right" data-toggle="modal" data-target="#revenueModal">
        <i class="fa fa-plus"></i>&nbsp;Add Revenue
      </button>
    <?php 
    } 
    ?>
  </div>

  <div class="box-body">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>#</th>
          <th>Booking ID</th>
          <th>User Name</th>
          <th>Address</th>
          <th>Booking Date</th>
          <th>Updated Amount</th>
          <th>Issued By</th>
          <th>Receipt</th>
          <th>Updated At</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $idx = ($page - 1) * $per_page + 1;
        foreach ($records as $rec) {
          extract($rec);
        ?>
        <tr id="row-<?php echo $reservation_id; ?>">
          <td><?php echo $idx++; ?></td>
          <td><?php echo $reservation_id; ?></td>
          <td><?php echo htmlspecialchars($user_first_name . ' ' . $user_last_name); ?></td>
          <td><?php echo htmlspecialchars($booking_address); ?></td>
          <td><?php echo (new DateTime($booking_date))->format('d/m/Y H:i'); ?></td>
          <td class="amount-cell" id="amount-<?php echo $reservation_id; ?>">
            <?php echo number_format($updated_amount, 2) . " ฿"; ?>
          </td>
          <td><?php echo htmlspecialchars($issued_first_name . ' ' . $issued_last_name); ?></td>
          <td>
            <?php if (!empty($receipt_image)) { ?>
              <img src="../uploads/revenue/<?php echo $receipt_image; ?>"
                   width="60" height="60"
                   class="img-thumbnail receipt-image"
                   data-toggle="modal"
                   data-target="#imageModal"
                   data-img="../uploads/revenue/<?php echo $receipt_image; ?>">
            <?php } else { ?>
              <span class="text-muted">No Image</span>
            <?php } ?>
          </td>
          <td><?php echo (new DateTime($updated_at))->format('d/m/Y H:i:s'); ?></td>
          <td>
            <?php 
            // ตรวจสอบว่าผู้ใช้ปัจจุบันไม่ใช่ประเภท owner
            if (!isset($_SESSION['calendar_fd_user']) || $_SESSION['calendar_fd_user']['type'] !== 'owner') { 
            ?>
              <button class="btn btn-warning btn-sm edit-btn"
                      data-id="<?php echo $reservation_id; ?>"
                      data-amount="<?php echo $updated_amount; ?>"
                      data-receipt="<?php echo $receipt_image; ?>">
                Edit
              </button>
              <button class="btn btn-danger btn-sm delete-btn"
                      data-id="<?php echo $reservation_id; ?>">
                Delete
              </button>
            <?php 
            } else {
              echo '<span class="text-muted">No Actions</span>';
            }
            ?>
          </td>
        </tr>
        <?php } ?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="5" style="text-align: right;"><strong>Total Revenue:</strong></td>
          <td colspan="5">
            <strong id="totalRevenue"><?php echo number_format($totalRevenueAll, 2) . " ฿"; ?></strong>
          </td>
        </tr>
      </tfoot>
    </table>
  </div>

  <div class="box-footer clearfix">
    <ul class="pagination pagination-sm no-margin pull-right">
      <?php if ($page > 1) { ?>
        <li><a href="?v=REVENUE&page=<?php echo ($page - 1); ?>">« Previous</a></li>
      <?php } ?>
      <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
        <li class="<?php echo ($i == $page) ? 'active' : ''; ?>">
          <a href="?v=REVENUE&page=<?php echo $i; ?>"><?php echo $i; ?></a>
        </li>
      <?php } ?>
      <?php if ($page < $total_pages) { ?>
        <li><a href="?v=REVENUE&page=<?php echo ($page + 1); ?>">Next »</a></li>
      <?php } ?>
    </ul>
  </div>
</div>

<!-- Modal Edit Revenue -->
<div id="editRevenueModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit Revenue</h4>
      </div>
      <form id="editRevenueForm" class="form-horizontal">
        <div class="modal-body">
          <input type="hidden" name="bookingId" id="editBookingId">

          <div class="form-group">
            <label class="col-sm-4 control-label">Amount</label>
            <div class="col-sm-8">
              <input type="text" class="form-control" id="editAmountFormatted" required>
              <input type="hidden" name="amount" id="editAmount">
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-4 control-label">Receipt Image</label>
            <div class="col-sm-8">
              <input type="file" name="receipt_image" class="form-control" accept="image/*">
              <div id="currentReceiptPreview" style="margin-top: 10px;">
                <img id="currentReceiptImage" src="" width="60" height="60" class="img-thumbnail">
              </div>
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-info">Update Revenue</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- เพิ่ม Modal สำหรับแสดงรูปภาพขนาดใหญ่ -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="imageModalLabel">รูปภาพใบเสร็จ</h4>
      </div>
      <div class="modal-body text-center">
        <img id="modalImage" src="" style="max-width: 100%; height: auto;">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
      </div>
    </div>
  </div>
</div>

<!-- สคริปต์รวมการทำงานทั้งหมด -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    // ฟังก์ชันอัปเดต Total Revenue จากเซิร์ฟเวอร์
    function updateTotalRevenue() {
        fetch('../api/process.php?cmd=getTotalRevenue')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById("totalRevenue").textContent =
                    Number(data.totalRevenue).toLocaleString() + " ฿";
            } else {
                console.error("Error updating total revenue:", data.message);
            }
        })
        .catch(error => console.error("Fetch Error:", error));
    }

    // เรียกใช้ `updateTotalRevenue()` เมื่อโหลดหน้าเว็บ
    updateTotalRevenue();

    // ฟังก์ชันเปิด Modal แก้ไข และเติมข้อมูล
    function openEditModal(button) {
        document.getElementById('editBookingId').value = button.dataset.id;
        document.getElementById('editAmountFormatted').value =
            Number(button.dataset.amount).toLocaleString();
        document.getElementById('editAmount').value = button.dataset.amount;

        let receiptImage = button.dataset.receipt;
        let imgElement = document.getElementById('currentReceiptImage');
        let previewDiv = document.getElementById('currentReceiptPreview');

        if (receiptImage) {
            imgElement.src = "../uploads/revenue/" + receiptImage;
            previewDiv.style.display = "block";
        } else {
            previewDiv.style.display = "none";
        }

        $('#editRevenueModal').modal('show');
    }

    // เพิ่ม EventListener สำหรับปุ่ม Edit
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function () {
            openEditModal(this);
        });
    });

    // ฟังก์ชัน Highlight แถวที่มีการเปลี่ยนแปลง
    function highlightRow(bookingId) {
        let row = document.getElementById(`row-${bookingId}`);
        if (row) {
            row.classList.add("highlight-success");
            // หลังจาก 4 วินาที ให้จางหายไป
            setTimeout(() => {
                row.classList.remove("highlight-success");
                row.classList.add("fade-out");
                setTimeout(() => row.classList.remove("fade-out"), 2000); // กลับเป็นปกติ
            }, 4000);
        }
    }

    // ฟังก์ชันอัปเดต Revenue
    document.getElementById('editRevenueForm').addEventListener('submit', function(event) {
        event.preventDefault();

        let formData = new FormData(this);
        let bookingId = document.getElementById('editBookingId').value;
        let fileInput = document.querySelector("input[name='receipt_image']");

        // ถ้าไม่มีไฟล์ใหม่ -> ลบ `receipt_image` ออกจาก FormData เพื่อไม่ให้อัปเดตรูป
        if (fileInput.files.length === 0) {
            formData.delete("receipt_image");
        }

        fetch('../api/process.php?cmd=updateRevenue', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Revenue updated successfully!");

                // อัปเดตค่า amount ในตาราง
                let amountCell = document.getElementById(`amount-${bookingId}`);
                if (amountCell) {
                    amountCell.textContent =
                        Number(data.updatedData.updated_amount).toLocaleString() + " ฿";
                }

                // อัปเดตรูปภาพใบเสร็จ **ถ้ามีไฟล์ใหม่เท่านั้น**
                if (data.updatedData.receipt_image) {
                    let imgElement = document.querySelector(`#row-${bookingId} .img-thumbnail`);
                    if (imgElement) {
                        imgElement.src = "../uploads/revenue/" + data.updatedData.receipt_image;
                        // อัปเดต data-img ให้ Modal ด้วย
                        imgElement.setAttribute('data-img', "../uploads/revenue/" + data.updatedData.receipt_image);
                    }
                }

                // อัปเดต data-* ของปุ่ม Edit
                let editButton = document.querySelector(`button.edit-btn[data-id="${bookingId}"]`);
                if (editButton) {
                    editButton.setAttribute('data-amount', data.updatedData.updated_amount);
                    editButton.setAttribute('data-receipt', data.updatedData.receipt_image || "");
                }

                // ล้างไฟล์เลือก
                fileInput.value = "";

                // อัปเดต Total Revenue
                updateTotalRevenue();

                // Highlight แถว
                highlightRow(bookingId);

                // ปิด Modal
                setTimeout(() => {
                    $('#editRevenueModal').modal('hide');
                }, 100);
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => {
            console.error("Fetch Error:", error);
            alert("Error processing request. Please check console for details.");
        });
    });

    // ฟังก์ชันอัปเดตตารางรายได้รายเดือน (เชื่อมกับ revenueMonthly.php ถ้ามี)
    function updateMonthlyRevenue() {
        fetch('../api/process.php?cmd=getMonthlyRevenueJSON')
        .then(response => response.json())
        .then(data => {
            let tableBody = document.getElementById("monthlyRevenueBody");
            let grandTotalElem = document.getElementById("grandTotal");

            if (!tableBody) return; // กรณีไม่มีตารางนี้ในหน้า
            // เคลียร์ข้อมูลเก่า
            tableBody.innerHTML = "";
            let grandTotal = 0;
            let index = 1;

            // อัปเดตข้อมูลใหม่
            data.forEach(record => {
                if (record.total_revenue > 0) {
                    let row = `<tr>
                        <td>${index++}</td>
                        <td>${new Date(record.month).toLocaleString('en-US', { month: '2-digit', year: 'numeric' })}</td>
                        <td>${parseFloat(record.total_revenue).toLocaleString()} ฿</td>
                    </tr>`;
                    tableBody.innerHTML += row;
                    grandTotal += parseFloat(record.total_revenue);
                }
            });

            // อัปเดต Grand Total
            if (grandTotalElem) {
                grandTotalElem.textContent = grandTotal.toLocaleString() + " ฿";
            }
        })
        .catch(error => console.error("Fetch Error:", error));
    }
    updateMonthlyRevenue();

    // ลบ Revenue
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function () {
            let bookingId = this.dataset.id;
            if (confirm("Are you sure you want to delete this record?")) {
                fetch(`../api/process.php?cmd=deleteRevenue&bookingId=${bookingId}`, {
                    method: 'GET'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Revenue deleted successfully!");
                        document.getElementById(`row-${bookingId}`).remove(); // ลบแถวออกจากตาราง
                        // แจ้งเหตุการณ์เพื่อให้ revenueMonthly (ถ้ามี) โหลดข้อมูลใหม่
                        document.dispatchEvent(new Event("revenueDeleted"));
                        updateMonthlyRevenue();
                        updateTotalRevenue();
                    } else {
                        alert("Error: " + data.message);
                    }
                })
                .catch(error => {
                    console.error("Fetch Error:", error);
                    alert("Error processing request. Please check console for details.");
                });
            }
        });
    });

    // เพิ่มส่วนนี้สำหรับการแสดงรูปภาพใน modal (ใบเสร็จขนาดใหญ่)
    $('#imageModal').on('show.bs.modal', function (event) {
        var imgSrc = $(event.relatedTarget).data('img');
        $('#modalImage').attr('src', imgSrc);
    });

    // ปรับให้รูปภาพในตารางมี cursor เป็น pointer
    document.querySelectorAll('.receipt-image').forEach(img => {
        img.style.cursor = 'pointer';
    });

    // จัดรูปแบบ input จำนวนเงิน (Modal Edit)
    const amountFormatted = document.getElementById("editAmountFormatted");
    const amount = document.getElementById("editAmount");
    function formatNumberInput(event) {
        const value = event.target.value.replace(/,/g, "");
        if (!isNaN(value) && value !== "") {
            const formatted = Number(value).toLocaleString("en-US");
            amountFormatted.value = formatted;
            amount.value = value;
        } else {
            amountFormatted.value = "";
            amount.value = "";
        }
    }
    amountFormatted.addEventListener("input", formatNumberInput);
});
</script>

<!-- ฟอร์มเพิ่ม Revenue -->
<?php include('revenueform.php'); ?>

<!-- CSS -->
<style>
  /* สีเขียวอ่อนเมื่อมีการอัปเดต */
  .highlight-success {
    background-color: #d4edda !important; /* สีเขียวอ่อน */
    transition: background-color 2s ease-in-out;
  }

  /* สีจางกลับเป็นปกติ */
  .fade-out {
    background-color: white !important;
    transition: background-color 2s ease-in-out;
  }

  /* จัดรูปแบบให้ตารางดูดีขึ้น */
  .table-bordered th, .table-bordered td {
      text-align: center;
      vertical-align: middle;
      white-space: nowrap;
  }
  .table-bordered th {
      background-color: #f4f4f4;
  }

  /* ขนาดของรูปใบเสร็จ */
  .img-thumbnail {
      max-width: 60px;
      height: auto;
  }

  /* เพิ่ม cursor pointer ให้รูปใบเสร็จ */
  .receipt-image {
      cursor: pointer;
  }
</style>