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
/* ════════════════════════════════════════════════════
   HEADER — Midnight Blue — v3
   ════════════════════════════════════════════════════ */

/* ── WRAPPER ── */
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

/* noise dots bg */
.hdr-dots {
    position: absolute; inset: 0;
    background-image: radial-gradient(circle, rgba(255,255,255,.05) 1px, transparent 1px);
    background-size: 28px 28px;
    pointer-events: none; z-index: 1;
}
/* radial glow top-right */
.hdr-glow {
    position: absolute;
    width: 800px; height: 800px; border-radius: 50%;
    background: radial-gradient(circle, rgba(96,165,250,.16) 0%, transparent 65%);
    top: -250px; right: -200px;
    pointer-events: none; z-index: 1;
}
/* radial glow bottom-left */
.hdr-glow2 {
    position: absolute;
    width: 600px; height: 600px; border-radius: 50%;
    background: radial-gradient(circle, rgba(167,139,250,.1) 0%, transparent 65%);
    bottom: -150px; left: -100px;
    pointer-events: none; z-index: 1;
}

/* floating circles */
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
    z-index: 500;
    padding: 1.4rem 0;
    transition: all .3s ease;
}
.hdr-nav.scrolled {
    position: fixed;
    background: rgba(255,255,255,.96);
    backdrop-filter: blur(20px);
    padding: .9rem 0;
    box-shadow: 0 4px 24px rgba(0,0,0,.1);
}
body.dark-mode .hdr-nav.scrolled {
    background: rgba(15,23,42,.96);
}

.hdr-nav-c {
    max-width: 1400px; margin: 0 auto; padding: 0 2rem;
    display: flex; align-items: center; gap: 1.5rem;
}

/* Logo */
.hdr-logo {
    display: flex; align-items: center; gap: .7rem;
    text-decoration: none; color: #fff;
    font-size: 1.4rem; font-weight: 800;
    flex-shrink: 0; white-space: nowrap;
}
.hdr-logo-icon { font-size: 1.9rem; animation: hdrLogoFloat 3s ease-in-out infinite; }
@keyframes hdrLogoFloat { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-7px)} }
.hdr-nav.scrolled .hdr-logo { color: var(--text-primary); }

