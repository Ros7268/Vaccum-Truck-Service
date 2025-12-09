<?php
require_once '../library/functions.php';

$monthlyRevenue = getMonthlyRevenueRecords();
?>

<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title">Monthly Revenue Summary</h3>
  </div>
  <div class="box-body">
    <table class="table table-bordered">
      <tr>
        <th>#</th>
        <th>Month</th>
        <th>Total Revenue</th>
      </tr>
      <?php
      $idx = 1;
      $grandTotal = 0;
      foreach ($monthlyRevenue as $record) {
          extract($record);
          
          // ถ้า total_revenue เป็น NULL หรือ 0 ไม่ต้องแสดง
          if (!empty($total_revenue)) { 
              $grandTotal += $total_revenue;
      ?>
      <tr>
        <td><?php echo $idx++; ?></td>
        <td><?php echo !empty($month) ? (new DateTime($month))->format('m/Y') : ''; ?></td>
        <td><?php echo number_format($total_revenue, 2) . " ฿"; ?></td>
      </tr>
      <?php 
          } // End if 
      } ?>
      
      <!-- แสดง Grand Total เฉพาะเมื่อมีข้อมูล -->
      <?php if ($grandTotal > 0) { ?>
      <tr>
        <td colspan="2" style="text-align: right;"><strong>Grand Total:</strong></td>
        <td><strong><?php echo number_format($grandTotal, 2) . " ฿"; ?></strong></td>
      </tr>
      <?php } ?>
    </table>
  </div>
</div>
