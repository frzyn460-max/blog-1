<?php
/**
 * صفحه پروفایل پیشرفته - کتاب نت
 */

if (session_status() === PHP_SESSION_NONE) session_start();
require_once("./include/config.php");
require_once("./include/db.php");

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

$success = '';
$error   = '';

// آپلود آواتار
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_avatar'])) {
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['avatar']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (!in_array($ext, $allowed)) {
            $error = 'فقط فایل‌های JPG, PNG, GIF مجاز هستند.';
        } elseif ($_FILES['avatar']['size'] > 2 * 1024 * 1024) {
            $error = 'حجم فایل نباید بیشتر از ۲ مگابایت باشد.';
        } else {
            $upload_dir = './upload/avatars/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
            
            $new_name = 'avatar_' . $user_id . '_' . time() . '.' . $ext;
            $target = $upload_dir . $new_name;
            
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target)) {
                // حذف آواتار قبلی
                if ($user['avatar'] && file_exists($user['avatar'])) {
                    unlink($user['avatar']);
                }
                executeQuery($db, "UPDATE members SET avatar = ? WHERE id = ?", [$target, $user_id]);
                $user['avatar'] = $target;
                $success = 'تصویر پروفایل با موفقیت آپلود شد.';
            } else {
                $error = 'خطا در آپلود فایل.';
            }
        }
    } else {
        $error = 'لطفاً یک فایل انتخاب کنید.';
    }
}

// حذف آواتار
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_avatar'])) {
    if ($user['avatar'] && file_exists($user['avatar'])) {
        unlink($user['avatar']);
    }
    executeQuery($db, "UPDATE members SET avatar = NULL WHERE id = ?", [$user_id]);
    $user['avatar'] = null;
    $success = 'تصویر پروفایل حذف شد.';
}

// ویرایش اطلاعات
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_info'])) {
    $name  = trim($_POST['name']  ?? '');
    $phone = trim($_POST['phone'] ?? '');
    
    if (!$name) {
        $error = 'نام نمی‌تواند خالی باشد.';
    } else {
        try {
            executeQuery($db, "UPDATE members SET name = ?, phone = ? WHERE id = ?", [$name, $phone ?: null, $user_id]);
            $_SESSION['member_name'] = $name;
            $user['name']  = $name;
            $user['phone'] = $phone;
            $success = 'اطلاعات با موفقیت به‌روزرسانی شد.';
        } catch (PDOException $e) {
            $error = 'خطا در ذخیره اطلاعات.';
        }
    }
}

// تغییر رمز
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current = trim($_POST['current_password'] ?? '');
    $new     = trim($_POST['new_password']     ?? '');
    $confirm = trim($_POST['confirm_password'] ?? '');
    
    if (!$current || !$new || !$confirm) {
        $error = 'لطفاً همه فیلدهای رمز عبور را پر کنید.';
    } elseif (!password_verify($current, $user['password'])) {
        $error = 'رمز عبور فعلی اشتباه است.';
    } elseif (mb_strlen($new) < 6) {
        $error = 'رمز عبور جدید باید حداقل ۶ کاراکتر باشد.';
    } elseif ($new !== $confirm) {
        $error = 'رمز عبور جدید و تکرار آن یکسان نیستند.';
    } else {
        try {
            $hashed = password_hash($new, PASSWORD_BCRYPT);
            executeQuery($db, "UPDATE members SET password = ? WHERE id = ?", [$hashed, $user_id]);
            $success = 'رمز عبور با موفقیت تغییر کرد.';
        } catch (PDOException $e) {
            $error = 'خطا در تغییر رمز عبور.';
        }
    }
}

// آمار کاربر (فرضی - در صورت وجود جداول)
$total_orders = 0;
$total_spent  = 0;
try {
    // اگر جدول سفارشات دارید
    // $orders = fetchAll($db, "SELECT * FROM orders WHERE member_id = ?", [$user_id]);
    // $total_orders = count($orders);
    // $total_spent = array_sum(array_column($orders, 'total_price'));
} catch (Exception $e) {}

$avatar_letter = mb_substr($user['name'], 0, 1);
$member_since = date('Y/m/d', strtotime($user['created_at'] ?? 'now'));
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پروفایل | <?= escape(SITE_NAME) ?></title>
    <style>
