<?php 
$msgTxt = (isset($_GET['msg']) && $_GET['msg'] != '') ? $_GET['msg'] : '&nbsp;';
$errTxt = (isset($_GET['err']) && $_GET['err'] != '') ? $_GET['err'] : '&nbsp;';
$mCls 	= ""; 
$mFaCls = "";

// ตั้งค่าสีสำหรับข้อความแจ้งเตือน
if($msgTxt != '&nbsp;') {
    $mCls = 'success';
    $mFaCls = 'check';
}
if($errTxt != '&nbsp;') {
    $mCls = 'danger';
    $mFaCls = 'ban';
}

// ถ้ามีข้อความใดๆ ให้แสดง
if ($msgTxt != "&nbsp;" || $errTxt != "&nbsp;") {
    $dMsg = $msgTxt != "&nbsp;" ? $msgTxt : $errTxt; 

    // ตรวจสอบว่าเป็นข้อความ "Max 3 bookings" หรือ "Max 6 bookings" ไหม
    if (strpos($dMsg, "Max 3 bookings") !== false || strpos($dMsg, "Max 6 bookings") !== false) {
        $mCls = 'danger'; // เปลี่ยนเป็นสีแดง
    }
?>
<div class="alert alert-<?php echo $mCls; ?> alert-dismissable">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  <i class="icon fa fa-<?php echo $mFaCls; ?>"></i><?php echo $dMsg; ?>
</div>
<?php 
} // End if
?>
