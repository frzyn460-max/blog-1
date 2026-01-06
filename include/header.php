<?php
session_start();  // حتما باید باشه

include("./include/config.php");
include("./include/db.php");

$cart_items = $_SESSION['cart'] ?? [];
$total_cart_count = 0;
foreach ($cart_items as $quantity) {
    $total_cart_count += $quantity;
}

$query_slider = "SELECT * FROM img";
$posts_slider = $db->query($query_slider)->fetchAll();

$query = "SELECT * FROM categories";
$categories = $db->query($query);
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>weblog</title>

    <style>
        body {
            font-family: Tahoma, Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            margin: 0;
            padding: 0;
            transition: background-color 0.3s, color 0.3s;
        }

        /* حالت تاریک */
        body.dark-mode {
            background-color: #121212;
            color: #f0f0f0;
        }

        .custom-carousel {
            position: relative;
            width: 100%;
            height: 85vh;
            overflow: hidden;
            font-family: Tahoma, sans-serif;
            border-radius: 15px;
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
            top: 0;
            left: 0;
            opacity: 0;
            transition: opacity 0.5s ease;
        }

        .carousel-item.active {
            opacity: 1;
            z-index: 1;
        }

        .carousel-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .nav-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(0, 0, 0, 0.4);
            border: none;
            color: white;
            font-size: 30px;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 50%;
            user-select: none;
            z-index: 10;
        }

        .nav-btn:hover {
            background-color: rgba(0, 0, 0, 0.7);
        }

        .prev { left: 20px; }
        .next { right: 20px; }

        .carousel-indicators {
            position: absolute;
            bottom: 15px;
            width: 100%;
            text-align: center;
            z-index: 10;
        }

        .carousel-indicators button {
            width: 12px;
            height: 12px;
            margin: 0 6px;
            background-color: rgba(255, 255, 255, 0.6);
            border: none;
            border-radius: 50%;
            cursor: pointer;
        }

        .carousel-indicators button.active {
            background-color: white;
        }

        /* ---------- منو داخل اسلایدر ---------- */
        nav.navbar {
            position: absolute;
            top: 0;
            right: 0;
            left: 0;
            background: linear-gradient(rgba(0, 0, 0, 0.6), transparent);
            padding: 0.75rem 1rem;
            z-index: 1000;
            display: flex;
            justify-content: center;
        }

        nav .container {
            width: 100%;
            max-width: 1140px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .navbar-brand {
            color: #fff;
            font-size: 1.4rem;
            text-decoration: none;
            font-weight: bold;
            user-select: none;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .navbar-brand a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        .cart-icon {
            position: relative;
            display: flex;
            align-items: center;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .cart-icon:hover {
            transform: scale(1.1);
        }

        .cart-img {
            width: 30px;
            height: 30px;
            object-fit: contain;
        }

        .cart-count {
            position: absolute;
            top: -6px;
            right: -10px;
            background: red;
            color: white;
            font-size: 0.75rem;
            padding: 2px 6px;
            border-radius: 50%;
            font-weight: bold;
            user-select: none;
        }

        .navbar-toggler {
            display: none;
            background: transparent;
            border: none;
            color: #fff;
            font-size: 1.5rem;
        }

        #my-nav {
            display: flex;
            flex-wrap: wrap;
        }

        /* ======= استایل بهبود یافته منوی اصلی ======= */
        ul.navbar-nav {
            list-style: none;
            display: flex;
            gap: 0.5rem;
            padding: 0;
            margin: 0;
        }

        ul.navbar-nav li.nav-item a.nav-link {
            color: #fff;
            text-decoration: none;
            padding: 0.5rem 0.75rem;
            border-radius: 0.25rem;
            transition: background-color 0.3s ease, color 0.3s ease, transform 0.3s ease;
            display: inline-block;
            font-weight: 600;
            user-select: none;
        }

        ul.navbar-nav li.nav-item a.nav-link:hover {
            color: #f7f7f7ff;
            transform: scale(1.1) translateY(-2px);
        }

        ul.navbar-nav li.nav-item.active a.nav-link {
            background-color: #007bff;
            color: #fff;
            font-weight: 700;
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.6);
            transform: scale(1.05);
            transition: all 0.3s ease;
        }

        /* دکمه دارک مود */
        .dark-mode-toggle {
            background: transparent;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            transition: background-color 0.3s;
            margin-left: 10px;
        }

        .dark-mode-toggle:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        /* دکمه‌های ورود/ثبت‌نام */
        .auth-buttons {
            display: inline-flex;
            gap: 0;
            align-items: center;
            position: absolute;
            top: 15px;
            left: 40px;
            z-index: 1100;
            direction: ltr;
        }

        .auth-buttons .btn {
            padding: 8px 16px;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            user-select: none;
            border: 2px solid transparent;
            box-shadow: 0 3px 6px rgba(0,0,0,0.2);
            transition: background-color 0.3s, color 0.3s, border-color 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 0;
            border-right: none;
        }

        .auth-buttons .btn-login {
            color: white;
            border-top-left-radius: 20px;
            border-bottom-left-radius: 20px;
        }

        .auth-buttons .btn-login:hover {
            background-color: #ffffffff;
            color: #1b1b1bff;
            box-shadow: 0px 5px 10px #f8f8f8ff;
        }

        .auth-buttons .btn-register {
            background-color: transparent;
            color: #f7f7f7ff;
            border-top-right-radius: 20px;
            border-bottom-right-radius: 20px;
            border-left: 1px solid rgba(88, 88, 88, 1);
        }

        .auth-buttons .btn-register:hover {
            background-color: #007bff;
            color: white;
            box-shadow: 5px 5px 10px #0059ff;
        }

        .btn-icon {
            width: 18px;
            height: 18px;
            fill: currentColor;
        }

        /* واکنشگرایی */
        @media (max-width: 767.98px) {
            .auth-buttons {
                position: static;
                direction: rtl;
                margin-bottom: 0.5rem;
                justify-content: center;
                width: 100%;
                display: flex;
                gap: 10px;
            }

            .auth-buttons .btn {
                border-radius: 20px;
                border: 2px solid transparent;
                border-right: 2px solid transparent;
                border-left: 2px solid transparent;
            }

            .auth-buttons .btn-login {
                border-color: #007bff;
                background-color: #007bff;
                color: white;
            }

            .auth-buttons .btn-register {
                border-color: #fff;
                background-color: transparent;
                color: #fff;
            }
        }

        @media (max-width: 767.98px) {
            .navbar-toggler {
                display: block;
            }

            #my-nav {
                display: none;
                flex-direction: column;
                width: 100%;
                background-color: rgba(0, 0, 0, 0.8);
                margin-top: 0.5rem;
                border-radius: 0.25rem;
            }

            #my-nav.show {
                display: flex;
            }

            ul.navbar-nav {
                flex-direction: column;
                width: 100%;
            }

            ul.navbar-nav li.nav-item a.nav-link {
                padding: 0.75rem 1rem;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }

            .navbar-brand {
                width: 100%;
                text-align: center;
                justify-content: center;
            }
        }
    </style>
