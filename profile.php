<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once("./include/config.php");
require_once("./include/db.php");

if (!isset($_SESSION['member_id'])) { header("Location: login.php"); exit(); }
$user_id = $_SESSION['member_id'];
$user = fetchOne($db, "SELECT * FROM members WHERE id = ?", [$user_id]);
if (!$user) { session_destroy(); header("Location: login.php"); exit(); }

$success = $error = '';

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['upload_avatar'])) {
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error']===UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext,['jpg','jpeg','png','gif','webp'])) $error='فرمت فایل مجاز نیست.';
        elseif ($_FILES['avatar']['size'] > 3*1024*1024) $error='حجم بیش از ۳MB است.';
        else {
            $dir='./upload/avatars/'; if(!is_dir($dir)) mkdir($dir,0755,true);
            $fname='avatar_'.$user_id.'_'.time().'.'.$ext;
            if (move_uploaded_file($_FILES['avatar']['tmp_name'],$dir.$fname)) {
                if (!empty($user['avatar'])&&file_exists($user['avatar'])) unlink($user['avatar']);
                executeQuery($db,"UPDATE members SET avatar=? WHERE id=?",[$dir.$fname,$user_id]);
                $user['avatar']=$dir.$fname; $_SESSION['member_avatar']=$dir.$fname; $success='تصویر پروفایل بروزرسانی شد.';
            } else $error='خطا در آپلود فایل.';
        }
    } else $error='فایلی انتخاب نشده.';
}
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['delete_avatar'])) {
    if (!empty($user['avatar'])&&file_exists($user['avatar'])) unlink($user['avatar']);
    executeQuery($db,"UPDATE members SET avatar=NULL WHERE id=?",[$user_id]);
    $user['avatar']=null; $_SESSION['member_avatar']=null; $success='تصویر حذف شد.';
}
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update_info'])) {
    $name=trim($_POST['name']??''); $phone=trim($_POST['phone']??'');
    if (!$name) $error='نام نمی‌تواند خالی باشد.';
    else {
        executeQuery($db,"UPDATE members SET name=?,phone=? WHERE id=?",[$name,$phone?:null,$user_id]);
        $_SESSION['member_name']=$name; $user['name']=$name; $user['phone']=$phone; $success='اطلاعات با موفقیت ذخیره شد.';
    }
}
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['change_password'])) {
    $cur=trim($_POST['current_password']??''); $new=trim($_POST['new_password']??''); $con=trim($_POST['confirm_password']??'');
    if (!$cur||!$new||!$con) $error='همه فیلدها الزامی است.';
    elseif (!password_verify($cur,$user['password'])) $error='رمز فعلی اشتباه است.';
    elseif (mb_strlen($new)<6) $error='رمز جدید حداقل ۶ کاراکتر باشد.';
    elseif ($new!==$con) $error='رمز جدید و تکرار آن یکسان نیستند.';
    else { executeQuery($db,"UPDATE members SET password=? WHERE id=?",[password_hash($new,PASSWORD_BCRYPT),$user_id]); $success='رمز عبور تغییر کرد.'; }
}

$total_orders=0; $total_spent=0;
try { $od=fetchAll($db,"SELECT total_price FROM orders WHERE member_id=?",[$user_id]); $total_orders=count($od); $total_spent=(int)array_sum(array_column($od,'total_price')); } catch(Exception $e){}

$av_letter   = mb_substr($user['name'],0,1);
$member_since= date('Y/m/d', strtotime($user['created_at']??'now'));
$avatar_src  = (!empty($user['avatar'])&&file_exists($user['avatar'])) ? $user['avatar'] : null;
$active_tab  = $_GET['tab'] ?? 'info';
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>پروفایل | <?=escape(SITE_NAME)?></title>
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
  margin-left: auto; /* push to left in RTL = right visually */
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

.topbar-search {
  flex: 1; max-width: 600px; position: relative;
}
.topbar-search input {
  width: 100%; padding: .6rem 2.6rem .6rem 1rem;
  border: 1.5px solid var(--brd); border-radius: 50px;
  font-size: .86rem; font-family: inherit;
  color: var(--tx); background: var(--surf2);
  outline: none; transition: all .22s;
}
.topbar-search input:focus { border-color: var(--gr); box-shadow: 0 0 0 3px var(--grl2) }
.topbar-search input::placeholder { color: var(--tx4) }
.topbar-search-ic {
  position: absolute; right: .9rem; top: 50%; transform: translateY(-50%);
  width: 16px; height: 16px; color: var(--tx4); pointer-events: none;
}

