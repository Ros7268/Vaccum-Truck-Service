<?php
require_once '../library/functions.php';

$pendingBookings = getPendingBookings();
?>

<div id="outsourcingModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Outsource Booking</h4>
      </div>

      <form id="outsourcingForm" class="form-horizontal" method="post" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="form-group">
            <label class="col-sm-4 control-label">Select Booking</label>
            <div class="col-sm-8">
              <select name="bookingId" id="bookingId" class="form-control" required>
                <option value="">-- Select Booking --</option>
                <?php foreach ($pendingBookings as $booking) { ?>
                  <option value="<?php echo $booking['reservation_id']; ?>">
                    <?php echo "Booking #".$booking['reservation_id']." - ".$booking['rdate']; ?>
                  </option>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-4 control-label">Outsourcing Price</label>
            <div class="col-sm-8">
              <input type="text" class="form-control" id="priceFormatted" name="priceFormatted" placeholder="Enter Price" required>
              <input type="hidden" name="price" id="price">
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-4 control-label">Attachment</label>
            <div class="col-sm-8">
              <input type="file" name="attachment" id="attachment" class="form-control" accept="image/*">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-info">Outsource</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("outsourcingForm");
    const priceFormatted = document.getElementById("priceFormatted");
    const price = document.getElementById("price");

    function formatNumberInput(event) {
        const value = event.target.value.replace(/,/g, "");
        if (!isNaN(value) && value !== "") {
            const formatted = Number(value).toLocaleString("en-US");
            priceFormatted.value = formatted;
            price.value = value;
        } else {
            priceFormatted.value = "";
            price.value = "";
        }
    }
    priceFormatted.addEventListener("input", formatNumberInput);

    form.addEventListener("submit", function (event) {
        event.preventDefault();

        let formData = new FormData(this);

        fetch('../api/process.php?cmd=outsourceBooking', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Booking outsourced successfully!");
                location.reload();
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => {
            console.error("Fetch Error:", error);
            alert("Error processing request.");
        });
    });
});
</script>