</head>

<body>

    <section class="custom-carousel" aria-label="اسلایدر با منو">

        <!-- منوی داخل اسلایدر -->
        <nav class="navbar" role="navigation" aria-label="منوی اصلی">
            <div class="container">
                <!-- دکمه دارک مود -->
                <button id="darkModeToggle" class="dark-mode-toggle" aria-label="تغییر حالت نمایش">
                    <!-- آیکون به صورت پویا با جاوااسکریپت تغییر می‌کند -->
                    <span id="darkModeIcon">🌙</span>
                </button>

                <div class="auth-buttons">
                    <a href="login.php" class="btn btn-login" aria-label="ورود">
                        <svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                        ورود
                    </a>
                    <a href="register.php" class="btn btn-register" aria-label="ثبت نام">
                        <svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4zM19 7h-2v2h-2v2h2v2h2v-2h2v-2h-2z"/>
                        </svg>
                        ثبت نام
                    </a>
                </div>

                <a class="navbar-brand" href="index.php">
                    WEBlog.ir
                </a>

                <a href="cart.php" class="cart-icon" aria-label="سبد خرید">
                    <img src="./img/PNG4.png" alt="سبد خرید" class="cart-img" />
                    <?php if ($total_cart_count > 0): ?>
                        <span class="cart-count"><?= $total_cart_count ?></span>
                    <?php endif; ?>
                </a>

                <button class="navbar-toggler" aria-controls="my-nav" aria-expanded="false" aria-label="تغییر منو">
                    <span class="navbar-toggler-icon">&#9776;</span>
                </button>
                <div id="my-nav" class="collapse navbar-collapse">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link <?= (!isset($_GET['page']) || $_GET['page'] == 'home') ? 'active' : '' ?>" href="index.php?page=home">صفحه اصلی</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (isset($_GET['page']) && $_GET['page'] == 'products') ? 'active' : '' ?>" href="products.php?page=products">محصولات</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (isset($_GET['page']) && $_GET['page'] == 'posts') ? 'active' : '' ?>" href="posts.php?page=posts">مقالات</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (isset($_GET['page']) && $_GET['page'] == 'about') ? 'active' : '' ?>" href="about.php">درباره ما</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- تصاویر اسلایدر -->
        <div class="carousel-inner" id="carouselInner">
            <?php
            if (count($posts_slider) > 0) {
                foreach ($posts_slider as $index => $img) {
                    $activeClass = ($index == 0) ? "active" : ""; // اسلاید اول فعال باشه
                    echo '<div class="carousel-item ' . $activeClass . '">
                            <img src="./img1/' . htmlspecialchars($img['img']) . '" alt="اسلاید ' . ($index + 1) . '">
                        </div>';
                }
            }
            ?>
        </div>
        <button class="nav-btn prev" aria-label="قبلی">&#10094;</button>
        <button class="nav-btn next" aria-label="بعدی">&#10095;</button>

        <div class="carousel-indicators" id="carouselIndicators">
            <?php
            for ($i = 0; $i < count($posts_slider); $i++) {
                $active = ($i === 0) ? 'active' : '';
                echo '<button class="indicator ' . $active . '" data-slide-to="' . $i . '" aria-label="اسلاید ' . ($i + 1) . '"></button>';
            }
            ?>
        </div>
    </section>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // تغییر منو موبایل
            var toggler = document.querySelector(".navbar-toggler");
            var menu = document.getElementById("my-nav");
            toggler.addEventListener("click", function() {
                menu.classList.toggle("show");
            });

            // اسلایدر
            const slides = document.querySelectorAll('.carousel-item');
            const indicators = document.querySelectorAll('#carouselIndicators button');
            const prevBtn = document.querySelector('.nav-btn.prev');
            const nextBtn = document.querySelector('.nav-btn.next');
            const totalSlides = slides.length;
            let currentIndex = 0;

            function showSlide(index) {
                if (index >= totalSlides) index = 0;
                if (index < 0) index = totalSlides - 1;
                slides.forEach(slide => slide.classList.remove('active'));
                indicators.forEach(ind => ind.classList.remove('active'));
                slides[index].classList.add('active');
                indicators[index].classList.add('active');
                currentIndex = index;
            }

            prevBtn.addEventListener('click', () => showSlide(currentIndex - 1));
            nextBtn.addEventListener('click', () => showSlide(currentIndex + 1));
            indicators.forEach((indicator, i) => {
                indicator.addEventListener('click', () => showSlide(i));
            });

            // Dark Mode
            const darkModeToggle = document.getElementById('darkModeToggle');
            const darkModeIcon = document.getElementById('darkModeIcon');
            const body = document.body;

            // بررسی حالت ذخیره‌شده
            if (localStorage.getItem('darkMode') === 'enabled') {
                body.classList.add('dark-mode');
                darkModeIcon.textContent = '☀️';
            }

            darkModeToggle.addEventListener('click', () => {
                body.classList.toggle('dark-mode');
                if (body.classList.contains('dark-mode')) {
                    localStorage.setItem('darkMode', 'enabled');
                    darkModeIcon.textContent = '☀️';
                } else {
                    localStorage.setItem('darkMode', 'disabled');
                    darkModeIcon.textContent = '🌙';
                }
            });
        });
    </script>
</body>
</html>