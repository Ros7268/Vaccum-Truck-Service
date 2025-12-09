<?php
require_once '../library/config.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.5/fullcalendar.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.5/fullcalendar.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        :root {
            --primary-color: #3490dc;
            --secondary-color: #f8fafc;
            --accent-color: #2779bd;
            --text-color: #2d3748;
            --light-gray: #e2e8f0;
            
            /* Status colors */
            --denied-color: #dc3545;
            --waiting-color: #ffc107;
            --approved-color: #28a745;
            --success-color: #0d6efd;
            --outsource-color: #AA38A6;
        }
        
        /* Base styles */
        body {
            font-family: 'Kanit', 'Prompt', sans-serif;
            background-color: var(--secondary-color);
            color: var(--text-color);
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        
        /* Navbar */
        .navbar {
            background-color: white !important;
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        .navbar-brand {
            font-size: 26px;
            font-weight: 700;
            color: var(--primary-color) !important;
            letter-spacing: 1px;
        }
        
        .navbar-nav .nav-link {
            color: var(--text-color) !important;
            font-weight: 500;
            margin: 0 10px;
            position: relative;
            padding: 8px 0;
            transition: all 0.3s ease;
        }
        
        .navbar-nav .nav-link:hover {
            color: var(--primary-color) !important;
        }
        
        .navbar-nav .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: var(--primary-color);
            transition: width 0.3s ease;
        }
        
        .navbar-nav .nav-link:hover::after {
            width: 100%;
        }
        
        /* Calendar container */
        .calendar-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 25px;
            margin-top: 100px;
            margin-bottom: 50px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .calendar-container:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        h2 {
            color: var(--text-color);
            font-weight: 700;
            margin-bottom: 25px;
            text-align: center;
            font-size: 32px;
        }
        
        /* Calendar customization */
        #calendar {
            margin-bottom: 30px;
        }
        
        .fc-today {
            background-color: rgba(52, 144, 220, 0.1) !important;
        }
        
        .fc-day-header {
            background-color: var(--primary-color);
            color: white;
            padding: 12px 0 !important;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 14px;
        }
        
        .fc-button {
            background-color: white !important;
            border: 1px solid var(--light-gray) !important;
            color: var(--text-color) !important;
            text-transform: capitalize !important;
            box-shadow: none !important;
            padding: 8px 15px !important;
            transition: all 0.3s ease;
        }
        
        .fc-button:hover {
            background-color: var(--primary-color) !important;
            color: white !important;
            border-color: var(--primary-color) !important;
        }
        
        .fc-state-active {
            background-color: var(--primary-color) !important;
            color: white !important;
            border-color: var(--primary-color) !important;
        }
        
        /* Status legend */
        .status-legend {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin: 20px 0;
            padding: 15px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        
        .status-item {
            display: flex;
            align-items: center;
            margin: 0 15px 10px;
            font-weight: 500;
        }
        
        .status-color {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            margin-right: 8px;
        }
        
        .denied-color {
            background-color: var(--denied-color);
        }
        
        .waiting-color {
            background-color: var(--waiting-color);
        }
        
        .approved-color {
            background-color: var(--approved-color);
        }
        
        .success-color {
            background-color: var(--success-color);
        }
        
        .outsource-color {
            background-color: var(--outsource-color);
        }
        /* Footer */
        footer {
            background-color: var(--text-color);
            color: white;
            padding: 40px 0 20px;
        }
        
        .footer-links h5 {
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        .footer-links ul {
            list-style: none;
            padding: 0;
        }
        
        .footer-links li {
            margin-bottom: 10px;
        }
        
        .footer-links a {
            color: var(--light-gray);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer-links a:hover {
            color: white;
        }
        
        .copyright {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 30px;
            font-size: 14px;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .calendar-container {
                margin-top: 80px;
                padding: 15px;
            }
            
            h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
        <a class="navbar-brand" href="/event-management/homepage.php">TANAWAT</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link active" href="#">Calendar</a></li>
                    <li class="nav-item"><a class="nav-link" href="/event-management/login.php">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container calendar-container">
        <h2>BOOKING INFO</h2>

        <!-- Status Legend -->
        <div class="status-legend">
            <div class="status-item">
                <div class="status-color denied-color"></div>
                <span>Denied</span>
            </div>
            <div class="status-item">
                <div class="status-color waiting-color"></div>
                <span>Waiting</span>
            </div>
            <div class="status-item">
                <div class="status-color approved-color"></div>
                <span>Approved</span>
            </div>
            <div class="status-item">
                <div class="status-color success-color"></div>
                <span>Success</span>
            </div>
            <div class="status-item">
        <div class="status-color outsource-color"></div>
        <span>Outsource</span>
    </div>
        </div>
        
        <!-- Calendar -->
        <div id="calendar"></div>
    </div>
    
    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4 footer-links">
                    <h5>ติดต่อเรา</h5>
                    <ul>
                        <li><i class="fas fa-phone me-2"></i> 097-295-1874</li>
                        <li><i class="fas fa-envelope me-2"></i> warakorn.chsm@gmail.com</li>
                    </ul>
                </div>
                <div class="col-md-4 footer-links">
                    <h5>บริการของเรา</h5>
                    <ul>
                        <li><a href="#">ดูดสิ่งปฏิกูล</a></li>
                        <li><a href="#">ดูดไขมัน</a></li>
                        <li><a href="#">สูบตะกอน</a></li>
                        <li><a href="#">บริการงูเหล็ก</a></li>
                    </ul>
                </div>
                <div class="col-md-4 footer-links">
                    <h5>ช่องทางติดตาม</h5>
                    <div class="social-icons">
                        <a href="https://www.facebook.com/profile.php?id=61559961270130" class="me-3" target="_blank"><i class="fab fa-facebook fa-2x"></i></a>
                        <a href="https://line.me/ti/p/~pee2000" class="me-3" target="_blank"><i class="fab fa-line fa-2x"></i></a>
                        <a href="#" class="me-3"><i class="fab fa-instagram fa-2x"></i></a>
                    </div>
                </div>
            </div>
            <div class="copyright">
                &copy; 2025 TANAWAT. All Rights Reserved.
            </div>
        </div>
    </footer>

    <script>
    $(document).ready(function() {
        $('#calendar').fullCalendar({
            eventLimit: true, // เปิดใช้งานการจำกัดจำนวนอีเวนต์ต่อวัน
eventLimitText: "more", // กำหนดข้อความ "+X รายการ"
eventLimitClick: "popover", // คลิกที่ "+X รายการ" เพื่อดูรายละเอียด
views: {
    month: {
        eventLimit: 4 // กำหนดให้แสดง 3 อีเวนต์ต่อวัน (ปรับได้ตามต้องการ)
    }},
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            buttonText: {
            today: 'Today',
            month: 'Month',
            week: 'Week',
            day: 'Day'
            },
            monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            monthNamesShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            dayNames: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
            dayNamesShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],

            editable: false,
            eventStartEditable: false,
            eventDurationEditable: false,
            droppable: false,
            selectable: false,
            eventClick: function() { return false; },
            eventRender: function(event, element) {
    var eventTime = event.start ? moment(event.start).format('HH:mm') + ' ' : '';
    
    // แสดงเวลาใน tooltip พร้อมสถานะ
    var statusText = event.status ? ' - ' + event.status : '';
    element.attr('title', eventTime + event.title + statusText);

    // ปรับแต่งสไตล์ของ event
    element.css({
        'border-radius': '4px',
        'border': 'none',
        'padding': '2px 5px',
        'font-size': '0.9em',
        'box-shadow': '0 2px 4px rgba(0,0,0,0.1)',
        'white-space': 'nowrap',
        'overflow': 'hidden',
        'text-overflow': 'ellipsis'
    });

    // อัปเดตข้อความใน event ให้ไม่ซ้ำ และเพิ่มเวลา
    element.find('.fc-content').html('<span>' + eventTime + event.title + '</span>');
},
            eventAfterAllRender: function() {
                // Additional styling after all events are rendered
                $('.fc-day-grid-event').hover(
                    function() {
                        $(this).css('transform', 'translateY(-2px)');
                    },
                    function() {
                        $(this).css('transform', 'translateY(0)');
                    }
                );
            },
            // ปรับแต่งใน eventRender และ events callback
events: function(start, end, timezone, callback) {
    $.ajax({
        url: '../api/process.php?cmd=calview',
        dataType: 'json',
        type: 'POST',
        data: {
            start: start.format(),
            end: end.format()
        },
        success: function(doc) {
            var mappedEvents = doc.map(function(event) {
                if (event.status) {
                    switch(event.status.toLowerCase()) {
                        case 'denied':
                            event.backgroundColor = '#dc3545';
                            event.borderColor = '#dc3545';
                            break;
                        case 'waiting':
                            event.backgroundColor = '#ffc107';
                            event.borderColor = '#ffc107';
                            break;
                        case 'approve':
                        case 'approved':
                            event.backgroundColor = '#28a745';
                            event.borderColor = '#28a745';
                            break;
                        case 'success':
                            event.backgroundColor = '#0d6efd';
                            event.borderColor = '#0d6efd';
                            break;
                        case 'outsource':  // เพิ่มการจัดการสีสำหรับ Outsource
                            event.backgroundColor = '#AA38A6';
                            event.borderColor = '#AA38A6';
                            break;
                        default:
                            event.backgroundColor = '#6c757d';
                            event.borderColor = '#6c757d';
                    }
                }
                return event;
            });
            callback(mappedEvents);
        }
    });
}

        });
    });
    </script>
</body>
</html>