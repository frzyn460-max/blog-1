<?php
/**
 * Header مشترک برای همه صفحات - کتاب نت
 * استفاده: include('./include/site_header.php');
 */

// اطمینان از شروع session
if (session_status() === PHP_SESSION_NONE) session_start();

// اطلاعات کاربر لاگین‌شده
$is_logged_in = isset($_SESSION['member_id']);
$user_name = '';
$user_avatar = '';
$avatar_letter = '';

if ($is_logged_in && isset($db)) {
    try {
        $user_data = fetchOne($db, "SELECT name, avatar FROM members WHERE id = ?", [$_SESSION['member_id']]);
        if ($user_data) {
            $user_name = $user_data['name'];
            $user_avatar = $user_data['avatar'];
            $avatar_letter = mb_substr($user_name, 0, 1);
        }
    } catch (Exception $e) {
        $user_name = $_SESSION['member_name'] ?? 'کاربر';
        $avatar_letter = mb_substr($user_name, 0, 1);
    }
} elseif ($is_logged_in) {
    $user_name = $_SESSION['member_name'] ?? 'کاربر';
    $avatar_letter = mb_substr($user_name, 0, 1);
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? escape($page_title) . ' | ' : '' ?><?= escape(SITE_NAME) ?></title>
    <link rel="stylesheet" href="./css/style.css">
    <style>
@import url('https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css');

/* استایل اضافی برای نمایش آواتار در header */
.user-avatar-wrap{
    position:relative;display:inline-flex;align-items:center;
    padding:.35rem .75rem .35rem .4rem;
    background:rgba(255,255,255,.12);border-radius:24px;
    border:1px solid rgba(255,255,255,.18);
    gap:.55rem;cursor:pointer;transition:all .25s;
}
body.dark-mode .user-avatar-wrap{
    background:rgba(59,130,246,.15);
    border-color:rgba(59,130,246,.25);
}
.user-avatar-wrap:hover{
    background:rgba(255,255,255,.2);
    transform:translateY(-1px);
}
body.dark-mode .user-avatar-wrap:hover{
    background:rgba(59,130,246,.25);
}
.user-avatar-mini{
    width:32px;height:32px;border-radius:50%;
    background:linear-gradient(135deg,#f59e0b,#ef4444);
    display:flex;align-items:center;justify-content:center;
    font-size:1rem;font-weight:900;color:#fff;
    overflow:hidden;flex-shrink:0;
    box-shadow:0 2px 8px rgba(0,0,0,.15);
}
body.dark-mode .user-avatar-mini{
    box-shadow:0 2px 8px rgba(0,0,0,.3);
}
.user-avatar-mini img{width:100%;height:100%;object-fit:cover}
.user-name-mini{
    font-size:.9rem;font-weight:700;color:#fff;
    max-width:120px;overflow:hidden;text-overflow:ellipsis;
    white-space:nowrap;
}

/* Responsive برای آواتار */
@media(max-width:768px){
    .user-name-mini{display:none}
    .user-avatar-wrap{padding:.4rem}
}
    </style>
</head>
<body>

<!-- Navigation با نمایش آواتار -->
<nav class="navbar">
    <div class="container">
        <a href="index.php" class="logo">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M18,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V4A2,2 0 0,0 18,2M18,20H6V4H11V12L13.5,10.5L16,12V4H18V20Z"/>
            </svg>
            <span><?= escape(SITE_NAME) ?></span>
        </a>

        <button class="mobile-menu-toggle" id="mobileToggle">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M3,6H21V8H3V6M3,11H21V13H3V11M3,16H21V18H3V16Z"/>
            </svg>
        </button>

        <div class="nav-menu" id="navMenu">
            <a href="index.php" class="nav-link">خانه</a>
            <a href="products.php" class="nav-link">محصولات</a>
            <a href="blog.php" class="nav-link">وبلاگ</a>
            <a href="about.php" class="nav-link">درباره ما</a>
            <a href="contact.php" class="nav-link">تماس</a>

            <div class="nav-actions">
                <button class="icon-btn" id="darkModeToggle" title="تغییر حالت">
                    <svg viewBox="0 0 24 24" fill="currentColor" id="themeIcon">
                        <path d="M17.75,4.09L15.22,6.03L16.13,9.09L13.5,7.28L10.87,9.09L11.78,6.03L9.25,4.09L12.44,4L13.5,1L14.56,4L17.75,4.09M21.25,11L19.61,12.25L20.2,14.23L18.5,13.06L16.8,14.23L17.39,12.25L15.75,11L17.81,10.95L18.5,9L19.19,10.95L21.25,11M18.97,15.95C19.8,15.87 20.69,17.05 20.16,17.8C19.84,18.25 19.5,18.67 19.08,19.07C15.17,23 8.84,23 4.94,19.07C1.03,15.17 1.03,8.83 4.94,4.93C5.34,4.53 5.76,4.17 6.21,3.85C6.96,3.32 8.14,4.21 8.06,5.04C7.79,7.9 8.75,10.87 10.95,13.06C13.14,15.26 16.1,16.22 18.97,15.95M17.33,17.97C14.5,17.81 11.7,16.64 9.53,14.5C7.36,12.31 6.2,9.5 6.04,6.68C3.23,9.82 3.34,14.64 6.35,17.66C9.37,20.67 14.19,20.78 17.33,17.97Z"/>
                    </svg>
                </button>

                <button class="icon-btn" title="سبد خرید">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17,18A2,2 0 0,1 19,20A2,2 0 0,1 17,22C15.89,22 15,21.1 15,20C15,18.89 15.89,18 17,18M1,2H4.27L5.21,4H20A1,1 0 0,1 21,5C21,5.17 20.95,5.34 20.88,5.5L17.3,11.97C16.96,12.58 16.3,13 15.55,13H8.1L7.2,14.63L7.17,14.75A0.25,0.25 0 0,0 7.42,15H19V17H7C5.89,17 5,16.1 5,15C5,14.65 5.09,14.32 5.24,14.04L6.6,11.59L3,4H1V2M7,18A2,2 0 0,1 9,20A2,2 0 0,1 7,22C5.89,22 5,21.1 5,20C5,18.89 5.89,18 7,18M16,11L18.78,6H6.14L8.5,11H16Z"/>
                    </svg>
                    <span class="badge">0</span>
                </button>

                <?php if ($is_logged_in): ?>
                    <div class="user-dropdown">
                        <div class="user-avatar-wrap" id="userMenuToggle">
                            <div class="user-avatar-mini">
                                <?php if ($user_avatar && file_exists($user_avatar)): ?>
                                    <img src="<?= escape($user_avatar) ?>" alt="avatar">
                                <?php else: ?>
                                    <?= $avatar_letter ?>
                                <?php endif; ?>
                            </div>
                            <span class="user-name-mini"><?= escape($user_name) ?></span>
                            <svg style="width:14px;height:14px;opacity:.7" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M7,10L12,15L17,10H7Z"/>
                            </svg>
                        </div>
                        <div class="dropdown-menu" id="userMenu">
                            <a href="profile.php" class="dropdown-item">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/></svg>
                                پروفایل من
                            </a>
                            <a href="orders.php" class="dropdown-item">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18,18H6V6H18V18M18,4H6A2,2 0 0,0 4,6V18A2,2 0 0,0 6,20H18A2,2 0 0,0 20,18V6A2,2 0 0,0 18,4M14,15H8V17H14V15M14,10H8V12H14V10M14,5H8V7H14V5Z"/></svg>
                                سفارشات
                            </a>
                            <a href="logout.php" class="dropdown-item text-danger">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M16,17V14H9V10H16V7L21,12L16,17M14,2A2,2 0 0,1 16,4V6H14V4H5V20H14V18H16V20A2,2 0 0,1 14,22H5A2,2 0 0,1 3,20V4A2,2 0 0,1 5,2H14Z"/></svg>
                                خروج
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="btn-login">ورود</a>
                    <a href="register.php" class="btn-register">ثبت‌نام</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<script>
// Mobile menu toggle
document.getElementById('mobileToggle').addEventListener('click', function() {
    document.getElementById('navMenu').classList.toggle('active');
});

// Dark mode toggle
const darkToggle = document.getElementById('darkModeToggle');
const themeIcon = document.getElementById('themeIcon');
const MOON = '<path d="M17.75,4.09L15.22,6.03L16.13,9.09L13.5,7.28L10.87,9.09L11.78,6.03L9.25,4.09L12.44,4L13.5,1L14.56,4L17.75,4.09M21.25,11L19.61,12.25L20.2,14.23L18.5,13.06L16.8,14.23L17.39,12.25L15.75,11L17.81,10.95L18.5,9L19.19,10.95L21.25,11M18.97,15.95C19.8,15.87 20.69,17.05 20.16,17.8C19.84,18.25 19.5,18.67 19.08,19.07C15.17,23 8.84,23 4.94,19.07C1.03,15.17 1.03,8.83 4.94,4.93C5.34,4.53 5.76,4.17 6.21,3.85C6.96,3.32 8.14,4.21 8.06,5.04C7.79,7.9 8.75,10.87 10.95,13.06C13.14,15.26 16.1,16.22 18.97,15.95M17.33,17.97C14.5,17.81 11.7,16.64 9.53,14.5C7.36,12.31 6.2,9.5 6.04,6.68C3.23,9.82 3.34,14.64 6.35,17.66C9.37,20.67 14.19,20.78 17.33,17.97Z"/>';
const SUN = '<path d="M12,8A4,4 0 0,0 8,12A4,4 0 0,0 12,16A4,4 0 0,0 16,12A4,4 0 0,0 12,8M12,18A6,6 0 0,1 6,12A6,6 0 0,1 12,6A6,6 0 0,1 18,12A6,6 0 0,1 12,18M20,8.69V4H15.31L12,0.69L8.69,4H4V8.69L0.69,12L4,15.31V20H8.69L12,23.31L15.31,20H20V15.31L23.31,12L20,8.69Z"/>';

if (localStorage.getItem('darkMode') === 'enabled') {
    document.body.classList.add('dark-mode');
    themeIcon.innerHTML = SUN;
}

darkToggle.addEventListener('click', function() {
    const isDark = document.body.classList.toggle('dark-mode');
    localStorage.setItem('darkMode', isDark ? 'enabled' : 'disabled');
    themeIcon.innerHTML = isDark ? SUN : MOON;
});

// User dropdown toggle
<?php if ($is_logged_in): ?>
const userToggle = document.getElementById('userMenuToggle');
const userMenu = document.getElementById('userMenu');

userToggle.addEventListener('click', function(e) {
    e.stopPropagation();
    userMenu.classList.toggle('active');
});

document.addEventListener('click', function() {
    userMenu.classList.remove('active');
});

userMenu.addEventListener('click', function(e) {
    e.stopPropagation();
});
<?php endif; ?>
</script>