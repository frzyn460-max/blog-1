<?php
/**
 * فوتر سایت
 * شامل لینک‌های مهم، اطلاعات تماس و شبکه‌های اجتماعی
 */

// دریافت سال جاری
$current_year = date('Y');
?>

<footer class="main-footer">
    <div class="footer-container">
        
        <!-- بخش بالایی فوتر -->
        <div class="footer-top">
            
            <!-- ستون درباره ما -->
            <div class="footer-column">
                <div class="footer-logo">
                    <h3>📚 <?= escape(SITE_NAME) ?></h3>
                </div>
                <p class="footer-desc">
                    بزرگترین فروشگاه آنلاین کتاب در ایران با بیش از 10,000 عنوان کتاب 
                    در دسته‌بندی‌های مختلف. کیفیت، قیمت مناسب و ارسال سریع از ویژگی‌های ماست.
                </p>
                <div class="footer-badges">
                    <div class="badge-item">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1Z"/>
                        </svg>
                        <span>پرداخت امن</span>
                    </div>
                    <div class="badge-item">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M18,18.5A1.5,1.5 0 0,1 16.5,17A1.5,1.5 0 0,1 18,15.5A1.5,1.5 0 0,1 19.5,17A1.5,1.5 0 0,1 18,18.5M19.5,9.5L21.46,12H17V9.5M6,18.5A1.5,1.5 0 0,1 4.5,17A1.5,1.5 0 0,1 6,15.5A1.5,1.5 0 0,1 7.5,17A1.5,1.5 0 0,1 6,18.5M20,8H17V4H3C1.89,4 1,4.89 1,6V17H3A3,3 0 0,0 6,20A3,3 0 0,0 9,17H15A3,3 0 0,0 18,20A3,3 0 0,0 21,17H23V12L20,8Z"/>
                        </svg>
                        <span>ارسال سریع</span>
                    </div>
                </div>
            </div>

            <!-- ستون دسترسی سریع -->
            <div class="footer-column">
                <h4 class="footer-title">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M10,20V14H14V20H19V12H22L12,3L2,12H5V20H10Z"/>
                    </svg>
                    دسترسی سریع
                </h4>
                <ul class="footer-links">
                    <li><a href="index.php">🏠 صفحه اصلی</a></li>
                    <li><a href="products.php">📦 محصولات</a></li>
                    <li><a href="posts.php">📝 مقالات</a></li>
                    <li><a href="about.php">ℹ️ درباره ما</a></li>
                    <li><a href="contact.php">📞 تماس با ما</a></li>
                </ul>
            </div>

            <!-- ستون راهنما -->
            <div class="footer-column">
                <h4 class="footer-title">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4M11,7V13H13V7H11M11,15V17H13V15H11Z"/>
                    </svg>
                    راهنما
                </h4>
                <ul class="footer-links">
                    <li><a href="#">❓ سوالات متداول</a></li>
                    <li><a href="#">📋 قوانین و مقررات</a></li>
                    <li><a href="#">🔒 حریم خصوصی</a></li>
                    <li><a href="#">💳 نحوه خرید</a></li>
                    <li><a href="#">🚚 رویه ارسال</a></li>
                </ul>
            </div>

            <!-- ستون تماس با ما -->
            <div class="footer-column">
                <h4 class="footer-title">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M6.62,10.79C8.06,13.62 10.38,15.94 13.21,17.38L15.41,15.18C15.69,14.9 16.08,14.82 16.43,14.93C17.55,15.3 18.75,15.5 20,15.5A1,1 0 0,1 21,16.5V20A1,1 0 0,1 20,21A17,17 0 0,1 3,4A1,1 0 0,1 4,3H7.5A1,1 0 0,1 8.5,4C8.5,5.25 8.7,6.45 9.07,7.57C9.18,7.92 9.1,8.31 8.82,8.59L6.62,10.79Z"/>
                    </svg>
                    ارتباط با ما
                </h4>
                <ul class="footer-contact">
                    <li>
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M6.62,10.79C8.06,13.62 10.38,15.94 13.21,17.38L15.41,15.18C15.69,14.9 16.08,14.82 16.43,14.93C17.55,15.3 18.75,15.5 20,15.5A1,1 0 0,1 21,16.5V20A1,1 0 0,1 20,21A17,17 0 0,1 3,4A1,1 0 0,1 4,3H7.5A1,1 0 0,1 8.5,4C8.5,5.25 8.7,6.45 9.07,7.57C9.18,7.92 9.1,8.31 8.82,8.59L6.62,10.79Z"/>
                        </svg>
                        <span dir="ltr">021-1234567</span>
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20,8L12,13L4,8V6L12,11L20,6M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.1,4 20,4Z"/>
                        </svg>
                        <span>info@ketabnet.ir</span>
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z"/>
                        </svg>
                        <span>تهران، خیابان ولیعصر</span>
                    </li>
                </ul>
            </div>

        </div>

        <!-- خط جداکننده -->
        <div class="footer-divider"></div>

        <!-- بخش پایین فوتر -->
        <div class="footer-bottom">
            
            <!-- شبکه‌های اجتماعی -->
            <div class="social-section">
                <span class="social-title">ما را دنبال کنید:</span>
                <div class="social-icons">
                    <a href="#" class="social-link instagram" aria-label="اینستاگرام">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M7.8,2H16.2C19.4,2 22,4.6 22,7.8V16.2A5.8,5.8 0 0,1 16.2,22H7.8C4.6,22 2,19.4 2,16.2V7.8A5.8,5.8 0 0,1 7.8,2M7.6,4A3.6,3.6 0 0,0 4,7.6V16.4C4,18.39 5.61,20 7.6,20H16.4A3.6,3.6 0 0,0 20,16.4V7.6C20,5.61 18.39,4 16.4,4H7.6M17.25,5.5A1.25,1.25 0 0,1 18.5,6.75A1.25,1.25 0 0,1 17.25,8A1.25,1.25 0 0,1 16,6.75A1.25,1.25 0 0,1 17.25,5.5M12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9Z"/>
                        </svg>
                    </a>
                    <a href="#" class="social-link telegram" aria-label="تلگرام">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M9.78,18.65L10.06,14.42L17.74,7.5C18.08,7.19 17.67,7.04 17.22,7.31L7.74,13.3L3.64,12C2.76,11.75 2.75,11.14 3.84,10.7L19.81,4.54C20.54,4.21 21.24,4.72 20.96,5.84L18.24,18.65C18.05,19.56 17.5,19.78 16.74,19.36L12.6,16.3L10.61,18.23C10.38,18.46 10.19,18.65 9.78,18.65Z"/>
                        </svg>
                    </a>
                    <a href="#" class="social-link twitter" aria-label="توییتر">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M22.46,6C21.69,6.35 20.86,6.58 20,6.69C20.88,6.16 21.56,5.32 21.88,4.31C21.05,4.81 20.13,5.16 19.16,5.36C18.37,4.5 17.26,4 16,4C13.65,4 11.73,5.92 11.73,8.29C11.73,8.63 11.77,8.96 11.84,9.27C8.28,9.09 5.11,7.38 3,4.79C2.63,5.42 2.42,6.16 2.42,6.94C2.42,8.43 3.17,9.75 4.33,10.5C3.62,10.5 2.96,10.3 2.38,10C2.38,10 2.38,10 2.38,10.03C2.38,12.11 3.86,13.85 5.82,14.24C5.46,14.34 5.08,14.39 4.69,14.39C4.42,14.39 4.15,14.36 3.89,14.31C4.43,16 6,17.26 7.89,17.29C6.43,18.45 4.58,19.13 2.56,19.13C2.22,19.13 1.88,19.11 1.54,19.07C3.44,20.29 5.70,21 8.12,21C16,21 20.33,14.46 20.33,8.79C20.33,8.6 20.33,8.42 20.32,8.23C21.16,7.63 21.88,6.87 22.46,6Z"/>
                        </svg>
                    </a>
                    <a href="#" class="social-link youtube" aria-label="یوتیوب">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M10,15L15.19,12L10,9V15M21.56,7.17C21.69,7.64 21.78,8.27 21.84,9.07C21.91,9.87 21.94,10.56 21.94,11.16L22,12C22,14.19 21.84,15.8 21.56,16.83C21.31,17.73 20.73,18.31 19.83,18.56C19.36,18.69 18.5,18.78 17.18,18.84C15.88,18.91 14.69,18.94 13.59,18.94L12,19C7.81,19 5.2,18.84 4.17,18.56C3.27,18.31 2.69,17.73 2.44,16.83C2.31,16.36 2.22,15.73 2.16,14.93C2.09,14.13 2.06,13.44 2.06,12.84L2,12C2,9.81 2.16,8.2 2.44,7.17C2.69,6.27 3.27,5.69 4.17,5.44C4.64,5.31 5.5,5.22 6.82,5.16C8.12,5.09 9.31,5.06 10.41,5.06L12,5C16.19,5 18.8,5.16 19.83,5.44C20.73,5.69 21.31,6.27 21.56,7.17Z"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- متن کپی‌رایت -->
            <div class="copyright">
                <p>
                    © <?= $current_year ?> 
                    <strong><?= escape(SITE_NAME) ?></strong> 
                    - تمامی حقوق محفوظ است
                </p>
                <p class="developer">
                    طراحی و توسعه با 
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12,21.35L10.55,20.03C5.4,15.36 2,12.27 2,8.5C2,5.41 4.42,3 7.5,3C9.24,3 10.91,3.81 12,5.08C13.09,3.81 14.76,3 16.5,3C19.58,3 22,5.41 22,8.5C22,12.27 18.6,15.36 13.45,20.03L12,21.35Z"/>
                    </svg>
                    توسط تیم برنامه‌نویسی
                </p>
            </div>

            <!-- نمادها -->
            <div class="footer-logos">
                <div class="logo-placeholder">
                    <span>🏆</span>
                </div>
                <div class="logo-placeholder">
                    <span>✅</span>
                </div>
            </div>

        </div>

    </div>

    <!-- دکمه بازگشت به بالا -->
    <button id="backToTop" class="back-to-top" aria-label="بازگشت به بالا">
        <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M7.41,15.41L12,10.83L16.59,15.41L18,14L12,8L6,14L7.41,15.41Z"/>
        </svg>
    </button>
