<?php
require_once("./include/header.php");

$category_id = isset($_GET['category']) ? filter_var($_GET['category'], FILTER_VALIDATE_INT) : null;

if ($category_id) {
    $posts    = fetchAll($db, 'SELECT * FROM posts WHERE category_id = ? ORDER BY id DESC LIMIT 4', [$category_id]);
    $products = fetchAll($db, 'SELECT * FROM product WHERE category_id = ? ORDER BY id DESC LIMIT 6', [$category_id]);
} else {
    $posts    = fetchAll($db, "SELECT * FROM posts ORDER BY id DESC LIMIT 4");
    $products = fetchAll($db, "SELECT * FROM product ORDER BY id DESC LIMIT 6");
}

$total_products = fetchOne($db, "SELECT COUNT(*) as c FROM product")['c'] ?? 0;
$total_members  = fetchOne($db, "SELECT COUNT(*) as c FROM members")['c'] ?? 0;
$flash_product  = fetchOne($db, "SELECT *, (CAST(price AS UNSIGNED) - CAST(`new-price` AS UNSIGNED)) as disc FROM product ORDER BY disc DESC LIMIT 1");

function truncateText($t, $l = 130) {
    $t = strip_tags($t);
    return mb_strlen($t) > $l ? mb_substr($t, 0, $l) . '...' : $t;
}
function formatPrice($p) { return number_format($p) . ' تومان'; }
function calcDisc($o, $n)  { return $o > 0 ? round((($o - $n) / $o) * 100) : 0; }
?>

<link rel="stylesheet" href="./css/style.css">

<style>
/* ═══════════════════════════════════════════════════════
   index.php — تم Midnight Blue — فونت Vazirmatn
   ═══════════════════════════════════════════════════════ */

/* ── wrapper ── */
.idx-wrap {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 1.5rem;
}

/* ══════════════════════ HERO ══════════════════════ */
.idx-hero {
    position: relative;
    min-height: 92vh;
    display: flex;
    align-items: center;
    overflow: hidden;
    background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #1e40af 100%);
}

/* نقاط پس‌زمینه */
.idx-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image: radial-gradient(circle, rgba(255,255,255,.06) 1px, transparent 1px);
    background-size: 32px 32px;
    pointer-events: none;
}

/* درخشش گوشه */
.idx-hero::after {
    content: '';
    position: absolute;
    width: 800px;
    height: 800px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(96,165,250,.18) 0%, transparent 65%);
    top: -200px;
    right: -200px;
    pointer-events: none;
}

.idx-hero-inner {
    position: relative;
    z-index: 2;
    width: 100%;
    max-width: 1400px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 420px;
    gap: 5rem;
    align-items: center;
    padding: 9rem 1.5rem 6rem;
}

