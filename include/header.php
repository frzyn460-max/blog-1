<?php
/**
 * فایل هدر سایت - نسخه پیشرفته
 * شامل منو، اسلایدر و تنظیمات اولیه با طراحی فوق‌العاده
 */

// شروع session با بررسی
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// فراخوانی فایل‌های پیکربندی
require_once(__DIR__ . "/config.php");
require_once(__DIR__ . "/db.php");

// محاسبه تعداد آیتم‌های سبد خرید
$cart_items = $_SESSION['cart'] ?? [];
$total_cart_count = array_sum($cart_items);

// دریافت تصاویر اسلایدر به صورت امن
$posts_slider = fetchAll($db, "SELECT * FROM img");

// دریافت دسته‌بندی‌ها
$categories = fetchAll($db, "SELECT * FROM categories ORDER BY title ASC");

// تولید توکن CSRF
$csrf_token = generate_csrf_token();

// تشخیص صفحه فعلی برای فعال کردن منو
$current_page = $_GET['page'] ?? 'home';
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="کتاب نت - فروشگاه آنلاین کتاب با بهترین قیمت و تنوع">
    <meta name="keywords" content="کتاب، خرید کتاب، فروشگاه کتاب، کتاب فارسی">
    <meta name="author" content="<?= escape(SITE_NAME) ?>">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?= escape(SITE_NAME) ?>">
    <meta property="og:description" content="فروشگاه آنلاین کتاب">
    <meta property="og:type" content="website">
    
    <title><?= escape(SITE_NAME) ?> - فروشگاه آنلاین کتاب</title>
    
    <!-- فونت فارسی -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    
    <style>
        /* ===== ریست و تنظیمات پایه ===== */
        @import url('https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            /* رنگ‌های Light Mode */
            --bg-primary: #ffffff;
            --bg-secondary: #f8f9fa;
            --text-primary: #1a202c;
            --text-secondary: #4a5568;
            --accent-primary: #3b82f6;
            --accent-hover: #2563eb;
            --accent-light: #dbeafe;
            --border-color: rgba(0, 0, 0, 0.08);
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.08);
            --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.12);
            --shadow-lg: 0 10px 40px rgba(0, 0, 0, 0.15);
            --gradient-1: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-2: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --gradient-3: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        body {
            font-family: 'Vazirmatn', Tahoma, Arial, sans-serif;
            background: #f5f7fa;
            color: var(--text-primary);
            line-height: 1.6;
            transition: background-color 0.3s ease;
            min-height: 100vh;
        }

        /* ===== Dark Mode ===== */
        body.dark-mode {
            --bg-primary: #1e293b;
            --bg-secondary: #0f172a;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --accent-primary: #60a5fa;
            --accent-hover: #3b82f6;
            --accent-light: #1e3a8a;
            --border-color: rgba(255, 255, 255, 0.1);
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.3);
            --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.4);
            --shadow-lg: 0 10px 40px rgba(0, 0, 0, 0.5);
            background: #0f172a;
        }

        body.dark-mode .custom-carousel::before {
            background: linear-gradient(180deg, 
                rgba(15, 23, 42, 0.95) 0%, 
                rgba(15, 23, 42, 0.7) 40%,
                transparent 100%);
        }

        /* ===== اسلایدر فوق‌العاده ===== */
        .custom-carousel {
            position: relative;
            width: 100%;
            height: 100vh;
            overflow: hidden;
            border-radius: 0;
        }

        /* اورلی گرادینت برای خوانایی بهتر */
        .custom-carousel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(180deg, 
                rgba(0, 0, 0, 0.6) 0%, 
                rgba(0, 0, 0, 0.3) 40%,
                transparent 100%);
            z-index: 2;
            pointer-events: none;
        }

        .carousel-inner {
            position: relative;
            width: 100%;
            height: 100%;
        }

        .carousel-item {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .carousel-item.active {
            opacity: 1;
            z-index: 1;
        }

        .carousel-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            animation: kenburns 20s ease infinite;
        }

        /* انیمیشن Ken Burns برای تصاویر */
        @keyframes kenburns {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }

        /* دکمه‌های ناوبری اسلایدر - طراحی جدید */
        .nav-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 2px solid rgba(255, 255, 255, 0.2);
            color: white;
            font-size: 24px;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border-radius: 50%;
            z-index: 100;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }

        .nav-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-50%) scale(1.15);
            box-shadow: 0 12px 48px rgba(0, 0, 0, 0.3);
        }

        .nav-btn:active {
            transform: translateY(-50%) scale(0.95);
        }

        .prev { left: 30px; }
        .next { right: 30px; }

        /* اندیکاتورهای اسلایدر - طراحی جدید */
        .carousel-indicators {
            position: absolute;
            bottom: 40px;
            width: 100%;
            text-align: center;
            z-index: 100;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
        }

        .carousel-indicators button {
            width: 12px;
            height: 12px;
            background: rgba(255, 255, 255, 0.4);
            border: 2px solid rgba(255, 255, 255, 0.6);
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 0;
        }

        .carousel-indicators button:hover {
            background: rgba(255, 255, 255, 0.6);
            transform: scale(1.2);
        }

        .carousel-indicators button.active {
            background: white;
            width: 50px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(255, 255, 255, 0.4);
        }

        /* ===== منوی حرفه‌ای ===== */
        nav.navbar {
            position: absolute;
            top: 0;
            right: 0;
            left: 0;
            background: transparent;
            padding: 1.5rem 2rem;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        /* منوی چسبنده با اسکرول */
        nav.navbar.scrolled {
            position: fixed;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 1rem 2rem;
        }

        body.dark-mode nav.navbar.scrolled {
            background: rgba(30, 41, 59, 0.95);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
        }

        nav.navbar.scrolled .navbar-brand,
        nav.navbar.scrolled .navbar-nav a,
        nav.navbar.scrolled .dark-mode-toggle {
            color: var(--text-primary);
        }

        nav .container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        /* لوگو - طراحی جدید */
        .navbar-brand {
            color: white;
            font-size: 2rem;
            font-weight: 900;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s ease;
            position: relative;
        }

        .navbar-brand::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 3px;
            background: var(--gradient-1);
            transition: width 0.3s ease;
            border-radius: 2px;
        }

        .navbar-brand:hover::after {
            width: 100%;
        }

        .logo-icon {
            font-size: 2.2rem;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.3));
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }

        /* گروه راست منو */
        .navbar-right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        /* آیکون سبد خرید - طراحی جدید */
        .cart-icon {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 15px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        nav.navbar.scrolled .cart-icon {
            background: var(--accent-light);
            border-color: var(--accent-primary);
        }

        .cart-icon:hover {
            transform: translateY(-3px) rotate(-5deg);
            box-shadow: 0 8px 24px rgba(59, 130, 246, 0.4);
        }

        .cart-img {
            width: 24px;
            height: 24px;
            filter: brightness(0) invert(1);
            transition: filter 0.3s ease;
        }

        nav.navbar.scrolled .cart-img {
            filter: none;
        }

        body.dark-mode nav.navbar.scrolled .cart-img {
            filter: brightness(0) invert(1);
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            font-size: 0.7rem;
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.5);
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            min-width: 20px;
            text-align: center;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.1);
                opacity: 0.9;
            }
        }

        /* دکمه همبرگر موبایل - طراحی جدید */
        .navbar-toggler {
            display: none;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 2px solid rgba(255, 255, 255, 0.2);
            color: white;
            font-size: 1.5rem;
            width: 50px;
            height: 50px;
            border-radius: 15px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .navbar-toggler:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: rotate(90deg);
        }

        /* منوی اصلی */
        #my-nav {
            display: flex;
            align-items: center;
            gap: 2.5rem;
            flex: 1;
            justify-content: center;
        }

        ul.navbar-nav {
            list-style: none;
            display: flex;
            gap: 0.5rem;
            margin: 0;
            padding: 0;
            align-items: center;
        }

        ul.navbar-nav li a {
            color: white;
            text-decoration: none;
            padding: 0.8rem 1.5rem;
            border-radius: 15px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        ul.navbar-nav li a::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.2);
            transition: left 0.4s ease;
        }

        ul.navbar-nav li a:hover::before {
            left: 0;
        }

        ul.navbar-nav li a:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        /* منوی فعال */
        ul.navbar-nav li.nav-item.active a {
            background: rgba(255, 255, 255, 0.25);
            box-shadow: 0 8px 24px rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.4);
        }

        /* استایل منو در حالت اسکرول */
        nav.navbar.scrolled ul.navbar-nav li a {
            color: var(--text-primary);
            background: transparent;
            border: 1px solid transparent;
        }

        nav.navbar.scrolled ul.navbar-nav li a:hover {
            background: var(--accent-light);
            color: var(--accent-primary);
        }

        nav.navbar.scrolled ul.navbar-nav li.active a {
            background: var(--accent-primary);
            color: white;
        }

        /* دکمه دارک مود - طراحی جدید */
        .dark-mode-toggle {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 2px solid rgba(255, 255, 255, 0.2);
            color: white;
            font-size: 24px;
            width: 50px;
            height: 50px;
            border-radius: 15px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        nav.navbar.scrolled .dark-mode-toggle {
            background: var(--accent-light);
            border-color: var(--accent-primary);
            color: var(--accent-primary);
        }

        .dark-mode-toggle::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: all 0.5s ease;
        }

        .dark-mode-toggle:hover::before {
            width: 100px;
            height: 100px;
        }

        .dark-mode-toggle:hover {
            transform: rotate(180deg) scale(1.1);
            box-shadow: 0 8px 24px rgba(59, 130, 246, 0.4);
        }

        #darkModeIcon {
            position: relative;
            z-index: 1;
            font-size: 1.5rem;
        }

        /* دکمه‌های ورود/ثبت‌نام - طراحی جدید */
        .auth-buttons {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .auth-buttons .btn {
            padding: 0.8rem 1.5rem;
            font-size: 0.95rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            border: 2px solid rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            color: white;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            border-radius: 15px;
            position: relative;
            overflow: hidden;
        }

        .auth-buttons .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.2);
            transition: left 0.3s ease;
        }

        .auth-buttons .btn:hover::before {
            left: 0;
        }

        .auth-buttons .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        .btn-login {
            background: rgba(59, 130, 246, 0.3);
            border-color: rgba(59, 130, 246, 0.5);
        }

        .btn-register {
            background: rgba(239, 68, 68, 0.3);
            border-color: rgba(239, 68, 68, 0.5);
        }

        nav.navbar.scrolled .auth-buttons .btn {
            color: var(--text-primary);
        }

        nav.navbar.scrolled .btn-login {
            background: var(--accent-primary);
            color: white;
            border-color: var(--accent-primary);
        }

        nav.navbar.scrolled .btn-register {
            background: transparent;
            color: var(--accent-primary);
            border-color: var(--accent-primary);
        }

        .btn-icon {
            width: 18px;
            height: 18px;
            fill: currentColor;
        }

        /* ===== Responsive ===== */
        @media (max-width: 991px) {
            nav.navbar {
                padding: 1rem 1.5rem;
            }

            .navbar-toggler {
                display: flex;
            }

            #my-nav {
                display: none;
                width: 100%;
                background: rgba(30, 41, 59, 0.98);
                backdrop-filter: blur(30px);
                -webkit-backdrop-filter: blur(30px);
                margin-top: 1rem;
                padding: 1.5rem;
                border-radius: 20px;
                flex-direction: column;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            }

            #my-nav.show {
                display: flex;
                animation: slideDown 0.4s ease;
            }

            @keyframes slideDown {
                from {
                    opacity: 0;
                    transform: translateY(-20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            ul.navbar-nav {
                flex-direction: column;
                width: 100%;
                gap: 0.8rem;
            }

            ul.navbar-nav li a {
                width: 100%;
                justify-content: center;
                color: white;
                background: rgba(255, 255, 255, 0.1);
            }

            .auth-buttons {
                width: 100%;
                justify-content: center;
                flex-wrap: wrap;
                gap: 1rem;
            }

            .auth-buttons .btn {
                flex: 1;
                min-width: 150px;
                justify-content: center;
            }

            .custom-carousel {
                height: 80vh;
            }

            .nav-btn {
                width: 50px;
                height: 50px;
                font-size: 20px;
            }

            .prev { left: 20px; }
            .next { right: 20px; }

            .navbar-brand {
                font-size: 1.5rem;
            }

            .logo-icon {
                font-size: 1.7rem;
            }
        }

        @media (max-width: 576px) {
            nav.navbar {
                padding: 1rem;
            }

            .navbar-brand {
                font-size: 1.3rem;
            }

            .custom-carousel {
                height: 70vh;
            }

            .nav-btn {
                width: 45px;
                height: 45px;
                font-size: 18px;
            }

            .prev { left: 15px; }
            .next { right: 15px; }

            .carousel-indicators {
                bottom: 20px;
            }

            .cart-icon,
            .dark-mode-toggle {
                width: 45px;
                height: 45px;
            }

            .navbar-toggler {
                width: 45px;
                height: 45px;
            }
        }

        /* بهبود عملکرد انیمیشن‌ها */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>
<body>

    <!-- اسلایدر اصلی -->
    <section class="custom-carousel" aria-label="اسلایدر تصاویر">
        
        <!-- منوی ناوبری -->
        <nav class="navbar" id="mainNavbar" role="navigation" aria-label="منوی اصلی">
            <div class="container">
                
                <!-- لوگو -->
                <a class="navbar-brand" href="index.php">
                    <span class="logo-icon">📚</span>
                    <span><?= escape(SITE_NAME) ?></span>
                </a>

                <!-- دکمه همبرگر -->
                <button class="navbar-toggler" aria-label="باز کردن منو" id="menuToggle">
                    <span>☰</span>
                </button>

                <!-- منوی اصلی -->
                <div id="my-nav">
                    <ul class="navbar-nav">
                        <li class="nav-item <?= $current_page === 'home' ? 'active' : '' ?>">
                            <a href="index.php?page=home">
                                <span>🏠</span>
                                صفحه اصلی
                            </a>
                        </li>
                        <li class="nav-item <?= $current_page === 'products' ? 'active' : '' ?>">
                            <a href="products.php?page=products">
                                <span>📦</span>
                                محصولات
                            </a>
                        </li>
                        <li class="nav-item <?= $current_page === 'posts' ? 'active' : '' ?>">
                            <a href="posts.php?page=posts">
                                <span>📝</span>
                                مقالات
                            </a>
                        </li>
                        <li class="nav-item <?= $current_page === 'about' ? 'active' : '' ?>">
                            <a href="about.php">
                                <span>ℹ️</span>
                                درباره ما
                            </a>
                        </li>
                    </ul>

                    <div class="auth-buttons">
                        <a href="login.php" class="btn btn-login">
                            <svg class="btn-icon" viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                            ورود
                        </a>
                        <a href="register.php" class="btn btn-register">
                            <svg class="btn-icon" viewBox="0 0 24 24">
                                <path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                            ثبت‌نام
                        </a>
                    </div>
                </div>

                <!-- گروه راست (سبد و دارک مود) -->
                <div class="navbar-right">
                    <!-- دکمه دارک مود -->
                    <button id="darkModeToggle" class="dark-mode-toggle" aria-label="تغییر حالت تاریک/روشن">
                        <span id="darkModeIcon">🌙</span>
                    </button>

                    <!-- سبد خرید -->
                    <a href="cart.php" class="cart-icon" aria-label="سبد خرید">
                        <img src="./img/PNG4.png" alt="سبد خرید" class="cart-img">
                        <?php if ($total_cart_count > 0): ?>
                            <span class="cart-count"><?= $total_cart_count ?></span>
                        <?php endif; ?>
                    </a>
                </div>

            </div>
        </nav>

        <!-- تصاویر اسلایدر -->
        <div class="carousel-inner">
            <?php if (!empty($posts_slider)): ?>
                <?php foreach ($posts_slider as $index => $img): ?>
                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                        <img src="./img1/<?= escape($img['img']) ?>" 
                             alt="اسلاید <?= $index + 1 ?>" 
                             loading="<?= $index === 0 ? 'eager' : 'lazy' ?>">
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- دکمه‌های قبل/بعد -->
        <button class="nav-btn prev" aria-label="اسلاید قبلی">❮</button>
        <button class="nav-btn next" aria-label="اسلاید بعدی">❯</button>

        <!-- اندیکاتورها -->
        <div class="carousel-indicators">
            <?php foreach ($posts_slider as $index => $img): ?>
                <button class="<?= $index === 0 ? 'active' : '' ?>" 
                        data-slide="<?= $index ?>" 
                        aria-label="اسلاید <?= $index + 1 ?>"></button>
            <?php endforeach; ?>
        </div>

    </section>

    <script>
        // اسکریپت اسلایدر و منو - بهبود یافته
        document.addEventListener('DOMContentLoaded', () => {
            
            // === منوی موبایل ===
            const toggler = document.getElementById('menuToggle');
            const menu = document.getElementById('my-nav');
            
            toggler?.addEventListener('click', () => {
                menu.classList.toggle('show');
            });

            // بستن منو با کلیک خارج از آن
            document.addEventListener('click', (e) => {
                if (!menu.contains(e.target) && !toggler.contains(e.target)) {
                    menu.classList.remove('show');
                }
            });

            // === اسلایدر ===
            const slides = document.querySelectorAll('.carousel-item');
            const indicators = document.querySelectorAll('.carousel-indicators button');
            const prevBtn = document.querySelector('.prev');
            const nextBtn = document.querySelector('.next');
            let currentIndex = 0;
            let autoPlayInterval;

            const showSlide = (index) => {
                slides.forEach(s => s.classList.remove('active'));
                indicators.forEach(i => i.classList.remove('active'));
                
                currentIndex = (index + slides.length) % slides.length;
                
                slides[currentIndex].classList.add('active');
                indicators[currentIndex].classList.add('active');
            };

            const nextSlide = () => showSlide(currentIndex + 1);
            const prevSlide = () => showSlide(currentIndex - 1);

            // اتوپلی
            const startAutoPlay = () => {
                autoPlayInterval = setInterval(nextSlide, 5000);
            };

            const stopAutoPlay = () => {
                clearInterval(autoPlayInterval);
            };

            prevBtn?.addEventListener('click', () => {
                prevSlide();
                stopAutoPlay();
                startAutoPlay();
            });

            nextBtn?.addEventListener('click', () => {
                nextSlide();
                stopAutoPlay();
                startAutoPlay();
            });

            indicators.forEach((indicator, i) => {
                indicator.addEventListener('click', () => {
                    showSlide(i);
                    stopAutoPlay();
                    startAutoPlay();
                });
            });

            // توقف اتوپلی هنگام hover
            const carousel = document.querySelector('.custom-carousel');
            carousel?.addEventListener('mouseenter', stopAutoPlay);
            carousel?.addEventListener('mouseleave', startAutoPlay);

            startAutoPlay();

            // === منوی Sticky با اسکرول ===
            const navbar = document.getElementById('mainNavbar');
            let lastScroll = 0;

            window.addEventListener('scroll', () => {
                const currentScroll = window.pageYOffset;

                if (currentScroll > 100) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }

                lastScroll = currentScroll;
            });

            // === دارک مود ===
            const darkToggle = document.getElementById('darkModeToggle');
            const darkIcon = document.getElementById('darkModeIcon');
            const body = document.body;

            // بررسی حالت ذخیره شده
            const savedMode = localStorage.getItem('darkMode');
            if (savedMode === 'enabled') {
                body.classList.add('dark-mode');
                darkIcon.textContent = '☀️';
            }

            darkToggle?.addEventListener('click', () => {
                body.classList.toggle('dark-mode');
                
                if (body.classList.contains('dark-mode')) {
                    localStorage.setItem('darkMode', 'enabled');
                    darkIcon.textContent = '☀️';
                } else {
                    localStorage.setItem('darkMode', 'disabled');
                    darkIcon.textContent = '🌙';
                }
            });

            // === بهبود عملکرد با Passive Event Listeners ===
            document.addEventListener('touchstart', () => {}, { passive: true });
            document.addEventListener('touchmove', () => {}, { passive: true });
        });
    </script>

</body>
</html>