<?php
/**
 * فوتر سایت — بازطراحی شده
 * تم: Midnight Blue — هماهنگ با هدر و صفحه اصلی
 */
$current_year = date('Y');
?>

<footer class="ftr">

    <!-- ══ محتوای اصلی ══ -->
    <div class="ftr-body">

        <!-- نقاط پس‌زمینه -->
        <div class="ftr-dots"></div>
        <!-- درخشش‌های نوری -->
        <div class="ftr-glow ftr-glow--right"></div>
        <div class="ftr-glow ftr-glow--left"></div>

        <div class="ftr-inner">

            <!-- ══ ردیف اصلی ══ -->
            <div class="ftr-grid">

                <!-- ستون ۱ — برند -->
                <div class="ftr-col ftr-col--brand">
                    <a href="index.php" class="ftr-logo">
                        <span class="ftr-logo-icon">📚</span>
                        <span class="ftr-logo-text"><?= escape(SITE_NAME) ?></span>
                    </a>
                    <p class="ftr-tagline">
                        دروازه‌ای به دنیای دانش — بزرگ‌ترین فروشگاه آنلاین کتاب با بیش از
                        <strong>۱۰,۰۰۰</strong> عنوان در دسته‌بندی‌های متنوع.
                    </p>

                    <!-- نشان‌ها -->
                    <div class="ftr-badges">
                        <div class="ftr-badge">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1Z"/></svg>
                            پرداخت امن
                        </div>
                        <div class="ftr-badge">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18,18.5A1.5,1.5 0 0,1 16.5,17A1.5,1.5 0 0,1 18,15.5A1.5,1.5 0 0,1 19.5,17A1.5,1.5 0 0,1 18,18.5M19.5,9.5L21.46,12H17V9.5M6,18.5A1.5,1.5 0 0,1 4.5,17A1.5,1.5 0 0,1 6,15.5A1.5,1.5 0 0,1 7.5,17A1.5,1.5 0 0,1 6,18.5M20,8H17V4H3C1.89,4 1,4.89 1,6V17H3A3,3 0 0,0 6,20A3,3 0 0,0 9,17H15A3,3 0 0,0 18,20A3,3 0 0,0 21,17H23V12L20,8Z"/></svg>
                            ارسال سریع
                        </div>
                        <div class="ftr-badge">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,20A8,8 0 0,1 4,12A8,8 0 0,1 12,4A8,8 0 0,1 20,12A8,8 0 0,1 12,20M13,9.94L14.06,11L15.12,9.94L16.18,11L17.24,9.94L15.12,7.82L13,9.94M8.88,9.94L9.94,11L11,9.94L8.88,7.82L6.76,9.94L7.82,11L8.88,9.94M12,17.5C14.33,17.5 16.3,16.04 17.11,14H6.89C7.7,16.04 9.67,17.5 12,17.5Z"/></svg>
                            رضایت مشتری
                        </div>
                    </div>

                    <!-- شبکه‌های اجتماعی -->
                    <div class="ftr-socials">
                        <a href="#" class="ftr-social ftr-social--ig" aria-label="اینستاگرام">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M7.8,2H16.2C19.4,2 22,4.6 22,7.8V16.2A5.8,5.8 0 0,1 16.2,22H7.8C4.6,22 2,19.4 2,16.2V7.8A5.8,5.8 0 0,1 7.8,2M7.6,4A3.6,3.6 0 0,0 4,7.6V16.4C4,18.39 5.61,20 7.6,20H16.4A3.6,3.6 0 0,0 20,16.4V7.6C20,5.61 18.39,4 16.4,4H7.6M17.25,5.5A1.25,1.25 0 0,1 18.5,6.75A1.25,1.25 0 0,1 17.25,8A1.25,1.25 0 0,1 16,6.75A1.25,1.25 0 0,1 17.25,5.5M12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9Z"/></svg>
                        </a>
                        <a href="#" class="ftr-social ftr-social--tg" aria-label="تلگرام">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9.78,18.65L10.06,14.42L17.74,7.5C18.08,7.19 17.67,7.04 17.22,7.31L7.74,13.3L3.64,12C2.76,11.75 2.75,11.14 3.84,10.7L19.81,4.54C20.54,4.21 21.24,4.72 20.96,5.84L18.24,18.65C18.05,19.56 17.5,19.78 16.74,19.36L12.6,16.3L10.61,18.23C10.38,18.46 10.19,18.65 9.78,18.65Z"/></svg>
                        </a>
                        <a href="#" class="ftr-social ftr-social--tw" aria-label="توییتر">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M22.46,6C21.69,6.35 20.86,6.58 20,6.69C20.88,6.16 21.56,5.32 21.88,4.31C21.05,4.81 20.13,5.16 19.16,5.36C18.37,4.5 17.26,4 16,4C13.65,4 11.73,5.92 11.73,8.29C11.73,8.63 11.77,8.96 11.84,9.27C8.28,9.09 5.11,7.38 3,4.79C2.63,5.42 2.42,6.16 2.42,6.94C2.42,8.43 3.17,9.75 4.33,10.5C3.62,10.5 2.96,10.3 2.38,10C2.38,12.11 3.86,13.85 5.82,14.24C5.46,14.34 5.08,14.39 4.69,14.39C4.42,14.39 4.15,14.36 3.89,14.31C4.43,16 6,17.26 7.89,17.29C6.43,18.45 4.58,19.13 2.56,19.13C2.22,19.13 1.88,19.11 1.54,19.07C3.44,20.29 5.7,21 8.12,21C16,21 20.33,14.46 20.33,8.79C20.33,8.6 20.33,8.42 20.32,8.23C21.16,7.63 21.88,6.87 22.46,6Z"/></svg>
                        </a>
                        <a href="#" class="ftr-social ftr-social--yt" aria-label="یوتیوب">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M10,15L15.19,12L10,9V15M21.56,7.17C21.69,7.64 21.78,8.27 21.84,9.07C21.91,9.87 21.94,10.56 21.94,11.16L22,12C22,14.19 21.84,15.8 21.56,16.83C21.31,17.73 20.73,18.31 19.83,18.56C19.36,18.69 18.5,18.78 17.18,18.84C15.88,18.91 14.69,18.94 13.59,18.94L12,19C7.81,19 5.2,18.84 4.17,18.56C3.27,18.31 2.69,17.73 2.44,16.83C2.31,16.36 2.22,15.73 2.16,14.93C2.09,14.13 2.06,13.44 2.06,12.84L2,12C2,9.81 2.16,8.2 2.44,7.17C2.69,6.27 3.27,5.69 4.17,5.44C4.64,5.31 5.5,5.22 6.82,5.16C8.12,5.09 9.31,5.06 10.41,5.06L12,5C16.19,5 18.8,5.16 19.83,5.44C20.73,5.69 21.31,6.27 21.56,7.17Z"/></svg>
                        </a>
                    </div>
                </div>

                <!-- ستون ۲ — لینک‌های سریع -->
                <div class="ftr-col">
                    <h4 class="ftr-col-title">
                        <span class="ftr-col-title-dot"></span>
                        دسترسی سریع
                    </h4>
                    <ul class="ftr-links">
                        <li><a href="index.php" class="ftr-link"><span>🏠</span> صفحه اصلی</a></li>
                        <li><a href="products.php" class="ftr-link"><span>📦</span> محصولات</a></li>
                        <li><a href="posts.php" class="ftr-link"><span>📝</span> مقالات</a></li>
                        <li><a href="about.php" class="ftr-link"><span>ℹ️</span> درباره ما</a></li>
                        <li><a href="cart.php" class="ftr-link"><span>🛒</span> سبد خرید</a></li>
                    </ul>
                </div>

                <!-- ستون ۳ — راهنما -->
                <div class="ftr-col">
                    <h4 class="ftr-col-title">
                        <span class="ftr-col-title-dot"></span>
                        راهنما
                    </h4>
                    <ul class="ftr-links">
                        <li><a href="#" class="ftr-link"><span>❓</span> سوالات متداول</a></li>
                        <li><a href="#" class="ftr-link"><span>📋</span> قوانین و مقررات</a></li>
                        <li><a href="#" class="ftr-link"><span>🔒</span> حریم خصوصی</a></li>
                        <li><a href="#" class="ftr-link"><span>💳</span> نحوه خرید</a></li>
                        <li><a href="#" class="ftr-link"><span>🚚</span> رویه ارسال</a></li>
                    </ul>
                </div>

                <!-- ستون ۴ — خبرنامه + تماس -->
                <div class="ftr-col ftr-col--news">
                    <h4 class="ftr-col-title">
                        <span class="ftr-col-title-dot"></span>
                        خبرنامه
                    </h4>
                    <p class="ftr-news-desc">از آخرین تخفیف‌ها و کتاب‌های جدید باخبر شو</p>
                    <form class="ftr-news-form" onsubmit="ftrNewsSubmit(event)">
                        <input type="email" class="ftr-news-input" placeholder="ایمیل شما..." required>
                        <button type="submit" class="ftr-news-btn">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M2,21L23,12L2,3V10L17,12L2,14V21Z"/></svg>
                        </button>
                    </form>
                    <p class="ftr-news-note">🔒 هرگز اسپم نمی‌فرستیم</p>

                    <!-- اطلاعات تماس -->
                    <div class="ftr-contacts">
                        <a href="tel:02112345678" class="ftr-contact-item">
                            <div class="ftr-contact-icon">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M6.62,10.79C8.06,13.62 10.38,15.94 13.21,17.38L15.41,15.18C15.69,14.9 16.08,14.82 16.43,14.93C17.55,15.3 18.75,15.5 20,15.5A1,1 0 0,1 21,16.5V20A1,1 0 0,1 20,21A17,17 0 0,1 3,4A1,1 0 0,1 4,3H7.5A1,1 0 0,1 8.5,4C8.5,5.25 8.7,6.45 9.07,7.57C9.18,7.92 9.1,8.31 8.82,8.59L6.62,10.79Z"/></svg>
                            </div>
                            <span dir="ltr">021-1234567</span>
                        </a>
                        <a href="mailto:info@ketabnet.ir" class="ftr-contact-item">
                            <div class="ftr-contact-icon">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20,8L12,13L4,8V6L12,11L20,6M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.1,4 20,4Z"/></svg>
                            </div>
                            <span>info@ketabnet.ir</span>
                        </a>
                    </div>
                </div>

            </div>

            <!-- ══ خط جداکننده با گرادیانت ══ -->
            <div class="ftr-divider">
                <div class="ftr-divider-line"></div>
                <div class="ftr-divider-logo">📚</div>
                <div class="ftr-divider-line"></div>
            </div>

            <!-- ══ پایین فوتر ══ -->
            <div class="ftr-bottom">
                <div class="ftr-copy">
                    © <?= $current_year ?> <strong><?= escape(SITE_NAME) ?></strong> — تمامی حقوق محفوظ است
                </div>
                <div class="ftr-bottom-links">
                    <a href="#">حریم خصوصی</a>
                    <span class="ftr-sep">·</span>
                    <a href="#">قوانین</a>
                    <span class="ftr-sep">·</span>
                    <a href="#">تماس</a>
                </div>
                <div class="ftr-made">
                    ساخته شده با
                    <svg viewBox="0 0 24 24" fill="currentColor" class="ftr-heart"><path d="M12,21.35L10.55,20.03C5.4,15.36 2,12.27 2,8.5C2,5.41 4.42,3 7.5,3C9.24,3 10.91,3.81 12,5.08C13.09,3.81 14.76,3 16.5,3C19.58,3 22,5.41 22,8.5C22,12.27 18.6,15.36 13.45,20.03L12,21.35Z"/></svg>
                </div>
            </div>

        </div>
    </div>

    <!-- ══ دکمه بازگشت به بالا ══ -->
    <button id="backToTop" class="ftr-btt" aria-label="بازگشت به بالا">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M7.41,15.41L12,10.83L16.59,15.41L18,14L12,8L6,14L7.41,15.41Z"/></svg>
    </button>

    <!-- ══ Toast خبرنامه ══ -->
    <div class="ftr-toast" id="ftrToast"></div>

