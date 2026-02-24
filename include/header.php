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
$user_avatar_letter = $user_name ? mb_substr($user_name, 0, 1) : '?';
// آواتار واقعی از دیتابیس
$user_real_avatar = null;
if ($logged_in) {
    $member_row = fetchOne($db, "SELECT avatar FROM members WHERE id = ?", [$_SESSION['member_id']]);
    if ($member_row && !empty($member_row['avatar']) && file_exists($member_row['avatar'])) {
        $user_real_avatar = $member_row['avatar'];
    }
}
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
/* ════════════════════════════════════════════════════
   HEADER — Midnight Blue — v5
   ════════════════════════════════════════════════════ */

.hdr-wrapper {
    position: relative;
    width: 100%;
    min-height: 100vh;
    background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 55%, #1e40af 100%);
    overflow: hidden;
}
body.dark-mode .hdr-wrapper {
    background: linear-gradient(135deg, #020617 0%, #0c1e47 55%, #1e3a8a 100%);
}
.hdr-dots {
    position: absolute; inset: 0;
    background-image: radial-gradient(circle, rgba(255,255,255,.05) 1px, transparent 1px);
    background-size: 28px 28px;
    pointer-events: none; z-index: 1;
}
.hdr-glow {
    position: absolute; width: 800px; height: 800px; border-radius: 50%;
    background: radial-gradient(circle, rgba(96,165,250,.16) 0%, transparent 65%);
    top: -250px; right: -200px; pointer-events: none; z-index: 1;
}
.hdr-glow2 {
    position: absolute; width: 600px; height: 600px; border-radius: 50%;
    background: radial-gradient(circle, rgba(167,139,250,.1) 0%, transparent 65%);
    bottom: -150px; left: -100px; pointer-events: none; z-index: 1;
}
.hdr-circles { position: absolute; inset: 0; overflow: hidden; z-index: 1; pointer-events: none; }
.hdr-circles span {
    position: absolute; display: block;
    background: rgba(255,255,255,.07); border-radius: 50%;
    animation: hdrFloat 22s infinite ease-in-out;
}
.hdr-circles span:nth-child(1) { width:70px; height:70px; top:12%; right:22%; animation-delay:0s; }
.hdr-circles span:nth-child(2) { width:110px; height:110px; top:62%; right:78%; animation-delay:4s; }
.hdr-circles span:nth-child(3) { width:90px; height:90px; top:38%; right:48%; animation-delay:8s; }
@keyframes hdrFloat {
    0%,100%{ transform: translate(0,0); opacity:.3; }
    50%     { transform: translate(80px,-80px); opacity:.6; }
}

/* ── NAVBAR ── */
.hdr-nav {
    position: absolute; top:0; left:0; right:0;
    z-index: 500; padding: 1.4rem 0;
    transition: all .3s ease;
}
.hdr-nav.scrolled {
    position: fixed;
    background: rgba(255,255,255,.96);
    backdrop-filter: blur(20px);
    padding: .9rem 0;
    box-shadow: 0 4px 24px rgba(0,0,0,.1);
    border-radius: 0 0 18px 18px;
}
body.dark-mode .hdr-nav.scrolled { background: rgba(15,23,42,.96); }

.hdr-nav-c {
    max-width: 1400px; margin: 0 auto; padding: 0 2rem;
    display: flex; align-items: center; gap: 1.2rem;
}

/* ── همبرگر — سمت راست در RTL ── */
.hdr-ham {
    display: none;
    order: -1;   /* در RTL = سمت راست */
    width: 44px; height: 44px; border-radius: 12px;
    background: rgba(255,255,255,.12); border: 1.5px solid rgba(255,255,255,.2);
    color: #fff; cursor: pointer;
    flex-direction: column; align-items: center; justify-content: center;
    gap: 5px; flex-shrink: 0; transition: all .3s;
}
.hdr-ham:hover { background: rgba(255,255,255,.2); }
.hdr-ham-line {
    width: 20px; height: 2px; background: #fff; border-radius: 2px;
    transition: all .35s cubic-bezier(.4,0,.2,1);
}
.hdr-nav.scrolled .hdr-ham { background: var(--bg-secondary,#f1f5f9); border-color: var(--border-color,#e2e8f0); }
.hdr-nav.scrolled .hdr-ham-line { background: var(--text-primary,#0f172a); }
.hdr-ham.open .hdr-ham-line:nth-child(1) { transform: translateY(7px) rotate(45deg); }
.hdr-ham.open .hdr-ham-line:nth-child(2) { opacity: 0; transform: scaleX(0); }
.hdr-ham.open .hdr-ham-line:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

/* ── Logo ── */
.hdr-logo {
    display: flex; align-items: center; gap: .7rem;
    text-decoration: none; color: #fff;
    font-size: 1.4rem; font-weight: 800;
    flex-shrink: 0; white-space: nowrap;
}
.hdr-logo-icon { font-size: 1.9rem; animation: hdrLogoFloat 3s ease-in-out infinite; }
@keyframes hdrLogoFloat { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-7px)} }
.hdr-nav.scrolled .hdr-logo { color: var(--text-primary,#0f172a); }

/* ── Desktop Menu ── */
.hdr-menu { display: flex; align-items: center; gap: .4rem; list-style: none; flex: 1; }
.hdr-mlink {
    color: rgba(255,255,255,.9); text-decoration: none;
    padding: .6rem 1.2rem; border-radius: 50px;
    font-weight: 600; font-size: .9rem;
    display: flex; align-items: center; gap: .45rem;
    transition: all .3s ease;
    background: rgba(255,255,255,.08);
    position: relative; overflow: hidden; white-space: nowrap;
}
.hdr-mlink::before {
    content: ''; position: absolute; inset: 0;
    background: rgba(255,255,255,.15);
    transform: translateX(-100%); transition: transform .3s ease;
}
.hdr-mlink:hover::before { transform: translateX(0); }
.hdr-mlink:hover { transform: translateY(-2px); }
.hdr-mlink.on { background: rgba(255,255,255,.22); box-shadow: 0 4px 16px rgba(255,255,255,.12); }
.hdr-nav.scrolled .hdr-mlink { color: var(--text-primary,#0f172a); background: transparent; }
.hdr-nav.scrolled .hdr-mlink:hover { background: var(--hover-bg,#f1f5f9); color: var(--accent-primary,#2563eb); }
.hdr-nav.scrolled .hdr-mlink.on { background: var(--accent-primary,#2563eb); color: #fff; }

/* ── دکمه سرچ ── */
.hdr-search-trigger {
    display: flex; align-items: center; gap: .6rem;
    padding: .65rem 1.1rem; border-radius: 14px;
    background: rgba(255,255,255,.1);
    border: 1.5px solid rgba(255,255,255,.18);
    color: rgba(255,255,255,.8);
    font-size: .87rem; font-weight: 500;
    cursor: pointer; transition: all .3s;
    font-family: inherit; white-space: nowrap;
    min-width: 190px;
}
.hdr-search-trigger:hover {
    background: rgba(255,255,255,.16);
    border-color: rgba(255,255,255,.38);
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(0,0,0,.2);
}
.hdr-search-trigger-icon {
    width: 30px; height: 30px; border-radius: 8px;
    background: linear-gradient(135deg, #1e3a8a, #3b82f6);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.hdr-search-trigger-icon svg { width:14px; height:14px; color:#fff; }
.hdr-search-trigger-text { flex: 1; text-align: right; }
.hdr-search-trigger-kbd {
    background: rgba(255,255,255,.12); padding: .15rem .45rem; border-radius: 5px;
    font-size: .68rem; font-family: monospace; color: rgba(255,255,255,.45);
    border: 1px solid rgba(255,255,255,.12); flex-shrink: 0;
}
.hdr-nav.scrolled .hdr-search-trigger {
    background: var(--bg-secondary,#f1f5f9); border-color: var(--border-color,#e2e8f0);
    color: var(--text-secondary,#64748b); box-shadow: none;
}
.hdr-nav.scrolled .hdr-search-trigger:hover {
    border-color: var(--accent-primary,#2563eb); color: var(--text-primary,#0f172a);
}
.hdr-nav.scrolled .hdr-search-trigger-kbd {
    background: var(--border-color,#e2e8f0); color: var(--text-secondary,#64748b); border-color: transparent;
}

/* ── Icons ── */
.hdr-icons { display: flex; align-items: center; gap: .6rem; flex-shrink: 0; }
.hdr-ibtn {
    width: 44px; height: 44px; border-radius: 50%;
    background: rgba(255,255,255,.12); border: none;
    color: #fff; font-size: 1.1rem;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; transition: all .3s ease;
    text-decoration: none; position: relative; flex-shrink: 0;
}
.hdr-ibtn:hover { transform: scale(1.1); background: rgba(255,255,255,.22); }
.hdr-nav.scrolled .hdr-ibtn { background: rgba(37,99,235,.1); color: var(--accent-primary,#2563eb); }
.hdr-cart-badge {
    position: absolute; top: -5px; left: -5px;
    background: linear-gradient(135deg,#f093fb,#f5576c);
    color: #fff; width:22px; height:22px; border-radius:50%;
    font-size:.65rem; display:flex; align-items:center; justify-content:center;
    font-weight:700; animation: hdrBounce 2s infinite;
}
@keyframes hdrBounce { 0%,100%{transform:scale(1)} 50%{transform:scale(1.15)} }

/* ── Auth / User ── */
.hdr-auth { display:flex; align-items:center; gap:.6rem; }
.hdr-login {
    display: inline-flex; align-items:center; gap:.45rem;
    padding: .55rem 1.2rem; border-radius:50px;
    color: #fff; border: 1.5px solid rgba(255,255,255,.45);
    background: transparent; font-weight:600; font-size:.88rem;
    text-decoration:none; transition:all .3s; white-space:nowrap;
}
.hdr-login:hover { background:rgba(255,255,255,.12); border-color:#fff; }
.hdr-login svg { width:17px; height:17px; }
.hdr-register {
    display: inline-flex; align-items:center; gap:.45rem;
    padding: .55rem 1.2rem; border-radius:50px;
    color: #1e3a8a; background: #fff; font-weight:700; font-size:.88rem;
    text-decoration:none; transition:all .3s; white-space:nowrap;
    box-shadow: 0 4px 15px rgba(255,255,255,.25);
}
.hdr-register:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(255,255,255,.35); }
.hdr-register svg { width:17px; height:17px; }
.hdr-nav.scrolled .hdr-login  { color:var(--text-primary,#0f172a); border-color:var(--border-color,#e2e8f0); }
.hdr-nav.scrolled .hdr-login:hover { background:var(--bg-secondary,#f1f5f9); }
.hdr-nav.scrolled .hdr-register { color:#fff; background:linear-gradient(135deg,#1e3a8a,#3b82f6); }

/* User dropdown */
.hdr-user { position:relative; }
.hdr-user-btn {
    display:inline-flex; align-items:center; gap:.55rem;
    padding:.45rem .85rem; border-radius:50px;
    background:rgba(255,255,255,.12); border:1.5px solid rgba(255,255,255,.3);
    color:#fff; font-weight:600; font-size:.88rem;
    cursor:pointer; transition:all .3s; font-family:inherit;
}
.hdr-user-btn:hover { background:rgba(255,255,255,.22); }
.hdr-user-btn svg { width:17px; height:17px; transition:transform .3s; }
.hdr-user-btn.open svg { transform:rotate(180deg); }
.hdr-uav {
    width:38px; height:38px; border-radius:50%;
    background:linear-gradient(135deg,#f59e0b,#ef4444);
    display:flex; align-items:center; justify-content:center;
    font-weight:800; font-size:.95rem; color:#fff; flex-shrink:0;
    overflow:hidden; position:relative;
    box-shadow: 0 0 0 2.5px rgba(255,255,255,.6), 0 0 0 5px rgba(255,255,255,.15);
    transition: box-shadow .3s;
}
.hdr-user-btn:hover .hdr-uav {
    box-shadow: 0 0 0 2.5px #fff, 0 0 0 5px rgba(96,165,250,.55);
}
.hdr-nav.scrolled .hdr-uav {
    box-shadow: 0 0 0 2.5px #2563eb, 0 0 0 5px rgba(37,99,235,.18);
}
.hdr-uav img {
    width:100%; height:100%;
    object-fit:cover; object-position:center top;
    border-radius:50%;
    position:absolute; inset:0;
}
.hdr-nav.scrolled .hdr-user-btn { color:var(--text-primary); background:var(--bg-secondary); border-color:var(--border-color); }
.hdr-udrop {
    position:absolute; top:calc(100% + 10px); left:0;
    background:var(--bg-primary,#fff); border-radius:18px;
    box-shadow:0 20px 60px rgba(0,0,0,.15);
    border:1px solid var(--border-color,rgba(0,0,0,.08));
    min-width:200px; overflow:hidden;
    opacity:0; visibility:hidden; transform:translateY(-10px);
    transition:all .3s cubic-bezier(.4,0,.2,1); z-index:9999;
}
.hdr-udrop.open { opacity:1; visibility:visible; transform:translateY(0); }
.hdr-ditem {
    display:flex; align-items:center; gap:.7rem;
    padding:.85rem 1.2rem; color:var(--text-primary,#0f172a);
    text-decoration:none; font-weight:500; font-size:.92rem;
    transition:background .2s;
}
.hdr-ditem:hover { background:var(--bg-secondary,#f8fafc); }
.hdr-ditem svg { width:19px; height:19px; color:var(--accent-primary,#1e3a8a); flex-shrink:0; }
.hdr-ditem.danger { color:#ef4444; }
.hdr-ditem.danger svg { color:#ef4444; }
.hdr-ditem.danger:hover { background:rgba(239,68,68,.08); }
.hdr-ddiv { height:1px; background:var(--border-color,rgba(0,0,0,.08)); margin:.3rem 0; }

/* ════════════════════════════════════════
   SEARCH MODAL
   ════════════════════════════════════════ */
.hdr-search-modal {
    position: fixed; inset: 0; z-index: 2000;
    display: flex; align-items: flex-start; justify-content: center;
    padding-top: 7vh;
    opacity: 0; pointer-events: none;
    transition: opacity .3s ease;
}
.hdr-search-modal.open { opacity: 1; pointer-events: all; }
.hdr-search-bg {
    position: absolute; inset: 0;
    background: rgba(2,6,23,.88); backdrop-filter: blur(16px);
}
.hdr-search-box-wrap {
    position: relative; z-index: 2;
    width: 100%; max-width: 680px; margin: 0 1.5rem;
    transform: translateY(-24px) scale(.96);
    transition: all .35s cubic-bezier(.4,0,.2,1);
}
.hdr-search-modal.open .hdr-search-box-wrap { transform: translateY(0) scale(1); }

.hdr-search-field {
    background: #fff; border-radius: 22px;
    box-shadow: 0 30px 80px rgba(0,0,0,.45), 0 0 0 1px rgba(255,255,255,.05);
    overflow: hidden;
}
body.dark-mode .hdr-search-field {
    background: #0f172a;
    box-shadow: 0 30px 80px rgba(0,0,0,.6), 0 0 0 1px rgba(255,255,255,.08);
}

/* ردیف ورودی */
.hdr-search-row {
    display: flex; align-items: center;
    padding: 1rem 1.4rem; gap: .85rem;
}
.hdr-search-row-icon {
    width: 40px; height: 40px; border-radius: 11px;
    background: linear-gradient(135deg, #1e3a8a, #3b82f6);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(37,99,235,.35);
}
.hdr-search-row-icon svg { width:19px; height:19px; color:#fff; }
.hdr-search-input {
    flex: 1; border: none; outline: none;
    font-family: inherit; font-size: 1.02rem;
    color: #0f172a; background: transparent; direction: rtl;
}
body.dark-mode .hdr-search-input { color: #e2e8f0; }
.hdr-search-input::placeholder { color: #94a3b8; }

.hdr-search-esc {
    display: flex; align-items: center; gap: .35rem;
    color: #94a3b8; font-size: .78rem; cursor: pointer;
    flex-shrink: 0; transition: color .2s; user-select: none;
}
.hdr-search-esc:hover { color: #475569; }
.hdr-search-esc kbd {
    background: #f1f5f9; border: 1px solid #e2e8f0;
    padding: .15rem .5rem; border-radius: 6px;
    font-family: monospace; font-size: .7rem; color: #475569;
    box-shadow: 0 1px 0 #e2e8f0;
}
body.dark-mode .hdr-search-esc kbd { background:#1e293b; border-color:#334155; color:#94a3b8; }

.hdr-search-divider {
    height: 1px;
    background: linear-gradient(90deg, transparent, #e2e8f0 20%, #e2e8f0 80%, transparent);
    margin: 0 1.4rem;
}
body.dark-mode .hdr-search-divider {
    background: linear-gradient(90deg, transparent, #1e293b 20%, #1e293b 80%, transparent);
}

/* اسپینر */
.hdr-spin {
    width: 20px; height: 20px; border-radius: 50%;
    border: 2.5px solid rgba(37,99,235,.15); border-top-color: #2563eb;
    animation: hdrSpin .65s linear infinite;
    display: none; flex-shrink: 0;
}
@keyframes hdrSpin { to{transform:rotate(360deg)} }

/* نتایج */
.hdr-search-results {
    padding: .6rem .75rem;
    max-height: 400px; overflow-y: auto;
}
.hdr-search-results::-webkit-scrollbar { width: 4px; }
.hdr-search-results::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }

/* حالت اولیه */
.hdr-search-hint {
    display: flex; flex-direction: column; align-items: center;
    padding: 2.2rem 1rem; color: #94a3b8; text-align: center;
}
.hdr-search-hint-icon { font-size: 2.8rem; margin-bottom: .8rem; animation: searchFloat 3s ease-in-out infinite; }
@keyframes searchFloat { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-7px)} }
.hdr-search-hint p { font-size: .88rem; line-height: 1.7; }
.hdr-search-hint strong { color: #475569; }

/* لیبل بخش */
.hdr-sr-section {
    display: flex; align-items: center; gap: .6rem;
    padding: .65rem .5rem .35rem;
    font-size: .68rem; font-weight: 800; letter-spacing: .16em;
    text-transform: uppercase; color: #94a3b8;
}
.hdr-sr-section::after { content:''; flex:1; height:1px; background:#f1f5f9; }
body.dark-mode .hdr-sr-section::after { background: #1e293b; }

/* آیتم */
.hdr-sr-item {
    display: flex; align-items: center; gap: .9rem;
    padding: .7rem .6rem; border-radius: 13px;
    text-decoration: none; color: #0f172a;
    transition: all .2s;
}
.hdr-sr-item:hover { background: #f0f6ff; transform: translateX(-2px); }
body.dark-mode .hdr-sr-item { color: #e2e8f0; }
body.dark-mode .hdr-sr-item:hover { background: #1e293b; }

.hdr-sr-img-wrap { position:relative; flex-shrink:0; width:48px; height:60px; }
.hdr-sr-img {
    width:100%; height:100%; object-fit:cover;
    border-radius:9px; border:1px solid #f1f5f9;
}
body.dark-mode .hdr-sr-img { border-color:#1e293b; }
.hdr-sr-info { flex:1; min-width:0; }
.hdr-sr-name {
    font-size:.93rem; font-weight:700; color:#0f172a;
    white-space:nowrap; overflow:hidden; text-overflow:ellipsis; line-height:1.3;
}
body.dark-mode .hdr-sr-name { color:#e2e8f0; }
.hdr-sr-meta { display:flex; align-items:center; gap:.45rem; margin-top:.3rem; flex-wrap:wrap; }
.hdr-sr-price { font-size:.83rem; color:#10b981; font-weight:800; }
.hdr-sr-old   { font-size:.74rem; color:#94a3b8; text-decoration:line-through; }
.hdr-sr-disc  {
    background:linear-gradient(135deg,#ef4444,#f97316);
    color:#fff; padding:.06rem .38rem; border-radius:5px;
    font-size:.68rem; font-weight:800;
}
.hdr-sr-author { font-size:.77rem; color:#64748b; }
.hdr-sr-arrow { color:#cbd5e1; font-size:.9rem; opacity:0; transition:all .2s; flex-shrink:0; }
.hdr-sr-item:hover .hdr-sr-arrow { opacity:1; transform:translateX(-3px); }

/* خالی */
.hdr-sr-empty {
    text-align:center; padding:2rem 1rem; color:#94a3b8; font-size:.9rem;
}
.hdr-sr-empty span { font-size:2.2rem; display:block; margin-bottom:.6rem; }

/* مشاهده همه */
.hdr-sr-all-wrap { padding:.6rem .75rem .9rem; }
.hdr-sr-all {
    display:flex; align-items:center; justify-content:center; gap:.5rem;
    padding:.85rem;
    border-radius:13px; border:1.5px solid #e2e8f0;
    background:#f8fafc; color:#2563eb;
    font-weight:700; font-size:.87rem;
    text-decoration:none; transition:all .25s;
}
.hdr-sr-all:hover {
    background:linear-gradient(135deg,#2563eb,#1d4ed8);
    border-color:transparent; color:#fff;
    box-shadow:0 6px 18px rgba(37,99,235,.3);
}
body.dark-mode .hdr-sr-all { background:#0f172a; border-color:#1e293b; }
.hdr-sr-all svg { width:15px; height:15px; }

/* دسته‌بندی سریع */
.hdr-search-cats-wrap { padding:.4rem 1.4rem 1.1rem; }
.hdr-search-cats-lbl {
    font-size:.66rem; letter-spacing:.16em; text-transform:uppercase;
    color:#94a3b8; font-weight:700; display:block; margin-bottom:.55rem;
}
.hdr-search-cats { display:flex; gap:.45rem; flex-wrap:wrap; }
.hdr-search-cat {
    padding:.38rem .95rem; border-radius:50px;
    background:#f1f5f9; border:1.5px solid #e2e8f0;
    color:#475569; font-size:.78rem; font-weight:600;
    cursor:pointer; transition:all .2s; text-decoration:none;
}
.hdr-search-cat:hover { background:#1e3a8a; border-color:#1e3a8a; color:#fff; transform:translateY(-1px); }
body.dark-mode .hdr-search-cat { background:#1e293b; border-color:#334155; color:#94a3b8; }

/* ── DRAWER OVERLAY ── */
.hdr-drawer-overlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,.55); backdrop-filter: blur(4px);
    z-index: 800; opacity: 0; pointer-events: none;
    transition: opacity .35s ease;
}
.hdr-drawer-overlay.open { opacity: 1; pointer-events: all; }

/* ── MOBILE DRAWER — از راست ── */
.hdr-drawer {
    position: fixed;
    top: 0; right: 0;
    width: 285px; height: 100vh;
    background: linear-gradient(180deg, #0f172a 0%, #1e3a8a 100%);
    z-index: 900;
    transform: translateX(100%);
    transition: transform .38s cubic-bezier(.4,0,.2,1);
    display: flex; flex-direction: column; overflow-y: auto;
}
.hdr-drawer.open { transform: translateX(0); }

.hdr-drawer-top {
    display: flex; align-items: center; justify-content: space-between;
    padding: 1.6rem 1.5rem 1rem;
    border-bottom: 1px solid rgba(255,255,255,.1);
}
.hdr-drawer-logo {
    display: flex; align-items: center; gap: .6rem;
    color: #fff; font-size: 1.2rem; font-weight: 800; text-decoration: none;
}
.hdr-drawer-close {
    width: 38px; height: 38px; border-radius: 10px;
    background: rgba(255,255,255,.1); border: none;
    color: #fff; font-size: 1.3rem; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: background .3s;
}
.hdr-drawer-close:hover { background: rgba(255,255,255,.2); }

.hdr-drawer-search { padding: 1rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,.08); }
.hdr-drawer-search-box {
    display: flex; align-items: center; gap: .6rem;
    background: rgba(255,255,255,.1); border: 1.5px solid rgba(255,255,255,.18);
    border-radius: 12px; padding: .7rem 1rem;
}
.hdr-drawer-search-box svg { width:17px; height:17px; color:rgba(255,255,255,.5); flex-shrink:0; }
.hdr-drawer-sinput {
    background: none; border: none; outline: none;
    color: #fff; font-family: inherit; font-size: .9rem; width: 100%;
}
.hdr-drawer-sinput::placeholder { color: rgba(255,255,255,.4); }

.hdr-drawer-nav { padding: 1rem 1.2rem; flex: 1; display: flex; flex-direction: column; gap: .4rem; }
.hdr-drawer-link {
    display: flex; align-items: center; gap: .8rem;
    padding: .95rem 1.1rem; border-radius: 14px;
    color: rgba(255,255,255,.85); text-decoration: none;
    font-weight: 600; font-size: .97rem;
    transition: all .3s; background: transparent;
}
.hdr-drawer-link:hover { background: rgba(255,255,255,.1); color: #fff; transform: translateX(4px); }
.hdr-drawer-link.on { background: rgba(255,255,255,.18); color: #fff; box-shadow: 0 4px 16px rgba(0,0,0,.2); }
.hdr-drawer-link-icon {
    width: 36px; height: 36px; border-radius: 10px;
    background: rgba(255,255,255,.1);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; flex-shrink: 0; transition: background .3s;
}
.hdr-drawer-link.on .hdr-drawer-link-icon { background: var(--accent-primary,#2563eb); }
.hdr-drawer-link:hover .hdr-drawer-link-icon { background: rgba(255,255,255,.18); }
.hdr-drawer-link-arrow { margin-right: auto; opacity: .5; font-size: .8rem; }
.hdr-drawer-link.on .hdr-drawer-link-arrow { opacity: 1; }
.hdr-drawer-sep {
    font-size: .68rem; letter-spacing: .18em; text-transform: uppercase;
    color: rgba(255,255,255,.3); padding: .6rem 1.2rem .3rem; font-weight: 700;
}
.hdr-drawer-auth {
    padding: 1rem 1.2rem 2rem; border-top: 1px solid rgba(255,255,255,.1);
    display: flex; flex-direction: column; gap: .7rem;
}
.hdr-drawer-auth-btn {
    display: flex; align-items: center; justify-content: center; gap: .5rem;
    padding: .85rem; border-radius: 14px;
    font-weight: 700; font-size: .9rem;
    text-decoration: none; transition: all .3s; font-family: inherit; border: none; cursor: pointer;
}
.hdr-drawer-auth-btn.login { background: rgba(255,255,255,.1); border: 1.5px solid rgba(255,255,255,.25); color: #fff; }
.hdr-drawer-auth-btn.login:hover { background: rgba(255,255,255,.18); }
.hdr-drawer-auth-btn.register { background: linear-gradient(135deg,#2563eb,#1d4ed8); color: #fff; box-shadow: 0 6px 20px rgba(37,99,235,.4); }
.hdr-drawer-auth-btn.register:hover { transform: translateY(-2px); }
.hdr-drawer-auth-btn svg { width:18px; height:18px; }

/* ── HERO ── */
.hdr-hero {
    position: relative; min-height: 100vh;
    display: flex; align-items: center;
    z-index: 10; padding: 8rem 0 5rem;
}
.hdr-hero-grid {
    max-width: 1400px; margin: 0 auto; padding: 0 2rem;
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 4rem; align-items: center;
}
.hdr-hero-text { animation: hdrSlideR 1s ease; }
@keyframes hdrSlideR { from{opacity:0;transform:translateX(40px)} to{opacity:1;transform:translateX(0)} }
.hdr-hero-badge {
    display: inline-flex; align-items: center; gap: .5rem;
    background: rgba(255,255,255,.18); backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,.25);
    padding: .65rem 1.4rem; border-radius: 50px;
    color: #fff; font-weight: 600; font-size: .88rem; margin-bottom: 1.8rem;
}
.hdr-hero-title {
    font-size: 4rem; font-weight: 900; color: #fff;
    line-height: 1.15; margin-bottom: 1.4rem; letter-spacing: -.02em;
}
.hdr-hero-hl { display: inline-block; position: relative; }
.hdr-hero-hl::after {
    content: ''; position: absolute; bottom: 8px; right: 0; left: 0;
    height: 14px; background: rgba(255,255,255,.22); z-index: -1; border-radius: 6px;
}
.hdr-hero-desc { font-size: 1.15rem; color: rgba(255,255,255,.82); margin-bottom: 2.4rem; line-height: 1.85; }
.hdr-hero-desc strong { color: #fff; }
.hdr-hero-btns { display: flex; gap: 1.2rem; flex-wrap: wrap; }
.hdr-hbtn {
    padding: .95rem 2.2rem; border-radius: 50px;
    font-weight: 700; font-size: .95rem;
    text-decoration: none; display: inline-flex; align-items: center; gap: .6rem;
    transition: all .3s ease; font-family: inherit; border: none; cursor: pointer;
}
.hdr-hbtn-p { background: #fff; color: #1e3a8a; box-shadow: 0 8px 28px rgba(255,255,255,.25); }
.hdr-hbtn-p:hover { transform: translateY(-4px); box-shadow: 0 14px 36px rgba(255,255,255,.35); }
.hdr-hbtn-s { background: rgba(255,255,255,.12); color: #fff; border: 2px solid rgba(255,255,255,.45); }
.hdr-hbtn-s:hover { background: #fff; color: #1e3a8a; }

/* Slider */
.hdr-slider-w { position: relative; animation: hdrSlideL 1s ease; }
@keyframes hdrSlideL { from{opacity:0;transform:translateX(-40px)} to{opacity:1;transform:translateX(0)} }
.hdr-slider { position: relative; width: 100%; max-width: 480px; aspect-ratio: 4/5; margin: 0 auto; }
.hdr-slide {
    position: absolute; inset: 0; opacity: 0; transition: opacity .9s ease;
    border-radius: 28px; overflow: hidden;
    box-shadow: 0 32px 80px rgba(0,0,0,.35); border: 1px solid rgba(255,255,255,.12);
}
.hdr-slide.on { opacity: 1; }
.hdr-slide img { width: 100%; height: 100%; object-fit: cover; }
.hdr-slider-dots {
    position: absolute; bottom: -52px; left: 50%; transform: translateX(-50%);
    display: flex; gap: .7rem;
}
.hdr-dot {
    width: 10px; height: 10px; border-radius: 50%;
    background: rgba(255,255,255,.25); border: none; cursor: pointer; transition: all .3s ease;
}
.hdr-dot.on { width: 36px; border-radius: 8px; background: #fff; }
.hdr-scroll-cue {
    position: absolute; bottom: 2rem; left: 50%; transform: translateX(-50%);
    z-index: 10; display: flex; flex-direction: column; align-items: center; gap: .4rem;
    color: rgba(255,255,255,.3); font-size: .68rem; letter-spacing: .16em; text-transform: uppercase;
}
.hdr-scroll-line {
    width: 1px; height: 40px;
    background: linear-gradient(to bottom, rgba(255,255,255,.3), transparent);
    animation: hdrScroll 2s ease-in-out infinite;
}
@keyframes hdrScroll { 0%,100%{opacity:.3} 50%{opacity:1} }

/* ── RESPONSIVE ── */
@media (max-width:1100px) {
    .hdr-menu, .hdr-search-trigger { display: none; }
    .hdr-ham { display: flex; }
}
@media (max-width:768px) {
    .hdr-nav-c { padding: 0 1.25rem; gap: 1rem; }
    .hdr-hero-grid { grid-template-columns: 1fr; text-align: center; gap: 2rem; padding: 0 1.25rem; }
    .hdr-hero-title { font-size: 2.6rem; }
    .hdr-hero-btns { justify-content: center; }
    .hdr-hero { padding: 7rem 0 5rem; min-height: auto; }
    .hdr-slider { max-width: 320px; }
    .hdr-login span, .hdr-register span { display: none; }
    .hdr-login, .hdr-register { width: 42px; height: 42px; border-radius: 50%; padding: 0; justify-content: center; }
    .hdr-user-name { display: none; }
}
@media (max-width:576px) {
    .hdr-nav-c { padding: 0 1rem; }
    .hdr-logo-text { display: none; }
    .hdr-hero-title { font-size: 2.2rem; }
    .hdr-search-box-wrap { margin: 0 1rem; }
}
</style>
</head>
<body>

<!-- ═══════ SEARCH MODAL ═══════ -->
<div class="hdr-search-modal" id="searchModal">
    <div class="hdr-search-bg" id="searchBg"></div>
    <div class="hdr-search-box-wrap">
        <div class="hdr-search-field">
            <div class="hdr-search-row">
                <div class="hdr-search-row-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                </div>
                <input class="hdr-search-input" type="text" id="modalSearchInput"
                       placeholder="نام کتاب، نویسنده یا موضوع..." autocomplete="off">
                <div class="hdr-spin" id="searchSpinner"></div>
                <div class="hdr-search-esc" id="searchClose">
                    <kbd>ESC</kbd> بستن
                </div>
            </div>
            <div class="hdr-search-divider"></div>
            <div class="hdr-search-results" id="searchResults">
                <div class="hdr-search-hint">
                    <div class="hdr-search-hint-icon">📚</div>
                    <p>نام <strong>کتاب</strong> یا <strong>نویسنده</strong> را وارد کنید<br>تا نتایج بلادرنگ نمایش داده شود</p>
                </div>
            </div>
            <div id="searchCatsWrap" class="hdr-search-cats-wrap">
                <span class="hdr-search-cats-lbl">دسته‌بندی سریع</span>
                <div class="hdr-search-cats">
                    <?php foreach (array_slice($categories, 0, 6) as $cat): ?>
                        <a href="products.php?category=<?= $cat['id'] ?>" class="hdr-search-cat">
                            <?= escape($cat['title']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ═══════ MOBILE DRAWER OVERLAY ═══════ -->
<div class="hdr-drawer-overlay" id="drawerOverlay"></div>

<!-- ═══════ MOBILE DRAWER ═══════ -->
<div class="hdr-drawer" id="hdrDrawer">
    <div class="hdr-drawer-top">
        <a href="index.php" class="hdr-drawer-logo"><span>📚</span><span><?= escape(SITE_NAME) ?></span></a>
        <button class="hdr-drawer-close" id="drawerClose">✕</button>
    </div>
    <div class="hdr-drawer-search">
        <div class="hdr-drawer-search-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input class="hdr-drawer-sinput" type="text" placeholder="جستجوی کتاب..." id="drawerSearchInput">
        </div>
    </div>
    <nav class="hdr-drawer-nav">
        <div class="hdr-drawer-sep">منوی اصلی</div>
        <a href="index.php"    class="hdr-drawer-link <?= $current_file==='index'    ?'on':'' ?>"><span class="hdr-drawer-link-icon">🏠</span>صفحه اصلی<span class="hdr-drawer-link-arrow">‹</span></a>
        <a href="products.php" class="hdr-drawer-link <?= $current_file==='products' ?'on':'' ?>"><span class="hdr-drawer-link-icon">📦</span>محصولات<span class="hdr-drawer-link-arrow">‹</span></a>
        <a href="posts.php"    class="hdr-drawer-link <?= $current_file==='posts'    ?'on':'' ?>"><span class="hdr-drawer-link-icon">📝</span>مقالات<span class="hdr-drawer-link-arrow">‹</span></a>
        <a href="about.php"    class="hdr-drawer-link <?= $current_file==='about'    ?'on':'' ?>"><span class="hdr-drawer-link-icon">ℹ️</span>درباره ما<span class="hdr-drawer-link-arrow">‹</span></a>
        <?php if ($logged_in): ?>
        <div class="hdr-drawer-sep" style="margin-top:.5rem">حساب کاربری</div>
        <a href="profile.php" class="hdr-drawer-link"><span class="hdr-drawer-link-icon">👤</span>پروفایل من<span class="hdr-drawer-link-arrow">‹</span></a>
        <a href="orders.php"  class="hdr-drawer-link"><span class="hdr-drawer-link-icon">🛒</span>سفارش‌های من<span class="hdr-drawer-link-arrow">‹</span></a>
        <a href="logout.php"  class="hdr-drawer-link" style="color:rgba(239,68,68,.9)"><span class="hdr-drawer-link-icon">🚪</span>خروج<span class="hdr-drawer-link-arrow">‹</span></a>
        <?php endif; ?>
    </nav>
    <?php if (!$logged_in): ?>
    <div class="hdr-drawer-auth">
        <a href="login.php" class="hdr-drawer-auth-btn login">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M10,17V14H3V10H10V7L15,12L10,17M10,2H19A2,2 0 0,1 21,4V20A2,2 0 0,1 19,22H10A2,2 0 0,1 8,20V18H10V20H19V4H10V6H8V4A2,2 0 0,1 10,2Z"/></svg>
            ورود به حساب
        </a>
        <a href="register.php" class="hdr-drawer-auth-btn register">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M15,14C12.33,14 7,15.33 7,18V20H23V18C23,15.33 17.67,14 15,14M6,10V7H4V10H1V12H4V15H6V12H9V10M15,12A4,4 0 0,0 19,8A4,4 0 0,0 15,4A4,4 0 0,0 11,8A4,4 0 0,0 15,12Z"/></svg>
            ثبت‌نام رایگان
        </a>
    </div>
    <?php endif; ?>
</div>

<!-- ═══════ WRAPPER ═══════ -->
<div class="hdr-wrapper">
    <div class="hdr-dots"></div>
    <div class="hdr-glow"></div>
    <div class="hdr-glow2"></div>
    <div class="hdr-circles"><span></span><span></span><span></span></div>

    <nav class="hdr-nav" id="hdrNav">
        <div class="hdr-nav-c">

            <!-- ① همبرگر — order:-1 = سمت راست در RTL -->
            <button class="hdr-ham" id="hdrHam" aria-label="منو">
                <span class="hdr-ham-line"></span>
                <span class="hdr-ham-line"></span>
                <span class="hdr-ham-line"></span>
            </button>

            <!-- ② Logo -->
            <a href="index.php" class="hdr-logo">
                <span class="hdr-logo-icon">📚</span>
                <span class="hdr-logo-text"><?= escape(SITE_NAME) ?></span>
            </a>

            <!-- ③ Desktop Menu -->
            <ul class="hdr-menu">
                <li><a href="index.php"    class="hdr-mlink <?= $current_file==='index'    ?'on':'' ?>"><span>🏠</span>صفحه اصلی</a></li>
                <li><a href="products.php" class="hdr-mlink <?= $current_file==='products' ?'on':'' ?>"><span>📦</span>محصولات</a></li>
                <li><a href="posts.php"    class="hdr-mlink <?= $current_file==='posts'    ?'on':'' ?>"><span>📝</span>مقالات</a></li>
                <li><a href="about.php"    class="hdr-mlink <?= $current_file==='about'    ?'on':'' ?>"><span>ℹ️</span>درباره ما</a></li>
            </ul>

            <!-- ④ Search Trigger -->
            <button class="hdr-search-trigger" id="searchTrigger">
                <span class="hdr-search-trigger-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                </span>
                <span class="hdr-search-trigger-text">جستجو در کتاب‌ها...</span>
                <span class="hdr-search-trigger-kbd">⌘K</span>
            </button>

            <!-- ⑤ Icons -->
            <div class="hdr-icons">
                <button class="hdr-ibtn" id="darkToggle" aria-label="تغییر تم"><span id="darkIcon">🌙</span></button>
                <a href="cart.php" class="hdr-ibtn" aria-label="سبد خرید">
                    🛒
                    <?php if ($total_cart_count > 0): ?>
                        <span class="hdr-cart-badge"><?= $total_cart_count ?></span>
                    <?php endif; ?>
                </a>
                <?php if ($logged_in): ?>
                    <div class="hdr-user">
                        <button class="hdr-user-btn" id="userMenuBtn">
                            <span class="hdr-uav">
                                <?php if ($user_real_avatar): ?>
                                    <img src="<?= escape($user_real_avatar) ?>?v=<?= time() ?>" alt="avatar">
                                <?php else: ?>
                                    <?= $user_avatar_letter ?>
                                <?php endif; ?>
                            </span>
                            <span class="hdr-user-name"><?= escape($user_name) ?></span>
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M7,10L12,15L17,10H7Z"/></svg>
                        </button>
                        <div class="hdr-udrop" id="userDrop">
                            <a href="profile.php" class="hdr-ditem"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/></svg>پروفایل</a>
                            <a href="orders.php"  class="hdr-ditem"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M17,18A2,2 0 0,1 19,20A2,2 0 0,1 17,22C15.89,22 15,21.1 15,20C15,18.89 15.89,18 17,18M1,2H4.27L5.21,4H20A1,1 0 0,1 21,5L17.3,11.97C16.96,12.58 16.3,13 15.55,13H8.1L7.2,14.63L7.17,14.75A0.25,0.25 0 0,0 7.42,15H19V17H7C5.89,17 5,16.1 5,15L6.6,11.59L3,4H1V2Z"/></svg>سفارش‌ها</a>
                            <div class="hdr-ddiv"></div>
                            <a href="logout.php" class="hdr-ditem danger"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M16,17V14H9V10H16V7L21,12L16,17M14,2A2,2 0 0,1 16,4V6H14V4H5V20H14V18H16V20A2,2 0 0,1 14,22H5A2,2 0 0,1 3,20V4A2,2 0 0,1 5,2H14Z"/></svg>خروج</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="hdr-auth">
                        <a href="login.php" class="hdr-login"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M10,17V14H3V10H10V7L15,12L10,17M10,2H19A2,2 0 0,1 21,4V20A2,2 0 0,1 19,22H10A2,2 0 0,1 8,20V18H10V20H19V4H10V6H8V4A2,2 0 0,1 10,2Z"/></svg><span>ورود</span></a>
                        <a href="register.php" class="hdr-register"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M15,14C12.33,14 7,15.33 7,18V20H23V18C23,15.33 17.67,14 15,14M6,10V7H4V10H1V12H4V15H6V12H9V10M15,12A4,4 0 0,0 19,8A4,4 0 0,0 15,4A4,4 0 0,0 11,8A4,4 0 0,0 15,12Z"/></svg><span>ثبت‌نام</span></a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <div class="hdr-hero">
        <div class="hdr-hero-grid">
            <div class="hdr-hero-text">
                <div class="hdr-hero-badge">✨ تخفیف ویژه تا ۵۰٪</div>
                <h1 class="hdr-hero-title">دنیای <span class="hdr-hero-hl">کتاب</span><br>در یک کلیک</h1>
                <p class="hdr-hero-desc">بیش از <strong>۱۰,۰۰۰</strong> عنوان کتاب در دسته‌بندی‌های مختلف با بهترین کیفیت و قیمت مناسب</p>
                <div class="hdr-hero-btns">
                    <a href="products.php" class="hdr-hbtn hdr-hbtn-p">🚀 مشاهده محصولات</a>
                    <a href="posts.php"    class="hdr-hbtn hdr-hbtn-s">📖 مقالات آموزشی</a>
                </div>
            </div>
            <div class="hdr-slider-w">
                <div class="hdr-slider">
                    <?php foreach ($posts_slider as $i => $slide): ?>
                        <div class="hdr-slide <?= $i===0?'on':'' ?>">
                            <img src="./img1/<?= escape($slide['img']) ?>" alt="کتاب <?= $i+1 ?>" loading="<?= $i===0?'eager':'lazy' ?>">
                        </div>
                    <?php endforeach; ?>
                    <div class="hdr-slider-dots" id="sliderDots">
                        <?php foreach ($posts_slider as $i => $slide): ?>
                            <button class="hdr-dot <?= $i===0?'on':'' ?>" data-i="<?= $i ?>"></button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="hdr-scroll-cue"><span>اسکرول</span><div class="hdr-scroll-line"></div></div>
</div>

<script>
(function () {

    /* ── Slider ── */
    var slides = document.querySelectorAll('.hdr-slide');
    var dots   = document.querySelectorAll('.hdr-dot');
    var cur    = 0;
    function goSlide(n) {
        slides.forEach(function(s){ s.classList.remove('on'); });
        dots.forEach(function(d){ d.classList.remove('on'); });
        cur = (n + slides.length) % slides.length;
        slides[cur].classList.add('on');
        dots[cur].classList.add('on');
    }
    dots.forEach(function(d,i){ d.addEventListener('click', function(){ goSlide(i); }); });
    if (slides.length > 1) setInterval(function(){ goSlide(cur+1); }, 5000);

    /* ── Sticky Navbar ── */
    var nav = document.getElementById('hdrNav');
    window.addEventListener('scroll', function(){ nav.classList.toggle('scrolled', window.scrollY > 80); }, {passive:true});

    /* ── Drawer ── */
    var ham = document.getElementById('hdrHam');
    var drawer  = document.getElementById('hdrDrawer');
    var overlay = document.getElementById('drawerOverlay');
    var dClose  = document.getElementById('drawerClose');
    function openDrawer()  { ham.classList.add('open'); drawer.classList.add('open'); overlay.classList.add('open'); document.body.style.overflow='hidden'; }
    function closeDrawer() { ham.classList.remove('open'); drawer.classList.remove('open'); overlay.classList.remove('open'); document.body.style.overflow=''; }
    ham.addEventListener('click', function(){ drawer.classList.contains('open') ? closeDrawer() : openDrawer(); });
    overlay.addEventListener('click', closeDrawer);
    dClose.addEventListener('click', closeDrawer);
    var sw = 0;
    drawer.addEventListener('touchstart', function(e){ sw = e.touches[0].clientX; }, {passive:true});
    drawer.addEventListener('touchmove',  function(e){ if(e.touches[0].clientX - sw > 70) closeDrawer(); }, {passive:true});
    document.getElementById('drawerSearchInput').addEventListener('keydown', function(e){
        if (e.key==='Enter' && this.value.trim()) window.location=SITE_BASE+'search.php?q='+encodeURIComponent(this.value.trim());
    });

    /* ── Search Modal ── */
    var modal     = document.getElementById('searchModal');
    var searchBg  = document.getElementById('searchBg');
    var searchIn  = document.getElementById('modalSearchInput');
    var searchRes = document.getElementById('searchResults');
    var catsWrap  = document.getElementById('searchCatsWrap');
    var spinner   = document.getElementById('searchSpinner');
    var sClose    = document.getElementById('searchClose');
    var sTrigger  = document.getElementById('searchTrigger');

    var HINT = '<div class="hdr-search-hint"><div class="hdr-search-hint-icon">📚</div>'
             + '<p>نام <strong>کتاب</strong> یا <strong>نویسنده</strong> را وارد کنید<br>تا نتایج بلادرنگ نمایش داده شود</p></div>';

    function openSearch() {
        modal.classList.add('open');
        document.body.style.overflow = 'hidden';
        setTimeout(function(){ searchIn.focus(); }, 120);
    }
    function closeSearch() {
        modal.classList.remove('open');
        document.body.style.overflow = '';
        searchIn.value = '';
        searchRes.innerHTML = HINT;
        catsWrap.style.display = '';
        spinner.style.display = 'none';
    }
    window.closeSearch = closeSearch;

    sTrigger && sTrigger.addEventListener('click', openSearch);
    searchBg.addEventListener('click', closeSearch);
    sClose.addEventListener('click', closeSearch);
    document.addEventListener('keydown', function(e){
        if ((e.metaKey||e.ctrlKey) && e.key==='k') { e.preventDefault(); openSearch(); }
        if (e.key==='Escape') { closeSearch(); closeDrawer(); }
    });

    /* ── مسیر سایت — مطمئن‌ترین روش ── */
    var SITE_BASE = (function(){
        var d = '<?= rtrim(str_replace("\\\\" , "/", dirname($_SERVER["SCRIPT_NAME"])), "/") ?>';
        return window.location.protocol + '//' + window.location.host + d + '/';
    })();

    /* ── Live Search ── */
    var timer;
    searchIn.addEventListener('input', function(){
        clearTimeout(timer);
        var q = this.value.trim();
        if (!q) { searchRes.innerHTML=HINT; catsWrap.style.display=''; spinner.style.display='none'; return; }
        catsWrap.style.display = 'none';
        spinner.style.display  = 'block';
        searchRes.innerHTML    = '';

        timer = setTimeout(function(){
            fetch(SITE_BASE + 'search_live.php?q=' + encodeURIComponent(q) + '&_=' + Date.now())
                .then(function(r){
                    if (!r.ok) throw new Error('HTTP ' + r.status);
                    return r.json();
                })
                .then(function(data){
                    spinner.style.display = 'none';
                    renderResults(data, q);
                })
                .catch(function(err){
                    console.warn('Live search error:', err);
                    spinner.style.display = 'none';
                    /* fallback: لینک مستقیم به صفحه سرچ */
                    searchRes.innerHTML =
                        '<div class="hdr-sr-all-wrap">'
                        + '<a href="'+SITE_BASE+'search.php?q='+encodeURIComponent(q)+'" class="hdr-sr-all">'
                        + '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z"/></svg>'
                        + 'مشاهده نتایج برای «'+esc(q)+'»'
                        + '</a></div>';
                });
        }, 300);
    });

    function renderResults(data, q) {
        if (!data || (!data.products.length && !data.posts.length)) {
            searchRes.innerHTML = '<div class="hdr-sr-empty"><span>🔍</span>نتیجه‌ای برای «<strong>'+esc(q)+'</strong>» یافت نشد</div>';
            /* دکمه مشاهده همه حتی وقتی نتیجه نیست */
            var allDiv = document.createElement('div');
            allDiv.className = 'hdr-sr-all-wrap';
            allDiv.innerHTML = buildAllBtn(q);
            searchRes.appendChild(allDiv);
            return;
        }
        var html = '';

        /* محصولات — حداکثر 3 تا */
        if (data.products && data.products.length) {
            html += '<div class="hdr-sr-section">📦 محصولات</div>';
            data.products.slice(0, 3).forEach(function(p){
                var disc = (p.old_price > 0 && p.old_price != p.price)
                    ? '<span class="hdr-sr-disc">'+Math.round((p.old_price-p.price)/p.old_price*100)+'%</span>' : '';
                var old = (p.old_price != p.price)
                    ? '<span class="hdr-sr-old">'+fmt(p.old_price)+'</span>' : '';
                html += '<a href="'+SITE_BASE+'single_product.php?product='+p.id+'" class="hdr-sr-item" onclick="closeSearch()">'
                      + '<div class="hdr-sr-img-wrap"><img class="hdr-sr-img" src="'+SITE_BASE+'upload/products/'+esc(p.pic)+'" alt="'+esc(p.name)+'" loading="lazy"></div>'
                      + '<div class="hdr-sr-info">'
                      +   '<div class="hdr-sr-name">'+highlight(p.name,q)+'</div>'
                      +   '<div class="hdr-sr-meta"><span class="hdr-sr-price">'+fmt(p.price)+'</span>'+old+disc+'</div>'
                      + '</div>'
                      + '<span class="hdr-sr-arrow">←</span>'
                      + '</a>';
            });
        }

        /* مقالات — حداکثر 3 تا */
        if (data.posts && data.posts.length) {
            html += '<div class="hdr-sr-section" style="margin-top:.3rem">📝 مقالات</div>';
            data.posts.slice(0, 3).forEach(function(p){
                html += '<a href="'+SITE_BASE+'single.php?post='+p.id+'" class="hdr-sr-item" onclick="closeSearch()">'
                      + '<div class="hdr-sr-img-wrap"><img class="hdr-sr-img" src="'+SITE_BASE+'upload/posts/'+esc(p.image)+'" alt="'+esc(p.title)+'" loading="lazy"></div>'
                      + '<div class="hdr-sr-info">'
                      +   '<div class="hdr-sr-name">'+highlight(p.title,q)+'</div>'
                      +   '<div class="hdr-sr-meta"><span class="hdr-sr-author">✍️ '+esc(p.author)+'</span></div>'
                      + '</div>'
                      + '<span class="hdr-sr-arrow">←</span>'
                      + '</a>';
            });
        }

        searchRes.innerHTML = html;

        /* دکمه مشاهده همه — میره به search.php */
        var allDiv = document.createElement('div');
        allDiv.className = 'hdr-sr-all-wrap';
        allDiv.innerHTML = buildAllBtn(q);
        searchRes.appendChild(allDiv);
    }

    function buildAllBtn(q) {
        return '<a href="'+SITE_BASE+'search.php?q='+encodeURIComponent(q)+'" class="hdr-sr-all" onclick="closeSearch()">'
            + '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z"/></svg>'
            + 'مشاهده همه نتایج برای «'+esc(q)+'»'
            + '</a>';
    }

    function highlight(text, q) {
        if (!q) return esc(text);
        var re = new RegExp('('+q.replace(/[.*+?^${}()|[\]\\]/g,'\\$&')+')', 'gi');
        return esc(text).replace(re, '<mark style="background:#fde68a;color:#92400e;padding:1px 3px;border-radius:4px;font-weight:700">$1</mark>');
    }
    function esc(s) { var d=document.createElement('div'); d.textContent=s||''; return d.innerHTML; }
    function fmt(n) { return Number(n).toLocaleString('fa-IR')+' تومان'; }

    searchIn.addEventListener('keydown', function(e){
        if (e.key==='Enter' && this.value.trim())
            window.location = SITE_BASE + 'search.php?q='+encodeURIComponent(this.value.trim());
    });

    /* ── User Dropdown ── */
    var uBtn=document.getElementById('userMenuBtn'), uDrop=document.getElementById('userDrop');
    if (uBtn) {
        uBtn.addEventListener('click', function(e){ e.stopPropagation(); uBtn.classList.toggle('open'); uDrop.classList.toggle('open'); });
        document.addEventListener('click', function(){ if(uBtn){uBtn.classList.remove('open');uDrop.classList.remove('open');} });
    }

    /* ── Dark Mode ── */
    var darkBtn=document.getElementById('darkToggle'), darkIcon=document.getElementById('darkIcon');
    if (localStorage.getItem('darkMode')==='enabled') { document.body.classList.add('dark-mode'); darkIcon.textContent='☀️'; }
    darkBtn.addEventListener('click', function(){
        var d=document.body.classList.toggle('dark-mode');
        localStorage.setItem('darkMode', d?'enabled':'disabled');
        darkIcon.textContent = d?'☀️':'🌙';
    });

})();
</script>