</footer>

<style>
    /* ===== استایل فوتر ===== */
    .main-footer {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        color: #e0e2e7;
        margin-top: 5rem;
        padding: 3rem 1.5rem 2rem;
        border-radius: 30px 30px 0 0;
        position: relative;
        box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.3);
    }

    .footer-container {
        max-width: 1400px;
        margin: 0 auto;
    }

    /* بخش بالایی */
    .footer-top {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 3rem;
        margin-bottom: 3rem;
    }

    .footer-column {
        display: flex;
        flex-direction: column;
        gap: 1.2rem;
    }

    /* لوگو و توضیحات */
    .footer-logo h3 {
        font-size: 1.8rem;
        font-weight: 800;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 0.5rem;
    }

    .footer-desc {
        color: #9ca3af;
        line-height: 1.8;
        font-size: 0.95rem;
    }

    /* نشان‌ها */
    .footer-badges {
        display: flex;
        gap: 1rem;
        margin-top: 1rem;
    }

    .badge-item {
        display: flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.05);
        padding: 0.7rem 1rem;
        border-radius: 12px;
        font-size: 0.85rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .badge-item svg {
        width: 20px;
        height: 20px;
        color: #10b981;
    }

    /* عنوان ستون‌ها */
    .footer-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.2rem;
        font-weight: 700;
        color: #fff;
        margin-bottom: 0.5rem;
    }

    .footer-title svg {
        width: 24px;
        height: 24px;
        color: #667eea;
    }

    /* لینک‌ها */
    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 0.8rem;
    }

    .footer-links a {
        color: #9ca3af;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        font-size: 0.95rem;
        position: relative;
        padding-right: 0;
    }

    .footer-links a::before {
        content: '';
        position: absolute;
        right: -20px;
        width: 0;
        height: 2px;
        background: linear-gradient(90deg, #667eea, transparent);
        transition: width 0.3s ease;
    }

    .footer-links a:hover {
        color: #fff;
        transform: translateX(-5px);
    }

    .footer-links a:hover::before {
        width: 15px;
    }

    /* اطلاعات تماس */
    .footer-contact {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .footer-contact li {
        display: flex;
        align-items: center;
        gap: 12px;
        color: #9ca3af;
        font-size: 0.95rem;
    }

    .footer-contact svg {
        width: 20px;
        height: 20px;
        color: #667eea;
        flex-shrink: 0;
    }

    /* خط جداکننده */
    .footer-divider {
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        margin: 2rem 0;
    }

    /* بخش پایین */
    .footer-bottom {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 2rem;
    }

    /* شبکه‌های اجتماعی */
    .social-section {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .social-title {
        font-weight: 600;
        color: #fff;
        font-size: 1rem;
    }

    .social-icons {
        display: flex;
        gap: 1rem;
    }

    .social-link {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #fff;
    }

    .social-link svg {
        width: 22px;
        height: 22px;
    }

    .social-link:hover {
        transform: translateY(-5px) rotate(5deg);
    }

    .instagram:hover {
        background: linear-gradient(135deg, #f58529, #dd2a7b, #8134af);
        box-shadow: 0 8px 20px rgba(221, 42, 123, 0.4);
    }

    .telegram:hover {
        background: linear-gradient(135deg, #0088cc, #229ed9);
        box-shadow: 0 8px 20px rgba(0, 136, 204, 0.4);
    }

    .twitter:hover {
        background: linear-gradient(135deg, #1da1f2, #0d8bd9);
        box-shadow: 0 8px 20px rgba(29, 161, 242, 0.4);
    }

    .youtube:hover {
        background: linear-gradient(135deg, #ff0000, #cc0000);
        box-shadow: 0 8px 20px rgba(255, 0, 0, 0.4);
    }

    /* کپی‌رایت */
    .copyright {
        text-align: center;
        flex: 1;
    }

    .copyright p {
        color: #9ca3af;
        font-size: 0.9rem;
        margin: 0.3rem 0;
    }

    .copyright strong {
        color: #fff;
        font-weight: 700;
    }

    .developer {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-size: 0.85rem !important;
        margin-top: 0.5rem !important;
    }

    .developer svg {
        width: 16px;
        height: 16px;
        color: #ef4444;
        animation: heartbeat 1.5s infinite;
    }

    @keyframes heartbeat {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.2); }
    }

    /* نمادها */
    .footer-logos {
        display: flex;
        gap: 1rem;
    }

    .logo-placeholder {
        width: 60px;
        height: 60px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(255, 255, 255, 0.1);
        font-size: 1.5rem;
    }

    /* دکمه بازگشت به بالا */
    .back-to-top {
        position: fixed;
        bottom: 30px;
        left: 30px;
        width: 55px;
        height: 55px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 50%;
        color: white;
        cursor: pointer;
        display: none;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        transition: all 0.3s ease;
        z-index: 999;
    }

    .back-to-top:hover {
        transform: translateY(-5px) scale(1.1);
        box-shadow: 0 12px 30px rgba(102, 126, 234, 0.6);
    }

    .back-to-top.show {
        display: flex;
    }

    .back-to-top svg {
        width: 24px;
        height: 24px;
    }

    /* ===== Dark Mode ===== */
    body.dark-mode .main-footer {
        background: linear-gradient(135deg, #0a0e27 0%, #1a1c3a 100%);
    }

    /* ===== Responsive ===== */
    @media (max-width: 991px) {
        .footer-top {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
        }

        .footer-bottom {
            flex-direction: column;
            text-align: center;
        }

        .social-section {
            flex-direction: column;
            gap: 1rem;
        }

        .footer-logos {
            justify-content: center;
        }

        .back-to-top {
            bottom: 20px;
            left: 20px;
            width: 50px;
            height: 50px;
        }
    }

    @media (max-width: 576px) {
        .main-footer {
            padding: 2rem 1rem 1.5rem;
        }

        .footer-top {
            gap: 1.5rem;
        }

        .footer-badges {
            flex-direction: column;
        }

        .social-icons {
            flex-wrap: wrap;
            justify-content: center;
        }
    }
</style>

<script>
    // اسکریپت دکمه بازگشت به بالا
    document.addEventListener('DOMContentLoaded', () => {
        const backToTopBtn = document.getElementById('backToTop');
        
        // نمایش/مخفی کردن دکمه
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTopBtn.classList.add('show');
            } else {
                backToTopBtn.classList.remove('show');
            }
        });
        
        // اسکرول به بالا با انیمیشن
        backToTopBtn?.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });
</script>

</body>
</html>