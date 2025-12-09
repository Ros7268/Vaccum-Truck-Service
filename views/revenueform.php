<?php
require_once '../library/functions.php';

$bookingDates = getUnupdatedBookingDates();
$employees = getEmployeesOrAdmins(); 
?>

<div id="revenueModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add Revenue</h4>
      </div>

      <form id="revenueForm" class="form-horizontal" method="post" enctype="multipart/form-data">
        <div class="modal-body">

          <!-- Booking Date -->
          <div class="form-group">
            <label class="col-sm-4 control-label">Booking Date</label>
            <div class="col-sm-8">
              <select name="bookingDate" id="bookingDate" class="form-control" required>
                <option value="">-- Select Booking Date --</option>
                <?php foreach ($bookingDates as $date) { ?>
                  <option value="<?php echo $date['rdate']; ?>">
                    <?php echo (new DateTime($date['rdate']))->format('d/m/Y H:i'); ?>
                  </option>
                <?php } ?>
              </select>
            </div>
          </div>

          <!-- รายละเอียดสินค้า -->
          <div class="form-group">
            <label class="col-sm-4 control-label">Item Description</label>
            <div class="col-sm-8">
              <input type="text" class="form-control" name="item_description" id="item_description" placeholder="เช่น ค่าบริการขนส่ง" required>
            </div>
          </div>

          <!-- จำนวนหน่วย -->
          <div class="form-group">
            <label class="col-sm-4 control-label">Quantity</label>
            <div class="col-sm-8">
              <input type="number" class="form-control" name="quantity" id="quantity" min="1" value="1" required>
            </div>
          </div>

          <!-- ราคาต่อหน่วย -->
          <div class="form-group">
            <label class="col-sm-4 control-label">Unit Price (฿)</label>
            <div class="col-sm-8">
              <input type="text" class="form-control" id="unitPriceFormatted" placeholder="ราคาต่อหน่วย" required>
              <input type="hidden" name="unit_price" id="unit_price">
            </div>
          </div>

          <!-- Amount (รวมอัตโนมัติ) -->
          <div class="form-group">
            <label class="col-sm-4 control-label">Total Amount</label>
            <div class="col-sm-8">
              <input type="text" class="form-control" id="amountFormatted" readonly>
              <input type="hidden" name="amount" id="amount" required>
            </div>
          </div>

          <!-- หักภาษี ณ ที่จ่าย -->
          <div class="form-group">
            <label class="col-sm-4 control-label">Withholding Tax (3%)</label>
            <div class="col-sm-8">
              <label><input type="checkbox" id="withholdingTax" name="withholding_tax" value="1"> Apply 3% tax</label>
            </div>
          </div>

          <!-- ออกใบเสร็จโดย -->
          <div class="form-group">
            <label class="col-sm-4 control-label">Issued By</label>
            <div class="col-sm-8">
              <select name="issued_by" id="issued_by" class="form-control" required>
                <option value="">-- Select Employee/Admin --</option>
                <?php foreach ($employees as $emp) { ?>
                  <option value="<?php echo $emp['user_id']; ?>">
                    <?php echo htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']); ?>
                  </option>
                <?php } ?>
              </select>
            </div>
          </div>

          <!-- รูปภาพใบเสร็จ -->
          <div class="form-group">
            <label class="col-sm-4 control-label">Receipt Image</label>
            <div class="col-sm-8">
              <input type="file" name="receipt_image" id="receipt_image" class="form-control" accept="image/*">
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-info">Add Revenue</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("revenueForm");
    const amountFormatted = document.getElementById("amountFormatted");
    const amount = document.getElementById("amount");

    // Format amount input
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

    // Handle form submission
    form.addEventListener("submit", function (event) {
        event.preventDefault();

        let formData = new FormData(this);
        console.log("Submitting Revenue Data:", Object.fromEntries(formData));

        fetch('../api/process.php?cmd=addRevenue', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log("Server Response:", data);

            if (data.success) {
                alert("Revenue added successfully!");
                location.reload(); // รีเฟรชหน้าเว็บ
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => {
            console.error("Fetch Error:", error);
            alert("Error processing request. Please check console for details.");
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const quantity = document.getElementById("quantity");
    const unitPriceFormatted = document.getElementById("unitPriceFormatted");
    const unit_price = document.getElementById("unit_price");
    const amountFormatted = document.getElementById("amountFormatted");
    const amount = document.getElementById("amount");
    const withholdingTaxCheckbox = document.getElementById("withholdingTax");

    function calculateTotal() {
        let qty = parseInt(quantity.value) || 0;
        let unitPrice = parseFloat(unitPriceFormatted.value.replace(/,/g, '')) || 0;
        let total = qty * unitPrice;

        // หักภาษี 3% ถ้าเลือก
        if (withholdingTaxCheckbox.checked) {
            total = total * 0.97;
        }

        unit_price.value = unitPrice.toFixed(2);
        amount.value = total.toFixed(2);
        amountFormatted.value = Number(total).toLocaleString('en-US', { minimumFractionDigits: 2 });
    }

    quantity.addEventListener("input", calculateTotal);
    unitPriceFormatted.addEventListener("input", calculateTotal);
    withholdingTaxCheckbox.addEventListener("change", calculateTotal);

    // ยืนยันฟอร์มส่ง
    document.getElementById("revenueForm").addEventListener("submit", function (e) {
        if (!amount.value || amount.value <= 0) {
            alert("Please enter valid quantity and unit price.");
            e.preventDefault();
        }
    });
});
</script>
