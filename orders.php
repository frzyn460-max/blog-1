<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once("./include/config.php");
require_once("./include/db.php");

if (!isset($_SESSION['member_id'])) { header("Location: login.php"); exit(); }
$user_id = $_SESSION['member_id'];
$user = fetchOne($db, "SELECT * FROM members WHERE id = ?", [$user_id]);
if (!$user) { session_destroy(); header("Location: login.php"); exit(); }

$sf = $_GET['status'] ?? 'all';
$sq = trim($_GET['search'] ?? '');
$wc = ["member_id = ?"]; $ps = [$user_id];
if ($sf !== 'all') { $wc[] = "status = ?"; $ps[] = $sf; }
if ($sq) { $sp = "%$sq%"; $wc[] = "(order_number LIKE ? OR id LIKE ?)"; $ps[] = $sp; $ps[] = $sp; }
try { $orders = fetchAll($db,"SELECT * FROM orders WHERE ".implode(" AND ",$wc)." ORDER BY created_at DESC",$ps); }
catch(Exception $e) { $orders = []; }

$all = fetchAll($db,"SELECT status,total_price FROM orders WHERE member_id=?",[$user_id]) ?? [];
$total = count($all); $pending = $done = $cancelled = $processing = 0; $amount = 0;
foreach($all as $o){
  $amount += $o['total_price']??0;
  switch($o['status']??'pending'){
    case 'pending':    $pending++;    break;
    case 'processing': $processing++; break;
    case 'completed':  $done++;       break;
    case 'cancelled':  $cancelled++;  break;
  }
}

$avatar_src = (!empty($user['avatar'])&&file_exists($user['avatar'])) ? $user['avatar'] : null;
$av_letter  = mb_substr($user['name'],0,1);
$sm = [
  'pending'    => ['label'=>'در انتظار',  'cls'=>'org'],
  'processing' => ['label'=>'در پردازش', 'cls'=>'blue'],
  'completed'  => ['label'=>'تکمیل شده', 'cls'=>'grn'],
  'cancelled'  => ['label'=>'لغو شده',   'cls'=>'red'],
];
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>سفارشات | <?=escape(SITE_NAME)?></title>
<style>
@import url('https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css');
*,*::before,*::after { margin:0; padding:0; box-sizing:border-box }

/* ══ TOKENS ══ */
:root {
  --gr:       #1e3a8a;
  --gr2:      #1e40af;
  --gr3:      #1d4ed8;
  --grl:      #dbeafe;
  --grl2:     rgba(30,58,138,.08);
  --grbdr:    rgba(30,58,138,.2);
  --bg:       #f1f5f9;
  --surf:     #ffffff;
  --surf2:    #f8fafc;
  --surf3:    #f1f5f9;
  --brd:      #e2e8f0;
  --brd2:     #cbd5e1;
  --tx:       #0f172a;
  --tx2:      #1e293b;
  --tx3:      #64748b;
  --tx4:      #94a3b8;
  --red:      #ef4444;
  --redl:     #fef2f2;
  --org:      #f59e0b;
  --orgl:     #fffbeb;
  --blue:     #3b82f6;
  --bluel:    #eff6ff;
  --grn:      #10b981;
  --grnl:     #ecfdf5;
  --sb-w:     72px;
  --topbar-h: 64px;
  --r:        12px;
  --rl:       16px;
  --sh:       0 1px 3px rgba(0,0,0,.08), 0 2px 8px rgba(0,0,0,.05);
  --sh-md:    0 4px 12px rgba(0,0,0,.1), 0 8px 24px rgba(0,0,0,.07);
  --sh-gr:    0 4px 16px rgba(30,58,138,.22);
}
[data-theme="dark"] {
  --bg:    #0f1923;
  --surf:  #1a2535;
  --surf2: #1f2d3e;
  --surf3: #243347;
  --brd:   #2d3d52;
  --brd2:  #3a4f68;
  --tx:    #f0f6ff;
  --tx2:   #a8bcce;
  --tx3:   #607590;
  --tx4:   #445a72;
  --grl:   rgba(30,58,138,.18);
  --grl2:  rgba(30,58,138,.1);
  --redl:  rgba(239,68,68,.12);
  --orgl:  rgba(245,158,11,.12);
  --bluel: rgba(59,130,246,.12);
  --grnl:  rgba(16,185,129,.12);
}

html, body { min-height: 100vh }
body {
  font-family: 'Vazirmatn', Tahoma, sans-serif;
  direction: rtl; background: var(--bg); color: var(--tx);
  transition: background .25s, color .25s;
  overflow-x: hidden;
}

