<?php 
require_once '../library/functions.php';

// Retrieve revenue data
$totalRevenue = getTotalRevenue(); 
$totalTransactions = getTotalRevenueTransactions();
$lastUpdated = getLastRevenueUpdateDate();

// Monthly revenue data
$monthlyRevenue = getMonthlyRevenueRecords();

// Get total bookings data
$sql = "SELECT COUNT(*) AS total FROM tbl_reservations";
$result = dbQuery($sql);
$totalBookings = dbFetchAssoc($result)['total'];

// Get successful bookings data
$sql = "SELECT COUNT(*) AS total FROM tbl_reservations WHERE status = 'SUCCESS'";
$result = dbQuery($sql);
$successfulBookings = dbFetchAssoc($result)['total'];

// Get average revenue per booking
$avgRevenue = $totalRevenue > 0 && $successfulBookings > 0 ? $totalRevenue / $successfulBookings : 0;

// Get bookings by status
$sql = "SELECT status, COUNT(*) as count FROM tbl_reservations GROUP BY status";
$result = dbQuery($sql);
$bookingsByStatus = array();
while ($row = dbFetchAssoc($result)) {
    $bookingsByStatus[$row['status']] = $row['count'];
}

// Get last 5 revenue transactions
$sql = "SELECT ru.revenue_id, ru.reservation_id, ru.updated_amount, ru.updated_at, u.name AS customer_name
FROM tbl_revenue_updates ru
LEFT JOIN tbl_reservations r ON ru.reservation_id = r.reservation_id
LEFT JOIN tbl_users u ON r.user_id = u.user_id
ORDER BY ru.updated_at DESC LIMIT 5";
$result = dbQuery($sql);
$recentTransactions = array();
while ($row = dbFetchAssoc($result)) {
    $recentTransactions[] = $row;
}

// Get monthly assigned jobs data
$assignmentData = calculateMonthlyAssignments();
$recentMonths = array_slice(array_keys($assignmentData), 0, 6);
?>

<!-- Main Dashboard Content -->
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-dashboard"></i> Revenue Dashboard</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <!-- Key Metrics Row -->
                <div class="row">
                    <!-- Total Revenue -->
                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-green">
                            <div class="inner">
                                <h3><?php echo number_format($totalRevenue, 2); ?> ฿</h3>
                                <p>Total Revenue</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-money"></i>
                            </div>
                            <a href="<?php echo WEB_ROOT; ?>views/?v=REVENUE" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    
                    <!-- Total Transactions -->
                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-blue">
                            <div class="inner">
                                <h3><?php echo number_format($totalTransactions); ?></h3>
                                <p>Total Transactions</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-file-text"></i>
                            </div>
                            <a href="<?php echo WEB_ROOT; ?>views/?v=RECEIPT" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    
                    <!-- Average Revenue per Booking -->
                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-purple">
                            <div class="inner">
                                <h3><?php echo number_format($avgRevenue, 2); ?> ฿</h3>
                                <p>Average Revenue/Booking</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-line-chart"></i>
                            </div>
                            <div class="small-box-footer">Based on successful bookings</div>
                        </div>
                    </div>
                    
                    <!-- Last Updated -->
                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-yellow">
                            <div class="inner">
                                <h3><?php echo $lastUpdated; ?></h3>
                                <p>Last Updated</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-clock-o"></i>
                            </div>
                            <div class="small-box-footer">Last transaction time</div>
                        </div>
                    </div>
                </div>
                
                <!-- Booking Statistics Row -->
                <div class="row">
                    <!-- Monthly Revenue Chart -->
                    <div class="col-md-8">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Monthly Revenue Trend</h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="chart">
                                    <canvas id="revenueChart" style="height: 300px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Booking Status -->
                    <div class="col-md-4">
                        <div class="box box-danger">
                            <div class="box-header with-border">
                                <h3 class="box-title">Booking Status</h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="box-body">
                                <canvas id="bookingStatusChart" style="height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Transactions and Employee Assignment -->
                <div class="row">
                    <!-- Recent Transactions -->
                    <div class="col-md-6">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Recent Revenue Transactions</h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Customer</th>
                                                <th>Amount</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentTransactions as $transaction): ?>
                                            <tr>
                                                <td><?php echo $transaction['reservation_id']; ?></td>
                                                <td><?php echo $transaction['customer_name']; ?></td>
                                                <td><?php echo number_format($transaction['updated_amount'], 2); ?> ฿</td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($transaction['updated_at'])); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-center">
                                    <a href="<?php echo WEB_ROOT; ?>views/?v=REVENUE" class="btn btn-primary btn-sm">View All Transactions</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Employee Assignment -->
                    <div class="col-md-6">
                        <div class="box box-success">
                            <div class="box-header with-border">
                                <h3 class="box-title">Monthly Work Assignments</h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="box-body">
                                <canvas id="employeeAssignmentChart" style="height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Monthly Revenue Chart
    var monthlyRevenueData = <?php echo json_encode($monthlyRevenue); ?>;
    var labels = monthlyRevenueData.map(item => {
        // Convert YYYY-MM to readable format (ex: Jan 2023)
        var year = item.month.substring(0, 4);
        var month = parseInt(item.month.substring(5, 7));
        var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return months[month - 1] + ' ' + year;
    });
    var data = monthlyRevenueData.map(item => parseFloat(item.total_revenue));
    
    // Reverse arrays so chart shows oldest to newest
    labels.reverse();
    data.reverse();
    
    var ctx = document.getElementById('revenueChart').getContext('2d');
    var revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Monthly Revenue (฿)',
                data: data,
                borderColor: 'rgba(60, 141, 188, 1)',
                backgroundColor: 'rgba(60, 141, 188, 0.2)',
                borderWidth: 2,
                pointRadius: 4,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' ฿';
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.raw.toLocaleString() + ' ฿';
                        }
                    }
                }
            }
        }
    });
    
    // Booking Status Chart
    var statusData = <?php echo json_encode($bookingsByStatus); ?>;
    var statusLabels = Object.keys(statusData);
    var statusCounts = Object.values(statusData);
    var statusColors = [
        'rgba(0, 166, 90, 0.8)',  // SUCCESS
        'rgba(60, 141, 188, 0.8)', // PENDING
        'rgba(243, 156, 18, 0.8)', // APPROVED
        'rgba(221, 75, 57, 0.8)',  // CANCELLED
        'rgba(0, 192, 239, 0.8)'   // Other
    ];
    
    var statusChart = new Chart(document.getElementById('bookingStatusChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusCounts,
                backgroundColor: statusColors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });
    
    // Employee Assignment Chart
    var assignmentData = <?php echo json_encode($assignmentData); ?>;
    var recentMonths = <?php echo json_encode($recentMonths); ?>;
    
    // Process data for chart
    var employeeNames = new Set();
    recentMonths.forEach(month => {
        if (assignmentData[month]) {
            Object.keys(assignmentData[month]).forEach(name => {
                if (name) employeeNames.add(name);
            });
        }
    });
    
    // Convert Set to Array
    var employeeArray = Array.from(employeeNames);
    
    // Create datasets
    var datasets = employeeArray.map((name, index) => {
        // Generate a color based on index
        var hue = index * (360 / employeeArray.length);
        var color = `hsl(${hue}, 70%, 60%)`;
        
        var monthlyData = recentMonths.map(month => {
            return assignmentData[month] && assignmentData[month][name] ? assignmentData[month][name] : 0;
        });
        
        return {
            label: name,
            data: monthlyData,
            backgroundColor: color,
            borderColor: color,
            borderWidth: 1
        };
    });
    
    // Format months for display
    var displayMonths = recentMonths.map(monthStr => {
        var year = monthStr.substring(0, 4);
        var month = parseInt(monthStr.substring(5, 7));
        var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return months[month - 1] + ' ' + year;
    });
    
    // Create chart
    var assignmentCtx = document.getElementById('employeeAssignmentChart').getContext('2d');
    var assignmentChart = new Chart(assignmentCtx, {
        type: 'bar',
        data: {
            labels: displayMonths,
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    stacked: true
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Assignments'
                    }
                }
            }
        }
    });
});
</script>

