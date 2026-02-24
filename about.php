<?php
/**
 * صفحه درباره ما — کتاب‌نت
 * تم: Midnight Blue | بدون سایدبار | وسط‌چین
 */
require_once("./include/header.php");
?>

<link rel="stylesheet" href="./css/style.css">

<style>
/* ═══════════════════════════════════════════════
   about.php — Midnight Blue — Full Width
   ═══════════════════════════════════════════════ */
.pw { max-width: 1400px; margin: 0 auto; padding: 0 1.5rem; }

/* ── PAGE HERO ── */
.ab-hero {
    position: relative;
    background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #1e40af 100%);
    padding: 10rem 1.5rem 6rem;
    overflow: hidden;
    text-align: center;
}
.ab-hero::before {
    content: '';
    position: absolute; inset: 0;
    background-image: radial-gradient(circle, rgba(255,255,255,.055) 1px, transparent 1px);
    background-size: 30px 30px; pointer-events: none;
}
.ab-hero::after {
    content: '';
    position: absolute;
    width: 800px; height: 800px; border-radius: 50%;
    background: radial-gradient(circle, rgba(96,165,250,.14) 0%, transparent 65%);
    top: -250px; right: -200px; pointer-events: none;
}
/* دایره گوشه چپ */
.ab-hero-blob {
    position: absolute;
    width: 500px; height: 500px; border-radius: 50%;
    background: radial-gradient(circle, rgba(167,139,250,.12) 0%, transparent 65%);
    bottom: -150px; left: -100px; pointer-events: none;
}
.ab-hero-in { position: relative; z-index: 2; max-width: 760px; margin: 0 auto; }
.ab-ey {
    display: inline-flex; align-items: center; gap: .6rem;
    font-size: .72rem; letter-spacing: .2em; text-transform: uppercase;
    color: #93c5fd; font-weight: 700; margin-bottom: 1.4rem;
}
.ab-ey span { width: 24px; height: 1px; background: #93c5fd; display: inline-block; }
.ab-hero-title {
    font-size: clamp(2.4rem, 5vw, 4.2rem);
    font-weight: 900; color: #fff;
    letter-spacing: -.025em; line-height: 1.1; margin-bottom: 1.2rem;
}
.ab-hero-title em {
    font-style: normal;
    background: linear-gradient(135deg, #60a5fa, #a78bfa);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
}
.ab-hero-sub {
    font-size: 1.12rem; color: rgba(255,255,255,.62);
    line-height: 1.85; margin-bottom: 3rem; max-width: 580px; margin-left: auto; margin-right: auto;
}
/* badge */
.ab-hero-badge {
    display: inline-flex; align-items: center; gap: .6rem;
    background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.18);
    backdrop-filter: blur(8px); color: #fff;
    padding: .6rem 1.4rem; border-radius: 50px; font-size: .88rem; font-weight: 600;
    margin-bottom: 2rem;
}

/* آمار hero */
.ab-hero-stats {
    display: inline-flex; gap: 3rem; flex-wrap: wrap; justify-content: center;
    background: rgba(255,255,255,.07); border: 1px solid rgba(255,255,255,.12);
    border-radius: 20px; padding: 1.2rem 3rem; backdrop-filter: blur(8px);
}
.ab-hs strong { display: block; font-size: 1.9rem; font-weight: 900; color: #60a5fa; line-height: 1; }
.ab-hs span   { font-size: .72rem; color: rgba(255,255,255,.45); letter-spacing: .08em; text-transform: uppercase; margin-top: .25rem; display: block; }

/* scroll cue */
.ab-scroll {
    position: absolute; bottom: 2.2rem; left: 50%; transform: translateX(-50%);
    z-index: 10; display: flex; flex-direction: column; align-items: center; gap: .5rem;
    color: rgba(255,255,255,.3); font-size: .68rem; letter-spacing: .16em; text-transform: uppercase;
}
.ab-scroll-line {
    width: 1px; height: 40px;
    background: linear-gradient(to bottom, rgba(255,255,255,.3), transparent);
    animation: sPulse 2s ease-in-out infinite;
}
@keyframes sPulse { 0%,100%{opacity:.3} 50%{opacity:1} }

/* ── SECTION LABEL ── */
.ab-lbl {
    display: inline-flex; align-items: center; gap: .55rem;
    font-size: .7rem; letter-spacing: .2em; text-transform: uppercase;
    color: var(--accent-primary, #2563eb); font-weight: 700; margin-bottom: .9rem;
}
.ab-lbl::before, .ab-lbl::after { content: ''; width: 20px; height: 1px; background: var(--accent-primary,#2563eb); }

/* section header */
.ab-sh {
    display: flex; align-items: center; gap: 1rem; margin-bottom: 2.5rem;
}
.ab-sh-icon { width: 32px; height: 32px; color: var(--accent-primary); flex-shrink: 0; }
.ab-sh-title { font-size: 1.9rem; font-weight: 900; color: var(--text-primary); }

/* ── SECTIONS ── */
.ab-sec { padding: 5.5rem 0; }
.ab-sec-center { text-align: center; }

/* ── STORY ── */
.ab-story-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 5rem;
    align-items: center;
}
.ab-story-img { position: relative; }
.ab-story-frame {
    position: relative; border-radius: 24px; overflow: hidden;
    box-shadow: var(--shadow-lg);
    z-index: 2;
}
.ab-story-frame img { width: 100%; display: block; border-radius: 24px; }
.ab-story-deco {
    position: absolute;
    top: -16px; right: -16px;
    width: 100%; height: 100%;
    background: linear-gradient(135deg, var(--accent-primary,#2563eb), var(--accent-hover,#1d4ed8));
    border-radius: 24px; z-index: 1; opacity: .65;
}
.ab-story-text p {
    font-size: 1.05rem; line-height: 1.95;
    color: var(--text-secondary);
    margin-bottom: 1.4rem; text-align: justify;
}
.ab-story-text strong { color: var(--accent-primary); font-weight: 700; }
.ab-story-cta {
    display: inline-flex; align-items: center; gap: .5rem;
    margin-top: .5rem;
    padding: .9rem 2rem; border-radius: 13px;
    background: linear-gradient(135deg, var(--accent-primary,#2563eb), var(--accent-hover,#1d4ed8));
    color: #fff; font-weight: 700; font-size: .92rem;
    text-decoration: none; transition: all .3s;
    box-shadow: 0 6px 20px rgba(37,99,235,.3);
}
.ab-story-cta:hover { transform: translateY(-3px); box-shadow: 0 12px 28px rgba(37,99,235,.4); }
.ab-story-cta svg { width: 18px; height: 18px; }

/* ── VALUES ── */
.ab-vals-wrap {
    background: var(--bg-secondary);
    border-radius: 2px;
    padding: 5.5rem 1.5rem;
    margin: 0 -1.5rem;
}
.ab-vals-head { text-align: center; margin-bottom: 3.5rem; }
.ab-vals-title { font-size: clamp(1.8rem, 3vw, 2.6rem); font-weight: 900; color: var(--text-primary); }
.ab-vals-sub   { color: var(--text-secondary); margin-top: .6rem; font-size: .95rem; }
.ab-vals-grid {
    max-width: 1400px; margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1.5rem;
}
.ab-vcard {
    background: var(--bg-primary);
    padding: 2.4rem 2rem;
    border-radius: 22px;
    text-align: center;
    border: 1px solid var(--border-color);
    transition: all .4s cubic-bezier(.4,0,.2,1);
    position: relative; overflow: hidden;
}
.ab-vcard::after {
    content: '';
    position: absolute; bottom: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, var(--accent-primary,#2563eb), #60a5fa);
    transform: scaleX(0); transition: transform .35s ease; transform-origin: right;
}
.ab-vcard:hover { transform: translateY(-10px); box-shadow: var(--shadow-lg); }
.ab-vcard:hover::after { transform: scaleX(1); transform-origin: left; }
.ab-vcard-icon {
    width: 76px; height: 76px; margin: 0 auto 1.4rem;
    background: linear-gradient(135deg, var(--accent-primary,#2563eb), var(--accent-hover,#1d4ed8));
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: #fff;
    box-shadow: 0 8px 24px rgba(37,99,235,.3);
}
.ab-vcard-icon svg { width: 36px; height: 36px; }
.ab-vcard h3 { font-size: 1.2rem; font-weight: 800; color: var(--text-primary); margin-bottom: .65rem; }
.ab-vcard p  { color: var(--text-secondary); line-height: 1.72; font-size: .9rem; }

/* ── STATS COUNTER ── */
.ab-stats {
    background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #1e40af 100%);
    border-radius: 28px; padding: 5rem 3rem;
    position: relative; overflow: hidden; margin: 5.5rem 0;
}
.ab-stats::before {
    content: '';
    position: absolute; inset: 0;
    background-image: radial-gradient(circle, rgba(255,255,255,.04) 1px, transparent 1px);
    background-size: 28px 28px; pointer-events: none;
}
.ab-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 2rem; position: relative; z-index: 2;
}
.ab-stat {
    text-align: center; color: #fff;
    padding: 1.5rem; border-radius: 18px;
    background: rgba(255,255,255,.05);
    border: 1px solid rgba(255,255,255,.1);
    transition: all .3s;
}
.ab-stat:hover { background: rgba(255,255,255,.1); transform: translateY(-4px); }
.ab-stat-icon { font-size: 2.6rem; margin-bottom: .9rem; display: block; }
.ab-stat-num  { font-size: 2.5rem; font-weight: 900; color: #60a5fa; line-height: 1; margin-bottom: .4rem; display: block; }
.ab-stat-lbl  { font-size: .85rem; color: rgba(255,255,255,.55); letter-spacing: .06em; }

/* ── TEAM ── */
.ab-team-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 2rem;
}
.ab-tcard {
    background: var(--bg-primary);
    padding: 2.5rem 2rem;
    border-radius: 22px;
    text-align: center;
    border: 1px solid var(--border-color);
    transition: all .4s cubic-bezier(.4,0,.2,1);
    position: relative; overflow: hidden;
}
.ab-tcard::before {
    content: '';
    position: absolute; top: 0; left: 0; right: 0; height: 4px;
    background: linear-gradient(90deg, var(--accent-primary,#2563eb), #60a5fa);
    opacity: 0; transition: opacity .35s;
}
.ab-tcard:hover { transform: translateY(-10px); box-shadow: var(--shadow-lg); }
.ab-tcard:hover::before { opacity: 1; }
.ab-tav {
    width: 110px; height: 110px; margin: 0 auto 1.4rem;
    background: linear-gradient(135deg, var(--accent-primary,#2563eb), var(--accent-hover,#1d4ed8));
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 3.4rem;
    box-shadow: 0 10px 28px rgba(37,99,235,.3);
    border: 4px solid var(--bg-secondary);
}
.ab-tcard h3 { font-size: 1.25rem; font-weight: 800; color: var(--text-primary); margin-bottom: .35rem; }
.ab-trole {
    display: inline-block;
    background: rgba(37,99,235,.1); color: var(--accent-primary);
    padding: .28rem .85rem; border-radius: 50px;
    font-weight: 700; font-size: .8rem; margin-bottom: .9rem; letter-spacing: .05em;
}
.ab-tdesc { color: var(--text-secondary); font-size: .88rem; line-height: 1.7; }

/* social icons on team card */
.ab-tcard-socials { display: flex; justify-content: center; gap: .6rem; margin-top: 1.2rem; }
.ab-tsocial {
    width: 34px; height: 34px; border-radius: 50%;
    border: 1.5px solid var(--border-color);
    display: flex; align-items: center; justify-content: center;
    color: var(--text-secondary); text-decoration: none;
    transition: all .3s; font-size: .9rem;
}
.ab-tsocial:hover { background: var(--accent-primary); border-color: var(--accent-primary); color: #fff; transform: translateY(-2px); }

/* ── CONTACT ── */
.ab-contact {
    background: var(--bg-primary);
    border-radius: 28px; padding: 4rem 3rem;
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-md);
    margin-bottom: 5.5rem;
    position: relative; overflow: hidden;
}
.ab-contact::before {
    content: '📞';
    font-size: 10rem; opacity: .04;
    position: absolute; top: -1rem; right: 1rem;
    pointer-events: none; line-height: 1;
}
.ab-contact-head { margin-bottom: 2.8rem; }
.ab-contact-head h2 { font-size: clamp(1.8rem, 3vw, 2.4rem); font-weight: 900; color: var(--text-primary); margin-bottom: .6rem; }
.ab-contact-head p  { color: var(--text-secondary); font-size: 1rem; }
.ab-contact-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1.5rem;
}
.ab-citem {
    display: flex; align-items: center; gap: 1.1rem;
    padding: 1.6rem; border-radius: 18px;
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    transition: all .35s;
}
.ab-citem:hover { transform: translateY(-6px); box-shadow: var(--shadow-md); border-color: var(--accent-primary); }
.ab-citem-icon {
    width: 52px; height: 52px; border-radius: 14px;
    background: linear-gradient(135deg, var(--accent-primary,#2563eb), var(--accent-hover,#1d4ed8));
    display: flex; align-items: center; justify-content: center;
    color: #fff; flex-shrink: 0;
    box-shadow: 0 6px 18px rgba(37,99,235,.25);
}
.ab-citem-icon svg { width: 24px; height: 24px; }
.ab-citem strong { display: block; color: var(--text-primary); font-weight: 700; font-size: .95rem; margin-bottom: .2rem; }
.ab-citem span   { color: var(--text-secondary); font-size: .88rem; }

/* CTA BANNER */
.ab-cta {
    background: linear-gradient(135deg, var(--accent-primary,#1e3a8a), var(--accent-hover,#1e40af));
    border-radius: 28px; padding: 4.5rem 2rem;
    text-align: center; margin-bottom: 5.5rem;
    position: relative; overflow: hidden;
}
.ab-cta::before {
    content: '';
    position: absolute; inset: 0;
    background-image: radial-gradient(circle, rgba(255,255,255,.05) 1px, transparent 1px);
    background-size: 26px 26px; pointer-events: none;
}
.ab-cta-in { position: relative; z-index: 2; }
.ab-cta h2 { font-size: clamp(1.8rem, 3vw, 2.6rem); font-weight: 900; color: #fff; margin-bottom: .75rem; }
.ab-cta p  { color: rgba(255,255,255,.65); margin-bottom: 2.2rem; font-size: 1rem; }
.ab-cta-btns { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
.ab-cta-btn-w {
    padding: 1rem 2.2rem; border-radius: 14px;
    background: rgba(255,255,255,.15); border: 1.5px solid rgba(255,255,255,.35);
    color: #fff; font-weight: 700; font-size: .95rem;
    text-decoration: none; transition: all .3s; display: inline-flex; align-items: center; gap: .5rem;
}
.ab-cta-btn-w:hover { background: rgba(255,255,255,.25); transform: translateY(-3px); }
.ab-cta-btn-s {
    padding: 1rem 2.2rem; border-radius: 14px;
    background: #fff; color: var(--accent-primary,#2563eb);
    font-weight: 800; font-size: .95rem;
    text-decoration: none; transition: all .3s; display: inline-flex; align-items: center; gap: .5rem;
    box-shadow: 0 6px 20px rgba(0,0,0,.2);
}
.ab-cta-btn-s:hover { transform: translateY(-3px); box-shadow: 0 12px 28px rgba(0,0,0,.25); }
.ab-cta-btn-w svg, .ab-cta-btn-s svg { width: 18px; height: 18px; }

/* AOS */
[data-aos] { opacity: 0; transition-property: transform,opacity; transition-duration: .65s; transition-timing-function: cubic-bezier(.4,0,.2,1); }
[data-aos].aos-animate { opacity: 1; }
[data-aos="fade-up"]    { transform: translateY(30px); }    [data-aos="fade-up"].aos-animate    { transform: translateY(0); }
[data-aos="fade-right"] { transform: translateX(-30px); }   [data-aos="fade-right"].aos-animate { transform: translateX(0); }
[data-aos="fade-left"]  { transform: translateX(30px); }    [data-aos="fade-left"].aos-animate  { transform: translateX(0); }
[data-aos="zoom-in"]    { transform: scale(.93); }           [data-aos="zoom-in"].aos-animate    { transform: scale(1); }
[data-aos="flip-up"]    { transform: perspective(1200px) rotateX(-90deg); } [data-aos="flip-up"].aos-animate { transform: perspective(1200px) rotateX(0); }

/* ── RESPONSIVE ── */
@media (max-width: 1024px) {
    .ab-stats-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 768px) {
    .ab-hero { padding: 8rem 1.25rem 4.5rem; }
    .pw { padding: 0 1.25rem; }
    .ab-story-grid { grid-template-columns: 1fr; gap: 2.5rem; }
    .ab-story-img { order: -1; }
    .ab-vals-wrap { padding: 4rem 1.25rem; margin: 0 -1.25rem; }
    .ab-stats { padding: 3.5rem 1.5rem; margin: 4rem 0; }
    .ab-stats-grid { grid-template-columns: repeat(2, 1fr); gap: 1.25rem; }
    .ab-contact { padding: 2.5rem 1.5rem; }
    .ab-sec { padding: 4rem 0; }
    .ab-cta { padding: 3.5rem 1.5rem; }
    .ab-hero-stats { gap: 2rem; padding: 1rem 2rem; }
}
@media (max-width: 576px) {
    .ab-hero-title { font-size: 2.2rem; }
    .ab-hero-stats { gap: 1.5rem; padding: .85rem 1.4rem; }
    .ab-stats-grid { grid-template-columns: repeat(2, 1fr); gap: 1rem; }
    .ab-stat-num { font-size: 1.9rem; }
    .ab-cta-btns { flex-direction: column; align-items: center; }
    .ab-sh-title { font-size: 1.5rem; }
    .ab-contact-grid { grid-template-columns: 1fr; }
    .ab-vals-grid { grid-template-columns: 1fr; }
    .ab-team-grid { grid-template-columns: 1fr; }
}
@media (max-width: 420px) {
    .pw { padding: 0 1rem; }
    .ab-stats-grid { grid-template-columns: 1fr 1fr; }
    .ab-hero { padding: 7.5rem 1rem 4rem; }
}
</style>

<div class="pw">

    <!-- ── STORY ── -->
    <section class="ab-sec">
        <div class="ab-sh" data-aos="fade-up">
            <svg class="ab-sh-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4M11,16.5L6.5,12L7.91,10.59L11,13.67L16.59,8.09L18,9.5L11,16.5Z"/></svg>
            <h2 class="ab-sh-title">داستان ما</h2>
        </div>
        <div class="ab-story-grid">
            <div class="ab-story-img" data-aos="fade-right">
                <div class="ab-story-deco"></div>
                <div class="ab-story-frame">
                    <img src="./img/25.jpg" alt="کتاب‌نت — داستان ما">
                </div>
            </div>
            <div class="ab-story-text" data-aos="fade-left">
                <div class="ab-lbl" style="margin-bottom:1.2rem">داستان کتاب‌نت</div>
                <p>
                    <strong>کتاب‌نت</strong> در سال ۱۳۹۵ با هدف ایجاد دسترسی آسان و سریع به کتاب‌های
                    مختلف برای تمام علاقه‌مندان به مطالعه راه‌اندازی شد. ما معتقدیم که کتاب پلی است
                    به دنیای دانش و هر فردی حق دارد به بهترین منابع دسترسی داشته باشد.
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
                <a href="products.php" class="ab-story-cta">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17,18A2,2 0 0,1 19,20A2,2 0 0,1 17,22C15.89,22 15,21.1 15,20C15,18.89 15.89,18 17,18M1,2H4.27L5.21,4H20A1,1 0 0,1 21,5L17.3,11.97C16.96,12.58 16.3,13 15.55,13H8.1L7.2,14.63L7.17,14.75A0.25,0.25 0 0,0 7.42,15H19V17H7C5.89,17 5,16.1 5,15L6.6,11.59L3,4H1V2Z"/></svg>
                    مشاهده محصولات
                </a>
            </div>
        </div>
    </section>

</div><!-- /pw -->

<!-- ── VALUES (full-bleed bg) ── -->
<div class="ab-vals-wrap">
    <div class="ab-vals-head" data-aos="fade-up">
        <div class="ab-lbl" style="justify-content:center">ارزش‌های ما</div>
        <h2 class="ab-vals-title">چرا کتاب‌نت؟</h2>
        <p class="ab-vals-sub">اصولی که در هر قدم راهنمای ماست</p>
    </div>
    <div class="ab-vals-grid">
        <div class="ab-vcard" data-aos="zoom-in" style="transition-delay:0ms">
            <div class="ab-vcard-icon">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1Z"/></svg>
            </div>
            <h3>کیفیت برتر</h3>
            <p>تمام کتاب‌های ما اصل و با بهترین کیفیت چاپ هستند. ضمانت بازگشت ۷ روزه داریم.</p>
        </div>
        <div class="ab-vcard" data-aos="zoom-in" style="transition-delay:80ms">
            <div class="ab-vcard-icon">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,20A8,8 0 0,1 4,12A8,8 0 0,1 12,4A8,8 0 0,1 20,12A8,8 0 0,1 12,20M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12.5,7V12.25L17,14.92L16.25,16.15L11,13V7H12.5Z"/></svg>
            </div>
            <h3>ارسال سریع</h3>
            <p>تحویل سفارش‌ها در کمتر از ۴۸ ساعت در سراسر کشور با بسته‌بندی ایمن.</p>
        </div>
        <div class="ab-vcard" data-aos="zoom-in" style="transition-delay:160ms">
            <div class="ab-vcard-icon">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M7,15H9C9,16.08 10.37,17 12,17C13.63,17 15,16.08 15,15C15,13.9 13.96,13.5 11.76,12.97C9.64,12.44 7,11.78 7,9C7,7.21 8.47,5.69 10.5,5.18V3H13.5V5.18C15.53,5.69 17,7.21 17,9H15C15,7.92 13.63,7 12,7C10.37,7 9,7.92 9,9C9,10.1 10.04,10.5 12.24,11.03C14.36,11.56 17,12.22 17,15C17,16.79 15.53,18.31 13.5,18.82V21H10.5V18.82C8.47,18.31 7,16.79 7,15Z"/></svg>
            </div>
            <h3>قیمت مناسب</h3>
            <p>بهترین قیمت‌ها با تخفیف‌های ویژه و برنامه وفاداری برای مشتریان دائمی.</p>
        </div>
        <div class="ab-vcard" data-aos="zoom-in" style="transition-delay:240ms">
            <div class="ab-vcard-icon">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,2A3,3 0 0,1 15,5V11A3,3 0 0,1 12,14A3,3 0 0,1 9,11V5A3,3 0 0,1 12,2M19,11C19,14.53 16.39,17.44 13,17.93V21H11V17.93C7.61,17.44 5,14.53 5,11H7A5,5 0 0,0 12,16A5,5 0 0,0 17,11H19Z"/></svg>
            </div>
            <h3>پشتیبانی ۲۴/۷</h3>
            <p>تیم پشتیبانی ما شبانه‌روز آماده پاسخگویی به سوالات و رفع مشکلات شماست.</p>
        </div>
        <div class="ab-vcard" data-aos="zoom-in" style="transition-delay:320ms">
            <div class="ab-vcard-icon">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,17.27L18.18,21L16.54,13.97L22,9.24L14.81,8.62L12,2L9.19,8.62L2,9.24L7.45,13.97L5.82,21L12,17.27Z"/></svg>
            </div>
            <h3>بهترین انتخاب</h3>
            <p>بیش از ۱۰,۰۰۰ عنوان کتاب در دسته‌بندی‌های متنوع با به‌روزرسانی مستمر.</p>
        </div>
        <div class="ab-vcard" data-aos="zoom-in" style="transition-delay:400ms">
            <div class="ab-vcard-icon">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M13,9H18.5L13,3.5V9M6,2H14L20,8V20A2,2 0 0,1 18,22H6C4.89,22 4,21.1 4,20V4C4,2.89 4.89,2 6,2M15,18V16H6V18H15M18,14V12H6V14H18Z"/></svg>
            </div>
            <h3>نقد و معرفی</h3>
            <p>مقالات تخصصی و نقد ادبی برای کمک به انتخاب بهتر کتاب‌های مناسب شما.</p>
        </div>
    </div>
</div>

<div class="pw">

    <!-- ── STATS ── -->
    <div class="ab-stats" data-aos="zoom-in">
        <div class="ab-stats-grid">
            <div class="ab-stat" data-aos="fade-up" style="transition-delay:0ms">
                <span class="ab-stat-icon">📚</span>
                <span class="ab-stat-num" data-target="10000">0</span>
                <span class="ab-stat-lbl">عنوان کتاب</span>
            </div>
            <div class="ab-stat" data-aos="fade-up" style="transition-delay:100ms">
                <span class="ab-stat-icon">👥</span>
                <span class="ab-stat-num" data-target="50000">0</span>
                <span class="ab-stat-lbl">کاربر فعال</span>
            </div>
            <div class="ab-stat" data-aos="fade-up" style="transition-delay:200ms">
                <span class="ab-stat-icon">📦</span>
                <span class="ab-stat-num" data-target="100000">0</span>
                <span class="ab-stat-lbl">سفارش تحویل شده</span>
            </div>
            <div class="ab-stat" data-aos="fade-up" style="transition-delay:300ms">
                <span class="ab-stat-icon">⭐</span>
                <span class="ab-stat-num" data-target="48" data-decimal="true">0</span>
                <span class="ab-stat-lbl">رضایت مشتریان از ۵</span>
            </div>
        </div>
    </div>

    <!-- ── TEAM ── -->
    <section class="ab-sec">
        <div class="ab-sh" data-aos="fade-up">
            <svg class="ab-sh-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M16,13C15.71,13 15.38,13 15.03,13.05C16.19,13.89 17,15 17,16.5V19H23V16.5C23,14.17 18.33,13 16,13M8,13C5.67,13 1,14.17 1,16.5V19H15V16.5C15,14.17 10.33,13 8,13M8,11A3,3 0 0,0 11,8A3,3 0 0,0 8,5A3,3 0 0,0 5,8A3,3 0 0,0 8,11M16,11A3,3 0 0,0 19,8A3,3 0 0,0 16,5A3,3 0 0,0 13,8A3,3 0 0,0 16,11Z"/></svg>
            <h2 class="ab-sh-title">تیم ما</h2>
        </div>
        <div class="ab-team-grid">
            <?php
            $team = [
                ['👨‍💼', 'علی احمدی',    'مدیر عامل',   'با بیش از ۱۵ سال تجربه در صنعت نشر و کتاب'],
                ['👩‍💻', 'سارا محمدی',   'مدیر فنی',    'متخصص در توسعه پلتفرم‌های آنلاین'],
                ['👨‍🎨', 'رضا کریمی',    'مدیر محتوا',  'مسئول انتخاب و تأمین بهترین کتاب‌ها'],
                ['👩‍📋', 'نازنین رضایی', 'مدیر پشتیبانی','متخصص در خدمات مشتریان و تجربه کاربری'],
            ];
            foreach ($team as $i => $m): ?>
            <div class="ab-tcard" data-aos="flip-up" style="transition-delay:<?= $i * 80 ?>ms">
                <div class="ab-tav"><?= $m[0] ?></div>
                <h3><?= $m[1] ?></h3>
                <span class="ab-trole"><?= $m[2] ?></span>
                <p class="ab-tdesc"><?= $m[3] ?></p>
                <div class="ab-tcard-socials">
                    <a href="#" class="ab-tsocial" title="لینکدین">in</a>
                    <a href="#" class="ab-tsocial" title="توییتر">𝕏</a>
                    <a href="#" class="ab-tsocial" title="ایمیل">@</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- ── CONTACT ── -->
    <div class="ab-contact" data-aos="fade-up">
        <div class="ab-contact-head">
            <div class="ab-lbl">تماس با ما</div>
            <h2>با ما در ارتباط باشید</h2>
            <p>سوال، پیشنهاد یا انتقادی دارید؟ دوست داریم از شما بشنویم!</p>
        </div>
        <div class="ab-contact-grid">
            <div class="ab-citem">
                <div class="ab-citem-icon">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M6.62,10.79C8.06,13.62 10.38,15.94 13.21,17.38L15.41,15.18C15.69,14.9 16.08,14.82 16.43,14.93C17.55,15.3 18.75,15.5 20,15.5A1,1 0 0,1 21,16.5V20A1,1 0 0,1 20,21A17,17 0 0,1 3,4A1,1 0 0,1 4,3H7.5A1,1 0 0,1 8.5,4C8.5,5.25 8.7,6.45 9.07,7.57C9.18,7.92 9.1,8.31 8.82,8.59L6.62,10.79Z"/></svg>
                </div>
                <div>
                    <strong>تلفن تماس</strong>
                    <span>۰۲۱-۱۲۳۴۵۶۷</span>
                </div>
            </div>
            <div class="ab-citem">
                <div class="ab-citem-icon">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20,8L12,13L4,8V6L12,11L20,6M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.1,4 20,4Z"/></svg>
                </div>
                <div>
                    <strong>ایمیل</strong>
                    <span>info@ketabnet.ir</span>
                </div>
            </div>
            <div class="ab-citem">
                <div class="ab-citem-icon">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z"/></svg>
                </div>
                <div>
                    <strong>آدرس</strong>
                    <span>تهران، خیابان ولیعصر</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ── CTA BANNER ── -->
    <div class="ab-cta" data-aos="zoom-in">
        <div class="ab-cta-in">
            <div class="ab-lbl" style="justify-content:center;color:rgba(255,255,255,.7)">
                <span style="background:rgba(255,255,255,.4)"></span>
                همین حالا شروع کنید
                <span style="background:rgba(255,255,255,.4)"></span>
            </div>
            <h2>آماده‌اید اولین خریدتان را کنید؟</h2>
            <p>بیش از ۱۰,۰۰۰ کتاب منتظر شما هستند</p>
            <div class="ab-cta-btns">
                <a href="products.php" class="ab-cta-btn-s">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17,18A2,2 0 0,1 19,20A2,2 0 0,1 17,22C15.89,22 15,21.1 15,20C15,18.89 15.89,18 17,18M1,2H4.27L5.21,4H20A1,1 0 0,1 21,5L17.3,11.97C16.96,12.58 16.3,13 15.55,13H8.1L7.2,14.63L7.17,14.75A0.25,0.25 0 0,0 7.42,15H19V17H7C5.89,17 5,16.1 5,15L6.6,11.59L3,4H1V2Z"/></svg>
                    خرید کنید
                </a>
                <a href="posts.php" class="ab-cta-btn-w">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19,5V7H15V5H19M9,5V11H5V5H9M19,13V19H15V13H19M9,17V19H5V17H9M21,3H13V9H21V3M11,3H3V13H11V3M21,11H13V21H21V11M11,15H3V21H11V15Z"/></svg>
                    مقالات آموزشی
                </a>
            </div>
        </div>
    </div>

</div><!-- /pw -->

<?php require_once("./include/footer.php"); ?>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* AOS */
    var ao = new IntersectionObserver(function (es) {
        es.forEach(function (e) {
            if (e.isIntersecting) e.target.classList.add('aos-animate');
        });
    }, { threshold: .1, rootMargin: '0px 0px -60px 0px' });
    document.querySelectorAll('[data-aos]').forEach(function (el) { ao.observe(el); });

    /* Counter Animation */
    var counters = document.querySelectorAll('.ab-stat-num[data-target]');
    var counted  = false;

    function runCounters() {
        if (counted) return;
        var statsEl = document.querySelector('.ab-stats');
        if (!statsEl) return;
        var rect = statsEl.getBoundingClientRect();
        if (rect.top < window.innerHeight - 100) {
            counted = true;
            counters.forEach(function (el) {
                var target  = parseInt(el.dataset.target, 10);
                var decimal = el.dataset.decimal === 'true';
                var steps   = 60;
                var step    = 0;
                var timer   = setInterval(function () {
                    step++;
                    var val = Math.round((target / steps) * step);
                    if (decimal) {
                        el.textContent = (val / 10).toFixed(1);
                    } else {
                        el.textContent = val.toLocaleString('fa-IR');
                    }
                    if (step >= steps) {
                        clearInterval(timer);
                        if (decimal) el.textContent = (target / 10).toFixed(1);
                        else         el.textContent = target.toLocaleString('fa-IR');
                    }
                }, 1200 / steps);
            });
        }
    }

    window.addEventListener('scroll', runCounters, { passive: true });
    runCounters();

});
</script>