/* ══ TOPBAR ══ */
.topbar {
  height: var(--topbar-h);
  background: var(--surf);
  border-bottom: 1px solid var(--brd);
  display: flex; align-items: center;
  padding: 0 1.5rem 0 calc(var(--sb-w) + 1.5rem);
  position: sticky; top: 0; z-index: 200;
  box-shadow: var(--sh);
  gap: 1rem;
}
.topbar-logo {
  display: flex; align-items: center; gap: .5rem;
  text-decoration: none; color: var(--tx); font-weight: 800; font-size: 1rem;
  margin-left: auto;
  white-space: nowrap;
}
.topbar-logo-mark {
  width: 36px; height: 36px; border-radius: 10px;
  background: linear-gradient(135deg,var(--gr2),var(--gr));
  display: flex; align-items: center; justify-content: center;
  font-size: 1.1rem; box-shadow: var(--sh-gr); flex-shrink: 0;
}
.topbar-logo-text { line-height: 1.1 }
.topbar-logo-text small { font-size: .68rem; color: var(--tx3); display: block; font-weight: 400 }

.topbar-search { flex: 1; max-width: 600px; position: relative }
.topbar-search input {
  width: 100%; padding: .6rem 2.6rem .6rem 1rem;
  border: 1.5px solid var(--brd); border-radius: 50px;
  font-size: .86rem; font-family: inherit;
  color: var(--tx); background: var(--surf2);
  outline: none; transition: all .22s;
}
.topbar-search input:focus { border-color: var(--gr); box-shadow: 0 0 0 3px var(--grl2) }
.topbar-search input::placeholder { color: var(--tx4) }
.topbar-search-ic { position: absolute; right: .9rem; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: var(--tx4); pointer-events: none }

.topbar-actions { display: flex; align-items: center; gap: .6rem; flex-shrink: 0 }
.t-icon-btn {
  width: 38px; height: 38px; border-radius: 10px;
  border: 1.5px solid var(--brd); background: var(--surf2);
  color: var(--tx3); cursor: pointer; display: flex; align-items: center; justify-content: center;
  transition: all .2s; text-decoration: none;
}
.t-icon-btn:hover { border-color: var(--gr); color: var(--gr); background: var(--grl) }
.t-icon-btn svg { width: 17px; height: 17px }
.t-user-btn {
  display: flex; align-items: center; gap: .5rem;
  padding: .48rem .9rem .48rem .6rem; border-radius: 10px;
  background: var(--gr); color: #fff; font-weight: 700; font-size: .82rem;
  cursor: pointer; border: none; font-family: inherit; text-decoration: none;
  box-shadow: var(--sh-gr); transition: all .2s;
}
.t-user-btn:hover { background: var(--gr2); transform: translateY(-1px) }
.t-user-av {
  width: 26px; height: 26px; border-radius: 50%; overflow: hidden;
  background: rgba(255,255,255,.3);
  display: flex; align-items: center; justify-content: center;
  font-size: .85rem; font-weight: 800; flex-shrink: 0;
}
.t-user-av img { width:100%; height:100%; object-fit:cover }

/* ══ SIDEBAR ══ */
.sidebar {
  position: fixed; top: 0; right: 0;
  width: var(--sb-w); height: 100vh;
  background: var(--surf);
  border-left: 1px solid var(--brd);
  display: flex; flex-direction: column; align-items: center;
  padding: calc(var(--topbar-h) + 1.25rem) 0 1.5rem;
  z-index: 190; gap: .25rem;
  box-shadow: -2px 0 12px rgba(0,0,0,.05);
}
.sb-item {
  width: 48px; height: 48px; border-radius: 12px;
  display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 3px;
  cursor: pointer; transition: all .22s; position: relative;
  text-decoration: none; color: var(--tx3);
  border: 1.5px solid transparent;
}
.sb-item:hover { background: var(--grl); color: var(--gr); border-color: var(--grbdr) }
.sb-item.active { background: var(--grl); color: var(--gr); border-color: var(--grbdr) }
.sb-item svg { width: 20px; height: 20px }
.sb-label { font-size: .56rem; font-weight: 700; line-height: 1 }
.sb-divider { width: 32px; height: 1px; background: var(--brd); margin: .5rem 0 }
.sb-bottom { margin-top: auto; display: flex; flex-direction: column; align-items: center; gap: .25rem }
.sb-logout {
  width: 48px; height: 48px; border-radius: 12px;
  display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 3px;
  cursor: pointer; color: var(--tx4); text-decoration: none;
  transition: all .22s; border: 1.5px solid transparent;
}
.sb-logout:hover { background: var(--redl); color: var(--red); border-color: rgba(239,68,68,.25) }
.sb-logout svg { width: 19px; height: 19px }
.sb-logout span { font-size: .56rem; font-weight: 700 }
.sb-theme {
  width: 48px; height: 48px; border-radius: 12px;
  border: 1.5px solid transparent; background: none;
  color: var(--tx4); cursor: pointer; font-family: inherit;
  display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 3px;
  transition: all .22s;
}
.sb-theme:hover { background: var(--surf2); color: var(--tx2); border-color: var(--brd) }
.sb-theme svg { width: 19px; height: 19px }
.sb-theme span { font-size: .56rem; font-weight: 700 }