<style>
.small-box {
    border-radius: 5px;
    position: relative;
    display: block;
    margin-bottom: 20px;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
}

.small-box > .inner {
    padding: 10px;
}

.small-box h3 {
    font-size: 38px;
    font-weight: bold;
    margin: 0 0 10px 0;
    white-space: nowrap;
    padding: 0;
}

.small-box p {
    font-size: 15px;
    color: rgba(255, 255, 255, 0.8);
}

.small-box .icon {
    position: absolute;
    top: 15px;
    right: 15px;
    font-size: 70px;
    color: rgba(0, 0, 0, 0.15);
}

.small-box > .small-box-footer {
    position: relative;
    text-align: center;
    padding: 3px 0;
    color: rgba(255, 255, 255, 0.8);
    display: block;
    z-index: 10;
    background: rgba(0, 0, 0, 0.1);
    text-decoration: none;
}

.small-box > .small-box-footer:hover {
    color: #fff;
    background: rgba(0, 0, 0, 0.15);
}

.bg-green {
    background-color: #00a65a !important;
    color: white !important;
}

.bg-blue {
    background-color: #0073b7 !important;
    color: white !important;
}

.bg-yellow {
    background-color: #f39c12 !important;
    color: white !important;
}

.bg-purple {
    background-color: #605ca8 !important;
    color: white !important;
}

.box {
    position: relative;
    border-radius: 3px;
    background: #ffffff;
    border-top: 3px solid #d2d6de;
    margin-bottom: 20px;
    width: 100%;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
}

.box-header {
    color: #444;
    display: block;
    padding: 10px;
    position: relative;
}

.box-header.with-border {
    border-bottom: 1px solid #f4f4f4;
}

.box-body {
    border-top-left-radius: 0;
    border-top-right-radius: 0;
    border-bottom-right-radius: 3px;
    border-bottom-left-radius: 3px;
    padding: 10px;
}

.box-footer {
    border-top-left-radius: 0;
    border-top-right-radius: 0;
    border-bottom-right-radius: 3px;
    border-bottom-left-radius: 3px;
    border-top: 1px solid #f4f4f4;
    padding: 10px;
    background-color: #fff;
}

.box-primary {
    border-top-color: #3c8dbc;
}

.box-info {
    border-top-color: #00c0ef;
}

.box-success {
    border-top-color: #00a65a;
}

.box-danger {
    border-top-color: #dd4b39;
}

.table-responsive {
    min-height: .01%;
    overflow-x: auto;
}

.btn-primary {
    background-color: #3c8dbc;
    border-color: #367fa9;
    color: #fff;
}

.btn-primary:hover {
    background-color: #367fa9;
    border-color: #204d74;
}
</style>