<?php
/**
 * صفحه درباره ما
 * معرفی سایت و تیم
 */

// فراخوانی هدر
require_once("./include/header.php");
?>

<!-- بخش هیرو درباره ما -->
<section class="about-hero">
    <div class="container">
        <div class="hero-content" data-aos="fade-up">
            <span class="hero-badge">📖 درباره ما</span>
            <h1 class="hero-title">
                ما <span class="gradient-text">کتاب نت</span> هستیم
            </h1>
            <p class="hero-desc">
                بزرگترین فروشگاه آنلاین کتاب در ایران با بیش از 10 سال سابقه
            </p>
        </div>
    </div>
</section>

<!-- کانتینر اصلی -->
<div class="page-wrapper">
    
    <!-- محتوای اصلی -->
    <main class="main-content">
        
        <!-- بخش داستان ما -->
        <section class="story-section" data-aos="fade-up">
            <div class="section-header">
                <svg class="section-icon" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4M11,16.5L6.5,12L7.91,10.59L11,13.67L16.59,8.09L18,9.5L11,16.5Z"/>
                </svg>
                <h2 class="section-title">داستان ما</h2>
            </div>
            <div class="story-content">
                <div class="story-image" data-aos="fade-right">
                    <img src="./img/25.jpg" alt="کتاب نت">
                    <div class="image-decoration"></div>
                </div>
                <div class="story-text" data-aos="fade-left">
                    <p>
                        <strong>کتاب نت</strong> در سال 1395 با هدف ایجاد دسترسی آسان و سریع به کتاب‌های مختلف 
                        برای تمام علاقه‌مندان به مطالعه راه‌اندازی شد. ما معتقدیم که کتاب، پلی است به دنیای دانش 
                        و هر فردی حق دارد به بهترین منابع دسترسی داشته باشد.
                    </p>
                    <p>
                        امروزه با افتخار میزبان بیش از <strong>50,000 کاربر فعال</strong> هستیم و روزانه 
                        صدها سفارش کتاب را در سراسر کشور پردازش می‌کنیم. تیم ما شبانه‌روز در تلاش است 
                        تا بهترین تجربه خرید را برای شما فراهم کند.
                    </p>
                    <p>
                        ماموریت ما ساده است: <strong>ساخت بزرگترین کتابخانه آنلاین فارسی</strong> با بهترین 
                        قیمت‌ها، سریع‌ترین ارسال و بهترین خدمات پس از فروش.
                    </p>
                </div>
            </div>
        </section>

        <!-- بخش ارزش‌های ما -->
        <section class="values-section" data-aos="fade-up">
            <div class="section-header">
                <svg class="section-icon" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12,1L8,5H11V14H13V5H16M18,23H6C4.89,23 4,22.1 4,21V9A2,2 0 0,1 6,7H9V9H6V21H18V9H15V7H18A2,2 0 0,1 20,9V21A2,2 0 0,1 18,23Z"/>
                </svg>
                <h2 class="section-title">ارزش‌های ما</h2>
            </div>
            <div class="values-grid">
                <div class="value-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="value-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1Z"/>
                        </svg>
                    </div>
                    <h3>کیفیت برتر</h3>
                    <p>تمام کتاب‌های ما اصل و با بهترین کیفیت چاپ هستند</p>
                </div>
                
                <div class="value-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="value-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12,20A8,8 0 0,1 4,12A8,8 0 0,1 12,4A8,8 0 0,1 20,12A8,8 0 0,1 12,20M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12.5,7V12.25L17,14.92L16.25,16.15L11,13V7H12.5Z"/>
                        </svg>
                    </div>
                    <h3>ارسال سریع</h3>
                    <p>تحویل سفارش‌ها در کمتر از 48 ساعت در سراسر کشور</p>
                </div>
                
                <div class="value-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="value-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M7,15H9C9,16.08 10.37,17 12,17C13.63,17 15,16.08 15,15C15,13.9 13.96,13.5 11.76,12.97C9.64,12.44 7,11.78 7,9C7,7.21 8.47,5.69 10.5,5.18V3H13.5V5.18C15.53,5.69 17,7.21 17,9H15C15,7.92 13.63,7 12,7C10.37,7 9,7.92 9,9C9,10.1 10.04,10.5 12.24,11.03C14.36,11.56 17,12.22 17,15C17,16.79 15.53,18.31 13.5,18.82V21H10.5V18.82C8.47,18.31 7,16.79 7,15Z"/>
                        </svg>
                    </div>
                    <h3>قیمت مناسب</h3>
                    <p>بهترین قیمت‌ها با تخفیف‌های ویژه برای مشتریان وفادار</p>
                </div>
                
                <div class="value-card" data-aos="fade-up" data-aos-delay="400">
                    <div class="value-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12,2A3,3 0 0,1 15,5V11A3,3 0 0,1 12,14A3,3 0 0,1 9,11V5A3,3 0 0,1 12,2M19,11C19,14.53 16.39,17.44 13,17.93V21H11V17.93C7.61,17.44 5,14.53 5,11H7A5,5 0 0,0 12,16A5,5 0 0,0 17,11H19Z"/>
                        </svg>
                    </div>
                    <h3>پشتیبانی 24/7</h3>
                    <p>تیم پشتیبانی ما همیشه آماده پاسخگویی به شماست</p>
                </div>
            </div>
        </section>

        <!-- بخش آمار -->
        <section class="stats-section" data-aos="fade-up">
            <div class="stats-grid">
                <div class="stat-item" data-aos="fade-up" data-aos-delay="100">
                    <div class="stat-icon">📚</div>
                    <div class="stat-number">10,000+</div>
                    <div class="stat-label">کتاب متنوع</div>
                </div>
                
                <div class="stat-item" data-aos="fade-up" data-aos-delay="200">
                    <div class="stat-icon">👥</div>
                    <div class="stat-number">50,000+</div>
                    <div class="stat-label">کاربر فعال</div>
                </div>
                
                <div class="stat-item" data-aos="fade-up" data-aos-delay="300">
                    <div class="stat-icon">📦</div>
                    <div class="stat-number">100,000+</div>
                    <div class="stat-label">سفارش تحویل شده</div>
                </div>
                
                <div class="stat-item" data-aos="fade-up" data-aos-delay="400">
                    <div class="stat-icon">⭐</div>
                    <div class="stat-number">4.8/5</div>
                    <div class="stat-label">رضایت مشتریان</div>
                </div>
            </div>
        </section>

        <!-- بخش تیم ما -->
        <section class="team-section" data-aos="fade-up">
            <div class="section-header">
                <svg class="section-icon" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M16,13C15.71,13 15.38,13 15.03,13.05C16.19,13.89 17,15 17,16.5V19H23V16.5C23,14.17 18.33,13 16,13M8,13C5.67,13 1,14.17 1,16.5V19H15V16.5C15,14.17 10.33,13 8,13M8,11A3,3 0 0,0 11,8A3,3 0 0,0 8,5A3,3 0 0,0 5,8A3,3 0 0,0 8,11M16,11A3,3 0 0,0 19,8A3,3 0 0,0 16,5A3,3 0 0,0 13,8A3,3 0 0,0 16,11Z"/>
                </svg>
                <h2 class="section-title">تیم ما</h2>
            </div>
            <div class="team-grid">
                <div class="team-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="team-image">
                        <div class="avatar">👨‍💼</div>
                    </div>
                    <h3>علی احمدی</h3>
                    <p class="team-role">مدیر عامل</p>
                    <p class="team-desc">با بیش از 15 سال تجربه در صنعت نشر و کتاب</p>
                </div>
                
                <div class="team-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="team-image">
                        <div class="avatar">👩‍💻</div>
                    </div>
                    <h3>سارا محمدی</h3>
                    <p class="team-role">مدیر فنی</p>
                    <p class="team-desc">متخصص در توسعه پلتفرم‌های آنلاین</p>
                </div>
                
                <div class="team-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="team-image">
                        <div class="avatar">👨‍🎨</div>
                    </div>
                    <h3>رضا کریمی</h3>
                    <p class="team-role">مدیر محتوا</p>
                    <p class="team-desc">مسئول انتخاب و تأمین بهترین کتاب‌ها</p>
                </div>
            </div>
        </section>

        <!-- بخش تماس با ما -->
        <section class="contact-section" data-aos="fade-up">
            <div class="contact-card">
                <div class="contact-content">
                    <h2>با ما در ارتباط باشید</h2>
                    <p>سوال، پیشنهاد یا انتقادی دارید؟ دوست داریم از شما بشنویم!</p>
                    <div class="contact-methods">
                        <div class="contact-item">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M6.62,10.79C8.06,13.62 10.38,15.94 13.21,17.38L15.41,15.18C15.69,14.9 16.08,14.82 16.43,14.93C17.55,15.3 18.75,15.5 20,15.5A1,1 0 0,1 21,16.5V20A1,1 0 0,1 20,21A17,17 0 0,1 3,4A1,1 0 0,1 4,3H7.5A1,1 0 0,1 8.5,4C8.5,5.25 8.7,6.45 9.07,7.57C9.18,7.92 9.1,8.31 8.82,8.59L6.62,10.79Z"/>
                            </svg>
                            <div>
                                <strong>تلفن</strong>
                                <span>021-1234567</span>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20,8L12,13L4,8V6L12,11L20,6M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.1,4 20,4Z"/>
                            </svg>
                            <div>
                                <strong>ایمیل</strong>
                                <span>info@ketabnet.ir</span>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z"/>
                            </svg>
                            <div>
                                <strong>آدرس</strong>
                                <span>تهران، خیابان ولیعصر</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- سایدبار -->
    <aside class="sidebar">
        <?php require_once("./include/sidebar.php"); ?>
    </aside>

