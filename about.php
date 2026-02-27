<?php
/**
 * صفحه درباره ما — کتاب‌نت
 * طراحی: Midnight Blue | تمیز و مدرن‌تر
 */
require_once("./include/header.php");
?>

<link rel="stylesheet" href="./css/style.css">

<style>
/* ════════════════════════════════════════════
   about.php — بازطراحی شده — Midnight Blue
   ════════════════════════════════════════════ */
.pw { max-width: 1280px; margin: 0 auto; padding: 0 1.5rem; }
/* جلوگیری از overflow افقی */
body { overflow-x: hidden; }
.ab-story-img { overflow: visible; }

/* ── SECTION SHARED ── */
.ab-sec { padding: 5.5rem 0; }
.ab-lbl {
    display: inline-flex; align-items: center; gap: .55rem;
    font-size: .68rem; letter-spacing: .22em; text-transform: uppercase;
    color: var(--accent-secondary, #3b82f6); font-weight: 700; margin-bottom: .9rem;
}
.ab-lbl::before, .ab-lbl::after { content: ''; width: 18px; height: 1px; background: currentColor; }
.ab-sec-title { font-size: clamp(1.8rem, 3vw, 2.5rem); font-weight: 900; color: var(--text-primary); letter-spacing: -.02em; line-height: 1.2; margin-bottom: .6rem; }
.ab-sec-sub   { color: var(--text-secondary); font-size: .95rem; }

/* ── STORY ── */
.ab-story-grid {
    display: grid;
    grid-template-columns: 5fr 6fr;
    gap: 6rem; align-items: center;
}
.ab-story-img { position: relative; }
.ab-story-frame {
    position: relative; border-radius: 28px; overflow: hidden;
    box-shadow: 0 32px 80px rgba(0,0,0,.22);
    border: 1px solid var(--border-color);
    z-index: 2;
}
.ab-story-frame img { width: 100%; display: block; }

/* دکوی گوشه‌ای */
.ab-story-deco-br {
    position: absolute; bottom: -20px; right: -20px;
    width: 60%; height: 60%; z-index: 1;
    background: linear-gradient(135deg, rgba(30,58,138,.5), rgba(99,102,241,.3));
    border-radius: 20px;
    filter: blur(1px);
}
.ab-story-deco-tl {
    position: absolute; top: -14px; left: -14px;
    width: 90px; height: 90px; z-index: 3;
    background: linear-gradient(135deg, #1e3a8a, #3b82f6);
    border-radius: 18px;
    display: flex; align-items: center; justify-content: center;
    font-size: 2.4rem; box-shadow: 0 8px 24px rgba(30,58,138,.45);
}

/* بج نوار */
.ab-story-badge {
    position: absolute; bottom: 24px; left: -20px; z-index: 5;
    background: var(--bg-primary); border: 1px solid var(--border-color);
    border-radius: 16px; padding: 1rem 1.4rem;
    box-shadow: 0 12px 32px rgba(0,0,0,.15);
    display: flex; align-items: center; gap: .8rem;
    white-space: nowrap; min-width: 180px;
}
.ab-story-badge-icon { width: 40px; height: 40px; border-radius: 12px; background: linear-gradient(135deg,#1e3a8a,#3b82f6); display: flex; align-items: center; justify-content: center; color:#fff; flex-shrink:0; }
.ab-story-badge-icon svg { width:20px; height:20px; }
.ab-story-badge strong { display:block; font-size:1rem; font-weight:800; color:var(--text-primary); }
.ab-story-badge span { font-size:.75rem; color:var(--text-secondary); }

.ab-story-text { }
.ab-story-text p { font-size: 1rem; line-height: 2; color: var(--text-secondary); margin-bottom: 1.2rem; }
.ab-story-text strong { color: var(--accent-secondary,#3b82f6); font-weight: 700; }
.ab-story-cta {
    display: inline-flex; align-items: center; gap: .55rem;
    margin-top: .8rem; padding: .95rem 2rem; border-radius: 14px;
    background: linear-gradient(135deg, #1e3a8a, #3b82f6);
    color: #fff; font-weight: 700; font-size: .9rem;
    text-decoration: none; transition: all .35s; box-shadow: 0 8px 24px rgba(30,58,138,.35);
}
.ab-story-cta:hover { transform: translateY(-4px); box-shadow: 0 16px 36px rgba(30,58,138,.5); }
.ab-story-cta svg { width: 17px; height: 17px; }

/* ── VALUES ── */
.ab-vals-wrap {
    background: var(--bg-secondary);
    padding: 6rem 1.5rem; margin: 0 -1.5rem;
}
.ab-vals-inner { max-width: 1280px; margin: 0 auto; }
.ab-vals-head { text-align: center; margin-bottom: 3.5rem; }
.ab-vals-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
}
.ab-vcard {
    background: var(--bg-primary);
    padding: 2.2rem 1.8rem; border-radius: 22px;
    border: 1px solid var(--border-color);
    transition: all .4s cubic-bezier(.4,0,.2,1);
    position: relative; overflow: hidden;
}
.ab-vcard-glow {
    position: absolute; top: 0; right: 0;
    width: 120px; height: 120px; border-radius: 50%;
    background: radial-gradient(circle, rgba(59,130,246,.08) 0%, transparent 70%);
    transform: translate(30px,-30px);
    transition: all .4s;
}
.ab-vcard:hover .ab-vcard-glow { transform: translate(10px,-10px); opacity: 1.5; }
.ab-vcard::after {
    content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, #3b82f6, #8b5cf6);
    transform: scaleX(0); transition: transform .35s ease; transform-origin: right;
}
.ab-vcard:hover { transform: translateY(-8px); box-shadow: 0 20px 50px rgba(0,0,0,.1); }
.ab-vcard:hover::after { transform: scaleX(1); transform-origin: left; }
.ab-vcard-icon {
    width: 58px; height: 58px; margin-bottom: 1.3rem;
    background: linear-gradient(135deg, #1e3a8a, #3b82f6);
    border-radius: 16px;
    display: flex; align-items: center; justify-content: center;
    color: #fff; box-shadow: 0 6px 18px rgba(30,58,138,.3);
}
.ab-vcard-icon svg { width: 28px; height: 28px; }
.ab-vcard h3 { font-size: 1.1rem; font-weight: 800; color: var(--text-primary); margin-bottom: .55rem; }
.ab-vcard p  { color: var(--text-secondary); line-height: 1.75; font-size: .88rem; }

/* ── STATS ── */
.ab-stats {
    background: linear-gradient(155deg, #0f172a 0%, #1e3a8a 55%, #312e81 100%);
    border-radius: 28px; padding: 5rem 2.5rem;
    position: relative; overflow: hidden; margin: 5.5rem 0;
}
.ab-stats::before { content: ''; position: absolute; inset: 0; background-image: radial-gradient(circle, rgba(255,255,255,.04) 1px, transparent 1px); background-size: 28px 28px; pointer-events: none; }
.ab-stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; position: relative; z-index: 2; }
.ab-stat {
    text-align: center; color: #fff; padding: 2rem 1rem;
    border-radius: 20px; background: rgba(255,255,255,.05);
    border: 1px solid rgba(255,255,255,.08); transition: all .35s;
}
.ab-stat:hover { background: rgba(255,255,255,.1); transform: translateY(-5px); }
.ab-stat-icon { font-size: 2.4rem; margin-bottom: .8rem; display: block; }
.ab-stat-num { font-size: 2.6rem; font-weight: 900; line-height: 1; margin-bottom: .45rem; display: block; background: linear-gradient(135deg,#60a5fa,#a78bfa); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
.ab-stat-lbl { font-size: .8rem; color: rgba(255,255,255,.5); letter-spacing: .07em; }

/* ── TEAM ── */
.ab-team-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; }
.ab-tcard {
    background: var(--bg-primary); padding: 2.2rem 1.6rem;
    border-radius: 22px; text-align: center;
    border: 1px solid var(--border-color);
    transition: all .4s cubic-bezier(.4,0,.2,1);
    position: relative; overflow: hidden;
}
.ab-tcard::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, #3b82f6, #8b5cf6); opacity: 0; transition: opacity .35s; }
.ab-tcard:hover { transform: translateY(-8px); box-shadow: 0 20px 50px rgba(0,0,0,.1); }
.ab-tcard:hover::before { opacity: 1; }
.ab-tav { width: 88px; height: 88px; margin: 0 auto 1.2rem; background: linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.6rem; box-shadow: 0 8px 24px rgba(30,58,138,.3); border: 3px solid var(--bg-secondary); }
.ab-tcard h3 { font-size: 1.1rem; font-weight: 800; color: var(--text-primary); margin-bottom: .3rem; }
.ab-trole { display: inline-block; background: rgba(59,130,246,.1); color: var(--accent-secondary,#3b82f6); padding: .25rem .8rem; border-radius: 50px; font-weight: 700; font-size: .75rem; margin-bottom: .8rem; letter-spacing: .05em; border: 1px solid rgba(59,130,246,.2); }
.ab-tdesc { color: var(--text-secondary); font-size: .85rem; line-height: 1.7; }
.ab-tcard-socials { display: flex; justify-content: center; gap: .5rem; margin-top: 1.1rem; }
.ab-tsocial { width: 32px; height: 32px; border-radius: 50%; border: 1.5px solid var(--border-color); display: flex; align-items: center; justify-content: center; color: var(--text-secondary); text-decoration: none; transition: all .3s; font-size: .82rem; font-weight: 700; }
.ab-tsocial:hover { background: linear-gradient(135deg,#1e3a8a,#3b82f6); border-color: transparent; color: #fff; transform: translateY(-2px); }

/* ══════════════════════════════════════════
   MAP SECTION — نقشه موقعیت مکانی
   ══════════════════════════════════════════ */
.ab-map-section {
    margin-bottom: 1.5rem;
}
.ab-map-wrap {
    position: relative;
    border-radius: 28px; overflow: hidden;
    border: 1px solid var(--border-color);
    box-shadow: 0 16px 48px rgba(0,0,0,.1);
    background: var(--bg-secondary);
}
/* header نقشه */
.ab-map-header {
    background: linear-gradient(135deg, #0f172a, #1e3a8a);
    padding: 1.4rem 2rem;
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 1rem;
}
.ab-map-header-left { display: flex; align-items: center; gap: .9rem; }
.ab-map-pin-icon {
    width: 44px; height: 44px; border-radius: 12px; flex-shrink: 0;
    background: rgba(96,165,250,.2); border: 1px solid rgba(96,165,250,.3);
    display: flex; align-items: center; justify-content: center; color: #60a5fa;
}
.ab-map-pin-icon svg { width: 22px; height: 22px; }
.ab-map-title  { font-size: 1.05rem; font-weight: 800; color: #fff; }
.ab-map-addr   { font-size: .8rem; color: rgba(255,255,255,.5); margin-top: .15rem; }
.ab-map-open-btn {
    display: inline-flex; align-items: center; gap: .45rem;
    background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.2);
    color: #fff; padding: .5rem 1.1rem; border-radius: 10px;
    font-size: .8rem; font-weight: 700; text-decoration: none;
    transition: all .3s; white-space: nowrap;
}
.ab-map-open-btn:hover { background: rgba(255,255,255,.2); }
.ab-map-open-btn svg { width: 14px; height: 14px; }

/* iframe نقشه */
.ab-map-iframe-wrap {
    position: relative;
    height: 380px;
}
.ab-map-iframe-wrap iframe {
    width: 100%; height: 100%; border: none; display: block;
    filter: var(--map-filter, none);
}
/* دارک مود روی نقشه */
body.dark-mode .ab-map-iframe-wrap iframe {
    filter: invert(90%) hue-rotate(180deg) brightness(.85) saturate(.8);
}

/* info bar پایین نقشه */
.ab-map-info {
    background: var(--bg-primary);
    border-top: 1px solid var(--border-color);
    padding: 1.1rem 2rem;
    display: flex; align-items: center; gap: 2.5rem; flex-wrap: wrap;
}
.ab-map-info-item { display: flex; align-items: center; gap: .55rem; font-size: .82rem; color: var(--text-secondary); }
.ab-map-info-item svg { width: 16px; height: 16px; color: #3b82f6; flex-shrink: 0; }
.ab-map-info-item strong { color: var(--text-primary); font-weight: 600; }

/* ── CONTACT ── */
.ab-contact {
    background: var(--bg-primary); border-radius: 28px;
    padding: 3.5rem 3rem; border: 1px solid var(--border-color);
    box-shadow: 0 8px 32px rgba(0,0,0,.06);
    margin-bottom: 5.5rem;
}
.ab-contact-head { margin-bottom: 2.5rem; }
.ab-contact-head h2 { font-size: clamp(1.7rem, 3vw, 2.2rem); font-weight: 900; color: var(--text-primary); margin-bottom: .5rem; }
.ab-contact-head p  { color: var(--text-secondary); font-size: .96rem; }
.ab-contact-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.2rem; }
.ab-citem {
    display: flex; align-items: center; gap: 1rem;
    padding: 1.5rem; border-radius: 18px;
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    transition: all .35s;
}
.ab-citem:hover { transform: translateY(-5px); box-shadow: 0 12px 30px rgba(0,0,0,.08); border-color: rgba(59,130,246,.35); }
.ab-citem-icon { width: 48px; height: 48px; border-radius: 14px; background: linear-gradient(135deg, #1e3a8a, #3b82f6); display: flex; align-items: center; justify-content: center; color: #fff; flex-shrink: 0; box-shadow: 0 6px 16px rgba(30,58,138,.25); }
.ab-citem-icon svg { width: 22px; height: 22px; }
.ab-citem strong { display: block; color: var(--text-primary); font-weight: 700; font-size: .92rem; margin-bottom: .2rem; }
.ab-citem span   { color: var(--text-secondary); font-size: .84rem; }

/* ── CTA ── */
.ab-cta {
    background: linear-gradient(155deg, #0f172a 0%, #1e3a8a 55%, #312e81 100%);
    border-radius: 28px; padding: 4.5rem 2rem; text-align: center;
    margin-bottom: 5.5rem; position: relative; overflow: hidden;
}
.ab-cta::before { content: ''; position: absolute; inset: 0; background-image: radial-gradient(circle, rgba(255,255,255,.04) 1px, transparent 1px); background-size: 26px 26px; pointer-events: none; }
.ab-cta-in { position: relative; z-index: 2; }
.ab-cta h2 { font-size: clamp(1.8rem, 3vw, 2.5rem); font-weight: 900; color: #fff; margin-bottom: .65rem; }
.ab-cta p  { color: rgba(255,255,255,.55); margin-bottom: 2.5rem; font-size: .98rem; }
.ab-cta-btns { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
.ab-cta-btn-s { padding: 1rem 2.2rem; border-radius: 14px; background: #fff; color: #1e3a8a; font-weight: 800; font-size: .92rem; text-decoration: none; transition: all .3s; display: inline-flex; align-items: center; gap: .5rem; box-shadow: 0 8px 24px rgba(0,0,0,.2); }
.ab-cta-btn-s:hover { transform: translateY(-3px); box-shadow: 0 14px 32px rgba(0,0,0,.3); }
.ab-cta-btn-w { padding: 1rem 2.2rem; border-radius: 14px; background: rgba(255,255,255,.12); border: 1.5px solid rgba(255,255,255,.28); color: #fff; font-weight: 700; font-size: .92rem; text-decoration: none; transition: all .3s; display: inline-flex; align-items: center; gap: .5rem; }
.ab-cta-btn-w:hover { background: rgba(255,255,255,.22); transform: translateY(-3px); }
.ab-cta-btn-s svg, .ab-cta-btn-w svg { width: 17px; height: 17px; }

/* AOS */
[data-aos] { opacity: 0; transition-property: transform,opacity; transition-duration: .65s; transition-timing-function: cubic-bezier(.4,0,.2,1); }
[data-aos].aos-animate { opacity: 1; }
[data-aos="fade-up"]    { transform: translateY(28px); }   [data-aos="fade-up"].aos-animate    { transform: translateY(0); }
[data-aos="fade-right"] { transform: translateX(-28px); }  [data-aos="fade-right"].aos-animate { transform: translateX(0); }
[data-aos="fade-left"]  { transform: translateX(28px); }   [data-aos="fade-left"].aos-animate  { transform: translateX(0); }
[data-aos="zoom-in"]    { transform: scale(.94); }          [data-aos="zoom-in"].aos-animate    { transform: scale(1); }

/* ── RESPONSIVE ── */
@media (max-width: 1100px) {
    .ab-vals-grid { grid-template-columns: repeat(2, 1fr); }
    .ab-team-grid { grid-template-columns: repeat(2, 1fr); }
    .ab-stats-grid { grid-template-columns: repeat(2, 1fr); }
    .ab-story-grid { grid-template-columns: 1fr; gap: 3rem; }
    .ab-story-img { order: -1; }
    .ab-story-badge { left: auto; right: 1rem; }
}
@media (max-width: 768px) {
    .ab-hero { padding: 8rem 1.25rem 5rem; }
    .pw { padding: 0 1.25rem; }
    .ab-vals-wrap { padding: 4rem 1.25rem; margin: 0 -1.25rem; }
    .ab-vals-grid { grid-template-columns: 1fr; }
    .ab-team-grid { grid-template-columns: repeat(2, 1fr); }
    .ab-contact { padding: 2.5rem 1.5rem; }
    .ab-contact-grid { grid-template-columns: 1fr; }
    .ab-stats { padding: 3.5rem 1.5rem; margin: 4rem 0; }
    .ab-sec { padding: 3.5rem 0; }
    .ab-map-header { padding: 1.2rem 1.5rem; }
    .ab-map-info { padding: 1rem 1.5rem; gap: 1.5rem; }
    .ab-map-iframe-wrap { height: 280px; }
}
@media (max-width: 576px) {
    .ab-hero-title { font-size: 2.4rem; }
    .ab-hero-stats { gap: 1.8rem; padding: 1rem 1.8rem; }
    .ab-team-grid { grid-template-columns: 1fr; }
    .ab-cta-btns { flex-direction: column; align-items: center; }
    .ab-stats-grid { grid-template-columns: repeat(2, 1fr); gap: 1rem; }
    .ab-map-info { flex-direction: column; gap: .8rem; }
}
@media (max-width: 420px) {
    .pw { padding: 0 1rem; }
    .ab-hero { padding: 7.5rem 1rem 4rem; }
}
</style>



<div class="pw">

    <!-- ── STORY ── -->
    <section class="ab-sec">
        <div class="ab-story-grid">
            <div class="ab-story-img" data-aos="fade-right">
                <div class="ab-story-deco-br"></div>
                <div class="ab-story-deco-tl">📚</div>
                <div class="ab-story-frame">
                    <img src="./img/25.jpg" alt="کتاب‌نت — داستان ما">
                </div>
                <div class="ab-story-badge">
                    <div class="ab-story-badge-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,17.27L18.18,21L16.54,13.97L22,9.24L14.81,8.62L12,2L9.19,8.62L2,9.24L7.45,13.97L5.82,21L12,17.27Z"/></svg>
                    </div>
                    <div>
                        <strong>۴.۹ / ۵</strong>
                        <span>رضایت مشتریان</span>
                    </div>
                </div>
            </div>
            <div class="ab-story-text" data-aos="fade-left">
                <div class="ab-lbl">داستان ما</div>
                <h2 class="ab-sec-title">از یک ایده ساده<br>تا بزرگ‌ترین کتابفروشی آنلاین</h2>
                <br>
                <p>
                    <strong>کتاب‌نت</strong> در سال ۱۳۹۵ با هدف ایجاد دسترسی آسان و سریع به کتاب‌های مختلف راه‌اندازی شد. ما معتقدیم کتاب پلی است به دنیای دانش و هر فردی حق دارد به بهترین منابع دسترسی داشته باشد.
                </p>
                <p>
                    امروز با افتخار میزبان بیش از <strong>۵۰,۰۰۰ کاربر فعال</strong> هستیم. تیم ما شبانه‌روز در تلاش است تا بهترین تجربه خرید کتاب را برای شما فراهم کند.
                </p>
                <p>
                    ماموریت ما ساده است: <strong>ساخت بزرگ‌ترین کتابخانه آنلاین فارسی</strong> با بهترین قیمت‌ها، سریع‌ترین ارسال و خدمات استثنایی پس از فروش.
                </p>
                <a href="products.php" class="ab-story-cta">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17,18A2,2 0 0,1 19,20A2,2 0 0,1 17,22C15.89,22 15,21.1 15,20C15,18.89 15.89,18 17,18M1,2H4.27L5.21,4H20A1,1 0 0,1 21,5L17.3,11.97C16.96,12.58 16.3,13 15.55,13H8.1L7.2,14.63L7.17,14.75A0.25,0.25 0 0,0 7.42,15H19V17H7C5.89,17 5,16.1 5,15L6.6,11.59L3,4H1V2Z"/></svg>
                    مشاهده محصولات
                </a>
            </div>
        </div>
    </section>

</div>

<!-- ── VALUES ── -->
<div class="ab-vals-wrap">
    <div class="ab-vals-inner">
        <div class="ab-vals-head" data-aos="fade-up">
            <div class="ab-lbl" style="justify-content:center">چرا ما؟</div>
            <h2 class="ab-sec-title">ارزش‌هایی که به آن‌ها پایبندیم</h2>
            <p class="ab-sec-sub">اصولی که در هر قدم راهنمای ماست</p>
        </div>
        <div class="ab-vals-grid">
            <?php
            $values = [
                ['M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1Z', 'کیفیت برتر', 'تمام کتاب‌های ما اصل و با بهترین کیفیت چاپ هستند. ضمانت بازگشت ۷ روزه داریم.', 0],
                ['M12,20A8,8 0 0,1 4,12A8,8 0 0,1 12,4A8,8 0 0,1 20,12A8,8 0 0,1 12,20M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12.5,7V12.25L17,14.92L16.25,16.15L11,13V7H12.5Z', 'ارسال سریع', 'تحویل سفارش‌ها در کمتر از ۴۸ ساعت در سراسر کشور با بسته‌بندی ایمن و مراقب.', 80],
                ['M7,15H9C9,16.08 10.37,17 12,17C13.63,17 15,16.08 15,15C15,13.9 13.96,13.5 11.76,12.97C9.64,12.44 7,11.78 7,9C7,7.21 8.47,5.69 10.5,5.18V3H13.5V5.18C15.53,5.69 17,7.21 17,9H15C15,7.92 13.63,7 12,7C10.37,7 9,7.92 9,9C9,10.1 10.04,10.5 12.24,11.03C14.36,11.56 17,12.22 17,15C17,16.79 15.53,18.31 13.5,18.82V21H10.5V18.82C8.47,18.31 7,16.79 7,15Z', 'قیمت مناسب', 'بهترین قیمت‌ها با تخفیف‌های ویژه و برنامه وفاداری برای مشتریان دائمی ما.', 160],
                ['M12,2A3,3 0 0,1 15,5V11A3,3 0 0,1 12,14A3,3 0 0,1 9,11V5A3,3 0 0,1 12,2M19,11C19,14.53 16.39,17.44 13,17.93V21H11V17.93C7.61,17.44 5,14.53 5,11H7A5,5 0 0,0 12,16A5,5 0 0,0 17,11H19Z', 'پشتیبانی ۲۴/۷', 'تیم پشتیبانی ما شبانه‌روز آماده پاسخگویی به سوالات و رفع مشکلات شماست.', 240],
                ['M12,17.27L18.18,21L16.54,13.97L22,9.24L14.81,8.62L12,2L9.19,8.62L2,9.24L7.45,13.97L5.82,21L12,17.27Z', 'بهترین انتخاب', 'بیش از ۱۰,۰۰۰ عنوان کتاب در دسته‌بندی‌های متنوع با به‌روزرسانی مستمر.', 320],
                ['M13,9H18.5L13,3.5V9M6,2H14L20,8V20A2,2 0 0,1 18,22H6C4.89,22 4,21.1 4,20V4C4,2.89 4.89,2 6,2M15,18V16H6V18H15M18,14V12H6V14H18Z', 'نقد و معرفی', 'مقالات تخصصی و نقد ادبی برای کمک به انتخاب بهتر کتاب‌های مناسب شما.', 400],
            ];
            foreach ($values as $v): ?>
            <div class="ab-vcard" data-aos="zoom-in" style="transition-delay:<?=$v[3]?>ms">
                <div class="ab-vcard-glow"></div>
                <div class="ab-vcard-icon">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="<?=$v[0]?>"/></svg>
                </div>
                <h3><?=$v[1]?></h3>
                <p><?=$v[2]?></p>
            </div>
            <?php endforeach; ?>
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
                <span class="ab-stat-num" data-target="49" data-decimal="true">0</span>
                <span class="ab-stat-lbl">رضایت از ۵</span>
            </div>
        </div>
    </div>

    <!-- ── TEAM ── -->
    <section class="ab-sec">
        <div style="text-align:center;margin-bottom:3rem" data-aos="fade-up">
            <div class="ab-lbl" style="justify-content:center">تیم ما</div>
            <h2 class="ab-sec-title">افرادی که کتاب‌نت را می‌سازند</h2>
        </div>
        <div class="ab-team-grid">
            <?php
            $team = [
                ['👨‍💼', 'علی احمدی',    'مدیر عامل',    'با ۱۵ سال تجربه در صنعت نشر و کتاب'],
                ['👩‍💻', 'سارا محمدی',   'مدیر فنی',     'متخصص در توسعه پلتفرم‌های آنلاین'],
                ['👨‍🎨', 'رضا کریمی',    'مدیر محتوا',   'مسئول انتخاب و تأمین بهترین کتاب‌ها'],
                ['👩‍📋', 'نازنین رضایی', 'مدیر پشتیبانی','متخصص در خدمات مشتریان و UX'],
            ];
            foreach ($team as $i => $m): ?>
            <div class="ab-tcard" data-aos="fade-up" style="transition-delay:<?=$i*80?>ms">
                <div class="ab-tav"><?=$m[0]?></div>
                <h3><?=$m[1]?></h3>
                <span class="ab-trole"><?=$m[2]?></span>
                <p class="ab-tdesc"><?=$m[3]?></p>
                <div class="ab-tcard-socials">
                    <a href="#" class="ab-tsocial">in</a>
                    <a href="#" class="ab-tsocial">𝕏</a>
                    <a href="#" class="ab-tsocial">@</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- ══════════════════════════════════════════
         MAP + CONTACT
         ══════════════════════════════════════════ -->
    <div class="ab-contact" data-aos="fade-up">
        <div class="ab-contact-head">
            <div class="ab-lbl">تماس با ما</div>
            <h2>با ما در ارتباط باشید</h2>
            <p>سوال، پیشنهاد یا انتقادی دارید؟ دوست داریم از شما بشنویم!</p>
        </div>

        <!-- نقشه موقعیت مکانی -->
        <div class="ab-map-section" data-aos="zoom-in">
            <div class="ab-map-wrap">
                <!-- هدر نقشه -->
                <div class="ab-map-header">
                    <div class="ab-map-header-left">
                        <div class="ab-map-pin-icon">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z"/></svg>
                        </div>
                        <div>
                            <div class="ab-map-title">موقعیت مکانی کتاب‌نت</div>
                            <div class="ab-map-addr">تهران، خیابان ولیعصر، پلاک ۲۴۵</div>
                        </div>
                    </div>
                    <a href="https://maps.google.com/?q=تهران+خیابان+ولیعصر" target="_blank" class="ab-map-open-btn">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M14,3V5H17.59L7.76,14.83L9.17,16.24L19,6.41V10H21V3M19,19H5V5H12V3H5C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V12H19V19Z"/></svg>
                        باز کردن در نقشه
                    </a>
                </div>

                <!-- نقشه OpenStreetMap — بدون نیاز به API Key -->
                <div class="ab-map-iframe-wrap">
                    <iframe
                        src="https://www.openstreetmap.org/export/embed.html?bbox=51.36,35.68,51.44,35.74&layer=mapnik&marker=35.715,51.404"
                        loading="lazy"
                        allowfullscreen
                        title="موقعیت کتاب‌نت روی نقشه">
                    </iframe>
                </div>

                <!-- اطلاعات کوتاه -->
                <div class="ab-map-info">
                    <div class="ab-map-info-item">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z"/></svg>
                        <span>تهران، خیابان ولیعصر، پلاک ۲۴۵</span>
                    </div>
                    <div class="ab-map-info-item">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,20A8,8 0 0,1 4,12A8,8 0 0,1 12,4A8,8 0 0,1 20,12A8,8 0 0,1 12,20M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12.5,7V12.25L17,14.92L16.25,16.15L11,13V7H12.5Z"/></svg>
                        <span>شنبه تا پنج‌شنبه · <strong>۹ صبح تا ۶ عصر</strong></span>
                    </div>
                    <div class="ab-map-info-item">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M6.62,10.79C8.06,13.62 10.38,15.94 13.21,17.38L15.41,15.18C15.69,14.9 16.08,14.82 16.43,14.93C17.55,15.3 18.75,15.5 20,15.5A1,1 0 0,1 21,16.5V20A1,1 0 0,1 20,21A17,17 0 0,1 3,4A1,1 0 0,1 4,3H7.5A1,1 0 0,1 8.5,4C8.5,5.25 8.7,6.45 9.07,7.57C9.18,7.92 9.1,8.31 8.82,8.59L6.62,10.79Z"/></svg>
                        <span><strong>۰۲۱-۱۲۳۴۵۶۷</strong></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- اطلاعات تماس -->
        <div class="ab-contact-grid" style="margin-top:1.5rem">
            <div class="ab-citem">
                <div class="ab-citem-icon">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M6.62,10.79C8.06,13.62 10.38,15.94 13.21,17.38L15.41,15.18C15.69,14.9 16.08,14.82 16.43,14.93C17.55,15.3 18.75,15.5 20,15.5A1,1 0 0,1 21,16.5V20A1,1 0 0,1 20,21A17,17 0 0,1 3,4A1,1 0 0,1 4,3H7.5A1,1 0 0,1 8.5,4C8.5,5.25 8.7,6.45 9.07,7.57C9.18,7.92 9.1,8.31 8.82,8.59L6.62,10.79Z"/></svg>
                </div>
                <div><strong>تلفن تماس</strong><span>۰۲۱-۱۲۳۴۵۶۷</span></div>
            </div>
            <div class="ab-citem">
                <div class="ab-citem-icon">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20,8L12,13L4,8V6L12,11L20,6M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.1,4 20,4Z"/></svg>
                </div>
                <div><strong>ایمیل</strong><span>info@ketabnet.ir</span></div>
            </div>
            <div class="ab-citem">
                <div class="ab-citem-icon">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z"/></svg>
                </div>
                <div><strong>آدرس</strong><span>تهران، خیابان ولیعصر، پلاک ۲۴۵</span></div>
            </div>
        </div>
    </div>

    <!-- ── CTA ── -->
    <div class="ab-cta" data-aos="zoom-in">
        <div class="ab-cta-in">
            <div class="ab-lbl" style="justify-content:center;color:rgba(255,255,255,.6)">
                <span style="background:rgba(255,255,255,.35)"></span>
                همین حالا شروع کنید
                <span style="background:rgba(255,255,255,.35)"></span>
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

</div>

<?php require_once("./include/footer.php"); ?>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* AOS */
    var ao = new IntersectionObserver(function (es) {
        es.forEach(function (e) { if (e.isIntersecting) e.target.classList.add('aos-animate'); });
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
        if (rect.top < window.innerHeight - 80) {
            counted = true;
            counters.forEach(function (el) {
                var target  = parseInt(el.dataset.target, 10);
                var decimal = el.dataset.decimal === 'true';
                var steps = 60, step = 0;
                var timer = setInterval(function () {
                    step++;
                    var val = Math.round((target / steps) * step);
                    if (decimal) el.textContent = (val / 10).toFixed(1);
                    else         el.textContent = val.toLocaleString('fa-IR');
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