@import url('https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css');
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
:root{
    --dp:#0f172a;--mid:#1e3a8a;--br:#3b82f6;--lt:#60a5fa;
    --w:#fff;--g50:#f8fafc;--g100:#f1f5f9;--g200:#e2e8f0;
    --g400:#94a3b8;--g600:#475569;--g800:#1e293b;
    --red:#ef4444;--green:#10b981;--orange:#f59e0b;--purple:#a855f7;
}
html,body{min-height:100%;transition:background .3s,color .3s}
body{
    font-family:'Vazirmatn',Tahoma,Arial,sans-serif;
    direction:rtl;min-height:100vh;
    background:var(--g100);color:var(--g800);
}
body.dark{
    --g100:#0f172a;--g50:#1e293b;--w:#f7fafc;
    --g200:#334155;--g400:#64748b;--g600:#cbd5e1;--g800:#f1f5f9;
    background:#0f172a;color:#f1f5f9;
}

/* ══ header ══ */
.top{
    background:linear-gradient(135deg,var(--dp),var(--mid));
    padding:1.25rem 0;box-shadow:0 4px 20px rgba(0,0,0,.2);
    position:sticky;top:0;z-index:100;
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
.main{max-width:1200px;margin:0 auto;padding:2rem 1.5rem}

/* cover */
.cover{
    background:linear-gradient(135deg,var(--mid),var(--br),var(--purple));
    height:180px;border-radius:22px 22px 0 0;
    position:relative;overflow:hidden;
}
body.dark .cover{background:linear-gradient(135deg,#1e3a8a,#3b82f6,#7c3aed)}
.cover::before{
    content:'';position:absolute;inset:0;
    background:url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="2" fill="rgba(255,255,255,.15)"/></svg>');
}

/* profile card */
.pcard{
    background:var(--w);border-radius:22px;
    box-shadow:0 4px 20px rgba(0,0,0,.08);margin-bottom:2rem;
    position:relative;
}
body.dark .pcard{background:var(--g50);box-shadow:0 4px 20px rgba(0,0,0,.4)}

.pcard-body{padding:0 2rem 2rem;margin-top:-50px;position:relative}

/* avatar */
.av-wrap{text-align:center;margin-bottom:1.5rem}
.av-outer{
    width:130px;height:130px;margin:0 auto;
    border-radius:50%;padding:5px;
    background:linear-gradient(135deg,var(--orange),var(--red));
    box-shadow:0 12px 36px rgba(239,68,68,.35);
    animation:avPop .65s cubic-bezier(.34,1.56,.64,1);
    position:relative;
}
@keyframes avPop{from{transform:scale(.4) rotate(-12deg)} to{transform:scale(1) rotate(0)}}
.av-inner{
    width:100%;height:100%;border-radius:50%;
    background:var(--g100);display:flex;align-items:center;justify-content:center;
    overflow:hidden;position:relative;
}
body.dark .av-inner{background:var(--g200)}
.av-img{width:100%;height:100%;object-fit:cover}
.av-letter{font-size:3rem;font-weight:900;color:var(--g800)}
body.dark .av-letter{color:var(--g100)}

.av-edit{
    position:absolute;bottom:5px;right:5px;
    width:36px;height:36px;border-radius:50%;
    background:var(--br);color:var(--w);border:3px solid var(--w);
    display:flex;align-items:center;justify-content:center;
    cursor:pointer;transition:all .25s;box-shadow:0 4px 12px rgba(59,130,246,.4);
}
body.dark .av-edit{border-color:var(--g50)}
.av-edit:hover{transform:scale(1.1)}
.av-edit svg{width:16px;height:16px}

/* اطلاعات */
.uname{font-size:1.85rem;font-weight:900;color:var(--g800);margin-bottom:.35rem;text-align:center}
body.dark .uname{color:var(--g800)}
.uemail{color:var(--g400);font-size:.95rem;text-align:center;margin-bottom:.25rem}
.ujoin{color:var(--g400);font-size:.82rem;text-align:center;display:flex;align-items:center;justify-content:center;gap:.4rem}
.ujoin svg{width:15px;height:15px}

/* stats */
.stats{
    display:grid;grid-template-columns:repeat(3,1fr);
    gap:1rem;margin-top:1.75rem;padding-top:1.75rem;
    border-top:2px solid var(--g200);
}
body.dark .stats{border-color:var(--g400)}
.stat{text-align:center}
.stat-val{font-size:1.8rem;font-weight:900;color:var(--br);margin-bottom:.25rem}
.stat-label{font-size:.82rem;color:var(--g400)}

/* پیام */
.msg{
    padding:.85rem 1rem;border-radius:11px;
    margin-bottom:1.5rem;font-size:.88rem;font-weight:600;
    display:flex;align-items:center;gap:.6rem;
    animation:slideIn .3s ease;
}
@keyframes slideIn{from{opacity:0;transform:translateY(-7px)} to{opacity:1;transform:none}}
.msg svg{width:17px;height:17px;flex-shrink:0}
.msg-ok{background:rgba(16,185,129,.12);color:var(--green);border:1.5px solid rgba(16,185,129,.25)}
.msg-err{background:rgba(239,68,68,.1);color:var(--red);border:1.5px solid rgba(239,68,68,.2)}

/* tabs */
.tabs{
    display:flex;border-bottom:2px solid var(--g200);
    margin-bottom:2rem;gap:.5rem;overflow-x:auto;
}
body.dark .tabs{border-color:var(--g400)}
.tab{
    padding:.85rem 1.5rem;border:none;background:none;
    font-weight:700;font-size:.92rem;font-family:inherit;
    color:var(--g400);cursor:pointer;position:relative;
    transition:color .25s;white-space:nowrap;
}
.tab.active{color:var(--br)}
.tab.active::after{
    content:'';position:absolute;bottom:-2px;left:0;right:0;
    height:2px;background:var(--br);
}

.tab-content{display:none}
.tab-content.active{display:block;animation:fadeIn .35s ease}
@keyframes fadeIn{from{opacity:0} to{opacity:1}}

/* cards */
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(450px,1fr));gap:1.75rem}

.card{
    background:var(--w);border-radius:18px;
    padding:1.75rem;box-shadow:0 2px 14px rgba(0,0,0,.06);
    transition:box-shadow .3s;
}
body.dark .card{background:var(--g50);box-shadow:0 2px 14px rgba(0,0,0,.3)}
.card:hover{box-shadow:0 8px 30px rgba(0,0,0,.12)}
body.dark .card:hover{box-shadow:0 8px 30px rgba(0,0,0,.5)}

.ch{display:flex;align-items:center;gap:.65rem;margin-bottom:1.4rem}
.ci{
    width:42px;height:42px;border-radius:11px;
    background:linear-gradient(135deg,var(--mid),var(--br));
    display:flex;align-items:center;justify-content:center;
    color:var(--w);box-shadow:0 6px 16px rgba(30,58,138,.25);flex-shrink:0;
}
.ci svg{width:20px;height:20px}
.ct{font-size:1.2rem;font-weight:800;color:var(--g800)}
body.dark .ct{color:var(--g800)}

/* form */
.fg{margin-bottom:1.1rem}
.fg label{display:block;font-weight:700;font-size:.83rem;color:var(--g800);margin-bottom:.42rem}
body.dark .fg label{color:var(--g600)}
.field{position:relative}
.fi{
    position:absolute;right:.8rem;top:50%;transform:translateY(-50%);
    width:16px;height:16px;color:var(--g400);pointer-events:none;transition:color .2s;
}
.fe{
    position:absolute;left:.8rem;top:50%;transform:translateY(-50%);
    background:none;border:none;cursor:pointer;color:var(--g400);padding:0;display:flex;transition:color .2s;
}
.fe:hover{color:var(--br)}
.fe svg{width:16px;height:16px;display:block}
.field input,.field textarea{
    width:100%;padding:.75rem 2.4rem .75rem 2.4rem;
    border:2px solid var(--g200);border-radius:11px;
    font-size:.9rem;font-family:'Vazirmatn',Tahoma,Arial,sans-serif;
    color:var(--g800);background:var(--g50);
    transition:border-color .25s,box-shadow .25s,background .25s;outline:none;
}
body.dark .field input,body.dark .field textarea{background:var(--g200);border-color:var(--g400);color:var(--g800)}
.field input:focus,.field textarea:focus{
    border-color:var(--br);background:var(--w);
    box-shadow:0 0 0 3px rgba(59,130,246,.1);
}
body.dark .field input:focus,body.dark .field textarea:focus{background:var(--g50)}
.field input::placeholder,.field textarea::placeholder{color:var(--g400)}
.field input:focus + .fi{color:var(--br)}
.field textarea{resize:vertical;min-height:90px;padding:.75rem 1rem}

.btn{
    padding:.85rem 1.4rem;border-radius:12px;
    font-weight:700;font-size:.9rem;
    font-family:'Vazirmatn',Tahoma,Arial,sans-serif;
    cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:.55rem;
    transition:all .25s;border:none;text-decoration:none;
}
.btn-pri{
    background:linear-gradient(135deg,var(--mid),var(--br));
    color:var(--w);box-shadow:0 6px 18px rgba(30,58,138,.25);
}
.btn-pri:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(30,58,138,.35)}
.btn-sec{background:var(--g200);color:var(--g800)}
body.dark .btn-sec{background:var(--g400);color:var(--g100)}
.btn-sec:hover{background:var(--g400);color:var(--w)}
.btn-danger{background:var(--red);color:var(--w)}
.btn-danger:hover{background:#dc2626}
.btn svg{width:17px;height:17px}
.cf{display:flex;gap:.7rem;margin-top:1.4rem;flex-wrap:wrap}
.btn-full{width:100%}

/* upload */
.upload-zone{
    border:2px dashed var(--g200);border-radius:12px;
    padding:2rem;text-align:center;cursor:pointer;
    transition:all .25s;background:var(--g50);
}
body.dark .upload-zone{background:var(--g200);border-color:var(--g400)}
.upload-zone:hover{border-color:var(--br);background:rgba(59,130,246,.05)}
.upload-zone svg{width:48px;height:48px;color:var(--g400);margin-bottom:.75rem}
.upload-zone p{color:var(--g400);font-size:.88rem}
.upload-zone input{display:none}

/* responsive */
@media(max-width:1000px){.cards{grid-template-columns:1fr}}
@media(max-width:700px){
    .top-in{justify-content:center;text-align:center}
    .top-act{width:100%;justify-content:center;flex-wrap:wrap}
    .main{padding:1.5rem 1rem}
    .pcard-body{padding:0 1.25rem 1.5rem}
    .cover{height:140px}
    .av-outer{width:100px;height:100px;margin-top:-50px}
    .av-letter{font-size:2.4rem}
    .uname{font-size:1.5rem}
    .stats{grid-template-columns:1fr;gap:.85rem}
    .card{padding:1.4rem}
    .cards{gap:1.25rem}
}
@media(max-width:420px){
    .tbtn span{display:none}
    .tbtn{padding:.5rem}
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

    <!-- پروفایل کارت -->
    <div class="pcard">
        <div class="cover"></div>
        <div class="pcard-body">
            <div class="av-wrap">
                <div class="av-outer">
                    <div class="av-inner">
                        <?php if ($user['avatar'] && file_exists($user['avatar'])): ?>
                            <img src="<?= escape($user['avatar']) ?>" alt="avatar" class="av-img">
                        <?php else: ?>
                            <div class="av-letter"><?= $avatar_letter ?></div>
                        <?php endif; ?>
                    </div>
                    <label for="avatarUpload" class="av-edit" title="تغییر تصویر">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9,16.2L4.8,12L3.4,13.4L9,19L21,7L19.6,5.6L9,16.2Z"/></svg>
                    </label>
                </div>
            </div>

            <div class="uname"><?= escape($user['name']) ?></div>
            <div class="uemail"><?= escape($user['email']) ?></div>
            <div class="ujoin">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9,10H7V12H9V10M13,10H11V12H13V10M17,10H15V12H17V10M19,3H18V1H16V3H8V1H6V3H5C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3M19,19H5V8H19V19Z"/></svg>
                عضو از: <?= $member_since ?>
            </div>

            <div class="stats">
                <div class="stat">
                    <div class="stat-val"><?= $total_orders ?></div>
                    <div class="stat-label">سفارشات</div>
                </div>
                <div class="stat">
                    <div class="stat-val"><?= number_format($total_spent) ?></div>
                    <div class="stat-label">تومان خرید</div>
                </div>
                <div class="stat">
                    <div class="stat-val">0</div>
                    <div class="stat-label">نظرات</div>
                </div>
            </div>
        </div>
    </div>

    <!-- پیام -->
    <?php if ($success): ?>
        <div class="msg msg-ok">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/></svg>
            <?= escape($success) ?>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="msg msg-err">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M13,13H11V7H13M13,17H11V15H13M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z"/></svg>
            <?= escape($error) ?>
        </div>
    <?php endif; ?>

    <!-- tabs -->
    <div class="tabs">
        <button class="tab active" onclick="showTab('info')">اطلاعات کاربری</button>
        <button class="tab" onclick="showTab('avatar')">تصویر پروفایل</button>
        <button class="tab" onclick="showTab('password')">تغییر رمز</button>
    </div>

    <!-- tab: اطلاعات -->
    <div class="tab-content active" id="tab-info">
        <div class="cards">
            <div class="card">
                <div class="ch">
                    <div class="ci">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/></svg>
                    </div>
                    <div class="ct">ویرایش اطلاعات</div>
                </div>
                <form method="POST">
                    <div class="fg">
                        <label>نام کامل</label>
                        <div class="field">
                            <svg class="fi" viewBox="0 0 24 24" fill="currentColor"><path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/></svg>
                            <input type="text" name="name" value="<?= escape($user['name']) ?>" required>
                        </div>
                    </div>
                    <div class="fg">
                        <label>شماره موبایل</label>
                        <div class="field">
                            <svg class="fi" viewBox="0 0 24 24" fill="currentColor"><path d="M6.62,10.79C8.06,13.62 10.38,15.94 13.21,17.38L15.41,15.18C15.69,14.9 16.08,14.82 16.43,14.93C17.55,15.3 18.75,15.5 20,15.5A1,1 0 0,1 21,16.5V20A1,1 0 0,1 20,21A17,17 0 0,1 3,4A1,1 0 0,1 4,3H7.5A1,1 0 0,1 8.5,4C8.5,5.25 8.7,6.45 9.07,7.57C9.18,7.92 9.1,8.31 8.82,8.59L6.62,10.79Z"/></svg>
                            <input type="tel" name="phone" value="<?= escape($user['phone'] ?? '') ?>" placeholder="09XX-XXX-XXXX">
                        </div>
                    </div>
                    <div class="fg">
                        <label>ایمیل (غیرقابل تغییر)</label>
                        <div class="field">
                            <svg class="fi" viewBox="0 0 24 24" fill="currentColor"><path d="M20,8L12,13L4,8V6L12,11L20,6M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.11,4 20,4Z"/></svg>
                            <input type="email" value="<?= escape($user['email']) ?>" disabled style="opacity:.5;cursor:not-allowed">
                        </div>
                    </div>
                    <div class="cf">
                        <button type="submit" name="update_info" class="btn btn-pri btn-full">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/></svg>
                            ذخیره تغییرات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- tab: آواتار -->
    <div class="tab-content" id="tab-avatar">
        <div class="cards">
            <div class="card">
                <div class="ch">
                    <div class="ci">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9,11.75A1.25,1.25 0 0,0 7.75,13A1.25,1.25 0 0,0 9,14.25A1.25,1.25 0 0,0 10.25,13A1.25,1.25 0 0,0 9,11.75M15,11.75A1.25,1.25 0 0,0 13.75,13A1.25,1.25 0 0,0 15,14.25A1.25,1.25 0 0,0 16.25,13A1.25,1.25 0 0,0 15,11.75M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,20C7.59,20 4,16.41 4,12C4,11.71 4,11.42 4.05,11.14C6.41,10.09 8.28,8.16 9.26,5.77C11.07,8.33 14.05,10 17.42,10C18.2,10 18.95,9.91 19.67,9.74C19.88,10.45 20,11.21 20,12C20,16.41 16.41,20 12,20Z"/></svg>
                    </div>
                    <div class="ct">آپلود تصویر پروفایل</div>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <label for="avatarUpload" class="upload-zone">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9,16V10H5L12,3L19,10H15V16H9M5,20V18H19V20H5Z"/></svg>
                        <p><strong>انتخاب تصویر</strong> یا بکشید و رها کنید<br>JPG, PNG, GIF (حداکثر 2MB)</p>
                        <input type="file" name="avatar" id="avatarUpload" accept="image/*" onchange="this.form.querySelector('.btn-pri').click()">
                    </label>
                    <div class="cf">
                        <button type="submit" name="upload_avatar" class="btn btn-pri" style="display:none">آپلود</button>
                        <?php if ($user['avatar']): ?>
                            <button type="submit" name="delete_avatar" class="btn btn-danger btn-full" onclick="return confirm('آیا مطمئن هستید؟')">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19,4H15.5L14.5,3H9.5L8.5,4H5V6H19M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19Z"/></svg>
                                حذف تصویر
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- tab: رمز -->
    <div class="tab-content" id="tab-password">
        <div class="cards">
            <div class="card">
                <div class="ch">
                    <div class="ci">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,17A2,2 0 0,0 14,15C14,13.89 13.1,13 12,13A2,2 0 0,0 10,15A2,2 0 0,0 12,17M18,8A2,2 0 0,1 20,10V20A2,2 0 0,1 18,22H6A2,2 0 0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0 0,1 12,1A5,5 0 0,1 17,6V8H18M12,3A3,3 0 0,0 9,6V8H15V6A3,3 0 0,0 12,3Z"/></svg>
                    </div>
                    <div class="ct">تغییر رمز عبور</div>
                </div>
                <form method="POST">
                    <div class="fg">
                        <label>رمز فعلی</label>
                        <div class="field">
                            <svg class="fi" viewBox="0 0 24 24" fill="currentColor"><path d="M12,17A2,2 0 0,0 14,15C14,13.89 13.1,13 12,13A2,2 0 0,0 10,15A2,2 0 0,0 12,17M18,8A2,2 0 0,1 20,10V20A2,2 0 0,1 18,22H6A2,2 0 0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0 0,1 12,1A5,5 0 0,1 17,6V8H18M12,3A3,3 0 0,0 9,6V8H15V6A3,3 0 0,0 12,3Z"/></svg>
                            <input type="password" name="current_password" id="p1">
                            <button type="button" class="fe" onclick="toggleEye('p1',this)">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9M12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="fg">
                        <label>رمز جدید</label>
                        <div class="field">
                            <svg class="fi" viewBox="0 0 24 24" fill="currentColor"><path d="M12,17A2,2 0 0,0 14,15C14,13.89 13.1,13 12,13A2,2 0 0,0 10,15A2,2 0 0,0 12,17M18,8A2,2 0 0,1 20,10V20A2,2 0 0,1 18,22H6A2,2 0 0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0 0,1 12,1A5,5 0 0,1 17,6V8H18M12,3A3,3 0 0,0 9,6V8H15V6A3,3 0 0,0 12,3Z"/></svg>
                            <input type="password" name="new_password" id="p2">
                            <button type="button" class="fe" onclick="toggleEye('p2',this)">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9M12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="fg">
                        <label>تکرار رمز</label>
                        <div class="field">
                            <svg class="fi" viewBox="0 0 24 24" fill="currentColor"><path d="M12,17A2,2 0 0,0 14,15C14,13.89 13.1,13 12,13A2,2 0 0,0 10,15A2,2 0 0,0 12,17M18,8A2,2 0 0,1 20,10V20A2,2 0 0,1 18,22H6A2,2 0 0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0 0,1 12,1A5,5 0 0,1 17,6V8H18M12,3A3,3 0 0,0 9,6V8H15V6A3,3 0 0,0 12,3Z"/></svg>
                            <input type="password" name="confirm_password" id="p3">
                            <button type="button" class="fe" onclick="toggleEye('p3',this)">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9M12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="cf">
                        <button type="submit" name="change_password" class="btn btn-pri btn-full">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/></svg>
                            تغییر رمز
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
const EO='<path d="M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9M12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z"/>';
const EC='<path d="M11.83,9L15,12.16C15,12.11 15,12.05 15,12A3,3 0 0,0 12,9C11.94,9 11.89,9 11.83,9M7.53,9.8L9.08,11.35C9.03,11.56 9,11.77 9,12A3,3 0 0,0 12,15C12.22,15 12.44,14.97 12.65,14.92L14.2,16.47C13.53,16.8 12.79,17 12,17A5,5 0 0,1 7,12C7,11.21 7.2,10.47 7.53,9.8M2,4.27L4.28,6.55L4.73,7C3.08,8.3 1.78,10 1,12C2.73,16.39 7,19.5 12,19.5C13.55,19.5 15.03,19.2 16.38,18.66L16.81,19.08L19.73,22L21,20.73L3.27,3M12,7C14.76,7 17,9.24 17,12C17,12.64 16.87,13.26 16.64,13.82L19.57,16.75C21.07,15.5 22.27,13.86 23,12C21.27,7.61 17,4.5 12,4.5C10.6,4.5 9.26,4.75 8,5.2L10.17,7.35C10.74,7.13 11.35,7 12,7Z"/>';

function toggleEye(id,btn){
    const i=document.getElementById(id);
    const h=i.type==='password';
    i.type=h?'text':'password';
    btn.querySelector('svg').innerHTML=h?EC:EO;
}

function showTab(name){
    document.querySelectorAll('.tab').forEach(t=>t.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c=>c.classList.remove('active'));
    event.target.classList.add('active');
    document.getElementById('tab-'+name).classList.add('active');
}

// dark mode
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