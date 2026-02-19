<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once(__DIR__ . "/config.php");
require_once(__DIR__ . "/db.php");

$cart_items       = $_SESSION['cart'] ?? [];
$total_cart_count = array_sum($cart_items);
$posts_slider     = fetchAll($db, "SELECT * FROM img");
$categories       = fetchAll($db, "SELECT * FROM categories ORDER BY title ASC");
$csrf_token       = generate_csrf_token();
$current_file     = basename($_SERVER['PHP_SELF'], '.php');
$logged_in        = isset($_SESSION['member_id']);
$user_name        = $_SESSION['member_name'] ?? '';
$user_avatar      = $user_name ? mb_substr($user_name, 0, 1) : '?';
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="<?= escape(SITE_NAME) ?> - بهترین فروشگاه آنلاین کتاب">
    <title><?= escape(SITE_NAME) ?> | فروشگاه آنلاین کتاب</title>
    <link rel="stylesheet" href="./css/style.css">
    <style>
        /* ══════════════════════════════════════════════════
           هدر - استایل پایه (بدون تغییر از نسخه اصلی)
           ══════════════════════════════════════════════════ */
        .hero-header-wrapper {
            position: relative;
            width: 100%;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #3b82f6 100%);
            overflow: hidden;
        }
        body.dark-mode .hero-header-wrapper {
            background: linear-gradient(135deg, #020617 0%, #0c1e47 50%, #1e3a8a 100%);
        }
        .header-animated-bg { position: absolute; inset: 0; overflow: hidden; z-index: 1; }
        .header-animated-bg span {
            position: absolute; display: block;
            background: rgba(255,255,255,0.1); border-radius: 50%;
            animation: headerFloat 20s infinite ease-in-out;
        }
        .header-animated-bg span:nth-child(1) { width:80px; height:80px; top:10%; right:20%; animation-delay:0s; }
        .header-animated-bg span:nth-child(2) { width:120px; height:120px; top:60%; right:80%; animation-delay:3s; }
        .header-animated-bg span:nth-child(3) { width:100px; height:100px; top:40%; right:50%; animation-delay:6s; }
        @keyframes headerFloat {
            0%,100% { transform:translate(0,0); opacity:0.3; }
            50%      { transform:translate(100px,-100px); opacity:0.6; }
        }

        /* NAVBAR */
        .header-navbar {
            position: absolute; top:0; left:0; right:0;
            z-index: 1000; padding: 1.5rem 0;
            transition: all 0.3s ease;
        }
        .header-navbar.scrolled {
            position: fixed;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
            padding: 1rem 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        body.dark-mode .header-navbar.scrolled { background: rgba(30,41,59,0.95); }

        .header-nav-container {
            max-width: 1400px; margin: 0 auto;
            padding: 0 2rem;
            display: flex; align-items: center; justify-content: space-between;
        }

        /* لوگو */
        .header-logo {
            display: flex; align-items: center; gap: 0.75rem;
            text-decoration: none; color: white;
            font-size: 1.5rem; font-weight: 800;
            position: relative; z-index: 10; flex-shrink: 0;
        }
        .header-logo-icon { font-size: 2rem; animation: headerLogoFloat 3s ease-in-out infinite; }
        @keyframes headerLogoFloat { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }
        .header-navbar.scrolled .header-logo { color: var(--text-primary); }

        /* منو desktop */
        .header-nav-menu {
            display: flex; align-items: center; gap: 0.5rem;
            list-style: none; z-index: 10;
        }
        .header-nav-link {
            color: white; text-decoration: none;
            padding: 0.75rem 1.5rem; border-radius: 50px;
            font-weight: 600; font-size: 0.95rem;
            display: flex; align-items: center; gap: 0.5rem;
            transition: all 0.3s ease;
            background: rgba(255,255,255,0.1);
            position: relative; overflow: hidden; white-space: nowrap;
        }
        .header-nav-link::before {
            content: ''; position: absolute; inset: 0;
            background: rgba(255,255,255,0.2);
            transform: translateX(-100%); transition: transform 0.3s ease;
        }
        .header-nav-link:hover::before { transform: translateX(0); }
        .header-nav-link:hover { transform: translateY(-3px); }
        .header-nav-link.active { background: rgba(255,255,255,0.25); }
        .header-navbar.scrolled .header-nav-link { color:var(--text-primary); background:transparent; }
        .header-navbar.scrolled .header-nav-link:hover { background:var(--hover-bg); color:var(--accent-primary); }
        .header-navbar.scrolled .header-nav-link.active { background:var(--accent-primary); color:white; }

        /* آیکون‌ها */
        .header-nav-icons {
            display: flex; align-items: center; gap: 0.75rem;
            z-index: 10; flex-shrink: 0;
        }
        .header-icon-btn {
            width: 50px; height: 50px; border-radius: 50%;
            background: rgba(255,255,255,0.15); border: none;
            color: white; font-size: 1.3rem;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.3s ease;
            text-decoration: none; position: relative; flex-shrink: 0;
        }
        .header-icon-btn:hover { transform:scale(1.1); background:rgba(255,255,255,0.25); }
        .header-navbar.scrolled .header-icon-btn { background:rgba(99,102,241,0.1); color:var(--accent-primary); }
        .header-cart-count {
            position: absolute; top:-5px; left:-5px;
            background: linear-gradient(135deg,#f093fb,#f5576c);
            color: white; width:24px; height:24px; border-radius:50%;
            font-size: 0.7rem; display:flex; align-items:center; justify-content:center;
            font-weight: 700; animation: bounce 2s infinite;
        }
        @keyframes bounce { 0%,100%{transform:scale(1)} 50%{transform:scale(1.1)} }

        /* همبرگر */
        .header-mobile-toggle {
            display: none; width:50px; height:50px; border-radius:50%;
            background: rgba(255,255,255,0.15); border:none;
            color: white; font-size:1.5rem;
            cursor: pointer; align-items:center; justify-content:center;
            flex-shrink: 0;
        }

        /* AUTH */
        .header-auth-btns { display:flex; align-items:center; gap:0.75rem; }
        .header-btn-login {
            display: inline-flex; align-items:center; gap:0.5rem;
            padding: 0.6rem 1.3rem; border-radius:50px;
            font-weight: 600; font-size:0.9rem;
            text-decoration: none; color:white;
            border: 2px solid rgba(255,255,255,0.6);
            background: transparent; transition:all 0.3s ease; white-space:nowrap;
        }
        .header-btn-login:hover { background:rgba(255,255,255,0.15); border-color:white; }
        .header-btn-login svg { width:18px; height:18px; }
        .header-btn-register {
            display: inline-flex; align-items:center; gap:0.5rem;
            padding: 0.6rem 1.3rem; border-radius:50px;
            font-weight: 700; font-size:0.9rem;
            text-decoration: none; color:#1e3a8a;
            background: white; transition:all 0.3s ease; white-space:nowrap;
            box-shadow: 0 4px 15px rgba(255,255,255,0.3);
        }
        .header-btn-register:hover { transform:translateY(-2px); box-shadow:0 8px 25px rgba(255,255,255,0.4); }
        .header-btn-register svg { width:18px; height:18px; }
        .header-navbar.scrolled .header-btn-login { color:var(--text-primary); border-color:var(--border-color); }
        .header-navbar.scrolled .header-btn-login:hover { background:var(--bg-secondary); }
        .header-navbar.scrolled .header-btn-register { color:white; background:linear-gradient(135deg,#1e3a8a,#3b82f6); }

        /* منوی کاربر */
        .header-user-menu { position:relative; }
        .header-user-btn {
            display: inline-flex; align-items:center; gap:0.6rem;
            padding: 0.5rem 1rem; border-radius:50px;
            background: rgba(255,255,255,0.15); border:2px solid rgba(255,255,255,0.4);
            color: white; font-weight:600; font-size:0.9rem;
            cursor: pointer; transition:all 0.3s ease; font-family:inherit;
        }
        .header-user-btn:hover { background:rgba(255,255,255,0.25); }
        .header-user-btn svg { width:18px; height:18px; transition:transform 0.3s; }
        .header-user-btn.open svg { transform:rotate(180deg); }
        .header-user-avatar {
            width:32px; height:32px; border-radius:50%;
            background: linear-gradient(135deg,#f59e0b,#ef4444);
            display: flex; align-items:center; justify-content:center;
            font-weight: 800; font-size:1rem; color:white; flex-shrink:0;
        }
        .header-navbar.scrolled .header-user-btn { color:var(--text-primary); background:var(--bg-secondary); border-color:var(--border-color); }
        .header-user-dropdown {
            position: absolute; top:calc(100% + 12px); left:0;
            background: var(--bg-primary,white); border-radius:18px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            border: 1px solid var(--border-color,rgba(0,0,0,0.08));
            min-width: 200px; overflow:hidden;
            opacity: 0; visibility:hidden; transform:translateY(-10px);
            transition: all 0.3s cubic-bezier(0.4,0,0.2,1); z-index:9999;
        }
        .header-user-dropdown.open { opacity:1; visibility:visible; transform:translateY(0); }
        .dropdown-item {
            display: flex; align-items:center; gap:0.75rem;
            padding: 0.9rem 1.2rem; color:var(--text-primary,#0f172a);
            text-decoration: none; font-weight:500; font-size:0.95rem;
            transition: background 0.2s;
        }
        .dropdown-item:hover { background:var(--bg-secondary,#f8fafc); }
        .dropdown-item svg { width:20px; height:20px; color:var(--accent-primary,#1e3a8a); flex-shrink:0; }
        .dropdown-item.danger { color:#ef4444; }
        .dropdown-item.danger svg { color:#ef4444; }
        .dropdown-item.danger:hover { background:rgba(239,68,68,0.08); }
        .dropdown-divider { height:1px; background:var(--border-color,rgba(0,0,0,0.08)); margin:0.3rem 0; }

        /* HERO CONTENT */
        .header-hero-content {
            position: relative; min-height:100vh;
            display: flex; align-items:center;
            z-index: 10; padding:8rem 0 4rem;
        }
        .header-hero-grid {
            max-width: 1400px; margin:0 auto; padding:0 2rem;
            display: grid; grid-template-columns:1fr 1fr;
            gap: 4rem; align-items:center;
        }
        .header-hero-text { animation: headerSlideInRight 1s ease; }
        @keyframes headerSlideInRight { from{opacity:0;transform:translateX(50px)} to{opacity:1;transform:translateX(0)} }

        .header-hero-badge {
            display: inline-flex; align-items:center; gap:0.5rem;
            background: rgba(255,255,255,0.2); backdrop-filter:blur(10px);
            padding: 0.75rem 1.5rem; border-radius:50px;
            color: white; font-weight:600; font-size:0.9rem; margin-bottom:2rem;
        }
        .header-hero-title { font-size:4rem; font-weight:900; color:white; line-height:1.2; margin-bottom:1.5rem; }
        .header-hero-highlight { display:inline-block; position:relative; }
        .header-hero-highlight::after {
            content:''; position:absolute; bottom:10px; right:0; left:0;
            height:15px; background:rgba(255,255,255,0.3); z-index:-1; border-radius:8px;
        }
        .header-hero-desc { font-size:1.2rem; color:rgba(255,255,255,0.9); margin-bottom:2.5rem; line-height:1.8; }
        .header-hero-buttons { display:flex; gap:1.5rem; flex-wrap:wrap; }
        .header-btn {
            padding: 1rem 2.5rem; border-radius:50px; font-weight:700; font-size:1rem;
            text-decoration: none; display:inline-flex; align-items:center; gap:0.75rem;
            transition: all 0.3s ease; font-family:inherit;
        }
        .header-btn-primary { background:white; color:var(--accent-primary); box-shadow:0 10px 30px rgba(255,255,255,0.3); }
        .header-btn-primary:hover { transform:translateY(-5px); box-shadow:0 15px 40px rgba(255,255,255,0.4); }
        .header-btn-secondary { background:rgba(255,255,255,0.15); color:white; border:2px solid white; }
        .header-btn-secondary:hover { background:white; color:var(--accent-primary); }

        /* اسلایدر */
        .header-slider-wrapper { position:relative; animation:headerSlideInLeft 1s ease; }
        @keyframes headerSlideInLeft { from{opacity:0;transform:translateX(-50px)} to{opacity:1;transform:translateX(0)} }
        .header-slider-container {
            position: relative; width:100%; max-width:500px;
            aspect-ratio: 4/5; margin:0 auto;
        }
        .header-slide {
            position: absolute; inset:0; opacity:0;
            transition: opacity 1s ease; border-radius:30px;
            overflow: hidden; box-shadow:0 30px 80px rgba(0,0,0,0.3);
        }
        .header-slide.active { opacity:1; }
        .header-slide img { width:100%; height:100%; object-fit:cover; }
        .header-slider-dots {
            position: absolute; bottom:-60px; left:50%; transform:translateX(-50%);
            display: flex; gap:0.75rem;
        }
        .header-dot {
            width:12px; height:12px; border-radius:50%;
            background: rgba(255,255,255,0.3); border:none; cursor:pointer;
            transition: all 0.3s ease;
        }
        .header-dot:hover { background:rgba(255,255,255,0.5); }
        .header-dot.active { width:40px; border-radius:10px; background:white; }

        /* ══════════════════════════════════════════════════
           RESPONSIVE - تقویت‌شده کامل
           ══════════════════════════════════════════════════ */

        /* لپتاپ کوچک */
        @media (max-width: 1200px) {
            .header-hero-title { font-size: 3.2rem; }
            .header-nav-link { padding: 0.65rem 1.1rem; font-size: 0.88rem; }
            .header-hero-grid { gap: 3rem; padding: 0 1.5rem; }
            .header-slider-container { max-width: 420px; }
        }

        /* تبلت بزرگ */
        @media (max-width: 991px) {
            .header-mobile-toggle { display: flex; }

            /* منو به sidebar */
            .header-nav-menu {
                position: fixed; top:0; right:-100%; width:300px; height:100vh;
                background: rgba(15,23,42,0.98); backdrop-filter:blur(20px);
                flex-direction: column; padding:5rem 1.5rem 2rem;
                transition: right 0.4s ease;
                box-shadow: -10px 0 40px rgba(0,0,0,0.5);
                overflow-y: auto;
            }
            .header-nav-menu.active { right: 0; }
            .header-nav-link { width:100%; justify-content:center; color:white; font-size:1rem; padding:0.85rem 1.25rem; }
            .header-navbar.scrolled .header-nav-link { color:white; }

            .header-hero-grid { grid-template-columns:1fr; text-align:center; gap:2.5rem; padding:0 1.5rem; }
            .header-hero-title { font-size: 3rem; }
            .header-hero-buttons { justify-content:center; }
            .header-slider-container { max-width: 360px; }
            .header-hero-content { padding: 7rem 0 5rem; min-height: auto; }

            /* auth در موبایل */
            .header-auth-btns { gap: 0.5rem; }
        }

        /* تبلت کوچک */
        @media (max-width: 768px) {
            .header-nav-container { padding: 0 1.25rem; }
            .header-logo { font-size: 1.3rem; }
            .header-logo-icon { font-size: 1.7rem; }
            .header-icon-btn { width:44px; height:44px; font-size:1.2rem; }
            .header-mobile-toggle { width:44px; height:44px; font-size:1.3rem; }

            /* auth: آیکون only */
            .header-auth-btns { gap: 0.4rem; }
            .header-btn-login span, .header-btn-register span { display:none; }
            .header-btn-login, .header-btn-register {
                padding: 0; width:44px; height:44px; border-radius:50%; justify-content:center;
            }
            .header-btn-login svg, .header-btn-register svg { width:20px; height:20px; }

            /* نام کاربر مخفی */
            .header-user-name { display: none; }
            .header-user-btn { padding: 0.4rem 0.6rem; }

            .header-hero-title { font-size: 2.5rem; }
            .header-hero-desc  { font-size: 1.1rem; }
            .header-btn { padding: 0.9rem 2rem; font-size: 0.95rem; }
            .header-slider-container { max-width: 300px; }
            .header-hero-content { padding: 6rem 0 4rem; }
        }

        /* موبایل */
        @media (max-width: 576px) {
            .header-nav-container { padding: 0 1rem; }
            .header-logo { font-size: 1.15rem; }
            .header-logo-icon { font-size: 1.5rem; }
            .header-icon-btn { width:40px; height:40px; font-size:1.1rem; }
            .header-mobile-toggle { width:40px; height:40px; font-size:1.2rem; }
            .header-nav-icons { gap: 0.4rem; }

            .header-hero-title { font-size: 2.1rem; }
            .header-hero-desc  { font-size: 1rem; margin-bottom: 1.75rem; }
            .header-hero-badge { font-size: 0.82rem; padding: 0.6rem 1.1rem; margin-bottom: 1.25rem; }
            .header-btn { padding: 0.85rem 1.75rem; font-size: 0.9rem; }
            .header-hero-buttons { gap: 0.85rem; }
            .header-slider-container { max-width: 260px; }
            .header-hero-content { padding: 5.5rem 0 4rem; }
            .header-hero-grid { gap: 2rem; padding: 0 1rem; }
            .header-navbar { padding: 1rem 0; }
            .header-navbar.scrolled { padding: 0.75rem 0; }

            .header-btn-login, .header-btn-register { width:40px; height:40px; }
        }

        /* موبایل خیلی کوچک */
        @media (max-width: 400px) {
            .header-logo span:not(.header-logo-icon) { display:none; }
            .header-hero-title { font-size: 1.9rem; }
            .header-hero-buttons { flex-direction: column; align-items: center; }
            .header-btn { width: 100%; justify-content: center; }
            .header-slider-container { max-width: 220px; }
            .header-hero-content { padding: 5rem 0 3.5rem; }
        }
    </style>
</head>
<body>

    <div class="hero-header-wrapper">
        <div class="header-animated-bg"><span></span><span></span><span></span></div>

        <nav class="header-navbar" id="headerNavbar">
            <div class="header-nav-container">
                <a href="index.php" class="header-logo">
                    <span class="header-logo-icon">📚</span>
                    <span><?= escape(SITE_NAME) ?></span>
                </a>

                <ul class="header-nav-menu" id="headerNavMenu">
                    <li><a href="index.php"    class="header-nav-link <?= $current_file==='index'    ?'active':'' ?>"><span>🏠</span> صفحه اصلی</a></li>
                    <li><a href="products.php" class="header-nav-link <?= $current_file==='products' ?'active':'' ?>"><span>📦</span> محصولات</a></li>
                    <li><a href="posts.php"    class="header-nav-link <?= $current_file==='posts'    ?'active':'' ?>"><span>📝</span> مقالات</a></li>
                    <li><a href="about.php"    class="header-nav-link <?= $current_file==='about'    ?'active':'' ?>"><span>ℹ️</span> درباره ما</a></li>
                </ul>

                <div class="header-nav-icons">
                    <button class="header-icon-btn" id="headerDarkToggle" aria-label="تغییر تم">
                        <span id="headerDarkIcon">🌙</span>
                    </button>
                    <a href="cart.php" class="header-icon-btn" aria-label="سبد خرید">
                        🛒
                        <?php if ($total_cart_count > 0): ?>
                            <span class="header-cart-count"><?= $total_cart_count ?></span>
                        <?php endif; ?>
                    </a>

                    <?php if ($logged_in): ?>
                        <div class="header-user-menu">
                            <button class="header-user-btn" id="userMenuToggle">
                                <span class="header-user-avatar"><?= $user_avatar ?></span>
                                <span class="header-user-name"><?= escape($user_name) ?></span>
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M7,10L12,15L17,10H7Z"/></svg>
                            </button>
                            <div class="header-user-dropdown" id="userDropdown">
                                <a href="profile.php" class="dropdown-item">
                                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/></svg>
                                    پروفایل
                                </a>
                                <a href="orders.php" class="dropdown-item">
                                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17,18A2,2 0 0,1 19,20A2,2 0 0,1 17,22C15.89,22 15,21.1 15,20C15,18.89 15.89,18 17,18M1,2H4.27L5.21,4H20A1,1 0 0,1 21,5C21,5.17 20.95,5.34 20.88,5.5L17.3,11.97C16.96,12.58 16.3,13 15.55,13H8.1L7.2,14.63L7.17,14.75A0.25,0.25 0 0,0 7.42,15H19V17H7C5.89,17 5,16.1 5,15C5,14.65 5.09,14.32 5.24,14.04L6.6,11.59L3,4H1V2Z"/></svg>
                                    سفارش‌ها
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="logout.php" class="dropdown-item danger">
                                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M16,17V14H9V10H16V7L21,12L16,17M14,2A2,2 0 0,1 16,4V6H14V4H5V20H14V18H16V20A2,2 0 0,1 14,22H5A2,2 0 0,1 3,20V4A2,2 0 0,1 5,2H14Z"/></svg>
                                    خروج
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="header-auth-btns">
                            <a href="login.php" class="header-btn-login">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M10,17V14H3V10H10V7L15,12L10,17M10,2H19A2,2 0 0,1 21,4V20A2,2 0 0,1 19,22H10A2,2 0 0,1 8,20V18H10V20H19V4H10V6H8V4A2,2 0 0,1 10,2Z"/></svg>
                                <span>ورود</span>
                            </a>
                            <a href="register.php" class="header-btn-register">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M15,14C12.33,14 7,15.33 7,18V20H23V18C23,15.33 17.67,14 15,14M6,10V7H4V10H1V12H4V15H6V12H9V10M15,12A4,4 0 0,0 19,8A4,4 0 0,0 15,4A4,4 0 0,0 11,8A4,4 0 0,0 15,12Z"/></svg>
                                <span>ثبت‌نام</span>
                            </a>
                        </div>
                    <?php endif; ?>

                    <button class="header-mobile-toggle" id="headerMobileToggle">☰</button>
                </div>
            </div>
        </nav>

        <div class="header-hero-content">
            <div class="header-hero-grid">
                <div class="header-hero-text">
                    <div class="header-hero-badge">✨ تخفیف ویژه تا ۵۰٪</div>
                    <h1 class="header-hero-title">
                        دنیای <span class="header-hero-highlight">کتاب</span><br>
                        در یک کلیک
                    </h1>
                    <p class="header-hero-desc">
                        بیش از <strong>۱۰,۰۰۰</strong> عنوان کتاب در دسته‌بندی‌های مختلف
                        با بهترین کیفیت و قیمت مناسب
                    </p>
                    <div class="header-hero-buttons">
                        <a href="products.php" class="header-btn header-btn-primary"><span>🚀</span> مشاهده محصولات</a>
                        <a href="posts.php"    class="header-btn header-btn-secondary"><span>📖</span> مقالات آموزشی</a>
                    </div>
                </div>

                <div class="header-slider-wrapper">
                    <div class="header-slider-container">
                        <?php if (!empty($posts_slider)): ?>
                            <?php foreach ($posts_slider as $index => $slide): ?>
                                <div class="header-slide <?= $index===0?'active':'' ?>">
                                    <img src="./img1/<?= escape($slide['img']) ?>" alt="کتاب <?= $index+1 ?>" loading="<?= $index===0?'eager':'lazy' ?>">
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <div class="header-slider-dots">
                            <?php foreach ($posts_slider as $index => $slide): ?>
                                <button class="header-dot <?= $index===0?'active':'' ?>" data-slide="<?= $index ?>"></button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // اسلایدر
        const headerSlides = document.querySelectorAll('.header-slide');
        const headerDots   = document.querySelectorAll('.header-dot');
        let headerCurrent  = 0;
        function showHeaderSlide(n) {
            headerSlides.forEach(s => s.classList.remove('active'));
            headerDots.forEach(d => d.classList.remove('active'));
            headerCurrent = (n + headerSlides.length) % headerSlides.length;
            headerSlides[headerCurrent].classList.add('active');
            headerDots[headerCurrent].classList.add('active');
        }
        headerDots.forEach((dot,i) => dot.addEventListener('click', () => showHeaderSlide(i)));
        if (headerSlides.length > 1) setInterval(() => showHeaderSlide(headerCurrent+1), 5000);

        // منوی موبایل
        const headerMobileToggle = document.getElementById('headerMobileToggle');
        const headerNavMenu      = document.getElementById('headerNavMenu');
        headerMobileToggle?.addEventListener('click', () => headerNavMenu.classList.toggle('active'));
        document.addEventListener('click', e => {
            if (!headerNavMenu?.contains(e.target) && !headerMobileToggle?.contains(e.target))
                headerNavMenu?.classList.remove('active');
        });

        // Sticky
        const headerNavbar = document.getElementById('headerNavbar');
        window.addEventListener('scroll', () => {
            headerNavbar.classList.toggle('scrolled', window.scrollY > 100);
        }, {passive:true});

        // منوی کاربر
        const userMenuToggle = document.getElementById('userMenuToggle');
        const userDropdown   = document.getElementById('userDropdown');
        userMenuToggle?.addEventListener('click', e => {
            e.stopPropagation();
            userMenuToggle.classList.toggle('open');
            userDropdown.classList.toggle('open');
        });
        document.addEventListener('click', () => {
            userMenuToggle?.classList.remove('open');
            userDropdown?.classList.remove('open');
        });

        // دارک مود
        const headerDarkToggle = document.getElementById('headerDarkToggle');
        const headerDarkIcon   = document.getElementById('headerDarkIcon');
        if (localStorage.getItem('darkMode') === 'enabled') {
            document.body.classList.add('dark-mode');
            headerDarkIcon.textContent = '☀️';
        }
        headerDarkToggle?.addEventListener('click', () => {
            const d = document.body.classList.toggle('dark-mode');
            localStorage.setItem('darkMode', d ? 'enabled' : 'disabled');
            headerDarkIcon.textContent = d ? '☀️' : '🌙';
        });
    </script>