/* ══ LAYOUT ══ */
.layout { margin-right: var(--sb-w); padding: 1.75rem 1.75rem 4rem; min-height: calc(100vh - var(--topbar-h)) }
.layout-inner { max-width: 1060px; margin: 0 auto }

/* ══ PAGE TITLE BAR ══ */
.page-title-bar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; gap: 1rem }
.page-title { font-size: 1.3rem; font-weight: 800; color: var(--tx) }
.page-breadcrumb { font-size: .78rem; color: var(--tx3); display: flex; align-items: center; gap: .35rem }
.page-breadcrumb a { color: var(--gr); text-decoration: none; font-weight: 600 }
.page-breadcrumb a:hover { text-decoration: underline }

/* ══ CARD ══ */
.card { background: var(--surf); border: 1px solid var(--brd); border-radius: var(--rl); box-shadow: var(--sh); overflow: hidden }
.card-head { padding: 1.2rem 1.5rem; border-bottom: 1px solid var(--brd); display: flex; align-items: center; gap: .75rem }
.card-head-ic { width: 38px; height: 38px; border-radius: 10px; background: var(--grl); border: 1px solid var(--grbdr); display: flex; align-items: center; justify-content: center; color: var(--gr); flex-shrink: 0 }
.card-head-ic svg { width: 18px; height: 18px }
.card-head-title { font-size: .95rem; font-weight: 800; color: var(--tx) }
.card-head-sub { font-size: .74rem; color: var(--tx3); margin-top: .1rem }

/* ══ STATS ROW ══ */
.stats-row { display: grid; grid-template-columns: repeat(4,1fr); gap: 1rem; margin-bottom: 1.25rem }
.stat-card {
  background: var(--surf); border: 1px solid var(--brd);
  border-radius: var(--rl); padding: 1.1rem 1rem;
  display: flex; align-items: center; gap: .85rem;
  box-shadow: var(--sh); transition: all .25s;
}
.stat-card:hover { transform: translateY(-3px); box-shadow: var(--sh-md) }
.stat-ic { width: 42px; height: 42px; border-radius: 11px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0 }
.si-ac   { background: var(--grl) }
.si-org  { background: var(--orgl) }
.si-grn  { background: var(--grnl) }
.si-red  { background: var(--redl) }
.stat-val { font-size: 1.25rem; font-weight: 900; color: var(--tx); line-height: 1 }
.stat-lbl { font-size: .7rem; color: var(--tx3); font-weight: 600; margin-top: .15rem }

/* ══ CONTROLS ══ */
.ctrl-bar { display: flex; align-items: center; gap: .85rem; flex-wrap: wrap; padding: 1.1rem 1.5rem; border-bottom: 1px solid var(--brd) }
.ctrl-srch { flex: 1; min-width: 200px; position: relative }
.ctrl-srch-ic { position: absolute; right: .85rem; top: 50%; transform: translateY(-50%); width: 15px; height: 15px; color: var(--tx4); pointer-events: none }
.ctrl-srch input {
  width: 100%; padding: .72rem .72rem .72rem 2.3rem;
  border: 1.5px solid var(--brd); border-radius: 9px;
  font-size: .85rem; font-family: inherit; color: var(--tx);
  background: var(--surf2); outline: none; transition: all .22s;
}
.ctrl-srch input:focus { border-color: var(--gr); box-shadow: 0 0 0 3px var(--grl2) }
.ctrl-srch input::placeholder { color: var(--tx4) }
.chips { display: flex; gap: .4rem; flex-wrap: wrap }
.chip {
  padding: .45rem .9rem; border-radius: 8px;
  font-size: .79rem; font-weight: 700; font-family: inherit;
  border: 1.5px solid var(--brd); background: transparent;
  color: var(--tx3); cursor: pointer; transition: all .2s; text-decoration: none;
}
.chip:hover { border-color: var(--gr); color: var(--gr); background: var(--grl) }
.chip.on { background: var(--gr); border-color: var(--gr); color: #fff; box-shadow: var(--sh-gr) }

/* ══ ORDER ITEMS ══ */
.ord-list { display: flex; flex-direction: column; gap: 0; }

.ord {
  border-bottom: 1px solid var(--brd);
  transition: background .2s;
  animation: fadeUp .3s ease both;
}
.ord:last-child { border-bottom: none }
.ord:hover { background: var(--surf2) }
@keyframes fadeUp { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:none} }