/* Desktop menu */
.hdr-menu {
    display: flex; align-items: center; gap: .4rem;
    list-style: none; flex: 1;
}
.hdr-mlink {
    color: rgba(255,255,255,.9);
    text-decoration: none;
    padding: .6rem 1.2rem; border-radius: 50px;
    font-weight: 600; font-size: .9rem;
    display: flex; align-items: center; gap: .45rem;
    transition: all .3s ease;
    background: rgba(255,255,255,.08);
    position: relative; overflow: hidden;
    white-space: nowrap;
}
.hdr-mlink::before {
    content: ''; position: absolute; inset: 0;
    background: rgba(255,255,255,.15);
    transform: translateX(-100%); transition: transform .3s ease;
}
.hdr-mlink:hover::before { transform: translateX(0); }
.hdr-mlink:hover { transform: translateY(-2px); }
.hdr-mlink.on {
    background: rgba(255,255,255,.22);
    box-shadow: 0 4px 16px rgba(255,255,255,.12);
}
.hdr-nav.scrolled .hdr-mlink { color: var(--text-primary); background: transparent; }
.hdr-nav.scrolled .hdr-mlink:hover { background: var(--hover-bg,#f1f5f9); color: var(--accent-primary); }
.hdr-nav.scrolled .hdr-mlink.on { background: var(--accent-primary,#2563eb); color: #fff; }

/* Right icons */
.hdr-icons {
    display: flex; align-items: center; gap: .6rem;
    flex-shrink: 0;
}
.hdr-ibtn {
    width: 44px; height: 44px; border-radius: 50%;
    background: rgba(255,255,255,.12); border: none;
    color: #fff; font-size: 1.1rem;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; transition: all .3s ease;
    text-decoration: none; position: relative;
    flex-shrink: 0;
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

/* Search trigger btn */
.hdr-search-trigger {
    display: flex; align-items: center; gap: .55rem;
    padding: .55rem 1.3rem .55rem .85rem;
    border-radius: 50px;
    background: rgba(255,255,255,.1);
    border: 1.5px solid rgba(255,255,255,.2);
    color: rgba(255,255,255,.75);
    font-size: .85rem; font-weight: 500;
    cursor: pointer; transition: all .3s;
    font-family: inherit; white-space: nowrap;
    min-width: 180px;
}
.hdr-search-trigger svg { width:16px; height:16px; flex-shrink:0; }
.hdr-search-trigger:hover {
    background: rgba(255,255,255,.18);
    border-color: rgba(255,255,255,.4);
    color: #fff;
}
.hdr-nav.scrolled .hdr-search-trigger {
    background: var(--bg-secondary,#f1f5f9);
    border-color: var(--border-color);
    color: var(--text-secondary);
}
.hdr-nav.scrolled .hdr-search-trigger:hover {
    border-color: var(--accent-primary);
    color: var(--text-primary);
}
.hdr-search-trigger-kbd {
    margin-right: auto;
    background: rgba(255,255,255,.15);
    padding: .12rem .45rem; border-radius: 5px;
    font-size: .7rem; font-family: monospace; color: rgba(255,255,255,.5);
}
.hdr-nav.scrolled .hdr-search-trigger-kbd {
    background: var(--border-color);
    color: var(--text-secondary);
}

/* Auth */
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
.hdr-nav.scrolled .hdr-login  { color:var(--text-primary); border-color:var(--border-color); }
.hdr-nav.scrolled .hdr-login:hover { background:var(--bg-secondary); }
.hdr-nav.scrolled .hdr-register { color:#fff; background:linear-gradient(135deg,#1e3a8a,#3b82f6); }

/* User menu */
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
    width:30px; height:30px; border-radius:50%;
    background:linear-gradient(135deg,#f59e0b,#ef4444);
    display:flex; align-items:center; justify-content:center;
    font-weight:800; font-size:.9rem; color:#fff; flex-shrink:0;
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

/* ── HAMBURGER BUTTON ── */
.hdr-ham {
    display: none;
    width: 44px; height: 44px; border-radius: 12px;
    background: rgba(255,255,255,.12); border: 1.5px solid rgba(255,255,255,.2);
    color: #fff; cursor: pointer;
    flex-direction: column; align-items: center; justify-content: center;
    gap: 5px; flex-shrink: 0; transition: all .3s;
}
.hdr-ham:hover { background: rgba(255,255,255,.2); }
.hdr-ham-line {
    width: 20px; height: 2px;
    background: #fff; border-radius: 2px;
    transition: all .35s cubic-bezier(.4,0,.2,1);
}
.hdr-nav.scrolled .hdr-ham { background: var(--bg-secondary); border-color: var(--border-color); }
.hdr-nav.scrolled .hdr-ham-line { background: var(--text-primary); }

/* ham → X */
.hdr-ham.open .hdr-ham-line:nth-child(1) { transform: translateY(7px) rotate(45deg); }
.hdr-ham.open .hdr-ham-line:nth-child(2) { opacity: 0; transform: scaleX(0); }
.hdr-ham.open .hdr-ham-line:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

/* ── MOBILE DRAWER ── */
.hdr-drawer-overlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,.55); backdrop-filter: blur(4px);
    z-index: 800;
    opacity: 0; pointer-events: none;
    transition: opacity .35s ease;
}
.hdr-drawer-overlay.open { opacity: 1; pointer-events: all; }

.hdr-drawer {
    position: fixed;
    top: 0; left: 0;
    width: 280px; height: 100vh;
    background: linear-gradient(180deg, #0f172a 0%, #1e3a8a 100%);
    z-index: 900;
    transform: translateX(-100%);
    transition: transform .38s cubic-bezier(.4,0,.2,1);
    display: flex; flex-direction: column;
    overflow-y: auto;
}
.hdr-drawer.open { transform: translateX(0); }

/* drawer header */
.hdr-drawer-top {
    display: flex; align-items: center; justify-content: space-between;
    padding: 1.6rem 1.5rem 1rem;
    border-bottom: 1px solid rgba(255,255,255,.1);
}
.hdr-drawer-logo {
    display: flex; align-items: center; gap: .6rem;
    color: #fff; font-size: 1.2rem; font-weight: 800;
    text-decoration: none;
}
.hdr-drawer-close {
    width: 38px; height: 38px; border-radius: 10px;
    background: rgba(255,255,255,.1); border: none;
    color: #fff; font-size: 1.3rem; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: background .3s;
}
.hdr-drawer-close:hover { background: rgba(255,255,255,.2); }

/* drawer search */
.hdr-drawer-search {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid rgba(255,255,255,.08);
}
.hdr-drawer-search-box {
    display: flex; align-items: center; gap: .6rem;
    background: rgba(255,255,255,.1);
    border: 1.5px solid rgba(255,255,255,.18);
    border-radius: 12px; padding: .7rem 1rem;
}
.hdr-drawer-search-box svg { width:17px; height:17px; color:rgba(255,255,255,.5); flex-shrink:0; }
.hdr-drawer-sinput {
    background: none; border: none; outline: none;
    color: #fff; font-family: inherit; font-size: .9rem; width: 100%;
}
.hdr-drawer-sinput::placeholder { color: rgba(255,255,255,.4); }

/* drawer nav links */
.hdr-drawer-nav {
    padding: 1rem 1.2rem; flex: 1;
    display: flex; flex-direction: column; gap: .4rem;
}
.hdr-drawer-link {
    display: flex; align-items: center; gap: .8rem;
    padding: .95rem 1.1rem; border-radius: 14px;
    color: rgba(255,255,255,.85); text-decoration: none;
    font-weight: 600; font-size: .97rem;
    transition: all .3s; background: transparent;
}
.hdr-drawer-link:hover { background: rgba(255,255,255,.1); color: #fff; transform: translateX(-4px); }
.hdr-drawer-link.on {
    background: rgba(255,255,255,.18);
    color: #fff;
    box-shadow: 0 4px 16px rgba(0,0,0,.2);
}
.hdr-drawer-link-icon {
    width: 36px; height: 36px; border-radius: 10px;
    background: rgba(255,255,255,.1);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; flex-shrink: 0;
    transition: background .3s;
}
.hdr-drawer-link.on .hdr-drawer-link-icon { background: var(--accent-primary,#2563eb); }
.hdr-drawer-link:hover .hdr-drawer-link-icon { background: rgba(255,255,255,.18); }
.hdr-drawer-link-arrow { margin-right: auto; opacity: .5; font-size: .8rem; }
.hdr-drawer-link.on .hdr-drawer-link-arrow { opacity: 1; }

/* drawer divider */
.hdr-drawer-sep {
    font-size: .68rem; letter-spacing: .18em; text-transform: uppercase;
    color: rgba(255,255,255,.3); padding: .6rem 1.2rem .3rem;
    font-weight: 700;
}

/* drawer auth */
.hdr-drawer-auth {
    padding: 1rem 1.2rem 2rem;
    border-top: 1px solid rgba(255,255,255,.1);
    display: flex; flex-direction: column; gap: .7rem;
}
.hdr-drawer-auth-btn {
    display: flex; align-items: center; justify-content: center; gap: .5rem;
    padding: .85rem; border-radius: 14px;
    font-weight: 700; font-size: .9rem;
    text-decoration: none; transition: all .3s; font-family: inherit; border: none;
    cursor: pointer;
}
.hdr-drawer-auth-btn.login {
    background: rgba(255,255,255,.1);
    border: 1.5px solid rgba(255,255,255,.25);
    color: #fff;
}
.hdr-drawer-auth-btn.login:hover { background: rgba(255,255,255,.18); }
.hdr-drawer-auth-btn.register {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #fff;
    box-shadow: 0 6px 20px rgba(37,99,235,.4);
}
.hdr-drawer-auth-btn.register:hover { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(37,99,235,.5); }
.hdr-drawer-auth-btn svg { width:18px; height:18px; }

/* ── SEARCH MODAL ── */
.hdr-search-modal {
    position: fixed; inset: 0;
    z-index: 2000;
    display: flex; align-items: flex-start; justify-content: center;
    padding-top: 8vh;
    opacity: 0; pointer-events: none;
    transition: opacity .3s ease;
}
.hdr-search-modal.open { opacity: 1; pointer-events: all; }

.hdr-search-bg {
    position: absolute; inset: 0;
    background: rgba(2,6,23,.85); backdrop-filter: blur(12px);
}
.hdr-search-box-wrap {
    position: relative; z-index: 2;
    width: 100%; max-width: 680px;
    margin: 0 1rem;
    transform: translateY(-20px) scale(.97);
    transition: all .35s cubic-bezier(.4,0,.2,1);
}
.hdr-search-modal.open .hdr-search-box-wrap {
    transform: translateY(0) scale(1);
}
.hdr-search-field {
    background: #fff; border-radius: 20px;
    box-shadow: 0 30px 80px rgba(0,0,0,.4);
    overflow: hidden;
}
.hdr-search-row {
    display: flex; align-items: center;
    padding: 1rem 1.4rem; gap: 1rem;
    border-bottom: 1px solid #f1f5f9;
}
.hdr-search-row svg { width:22px; height:22px; color:#94a3b8; flex-shrink:0; }
.hdr-search-input {
    flex: 1; border: none; outline: none;
    font-family: inherit; font-size: 1.05rem;
    color: #0f172a; background: transparent;
    direction: rtl;
}
.hdr-search-input::placeholder { color: #94a3b8; }
.hdr-search-close-btn {
    width: 34px; height: 34px; border-radius: 8px;
    border: none; background: #f1f5f9;
    color: #64748b; font-size: .8rem; font-family: monospace;
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    transition: all .2s; flex-shrink:0;
    font-weight: 700;
}
.hdr-search-close-btn:hover { background: #e2e8f0; color: #334155; }
/* results */
.hdr-search-results { padding: 1rem; max-height: 380px; overflow-y: auto; }
.hdr-search-hint {
    text-align: center; padding: 2rem 1rem;
    color: #94a3b8; font-size: .9rem;
}
.hdr-search-hint span { font-size: 2rem; display: block; margin-bottom: .5rem; }
.hdr-sr-item {
    display: flex; align-items: center; gap: .9rem;
    padding: .75rem 1rem; border-radius: 12px;
    text-decoration: none; color: #0f172a;
    transition: background .2s;
}
.hdr-sr-item:hover { background: #f8fafc; }
.hdr-sr-img {
    width: 42px; height: 50px; border-radius: 8px;
    object-fit: cover; flex-shrink: 0;
    border: 1px solid #f1f5f9;
}
.hdr-sr-name { font-size: .92rem; font-weight: 600; color: #0f172a; }
.hdr-sr-price { font-size: .8rem; color: #2563eb; font-weight: 700; margin-top: .15rem; }
.hdr-sr-empty { text-align: center; padding: 1.5rem; color: #94a3b8; font-size: .9rem; }
.hdr-search-cats {
    padding: 0 1rem 1rem;
    display: flex; gap: .5rem; flex-wrap: wrap;
}
.hdr-search-cat {
    padding: .35rem .9rem; border-radius: 50px;
    background: #f1f5f9; border: 1.5px solid #e2e8f0;
    color: #475569; font-size: .78rem; font-weight: 600;
    cursor: pointer; transition: all .2s; text-decoration: none;
}
.hdr-search-cat:hover { background: #2563eb; border-color: #2563eb; color: #fff; }
.hdr-search-cats-label {
    font-size: .65rem; letter-spacing: .15em; text-transform: uppercase;
    color: #94a3b8; font-weight: 700; width: 100%; padding: 0 0 .3rem;
}
/* loading spinner */
.hdr-spin {
    width: 22px; height: 22px; border-radius: 50%;
    border: 2.5px solid #e2e8f0; border-top-color: #2563eb;
    animation: hdrSpin .7s linear infinite; display: none; margin: auto;
}
@keyframes hdrSpin { to{transform:rotate(360deg)} }

/* section label inside results */
.hdr-sr-section-lbl {
    font-size: .68rem; font-weight: 700; letter-spacing: .14em;
    text-transform: uppercase; color: #94a3b8;
    padding: .6rem 1rem .3rem; display: block;
}
.hdr-sr-info { flex: 1; min-width: 0; }
.hdr-sr-meta { display: flex; align-items: center; gap: .5rem; margin-top: .15rem; }
.hdr-sr-disc {
    background: linear-gradient(135deg,#ef4444,#f97316);
    color: #fff; padding: .1rem .45rem; border-radius: 6px;
    font-size: .72rem; font-weight: 800;
}
.hdr-sr-all {
    display: block; text-align: center;
    padding: .85rem; margin: .5rem;
    border-radius: 12px; border: 1.5px solid #e2e8f0;
    color: #2563eb; font-weight: 700; font-size: .88rem;
    text-decoration: none; transition: all .25s;
}
.hdr-sr-all:hover { background: #2563eb; color: #fff; border-color: #2563eb; }

/* ── HERO CONTENT ── */
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
    line-height: 1.15; margin-bottom: 1.4rem;
    letter-spacing: -.02em;
}
.hdr-hero-hl {
    display: inline-block; position: relative;
}
.hdr-hero-hl::after {
    content: ''; position: absolute; bottom: 8px; right: 0; left: 0;
    height: 14px; background: rgba(255,255,255,.22); z-index: -1; border-radius: 6px;
}
.hdr-hero-desc {
    font-size: 1.15rem; color: rgba(255,255,255,.82);
    margin-bottom: 2.4rem; line-height: 1.85;
}
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
.hdr-slider {
    position: relative; width: 100%; max-width: 480px;
    aspect-ratio: 4/5; margin: 0 auto;
}
.hdr-slide {
    position: absolute; inset: 0;
    opacity: 0; transition: opacity .9s ease;
    border-radius: 28px; overflow: hidden;
    box-shadow: 0 32px 80px rgba(0,0,0,.35);
    border: 1px solid rgba(255,255,255,.12);
}
.hdr-slide.on { opacity: 1; }
.hdr-slide img { width: 100%; height: 100%; object-fit: cover; }
.hdr-dots {
    position: absolute; bottom: -52px; left: 50%; transform: translateX(-50%);
    display: flex; gap: .7rem;
}
.hdr-dot {
    width: 10px; height: 10px; border-radius: 50%;
    background: rgba(255,255,255,.25); border: none; cursor: pointer;
    transition: all .3s ease;
}
.hdr-dot:hover { background: rgba(255,255,255,.5); }
.hdr-dot.on { width: 36px; border-radius: 8px; background: #fff; }

/* scroll cue */
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

/* ═══════ RESPONSIVE ═══════ */
@media (max-width:1280px) {
    .hdr-hero-title { font-size: 3.4rem; }
    .hdr-search-trigger { min-width: 150px; }
}
@media (max-width:1100px) {
    .hdr-menu, .hdr-search-trigger { display: none; }
    .hdr-ham { display: flex; }
    .hdr-hero-title { font-size: 3rem; }
}
@media (max-width:768px) {
    .hdr-nav-c { padding: 0 1.25rem; gap: 1rem; }
    .hdr-hero-grid { grid-template-columns: 1fr; text-align: center; gap: 2rem; padding: 0 1.25rem; }
    .hdr-hero-title { font-size: 2.6rem; }
    .hdr-hero-btns { justify-content: center; }
    .hdr-hero { padding: 7rem 0 5rem; min-height: auto; }
    .hdr-slider { max-width: 320px; }
    /* auth: icon only */
    .hdr-login span, .hdr-register span { display: none; }
    .hdr-login, .hdr-register {
        width: 42px; height: 42px; border-radius: 50%;
        padding: 0; justify-content: center;
    }
    .hdr-user-name { display: none; }
}
@media (max-width:576px) {
    .hdr-nav-c { padding: 0 1rem; }
    .hdr-logo-text { display: none; }
    .hdr-hero-title { font-size: 2.2rem; }
    .hdr-hero-desc { font-size: 1rem; }
    .hdr-slider { max-width: 260px; }
    .hdr-hero { padding: 6rem 0 4rem; }
    .hdr-hero-grid { padding: 0 1rem; gap: 1.5rem; }
    .hdr-ibtn { width: 40px; height: 40px; font-size: 1rem; }
    .hdr-ham { width: 40px; height: 40px; }
    .hdr-auth { gap: .4rem; }
    .hdr-login, .hdr-register { width: 40px; height: 40px; }
}
@media (max-width:400px) {
    .hdr-hero-title { font-size: 1.9rem; }
    .hdr-hero-btns { flex-direction: column; align-items: center; }
    .hdr-hbtn { width: 100%; justify-content: center; }
    .hdr-slider { max-width: 220px; }
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
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input class="hdr-search-input"
                       type="text" id="modalSearchInput"
                       placeholder="جستجو در بیش از ۱۰,۰۰۰ کتاب..."
                       autocomplete="off">
                <div class="hdr-spin" id="searchSpinner"></div>
                <button class="hdr-search-close-btn" id="searchClose">ESC</button>
            </div>
            <div class="hdr-search-results" id="searchResults">
                <div class="hdr-search-hint">
                    <span>🔍</span>
                    نام کتاب یا نویسنده را وارد کنید
                </div>
            </div>
            <!-- دسته‌بندی‌های سریع -->
            <div class="hdr-search-cats" id="searchCats">
                <span class="hdr-search-cats-label">دسته‌بندی‌های محبوب</span>
                <?php foreach (array_slice($categories, 0, 6) as $cat): ?>
                    <a href="products.php?category=<?= $cat['id'] ?>" class="hdr-search-cat">
                        <?= escape($cat['title']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- ═══════ MOBILE DRAWER OVERLAY ═══════ -->
<div class="hdr-drawer-overlay" id="drawerOverlay"></div>

<!-- ═══════ MOBILE DRAWER ═══════ -->
<div class="hdr-drawer" id="hdrDrawer">
    <div class="hdr-drawer-top">
        <a href="index.php" class="hdr-drawer-logo">
            <span>📚</span>
            <span><?= escape(SITE_NAME) ?></span>
        </a>
        <button class="hdr-drawer-close" id="drawerClose">✕</button>
    </div>

    <!-- جستجو در drawer -->
    <div class="hdr-drawer-search">
        <div class="hdr-drawer-search-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input class="hdr-drawer-sinput" type="text"
                   placeholder="جستجوی کتاب..."
                   id="drawerSearchInput">
        </div>
    </div>

    <!-- لینک‌های منو -->
    <nav class="hdr-drawer-nav">
        <div class="hdr-drawer-sep">منوی اصلی</div>
        <a href="index.php" class="hdr-drawer-link <?= $current_file==='index' ?'on':'' ?>">
            <span class="hdr-drawer-link-icon">🏠</span>
            صفحه اصلی
            <span class="hdr-drawer-link-arrow">‹</span>
        </a>
        <a href="products.php" class="hdr-drawer-link <?= $current_file==='products' ?'on':'' ?>">
            <span class="hdr-drawer-link-icon">📦</span>
            محصولات
            <span class="hdr-drawer-link-arrow">‹</span>
        </a>
        <a href="posts.php" class="hdr-drawer-link <?= $current_file==='posts' ?'on':'' ?>">
            <span class="hdr-drawer-link-icon">📝</span>
            مقالات
            <span class="hdr-drawer-link-arrow">‹</span>
        </a>
        <a href="about.php" class="hdr-drawer-link <?= $current_file==='about' ?'on':'' ?>">
            <span class="hdr-drawer-link-icon">ℹ️</span>
            درباره ما
            <span class="hdr-drawer-link-arrow">‹</span>
        </a>

        <?php if ($logged_in): ?>
        <div class="hdr-drawer-sep" style="margin-top:.5rem">حساب کاربری</div>
        <a href="profile.php" class="hdr-drawer-link">
            <span class="hdr-drawer-link-icon">👤</span>
            پروفایل من
            <span class="hdr-drawer-link-arrow">‹</span>
        </a>
        <a href="orders.php" class="hdr-drawer-link">
            <span class="hdr-drawer-link-icon">🛒</span>
            سفارش‌های من
            <span class="hdr-drawer-link-arrow">‹</span>
        </a>
        <a href="logout.php" class="hdr-drawer-link" style="color:rgba(239,68,68,.9)">
            <span class="hdr-drawer-link-icon">🚪</span>
            خروج
            <span class="hdr-drawer-link-arrow">‹</span>
        </a>
        <?php endif; ?>
    </nav>

    <!-- auth در drawer -->
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

    <!-- NAVBAR -->
    <nav class="hdr-nav" id="hdrNav">
        <div class="hdr-nav-c">

            <!-- Logo -->
            <a href="index.php" class="hdr-logo">
                <span class="hdr-logo-icon">📚</span>
                <span class="hdr-logo-text"><?= escape(SITE_NAME) ?></span>
            </a>

            <!-- Desktop menu -->
            <ul class="hdr-menu">
                <li><a href="index.php"    class="hdr-mlink <?= $current_file==='index'    ?'on':'' ?>"><span>🏠</span> صفحه اصلی</a></li>
                <li><a href="products.php" class="hdr-mlink <?= $current_file==='products' ?'on':'' ?>"><span>📦</span> محصولات</a></li>
                <li><a href="posts.php"    class="hdr-mlink <?= $current_file==='posts'    ?'on':'' ?>"><span>📝</span> مقالات</a></li>
                <li><a href="about.php"    class="hdr-mlink <?= $current_file==='about'    ?'on':'' ?>"><span>ℹ️</span> درباره ما</a></li>
            </ul>

            <!-- Search trigger -->
            <button class="hdr-search-trigger" id="searchTrigger">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                جستجو در کتاب‌ها...
                <span class="hdr-search-trigger-kbd">⌘K</span>
            </button>

            <!-- Icons -->
            <div class="hdr-icons">
                <!-- Dark mode -->
                <button class="hdr-ibtn" id="darkToggle" aria-label="تغییر تم">
                    <span id="darkIcon">🌙</span>
                </button>
                <!-- Cart -->
                <a href="cart.php" class="hdr-ibtn" aria-label="سبد خرید">
                    🛒
                    <?php if ($total_cart_count > 0): ?>
                        <span class="hdr-cart-badge"><?= $total_cart_count ?></span>
                    <?php endif; ?>
                </a>

                <!-- Auth or User -->
                <?php if ($logged_in): ?>
                    <div class="hdr-user">
                        <button class="hdr-user-btn" id="userMenuBtn">
                            <span class="hdr-uav"><?= $user_avatar ?></span>
                            <span class="hdr-user-name"><?= escape($user_name) ?></span>
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M7,10L12,15L17,10H7Z"/></svg>
                        </button>
                        <div class="hdr-udrop" id="userDrop">
                            <a href="profile.php" class="hdr-ditem">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/></svg>
                                پروفایل
                            </a>
                            <a href="orders.php" class="hdr-ditem">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17,18A2,2 0 0,1 19,20A2,2 0 0,1 17,22C15.89,22 15,21.1 15,20C15,18.89 15.89,18 17,18M1,2H4.27L5.21,4H20A1,1 0 0,1 21,5L17.3,11.97C16.96,12.58 16.3,13 15.55,13H8.1L7.2,14.63L7.17,14.75A0.25,0.25 0 0,0 7.42,15H19V17H7C5.89,17 5,16.1 5,15L6.6,11.59L3,4H1V2Z"/></svg>
                                سفارش‌ها
                            </a>
                            <div class="hdr-ddiv"></div>
                            <a href="logout.php" class="hdr-ditem danger">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M16,17V14H9V10H16V7L21,12L16,17M14,2A2,2 0 0,1 16,4V6H14V4H5V20H14V18H16V20A2,2 0 0,1 14,22H5A2,2 0 0,1 3,20V4A2,2 0 0,1 5,2H14Z"/></svg>
                                خروج
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="hdr-auth">
                        <a href="login.php" class="hdr-login">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M10,17V14H3V10H10V7L15,12L10,17M10,2H19A2,2 0 0,1 21,4V20A2,2 0 0,1 19,22H10A2,2 0 0,1 8,20V18H10V20H19V4H10V6H8V4A2,2 0 0,1 10,2Z"/></svg>
                            <span>ورود</span>
                        </a>
                        <a href="register.php" class="hdr-register">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M15,14C12.33,14 7,15.33 7,18V20H23V18C23,15.33 17.67,14 15,14M6,10V7H4V10H1V12H4V15H6V12H9V10M15,12A4,4 0 0,0 19,8A4,4 0 0,0 15,4A4,4 0 0,0 11,8A4,4 0 0,0 15,12Z"/></svg>
                            <span>ثبت‌نام</span>
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Hamburger -->
                <button class="hdr-ham" id="hdrHam" aria-label="منو">
                    <span class="hdr-ham-line"></span>
                    <span class="hdr-ham-line"></span>
                    <span class="hdr-ham-line"></span>
                </button>
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <div class="hdr-hero">
        <div class="hdr-hero-grid">
            <div class="hdr-hero-text">
                <div class="hdr-hero-badge">✨ تخفیف ویژه تا ۵۰٪</div>
                <h1 class="hdr-hero-title">
                    دنیای <span class="hdr-hero-hl">کتاب</span><br>
                    در یک کلیک
                </h1>
                <p class="hdr-hero-desc">
                    بیش از <strong>۱۰,۰۰۰</strong> عنوان کتاب در دسته‌بندی‌های مختلف
                    با بهترین کیفیت و قیمت مناسب
                </p>
                <div class="hdr-hero-btns">
                    <a href="products.php" class="hdr-hbtn hdr-hbtn-p">🚀 مشاهده محصولات</a>
                    <a href="posts.php"    class="hdr-hbtn hdr-hbtn-s">📖 مقالات آموزشی</a>
                </div>
            </div>

            <div class="hdr-slider-w">
                <div class="hdr-slider">
                    <?php if (!empty($posts_slider)): ?>
                        <?php foreach ($posts_slider as $i => $slide): ?>
                            <div class="hdr-slide <?= $i===0?'on':'' ?>">
                                <img src="./img1/<?= escape($slide['img']) ?>"
                                     alt="کتاب <?= $i+1 ?>"
                                     loading="<?= $i===0?'eager':'lazy' ?>">
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <div class="hdr-dots" id="sliderDots">
                        <?php foreach ($posts_slider as $i => $slide): ?>
                            <button class="hdr-dot <?= $i===0?'on':'' ?>" data-i="<?= $i ?>"></button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="hdr-scroll-cue">
        <span>اسکرول</span>
        <div class="hdr-scroll-line"></div>
    </div>
</div><!-- /hdr-wrapper -->

<script>
(function () {

    /* ── Slider ── */
    var slides   = document.querySelectorAll('.hdr-slide');
    var dots     = document.querySelectorAll('.hdr-dot');
    var sliderCur = 0;
    function goSlide(n) {
        slides.forEach(function(s){ s.classList.remove('on'); });
        dots.forEach(function(d){ d.classList.remove('on'); });
        sliderCur = (n + slides.length) % slides.length;
        slides[sliderCur].classList.add('on');
        dots[sliderCur].classList.add('on');
    }
    dots.forEach(function(d,i){ d.addEventListener('click', function(){ goSlide(i); }); });
    if (slides.length > 1) setInterval(function(){ goSlide(sliderCur + 1); }, 5000);

    /* ── Sticky Navbar ── */
    var nav = document.getElementById('hdrNav');
    window.addEventListener('scroll', function(){
        nav.classList.toggle('scrolled', window.scrollY > 80);
    }, { passive: true });

    /* ── Hamburger + Drawer ── */
    var ham     = document.getElementById('hdrHam');
    var drawer  = document.getElementById('hdrDrawer');
    var overlay = document.getElementById('drawerOverlay');
    var dClose  = document.getElementById('drawerClose');

    function openDrawer() {
        ham.classList.add('open');
        drawer.classList.add('open');
        overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeDrawer() {
        ham.classList.remove('open');
        drawer.classList.remove('open');
        overlay.classList.remove('open');
        document.body.style.overflow = '';
    }
    ham.addEventListener('click', function(){
        drawer.classList.contains('open') ? closeDrawer() : openDrawer();
    });
    overlay.addEventListener('click', closeDrawer);
    dClose.addEventListener('click', closeDrawer);

    /* swipe left to close */
    var swipeStart = 0;
    drawer.addEventListener('touchstart', function(e){ swipeStart = e.touches[0].clientX; }, { passive:true });
    drawer.addEventListener('touchmove',  function(e){
        if (swipeStart - e.touches[0].clientX > 70) closeDrawer();
    }, { passive:true });

    /* ── Drawer search → redirect ── */
    document.getElementById('drawerSearchInput').addEventListener('keydown', function(e){
        if (e.key === 'Enter' && this.value.trim()) {
            window.location = 'products.php?search=' + encodeURIComponent(this.value.trim());
        }
    });

    /* ── Search Modal ── */
    var modal     = document.getElementById('searchModal');
    var searchBg  = document.getElementById('searchBg');
    var searchIn  = document.getElementById('modalSearchInput');
    var searchRes = document.getElementById('searchResults');
    var searchCats= document.getElementById('searchCats');
    var spinner   = document.getElementById('searchSpinner');
    var sClose    = document.getElementById('searchClose');
    var sTrigger  = document.getElementById('searchTrigger');

    function openSearch() {
        modal.classList.add('open');
        document.body.style.overflow = 'hidden';
        setTimeout(function(){ searchIn.focus(); }, 100);
    }
    function closeSearch() {
        modal.classList.remove('open');
        document.body.style.overflow = '';
        searchIn.value = '';
        searchRes.innerHTML = '<div class="hdr-search-hint"><span>🔍</span>نام کتاب یا نویسنده را وارد کنید</div>';
        searchCats.style.display = '';
    }
    sTrigger && sTrigger.addEventListener('click', openSearch);
    searchBg.addEventListener('click', closeSearch);
    sClose.addEventListener('click', closeSearch);

    /* Keyboard shortcut Cmd/Ctrl + K */
    document.addEventListener('keydown', function(e){
        if ((e.metaKey || e.ctrlKey) && e.key === 'k') { e.preventDefault(); openSearch(); }
        if (e.key === 'Escape') { closeSearch(); closeDrawer(); }
    });

    /* ── Live Search (inline data — no external file needed) ── */
    var searchTimer;
    searchIn.addEventListener('input', function(){
        clearTimeout(searchTimer);
        var q = this.value.trim();
        if (!q) {
            searchRes.innerHTML = '<div class="hdr-search-hint"><span>🔍</span>نام کتاب یا نویسنده را وارد کنید</div>';
            searchCats.style.display = '';
            spinner.style.display = 'none';
            return;
        }
        searchCats.style.display = 'none';
        spinner.style.display = 'block';
        searchRes.innerHTML = '';
        searchTimer = setTimeout(function(){
            var url = 'search_live.php?q=' + encodeURIComponent(q) + '&t=' + Date.now();
            fetch(url)
                .then(function(r){ return r.ok ? r.json() : Promise.reject(); })
                .then(function(data){
                    spinner.style.display = 'none';
                    renderResults(data, q);
                })
                .catch(function(){
                    spinner.style.display = 'none';
                    /* graceful fallback */
                    searchRes.innerHTML =
                        '<div class="hdr-sr-empty">⚡ <a href="products.php?search='+encodeURIComponent(q)+'" style="color:#2563eb;font-weight:700;">مشاهده همه نتایج برای «'+q+'»</a></div>';
                });
        }, 350);
    });

    function renderResults(data, q) {
        if (!data || (!data.products.length && !data.posts.length)) {
            searchRes.innerHTML = '<div class="hdr-sr-empty">📭 نتیجه‌ای برای «'+q+'» یافت نشد</div>';
            return;
        }
        var html = '';
        /* محصولات */
        if (data.products.length) {
            html += '<div class="hdr-sr-section-lbl">📦 محصولات</div>';
            data.products.forEach(function(p){
                var disc = p.old_price > 0 && p.old_price != p.price
                    ? '<span class="hdr-sr-disc">'+Math.round((p.old_price-p.price)/p.old_price*100)+'%</span>' : '';
                html += '<a href="single_product.php?product='+p.id+'" class="hdr-sr-item" onclick="closeSearch()">';
                html += '<img class="hdr-sr-img" src="./upload/products/'+esc(p.pic)+'" alt="'+esc(p.name)+'" loading="lazy">';
                html += '<div class="hdr-sr-info"><div class="hdr-sr-name">'+highlight(p.name,q)+'</div>';
                html += '<div class="hdr-sr-meta"><span class="hdr-sr-price">'+fmtPrice(p.price)+'</span>'+disc+'</div></div>';
                html += '</a>';
            });
        }
        /* مقالات */
        if (data.posts.length) {
            html += '<div class="hdr-sr-section-lbl" style="margin-top:.5rem">📝 مقالات</div>';
            data.posts.forEach(function(p){
                html += '<a href="single.php?post='+p.id+'" class="hdr-sr-item" onclick="closeSearch()">';
                html += '<img class="hdr-sr-img" src="./upload/posts/'+esc(p.image)+'" alt="'+esc(p.title)+'" loading="lazy">';
                html += '<div class="hdr-sr-info"><div class="hdr-sr-name">'+highlight(p.title,q)+'</div>';
                html += '<div class="hdr-sr-meta" style="color:#64748b;font-size:.78rem">'+esc(p.author)+'</div></div>';
                html += '</a>';
            });
        }
        /* لینک مشاهده همه */
        html += '<a href="products.php?search='+encodeURIComponent(q)+'" class="hdr-sr-all" onclick="closeSearch()">مشاهده همه نتایج ←</a>';
        searchRes.innerHTML = html;
    }

    function highlight(text, q) {
        if (!q) return esc(text);
        var safe = q.replace(/[.*+?^${}()|[\]\\]/g,'\\$&');
        return esc(text).replace(new RegExp('('+safe+')', 'gi'),
            '<mark style="background:#fde68a;color:#92400e;padding:1px 2px;border-radius:3px">$1</mark>');
    }
    function esc(s){ var d=document.createElement('div');d.textContent=s||'';return d.innerHTML; }
    function fmtPrice(n){ return Number(n).toLocaleString('fa-IR')+' تومان'; }
    window.closeSearch = closeSearch; /* expose for onclick */

    /* Enter → products page */
    searchIn.addEventListener('keydown', function(e){
        if (e.key === 'Enter' && this.value.trim()) {
            window.location = 'products.php?search=' + encodeURIComponent(this.value.trim());
        }
    });

    /* ── User dropdown ── */
    var uBtn  = document.getElementById('userMenuBtn');
    var uDrop = document.getElementById('userDrop');
    if (uBtn) {
        uBtn.addEventListener('click', function(e){
            e.stopPropagation();
            uBtn.classList.toggle('open');
            uDrop.classList.toggle('open');
        });
        document.addEventListener('click', function(){
            uBtn.classList.remove('open');
            uDrop.classList.remove('open');
        });
    }

    /* ── Dark Mode ── */
    var darkBtn  = document.getElementById('darkToggle');
    var darkIcon = document.getElementById('darkIcon');
    if (localStorage.getItem('darkMode') === 'enabled') {
        document.body.classList.add('dark-mode');
        darkIcon.textContent = '☀️';
    }
    darkBtn.addEventListener('click', function(){
        var d = document.body.classList.toggle('dark-mode');
        localStorage.setItem('darkMode', d ? 'enabled' : 'disabled');
        darkIcon.textContent = d ? '☀️' : '🌙';
    });

})();
</script>