</div>

<?php require_once("./include/footer.php"); ?>

<style>
    /* ===== تنظیمات پایه ===== */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Vazirmatn', Tahoma, sans-serif;
        background: var(--bg-secondary);
        color: var(--text-primary);
        overflow-x: hidden;
    }

    .container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 1.5rem;
    }

    /* ===== بخش هیرو ===== */
    .about-hero {
        padding: 50px;
        border-radius: 12px;
        margin-top: 20px;
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        position: relative;
        overflow: hidden;
    }

    .about-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="100" height="100" patternUnits="userSpaceOnUse"><path d="M 100 0 L 0 0 0 100" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grid)"/></svg>');
        opacity: 0.3;
    }

    .hero-content {
        position: relative;
        z-index: 1;
        text-align: center;
        color: white;
    }

    .hero-badge {
        display: inline-block;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        color: white;
        padding: 0.5rem 1.2rem;
        border-radius: 50px;
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .hero-title {
        font-size: 3.5rem;
        font-weight: 800;
        margin-bottom: 1rem;
    }

    .gradient-text {
        background: linear-gradient(135deg, #fff 0%, #f0f0f0 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .hero-desc {
        font-size: 1.3rem;
        opacity: 0.95;
    }

    /* ===== کانتینر اصلی ===== */
    .page-wrapper {
        max-width: 1400px;
        margin: 0 auto;
        padding: 4rem 1.5rem;
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 3rem;
        align-items: start;
    }

    .main-content {
        display: flex;
        flex-direction: column;
        gap: 4rem;
    }

    /* ===== هدر بخش‌ها ===== */
    .section-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2.5rem;
    }

    .section-icon {
        width: 35px;
        height: 35px;
        color: var(--accent-primary);
    }

    .section-title {
        font-size: 2rem;
        font-weight: 800;
        color: var(--text-primary);
    }

    /* ===== بخش داستان ===== */
    .story-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 3rem;
        align-items: center;
    }

    .story-image {
        position: relative;
    }

    .story-image img {
        width: 100%;
        border-radius: 20px;
        box-shadow: var(--shadow-lg);
        position: relative;
        z-index: 2;
    }

    .image-decoration {
        position: absolute;
        top: -20px;
        right: -20px;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-hover) 100%);
        border-radius: 20px;
        z-index: 1;
    }

    .story-text p {
        font-size: 1.1rem;
        line-height: 1.9;
        color: var(--text-secondary);
        margin-bottom: 1.5rem;
        text-align: justify;
    }

    .story-text strong {
        color: var(--accent-primary);
        font-weight: 700;
    }

    /* ===== بخش ارزش‌ها ===== */
    .values-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
    }

    .value-card {
        background: var(--bg-primary);
        padding: 2rem;
        border-radius: 20px;
        text-align: center;
        border: 1px solid var(--border-color);
        transition: all 0.3s ease;
    }

    .value-card:hover {
        transform: translateY(-10px);
        box-shadow: var(--shadow-lg);
    }

    .value-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 1.5rem;
        background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-hover) 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

    .value-icon svg {
        width: 40px;
        height: 40px;
    }

    .value-card h3 {
        font-size: 1.3rem;
        margin-bottom: 1rem;
        color: var(--text-primary);
    }

    .value-card p {
        color: var(--text-secondary);
        line-height: 1.7;
    }

    /* ===== بخش آمار ===== */
    .stats-section {
        background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-hover) 100%);
        padding: 4rem 3rem;
        border-radius: 30px;
        box-shadow: var(--shadow-lg);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 3rem;
    }

    .stat-item {
        text-align: center;
        color: white;
    }

    .stat-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        display: block;
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        font-size: 1rem;
        opacity: 0.9;
    }

    /* ===== بخش تیم ===== */
    .team-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
    }

    .team-card {
        background: var(--bg-primary);
        padding: 2rem;
        border-radius: 20px;
        text-align: center;
        border: 1px solid var(--border-color);
        transition: all 0.3s ease;
    }

    .team-card:hover {
        transform: translateY(-10px);
        box-shadow: var(--shadow-lg);
    }

    .team-image {
        margin-bottom: 1.5rem;
    }

    .avatar {
        width: 120px;
        height: 120px;
        margin: 0 auto;
        background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-hover) 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 4rem;
    }

    .team-card h3 {
        font-size: 1.3rem;
        margin-bottom: 0.5rem;
        color: var(--text-primary);
    }

    .team-role {
        color: var(--accent-primary);
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .team-desc {
        color: var(--text-secondary);
        line-height: 1.6;
        font-size: 0.95rem;
    }

    /* ===== بخش تماس ===== */
    .contact-card {
        background: var(--bg-primary);
        padding: 3rem;
        border-radius: 20px;
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-md);
    }

    .contact-content h2 {
        font-size: 2rem;
        margin-bottom: 1rem;
        color: var(--text-primary);
    }

    .contact-content > p {
        color: var(--text-secondary);
        margin-bottom: 2rem;
        font-size: 1.1rem;
    }

    .contact-methods {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
    }

    .contact-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.5rem;
        background: var(--bg-secondary);
        border-radius: 15px;
        border: 1px solid var(--border-color);
        transition: all 0.3s ease;
    }

    .contact-item:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-md);
    }

    .contact-item svg {
        width: 35px;
        height: 35px;
        color: var(--accent-primary);
        flex-shrink: 0;
    }

    .contact-item div {
        flex: 1;
    }

    .contact-item strong {
        display: block;
        color: var(--text-primary);
        margin-bottom: 0.3rem;
        font-size: 1rem;
    }

    .contact-item span {
        color: var(--text-secondary);
        font-size: 0.95rem;
    }

    /* ===== Dark Mode ===== */
    body.dark-mode .about-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #3b82f6 100%);
    }

    /* ===== انیمیشن AOS ===== */
    [data-aos] {
        opacity: 0;
        transition-property: transform, opacity;
        transition-duration: 0.6s;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }

    [data-aos].aos-animate {
        opacity: 1;
    }

    [data-aos="fade-up"] {
        transform: translateY(30px);
    }

    [data-aos="fade-up"].aos-animate {
        transform: translateY(0);
    }

    [data-aos="fade-left"] {
        transform: translateX(-30px);
    }

    [data-aos="fade-left"].aos-animate {
        transform: translateX(0);
    }

    [data-aos="fade-right"] {
        transform: translateX(30px);
    }

    [data-aos="fade-right"].aos-animate {
        transform: translateX(0);
    }

    /* ===== Responsive ===== */
    @media (max-width: 1200px) {
        .page-wrapper {
            grid-template-columns: 1fr;
        }

        .sidebar {
            order: 2;
        }
    }

    @media (max-width: 991px) {
        .about-hero {
            padding: 10rem 0 4rem;
        }

        .hero-title {
            font-size: 2.5rem;
        }

        .story-content {
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        .values-grid,
        .team-grid {
            grid-template-columns: 1fr;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
        }

        .contact-methods {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 576px) {
        .about-hero {
            padding: 8rem 0 3rem;
        }

        .hero-title {
            font-size: 2rem;
        }

        .hero-desc {
            font-size: 1rem;
        }

        .page-wrapper {
            padding: 3rem 1rem;
        }

        .section-title {
            font-size: 1.5rem;
        }

        .stats-section {
            padding: 3rem 1.5rem;
        }

        .stats-grid {
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        .contact-card {
            padding: 2rem 1.5rem;
        }
    }
</style>

<script>
    // اسکریپت انیمیشن AOS
    document.addEventListener('DOMContentLoaded', function() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('aos-animate');
                }
            });
        }, observerOptions);

        document.querySelectorAll('[data-aos]').forEach(function(el) {
            observer.observe(el);
        });
    });
</script>