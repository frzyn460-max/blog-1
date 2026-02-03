<?php
/**
 * سایدبار سایت
 * شامل جستجو، دسته‌بندی‌ها، فرم اشتراک و درباره ما
 */

// دریافت دسته‌بندی‌ها به صورت امن
$query_categories = "SELECT id, title FROM categories ORDER BY title ASC";
$categories = fetchAll($db, $query_categories);

// پردازش فرم اشتراک
$subscribe_message = '';
$subscribe_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subscribe'])) {
    // بررسی توکن CSRF
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $subscribe_message = 'درخواست نامعتبر است!';
        $subscribe_type = 'error';
    } else {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        
        // اعتبارسنجی
        if (empty($name) || empty($email)) {
            $subscribe_message = 'لطفاً تمام فیلدها را پر کنید.';
            $subscribe_type = 'error';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $subscribe_message = 'ایمیل معتبر وارد کنید.';
            $subscribe_type = 'error';
        } else {
            // بررسی تکراری نبودن ایمیل
            $check_email = fetchOne($db, "SELECT id FROM subscribers WHERE email = ?", [$email]);
            
            if ($check_email) {
                $subscribe_message = 'این ایمیل قبلاً ثبت شده است.';
                $subscribe_type = 'error';
            } else {
                // ثبت اشتراک
                $result = executeQuery($db, 
                    "INSERT INTO subscribers (name, email) VALUES (?, ?)", 
                    [$name, $email]
                );
                
                if ($result) {
                    $subscribe_message = '✅ اشتراک شما با موفقیت ثبت شد!';
                    $subscribe_type = 'success';
                } else {
                    $subscribe_message = 'خطایی رخ داد. لطفاً دوباره تلاش کنید.';
                    $subscribe_type = 'error';
                }
            }
        }
    }
}
?>

