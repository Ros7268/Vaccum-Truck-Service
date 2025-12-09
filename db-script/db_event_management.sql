-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 24, 2025 at 09:42 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_event_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_assigned_tasks`
--

CREATE TABLE `tbl_assigned_tasks` (
  `assigned_task_id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `employee_id` int(5) NOT NULL,
  `truck_id` int(5) NOT NULL,
  `driver_id` int(5) NOT NULL,
  `assigned_by` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_employees`
--

CREATE TABLE `tbl_employees` (
  `employee_id` int(5) NOT NULL,
  `employee_code` varchar(10) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `citizen_id` varchar(13) NOT NULL,
  `name` varchar(100) NOT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `age` varchar(2) NOT NULL DEFAULT '0',
  `religion` varchar(15) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `phone` varchar(10) NOT NULL,
  `emergency_phone` varchar(10) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `permanent_address` varchar(255) DEFAULT NULL,
  `position` varchar(20) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `profile_pic` varchar(255) DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_employees`
--

INSERT INTO `tbl_employees` (`employee_id`, `employee_code`, `username`, `citizen_id`, `name`, `last_name`, `gender`, `age`, `religion`, `birth_date`, `start_date`, `phone`, `emergency_phone`, `address`, `permanent_address`, `position`, `status`, `created_at`, `profile_pic`, `nationality`) VALUES
(49, '200425-15', 'visawapong', '1100446789987', 'วิศวพงศ์', 'พัวพันธ์', 'male', '22', 'พุทธ', '2003-03-23', '2025-04-20', '0994564321', '0826546546', '102 คอนโดดินแดง ถนนดินแดง แขวงดินแดง เขตดินแดง กรุงเทพฯ 11003', '201/45 หมู่บ้านเจริฐ ถนนรังสิต แขวงรังสิต เขตรังสิต ปธุมธานี 20650', 'driver', 'active', '2025-04-23 20:59:53', '1745416793_IMG_20210627_204638_edit_75888082282169 (1).jpg', 'ไทย'),
(50, '220425-16', 'woralite', '1100441651519', 'วรฤทธิ์', 'อิ่มสำราญ', 'male', '22', 'พุทธ', '2003-06-11', '2025-04-22', '0642151919', '0875165156', '33/102 หมู่บ้านบ้านครัว ถ.เจริญผล แขวงพญาไท เขตราชเทวี กรุงเทพฯ 10456', '33/102 หมู่บ้านบ้านครัว ถ.เจริญผล แขวงพญาไท เขตราชเทวี กรุงเทพฯ 10456', 'driver', 'active', '2025-04-23 21:02:34', '1745416954_IMG_20210627_204638_edit_75888082282169 (1).jpg', 'ไทย'),
(51, '020425-17', 'narakorn', '1100456489413', 'นรากร', 'แสงประเสริญฐ์', 'male', '23', 'พุทธ', '2002-09-27', '2025-04-02', '0695984645', '0821216519', '304 หมู่บ้านรามอินทรา ถนนนวมิล แขวงนวมิล เขตนวลจันทร์ 11405', '304 หมู่บ้านรามอินทรา ถนนนวมิล แขวงนวมิล เขตนวลจันทร์ 11405', 'employee', 'active', '2025-04-23 21:06:11', '1745417171_IMG_20210627_204638_edit_75888082282169 (1).jpg', 'ไทย'),
(56, '120425-18', 'samdriver', '1100703355544', 'วรากร', 'โชคสมัย', 'male', '22', 'พุทธ', '2002-06-22', '2025-04-12', '0806454894', '0949498498', '99 เดอะคอนเนค1 ประเวศ ประเวศ 10400', '99 เดอะคอนเนค1 ประเวศ ประเวศ 10400', 'driver', 'active', '2025-04-24 20:56:27', '1745502987_IMG_20210627_204638_edit_75888082282169 (1).jpg', 'ไทย'),
(57, '060425-19', 'veerayuth', '1100311456787', 'วีรยุทธ', 'ทองหลอม', 'male', '23', 'พุทธ', '2002-02-12', '2025-04-06', '0845646545', '0810989746', '123/45 ถนนสุขุมวิท แขวงคลองตันเหนือ เขตวัฒนา กรุงเทพมหานคร 10110', '123/45 ถนนสุขุมวิท แขวงคลองตันเหนือ เขตวัฒนา กรุงเทพมหานคร 10110', 'driver', 'active', '2025-04-25 02:25:37', '1745522737_IMG_20210627_204638_edit_75888082282169 (1).jpg', 'ไทย'),
(58, '020425-20', 'thepsak', '1100649495954', 'เทพศักดิ์', 'โชคสมัย', 'male', '21', 'พุทธ', '2003-11-20', '2025-04-02', '0841567987', '0894566789', '88/12 ถนนลาดพร้าว ซอย 64 แขวงวังทองหลาง เขตวังทองหลาง กรุงเทพมหานคร 10310', '88/12 ถนนลาดพร้าว ซอย 64 แขวงวังทองหลาง เขตวังทองหลาง กรุงเทพมหานคร 10310', 'driver', 'active', '2025-04-25 02:29:38', '1745522978_IMG_20210627_204638_edit_75888082282169 (1).jpg', 'ไทย'),
(59, '150225-21', 'rangsiman', '1104984984980', 'รังสิมันต์', 'เจริญชัย', 'male', '26', 'พุทธ', '1999-05-25', '2025-02-15', '0875465465', '0864849845', '55/7 ถนนเจริญกรุง แขวงบางรัก เขตบางรัก กรุงเทพมหานคร 10500', '55/7 ถนนเจริญกรุง แขวงบางรัก เขตบางรัก กรุงเทพมหานคร 10500', 'driver', 'active', '2025-04-25 02:31:31', '1745523091_IMG_20210627_204638_edit_75888082282169 (1).jpg', 'ไทย');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_outsourced_bookings`
--

CREATE TABLE `tbl_outsourced_bookings` (
  `outsourced_booking_id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_outsourced_bookings`
--

INSERT INTO `tbl_outsourced_bookings` (`outsourced_booking_id`, `reservation_id`, `price`, `attachment`, `created_at`) VALUES
(23, 213, 1000.00, '1745479063_ba154685-db18-4ac7-b318-a4a2b15b9d4c.jpg', '2025-04-24 14:17:43');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_password_resets`
--