.ord-stripe { height: 3px }
.s-org  { background: linear-gradient(90deg,#f59e0b,#fbbf24) }
.s-blue { background: linear-gradient(90deg,#3b82f6,#818cf8) }
.s-grn  { background: linear-gradient(90deg,#10b981,#34d399) }
.s-red  { background: linear-gradient(90deg,#ef4444,#f87171) }

.ord-inner { padding: 1.25rem 1.5rem }
.ord-head {
  display: flex; align-items: center; justify-content: space-between;
  gap: 1rem; margin-bottom: 1rem; flex-wrap: wrap;
}
.ord-meta { display: flex; align-items: center; gap: .7rem }
.ord-meta-ic {
  width: 38px; height: 38px; border-radius: 10px;
  background: var(--grl); border: 1px solid var(--grbdr);
  display: flex; align-items: center; justify-content: center; color: var(--gr); flex-shrink: 0;
}
.ord-meta-ic svg { width: 17px; height: 17px }
.ord-no { font-size: .95rem; font-weight: 800; color: var(--tx) }
.ord-date { font-size: .72rem; color: var(--tx4); margin-top: .15rem }

.badge {
  display: inline-flex; align-items: center; gap: .35rem;
  padding: .4rem .9rem; border-radius: 8px;
  font-size: .76rem; font-weight: 700; border: 1px solid; flex-shrink: 0;
}
.badge svg { width: 13px; height: 13px }
.b-org  { background: var(--orgl); color: var(--org); border-color: rgba(245,158,11,.22) }
.b-blue { background: var(--bluel); color: var(--blue); border-color: rgba(59,130,246,.22) }
.b-grn  { background: var(--grnl); color: var(--grn); border-color: rgba(16,185,129,.22) }
.b-red  { background: var(--redl); color: var(--red); border-color: rgba(239,68,68,.2) }

.ord-info {
  display: grid; grid-template-columns: repeat(3,1fr); gap: .65rem;
  background: var(--surf2); border: 1px solid var(--brd);
  border-radius: 10px; padding: .9rem 1.1rem; margin-bottom: 1rem;
}
.oi { display: flex; align-items: center; gap: .55rem }
.oi-ic { width: 32px; height: 32px; border-radius: 9px; background: var(--grl); display: flex; align-items: center; justify-content: center; color: var(--gr); flex-shrink: 0 }
.oi-ic svg { width: 14px; height: 14px }
.oi-lbl { font-size: .66rem; color: var(--tx4); font-weight: 600 }
.oi-val { font-size: .84rem; font-weight: 700; color: var(--tx); margin-top: .1rem }

.ord-foot { display: flex; align-items: center; justify-content: space-between; gap: .85rem; flex-wrap: wrap }
.ord-price { display: flex; align-items: baseline; gap: .3rem }
.ord-price-n { font-size: 1.35rem; font-weight: 900; color: var(--gr) }
.ord-price-u { font-size: .75rem; color: var(--tx3); font-weight: 600 }
.ord-btns { display: flex; gap: .5rem; flex-wrap: wrap }

.btn {
  display: inline-flex; align-items: center; gap: .5rem;
  padding: .68rem 1.3rem; border-radius: 9px; font-weight: 700;
  font-size: .84rem; font-family: inherit; cursor: pointer;
  transition: all .22s; border: none; text-decoration: none;
}
.btn-ac { background: var(--gr); color: #fff; box-shadow: var(--sh-gr) }
.btn-ac:hover { background: var(--gr2); transform: translateY(-2px); box-shadow: 0 8px 20px rgba(30,58,138,.35) }
.btn-ol { background: var(--surf2); color: var(--tx2); border: 1.5px solid var(--brd) }
.btn-ol:hover { border-color: var(--red); color: var(--red) }
.btn svg { width: 14px; height: 14px }

/* ══ EMPTY ══ */
.empty { text-align: center; padding: 4rem 2rem }
.empty-em { font-size: 4.5rem; display: block; margin-bottom: 1.1rem; opacity: .18; animation: fl 3.5s ease-in-out infinite }
@keyframes fl { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-12px)} }
.empty-title { font-size: 1.25rem; font-weight: 900; color: var(--tx); margin-bottom: .4rem }
.empty-desc { color: var(--tx4); font-size: .85rem; margin-bottom: 1.5rem; line-height: 1.8 }
.empty-cta {
  display: inline-flex; align-items: center; gap: .5rem;
  padding: .85rem 1.75rem; border-radius: 50px;
  background: var(--gr); color: #fff; font-weight: 700;
  text-decoration: none; box-shadow: var(--sh-gr); transition: all .28s;
}
.empty-cta:hover { background: var(--gr2); transform: translateY(-2px) }

/* ══ RESPONSIVE ══ */
@media(max-width:960px) { .stats-row { grid-template-columns: repeat(2,1fr) } .layout { padding: 1.5rem 1.25rem 4rem } }
@media(max-width:768px) {
  :root { --sb-w: 60px; --topbar-h: 56px }
  .topbar { padding: 0 .85rem 0 calc(var(--sb-w) + .85rem) }
  .topbar-search { display: none }
  .topbar-logo-text small { display: none }
  .layout { padding: 1rem .85rem 4rem }
  .ord-info { grid-template-columns: 1fr 1fr }
  .ord-head { flex-direction: column; align-items: flex-start }
  .ord-foot { flex-direction: column; align-items: flex-start }
  .ord-btns { width: 100% }
  .btn { flex: 1; justify-content: center }
}
@media(max-width:540px) {
  .stats-row { grid-template-columns: repeat(2,1fr) }
  .ctrl-bar { flex-direction: column; align-items: stretch }
  .chips { justify-content: center }
  .ord-info { grid-template-columns: 1fr }
  .ord-inner { padding: 1rem 1rem }
}
@media(max-width:480px) {
  :root { --sb-w: 56px }
  .sb-item, .sb-logout, .sb-theme { width: 40px; height: 40px; border-radius: 10px }
  .sb-label, .sb-logout span, .sb-theme span { display: none }
  .topbar-logo-text { display: none }
  .t-user-btn span:last-child { display: none }
  .layout { padding: .85rem .7rem 4rem }
}
</style>
</head>
<body>

<!-- ══ TOPBAR ══ -->
<header class="topbar">
  <a href="index.php" class="topbar-logo">
    <div class="topbar-logo-mark">📚</div>
    <div class="topbar-logo-text">
      <?=escape(SITE_NAME)?>
      <small>پنل کاربری</small>
    </div>
  </a>
  <div class="topbar-search">
    <svg class="topbar-search-ic" viewBox="0 0 24 24" fill="currentColor"><path d="M9.5,3A6.5,6.5 0,0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0,0,1 3,9.5A6.5,6.5 0,0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z"/></svg>
    <input type="text" placeholder="جستجو در سایت...">
  </div>
  <div class="topbar-actions">
    <a href="#" class="t-icon-btn" title="اعلان‌ها">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M21,19V20H3V19L5,17V11C5,7.9 7.03,5.17 10,4.29V4A2,2 0,0,1 12,2A2,2 0,0,1 14,4V4.29C16.97,5.17 19,7.9 19,11V17L21,19M14,21A2,2 0,0,1 12,23A2,2 0,0,1 10,21"/></svg>
    </a>
    <a href="cart.php" class="t-icon-btn" title="سبد خرید">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17,18A2,2 0,0,1 19,20A2,2 0,0,1 17,22C15.89,22 15,21.1 15,20C15,18.89 15.89,18 17,18M1,2H4.27L5.21,4H20A1,1 0,0,1 21,5L17.3,11.97C16.96,12.58 16.3,13 15.55,13H8.1L7.2,14.63L7.17,14.75A.25.25 0,0,0 7.42,15H19V17H7C5.89,17 5,16.1 5,15L6.6,11.59L3,4H1V2Z"/></svg>
    </a>
    <a href="profile.php" class="t-user-btn">
      <div class="t-user-av">
        <?php if($avatar_src): ?><img src="<?=escape($avatar_src)?>" alt=""><?php else: ?><?=$av_letter?><?php endif; ?>
      </div>
      حساب کاربری
    </a>
  </div>
</header>

<!-- ══ SIDEBAR ══ -->
<aside class="sidebar">
  <a href="profile.php" class="sb-item" title="پیشخوان">
    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,4A4,4 0,0,1 16,8A4,4 0,0,1 12,12A4,4 0,0,1 8,8A4,4 0,0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/></svg>
    <span class="sb-label">پیشخوان</span>
  </a>
  <a href="orders.php" class="sb-item active" title="سفارشات">
    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17,18A2,2 0,0,1 19,20A2,2 0,0,1 17,22C15.89,22 15,21.1 15,20C15,18.89 15.89,18 17,18M1,2H4.27L5.21,4H20A1,1 0,0,1 21,5L17.3,11.97C16.96,12.58 16.3,13 15.55,13H8.1L7.2,14.63L7.17,14.75A.25.25 0,0,0 7.42,15H19V17H7C5.89,17 5,16.1 5,15L6.6,11.59L3,4H1V2Z"/></svg>
    <span class="sb-label">سفارشات</span>
  </a>
  <a href="addresses.php" class="sb-item" title="آدرس‌ها">
    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,11.5A2.5,2.5 0,0,1 9.5,9A2.5,2.5 0,0,1 12,6.5A2.5,2.5 0,0,1 14.5,9A2.5,2.5 0,0,1 12,11.5M12,2A7,7 0,0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0,0,0 12,2Z"/></svg>
    <span class="sb-label">آدرس‌ها</span>
  </a>

  <div class="sb-bottom">
    <button class="sb-theme" id="themeBtn">
      <svg id="themeIco" viewBox="0 0 24 24" fill="currentColor"><path d="M17.75,4.09L15.22,6.03L16.13,9.09L13.5,7.28L10.87,9.09L11.78,6.03L9.25,4.09L12.44,4L13.5,1L14.56,4L17.75,4.09M21.25,11L19.61,12.25L20.2,14.23L18.5,13.06L16.8,14.23L17.39,12.25L15.75,11L17.81,10.95L18.5,9L19.19,10.95L21.25,11M18.97,15.95C19.8,15.87 20.69,17.05 20.16,17.8C19.84,18.25 19.5,18.67 19.08,19.07C15.17,23 8.84,23 4.94,19.07C1.03,15.17 1.03,8.83 4.94,4.93C5.34,4.53 5.76,4.17 6.21,3.85C6.96,3.32 8.14,4.21 8.06,5.04C7.79,7.9 8.75,10.87 10.95,13.06C13.14,15.26 16.1,16.22 18.97,15.95M17.33,17.97C14.5,17.81 11.7,16.64 9.53,14.5C7.36,12.31 6.2,9.5 6.04,6.68C3.23,9.82 3.34,14.64 6.35,17.66C9.37,20.67 14.19,20.78 17.33,17.97Z"/></svg>
      <span>تم</span>
    </button>
    <a href="logout.php" class="sb-logout" title="خروج">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M16,17V14H9V10H16V7L21,12L16,17M14,2A2,2 0,0,1 16,4V6H14V4H5V20H14V18H16V20A2,2 0,0,1 14,22H5A2,2 0,0,1 3,20V4A2,2 0,0,1 5,2H14Z"/></svg>
      <span>خروج</span>
    </a>
  </div>
</aside>

<!-- ══ MAIN ══ -->
<main class="layout">
  <div class="layout-inner">

    <div class="page-title-bar">
      <h1 class="page-title">سفارشات من</h1>
      <div class="page-breadcrumb">
        <a href="index.php">خانه</a>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M15.41,16.58L10.83,12L15.41,7.41L14,6L8,12L14,18L15.41,16.58Z"/></svg>
        <span>سفارشات</span>
      </div>
    </div>

    <!-- STATS -->
    <div class="stats-row">
      <div class="stat-card"><div class="stat-ic si-ac">📦</div><div><div class="stat-val"><?=$total?></div><div class="stat-lbl">کل سفارشات</div></div></div>
      <div class="stat-card"><div class="stat-ic si-org">⏳</div><div><div class="stat-val"><?=$pending?></div><div class="stat-lbl">در انتظار</div></div></div>
      <div class="stat-card"><div class="stat-ic si-grn">✅</div><div><div class="stat-val"><?=$done?></div><div class="stat-lbl">تکمیل شده</div></div></div>
      <div class="stat-card"><div class="stat-ic si-ac">💰</div><div><div class="stat-val"><?=number_format($amount)?></div><div class="stat-lbl">تومان خرید</div></div></div>
    </div>

    <!-- ORDERS CARD -->
    <div class="card">
      <div class="card-head">
        <div class="card-head-ic"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M3,3H21V7H3V3M4,8H20V21H4V8M9.5,11A.5.5 0,0,0 9,11.5V13H15V11.5A.5.5 0,0,0 14.5,11H9.5Z"/></svg></div>
        <div>
          <div class="card-head-title">لیست سفارشات</div>
          <div class="card-head-sub">جستجو و فیلتر سفارشات</div>
        </div>
      </div>

      <!-- Controls -->
      <form method="GET" class="ctrl-bar">
        <div class="ctrl-srch">
          <svg class="ctrl-srch-ic" viewBox="0 0 24 24" fill="currentColor"><path d="M9.5,3A6.5,6.5 0,0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0,0,1 3,9.5A6.5,6.5 0,0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z"/></svg>
          <input type="text" name="search" placeholder="جستجو در سفارشات..." value="<?=escape($sq)?>">
        </div>
        <div class="chips">
          <a href="?status=all"        class="chip <?=$sf==='all'?'on':''?>">همه</a>
          <a href="?status=pending"    class="chip <?=$sf==='pending'?'on':''?>">در انتظار</a>
          <a href="?status=processing" class="chip <?=$sf==='processing'?'on':''?>">پردازش</a>
          <a href="?status=completed"  class="chip <?=$sf==='completed'?'on':''?>">تکمیل شده</a>
          <a href="?status=cancelled"  class="chip <?=$sf==='cancelled'?'on':''?>">لغو شده</a>
        </div>
      </form>

      <!-- List -->
      <?php if(empty($orders)): ?>
      <div class="empty">
        <span class="empty-em"><?=$sq||$sf!=='all'?'🔍':'📭'?></span>
        <h2 class="empty-title"><?=$sq||$sf!=='all'?'نتیجه‌ای یافت نشد':'هنوز سفارشی ندارید'?></h2>
        <p class="empty-desc"><?=$sq||$sf!=='all'?'فیلترها را تغییر دهید.':'اولین خریدتان را ثبت کنید!'?></p>
        <a href="<?=$sq||$sf!=='all'?'orders.php':'products.php'?>" class="empty-cta"><?=$sq||$sf!=='all'?'مشاهده همه':'رفتن به فروشگاه'?></a>
      </div>
      <?php else: ?>
      <div class="ord-list">
        <?php
        $sm_map = ['org'=>['s-org','b-org'],'blue'=>['s-blue','b-blue'],'grn'=>['s-grn','b-grn'],'red'=>['s-red','b-red']];
        $ic_p = [
          'pending'    => 'M12,20A8,8 0,0,1 4,12A8,8 0,0,1 12,4A8,8 0,0,1 20,12A8,8 0,0,1 12,20M12,2A10,10 0,0,0 2,12A10,10 0,0,0 12,22A10,10 0,0,0 22,12A10,10 0,0,0 12,2M12.5,7V12.25L17,14.92L16.25,16.15L11,13V7H12.5Z',
          'processing' => 'M12,4V2A10,10 0,0,0 2,12H4A8,8 0,0,1 12,4Z',
          'completed'  => 'M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z',
          'cancelled'  => 'M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z',
        ];
        foreach($orders as $i=>$o):
          $os=$o['status']??'pending'; $m=$sm[$os]??$sm['pending'];
          [$sc,$bc] = $sm_map[$m['cls']] ?? ['s-org','b-org'];
        ?>
        <div class="ord" style="animation-delay:<?=min($i*50,300)?>ms">
          <div class="ord-stripe <?=$sc?>"></div>
          <div class="ord-inner">
            <div class="ord-head">
              <div class="ord-meta">
                <div class="ord-meta-ic"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M3,3H21V7H3V3M4,8H20V21H4V8M9.5,11A.5.5 0,0,0 9,11.5V13H15V11.5A.5.5 0,0,0 14.5,11H9.5Z"/></svg></div>
                <div>
                  <div class="ord-no">سفارش #<?=escape($o['order_number']??$o['id'])?></div>
                  <div class="ord-date"><?=date('Y/m/d · H:i',strtotime($o['created_at']??'now'))?></div>
                </div>
              </div>
              <div class="badge <?=$bc?>">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="<?=$ic_p[$os]??$ic_p['pending']?>"/></svg>
                <?=$m['label']?>
              </div>
            </div>

            <div class="ord-info">
              <div class="oi"><div class="oi-ic"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M17,18A2,2 0,0,1 19,20A2,2 0,0,1 17,22C15.89,22 15,21.1 15,20C15,18.89 15.89,18 17,18M1,2H4.27L5.21,4H20A1,1 0,0,1 21,5L17.3,11.97C16.96,12.58 16.3,13 15.55,13H8.1L7.2,14.63L7.17,14.75A.25.25 0,0,0 7.42,15H19V17H7C5.89,17 5,16.1 5,15L6.6,11.59L3,4H1V2Z"/></svg></div><div><div class="oi-lbl">تعداد</div><div class="oi-val"><?=$o['items_count']??0?> محصول</div></div></div>
              <div class="oi"><div class="oi-ic"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,11.5A2.5,2.5 0,0,1 9.5,9A2.5,2.5 0,0,1 12,6.5A2.5,2.5 0,0,1 14.5,9A2.5,2.5 0,0,1 12,11.5M12,2A7,7 0,0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0,0,0 12,2Z"/></svg></div><div><div class="oi-lbl">آدرس</div><div class="oi-val"><?=mb_substr(escape($o['address']??'ثبت نشده'),0,18)?>...</div></div></div>
              <div class="oi"><div class="oi-ic"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M6.62,10.79C8.06,13.62 10.38,15.94 13.21,17.38L15.41,15.18C15.69,14.9 16.08,14.82 16.43,14.93C17.55,15.3 18.75,15.5 20,15.5A1,1 0,0,1 21,16.5V20A1,1 0,0,1 20,21A17,17 0,0,1 3,4A1,1 0,0,1 4,3H7.5A1,1 0,0,1 8.5,4C8.5,5.25 8.7,6.45 9.07,7.57C9.18,7.92 9.1,8.31 8.82,8.59L6.62,10.79Z"/></svg></div><div><div class="oi-lbl">تلفن</div><div class="oi-val"><?=escape($o['phone']??'ثبت نشده')?></div></div></div>
            </div>

            <div class="ord-foot">
              <div class="ord-price"><span class="ord-price-n"><?=number_format($o['total_price']??0)?></span><span class="ord-price-u">تومان</span></div>
              <div class="ord-btns">
                <a href="order-detail.php?id=<?=$o['id']?>" class="btn btn-ac"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,9A3,3 0,0,0 9,12A3,3 0,0,0 12,15A3,3 0,0,0 15,12A3,3 0,0,0 12,9M12,17A5,5 0,0,1 7,12A5,5 0,0,1 12,7A5,5 0,0,1 17,12A5,5 0,0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z"/></svg>مشاهده جزئیات</a>
                <?php if($os==='pending'): ?><button type="button" class="btn btn-ol"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z"/></svg>لغو</button><?php endif; ?>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div><!-- /card -->

  </div>
</main>

<script>
/* ── Theme (identical to profile.php) ── */
const MOON='<path d="M17.75,4.09L15.22,6.03L16.13,9.09L13.5,7.28L10.87,9.09L11.78,6.03L9.25,4.09L12.44,4L13.5,1L14.56,4L17.75,4.09M21.25,11L19.61,12.25L20.2,14.23L18.5,13.06L16.8,14.23L17.39,12.25L15.75,11L17.81,10.95L18.5,9L19.19,10.95L21.25,11M18.97,15.95C19.8,15.87 20.69,17.05 20.16,17.8C19.84,18.25 19.5,18.67 19.08,19.07C15.17,23 8.84,23 4.94,19.07C1.03,15.17 1.03,8.83 4.94,4.93C5.34,4.53 5.76,4.17 6.21,3.85C6.96,3.32 8.14,4.21 8.06,5.04C7.79,7.9 8.75,10.87 10.95,13.06C13.14,15.26 16.1,16.22 18.97,15.95M17.33,17.97C14.5,17.81 11.7,16.64 9.53,14.5C7.36,12.31 6.2,9.5 6.04,6.68C3.23,9.82 3.34,14.64 6.35,17.66C9.37,20.67 14.19,20.78 17.33,17.97Z"/>';
const SUN='<path d="M12,8A4,4 0,0,0 8,12A4,4 0,0,0 12,16A4,4 0,0,0 16,12A4,4 0,0,0 12,8M12,18A6,6 0,0,1 6,12A6,6 0,0,1 12,6A6,6 0,0,1 18,12A6,6 0,0,1 12,18M20,8.69V4H15.31L12,0.69L8.69,4H4V8.69L0.69,12L4,15.31V20H8.69L12,23.31L15.31,20H20V15.31L23.31,12L20,8.69Z"/>';
const html=document.documentElement, ico=document.getElementById('themeIco');
function setT(d){html.dataset.theme=d?'dark':'light';ico.innerHTML=d?SUN:MOON;localStorage.setItem('kn_t',d?'1':'0')}
setT(localStorage.getItem('kn_t')==='1');
document.getElementById('themeBtn').onclick=()=>setT(html.dataset.theme!=='dark');
</script>
</body>
</html>