<aside class="sidebar-container">

    <!-- بخش جستجو -->
    <div class="sidebar-card search-card">
        <div class="card-header">
            <svg class="card-icon" viewBox="0 0 24 24" fill="currentColor">
                <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0016 9.5 6.5 6.5 0 109.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
            </svg>
            <h3>جستجو</h3>
        </div>
        <div class="card-body">
            <form action="search.php" method="get" class="search-form">
                <div class="search-wrapper">
                    <input 
                        type="text" 
                        name="search" 
                        class="search-input" 
                        placeholder="دنبال چی میگردی؟" 
                        required
                        autocomplete="off"
                    >
                    <button type="submit" class="search-btn" aria-label="جستجو">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0016 9.5 6.5 6.5 0 109.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- بخش دسته‌بندی‌ها -->
    <div class="sidebar-card categories-card">
        <div class="card-header">
            <svg class="card-icon" viewBox="0 0 24 24" fill="currentColor">
                <path d="M4 10.5c-.83 0-1.5.67-1.5 1.5s.67 1.5 1.5 1.5 1.5-.67 1.5-1.5-.67-1.5-1.5-1.5zm0-6c-.83 0-1.5.67-1.5 1.5S3.17 7.5 4 7.5 5.5 6.83 5.5 6 4.83 4.5 4 4.5zm0 12c-.83 0-1.5.68-1.5 1.5s.68 1.5 1.5 1.5 1.5-.68 1.5-1.5-.67-1.5-1.5-1.5zM7 19h14v-2H7v2zm0-6h14v-2H7v2zm0-8v2h14V5H7z"/>
            </svg>
            <h3>دسته‌بندی‌ها</h3>
        </div>
        <div class="card-body">
            <ul class="category-list">
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $category): ?>
                        <li>
                            <a href="index.php?category=<?= escape($category['id']) ?>" class="category-item">
                                <span class="category-icon">📂</span>
                                <span class="category-name"><?= escape($category['title']) ?></span>
                                <svg class="arrow-icon" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/>
                                </svg>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="no-data">دسته‌بندی‌ای یافت نشد</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <!-- بخش عضویت در خبرنامه -->
    <div class="sidebar-card subscribe-card">
        <div class="card-header">
            <svg class="card-icon" viewBox="0 0 24 24" fill="currentColor">
                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
            </svg>
            <h3>خبرنامه</h3>
        </div>
        <div class="card-body">
            <?php if ($subscribe_message): ?>
                <div class="alert alert-<?= $subscribe_type ?>">
                    <?= escape($subscribe_message) ?>
                </div>
            <?php endif; ?>
            
            <p class="subscribe-text">از جدیدترین کتاب‌ها و مقالات باخبر شوید!</p>
            
            <form method="post" class="subscribe-form">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                
                <div class="form-group">
                    <label for="sub_name">نام شما</label>
                    <input 
                        type="text" 
                        name="name" 
                        id="sub_name" 
                        class="form-input" 
                        placeholder="مثال: علی احمدی"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="sub_email">ایمیل</label>
                    <input 
                        type="email" 
                        name="email" 
                        id="sub_email" 
                        class="form-input" 
                        placeholder="example@gmail.com"
                        required
                    >
                </div>
                
                <button type="submit" name="subscribe" class="btn-subscribe">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                    </svg>
                    عضویت در خبرنامه
                </button>
            </form>
        </div>
    </div>

    <!-- بخش درباره ما -->
    <div class="sidebar-card about-card">
        <div class="card-header">
            <svg class="card-icon" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
            </svg>
            <h3>درباره ما</h3>
        </div>
        <div class="card-body">
            <p class="about-text">
                کتاب نت، بزرگترین مرجع خرید آنلاین کتاب در ایران با بیش از 10,000 عنوان کتاب 
                در دسته‌بندی‌های مختلف. ما اینجا هستیم تا بهترین تجربه خرید را برای شما فراهم کنیم.
            </p>
            <div class="social-links">
                <a href="#" class="social-btn" aria-label="اینستاگرام">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M7.8,2H16.2C19.4,2 22,4.6 22,7.8V16.2A5.8,5.8 0 0,1 16.2,22H7.8C4.6,22 2,19.4 2,16.2V7.8A5.8,5.8 0 0,1 7.8,2M7.6,4A3.6,3.6 0 0,0 4,7.6V16.4C4,18.39 5.61,20 7.6,20H16.4A3.6,3.6 0 0,0 20,16.4V7.6C20,5.61 18.39,4 16.4,4H7.6M17.25,5.5A1.25,1.25 0 0,1 18.5,6.75A1.25,1.25 0 0,1 17.25,8A1.25,1.25 0 0,1 16,6.75A1.25,1.25 0 0,1 17.25,5.5M12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9Z"/>
                    </svg>
                </a>
                <a href="#" class="social-btn" aria-label="تلگرام">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M9.78,18.65L10.06,14.42L17.74,7.5C18.08,7.19 17.67,7.04 17.22,7.31L7.74,13.3L3.64,12C2.76,11.75 2.75,11.14 3.84,10.7L19.81,4.54C20.54,4.21 21.24,4.72 20.96,5.84L18.24,18.65C18.05,19.56 17.5,19.78 16.74,19.36L12.6,16.3L10.61,18.23C10.38,18.46 10.19,18.65 9.78,18.65Z"/>
                    </svg>
                </a>
                <a href="#" class="social-btn" aria-label="توییتر">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M22.46,6C21.69,6.35 20.86,6.58 20,6.69C20.88,6.16 21.56,5.32 21.88,4.31C21.05,4.81 20.13,5.16 19.16,5.36C18.37,4.5 17.26,4 16,4C13.65,4 11.73,5.92 11.73,8.29C11.73,8.63 11.77,8.96 11.84,9.27C8.28,9.09 5.11,7.38 3,4.79C2.63,5.42 2.42,6.16 2.42,6.94C2.42,8.43 3.17,9.75 4.33,10.5C3.62,10.5 2.96,10.3 2.38,10C2.38,10 2.38,10 2.38,10.03C2.38,12.11 3.86,13.85 5.82,14.24C5.46,14.34 5.08,14.39 4.69,14.39C4.42,14.39 4.15,14.36 3.89,14.31C4.43,16 6,17.26 7.89,17.29C6.43,18.45 4.58,19.13 2.56,19.13C2.22,19.13 1.88,19.11 1.54,19.07C3.44,20.29 5.70,21 8.12,21C16,21 20.33,14.46 20.33,8.79C20.33,8.6 20.33,8.42 20.32,8.23C21.16,7.63 21.88,6.87 22.46,6Z"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>

</aside>

