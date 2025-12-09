<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #3490dc;
            --secondary-color: #f8fafc;
            --accent-color: #2779bd;
            --text-color: #2d3748;
            --light-gray: #e2e8f0;
        }
        
        body {
            font-family: 'Kanit', 'Prompt', sans-serif;
            background-color: var(--secondary-color);
            margin: 0;
            padding: 0;
            color: var(--text-color);
            line-height: 1.6;
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
        
        /* Hero Section - NEW DESIGN */
        .hero {
            height: 500px;
            margin-top: 76px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #1a5fb4 0%, #38b2ac 100%);
        }
        
        /* Cool geometric pattern overlay */
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 30% 20%, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0) 8%),
                radial-gradient(circle at 70% 60%, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0) 8%),
                radial-gradient(circle at 50% 50%, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 20%),
                repeating-linear-gradient(45deg, rgba(255, 255, 255, 0.05) 0px, rgba(255, 255, 255, 0.05) 2px, transparent 2px, transparent 10px);
            opacity: 0.8;
        }
        
        /* Floating tools icons */
        .floating-icons {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none; /* ทำให้คลิกผ่าน icon ได้ */
        }
        
        .floating-icon {
            position: absolute;
            color: rgba(255, 255, 255, 0.2);
            animation: float 6s infinite ease-in-out;
            font-size: 30px;
        }
        
        /* ปรับตำแหน่งสำหรับไอคอนทั้งหมด */
        .floating-icon:nth-child(1) { top: 15%; left: 10%; animation-delay: 0s; }
        .floating-icon:nth-child(2) { top: 70%; left: 15%; animation-delay: 1.2s; }
        .floating-icon:nth-child(3) { top: 25%; left: 25%; animation-delay: 2.3s; }
        .floating-icon:nth-child(4) { top: 60%; left: 30%; animation-delay: 3.1s; }
        .floating-icon:nth-child(5) { top: 40%; left: 5%; animation-delay: 4.5s; }
        .floating-icon:nth-child(6) { top: 20%; left: 40%; animation-delay: 0.7s; }
        .floating-icon:nth-child(7) { top: 80%; left: 45%; animation-delay: 1.5s; }
        .floating-icon:nth-child(8) { top: 30%; left: 60%; animation-delay: 2.7s; }
        .floating-icon:nth-child(9) { top: 65%; left: 65%; animation-delay: 3.4s; }
        .floating-icon:nth-child(10) { top: 45%; left: 75%; animation-delay: 4.2s; }
        .floating-icon:nth-child(11) { top: 10%; left: 80%; animation-delay: 0.3s; }
        .floating-icon:nth-child(12) { top: 75%; left: 85%; animation-delay: 1.8s; }
        .floating-icon:nth-child(13) { top: 35%; left: 90%; animation-delay: 2.1s; }
        .floating-icon:nth-child(14) { top: 50%; left: 95%; animation-delay: 3.7s; }
        .floating-icon:nth-child(15) { top: 85%; left: 5%; animation-delay: 4.8s; }
        .floating-icon:nth-child(16) { top: 5%; left: 35%; animation-delay: 0.5s; }
        .floating-icon:nth-child(17) { top: 90%; left: 25%; animation-delay: 1.3s; }
        .floating-icon:nth-child(18) { top: 15%; left: 70%; animation-delay: 2.6s; }
        .floating-icon:nth-child(19) { top: 55%; left: 50%; animation-delay: 3.2s; }
        .floating-icon:nth-child(20) { top: 75%; left: 55%; animation-delay: 4.1s; }
        .floating-icon:nth-child(21) { top: 25%; left: 85%; animation-delay: 0.9s; }
        .floating-icon:nth-child(22) { top: 60%; left: 80%; animation-delay: 1.7s; }
        .floating-icon:nth-child(23) { top: 38%; left: 20%; animation-delay: 2.9s; }
        .floating-icon:nth-child(24) { top: 82%; left: 35%; animation-delay: 3.5s; }
        .floating-icon:nth-child(25) { top: 48%; left: 65%; animation-delay: 4.6s; }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
            }
            25% {
                transform: translateY(-15px) rotate(3deg);
            }
            50% {
                transform: translateY(-25px) rotate(5deg);
            }
            75% {
                transform: translateY(-10px) rotate(2deg);
            }
        }
        
        .hero-content {
            z-index: 2;
            max-width: 800px;
            padding: 0 20px;
            position: relative;
        }
        
        .hero h1 {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            background: linear-gradient(to right, #ffffff, #f0f0f0);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        
        .hero p {
            font-size: 20px;
            margin-bottom: 30px;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
        }
        
        .hero-btn {
            display: inline-block;
            background: white;
            color: var(--primary-color);
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .hero-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            background: #f8f9fa;
            color: var(--accent-color);
        }
        
        /* Services Section */
        .services-section {
            padding: 80px 0;
            background-color: white;
        }
        
        .section-title {
            text-align: center;
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--text-color);
            font-size: 24px;
        }
        
        .service-card {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            margin-bottom: 30px;
        }
        
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .service-icon {
            font-size: 36px;
            color: var(--primary-color);
            margin-bottom: 15px;
        }
        
        .service-card .card-body {
            padding: 25px;
        }
        
        /* Carousel */
        .carousel {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-top: 20px;
        }
        
        .carousel-inner img {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }
        
        .carousel-indicators {
            bottom: 10px;
        }
        
        .carousel-indicators button {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin: 0 5px;
        }
        
        .carousel-control-prev, .carousel-control-next {
            width: 40px;
            height: 40px;
            background-color: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .carousel:hover .carousel-control-prev,
        .carousel:hover .carousel-control-next {
            opacity: 1;
        }
        
        /* Footer */
        footer {
            background-color: var(--text-color);
            color: white;
            padding: 40px 0 20px;
            margin-top: 50px;
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
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top"> 
        <div class="container">
            <a class="navbar-brand" href="#">TANAWAT</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="/event-management/views/calendar_standalone.php" role="button">Calendar</a></li>
                    <li class="nav-item"><a class="nav-link" href="/event-management/login.php" role="button">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Hero Section - NEW DESIGN -->
    <section class="hero">
        <!-- Floating icons - เพิ่มจำนวนไอคอนให้มากขึ้น -->
        <div class="floating-icons">
            <i class="floating-icon fas fa-truck"></i>
            <i class="floating-icon fas fa-toilet"></i> 
            <i class="floating-icon fas fa-water"></i> 
            <i class="floating-icon fas fa-calendar-check"></i> 
            <i class="floating-icon fas fa-phone-alt"></i>
            <i class="floating-icon fas fa-wrench"></i>
            <i class="floating-icon fas fa-shower"></i>
            <i class="floating-icon fas fa-hard-hat"></i>
            <i class="floating-icon fas fa-pump-soap"></i>
            <i class="floating-icon fas fa-sink"></i>
            <i class="floating-icon fas fa-faucet"></i>
            <i class="floating-icon fas fa-tools"></i>
            <i class="floating-icon fas fa-house"></i>
            <i class="floating-icon fas fa-building"></i>
            <i class="floating-icon fas fa-industry"></i>
            <i class="floating-icon fas fa-droplet"></i>
            <i class="floating-icon fas fa-screwdriver-wrench"></i>
            <i class="floating-icon fas fa-check-circle"></i>
            <i class="floating-icon fas fa-map-marker-alt"></i>
            <i class="floating-icon fas fa-thumbs-up"></i>
            <i class="floating-icon fas fa-clipboard-check"></i>
            <i class="floating-icon fas fa-comment-dots"></i>
            <i class="floating-icon fas fa-star"></i>
            <i class="floating-icon fas fa-truck-container"></i>
            <i class="floating-icon fas fa-recycle"></i>
        </div>
        
        <div class="hero-content">
            <h1>รถดูดสิ่งปฏิกูล</h1>
            <p>เรามีประสบการณ์มากกว่า 10 ปี ให้บริการดูดส้วมที่รวดเร็ว สะอาด และเป็นมืออาชีพ
ครอบคลุมทั้งบ้านพักอาศัย อาคารพาณิชย์ และโรงงาน
จองง่าย บริการถึงที่ ราคาเป็นมิตร ไว้วางใจเรา</p>
            <a href="tel:0972951874" class="hero-btn"> ติดต่อเรา <i class="fas fa-arrow-right ms-2"></i></a>

        </div>
    </section>
    
    <!-- Services Section -->
    <section class="services-section">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4">
                    <div class="service-card">
                        <div class="card-body">
                            <div class="service-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h3 class="section-title">บริการด่วน ตลอด 24 ชั่วโมง</h3>
                            <p>พร้อมให้บริการทุกที่ ทุกเวลา แม้ในยามฉุกเฉิน</p>
                            <div id="carousel1" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-indicators">
                                    <button type="button" data-bs-target="#carousel1" data-bs-slide-to="0" class="active"></button>
                                    <button type="button" data-bs-target="#carousel1" data-bs-slide-to="1"></button>
                                    <button type="button" data-bs-target="#carousel1" data-bs-slide-to="2"></button>
                                </div>
                                <div class="carousel-inner">
                                    <div class="carousel-item active">
                                        <img src="assets/images/homepage.jpg" alt="Our Services">
                                    </div>
                                    <div class="carousel-item">
                                        <img src="assets/images/homepagee.jpg" alt="Our Services">
                                    </div>
                                    <div class="carousel-item">
                                        <img src="assets/images/homepageee.jpg" alt="Our Services">
                                    </div>
                                </div>
                                <button class="carousel-control-prev" type="button" data-bs-target="#carousel1" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon"></span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#carousel1" data-bs-slide="next">
                                    <span class="carousel-control-next-icon"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="service-card">
                        <div class="card-body">
                            <div class="service-icon">
                                <i class="fas fa-hand-holding-usd"></i>
                            </div>
                            <h3 class="section-title">ราคาคุยง่าย เป็นกันเอง</h3>
                            <p>บริการด้วยราคาที่ยุติธรรม โปร่งใส เป็นกันเอง</p>
                            <div id="carousel2" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-indicators">
                                    <button type="button" data-bs-target="#carousel2" data-bs-slide-to="0" class="active"></button>
                                    <button type="button" data-bs-target="#carousel2" data-bs-slide-to="1"></button>
                                    <button type="button" data-bs-target="#carousel2" data-bs-slide-to="2"></button>
                                </div>
                                <div class="carousel-inner">
                                    <div class="carousel-item active">
                                        <img src="assets/images/homepage1.jpg" alt="Client Testimonials">
                                    </div>
                                    <div class="carousel-item">
                                        <img src="assets/images/homepage11.jpg" alt="Client Testimonials">
                                    </div>
                                    <div class="carousel-item">
                                        <img src="assets/images/homepage111.jpg" alt="Client Testimonials">
                                    </div>
                                </div>
                                <button class="carousel-control-prev" type="button" data-bs-target="#carousel2" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon"></span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#carousel2" data-bs-slide="next">
                                    <span class="carousel-control-next-icon"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="service-card">
                        <div class="card-body">
                            <div class="service-icon">
                                <i class="fas fa-tools"></i>
                            </div>
                            <h3 class="section-title">บริการโดยช่างผู้ชำนาญ</h3>
                            <p>ทีมงานมืออาชีพที่ผ่านการฝึกอบรมและมีประสบการณ์สูง</p>
                            <div id="carousel3" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-indicators">
                                    <button type="button" data-bs-target="#carousel3" data-bs-slide-to="0" class="active"></button>
                                    <button type="button" data-bs-target="#carousel3" data-bs-slide-to="1"></button>
                                    <button type="button" data-bs-target="#carousel3" data-bs-slide-to="2"></button>
                                </div>
                                <div class="carousel-inner">
                                    <div class="carousel-item active">
                                        <img src="assets/images/homepage2.jpg" alt="Portfolio">
                                    </div>
                                    <div class="carousel-item">
                                        <img src="assets/images/homepage22.jpg" alt="Portfolio">
                                    </div>
                                    <div class="carousel-item">
                                        <img src="assets/images/homepage222.jpg" alt="Portfolio">
                                    </div>
                                </div>
                                <button class="carousel-control-prev" type="button" data-bs-target="#carousel3" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon"></span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#carousel3" data-bs-slide="next">
                                    <span class="carousel-control-next-icon"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4 footer-links">
                    <h5>ติดต่อเรา</h5>
                    <ul>
                        <li><i class="fas fa-phone me-2"></i> 097-295-1874</li>
                        <li><i class="fas fa-envelope me-2"></i> fah.nichapat1312@gmail.com</li>
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
                        <a href="mailto:fah.nichapat1312@gmail.com" class="me-3">
    <i class="fas fa-envelope fa-2x"></i>
</a>

                    </div>
                </div>
            </div>
            <div class="copyright">
                &copy; 2025 TANAWAT. All Rights Reserved.
            </div>
        </div>
    </footer>
</body>
</html>