CREATE TABLE `tbl_password_resets` (
  `password_reset_id` int(5) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expire_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_reservations`
--

CREATE TABLE `tbl_reservations` (
  `reservation_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ucount` varchar(10) DEFAULT NULL,
  `rdate` datetime DEFAULT NULL,
  `status` varchar(10) NOT NULL,
  `bdate` datetime NOT NULL,
  `end_time` datetime DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbl_reservations`
--

INSERT INTO `tbl_reservations` (`reservation_id`, `user_id`, `ucount`, `rdate`, `status`, `bdate`, `end_time`, `address`) VALUES
(204, 63, '987986554', '2025-04-20 20:00:00', 'APPROVED', '2025-04-20 18:50:17', '2025-04-20 21:30:00', '99 คอนโดสยาม แขวงดินแดง เขตดินแดง 12065'),
(212, 78, '0899870987', '2025-04-25 09:00:00', 'SUCCESS', '2025-04-24 13:30:44', '2025-04-25 10:30:00', '99 คอนโดมอนเต้ หัวหมาก บางกะปิ 10240'),
(213, 78, '0894561231', '2025-04-25 12:00:00', 'OUTSOURCE', '2025-04-24 14:10:35', '2025-04-25 13:30:00', '99 คอนโดมอนเต้ หัวหมาก บางกะปิ 10240'),
(214, 78, '0498984984', '2025-04-26 09:00:00', 'APPROVED', '2025-04-25 01:02:43', '2025-04-26 10:30:00', '99 คอนโดมอนเต้ หัวหมาก บางกะปิ 10240');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_revenue_updates`
--

CREATE TABLE `tbl_revenue_updates` (
  `revenue_id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `booking_date` datetime NOT NULL,
  `updated_amount` decimal(10,2) NOT NULL,
  `item_description` varchar(255) DEFAULT NULL,
  `quantity` int(3) DEFAULT 1,
  `unit_price` decimal(10,2) DEFAULT 0.00,
  `withholding_tax` tinyint(1) DEFAULT 0,
  `issued_by` int(3) DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `receipt_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_trucks`
--

CREATE TABLE `tbl_trucks` (
  `truck_id` int(5) NOT NULL,
  `model` varchar(100) NOT NULL,
  `plate_number` varchar(20) NOT NULL,
  `capacity` int(4) NOT NULL,
  `status` enum('available','unavailable') NOT NULL DEFAULT 'available',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_trucks`
--

INSERT INTO `tbl_trucks` (`truck_id`, `model`, `plate_number`, `capacity`, `status`, `created_at`) VALUES
(2, 'toyota', '60789', 5000, 'available', '2025-04-11 15:20:11'),
(3, 'toyota', '70600', 5000, 'available', '2025-04-23 22:00:44'),
(4, 'toyota', '16066', 5000, 'available', '2025-04-23 22:01:15'),
(5, 'toyota', '70156', 5000, 'available', '2025-04-24 21:49:59');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users`
--

CREATE TABLE `tbl_users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `pwd` varchar(50) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(10) NOT NULL,
  `email` varchar(100) NOT NULL,
  `type` varchar(8) NOT NULL,
  `status` varchar(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_users`
--

INSERT INTO `tbl_users` (`user_id`, `first_name`, `last_name`, `name`, `pwd`, `address`, `phone`, `email`, `type`, `status`) VALUES
(1, 'admin', '(ผู้ดูแลระบบ)', 'admin01', '123456', '123 Bangkok Rd.', '0899999999', 'admin@example.com', 'admin', 'active'),
(60, 'ลูกค้า', '01', 'user01', '123456', '201 เดอะคอนเนค ซอยประเวศ แขวงประเวศ เขตประเวศ 10450', '0896453412', 'warakorn.chsm@gmail.com', 'user', 'active'),
(61, 'เจ้าของ', '(owner)', 'owner01', '123456', '988 หมู่บ้าน ซอยรามคำแหง46 แขวงหัวหมาก เขตบางกะปิ 10240', '0894631567', 'sam.warakorn@gmail.com', 'owner', 'active'),
(79, 'วิศวพงศ์', 'พัวพันธ์', 'visawapong', '123456', '102 คอนโดดินแดง ถนนดินแดง แขวงดินแดง เขตดินแดง กรุงเทพฯ 11003', '0994564321', 'visawapong@gmail.com', 'driver', 'active'),
(80, 'วรฤทธิ์', 'อิ่มสำราญ', 'woralite', '123456', '33/102 หมู่บ้านบ้านครัว ถ.เจริญผล แขวงพญาไท เขตราชเทวี กรุงเทพฯ 10456', '0642151919', 'woralite@gmail.com', 'driver', 'active'),
(81, 'นรากร', 'แสงประเสริญฐ์', 'narakorn', '123456', '304 หมู่บ้านรามอินทรา ถนนนวมิล แขวงนวมิล เขตนวลจันทร์ 11405', '0695984645', 'narakorn@gmail.com', 'employee', 'active'),
(87, 'ณัฐวุฒิ', 'ศิริชัย', 'nattawut', '123456', '101 คอนโดมอนเต้ รามคำแหง12 หัวหมาก บางกะปิ 10240', '0823654687', 'Nattawut@gmail.com', 'user', 'active'),
(89, 'วรากร', 'โชคสมัย', 'samdriver', '123456', '99 เดอะคอนเนค1 ประเวศ ประเวศ 10400', '0806454894', 'sam@gmail.com', 'driver', 'active'),
(91, 'วีรยุทธ', 'ทองหลอม', 'veerayuth', '123456', '123/45 ถนนสุขุมวิท แขวงคลองตันเหนือ เขตวัฒนา กรุงเทพมหานคร 10110', '0845646545', 'veerayuth@gmail.com', 'driver', 'active'),
(92, 'เทพศักดิ์', 'โชคสมัย', 'thepsak', '123456', '88/12 ถนนลาดพร้าว ซอย 64 แขวงวังทองหลาง เขตวังทองหลาง กรุงเทพมหานคร 10310', '0841567987', 'thepsak@gmail.com', 'driver', 'active'),
(93, 'รังสิมันต์', 'เจริญชัย', 'rangsiman', '123456', '55/7 ถนนเจริญกรุง แขวงบางรัก เขตบางรัก กรุงเทพมหานคร 10500', '0875465465', 'rangsiman@gmail.com', 'driver', 'active'),
(94, 'พัชรี', 'วงศ์กาญจนา', 'patcharee', '123456', '88/19 ถนนประชาชื่น แขวงบางซื่อ เขตบางซื่อ กรุงเทพมหานคร 10800', '0845465489', 'patcharee@gmail.com', 'user', 'active'),
(95, 'กิตติพงษ์', 'รัตนสกุล', 'Kittipong', '123456', '45/3 ซอยสุขุมวิท 39 แขวงคลองตันเหนือ เขตวัฒนา กรุงเทพมหานคร 10110', '0898794564', 'Kittipong@gmail.com', 'user', 'active'),
(96, 'ณัฐธิดา', 'ชัยมงคล', 'natthida', '123456', '103/2 ถนนรัชดาภิเษก แขวงดินแดง เขตดินแดง กรุงเทพมหานคร 10400', '0845614984', 'natthida@gmail.com', 'user', 'active'),
(97, 'ศุภกร', 'แสงทอง', 'supakorn', '123456', '67/9 ถนนพหลโยธิน แขวงสามเสนใน เขตพญาไท กรุงเทพมหานคร 10400', '0984564534', 'supakorn@gmail.com', 'user', 'active'),
(98, 'ธนภัทร', 'เลิศวัฒนกิจ', 'thanaphat', '123456', '22/1 ถนนเจริญนคร แขวงบางลำภูล่าง เขตคลองสาน กรุงเทพมหานคร 10600', '0898797979', 'thanaphat@gmail.com', 'user', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_assigned_tasks`
--
ALTER TABLE `tbl_assigned_tasks`
  ADD PRIMARY KEY (`assigned_task_id`),
  ADD KEY `reservation_id` (`reservation_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `truck_id` (`truck_id`);

--
-- Indexes for table `tbl_employees`
--
ALTER TABLE `tbl_employees`
  ADD PRIMARY KEY (`employee_id`);

--
-- Indexes for table `tbl_outsourced_bookings`
--
ALTER TABLE `tbl_outsourced_bookings`
  ADD PRIMARY KEY (`outsourced_booking_id`),
  ADD KEY `booking_id` (`reservation_id`);

--
-- Indexes for table `tbl_password_resets`
--
ALTER TABLE `tbl_password_resets`
  ADD PRIMARY KEY (`password_reset_id`);

--
-- Indexes for table `tbl_reservations`
--
ALTER TABLE `tbl_reservations`
  ADD PRIMARY KEY (`reservation_id`);

--
-- Indexes for table `tbl_revenue_updates`
--
ALTER TABLE `tbl_revenue_updates`
  ADD PRIMARY KEY (`revenue_id`),
  ADD KEY `tbl_revenue_updates_ibfk_1` (`reservation_id`);

--
-- Indexes for table `tbl_trucks`
--
ALTER TABLE `tbl_trucks`
  ADD PRIMARY KEY (`truck_id`);

--
-- Indexes for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_assigned_tasks`
--
ALTER TABLE `tbl_assigned_tasks`
  MODIFY `assigned_task_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `tbl_employees`
--
ALTER TABLE `tbl_employees`
  MODIFY `employee_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `tbl_outsourced_bookings`
--
ALTER TABLE `tbl_outsourced_bookings`
  MODIFY `outsourced_booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `tbl_password_resets`
--
ALTER TABLE `tbl_password_resets`
  MODIFY `password_reset_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `tbl_reservations`
--
ALTER TABLE `tbl_reservations`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=215;

--
-- AUTO_INCREMENT for table `tbl_revenue_updates`
--
ALTER TABLE `tbl_revenue_updates`
  MODIFY `revenue_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `tbl_trucks`
--
ALTER TABLE `tbl_trucks`
  MODIFY `truck_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=426;

--
-- AUTO_INCREMENT for table `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_assigned_tasks`
--
ALTER TABLE `tbl_assigned_tasks`
  ADD CONSTRAINT `tbl_assigned_tasks_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `tbl_reservations` (`reservation_id`),
  ADD CONSTRAINT `tbl_assigned_tasks_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `tbl_employees` (`employee_id`),
  ADD CONSTRAINT `tbl_assigned_tasks_ibfk_3` FOREIGN KEY (`truck_id`) REFERENCES `tbl_trucks` (`truck_id`);

--
-- Constraints for table `tbl_outsourced_bookings`
--
ALTER TABLE `tbl_outsourced_bookings`
  ADD CONSTRAINT `tbl_outsourced_bookings_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `tbl_reservations` (`reservation_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