<style>
    /* ===== استایل سایدبار ===== */
    .sidebar-container {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        width: 100%;
        max-width: 380px;
    }

    /* کارت‌های سایدبار */
    .sidebar-card {
        background: var(--bg-primary);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: var(--shadow-md);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid var(--border-color);
    }

    .sidebar-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }

    /* هدر کارت‌ها */
    .card-header {
        background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-hover) 100%);
        color: white;
        padding: 1.2rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 700;
        font-size: 1.1rem;
    }

    .card-icon {
        width: 24px;
        height: 24px;
        flex-shrink: 0;
    }

    .card-body {
        padding: 1.5rem;
    }

    /* ===== بخش جستجو ===== */
    .search-wrapper {
        display: flex;
        gap: 10px;
        background: var(--bg-secondary);
        border-radius: 15px;
        padding: 8px;
        border: 2px solid transparent;
        transition: all 0.3s ease;
    }

    .search-wrapper:focus-within {
        border-color: var(--accent-primary);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }

    .search-input {
        flex: 1;
        border: none;
        background: transparent;
        padding: 0.7rem 1rem;
        font-size: 1rem;
        color: var(--text-primary);
        outline: none;
        font-family: inherit;
    }

    .search-input::placeholder {
        color: var(--text-secondary);
    }

    .search-btn {
        background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-hover) 100%);
        border: none;
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        color: white;
    }

    .search-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
    }

    .search-btn svg {
        width: 22px;
        height: 22px;
    }

    /* ===== دسته‌بندی‌ها ===== */
    .category-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .category-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 1rem 1.2rem;
        background: var(--bg-secondary);
        border-radius: 12px;
        color: var(--text-primary);
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        font-weight: 500;
        position: relative;
        overflow: hidden;
    }

    .category-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 4px;
        background: var(--accent-primary);
        transform: scaleY(0);
        transition: transform 0.3s ease;
    }

    .category-item:hover {
        background: var(--accent-primary);
        color: white;
        transform: translateX(-5px);
    }

    .category-item:hover::before {
        transform: scaleY(1);
    }

    .category-icon {
        font-size: 1.3rem;
        flex-shrink: 0;
    }

    .category-name {
        flex: 1;
    }

    .arrow-icon {
        width: 20px;
        height: 20px;
        opacity: 0;
        transform: translateX(-10px);
        transition: all 0.3s ease;
    }

    .category-item:hover .arrow-icon {
        opacity: 1;
        transform: translateX(0);
    }

    .no-data {
        text-align: center;
        color: var(--text-secondary);
        padding: 2rem;
        font-style: italic;
    }

    /* ===== فرم اشتراک ===== */
    .subscribe-text {
        color: var(--text-secondary);
        margin-bottom: 1.5rem;
        text-align: center;
        font-size: 0.95rem;
    }

    .form-group {
        margin-bottom: 1.2rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        color: var(--text-primary);
        font-weight: 600;
        font-size: 0.95rem;
    }

    .form-input {
        width: 100%;
        padding: 0.9rem 1.2rem;
        border: 2px solid var(--border-color);
        border-radius: 12px;
        font-size: 1rem;
        font-family: inherit;
        background: var(--bg-secondary);
        color: var(--text-primary);
        transition: all 0.3s ease;
        outline: none;
    }

    .form-input:focus {
        border-color: var(--accent-primary);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }

    .btn-subscribe {
        width: 100%;
        padding: 1rem;
        background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-hover) 100%);
        border: none;
        border-radius: 12px;
        color: white;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: all 0.3s ease;
        font-family: inherit;
    }

    .btn-subscribe:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
    }

    .btn-subscribe svg {
        width: 20px;
        height: 20px;
    }

    /* پیام‌های Alert */
    .alert {
        padding: 1rem 1.2rem;
        border-radius: 12px;
        margin-bottom: 1.2rem;
        font-weight: 600;
        animation: slideDown 0.4s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .alert-success {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        color: #155724;
        border: 2px solid #c3e6cb;
    }

    .alert-error {
        background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        color: #721c24;
        border: 2px solid #f5c6cb;
    }

    /* ===== بخش درباره ما ===== */
    .about-text {
        color: var(--text-secondary);
        line-height: 1.8;
        text-align: justify;
        margin-bottom: 1.5rem;
        font-size: 0.95rem;
    }

    .social-links {
        display: flex;
        gap: 12px;
        justify-content: center;
    }

    .social-btn {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        background: var(--bg-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-primary);
        transition: all 0.3s ease;
        border: 2px solid var(--border-color);
    }

    .social-btn:hover {
        background: var(--accent-primary);
        color: white;
        transform: translateY(-3px) rotate(5deg);
        box-shadow: 0 6px 15px rgba(37, 99, 235, 0.3);
    }

    .social-btn svg {
        width: 22px;
        height: 22px;
    }

    /* ===== Dark Mode ===== */
    body.dark-mode .sidebar-card {
        background: var(--bg-secondary);
    }

    body.dark-mode .search-wrapper,
    body.dark-mode .category-item,
    body.dark-mode .form-input,
    body.dark-mode .social-btn {
        background: rgba(255, 255, 255, 0.05);
    }

    body.dark-mode .form-input:focus {
        background: rgba(255, 255, 255, 0.08);
    }

    /* ===== Responsive ===== */
    @media (max-width: 991px) {
        .sidebar-container {
            max-width: 100%;
        }
    }
</style>