/* eyebrow */
.idx-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: .6rem;
    font-size: .75rem;
    letter-spacing: .18em;
    text-transform: uppercase;
    color: #93c5fd;
    font-weight: 700;
    margin-bottom: 1.6rem;
}
.idx-eyebrow span { width: 28px; height: 1px; background: #93c5fd; display: inline-block; }

/* عنوان */
.idx-h1 {
    font-size: clamp(2.8rem, 5vw, 4.8rem);
    font-weight: 900;
    color: #fff;
    line-height: 1.1;
    margin-bottom: 1.8rem;
    letter-spacing: -.02em;
}
.idx-h1 em {
    font-style: normal;
    background: linear-gradient(135deg, #60a5fa, #a78bfa);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.idx-sub {
    font-size: 1.1rem;
    color: rgba(255,255,255,.65);
    line-height: 1.85;
    margin-bottom: 2.8rem;
    max-width: 480px;
}
.idx-sub strong { color: #60a5fa; }

/* دکمه‌ها */
.idx-btns { display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 3.5rem; }

.idx-btn-primary {
    padding: .95rem 2.2rem;
    border-radius: 14px;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #fff;
    font-weight: 700;
    font-size: .95rem;
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    transition: all .3s;
    border: none;
    cursor: pointer;
    font-family: inherit;
    box-shadow: 0 8px 28px rgba(37,99,235,.4);
    text-decoration: none;
}
.idx-btn-primary:hover { transform: translateY(-3px); box-shadow: 0 14px 36px rgba(37,99,235,.5); }
.idx-btn-primary svg { width: 18px; height: 18px; }

.idx-btn-ghost {
    padding: .95rem 2.2rem;
    border-radius: 14px;
    background: transparent;
    border: 1.5px solid rgba(255,255,255,.25);
    color: rgba(255,255,255,.85);
    font-weight: 600;
    font-size: .95rem;
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    transition: all .3s;
    cursor: pointer;
    font-family: inherit;
    text-decoration: none;
}
.idx-btn-ghost:hover { border-color: #60a5fa; color: #60a5fa; }
.idx-btn-ghost svg { width: 18px; height: 18px; }

/* آمار hero */
.idx-hero-stats {
    display: flex;
    gap: 3rem;
    flex-wrap: wrap;
    padding-top: 2.5rem;
    border-top: 1px solid rgba(255,255,255,.1);
}
.idx-hs strong { display: block; font-size: 2rem; font-weight: 900; color: #60a5fa; line-height: 1; }
.idx-hs span   { font-size: .78rem; color: rgba(255,255,255,.45); letter-spacing: .07em; text-transform: uppercase; margin-top: .2rem; display: block; }

/* تصویر hero */
.idx-hero-vis { position: relative; }
.idx-hero-frame {
    position: relative;
    border-radius: 24px;
    overflow: hidden;
    aspect-ratio: 3/4;
    box-shadow: 0 40px 90px rgba(0,0,0,.5);
    border: 1px solid rgba(255,255,255,.1);
}
.idx-hero-frame img { width: 100%; height: 100%; object-fit: cover; }

.idx-float-badge {
    position: absolute;
    bottom: -20px;
    left: -20px;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #fff;
    padding: 1.2rem 1.6rem;
    border-radius: 18px;
    font-weight: 800;
    font-size: .9rem;
    box-shadow: 0 12px 30px rgba(37,99,235,.45);
    z-index: 3;
    line-height: 1.3;
}
.idx-float-badge big { display: block; font-size: 1.7rem; font-weight: 900; }

/* scroll cue */
.idx-scroll {
    position: absolute;
    bottom: 2.5rem;
    left: 50%;
    transform: translateX(-50%);
    z-index: 10;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: .5rem;
    color: rgba(255,255,255,.3);
    font-size: .7rem;
    letter-spacing: .15em;
    text-transform: uppercase;
}
.idx-scroll-line {
    width: 1px;
    height: 48px;
    background: linear-gradient(to bottom, rgba(255,255,255,.3), transparent);
    animation: scrollPulse 2s ease-in-out infinite;
}
@keyframes scrollPulse { 0%,100%{opacity:.3} 50%{opacity:1} }

/* ══════════════════════ TICKER ══════════════════════ */
.idx-ticker {
    background: linear-gradient(90deg, var(--accent-primary, #2563eb), var(--accent-hover, #1d4ed8));
    padding: 1rem 0;
    overflow: hidden;
}
.idx-ticker-track {
    display: flex;
    white-space: nowrap;
    animation: tickerScroll 24s linear infinite;
}
@keyframes tickerScroll { from{transform:translateX(0)} to{transform:translateX(-50%)} }
.idx-ti {
    display: inline-flex;
    align-items: center;
    gap: .65rem;
    color: #fff;
    font-weight: 700;
    font-size: .88rem;
    padding: 0 2.5rem;
    border-left: 1px solid rgba(255,255,255,.2);
    flex-shrink: 0;
}

/* ══════════════════════ SECTION HEADER ══════════════════════ */
.idx-sec { padding: 5.5rem 0; }
.idx-sec-head { text-align: center; margin-bottom: 3.5rem; }
.idx-label {
    display: inline-flex;
    align-items: center;
    gap: .6rem;
    font-size: .72rem;
    letter-spacing: .2em;
    text-transform: uppercase;
    color: var(--accent-primary, #2563eb);
    font-weight: 700;
    margin-bottom: .8rem;
}
.idx-label::before, .idx-label::after { content:''; width:22px; height:1px; background:var(--accent-primary,#2563eb); }
.idx-title {
    font-size: clamp(1.8rem, 3vw, 2.8rem);
    font-weight: 900;
    color: var(--text-primary);
    letter-spacing: -.02em;
    line-height: 1.2;
}
.idx-title-sub { color: var(--text-secondary); margin-top: .6rem; font-size: .95rem; }
.idx-view-all {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    color: var(--accent-primary, #2563eb);
    font-weight: 700;
    font-size: .88rem;
    border: 1.5px solid var(--accent-primary, #2563eb);
    padding: .6rem 1.5rem;
    border-radius: 50px;
    transition: all .3s;
    margin-top: 1.5rem;
    text-decoration: none;
}
.idx-view-all:hover { background: var(--accent-primary, #2563eb); color: #fff; }
.idx-view-all svg { width: 15px; height: 15px; }

/* ══════════════════════ FLASH SALE ══════════════════════ */
.idx-flash {
    background: var(--bg-primary);
    border-radius: 24px;
    overflow: hidden;
    position: relative;
    margin-bottom: 5rem;
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-lg);
}
.idx-flash-inner {
    display: grid;
    grid-template-columns: 1fr 1fr;
    align-items: center;
}
.idx-flash-left { padding: 3rem 3.5rem; }
.idx-flash-tag {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    background: linear-gradient(135deg, #ef4444, #f97316);
    color: #fff;
    padding: .4rem 1rem;
    border-radius: 8px;
    font-weight: 800;
    font-size: .8rem;
    letter-spacing: .08em;
    margin-bottom: 1.4rem;
    animation: flashPulse 1.8s ease-in-out infinite;
}
@keyframes flashPulse { 0%,100%{opacity:1} 50%{opacity:.75} }

.idx-flash-name {
    font-size: 2rem;
    font-weight: 900;
    color: var(--text-primary);
    margin-bottom: .5rem;
    line-height: 1.2;
}
.idx-flash-prices {
    display: flex;
    align-items: baseline;
    gap: 1rem;
    margin: 1.2rem 0 1.8rem;
    flex-wrap: wrap;
}
.idx-flash-old   { font-size: .9rem; color: var(--text-secondary); text-decoration: line-through; }
.idx-flash-new   { font-size: 1.9rem; font-weight: 900; color: #10b981; }
.idx-flash-pct   { background: linear-gradient(135deg,#ef4444,#f97316); color:#fff; padding:.25rem .75rem; border-radius:7px; font-weight:800; font-size:.85rem; }

.idx-flash-timer { display:flex; gap:.5rem; align-items:center; margin-bottom:2rem; }
.idx-ft {
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: 10px;
    padding: .7rem .9rem;
    min-width: 56px;
    text-align: center;
}
.idx-ftv { display:block; font-size:1.5rem; font-weight:900; color:var(--text-primary); line-height:1; }
.idx-ftl { font-size:.6rem; color:var(--text-secondary); text-transform:uppercase; letter-spacing:.1em; }
.idx-fts { color:var(--text-secondary); font-size:1.3rem; font-weight:700; }

.idx-btn-buy {
    display: inline-flex;
    align-items: center;
    gap: .6rem;
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: #fff;
    padding: 1rem 2.2rem;
    border-radius: 14px;
    font-weight: 800;
    font-size: .95rem;
    transition: all .3s;
    box-shadow: 0 8px 24px rgba(239,68,68,.35);
    text-decoration: none;
}
.idx-btn-buy:hover { transform:translateY(-3px); box-shadow:0 14px 32px rgba(239,68,68,.5); }
.idx-btn-buy svg { width:18px; height:18px; }

.idx-flash-right {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2.5rem;
    background: var(--bg-secondary);
    border-right: 1px solid var(--border-color);
    min-height: 360px;
}
.idx-flash-img {
    width: 220px;
    height: 280px;
    object-fit: cover;
    border-radius: 18px;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--border-color);
}
.idx-flash-ring {
    position: absolute;
    top: 1.8rem;
    left: 1.8rem;
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ef4444, #f97316);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 900;
    line-height: 1.15;
    box-shadow: 0 8px 22px rgba(239,68,68,.45);
}
.idx-flash-ring big   { font-size:1.2rem; }
.idx-flash-ring small { font-size:.6rem; letter-spacing:.05em; }

/* ══════════════════════ PRODUCTS ══════════════════════ */
.idx-products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 2rem;
}

/* کارت محصول — همان استایل products.php */
.idx-pcard {
    background: var(--bg-primary);
    border-radius: 20px;
    overflow: hidden;
    transition: all .4s cubic-bezier(.4,0,.2,1);
    border: 1px solid var(--border-color);
    cursor: pointer;
    display: flex;
    flex-direction: column;
}
.idx-pcard:hover { transform: translateY(-10px); box-shadow: var(--shadow-lg); }

.idx-pcard-img-wrap { position: relative; padding-top: 100%; overflow: hidden; }
.idx-pcard-img {
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    object-fit: cover;
    transition: transform .5s ease;
}
.idx-pcard:hover .idx-pcard-img { transform: scale(1.1); }

.idx-pcard-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    z-index: 10;
    background: linear-gradient(135deg, #ef4444, #f97316);
    color: #fff;
    padding: .4rem .8rem;
    border-radius: 10px;
    font-size: .8rem;
    font-weight: 700;
    box-shadow: 0 4px 15px rgba(239,68,68,.4);
}

/* wishlist */
.idx-wish {
    position: absolute;
    top: 14px;
    left: 14px;
    z-index: 10;
    width: 36px; height: 36px;
    border-radius: 50%;
    background: rgba(255,255,255,.92);
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all .3s;
    color: #bbb;
    box-shadow: 0 2px 10px rgba(0,0,0,.15);
}
.idx-wish:hover, .idx-wish.on { color: #ef4444; transform: scale(1.15); }
.idx-wish svg { width: 16px; height: 16px; }

.idx-pcard-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity .3s;
}
.idx-pcard:hover .idx-pcard-overlay { opacity: 1; }
.idx-quick-view {
    background: white;
    color: var(--accent-primary);
    padding: .8rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    transform: translateY(20px);
    transition: transform .3s ease;
    font-size: .9rem;
}
.idx-pcard:hover .idx-quick-view { transform: translateY(0); }

.idx-pcard-info { padding: 1.5rem; display: flex; flex-direction: column; flex: 1; }

.idx-pcard-name {
    font-size: 1.05rem;
    font-weight: 700;
    margin-bottom: 1rem;
    line-height: 1.4;
    flex: 1;
}
.idx-pcard-name a { color: var(--text-primary); text-decoration: none; transition: color .3s; }
.idx-pcard-name a:hover { color: var(--accent-primary); }

.idx-pcard-pricing {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.2rem;
    flex-wrap: wrap;
}
.idx-pcard-old  { font-size: .9rem; color: var(--text-secondary); text-decoration: line-through; }
.idx-pcard-new  { font-size: 1.3rem; font-weight: 800; color: #10b981; }

.idx-pcard-btn {
    width: 100%;
    padding: .9rem;
    background: linear-gradient(135deg, var(--accent-primary, #2563eb), var(--accent-hover, #1d4ed8));
    color: #fff;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
    cursor: pointer;
    transition: all .3s;
    font-family: inherit;
    font-size: .9rem;
}
.idx-pcard-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(37,99,235,.3); }
.idx-pcard-btn svg { width: 18px; height: 18px; }

/* ══════════════════════ COUNTDOWN ══════════════════════ */
.idx-countdown {
    background: linear-gradient(135deg, var(--accent-primary, #1e3a8a), #1e40af);
    border-radius: 24px;
    padding: 4.5rem 2rem;
    text-align: center;
    position: relative;
    overflow: hidden;
    margin-bottom: 5rem;
}
.idx-countdown::before {
    content: '';
    position: absolute;
    width: 600px; height: 600px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(255,255,255,.06) 0%, transparent 65%);
    top: 50%; left: 50%;
    transform: translate(-50%,-50%);
    pointer-events: none;
}
.idx-countdown-inner { position: relative; z-index: 2; }
.idx-cd-label { color: rgba(255,255,255,.7); font-size: .8rem; letter-spacing: .18em; text-transform: uppercase; font-weight: 700; margin-bottom: 1rem; display: block; }
.idx-cd-title { font-size: clamp(1.8rem, 3.5vw, 2.8rem); font-weight: 900; color: #fff; margin-bottom: .6rem; }
.idx-cd-sub   { color: rgba(255,255,255,.6); margin-bottom: 2.5rem; font-size: .95rem; }
.idx-cdt {
    display: inline-flex;
    gap: .8rem;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
}
.idx-cd-box {
    background: rgba(255,255,255,.1);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 16px;
    padding: 1.4rem 1.2rem;
    min-width: 90px;
    text-align: center;
    backdrop-filter: blur(4px);
}
.idx-cd-val  { display: block; font-size: 2.6rem; font-weight: 900; color: #fff; line-height: 1; margin-bottom: .4rem; }
.idx-cd-lbl  { font-size: .68rem; color: rgba(255,255,255,.5); text-transform: uppercase; letter-spacing: .12em; }
.idx-cd-sep  { color: rgba(255,255,255,.3); font-size: 2rem; font-weight: 700; }
.idx-cd-btn  {
    display: inline-flex;
    align-items: center;
    gap: .6rem;
    margin-top: 2.5rem;
    padding: 1rem 2.5rem;
    border-radius: 14px;
    background: rgba(255,255,255,.15);
    border: 1.5px solid rgba(255,255,255,.35);
    color: #fff;
    font-weight: 700;
    font-size: .95rem;
    transition: all .3s;
    text-decoration: none;
}
.idx-cd-btn:hover { background: rgba(255,255,255,.25); transform: translateY(-3px); }
.idx-cd-btn svg { width: 17px; height: 17px; }

/* ══════════════════════ POSTS ══════════════════════ */
.idx-posts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
}
.idx-post {
    background: var(--bg-primary);
    border-radius: 20px;
    overflow: hidden;
    border: 1px solid var(--border-color);
    transition: all .4s cubic-bezier(.4,0,.2,1);
    display: flex;
    flex-direction: column;
}
.idx-post:hover { transform: translateY(-8px); box-shadow: var(--shadow-lg); }
.idx-post-img-wrap { position: relative; padding-top: 58%; overflow: hidden; }
.idx-post-img { position: absolute; top:0; left:0; width:100%; height:100%; object-fit:cover; transition: transform .5s; }
.idx-post:hover .idx-post-img { transform: scale(1.07); }
.idx-post-cat {
    position: absolute;
    bottom: 14px; right: 14px;
    background: rgba(0,0,0,.7);
    backdrop-filter: blur(8px);
    color: #fff;
    padding: .3rem .85rem;
    border-radius: 8px;
    font-weight: 700;
    font-size: .75rem;
}
.idx-post-time {
    position: absolute;
    top: 14px; left: 14px;
    background: rgba(0,0,0,.65);
    backdrop-filter: blur(8px);
    color: #fff;
    padding: .3rem .8rem;
    border-radius: 8px;
    font-size: .72rem;
    font-weight: 600;
}
.idx-post-body { padding: 1.5rem; flex: 1; display: flex; flex-direction: column; }
.idx-post-title { font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin-bottom: .75rem; line-height: 1.4; }
.idx-post-title a { color: inherit; text-decoration: none; transition: color .3s; }
.idx-post-title a:hover { color: var(--accent-primary); }
.idx-post-excerpt { color: var(--text-secondary); font-size: .9rem; line-height: 1.78; flex: 1; margin-bottom: 1.25rem; }
.idx-post-foot {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 1rem;
    border-top: 1px solid var(--border-color);
}
.idx-post-author { display: flex; align-items: center; gap: .6rem; color: var(--text-secondary); font-size: .85rem; }
.idx-post-av {
    width: 30px; height: 30px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--accent-primary,#2563eb), var(--accent-hover,#1d4ed8));
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: .82rem; flex-shrink: 0;
}
.idx-read-more { display:flex; align-items:center; gap:.3rem; color:var(--accent-primary); text-decoration:none; font-weight:700; font-size:.85rem; transition:gap .3s; }
.idx-read-more:hover { gap:.6rem; }
.idx-read-more svg { width:15px; height:15px; }

/* ══════════════════════ NEWSLETTER ══════════════════════ */
.idx-nl {
    background: var(--bg-primary);
    border-radius: 24px;
    padding: 4.5rem 2rem;
    text-align: center;
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-md);
    margin-bottom: 5rem;
    position: relative;
    overflow: hidden;
}
.idx-nl::before {
    content: '📚';
    font-size: 9rem;
    opacity: .04;
    position: absolute;
    top: -1rem; right: 0;
    pointer-events: none;
    line-height: 1;
}
.idx-nl-title { font-size: clamp(1.6rem, 2.8vw, 2.4rem); font-weight: 900; color: var(--text-primary); margin-bottom: .7rem; }
.idx-nl-sub   { color: var(--text-secondary); margin-bottom: 2.2rem; font-size: .95rem; }
.idx-nl-form  { display: flex; gap: 1rem; max-width: 520px; margin: 0 auto; flex-wrap: wrap; }
.idx-nl-input {
    flex: 1;
    padding: 1rem 1.4rem;
    border: 1.5px solid var(--border-color);
    border-radius: 14px;
    font-size: .95rem;
    font-family: inherit;
    background: var(--bg-secondary);
    color: var(--text-primary);
    transition: all .3s;
    outline: none;
    min-width: 200px;
}
.idx-nl-input:focus { border-color: var(--accent-primary); box-shadow: 0 0 0 3px rgba(37,99,235,.1); }
.idx-nl-btn {
    padding: 1rem 2rem;
    border-radius: 14px;
    background: linear-gradient(135deg, var(--accent-primary,#2563eb), var(--accent-hover,#1d4ed8));
    color: #fff;
    font-weight: 700;
    font-size: .95rem;
    border: none;
    cursor: pointer;
    font-family: inherit;
    transition: all .3s;
    white-space: nowrap;
}
.idx-nl-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 22px rgba(37,99,235,.3); }

/* ══════════════════════ FEATURES ══════════════════════ */
.idx-feats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1.5rem;
    margin-bottom: 5rem;
}
.idx-feat {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 20px;
    padding: 2.2rem 1.8rem;
    text-align: center;
    transition: all .4s;
    position: relative;
    overflow: hidden;
}
.idx-feat::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--accent-primary,#2563eb), #60a5fa);
    transform: scaleX(0);
    transition: transform .35s ease;
    transform-origin: right;
}
.idx-feat:hover { transform: translateY(-8px); box-shadow: var(--shadow-lg); }
.idx-feat:hover::after { transform: scaleX(1); transform-origin: left; }
.idx-feat-icon  { font-size: 2.6rem; margin-bottom: 1.1rem; display: block; }
.idx-feat-title { font-weight: 800; color: var(--text-primary); margin-bottom: .4rem; font-size: 1rem; }
.idx-feat-desc  { color: var(--text-secondary); font-size: .84rem; line-height: 1.7; }

/* ══════════════════════ TESTIMONIALS ══════════════════════ */
.idx-testi-wrap { overflow: hidden; border-radius: 20px; margin-bottom: 5rem; }
.idx-testi-track { display: flex; transition: transform .55s cubic-bezier(.4,0,.2,1); }
.idx-testi-card {
    min-width: 100%;
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 20px;
    padding: 3rem;
    text-align: center;
    position: relative;
}
.idx-testi-card::before {
    content: '"';
    font-size: 8rem;
    color: var(--accent-primary);
    opacity: .08;
    position: absolute;
    top: -1.5rem;
    right: 2rem;
    line-height: 1;
    pointer-events: none;
}
.idx-testi-stars { font-size: 1.3rem; color: #f59e0b; letter-spacing: 3px; margin-bottom: 1.4rem; }
.idx-testi-text {
    font-size: 1.15rem;
    color: var(--text-primary);
    line-height: 1.9;
    margin-bottom: 2rem;
    font-style: italic;
    max-width: 680px;
    margin-left: auto; margin-right: auto;
}
.idx-testi-av {
    width: 54px; height: 54px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--accent-primary,#2563eb), var(--accent-hover,#1d4ed8));
    color: #fff;
    font-weight: 900; font-size: 1.3rem;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 1rem;
}
.idx-testi-name { font-weight: 800; color: var(--text-primary); font-size: 1rem; }
.idx-testi-role { font-size: .8rem; color: var(--text-secondary); margin-top: .2rem; }
.idx-testi-ctrl { display: flex; align-items: center; justify-content: center; gap: 1rem; margin-top: 2rem; }
.idx-t-btn {
    width: 42px; height: 42px;
    border-radius: 50%;
    border: 1.5px solid var(--border-color);
    background: var(--bg-primary);
    color: var(--text-secondary);
    font-size: 1.3rem;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: all .3s;
    line-height: 1;
}
.idx-t-btn:hover { background: var(--accent-primary); border-color: var(--accent-primary); color: #fff; }
.idx-t-dots { display: flex; gap: .5rem; align-items: center; }
.idx-t-dot { width: 9px; height: 9px; border-radius: 50%; background: var(--border-color); border: none; cursor: pointer; padding: 0; transition: all .3s; }
.idx-t-dot.on { width: 26px; border-radius: 5px; background: var(--accent-primary); }

/* ══════════════════════ TOAST ══════════════════════ */
.idx-toast {
    position: fixed;
    bottom: 28px; right: 28px;
    background: var(--bg-primary);
    color: var(--text-primary);
    border: 1px solid var(--border-color);
    padding: .85rem 1.7rem;
    border-radius: 14px;
    font-size: .88rem;
    font-weight: 600;
    box-shadow: var(--shadow-lg);
    z-index: 9999;
    transform: translateY(80px);
    opacity: 0;
    transition: all .35s cubic-bezier(.4,0,.2,1);
    pointer-events: none;
}
.idx-toast.show { transform: translateY(0); opacity: 1; }

/* ══════════════════════ NO DATA ══════════════════════ */
.idx-no-data {
    grid-column: 1/-1;
    text-align: center;
    padding: 4rem 2rem;
    border-radius: 20px;
    border: 2px dashed var(--border-color);
    color: var(--text-secondary);
}

/* ══════════════════════ AOS ══════════════════════ */
[data-aos] {
    opacity: 0;
    transition-property: transform, opacity;
    transition-duration: .65s;
    transition-timing-function: cubic-bezier(.4,0,.2,1);
}
[data-aos].aos-animate { opacity: 1; }
[data-aos="fade-up"]    { transform: translateY(32px); }
[data-aos="fade-up"].aos-animate    { transform: translateY(0); }
[data-aos="zoom-in"]    { transform: scale(.92); }
[data-aos="zoom-in"].aos-animate    { transform: scale(1); }
[data-aos="flip-left"]  { transform: perspective(1200px) rotateY(-90deg); }
[data-aos="flip-left"].aos-animate  { transform: perspective(1200px) rotateY(0); }

/* ══════════════════════ RESPONSIVE ══════════════════════ */
@media (max-width: 1100px) {
    .idx-hero-inner { grid-template-columns: 1fr; padding: 8.5rem 1.5rem 5.5rem; text-align: center; }
    .idx-hero-vis { display: none; }
    .idx-sub, .idx-btns, .idx-hero-stats { justify-content: center; margin-left: auto; margin-right: auto; }
    .idx-sub { text-align: center; }
}
@media (max-width: 900px) {
    .idx-flash-inner { grid-template-columns: 1fr; }
    .idx-flash-right { display: none; }
    .idx-flash-left { padding: 2.5rem; }
}
@media (max-width: 768px) {
    .idx-sec { padding: 4rem 0; }
    .idx-wrap { padding: 0 1.25rem; }
    .idx-products-grid { grid-template-columns: repeat(2, 1fr); gap: 1.25rem; }
    .idx-posts-grid    { grid-template-columns: repeat(2, 1fr); gap: 1.25rem; }
    .idx-feats-grid    { grid-template-columns: repeat(2, 1fr); }
    .idx-cd-box { min-width: 76px; padding: 1.1rem .85rem; }
    .idx-cd-val { font-size: 2rem; }
    .idx-countdown, .idx-nl { padding: 3.5rem 1.5rem; }
    .idx-flash-name { font-size: 1.7rem; }
    .idx-hero-inner { padding: 7.5rem 1.25rem 4.5rem; }
}
@media (max-width: 576px) {
    .idx-h1 { font-size: 2.2rem; }
    .idx-btns { flex-direction: column; align-items: stretch; }
    .idx-btn-primary, .idx-btn-ghost { justify-content: center; }
    .idx-products-grid { grid-template-columns: repeat(2, 1fr); gap: .9rem; }
    .idx-posts-grid { grid-template-columns: 1fr; }
    .idx-feats-grid { grid-template-columns: 1fr 1fr; }
    .idx-cdt { gap: .45rem; }
    .idx-cd-box { min-width: 64px; }
    .idx-cd-val { font-size: 1.8rem; }
    .idx-nl-form { flex-direction: column; }
    .idx-nl-btn { width: 100%; }
    .idx-hero-stats { gap: 1.8rem; }
}
@media (max-width: 420px) {
    .idx-products-grid { grid-template-columns: 1fr; }
    .idx-feats-grid { grid-template-columns: 1fr; }
    .idx-wrap { padding: 0 1rem; }
}
</style>

<!-- ═══════ HERO ═══════ -->
<section class="idx-hero">
    <div class="idx-hero-inner">
        <div class="idx-hero-text" data-aos="fade-up">
            <div class="idx-eyebrow"><span></span> فروشگاه آنلاین کتاب <span></span></div>
            <h1 class="idx-h1">
            <em>کتاب</em>    نت
            </h1>
            <p class="idx-sub">
                بیش از <strong><?= number_format($total_products + 10000) ?></strong> عنوان کتاب<br>
                با بهترین کیفیت، قیمت مناسب و ارسال سریع.
            </p>
            <div class="idx-btns">
                <a href="products.php" class="idx-btn-primary">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17,18A2,2 0 0,1 19,20A2,2 0 0,1 17,22C15.89,22 15,21.1 15,20C15,18.89 15.89,18 17,18M1,2H4.27L5.21,4H20A1,1 0 0,1 21,5L17.3,11.97C16.96,12.58 16.3,13 15.55,13H8.1L7.2,14.63L7.17,14.75A0.25,0.25 0 0,0 7.42,15H19V17H7C5.89,17 5,16.1 5,15L6.6,11.59L3,4H1V2Z"/></svg>
                    مشاهده محصولات
                </a>
                <a href="#idx-posts" class="idx-btn-ghost">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19,5V7H15V5H19M9,5V11H5V5H9M19,13V19H15V13H19M9,17V19H5V17H9M21,3H13V9H21V3M11,3H3V13H11V3M21,11H13V21H21V11M11,15H3V21H11V15Z"/></svg>
                    مقالات آموزشی
                </a>
            </div>
            <div class="idx-hero-stats">
                <div class="idx-hs">
                    <strong><?= number_format($total_products + 10000) ?>+</strong>
                    <span>عنوان کتاب</span>
                </div>
                <div class="idx-hs">
                    <strong><?= number_format($total_members + 5000) ?>+</strong>
                    <span>کاربر فعال</span>
                </div>
                <div class="idx-hs"><strong>24/7</strong><span>پشتیبانی</span></div>
                <div class="idx-hs"><strong>4.9 ⭐</strong><span>رضایت مشتری</span></div>
            </div>
        </div>
        <div class="idx-hero-vis" data-aos="fade-up" data-aos-delay="200">
            <div class="idx-hero-frame">
                <img src="./img/25.jpg" alt="کتاب">
                <div class="idx-float-badge">
                    <big>50%</big>
                    تخفیف ویژه
                </div>
            </div>
        </div>
    </div>
    <div class="idx-scroll">
        <span>اسکرول</span>
        <div class="idx-scroll-line"></div>
    </div>
</section>

<!-- ═══════ TICKER ═══════ -->
<div class="idx-ticker" aria-hidden="true">
    <div class="idx-ticker-track">
        <?php for ($t = 0; $t < 2; $t++): ?>
            <span class="idx-ti">📦 ارسال رایگان بالای ۲۰۰ هزار تومان</span>
            <span class="idx-ti">⚡ تخفیف ویژه تا ۵۰٪</span>
            <span class="idx-ti">🔒 پرداخت امن و رمزگذاری‌شده</span>
            <span class="idx-ti">🏆 کتاب‌های اصل و با کیفیت</span>
            <span class="idx-ti">🎧 پشتیبانی ۲۴ ساعته</span>
            <span class="idx-ti">🚀 تحویل سریع در سراسر ایران</span>
        <?php endfor; ?>
    </div>
</div>

<div class="idx-wrap">

    <!-- ═══════ FLASH SALE ═══════ -->
    <?php if ($flash_product):
        $fdisc = calcDisc((int)$flash_product['price'], (int)$flash_product['new-price']);
    ?>
    <section class="idx-sec" style="padding-bottom:0">
        <div class="idx-flash" data-aos="zoom-in">
            <div class="idx-flash-inner">
                <div class="idx-flash-left">
                    <div class="idx-flash-tag">⚡ فلش سیل — فقط امروز</div>
                    <div class="idx-flash-name"><?= escape($flash_product['name']) ?></div>
                    <div class="idx-flash-prices">
                        <span class="idx-flash-old"><?= formatPrice((int)$flash_product['price']) ?></span>
                        <span class="idx-flash-new"><?= formatPrice((int)$flash_product['new-price']) ?></span>
                        <span class="idx-flash-pct"><?= $fdisc ?>% تخفیف</span>
                    </div>
                    <div class="idx-flash-timer">
                        <div class="idx-ft"><span class="idx-ftv" id="fh">12</span><span class="idx-ftl">ساعت</span></div>
                        <span class="idx-fts">:</span>
                        <div class="idx-ft"><span class="idx-ftv" id="fm">00</span><span class="idx-ftl">دقیقه</span></div>
                        <span class="idx-fts">:</span>
                        <div class="idx-ft"><span class="idx-ftv" id="fs">00</span><span class="idx-ftl">ثانیه</span></div>
                    </div>
                    <a href="single_product.php?product=<?= $flash_product['id'] ?>" class="idx-btn-buy">
                        همین الان بخر
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M4,11V13H16L10.5,18.5L11.92,19.92L19.84,12L11.92,4.08L10.5,5.5L16,11H4Z"/></svg>
                    </a>
                </div>
                <div class="idx-flash-right">
                    <img class="idx-flash-img"
                         src="./upload/products/<?= escape($flash_product['pic']) ?>"
                         alt="<?= escape($flash_product['name']) ?>">
                    <div class="idx-flash-ring">
                        <big><?= $fdisc ?>%</big>
                        <small>تخفیف</small>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ═══════ PRODUCTS ═══════ -->
    <section class="idx-sec">
        <div class="idx-sec-head" data-aos="fade-up">
            <div class="idx-label">محصولات ویژه</div>
            <h2 class="idx-title">بهترین کتاب‌های ما</h2>
            <p class="idx-title-sub">منتخب ویراستاران کتاب‌نت برای شما</p>
            <a href="products.php" class="idx-view-all">
                مشاهده همه
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z"/></svg>
            </a>
        </div>

        <!-- گرید ساده محصولات — بدون فیلتر دسته‌بندی -->
        <div class="idx-products-grid">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $index => $product): ?>
                    <?php
                    $disc = calcDisc((int)$product['price'], (int)$product['new-price']);
                    ?>
                    <article class="idx-pcard"
                             data-aos="fade-up"
                             data-aos-delay="<?= $index * 80 ?>">
                        <div class="idx-pcard-img-wrap">
                            <img class="idx-pcard-img"
                                 src="./upload/products/<?= escape($product['pic']) ?>"
                                 alt="<?= escape($product['name']) ?>"
                                 loading="lazy">
                            <?php if ($disc > 0): ?>
                                <span class="idx-pcard-badge"><?= $disc ?>% تخفیف</span>
                            <?php endif; ?>
                            <button class="idx-wish" data-id="<?= $product['id'] ?>" aria-label="علاقه‌مندی">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                                </svg>
                            </button>
                            <div class="idx-pcard-overlay">
                                <a href="single_product.php?product=<?= $product['id'] ?>" class="idx-quick-view">
                                    مشاهده سریع
                                </a>
                            </div>
                        </div>
                        <div class="idx-pcard-info">
                            <h3 class="idx-pcard-name">
                                <a href="single_product.php?product=<?= $product['id'] ?>">
                                    <?= escape($product['name']) ?>
                                </a>
                            </h3>
                            <div class="idx-pcard-pricing">
                                <?php if ((int)$product['price'] !== (int)$product['new-price']): ?>
                                    <span class="idx-pcard-old"><?= formatPrice((int)$product['price']) ?></span>
                                <?php endif; ?>
                                <span class="idx-pcard-new"><?= formatPrice((int)$product['new-price']) ?></span>
                            </div>
                            <a href="single_product.php?product=<?= $product['id'] ?>" class="idx-pcard-btn">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17,18A2,2 0 0,1 19,20A2,2 0 0,1 17,22C15.89,22 15,21.1 15,20C15,18.89 15.89,18 17,18M1,2H4.27L5.21,4H20A1,1 0 0,1 21,5L17.3,11.97C16.96,12.58 16.3,13 15.55,13H8.1L7.2,14.63L7.17,14.75A0.25,0.25 0 0,0 7.42,15H19V17H7C5.89,17 5,16.1 5,15L6.6,11.59L3,4H1V2Z"/>
                                </svg>
                                افزودن به سبد
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="idx-no-data">محصولی یافت نشد.</div>
            <?php endif; ?>
        </div>
    </section>

    <!-- ═══════ COUNTDOWN ═══════ -->
    <div class="idx-countdown" data-aos="zoom-in">
        <div class="idx-countdown-inner">
            <span class="idx-cd-label">پیشنهاد ویژه هفته</span>
            <h2 class="idx-cd-title">تخفیف ۵۰٪ تا پایان این هفته</h2>
            <p class="idx-cd-sub">فرصت را از دست ندهید — با بهترین قیمت بخرید</p>
            <div class="idx-cdt">
                <div class="idx-cd-box"><span class="idx-cd-val" id="cd-d">00</span><span class="idx-cd-lbl">روز</span></div>
                <span class="idx-cd-sep">:</span>
                <div class="idx-cd-box"><span class="idx-cd-val" id="cd-h">00</span><span class="idx-cd-lbl">ساعت</span></div>
                <span class="idx-cd-sep">:</span>
                <div class="idx-cd-box"><span class="idx-cd-val" id="cd-m">00</span><span class="idx-cd-lbl">دقیقه</span></div>
                <span class="idx-cd-sep">:</span>
                <div class="idx-cd-box"><span class="idx-cd-val" id="cd-s">00</span><span class="idx-cd-lbl">ثانیه</span></div>
            </div>
            <a href="products.php" class="idx-cd-btn">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17,18A2,2 0 0,1 19,20A2,2 0 0,1 17,22C15.89,22 15,21.1 15,20C15,18.89 15.89,18 17,18M1,2H4.27L5.21,4H20A1,1 0 0,1 21,5L17.3,11.97C16.96,12.58 16.3,13 15.55,13H8.1L7.2,14.63L7.17,14.75A0.25,0.25 0 0,0 7.42,15H19V17H7C5.89,17 5,16.1 5,15L6.6,11.59L3,4H1V2Z"/></svg>
                خرید ویژه
            </a>
        </div>
    </div>

    <!-- ═══════ POSTS ═══════ -->
    <section id="idx-posts" class="idx-sec">
        <div class="idx-sec-head" data-aos="fade-up">
            <div class="idx-label">مقالات آموزشی</div>
            <h2 class="idx-title">جدیدترین مقالات</h2>
            <p class="idx-title-sub">دانش خود را با بهترین مطالب گسترش دهید</p>
            <a href="posts.php" class="idx-view-all">
                مشاهده همه
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z"/></svg>
            </a>
        </div>
        <div class="idx-posts-grid">
            <?php if (!empty($posts)): ?>
                <?php foreach ($posts as $index => $post): ?>
                    <?php $pcat = fetchOne($db, "SELECT title FROM categories WHERE id = ?", [$post['category_id']]); ?>
                    <article class="idx-post" data-aos="fade-up" data-aos-delay="<?= $index * 80 ?>">
                        <div class="idx-post-img-wrap">
                            <img class="idx-post-img"
                                 src="./upload/posts/<?= escape($post['image']) ?>"
                                 alt="<?= escape($post['title']) ?>"
                                 loading="lazy">
                            <span class="idx-post-cat"><?= escape($pcat['title'] ?? 'نامشخص') ?></span>
                            <span class="idx-post-time">⏱ <?= rand(3, 12) ?> دقیقه</span>
                        </div>
                        <div class="idx-post-body">
                            <h3 class="idx-post-title">
                                <a href="single.php?post=<?= $post['id'] ?>"><?= escape($post['title']) ?></a>
                            </h3>
                            <p class="idx-post-excerpt"><?= escape(truncateText($post['body'], 120)) ?></p>
                            <div class="idx-post-foot">
                                <div class="idx-post-author">
                                    <div class="idx-post-av"><?= mb_substr($post['author'], 0, 1) ?></div>
                                    <?= escape($post['author']) ?>
                                </div>
                                <a href="single.php?post=<?= $post['id'] ?>" class="idx-read-more">
                                    ادامه
                                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M4,11V13H16L10.5,18.5L11.92,19.92L19.84,12L11.92,4.08L10.5,5.5L16,11H4Z"/></svg>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="idx-no-data">مقاله‌ای یافت نشد.</div>
            <?php endif; ?>
        </div>
    </section>

    <!-- ═══════ NEWSLETTER ═══════ -->
    <div class="idx-nl" data-aos="zoom-in">
        <div class="idx-label" style="justify-content:center">خبرنامه</div>
        <h2 class="idx-nl-title">همیشه یک قدم جلوتر باش</h2>
        <p class="idx-nl-sub">از جدیدترین کتاب‌ها، مقالات و تخفیف‌های ویژه باخبر شو</p>
        <div class="idx-nl-form">
            <input class="idx-nl-input" type="email" id="nlEmail" placeholder="ایمیل خود را وارد کنید">
            <button class="idx-nl-btn" id="nlBtn">عضو می‌شوم</button>
        </div>
    </div>

    <!-- ═══════ FEATURES ═══════ -->
    <section class="idx-sec" style="padding-top:0">
        <div class="idx-sec-head" data-aos="fade-up">
            <div class="idx-label">چرا کتاب‌نت؟</div>
            <h2 class="idx-title">تجربه‌ای متفاوت از خرید</h2>
        </div>
        <div class="idx-feats-grid">
            <?php
            $feats = [
                ['🚚', 'ارسال رایگان',    'برای خریدهای بالای ۲۰۰ هزار تومان در سراسر ایران'],
                ['🔒', 'پرداخت امن',      'درگاه پرداخت کاملاً ایمن و رمزگذاری‌شده'],
                ['🎧', 'پشتیبانی ۲۴/۷',  'تیم ما همیشه آماده پاسخگویی است'],
                ['🏆', 'کتاب‌های اصل',    'تضمین اصالت و کیفیت تمام محصولات'],
                ['🔄', 'بازگشت آسان',     '۷ روز ضمانت بازگشت کالا'],
            ];
            foreach ($feats as $i => $f): ?>
                <div class="idx-feat" data-aos="flip-left" data-aos-delay="<?= $i * 60 ?>">
                    <span class="idx-feat-icon"><?= $f[0] ?></span>
                    <div class="idx-feat-title"><?= $f[1] ?></div>
                    <div class="idx-feat-desc"><?= $f[2] ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- ═══════ TESTIMONIALS ═══════ -->
    <section class="idx-sec" style="padding-top:0">
        <div class="idx-sec-head" data-aos="fade-up">
            <div class="idx-label">نظرات مشتریان</div>
            <h2 class="idx-title">آن‌ها چه می‌گویند</h2>
        </div>
        <div class="idx-testi-wrap">
            <div class="idx-testi-track" id="testiTrack">
                <?php
                $testimonials = [
                    ['ع', 'علی احمدی',    'کتاب‌خوان حرفه‌ای', '★★★★★', 'سایت فوق‌العاده‌ای! کتاب‌های با کیفیت، ارسال سریع و قیمت مناسب. از پیدا کردن کتاب تا پرداخت فقط چند دقیقه طول کشید.'],
                    ['س', 'سارا محمدی',   'نویسنده',            '★★★★★', 'بهترین تجربه خرید آنلاین کتاب! پشتیبانی عالی و تحویل به موقع. کاملاً راضیم و حتماً دوباره خرید خواهم کرد.'],
                    ['ر', 'رضا کریمی',    'دانشجو',             '★★★★☆', 'تنوع کتاب‌ها عالیه. هر چی دنبالش بودم پیدا کردم. قیمت‌ها هم رقابتیه و تخفیف‌های خوبی می‌ذارن.'],
                    ['ن', 'نازنین رضایی', 'مدیر فروش',          '★★★★★', 'از بسته‌بندی تا تحویل همه چیز عالی بود. کتاب در کمال سلامت رسید. کتاب‌نت رو به همه توصیه می‌کنم.'],
                ];
                foreach ($testimonials as $t): ?>
                    <div class="idx-testi-card">
                        <div class="idx-testi-stars"><?= $t[4] ?></div>
                        <p class="idx-testi-text">«<?= $t[3] ?>»</p>
                        <div class="idx-testi-av"><?= $t[0] ?></div>
                        <div class="idx-testi-name"><?= $t[1] ?></div>
                        <div class="idx-testi-role"><?= $t[2] ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="idx-testi-ctrl">
                <button class="idx-t-btn" id="tPrev">‹</button>
                <div class="idx-t-dots" id="tDots"></div>
                <button class="idx-t-btn" id="tNext">›</button>
            </div>
        </div>
    </section>

</div><!-- /idx-wrap -->

<!-- Toast -->
<div class="idx-toast" id="idxToast"></div>

<?php require_once("./include/footer.php"); ?>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── AOS ── */
    const aosObs = new IntersectionObserver(function (entries) {
        entries.forEach(function (e) {
            if (e.isIntersecting) e.target.classList.add('aos-animate');
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -60px 0px' });
    document.querySelectorAll('[data-aos]').forEach(function (el) { aosObs.observe(el); });

    /* ── Toast ── */
    var toast = document.getElementById('idxToast');
    function showToast(msg) {
        toast.textContent = msg;
        toast.classList.add('show');
        setTimeout(function () { toast.classList.remove('show'); }, 2500);
    }

    /* ── Flash Sale Timer ── */
    var flashEnd = new Date();
    flashEnd.setHours(flashEnd.getHours() + 12);
    function updateFlash() {
        var d = flashEnd - Date.now();
        if (d < 0) return;
        document.getElementById('fh').textContent = String(Math.floor(d / 3600000)).padStart(2, '0');
        document.getElementById('fm').textContent = String(Math.floor(d / 60000 % 60)).padStart(2, '0');
        document.getElementById('fs').textContent = String(Math.floor(d / 1000 % 60)).padStart(2, '0');
    }
    updateFlash();
    setInterval(updateFlash, 1000);

    /* ── Countdown ── */
    var cdEnd = new Date();
    cdEnd.setDate(cdEnd.getDate() + 7);
    function updateCd() {
        var d = cdEnd - Date.now();
        if (d < 0) return;
        document.getElementById('cd-d').textContent = String(Math.floor(d / 86400000)).padStart(2, '0');
        document.getElementById('cd-h').textContent = String(Math.floor(d / 3600000 % 24)).padStart(2, '0');
        document.getElementById('cd-m').textContent = String(Math.floor(d / 60000 % 60)).padStart(2, '0');
        document.getElementById('cd-s').textContent = String(Math.floor(d / 1000 % 60)).padStart(2, '0');
    }
    updateCd();
    setInterval(updateCd, 1000);

    /* ── Wishlist ── */
    document.querySelectorAll('.idx-wish').forEach(function (btn) {
        var k = 'wish_' + btn.dataset.id;
        if (localStorage.getItem(k)) {
            btn.classList.add('on');
            btn.querySelector('svg').setAttribute('fill', '#ef4444');
        }
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            btn.classList.toggle('on');
            var on = btn.classList.contains('on');
            btn.querySelector('svg').setAttribute('fill', on ? '#ef4444' : 'none');
            on ? localStorage.setItem(k, '1') : localStorage.removeItem(k);
            showToast(on ? '❤️ به علاقه‌مندی‌ها اضافه شد' : '🤍 از علاقه‌مندی‌ها حذف شد');
        });
    });

    /* ── Testimonials Slider ── */
    var track  = document.getElementById('testiTrack');
    var dotsEl = document.getElementById('tDots');
    if (track) {
        var n = track.children.length;
        var cur = 0;
        for (var i = 0; i < n; i++) {
            var d = document.createElement('button');
            d.className = 'idx-t-dot' + (i === 0 ? ' on' : '');
            (function (idx) { d.onclick = function () { goTo(idx); }; })(i);
            dotsEl.appendChild(d);
        }
        function goTo(idx) {
            cur = (idx + n) % n;
            track.style.transform = 'translateX(' + (cur * 100) + '%)';
            dotsEl.querySelectorAll('.idx-t-dot').forEach(function (dot, j) {
                dot.classList.toggle('on', j === cur);
            });
        }
        document.getElementById('tNext').onclick = function () { goTo(cur + 1); };
        document.getElementById('tPrev').onclick = function () { goTo(cur - 1); };
        setInterval(function () { goTo(cur + 1); }, 5500);
    }

    /* ── Newsletter ── */
    var nlBtn = document.getElementById('nlBtn');
    if (nlBtn) {
        nlBtn.addEventListener('click', function () {
            var v = document.getElementById('nlEmail').value || '';
            if (v && v.includes('@')) {
                showToast('✅ عضویت با موفقیت ثبت شد!');
                document.getElementById('nlEmail').value = '';
            } else {
                showToast('⚠️ لطفاً ایمیل معتبر وارد کنید');
            }
        });
    }

});
</script>