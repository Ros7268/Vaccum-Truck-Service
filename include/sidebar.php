<!-- sidebar: style can be found in sidebar.less -->


<section class="sidebar">
  <ul class="sidebar-menu">
    <li class="header">MAIN NAVIGATION</li>
    
    <?php 
    $type = $_SESSION['calendar_fd_user']['type'] ?? '';

    // แสดง Dashboard เฉพาะ Admin และ Owner
    if ($type === 'admin' || $type === 'owner' || $type === 'employee') { ?>
      <li class="treeview"> 
        <a href="<?php echo WEB_ROOT; ?>views/?v=DASHBOARD">
          <i class="fa fa-dashboard"></i><span>Dashboard</span>
        </a>
      </li>
    <?php } ?>

    <!-- ทุกคนเห็น Booking Calendar -->
    <li class="treeview"> 
      <a href="<?php echo WEB_ROOT; ?>views/?v=DB">
        <i class="fa fa-calendar"></i><span>Booking Calendar</span>
      </a>
    </li>

    <!-- แก้ Booking Info ให้ owner เห็นด้วย -->
    <?php if ($type === 'admin' || $type === 'user' || $type === 'employee' || $type === 'owner') { ?>
      <li class="treeview"> 
        <a href="<?php echo WEB_ROOT; ?>views/?v=LIST">
          <i class="fa fa-newspaper-o"></i><span>Booking Info</span>
        </a>
      </li>
    <?php } ?>

    <!-- แก้ Assign Work ให้ owner เห็นด้วย -->
    <?php if ($type === 'admin' || $type === 'employee' || $type === 'owner') { ?>
      <li class="treeview"> 
        <a href="<?php echo WEB_ROOT; ?>views/?v=ASSIGNED">
          <i class="fa fa-tasks"></i><span>Assign Work</span>
        </a>
      </li>
    <?php } ?>

    <!-- Admin และ Owner และ Employee เท่านั้นที่เห็น Revenue -->
    <?php if ($type === 'admin' || $type === 'owner' || $type === 'employee') { ?>
      <li class="treeview"> 
        <a href="<?php echo WEB_ROOT; ?>views/?v=REVENUE">
          <i class="fa fa-money"></i><span>Revenue</span>
        </a>
      </li>
    <?php } ?>

    <!-- Admin และ Employee เท่านั้นที่เห็น Outsourcing Management -->
    <?php if ($type === 'admin' || $type === 'employee') { ?>
      <li class="treeview"> 
        <a href="<?php echo WEB_ROOT; ?>views/?v=OUTSOURCING">
          <i class="fa fa-external-link"></i><span>Outsourcing Management</span>
        </a>
      </li>
    <?php } ?>

    <!-- Admin และ Employee เท่านั้นที่เห็น Employees -->
    <?php if ($type === 'admin') { ?>
      <li class="treeview"> 
        <a href="<?php echo WEB_ROOT; ?>views/?v=EMPLOYEES">
          <i class="fa fa-briefcase"></i><span>Employees</span>
        </a>
      </li>
    <?php } ?>

    <?php if ($type === 'driver') { ?>
      <li class="treeview"> 
        <a href="<?php echo WEB_ROOT; ?>views/?v=ASSIGNEDLIST">
          <i class="fa fa-tasks"></i><span>Assign Work</span>
        </a>
      </li>
    <?php } ?>

    <!-- Admin และ Employee เท่านั้นที่เห็น User Management -->
    <?php if ($type === 'admin') { ?>
      <li class="treeview"> 
        <a href="<?php echo WEB_ROOT; ?>views/?v=USERS">
          <i class="fa fa-users"></i><span>User Management</span>
        </a>
      </li>
    <?php } ?>

    <!-- Admin เท่านั้นที่เห็น Vacuum Trucks -->
    <?php if ($type === 'admin') { ?>
      <li class="treeview"> 
        <a href="<?php echo WEB_ROOT; ?>views/?v=TRUCKS">
          <i class="fa fa-truck"></i><span>Vacuum Trucks</span>
        </a>
      </li>
    <?php } ?>

    <!-- Admin และ Owner เท่านั้นที่เห็น Receipts -->
    <?php if ($type === 'admin' || $type === 'owner' || $type === 'user' || $type === 'employee') { ?>
      <li class="treeview"> 
        <a href="<?php echo WEB_ROOT; ?>views/?v=RECEIPT">
          <i class="fa fa-file-pdf-o"></i><span>Receipts</span>
        </a>
      </li>
    <?php } ?>

  </ul>
</section>
<!-- /.sidebar -->
