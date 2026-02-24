<?php
/**
 * صفحه سفارشات - کتاب نت
 */

if (session_status() === PHP_SESSION_NONE) session_start();
require_once("./include/config.php");
require_once("./include/db.php");

// چک لاگین
if (!isset($_SESSION['member_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['member_id'];
$user = fetchOne($db, "SELECT * FROM members WHERE id = ?", [$user_id]);

if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// فیلتر وضعیت
$status_filter = $_GET['status'] ?? 'all';
$search_query = trim($_GET['search'] ?? '');

// ساخت کوئری
$where_conditions = ["member_id = ?"];
$params = [$user_id];

if ($status_filter !== 'all') {
    $where_conditions[] = "status = ?";
    $params[] = $status_filter;
}

if ($search_query) {
    $where_conditions[] = "(order_number LIKE ? OR id LIKE ?)";
    $search_param = "%{$search_query}%";
    $params[] = $search_param;
    $params[] = $search_param;
}

$where_sql = implode(" AND ", $where_conditions);

// گرفتن سفارشات
try {
    $orders = fetchAll($db, "SELECT * FROM orders WHERE {$where_sql} ORDER BY created_at DESC", $params);
} catch (Exception $e) {
    // اگر جدول orders وجود نداشت، یک آرایه خالی برمی‌گردونیم
    $orders = [];
}

// محاسبه آمار
$total_orders = count($orders);
$pending_count = 0;
$completed_count = 0;
$total_amount = 0;

foreach ($orders as $order) {
    $total_amount += $order['total_price'] ?? 0;
    $status = $order['status'] ?? 'pending';
    if ($status === 'pending') $pending_count++;
    if ($status === 'completed') $completed_count++;
}

$avatar_letter = mb_substr($user['name'], 0, 1);
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سفارشات من | <?= escape(SITE_NAME) ?></title>
    <style>
@import url('https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css');
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
:root{
    --dp:#0f172a;--mid:#1e3a8a;--br:#3b82f6;--lt:#60a5fa;
    --w:#fff;--g50:#f8fafc;--g100:#f1f5f9;--g200:#e2e8f0;
    --g400:#94a3b8;--g600:#475569;--g800:#1e293b;
    --red:#ef4444;--orange:#f59e0b;--green:#10b981;--blue:#3b82f6;--purple:#a855f7;
}
html,body{min-height:100%;transition:background .3s,color .3s}
body{
    font-family:'Vazirmatn',Tahoma,Arial,sans-serif;
    direction:rtl;min-height:100vh;
    background:var(--g100);color:var(--g800);
}
body.dark{
    --g100:#0a0f1e;--g50:#1a2332;--w:#ffffff;
    --g200:#2d3748;--g300:#4a5568;--g400:#718096;
    --g600:#cbd5e1;--g700:#e2e8f0;--g800:#f7fafc;--g900:#ffffff;
    background:#0a0f1e;color:#f7fafc;
}

/* ══ header ══ */
.top{
    background:linear-gradient(135deg,var(--dp),var(--mid));
    padding:1.25rem 0;box-shadow:0 4px 20px rgba(0,0,0,.2);
    position:sticky;top:0;z-index:100;border-radius: 0 0 16px 16px;
}
body.dark .top{background:linear-gradient(135deg,#020617,#0c1e47)}
.top::before{
    content:'';position:absolute;inset:0;
    background:radial-gradient(ellipse at 20% 0,rgba(59,130,246,.35),transparent 65%);
}
.top-in{
    max-width:1200px;margin:0 auto;padding:0 1.5rem;
    display:flex;align-items:center;justify-content:space-between;
    position:relative;z-index:1;gap:1rem;flex-wrap:wrap;
}
.logo{
    display:inline-flex;align-items:center;gap:.55rem;
    color:var(--w);text-decoration:none;font-size:1.15rem;font-weight:800;
    transition:opacity .25s;
}
.logo:hover{opacity:.8}
.logo svg{width:24px;height:24px}

.top-act{display:flex;align-items:center;gap:.65rem}
.tbtn{
    display:inline-flex;align-items:center;gap:.45rem;
    padding:.55rem 1rem;border-radius:11px;
    font-weight:600;font-size:.85rem;
    text-decoration:none;font-family:inherit;cursor:pointer;
    transition:all .25s;border:2px solid transparent;background:none;
}
.tbtn-home{background:rgba(255,255,255,.15);color:var(--w)}
.tbtn-home:hover{background:rgba(255,255,255,.25)}
.tbtn-dark{background:rgba(255,255,255,.12);color:var(--w)}
.tbtn-dark:hover{background:rgba(255,255,255,.22)}
.tbtn-out{background:rgba(239,68,68,.2);color:var(--w);border-color:rgba(239,68,68,.4)}
.tbtn-out:hover{background:rgba(239,68,68,.35)}
.tbtn svg{width:16px;height:16px}

/* ══ main ══ */
.main{max-width:1300px;margin:0 auto;padding:2rem 1.5rem}

/* page header */
.page-head{
    background:linear-gradient(135deg,var(--mid),var(--br),var(--purple));
    border-radius:22px 22px 0 0;
    padding:2rem;margin-bottom:0;
    position:relative;overflow:hidden;
}
body.dark .page-head{background:linear-gradient(135deg,#1e3a8a,#3b82f6,#7c3aed)}
.page-head::before{
    content:'';position:absolute;inset:0;
    background:url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="2" fill="rgba(255,255,255,.15)"/></svg>');
}
.page-head-content{position:relative;z-index:1;text-align:center}
.page-title{
    font-size:2rem;font-weight:900;color:#fff;
    display:inline-flex;align-items:center;gap:.75rem;
    text-shadow:0 2px 8px rgba(0,0,0,.2);margin-bottom:.5rem;
}
.page-title svg{width:36px;height:36px}
.page-subtitle{color:rgba(255,255,255,.85);font-size:1rem}

/* profile card wrapper */
.pcard{
    background:var(--w);border-radius:22px;
    box-shadow:0 4px 20px rgba(0,0,0,.08);margin-bottom:2rem;
}
body.dark .pcard{background:var(--g50);box-shadow:0 4px 20px rgba(0,0,0,.4)}
.pcard-body{padding:2rem;margin-top:0}

/* آمار */
.stats-grid{
    display:grid;grid-template-columns:repeat(4,1fr);
    gap:1rem;padding-top:1.75rem;margin-top:-50px;
    border-top:2px solid var(--g200);position:relative;z-index:1;
}
body.dark .stats-grid{border-color:var(--g400)}
.stat-card{
    background:transparent;border-radius:0;padding:1.5rem;
    box-shadow:none;
    transition:all .3s;position:relative;overflow:visible;
    text-align:center;
}
body.dark .stat-card{background:transparent;box-shadow:none;border:none}
.stat-card::before{display:none}
.stat-card:hover{transform:translateY(-3px);box-shadow:0 8px 20px rgba(0,0,0,.08)}
body.dark .stat-card:hover{box-shadow:0 8px 20px rgba(0,0,0,.3)}
.stat-icon{
    width:48px;height:48px;border-radius:12px;
    display:inline-flex;align-items:center;justify-content:center;
    margin:0 auto 1rem;font-size:1.5rem;
}
.stat-icon.blue{background:rgba(59,130,246,.12);color:var(--br)}
.stat-icon.orange{background:rgba(251,191,36,.12);color:var(--orange)}
.stat-icon.green{background:rgba(16,185,129,.12);color:var(--green)}
.stat-val{
    font-size:1.8rem;font-weight:900;
    background:linear-gradient(135deg,var(--br),var(--purple));
    -webkit-background-clip:text;-webkit-text-fill-color:transparent;
    background-clip:text;margin-bottom:.25rem;
}
.stat-label{font-size:.82rem;color:var(--g400);font-weight:600}

/* فیلتر و جستجو */
.controls{
    background:var(--w);border-radius:18px;padding:1.5rem;
    margin-bottom:2rem;box-shadow:0 2px 12px rgba(0,0,0,.06);
    display:flex;gap:1rem;flex-wrap:wrap;align-items:center;
}
body.dark .controls{background:var(--g50);box-shadow:0 2px 12px rgba(0,0,0,.4)}

.search-box{position:relative;flex:1;min-width:250px}
.search-box input{
    width:100%;padding:.85rem 3rem .85rem 1.25rem;
    border:2px solid var(--g200);border-radius:12px;
    font-size:.92rem;font-family:inherit;
    background:var(--g50);color:var(--g800);
    transition:all .25s;outline:none;
}
body.dark .search-box input{background:var(--g200);border-color:var(--g300);color:var(--g900)}
.search-box input:focus{border-color:var(--br);box-shadow:0 0 0 3px rgba(59,130,246,.12)}
.search-box svg{
    position:absolute;right:1rem;top:50%;transform:translateY(-50%);
    width:18px;height:18px;color:var(--g400);pointer-events:none;
}

.filter-btns{display:flex;gap:.65rem;flex-wrap:wrap}
.filter-btn{
    padding:.75rem 1.25rem;border-radius:10px;
    font-weight:600;font-size:.88rem;font-family:inherit;
    border:2px solid var(--g200);background:var(--g50);
    color:var(--g800);cursor:pointer;
    transition:all .25s;text-decoration:none;
}
body.dark .filter-btn{background:var(--g200);border-color:var(--g300);color:var(--g900)}
.filter-btn:hover{background:var(--g200);border-color:var(--g400)}
body.dark .filter-btn:hover{background:var(--g300)}
.filter-btn.active{
    background:linear-gradient(135deg,var(--mid),var(--br));
    border-color:var(--br);color:#fff;
    box-shadow:0 4px 12px rgba(59,130,246,.3);
}

/* لیست سفارشات */
.orders-list{display:flex;flex-direction:column;gap:1.25rem}

.order-card{
    background:var(--w);border-radius:18px;
    padding:1.75rem;box-shadow:0 2px 12px rgba(0,0,0,.06);
    transition:all .3s;border:2px solid transparent;
}
body.dark .order-card{background:var(--g50);box-shadow:0 2px 12px rgba(0,0,0,.4)}
.order-card:hover{
    border-color:rgba(59,130,246,.2);
    box-shadow:0 8px 28px rgba(0,0,0,.12);
    transform:translateY(-2px);
}
body.dark .order-card:hover{box-shadow:0 8px 28px rgba(0,0,0,.5)}

.order-header{
    display:flex;justify-content:space-between;align-items:flex-start;
    margin-bottom:1.25rem;gap:1rem;flex-wrap:wrap;
}
.order-number{
    font-size:1.3rem;font-weight:800;color:var(--g800);
    display:flex;align-items:center;gap:.6rem;
}
body.dark .order-number{color:var(--g800)}
.order-number svg{width:22px;height:22px;color:var(--br)}
.order-date{color:var(--g400);font-size:.85rem;margin-top:.3rem}

.order-status{
    padding:.6rem 1.25rem;border-radius:50px;
    font-weight:700;font-size:.85rem;
    display:inline-flex;align-items:center;gap:.5rem;
}
.order-status svg{width:16px;height:16px}
.order-status.pending{background:rgba(251,191,36,.15);color:#f59e0b;border:2px solid rgba(251,191,36,.3)}
.order-status.processing{background:rgba(59,130,246,.15);color:var(--br);border:2px solid rgba(59,130,246,.3)}
.order-status.completed{background:rgba(16,185,129,.15);color:var(--green);border:2px solid rgba(16,185,129,.3)}
.order-status.cancelled{background:rgba(239,68,68,.15);color:var(--red);border:2px solid rgba(239,68,68,.3)}

.order-body{
    padding-top:1.25rem;border-top:2px solid var(--g200);
    display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
    gap:1.25rem;
}
body.dark .order-body{border-color:var(--g300)}

.order-info{display:flex;align-items:center;gap:.75rem}
.order-info-icon{
    width:40px;height:40px;border-radius:10px;
    background:var(--g100);display:flex;
    align-items:center;justify-content:center;
    color:var(--br);flex-shrink:0;
}
body.dark .order-info-icon{background:var(--g200)}
.order-info-icon svg{width:20px;height:20px}
.order-info-label{font-size:.8rem;color:var(--g400);margin-bottom:.2rem}
.order-info-value{font-size:.95rem;font-weight:700;color:var(--g800)}
body.dark .order-info-value{color:var(--g800)}

.order-footer{
    margin-top:1.25rem;padding-top:1.25rem;
    border-top:2px solid var(--g200);
    display:flex;justify-content:space-between;align-items:center;
    gap:1rem;flex-wrap:wrap;
}
body.dark .order-footer{border-color:var(--g300)}

.order-total{
    font-size:1.5rem;font-weight:900;
    background:linear-gradient(135deg,var(--br),var(--purple));
    -webkit-background-clip:text;-webkit-text-fill-color:transparent;
    background-clip:text;
}

.order-actions{display:flex;gap:.65rem;flex-wrap:wrap}
.btn{
    padding:.75rem 1.5rem;border-radius:11px;
    font-weight:700;font-size:.88rem;font-family:inherit;
    cursor:pointer;display:inline-flex;align-items:center;gap:.55rem;
    transition:all .25s;text-decoration:none;border:none;
}
.btn-primary{background:linear-gradient(135deg,var(--mid),var(--br));color:#fff;box-shadow:0 4px 14px rgba(30,58,138,.25)}
.btn-primary:hover{transform:translateY(-2px);box-shadow:0 8px 22px rgba(30,58,138,.35)}
.btn-secondary{background:var(--g200);color:var(--g800);border:2px solid var(--g200)}
body.dark .btn-secondary{background:var(--g300);color:var(--g900);border-color:var(--g300)}
.btn-secondary:hover{background:var(--g400);color:#fff}
.btn svg{width:17px;height:17px}

/* پیام خالی */
.empty-state{
    text-align:center;padding:4rem 2rem;
    background:var(--w);border-radius:20px;
    box-shadow:0 2px 12px rgba(0,0,0,.06);
}
body.dark .empty-state{background:var(--g50);box-shadow:0 2px 12px rgba(0,0,0,.4)}
.empty-icon{
    font-size:6rem;margin-bottom:1.5rem;
    opacity:.3;animation:float 3s ease-in-out infinite;
}
@keyframes float{0%,100%{transform:translateY(0)} 50%{transform:translateY(-12px)}}
.empty-title{font-size:1.8rem;font-weight:900;color:var(--g800);margin-bottom:.75rem}
body.dark .empty-title{color:var(--g800)}
.empty-desc{color:var(--g400);font-size:1.05rem;margin-bottom:2rem}
.empty-btn{
    display:inline-flex;align-items:center;gap:.65rem;
    padding:1rem 2rem;border-radius:50px;
    background:linear-gradient(135deg,var(--mid),var(--br));
    color:#fff;font-weight:700;font-size:1rem;
    text-decoration:none;transition:all .3s;
    box-shadow:0 8px 24px rgba(30,58,138,.3);
}
.empty-btn:hover{transform:translateY(-3px);box-shadow:0 12px 32px rgba(30,58,138,.4)}

/* responsive */
@media(max-width:900px){
    .stats-grid{grid-template-columns:repeat(2,1fr)}
    .order-body{grid-template-columns:1fr}
}
@media(max-width:700px){
    .top-in{justify-content:center}
    .top-act{width:100%;justify-content:center}
    .main{padding:1.5rem 1rem}
    .page-head{padding:1.5rem}
    .pcard-body{padding:0 1.25rem 1.5rem}
    .page-title{font-size:1.6rem}
    .page-title svg{width:30px;height:30px}
    .stats-grid{grid-template-columns:1fr;gap:.85rem}
    .controls{flex-direction:column;align-items:stretch}
    .search-box{min-width:100%}
    .filter-btns{justify-content:center}
    .order-header{flex-direction:column;align-items:flex-start}
    .order-footer{flex-direction:column;align-items:flex-start}
    .order-actions{width:100%}
    .btn{width:100%;justify-content:center}
}
@media(max-width:420px){
    .tbtn span{display:none}
    .tbtn{padding:.55rem .8rem}
    .stat-card{padding:1.25rem}
}
    </style>
</head>
<body>

<!-- header -->
<div class="top">
    <div class="top-in">
        <a href="index.php" class="logo">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V4A2,2 0 0,0 18,2M18,20H6V4H11V12L13.5,10.5L16,12V4H18V20Z"/></svg>
            <span><?= escape(SITE_NAME) ?></span>
        </a>
        <div class="top-act">
            <a href="index.php" class="tbtn tbtn-home">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M10,20V14H14V20H19V12H22L12,3L2,12H5V20H10Z"/></svg>
                <span>خانه</span>
            </a>
            <button class="tbtn tbtn-dark" id="darkToggle">
                <svg viewBox="0 0 24 24" fill="currentColor" id="darkIcon"><path d="M17.75,4.09L15.22,6.03L16.13,9.09L13.5,7.28L10.87,9.09L11.78,6.03L9.25,4.09L12.44,4L13.5,1L14.56,4L17.75,4.09M21.25,11L19.61,12.25L20.2,14.23L18.5,13.06L16.8,14.23L17.39,12.25L15.75,11L17.81,10.95L18.5,9L19.19,10.95L21.25,11M18.97,15.95C19.8,15.87 20.69,17.05 20.16,17.8C19.84,18.25 19.5,18.67 19.08,19.07C15.17,23 8.84,23 4.94,19.07C1.03,15.17 1.03,8.83 4.94,4.93C5.34,4.53 5.76,4.17 6.21,3.85C6.96,3.32 8.14,4.21 8.06,5.04C7.79,7.9 8.75,10.87 10.95,13.06C13.14,15.26 16.1,16.22 18.97,15.95M17.33,17.97C14.5,17.81 11.7,16.64 9.53,14.5C7.36,12.31 6.2,9.5 6.04,6.68C3.23,9.82 3.34,14.64 6.35,17.66C9.37,20.67 14.19,20.78 17.33,17.97Z"/></svg>
                <span>دارک مود</span>
            </button>
            <a href="logout.php" class="tbtn tbtn-out">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M16,17V14H9V10H16V7L21,12L16,17M14,2A2,2 0 0,1 16,4V6H14V4H5V20H14V18H16V20A2,2 0 0,1 14,22H5A2,2 0 0,1 3,20V4A2,2 0 0,1 5,2H14Z"/></svg>
                <span>خروج</span>
            </a>
        </div>
    </div>
</div>

<!-- main -->
<div class="main">

    <!-- page header & stats -->
    <div class="pcard">
        <div class="page-head">
            <div class="page-head-content">
                <div class="page-title">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17,18A2,2 0 0,1 19,20A2,2 0 0,1 17,22C15.89,22 15,21.1 15,20C15,18.89 15.89,18 17,18M1,2H4.27L5.21,4H20A1,1 0 0,1 21,5C21,5.17 20.95,5.34 20.88,5.5L17.3,11.97C16.96,12.58 16.3,13 15.55,13H8.1L7.2,14.63L7.17,14.75A0.25,0.25 0 0,0 7.42,15H19V17H7C5.89,17 5,16.1 5,15C5,14.65 5.09,14.32 5.24,14.04L6.6,11.59L3,4H1V2M7,18A2,2 0 0,1 9,20A2,2 0 0,1 7,22C5.89,22 5,21.1 5,20C5,18.89 5.89,18 7,18M16,11L18.78,6H6.14L8.5,11H16Z"/></svg>
                    سفارشات من
                </div>
                <p class="page-subtitle">مدیریت و پیگیری سفارشات خود</p>
            </div>
        </div>

        <div class="pcard-body">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue">📦</div>
                    <div class="stat-val"><?= $total_orders ?></div>
                    <div class="stat-label">کل سفارشات</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon orange">⏳</div>
                    <div class="stat-val"><?= $pending_count ?></div>
                    <div class="stat-label">در انتظار</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green">✅</div>
                    <div class="stat-val"><?= $completed_count ?></div>
                    <div class="stat-label">تکمیل شده</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon blue">💰</div>
                    <div class="stat-val"><?= number_format($total_amount) ?></div>
                    <div class="stat-label">تومان</div>
                </div>
            </div>
        </div>
    </div>

    <!-- فیلتر و جستجو -->
    <form method="GET" class="controls">
        <div class="search-box">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z"/></svg>
            <input type="text" name="search" placeholder="جستجو در سفارشات..." value="<?= escape($search_query) ?>">
        </div>
        <div class="filter-btns">
            <a href="?status=all" class="filter-btn <?= $status_filter==='all'?'active':'' ?>">همه</a>
            <a href="?status=pending" class="filter-btn <?= $status_filter==='pending'?'active':'' ?>">در انتظار</a>
            <a href="?status=processing" class="filter-btn <?= $status_filter==='processing'?'active':'' ?>">در حال پردازش</a>
            <a href="?status=completed" class="filter-btn <?= $status_filter==='completed'?'active':'' ?>">تکمیل شده</a>
            <a href="?status=cancelled" class="filter-btn <?= $status_filter==='cancelled'?'active':'' ?>">لغو شده</a>
        </div>
    </form>

    <!-- لیست سفارشات -->
    <?php if (empty($orders)): ?>
        <div class="empty-state">
            <div class="empty-icon">📭</div>
            <h2 class="empty-title">هنوز سفارشی ثبت نکرده‌اید</h2>
            <p class="empty-desc">با خرید اولین محصول، سفارشات شما اینجا نمایش داده می‌شود</p>
            <a href="products.php" class="empty-btn">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17,18A2,2 0 0,1 19,20A2,2 0 0,1 17,22C15.89,22 15,21.1 15,20C15,18.89 15.89,18 17,18M1,2H4.27L5.21,4H20A1,1 0 0,1 21,5C21,5.17 20.95,5.34 20.88,5.5L17.3,11.97C16.96,12.58 16.3,13 15.55,13H8.1L7.2,14.63L7.17,14.75A0.25,0.25 0 0,0 7.42,15H19V17H7C5.89,17 5,16.1 5,15C5,14.65 5.09,14.32 5.24,14.04L6.6,11.59L3,4H1V2M7,18A2,2 0 0,1 9,20A2,2 0 0,1 7,22C5.89,22 5,21.1 5,20C5,18.89 5.89,18 7,18M16,11L18.78,6H6.14L8.5,11H16Z"/></svg>
                مشاهده محصولات
            </a>
        </div>
    <?php else: ?>
        <div class="orders-list">
            <?php foreach ($orders as $order): 
                $order_status = $order['status'] ?? 'pending';
                $status_labels = [
                    'pending' => 'در انتظار',
                    'processing' => 'در حال پردازش',
                    'completed' => 'تکمیل شده',
                    'cancelled' => 'لغو شده'
                ];
                $status_label = $status_labels[$order_status] ?? 'نامشخص';
            ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <div class="order-number">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M3,3H21V7H3V3M4,8H20V21H4V8M9.5,11A0.5,0.5 0 0,0 9,11.5V13H15V11.5A0.5,0.5 0 0,0 14.5,11H9.5Z"/></svg>
                                سفارش #<?= escape($order['order_number'] ?? $order['id']) ?>
                            </div>
                            <div class="order-date">
                                <?= date('Y/m/d - H:i', strtotime($order['created_at'] ?? 'now')) ?>
                            </div>
                        </div>
                        <div class="order-status <?= $order_status ?>">
                            <?php if($order_status==='pending'): ?>
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22C6.47,22 2,17.5 2,12A10,10 0 0,1 12,2M12.5,7V12.25L17,14.92L16.25,16.15L11,13V7H12.5Z"/></svg>
                            <?php elseif($order_status==='processing'): ?>
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,4V2A10,10 0 0,0 2,12H4A8,8 0 0,1 12,4Z"/></svg>
                            <?php elseif($order_status==='completed'): ?>
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/></svg>
                            <?php else: ?>
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z"/></svg>
                            <?php endif; ?>
                            <?= $status_label ?>
                        </div>
                    </div>

                    <div class="order-body">
                        <div class="order-info">
                            <div class="order-info-icon">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17,18A2,2 0 0,1 19,20A2,2 0 0,1 17,22C15.89,22 15,21.1 15,20C15,18.89 15.89,18 17,18M1,2H4.27L5.21,4H20A1,1 0 0,1 21,5C21,5.17 20.95,5.34 20.88,5.5L17.3,11.97C16.96,12.58 16.3,13 15.55,13H8.1L7.2,14.63L7.17,14.75A0.25,0.25 0 0,0 7.42,15H19V17H7C5.89,17 5,16.1 5,15C5,14.65 5.09,14.32 5.24,14.04L6.6,11.59L3,4H1V2Z"/></svg>
                            </div>
                            <div>
                                <div class="order-info-label">تعداد محصول</div>
                                <div class="order-info-value"><?= $order['items_count'] ?? 0 ?> عدد</div>
                            </div>
                        </div>

                        <div class="order-info">
                            <div class="order-info-icon">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z"/></svg>
                            </div>
                            <div>
                                <div class="order-info-label">آدرس</div>
                                <div class="order-info-value"><?= mb_substr(escape($order['address'] ?? 'نامشخص'), 0, 20) ?>...</div>
                            </div>
                        </div>

                        <div class="order-info">
                            <div class="order-info-icon">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M6.62,10.79C8.06,13.62 10.38,15.94 13.21,17.38L15.41,15.18C15.69,14.9 16.08,14.82 16.43,14.93C17.55,15.3 18.75,15.5 20,15.5A1,1 0 0,1 21,16.5V20A1,1 0 0,1 20,21A17,17 0 0,1 3,4A1,1 0 0,1 4,3H7.5A1,1 0 0,1 8.5,4C8.5,5.25 8.7,6.45 9.07,7.57C9.18,7.92 9.1,8.31 8.82,8.59L6.62,10.79Z"/></svg>
                            </div>
                            <div>
                                <div class="order-info-label">تلفن</div>
                                <div class="order-info-value"><?= escape($order['phone'] ?? 'نامشخص') ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="order-footer">
                        <div class="order-total">
                            <?= number_format($order['total_price'] ?? 0) ?> تومان
                        </div>
                        <div class="order-actions">
                            <a href="order-detail.php?id=<?= $order['id'] ?>" class="btn btn-primary">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9M12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z"/></svg>
                                جزئیات
                            </a>
                            <?php if ($order_status === 'pending'): ?>
                                <button class="btn btn-secondary">
                                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z"/></svg>
                                    لغو سفارش
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<script>
// دارک مود
const darkToggle=document.getElementById('darkToggle');
const darkIcon=document.getElementById('darkIcon');
const MOON='<path d="M17.75,4.09L15.22,6.03L16.13,9.09L13.5,7.28L10.87,9.09L11.78,6.03L9.25,4.09L12.44,4L13.5,1L14.56,4L17.75,4.09M21.25,11L19.61,12.25L20.2,14.23L18.5,13.06L16.8,14.23L17.39,12.25L15.75,11L17.81,10.95L18.5,9L19.19,10.95L21.25,11M18.97,15.95C19.8,15.87 20.69,17.05 20.16,17.8C19.84,18.25 19.5,18.67 19.08,19.07C15.17,23 8.84,23 4.94,19.07C1.03,15.17 1.03,8.83 4.94,4.93C5.34,4.53 5.76,4.17 6.21,3.85C6.96,3.32 8.14,4.21 8.06,5.04C7.79,7.9 8.75,10.87 10.95,13.06C13.14,15.26 16.1,16.22 18.97,15.95M17.33,17.97C14.5,17.81 11.7,16.64 9.53,14.5C7.36,12.31 6.2,9.5 6.04,6.68C3.23,9.82 3.34,14.64 6.35,17.66C9.37,20.67 14.19,20.78 17.33,17.97Z"/>';
const SUN='<path d="M12,8A4,4 0 0,0 8,12A4,4 0 0,0 12,16A4,4 0 0,0 16,12A4,4 0 0,0 12,8M12,18A6,6 0 0,1 6,12A6,6 0 0,1 12,6A6,6 0 0,1 18,12A6,6 0 0,1 12,18M20,8.69V4H15.31L12,0.69L8.69,4H4V8.69L0.69,12L4,15.31V20H8.69L12,23.31L15.31,20H20V15.31L23.31,12L20,8.69Z"/>';
if(localStorage.getItem('darkMode')==='enabled'){document.body.classList.add('dark');darkIcon.innerHTML=SUN}
darkToggle.onclick=()=>{
    const d=document.body.classList.toggle('dark');
    localStorage.setItem('darkMode',d?'enabled':'disabled');
    darkIcon.innerHTML=d?SUN:MOON;
};
</script>
</body>
</html>