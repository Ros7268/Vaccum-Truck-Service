<?php
require_once '../library/config.php';
require_once '../library/functions.php';

checkFDUser();

$view = (isset($_GET['v']) && $_GET['v'] != '') ? $_GET['v'] : '';

switch ($view) {
	case 'LIST' :
		$content 	= 'bookinginfo.php';		
		$pageTitle 	= 'View Event Details';
		break;

	case 'USERS' :
		$content 	= 'userlist.php';		
		$pageTitle 	= 'View User Details';
		break;
		
	case 'CREATE' :
		$content 	= 'userform.php';		
		$pageTitle 	= 'Create New User';
		break;
		
	case 'USER' :
		$content 	= 'user.php';		
		$pageTitle 	= 'View User Details';
		break;
	
	case 'HOLY' :
		$content 	= 'holidays.php';		
		$pageTitle 	= 'Holidays';
		break;
	
	case 'EMPLOYEES' :
		$content 	= 'employees.php';		
		$pageTitle 	= 'Employee';
		break;

	case 'TRUCKS' : 
		$content 	= 'truck.php';		
		$pageTitle 	= 'Truck Management';
		break;

	case 'REVENUE' :
		$content = 'revenue.php';
		$pageTitle = 'Revenue Management';
		break;
	
	case 'ASSIGNED':
		$content = 'assigned.php';
		$pageTitle = 'Assign Work';
		break;
	
	case 'RECEIPT':
		$content = 'receipt_list.php';
		$pageTitle = 'Receipt Management';
		break;
		
	case 'DASHBOARD':
		$content = 'dashboard_revenue.php';
		$pageTitle = 'Dashboard';
		break;
		
	case 'OUTSOURCING':
		$content = 'outsourcing_list.php';
		$pageTitle = 'OUTSOURCING Management';
		break;
	
	case 'ASSIGNEDLIST':
		$content = 'assigned_only.php';
		$pageTitle = 'Assigned List';
		break;		
	
	default :
		$content 	= 'dashboard.php';		
		$pageTitle 	= 'Calendar Dashboard';
}

require_once '../include/template.php';
?>
ASSIGNEDLIST