</footer>

<style>
/* ════════════════════════════════════════════════════════
   FOOTER — Midnight Blue — هماهنگ با هدر و صفحه اصلی
   ════════════════════════════════════════════════════════ */

.ftr {
    position: relative;
    margin-top: 6rem;
}

/* بدنه اصلی */
.ftr-body {
    position: relative;
    background: linear-gradient(170deg, #0f172a 0%, #1e293b 40%, #0f172a 100%);
    overflow: hidden;
    padding-bottom: 0;
    border-radius: 20px 20px 0 0;
}

/* نقاط پس‌زمینه */
.ftr-dots {
    position: absolute; inset: 0; pointer-events: none; z-index: 0;
    background-image: radial-gradient(circle, rgba(96,165,250,.07) 1.5px, transparent 1.5px);
    background-size: 36px 36px;
}

/* درخشش‌های نوری */
.ftr-glow {
    position: absolute; border-radius: 50%; pointer-events: none; z-index: 0;
}
.ftr-glow--right {
    width: 600px; height: 600px;
    background: radial-gradient(circle, rgba(30,58,138,.2) 0%, transparent 65%);
    top: -200px; right: -150px;
    animation: ftrGlow1 7s ease-in-out infinite;
}
.ftr-glow--left {
    width: 500px; height: 500px;
    background: radial-gradient(circle, rgba(109,40,217,.12) 0%, transparent 65%);
    bottom: -100px; left: -100px;
    animation: ftrGlow2 9s ease-in-out infinite;
}
@keyframes ftrGlow1 { 0%,100%{opacity:.7} 50%{opacity:1} }
@keyframes ftrGlow2 { 0%,100%{opacity:.5} 50%{opacity:.9} }

.ftr-inner {
    position: relative; z-index: 2;
    max-width: 1400px; margin: 0 auto;
    padding: 5rem 2rem 3rem;
}

/* ══ گرید اصلی ══ */
.ftr-grid {
    display: grid;
    grid-template-columns: 1.6fr 1fr 1fr 1.5fr;
    gap: 3.5rem;
    margin-bottom: 4rem;
}

/* ══ لوگو ══ */
.ftr-logo {
    display: inline-flex; align-items: center; gap: .7rem;
    text-decoration: none; margin-bottom: 1.4rem;
}
.ftr-logo-icon { font-size: 2.2rem; animation: logoFloat 3.5s ease-in-out infinite; }
@keyframes logoFloat { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-6px)} }
.ftr-logo-text {
    font-size: 1.6rem; font-weight: 900; color: #fff;
    background: linear-gradient(135deg, #60a5fa, #a78bfa);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
}

.ftr-tagline {
    color: rgba(255,255,255,.5); font-size: .9rem; line-height: 1.85;
    margin-bottom: 1.8rem; max-width: 280px;
}
.ftr-tagline strong { color: #60a5fa; }

/* نشان‌ها */
.ftr-badges { display: flex; flex-wrap: wrap; gap: .6rem; margin-bottom: 2rem; }
.ftr-badge {
    display: inline-flex; align-items: center; gap: .45rem;
    background: rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.1);
    color: rgba(255,255,255,.65); padding: .45rem .9rem;
    border-radius: 50px; font-size: .78rem; font-weight: 600;
    transition: all .3s;
}
.ftr-badge:hover { background: rgba(59,130,246,.15); border-color: rgba(96,165,250,.3); color: #60a5fa; }
.ftr-badge svg { width: 15px; height: 15px; color: #60a5fa; flex-shrink: 0; }

/* شبکه‌های اجتماعی */
.ftr-socials { display: flex; gap: .7rem; }
.ftr-social {
    width: 42px; height: 42px; border-radius: 12px;
    background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1);
    color: rgba(255,255,255,.6); display: flex; align-items: center; justify-content: center;
    transition: all .35s cubic-bezier(.4,0,.2,1); text-decoration: none;
}
.ftr-social svg { width: 19px; height: 19px; }
.ftr-social:hover { transform: translateY(-5px) scale(1.08); }
.ftr-social--ig:hover { background: linear-gradient(135deg,#f58529,#dd2a7b,#8134af); border-color: transparent; color: #fff; box-shadow: 0 8px 24px rgba(221,42,123,.4); }
.ftr-social--tg:hover { background: linear-gradient(135deg,#0088cc,#229ed9); border-color: transparent; color: #fff; box-shadow: 0 8px 24px rgba(0,136,204,.4); }
.ftr-social--tw:hover { background: linear-gradient(135deg,#1da1f2,#0d8bd9); border-color: transparent; color: #fff; box-shadow: 0 8px 24px rgba(29,161,242,.4); }
.ftr-social--yt:hover { background: linear-gradient(135deg,#ff0000,#cc0000); border-color: transparent; color: #fff; box-shadow: 0 8px 24px rgba(255,0,0,.4); }

/* ══ عنوان ستون‌ها ══ */
.ftr-col-title {
    display: flex; align-items: center; gap: .7rem;
    font-size: 1rem; font-weight: 800; color: #fff;
    margin-bottom: 1.6rem; letter-spacing: .02em;
}
.ftr-col-title-dot {
    width: 8px; height: 8px; border-radius: 50%;
    background: linear-gradient(135deg, #3b82f6, #8b5cf6);
    box-shadow: 0 0 10px rgba(59,130,246,.6);
    flex-shrink: 0;
    animation: dotPulse 2.5s ease-in-out infinite;
}
@keyframes dotPulse { 0%,100%{box-shadow:0 0 8px rgba(59,130,246,.5)} 50%{box-shadow:0 0 18px rgba(59,130,246,.9)} }

/* ══ لینک‌ها ══ */
.ftr-links { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: .6rem; }
.ftr-link {
    display: flex; align-items: center; gap: .65rem;
    color: rgba(255,255,255,.5); text-decoration: none;
    font-size: .9rem; font-weight: 500;
    padding: .55rem .7rem; border-radius: 10px;
    transition: all .3s; position: relative; overflow: hidden;
}
.ftr-link::before {
    content: '';
    position: absolute; inset: 0;
    background: rgba(59,130,246,.08);
    transform: translateX(100%); transition: transform .3s ease;
    border-radius: 10px;
}
.ftr-link:hover { color: #fff; transform: translateX(-4px); }
.ftr-link:hover::before { transform: translateX(0); }
.ftr-link span { font-size: .95rem; flex-shrink: 0; }

/* ══ خبرنامه ══ */
.ftr-news-desc { color: rgba(255,255,255,.45); font-size: .85rem; line-height: 1.7; margin-bottom: 1.1rem; }

.ftr-news-form {
    display: flex; gap: .5rem; margin-bottom: .7rem;
}
.ftr-news-input {
    flex: 1; padding: .8rem 1.1rem;
    background: rgba(255,255,255,.07); border: 1px solid rgba(255,255,255,.12);
    border-radius: 12px; color: #fff; font-family: inherit; font-size: .88rem;
    outline: none; transition: all .3s; min-width: 0;
}
.ftr-news-input::placeholder { color: rgba(255,255,255,.3); }
.ftr-news-input:focus { border-color: rgba(96,165,250,.5); background: rgba(255,255,255,.1); box-shadow: 0 0 0 3px rgba(59,130,246,.15); }

.ftr-news-btn {
    width: 46px; height: 46px; border-radius: 12px; flex-shrink: 0;
    background: linear-gradient(135deg, #1e3a8a, #3b82f6);
    border: none; color: #fff; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: all .3s; box-shadow: 0 4px 16px rgba(30,58,138,.4);
}
.ftr-news-btn:hover { transform: scale(1.08); box-shadow: 0 8px 24px rgba(30,58,138,.6); }
.ftr-news-btn svg { width: 18px; height: 18px; }

.ftr-news-note { font-size: .75rem; color: rgba(255,255,255,.3); margin-bottom: 1.8rem; }

/* اطلاعات تماس */
.ftr-contacts { display: flex; flex-direction: column; gap: .75rem; }
.ftr-contact-item {
    display: flex; align-items: center; gap: .8rem;
    color: rgba(255,255,255,.5); text-decoration: none; font-size: .88rem;
    transition: color .3s;
}
.ftr-contact-item:hover { color: #60a5fa; }
.ftr-contact-icon {
    width: 34px; height: 34px; border-radius: 9px; flex-shrink: 0;
    background: rgba(59,130,246,.12); border: 1px solid rgba(96,165,250,.18);
    display: flex; align-items: center; justify-content: center;
    transition: all .3s;
}
.ftr-contact-item:hover .ftr-contact-icon { background: rgba(59,130,246,.25); border-color: rgba(96,165,250,.4); }
.ftr-contact-icon svg { width: 16px; height: 16px; color: #60a5fa; }

/* ══ خط جداکننده ══ */
.ftr-divider {
    display: flex; align-items: center; gap: 1.2rem;
    margin-bottom: 2.5rem;
}
.ftr-divider-line {
    flex: 1; height: 1px;
    background: linear-gradient(90deg, transparent, rgba(96,165,250,.2), transparent);
}
.ftr-divider-logo { font-size: 1.4rem; opacity: .4; }

/* ══ پایین فوتر ══ */
.ftr-bottom {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 1rem;
    padding: 1.8rem 0;
    border-top: 1px solid rgba(255,255,255,.06);
}
.ftr-copy { color: rgba(255,255,255,.35); font-size: .82rem; }
.ftr-copy strong { color: rgba(255,255,255,.6); }

.ftr-bottom-links { display: flex; align-items: center; gap: .6rem; }
.ftr-bottom-links a { color: rgba(255,255,255,.35); font-size: .82rem; text-decoration: none; transition: color .3s; }
.ftr-bottom-links a:hover { color: #60a5fa; }
.ftr-sep { color: rgba(255,255,255,.2); }

.ftr-made {
    display: flex; align-items: center; gap: .35rem;
    color: rgba(255,255,255,.3); font-size: .82rem;
}
.ftr-heart { width: 14px; height: 14px; color: #ef4444; animation: heartbeat 1.4s infinite; flex-shrink: 0; }
@keyframes heartbeat { 0%,100%{transform:scale(1)} 50%{transform:scale(1.25)} }

/* ══ دکمه بازگشت به بالا ══ */
.ftr-btt {
    position: fixed; bottom: 28px; left: 28px; z-index: 999;
    width: 52px; height: 52px; border-radius: 50%;
    background: linear-gradient(135deg, #1e3a8a, #3b82f6);
    border: none; color: #fff; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 8px 24px rgba(30,58,138,.5);
    opacity: 0; pointer-events: none;
    transform: translateY(16px);
    transition: all .35s cubic-bezier(.4,0,.2,1);
}
.ftr-btt.show { opacity: 1; pointer-events: all; transform: translateY(0); }
.ftr-btt:hover { transform: translateY(-4px) scale(1.08); box-shadow: 0 14px 32px rgba(30,58,138,.7); }
.ftr-btt svg { width: 22px; height: 22px; }

/* ══ Toast ══ */
.ftr-toast {
    position: fixed; bottom: 28px; right: 28px; z-index: 9999;
    background: linear-gradient(135deg, #1e3a8a, #3b82f6);
    color: #fff; padding: .9rem 1.8rem; border-radius: 14px;
    font-size: .88rem; font-weight: 600; font-family: inherit;
    box-shadow: 0 12px 32px rgba(30,58,138,.5);
    transform: translateY(80px); opacity: 0; pointer-events: none;
    transition: all .35s cubic-bezier(.4,0,.2,1);
}
.ftr-toast.show { transform: translateY(0); opacity: 1; }

/* ══ Responsive ══ */
@media (max-width: 1100px) {
    .ftr-grid { grid-template-columns: 1fr 1fr; gap: 2.5rem; }
    .ftr-col--brand { grid-column: 1 / -1; }
    .ftr-tagline { max-width: 100%; }
}
@media (max-width: 768px) {
    .ftr-inner { padding: 4rem 1.5rem 2.5rem; }
    .ftr-grid { grid-template-columns: 1fr; gap: 2rem; }
    .ftr-bottom { flex-direction: column; text-align: center; gap: .8rem; }
    .ftr-btt { bottom: 20px; left: 20px; width: 46px; height: 46px; }
}
@media (max-width: 480px) {
    .ftr-socials { flex-wrap: wrap; }
    .ftr-badges { flex-direction: column; }
}
</style>

<script>
(function () {
    /* Back to top */
    var btt = document.getElementById('backToTop');
    window.addEventListener('scroll', function () {
        btt.classList.toggle('show', window.scrollY > 350);
    }, { passive: true });
    btt.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    /* Newsletter toast */
    var toast = document.getElementById('ftrToast');
    window.ftrNewsSubmit = function (e) {
        e.preventDefault();
        toast.textContent = '✅ با موفقیت عضو شدید!';
        toast.classList.add('show');
        e.target.reset();
        setTimeout(function () { toast.classList.remove('show'); }, 3000);
    };
})();
</script>

</body>
</html>