.topbar-actions { display: flex; align-items: center; gap: .6rem; flex-shrink: 0 }
.t-icon-btn {
  width: 38px; height: 38px; border-radius: 10px;
  border: 1.5px solid var(--brd); background: var(--surf2);
  color: var(--tx3); cursor: pointer; display: flex; align-items: center; justify-content: center;
  transition: all .2s; text-decoration: none; position: relative;
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
.t-user-btn svg { width: 15px; height: 15px }
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

/* theme btn in sidebar */
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

/* ══ MAIN LAYOUT ══ */
.layout {
  margin-right: var(--sb-w);
  padding: 1.75rem 1.75rem 4rem;
  min-height: calc(100vh - var(--topbar-h));
}
.layout-inner { max-width: 1060px; margin: 0 auto }

/* ══ ALERT ══ */
.alert {
  display: flex; align-items: center; gap: .65rem;
  padding: .9rem 1.1rem; border-radius: var(--r);
  font-size: .86rem; font-weight: 600; margin-bottom: 1.5rem;
  border: 1px solid; animation: fadeUp .3s ease;
}
@keyframes fadeUp { from { opacity:0; transform:translateY(-8px) } to { opacity:1; transform:none } }
.alert svg { width: 16px; height: 16px; flex-shrink: 0 }
.alert-ok  { background: var(--grl); color: var(--gr2); border-color: var(--grbdr) }
.alert-err { background: var(--redl); color: var(--red); border-color: rgba(239,68,68,.2) }

/* ══ PAGE TITLE BAR ══ */
.page-title-bar {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 1.5rem; gap: 1rem;
}
.page-title { font-size: 1.3rem; font-weight: 800; color: var(--tx) }
.page-breadcrumb {
  font-size: .78rem; color: var(--tx3);
  display: flex; align-items: center; gap: .35rem;
}
.page-breadcrumb a { color: var(--gr); text-decoration: none; font-weight: 600 }
.page-breadcrumb a:hover { text-decoration: underline }

/* ══ TWO-COL GRID ══ */
.two-col { display: grid; grid-template-columns: 1fr 2fr; gap: 1.25rem; align-items: start }

/* ══ CARD ══ */
.card {
  background: var(--surf); border: 1px solid var(--brd);
  border-radius: var(--rl); box-shadow: var(--sh);
  overflow: hidden;
}
.card-head {
  padding: 1.2rem 1.5rem; border-bottom: 1px solid var(--brd);
  display: flex; align-items: center; gap: .75rem;
}
.card-head-ic {
  width: 38px; height: 38px; border-radius: 10px;
  background: var(--grl); border: 1px solid var(--grbdr);
  display: flex; align-items: center; justify-content: center; color: var(--gr); flex-shrink: 0;
}
.card-head-ic svg { width: 18px; height: 18px }
.card-head-title { font-size: .95rem; font-weight: 800; color: var(--tx) }
.card-head-sub { font-size: .74rem; color: var(--tx3); margin-top: .1rem }
.card-body { padding: 1.5rem }

/* ══ AVATAR CARD ══ */
.av-center { text-align: center; padding: 2rem 1.5rem }
.av-ring {
  display: inline-block; position: relative; cursor: pointer; margin-bottom: 1.25rem;
}
.av-circle {
  width: 100px; height: 100px; border-radius: 50%; overflow: hidden;
  border: 3px solid var(--gr); box-shadow: 0 0 0 4px var(--grl), var(--sh-gr);
  background: linear-gradient(135deg, var(--gr2), var(--gr3));
  display: flex; align-items: center; justify-content: center;
  transition: transform .28s;
}
.av-ring:hover .av-circle { transform: scale(1.05) }
.av-circle img { width:100%; height:100%; object-fit:cover }
.av-letter-big { font-size: 2.5rem; font-weight: 900; color: #fff }
.av-edit-dot {
  position: absolute; bottom: 3px; right: 3px;
  width: 28px; height: 28px; border-radius: 50%;
  background: var(--gr); border: 3px solid var(--surf);
  display: flex; align-items: center; justify-content: center; color: #fff;
  box-shadow: var(--sh-gr); transition: transform .2s;
}
.av-ring:hover .av-edit-dot { transform: rotate(15deg) }
.av-edit-dot svg { width: 12px; height: 12px }

.av-name { font-size: 1.1rem; font-weight: 800; color: var(--tx); margin-bottom: .2rem }
.av-email { font-size: .8rem; color: var(--tx3); margin-bottom: 1rem }
.av-tags { display: flex; gap: .4rem; flex-wrap: wrap; justify-content: center; margin-bottom: 1.5rem }
.av-tag {
  display: inline-flex; align-items: center; gap: .3rem;
  padding: .26rem .7rem; border-radius: 50px; font-size: .7rem; font-weight: 700;
}
.av-tag-g { background: var(--grl); color: var(--gr2); border: 1px solid var(--grbdr) }
.av-tag-o { background: var(--orgl); color: var(--org) }
.av-tag-b { background: var(--bluel); color: var(--blue) }

/* upload btn */
.av-upload-btn {
  display: inline-flex; align-items: center; gap: .5rem;
  padding: .6rem 1.25rem; border-radius: 9px;
  border: 1.5px solid var(--brd); background: var(--surf2);
  color: var(--tx2); font-size: .83rem; font-weight: 700;
  cursor: pointer; font-family: inherit; transition: all .22s;
}
.av-upload-btn:hover { border-color: var(--gr); color: var(--gr); background: var(--grl) }
.av-upload-btn svg { width: 15px; height: 15px }
.av-del-btn {
  display: inline-flex; align-items: center; gap: .5rem;
  padding: .6rem 1.25rem; border-radius: 9px;
  border: 1.5px solid rgba(239,68,68,.25); background: var(--redl);
  color: var(--red); font-size: .83rem; font-weight: 700;
  cursor: pointer; font-family: inherit; transition: all .22s; margin-top: .5rem;
}
.av-del-btn:hover { background: var(--red); color: #fff; border-color: var(--red) }
.av-del-btn svg { width: 15px; height: 15px }
.av-hint { font-size: .72rem; color: var(--tx4); line-height: 1.7; margin-bottom: 1rem }

/* ══ STATS MINI ══ */
.stat-mini-row { display: grid; grid-template-columns: 1fr 1fr; gap: .75rem; margin-top: 1.25rem }
.stat-mini {
  background: var(--surf2); border: 1px solid var(--brd);
  border-radius: 10px; padding: .9rem .75rem; text-align: center;
}
.stat-mini-val { font-size: 1.3rem; font-weight: 900; color: var(--tx) }
.stat-mini-lbl { font-size: .68rem; color: var(--tx3); font-weight: 600 }

/* ══ INFO CARD (right col) ══ */
/* tab nav inside card */
.tab-bar {
  display: flex; border-bottom: 1px solid var(--brd);
  background: var(--surf2);
}
.tab-item {
  flex: 1; display: flex; align-items: center; justify-content: center; gap: .45rem;
  padding: .9rem 1rem; font-size: .84rem; font-weight: 700; color: var(--tx3);
  border-bottom: 2.5px solid transparent; cursor: pointer;
  text-decoration: none; transition: all .2s; white-space: nowrap;
}
.tab-item:hover { color: var(--gr); background: var(--grl) }
.tab-item.on { color: var(--gr2); border-bottom-color: var(--gr); background: var(--surf) }
.tab-item svg { width: 16px; height: 16px }

/* ══ FORM ══ */
.f-section-title {
  font-size: .78rem; font-weight: 800; color: var(--tx3);
  text-transform: uppercase; letter-spacing: .06em;
  margin-bottom: 1rem; padding-bottom: .5rem;
  border-bottom: 1px solid var(--brd);
}
.f-row { display: grid; gap: 1rem; margin-bottom: 1rem }
.f-row-2 { grid-template-columns: 1fr 1fr }
.fg { }
.fg label {
  display: flex; align-items: center; gap: .3rem;
  font-size: .78rem; font-weight: 700; color: var(--tx2); margin-bottom: .42rem;
}
.fg label .req { color: var(--red); font-size: .85em }
.fwrap { position: relative }
.fic { position: absolute; right: .9rem; top: 50%; transform: translateY(-50%); width: 15px; height: 15px; color: var(--tx4); pointer-events: none }
.feye { position: absolute; left: .9rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--tx4); padding: 0; display: flex; transition: color .2s }
.feye:hover { color: var(--gr) }
.feye svg { width: 15px; height: 15px }
.finput {
  width: 100%; padding: .78rem 2.4rem .78rem 2.4rem;
  border: 1.5px solid var(--brd); border-radius: 9px;
  font-size: .875rem; font-family: 'Vazirmatn', inherit;
  color: var(--tx); background: var(--surf);
  outline: none; transition: all .22s;
}
.finput:focus { border-color: var(--gr); box-shadow: 0 0 0 3px var(--grl2) }
.finput:disabled { opacity: .4; cursor: not-allowed; background: var(--surf2) }
.finput::placeholder { color: var(--tx4) }
.finput-hint { font-size: .73rem; color: var(--tx4); margin-top: .35rem; line-height: 1.6 }

/* ══ BUTTONS ══ */
.btn {
  display: inline-flex; align-items: center; gap: .5rem;
  padding: .78rem 1.5rem; border-radius: 9px; font-weight: 700;
  font-size: .875rem; font-family: 'Vazirmatn', inherit; cursor: pointer;
  transition: all .22s; border: none; text-decoration: none;
}
.btn-green { background: var(--gr); color: #fff; box-shadow: var(--sh-gr) }
.btn-green:hover { background: var(--gr2); transform: translateY(-2px); box-shadow: 0 8px 20px rgba(30,58,138,.35) }
.btn svg { width: 15px; height: 15px }
.btn-row { display: flex; justify-content: flex-start; margin-top: 1.5rem }

/* ══ PASSWORD STRENGTH ══ */
.pw-strength { margin-top: .5rem }
.pw-bar { height: 4px; border-radius: 2px; background: var(--brd); overflow: hidden }
.pw-fill { height: 100%; border-radius: 2px; width: 0; transition: width .3s, background .3s }
.pw-label { font-size: .7rem; margin-top: .3rem; font-weight: 600 }

/* ══ RESPONSIVE ══ */
@media(max-width:960px) {
  .two-col { grid-template-columns: 1fr }
  .layout { padding: 1.5rem 1.25rem 4rem }
}
@media(max-width:768px) {
  :root { --sb-w: 60px; --topbar-h: 56px }
  .topbar { padding: 0 .85rem 0 calc(var(--sb-w) + .85rem) }
  .topbar-search { display: none }
  .topbar-logo-text small { display: none }
  .layout { padding: 1rem .85rem 4rem }
  .f-row-2 { grid-template-columns: 1fr }
  .stat-mini-row { grid-template-columns: repeat(2,1fr) }
  .card-body { padding: 1.25rem }
  .page-title { font-size: 1.1rem }
}
@media(max-width:480px) {
  :root { --sb-w: 56px }
  .sb-item, .sb-logout, .sb-theme { width: 40px; height: 40px; border-radius: 10px }
  .sb-label, .sb-logout span, .sb-theme span { display: none }
  .topbar-logo-text { display: none }
  .t-user-btn span:last-child { display: none }
  .layout { padding: .85rem .7rem 4rem }
  .two-col { gap: .85rem }
  .av-center { padding: 1.5rem 1rem }
}
</style>
</head>
<body>

<!-- ══ TOPBAR ══ -->
<header class="topbar">
  <!-- Logo (right side in RTL) -->
  <a href="index.php" class="topbar-logo">
    <div class="topbar-logo-mark">📚</div>
    <div class="topbar-logo-text">
      <?=escape(SITE_NAME)?>
      <small>پنل کاربری</small>
    </div>
  </a>

  <!-- Search -->
  <div class="topbar-search">
    <svg class="topbar-search-ic" viewBox="0 0 24 24" fill="currentColor"><path d="M9.5,3A6.5,6.5 0,0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0,0,1 3,9.5A6.5,6.5 0,0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z"/></svg>
    <input type="text" placeholder="جستجو در سایت...">
  </div>

  <!-- Actions -->
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
  <a href="profile.php" class="sb-item active" title="پیشخوان">
    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,4A4,4 0,0,1 16,8A4,4 0,0,1 12,12A4,4 0,0,1 8,8A4,4 0,0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/></svg>
    <span class="sb-label">پیشخوان</span>
  </a>
  <a href="orders.php" class="sb-item" title="سفارشات">
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

    <?php if($success): ?>
    <div class="alert alert-ok">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/></svg>
      <?=escape($success)?>
    </div>
    <?php elseif($error): ?>
    <div class="alert alert-err">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M13,13H11V7H13M13,17H11V15H13M12,2A10,10 0,0,0 2,12A10,10 0,0,0 12,22A10,10 0,0,0 22,12A10,10 0,0,0 12,2Z"/></svg>
      <?=escape($error)?>
    </div>
    <?php endif; ?>

    <!-- Title -->
    <div class="page-title-bar">
      <h1 class="page-title">جزئیات حساب</h1>
      <div class="page-breadcrumb">
        <a href="index.php">خانه</a>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M15.41,16.58L10.83,12L15.41,7.41L14,6L8,12L14,18L15.41,16.58Z"/></svg>
        <span>پروفایل</span>
      </div>
    </div>

    <!-- TWO-COL LAYOUT -->
    <div class="two-col">

      <!-- ── LEFT COL: Avatar + sheba ── -->
      <div style="display:flex;flex-direction:column;gap:1.25rem">

        <!-- Avatar Card -->
        <div class="card">
          <div class="card-head">
            <div class="card-head-ic">
              <svg viewBox="0 0 24 24" fill="currentColor"><path d="M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0,0,0 3,5V19A2,2 0,0,0 5,21H19A2,2 0,0,0 21,19M8.5,13.5L11,16.51L14.5,12L19,18H5L8.5,13.5Z"/></svg>
            </div>
            <div>
              <div class="card-head-title">تصویر پروفایل</div>
              <div class="card-head-sub">تغییر تصویر نمایه</div>
            </div>
          </div>
          <div class="av-center">
            <label for="qAvIn">
              <div class="av-ring">
                <div class="av-circle">
                  <?php if($avatar_src): ?><img src="<?=escape($avatar_src)?>?v=<?=filemtime($avatar_src)?>" alt=""><?php else: ?><span class="av-letter-big"><?=$av_letter?></span><?php endif; ?>
                </div>
                <div class="av-edit-dot"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18,2.9 17.35,2.9 16.96,3.29L15.12,5.12L18.87,8.87M3,17.25V21H6.75L17.81,9.93L14.06,6.18L3,17.25Z"/></svg></div>
              </div>
            </label>
            <form method="POST" enctype="multipart/form-data" id="qAvForm">
              <input type="file" name="avatar" id="qAvIn" accept="image/*" style="display:none">
              <input type="hidden" name="upload_avatar" value="1">
            </form>

            <div class="av-name"><?=escape($user['name'])?></div>
            <div class="av-email"><?=escape($user['email'])?></div>
            <div class="av-tags">
              <span class="av-tag av-tag-g">✅ کاربر فعال</span>
              <span class="av-tag av-tag-o">📅 <?=$member_since?></span>
              <?php if($total_orders>0): ?><span class="av-tag av-tag-b">🛒 خریدار</span><?php endif; ?>
            </div>

            <p class="av-hint">فقط فایل‌های JPG، JPEG، PNG و WebP.<br>حداکثر حجم ۳MB.</p>

            <form method="POST" enctype="multipart/form-data" id="mainAvForm">
              <input type="file" name="avatar" id="mainAvIn" accept="image/*" style="display:none">
              <label for="mainAvIn" class="av-upload-btn">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9,16V10H5L12,3L19,10H15V16H9M5,20V18H19V20H5Z"/></svg>
                انتخاب تصویر
              </label>
              <input type="hidden" name="upload_avatar" value="1">
            </form>

            <?php if($avatar_src): ?>
            <form method="POST" style="margin-top:.5rem">
              <button type="submit" name="delete_avatar" class="av-del-btn" onclick="return confirm('تصویر حذف شود؟')">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19,4H15.5L14.5,3H9.5L8.5,4H5V6H19M6,19A2,2 0,0,0 8,21H16A2,2 0,0,0 18,19V7H6V19Z"/></svg>
                حذف تصویر
              </button>
            </form>
            <?php endif; ?>

            <!-- Mini Stats -->
            <div class="stat-mini-row">
              <div class="stat-mini">
                <div class="stat-mini-val"><?=$total_orders?></div>
                <div class="stat-mini-lbl">سفارش</div>
              </div>
              <div class="stat-mini">
                <div class="stat-mini-val"><?=$total_spent>0?number_format($total_spent).'<small style="font-size:.55em"> ت</small>':'۰'?></div>
                <div class="stat-mini-lbl">خرید کل</div>
              </div>
            </div>
          </div>
        </div>

      </div><!-- /left col -->

      <!-- ── RIGHT COL: Info form ── -->
      <div class="card">
        <!-- Tab bar -->
        <div class="tab-bar">
          <a href="?tab=info" class="tab-item <?=$active_tab==='info'?'on':''?>">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,4A4,4 0,0,1 16,8A4,4 0,0,1 12,12A4,4 0,0,1 8,8A4,4 0,0,1 12,4Z"/></svg>
            اطلاعات حساب
          </a>
          <a href="?tab=password" class="tab-item <?=$active_tab==='password'?'on':''?>">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,17A2,2 0,0,0 14,15C14,13.89 13.1,13 12,13A2,2 0,0,0 10,15A2,2 0,0,0 12,17M18,8A2,2 0,0,1 20,10V20A2,2 0,0,1 18,22H6A2,2 0,0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0,0,1 12,1A5,5 0,0,1 17,6V8H18Z"/></svg>
            تغییر رمز
          </a>
        </div>

        <div class="card-body">

          <?php if($active_tab !== 'password'): ?>
          <!-- INFO FORM -->
          <form method="POST">
            <p class="f-section-title">ویرایش اطلاعات حساب</p>
            <p style="font-size:.78rem;color:var(--tx3);margin-bottom:1.25rem">ویرایش اطلاعات حساب کاربری</p>

            <div class="f-row f-row-2">
              <div class="fg">
                <label>شماره موبایل <span class="req">*</span></label>
                <div class="fwrap">
                  <svg class="fic" viewBox="0 0 24 24" fill="currentColor"><path d="M6.62,10.79C8.06,13.62 10.38,15.94 13.21,17.38L15.41,15.18C15.69,14.9 16.08,14.82 16.43,14.93C17.55,15.3 18.75,15.5 20,15.5A1,1 0,0,1 21,16.5V20A1,1 0,0,1 20,21A17,17 0,0,1 3,4A1,1 0,0,1 4,3H7.5A1,1 0,0,1 8.5,4C8.5,5.25 8.7,6.45 9.07,7.57C9.18,7.92 9.1,8.31 8.82,8.59L6.62,10.79Z"/></svg>
                  <input class="finput" type="tel" value="<?=escape($user['phone']??'')?>" disabled placeholder="از پروفایل قابل تغییر نیست">
                </div>
              </div>
              <div class="fg">
                <label>نام کامل <span class="req">*</span></label>
                <div class="fwrap">
                  <svg class="fic" viewBox="0 0 24 24" fill="currentColor"><path d="M12,4A4,4 0,0,1 16,8A4,4 0,0,1 12,12A4,4 0,0,1 8,8A4,4 0,0,1 12,4Z"/></svg>
                  <input class="finput" type="text" name="name" value="<?=escape($user['name'])?>" required placeholder="نام و نام خانوادگی">
                </div>
              </div>
            </div>

            <div class="f-row">
              <div class="fg">
                <label>نام نمایشی</label>
                <div class="fwrap">
                  <svg class="fic" viewBox="0 0 24 24" fill="currentColor"><path d="M12,2A2,2 0,0,1 14,4A2,2 0,0,1 12,6A2,2 0,0,1 10,4A2,2 0,0,1 12,2M10,7H14A2,2 0,0,1 16,9V14H14V22H10V14H8V9A2,2 0,0,1 10,7Z"/></svg>
                  <input class="finput" type="text" name="display_name" value="<?=escape($user['name'])?>" placeholder="نامی که در نظرات نمایش داده می‌شود">
                </div>
                <div class="finput-hint">این اسم در حساب کاربری و نظرات دیده خواهد شد.</div>
              </div>
            </div>

            <div class="f-row">
              <div class="fg">
                <label>آدرس ایمیل</label>
                <div class="fwrap">
                  <svg class="fic" viewBox="0 0 24 24" fill="currentColor"><path d="M20,8L12,13L4,8V6L12,11L20,6M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0,0,0 4,20H20A2,2 0,0,0 22,18V6C22,4.89 21.1,4 20,4Z"/></svg>
                  <input class="finput" type="email" value="<?=escape($user['email'])?>" disabled>
                </div>
              </div>
            </div>

            <div class="btn-row">
              <button type="submit" name="update_info" class="btn btn-green">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/></svg>
                ذخیره تغییرات
              </button>
            </div>
          </form>

          <?php else: ?>
          <!-- PASSWORD FORM -->
          <form method="POST">
            <p class="f-section-title">تغییر رمز عبور</p>
            <p style="font-size:.78rem;color:var(--tx3);margin-bottom:1.25rem">
              تغییر گذرواژه<br><small style="color:var(--tx4)">گذرواژه پیشین (در صورتی که قصد تغییر ندارید خالی بگذارید)</small>
            </p>

            <?php foreach([
              ['current_password','p1','رمز عبور فعلی','رمز عبور کنونی را وارد کنید'],
              ['new_password','p2','رمز عبور جدید','حداقل ۶ کاراکتر'],
              ['confirm_password','p3','تکرار رمز جدید','رمز جدید را مجدداً وارد کنید'],
            ] as [$n,$id,$lbl,$ph]): ?>
            <div class="f-row">
              <div class="fg">
                <label><?=$lbl?></label>
                <div class="fwrap">
                  <svg class="fic" viewBox="0 0 24 24" fill="currentColor"><path d="M12,17A2,2 0,0,0 14,15C14,13.89 13.1,13 12,13A2,2 0,0,0 10,15A2,2 0,0,0 12,17M18,8A2,2 0,0,1 20,10V20A2,2 0,0,1 18,22H6A2,2 0,0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0,0,1 12,1A5,5 0,0,1 17,6V8H18Z"/></svg>
                  <input class="finput" type="password" id="<?=$id?>" name="<?=$n?>" placeholder="<?=$ph?>">
                  <button type="button" class="feye" onclick="eyeToggle('<?=$id?>',this)"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,9A3,3 0,0,0 9,12A3,3 0,0,0 12,15A3,3 0,0,0 15,12A3,3 0,0,0 12,9M12,17A5,5 0,0,1 7,12A5,5 0,0,1 12,7A5,5 0,0,1 17,12A5,5 0,0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z"/></svg></button>
                </div>
                <?php if($n==='new_password'): ?>
                <div class="pw-strength">
                  <div class="pw-bar"><div class="pw-fill" id="pwFill"></div></div>
                  <div class="pw-label" id="pwLabel" style="color:var(--tx4)"></div>
                </div>
                <?php endif; ?>
              </div>
            </div>
            <?php endforeach; ?>

            <div class="btn-row">
              <button type="submit" name="change_password" class="btn btn-green">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/></svg>
                ذخیره رمز جدید
              </button>
            </div>
          </form>
          <?php endif; ?>

        </div>
      </div><!-- /right card -->

    </div><!-- /two-col -->
  </div>
</main>

<script>
/* ── Theme ── */
const MOON='<path d="M17.75,4.09L15.22,6.03L16.13,9.09L13.5,7.28L10.87,9.09L11.78,6.03L9.25,4.09L12.44,4L13.5,1L14.56,4L17.75,4.09M21.25,11L19.61,12.25L20.2,14.23L18.5,13.06L16.8,14.23L17.39,12.25L15.75,11L17.81,10.95L18.5,9L19.19,10.95L21.25,11M18.97,15.95C19.8,15.87 20.69,17.05 20.16,17.8C19.84,18.25 19.5,18.67 19.08,19.07C15.17,23 8.84,23 4.94,19.07C1.03,15.17 1.03,8.83 4.94,4.93C5.34,4.53 5.76,4.17 6.21,3.85C6.96,3.32 8.14,4.21 8.06,5.04C7.79,7.9 8.75,10.87 10.95,13.06C13.14,15.26 16.1,16.22 18.97,15.95M17.33,17.97C14.5,17.81 11.7,16.64 9.53,14.5C7.36,12.31 6.2,9.5 6.04,6.68C3.23,9.82 3.34,14.64 6.35,17.66C9.37,20.67 14.19,20.78 17.33,17.97Z"/>';
const SUN='<path d="M12,8A4,4 0,0,0 8,12A4,4 0,0,0 12,16A4,4 0,0,0 16,12A4,4 0,0,0 12,8M12,18A6,6 0,0,1 6,12A6,6 0,0,1 12,6A6,6 0,0,1 18,12A6,6 0,0,1 12,18M20,8.69V4H15.31L12,0.69L8.69,4H4V8.69L0.69,12L4,15.31V20H8.69L12,23.31L15.31,20H20V15.31L23.31,12L20,8.69Z"/>';
const html=document.documentElement, ico=document.getElementById('themeIco');
function setT(d){html.dataset.theme=d?'dark':'light';ico.innerHTML=d?SUN:MOON;localStorage.setItem('kn_t',d?'1':'0')}
setT(localStorage.getItem('kn_t')==='1');
document.getElementById('themeBtn').onclick=()=>setT(html.dataset.theme!=='dark');

/* ── Password eye ── */
const EO='<path d="M12,9A3,3 0,0,0 9,12A3,3 0,0,0 12,15A3,3 0,0,0 15,12A3,3 0,0,0 12,9M12,17A5,5 0,0,1 7,12A5,5 0,0,1 12,7A5,5 0,0,1 17,12A5,5 0,0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z"/>';
const EC='<path d="M11.83,9L15,12.16C15,12.11 15,12.05 15,12A3,3 0,0,0 12,9C11.94,9 11.89,9 11.83,9M7.53,9.8L9.08,11.35C9.03,11.56 9,11.77 9,12A3,3 0,0,0 12,15C12.22,15 12.44,14.97 12.65,14.92L14.2,16.47C13.53,16.8 12.79,17 12,17A5,5 0,0,1 7,12C7,11.21 7.2,10.47 7.53,9.8M2,4.27L4.28,6.55L4.73,7C3.08,8.3 1.78,10 1,12C2.73,16.39 7,19.5 12,19.5C13.55,19.5 15.03,19.2 16.38,18.66L16.81,19.08L19.73,22L21,20.73L3.27,3M12,7C14.76,7 17,9.24 17,12C17,12.64 16.87,13.26 16.64,13.82L19.57,16.75C21.07,15.5 22.27,13.86 23,12C21.27,7.61 17,4.5 12,4.5C10.6,4.5 9.26,4.75 8,5.2L10.17,7.35C10.74,7.13 11.35,7 12,7Z"/>';
function eyeToggle(id,btn){const el=document.getElementById(id),h=el.type==='password';el.type=h?'text':'password';btn.querySelector('svg').innerHTML=h?EC:EO}

/* ── Password strength ── */
const pwIn=document.getElementById('p2'),fill=document.getElementById('pwFill'),lbl=document.getElementById('pwLabel');
if(pwIn){
  pwIn.addEventListener('input',function(){
    const v=this.value,l=v.length;
    let s=0,c='',t='';
    if(l>=6)s+=25; if(l>=10)s+=25;
    if(/[A-Z]/.test(v)||/[a-z]/.test(v))s+=15;
    if(/[0-9]/.test(v))s+=15; if(/[^A-Za-z0-9]/.test(v))s+=20;
    if(s<=25){c='#ef4444';t='ضعیف'}else if(s<=55){c='#f59e0b';t='متوسط'}else if(s<=80){c='#3b82f6';t='خوب'}else{c=getComputedStyle(document.documentElement).getPropertyValue('--gr');t='قوی'}
    fill.style.width=s+'%'; fill.style.background=c; lbl.textContent=l>0?t:''; lbl.style.color=c;
  });
}

/* ── Quick avatar ── */
document.getElementById('qAvIn')?.addEventListener('change',function(){if(this.files?.length)document.getElementById('qAvForm').submit()});
/* ── Main avatar form auto-submit preview ── */
document.getElementById('mainAvIn')?.addEventListener('change',function(){if(this.files?.length)document.getElementById('mainAvForm').submit()});
</script>
</body>
</html>