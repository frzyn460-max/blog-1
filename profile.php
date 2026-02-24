<?php
/**
 * صفحه پروفایل - با بخش علاقه‌مندی‌ها
 */
if (session_status() === PHP_SESSION_NONE) session_start();
require_once("./include/config.php");
require_once("./include/db.php");

if (!isset($_SESSION['member_id'])) { header("Location: login.php"); exit(); }

$user_id = $_SESSION['member_id'];
$user = fetchOne($db, "SELECT * FROM members WHERE id = ?", [$user_id]);
if (!$user) { session_destroy(); header("Location: login.php"); exit(); }

$success = $error = '';

/* ══ Wishlist: حذف آیتم ══ */
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['remove_wishlist'])) {
    $pid = (int)($_POST['product_id'] ?? 0);
    if ($pid > 0) {
        $_SESSION['wishlist'] = array_values(array_filter($_SESSION['wishlist'] ?? [], fn($id) => $id !== $pid));
        $success = 'از علاقه‌مندی‌ها حذف شد.';
    }
}
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['clear_wishlist'])) {
    $_SESSION['wishlist'] = [];
    $success = 'لیست علاقه‌مندی‌ها پاک شد.';
}

/* ══ آپلود آواتار ══ */
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['upload_avatar'])) {
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error']===UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext,['jpg','jpeg','png','gif','webp'])) { $error='فرمت فایل مجاز نیست.'; }
        elseif ($_FILES['avatar']['size'] > 3*1024*1024) { $error='حجم بیشتر از ۳MB است.'; }
        else {
            $dir = './upload/avatars/';
            if (!is_dir($dir)) mkdir($dir,0755,true);
            $name = 'avatar_'.$user_id.'_'.time().'.'.$ext;
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $dir.$name)) {
                if (!empty($user['avatar']) && file_exists($user['avatar'])) unlink($user['avatar']);
                executeQuery($db,"UPDATE members SET avatar=? WHERE id=?",[$dir.$name,$user_id]);
                $user['avatar']=$dir.$name;
                $_SESSION['member_avatar']=$dir.$name;
                $success='تصویر پروفایل آپلود شد.';
            } else { $error='خطا در آپلود.'; }
        }
    } else { $error='فایلی انتخاب نشده.'; }
}
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['delete_avatar'])) {
    if (!empty($user['avatar']) && file_exists($user['avatar'])) unlink($user['avatar']);
    executeQuery($db,"UPDATE members SET avatar=NULL WHERE id=?",[$user_id]);
    $user['avatar']=null; $_SESSION['member_avatar']=null;
    $success='تصویر حذف شد.';
}

/* ══ ویرایش اطلاعات ══ */
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update_info'])) {
    $name = trim($_POST['name'] ?? '');
    $phone= trim($_POST['phone']?? '');
    if (!$name) { $error='نام خالی نباشد.'; }
    else {
        executeQuery($db,"UPDATE members SET name=?,phone=? WHERE id=?",[$name,$phone?:null,$user_id]);
        $_SESSION['member_name']=$name;
        $user['name']=$name; $user['phone']=$phone;
        $success='اطلاعات ذخیره شد.';
    }
}

/* ══ تغییر رمز ══ */
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['change_password'])) {
    $cur = trim($_POST['current_password']??'');
    $new = trim($_POST['new_password']??'');
    $con = trim($_POST['confirm_password']??'');
    if (!$cur||!$new||!$con) $error='همه فیلدها را پر کنید.';
    elseif (!password_verify($cur,$user['password'])) $error='رمز فعلی اشتباه است.';
    elseif (mb_strlen($new)<6) $error='رمز جدید حداقل ۶ کاراکتر باشد.';
    elseif ($new!==$con) $error='رمز و تکرار آن یکسان نیستند.';
    else {
        executeQuery($db,"UPDATE members SET password=? WHERE id=?",[password_hash($new,PASSWORD_BCRYPT),$user_id]);
        $success='رمز تغییر کرد.';
    }
}

/* ══ واکشی wishlist ══ */
$wishlist_ids = $_SESSION['wishlist'] ?? [];
$wishlist_products = [];
if (!empty($wishlist_ids)) {
    $ph = implode(',', array_fill(0, count($wishlist_ids), '?'));
    $wishlist_products = fetchAll($db,
        "SELECT id, name, `new-price` AS new_price, price AS old_price, pic FROM product WHERE id IN ($ph) ORDER BY id DESC",
        $wishlist_ids
    );
}

/* ══ آمار ══ */
$total_orders=0; $total_spent=0;
try {
    $od = fetchAll($db,"SELECT total_price FROM orders WHERE member_id=?",[$user_id]);
    $total_orders=count($od);
    $total_spent=(int)array_sum(array_column($od,'total_price'));
} catch(Exception $e){}

$avatar_letter = mb_substr($user['name'],0,1);
$member_since  = date('Y/m/d', strtotime($user['created_at']??'now'));
$wishlist_count= count($wishlist_ids);
$avatar_src    = (!empty($user['avatar']) && file_exists($user['avatar'])) ? $user['avatar'] : null;
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
    --dp:#0f172a;--mid:#1e3a8a;--br:#3b82f6;
    --w:#fff;--g50:#f8fafc;--g100:#f1f5f9;--g200:#e2e8f0;
    --g400:#94a3b8;--g600:#475569;--g800:#1e293b;
    --red:#ef4444;--green:#10b981;--orange:#f59e0b;--purple:#a855f7;
}
body{font-family:'Vazirmatn',Tahoma,Arial,sans-serif;direction:rtl;min-height:100vh;background:var(--g100);color:var(--g800);transition:background .3s,color .3s}
body.dark{--g100:#0f172a;--g50:#1e293b;--g200:#334155;--g400:#64748b;--g600:#cbd5e1;--g800:#f1f5f9;background:#0f172a}

/* TOPBAR */
.top{background:linear-gradient(135deg,var(--dp),var(--mid));padding:1.25rem 0;box-shadow:0 4px 24px rgba(0,0,0,.25);position:sticky;top:0;z-index:100; border-radius: 0 0 16px 16px;}
body.dark .top{background:linear-gradient(135deg,#020617,#0c1e47)}
.top-in{max-width:1200px;margin:0 auto;padding:0 1.5rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap}
.logo{display:inline-flex;align-items:center;gap:.55rem;color:#fff;text-decoration:none;font-size:1.2rem;font-weight:800}
.logo:hover{opacity:.8}
.top-act{display:flex;align-items:center;gap:.65rem}
.tbtn{display:inline-flex;align-items:center;gap:.45rem;padding:.55rem 1rem;border-radius:11px;font-weight:600;font-size:.85rem;text-decoration:none;font-family:inherit;cursor:pointer;transition:all .25s;border:2px solid transparent;background:none}
.tbtn-home{background:rgba(255,255,255,.15);color:#fff}.tbtn-home:hover{background:rgba(255,255,255,.25)}
.tbtn-dark{background:rgba(255,255,255,.12);color:#fff}.tbtn-dark:hover{background:rgba(255,255,255,.22)}
.tbtn-out{background:rgba(239,68,68,.2);color:#fff;border-color:rgba(239,68,68,.4)}.tbtn-out:hover{background:rgba(239,68,68,.35)}
.tbtn svg{width:16px;height:16px}

/* MAIN */
.main{max-width:1200px;margin:0 auto;padding:2rem 1.5rem 4rem}

/* PROFILE CARD */
.pcard{background:var(--w);border-radius:24px;box-shadow:0 8px 40px rgba(0,0,0,.1);margin-bottom:2rem;overflow:hidden}
body.dark .pcard{background:var(--g50);box-shadow:0 8px 40px rgba(0,0,0,.4)}
.cover{height:200px;background:linear-gradient(135deg,var(--mid) 0%,var(--br) 50%,var(--purple) 100%);position:relative;overflow:hidden}
body.dark .cover{background:linear-gradient(135deg,#1e3a8a,#3b82f6,#7c3aed)}
.cover::before{content:'';position:absolute;inset:0;background-image:radial-gradient(circle,rgba(255,255,255,.06) 1px,transparent 1px);background-size:24px 24px}
.pcard-body{padding:0 2.5rem 2.5rem;position:relative}

/* AVATAR - کیفیت بالا */
.av-section{display:flex;align-items:flex-end;gap:1.5rem;margin-top:-55px;margin-bottom:1.5rem;position:relative}
.av-outer{
    width:140px;height:140px;flex-shrink:0;border-radius:50%;padding:5px;
    background:linear-gradient(135deg,#f59e0b,#ef4444,#ec4899);
    box-shadow:0 16px 50px rgba(239,68,68,.4),0 0 0 5px var(--w);
    position:relative;z-index:5;
    animation:avPop .7s cubic-bezier(.34,1.56,.64,1);
    transition:transform .3s,box-shadow .3s;
}
body.dark .av-outer{box-shadow:0 16px 50px rgba(239,68,68,.4),0 0 0 5px var(--g50)}
.av-outer:hover{transform:scale(1.04)}
@keyframes avPop{from{transform:scale(.3) rotate(-15deg);opacity:0}to{transform:scale(1) rotate(0);opacity:1}}
.av-inner{width:100%;height:100%;border-radius:50%;background:linear-gradient(135deg,var(--g100),var(--g200));display:flex;align-items:center;justify-content:center;overflow:hidden;position:relative}
body.dark .av-inner{background:linear-gradient(135deg,#1e293b,#0f172a)}
.av-img{
    width:100%;height:100%;object-fit:cover;object-position:center top;
    border-radius:50%;image-rendering:high-quality;
    -webkit-backface-visibility:hidden;backface-visibility:hidden;transform:translateZ(0);
}
.av-letter{font-size:3.5rem;font-weight:900;color:var(--mid)}
body.dark .av-letter{color:#60a5fa}
.av-edit-btn{
    position:absolute;bottom:8px;right:8px;width:38px;height:38px;border-radius:50%;
    background:linear-gradient(135deg,var(--br),var(--mid));color:#fff;
    border:3px solid var(--w);display:flex;align-items:center;justify-content:center;
    cursor:pointer;transition:all .3s;box-shadow:0 6px 16px rgba(37,99,235,.45);z-index:6;
}
body.dark .av-edit-btn{border-color:var(--g50)}
.av-edit-btn:hover{transform:scale(1.12) rotate(10deg)}
.av-edit-btn svg{width:16px;height:16px}
.av-info{padding-bottom:.6rem;flex:1}
.av-name{font-size:1.9rem;font-weight:900;color:var(--g800);margin-bottom:.3rem;line-height:1.2}
body.dark .av-name{color:#f1f5f9}
.av-email{color:var(--g400);font-size:.92rem;margin-bottom:.3rem}
.av-join{display:flex;align-items:center;gap:.4rem;color:var(--g400);font-size:.8rem}
.av-badges{display:flex;gap:.5rem;flex-wrap:wrap;margin-top:.6rem}
.badge{padding:.25rem .85rem;border-radius:50px;font-size:.72rem;font-weight:700}
.badge-blue{background:linear-gradient(135deg,#dbeafe,#eff6ff);color:var(--br)}
.badge-green{background:rgba(16,185,129,.12);color:var(--green)}
.badge-purple{background:rgba(168,85,247,.12);color:var(--purple)}

/* STATS */
.stats-bar{display:grid;grid-template-columns:repeat(4,1fr);border-top:1.5px solid var(--g200);padding-top:1.75rem;gap:1rem;margin-top:1.2rem}
body.dark .stats-bar{border-color:var(--g200)}
.stat-box{text-align:center;padding:1rem;border-radius:16px;background:var(--g100);transition:all .3s}
body.dark .stat-box{background:rgba(255,255,255,.05)}
.stat-box:hover{transform:translateY(-3px);box-shadow:0 8px 24px rgba(0,0,0,.1)}
.stat-icon{font-size:1.5rem;margin-bottom:.4rem}
.stat-val{font-size:1.6rem;font-weight:900;background:linear-gradient(135deg,var(--mid),var(--br));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.stat-label{font-size:.78rem;color:var(--g400);margin-top:.2rem}

/* MESSAGES */
.msg{padding:.9rem 1.1rem;border-radius:12px;margin-bottom:1.5rem;font-size:.88rem;font-weight:600;display:flex;align-items:center;gap:.6rem;animation:slideIn .35s ease}
@keyframes slideIn{from{opacity:0;transform:translateY(-8px)}to{opacity:1;transform:none}}
.msg svg{width:18px;height:18px;flex-shrink:0}
.msg-ok{background:rgba(16,185,129,.1);color:var(--green);border:1.5px solid rgba(16,185,129,.25)}
.msg-err{background:rgba(239,68,68,.08);color:var(--red);border:1.5px solid rgba(239,68,68,.2)}

/* TABS */
.tabs-wrap{background:var(--w);border-radius:18px;padding:1.1rem 1.25rem;margin-bottom:2rem;box-shadow:0 2px 10px rgba(0,0,0,.06);display:flex;gap:.45rem;overflow-x:auto}
body.dark .tabs-wrap{background:var(--g50)}
.tab{padding:.65rem 1.2rem;border-radius:12px;border:none;background:none;font-weight:700;font-size:.87rem;font-family:inherit;color:var(--g400);cursor:pointer;white-space:nowrap;display:flex;align-items:center;gap:.5rem;transition:all .25s}
.tab:hover{background:var(--g100);color:var(--g800)}
body.dark .tab:hover{background:rgba(255,255,255,.06)}
.tab.active{background:linear-gradient(135deg,var(--mid),var(--br));color:#fff;box-shadow:0 6px 18px rgba(30,58,138,.28)}
.tab-badge{background:rgba(255,255,255,.25);width:20px;height:20px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.68rem;font-weight:800}
.tab:not(.active) .tab-badge{background:var(--g200);color:var(--g800)}
body.dark .tab:not(.active) .tab-badge{background:var(--g200);color:var(--g600)}
.tab-pane{display:none}
.tab-pane.active{display:block;animation:fadeIn .35s ease}
@keyframes fadeIn{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:none}}

/* CARDS */
.cards-2{display:grid;grid-template-columns:repeat(auto-fit,minmax(420px,1fr));gap:1.75rem}
.card{background:var(--w);border-radius:20px;padding:1.75rem;box-shadow:0 2px 14px rgba(0,0,0,.06);transition:box-shadow .3s;border:1px solid var(--g200)}
body.dark .card{background:var(--g50);border-color:rgba(255,255,255,.07)}
.card:hover{box-shadow:0 8px 30px rgba(0,0,0,.12)}
.card-head{display:flex;align-items:center;gap:.7rem;margin-bottom:1.5rem}
.card-icon{width:44px;height:44px;border-radius:12px;flex-shrink:0;background:linear-gradient(135deg,var(--mid),var(--br));display:flex;align-items:center;justify-content:center;color:#fff;box-shadow:0 6px 18px rgba(30,58,138,.28)}
.card-icon svg{width:21px;height:21px}
.card-title{font-size:1.15rem;font-weight:800;color:var(--g800)}
body.dark .card-title{color:#f1f5f9}

/* FORM */
.fg{margin-bottom:1.1rem}
.fg label{display:block;font-weight:700;font-size:.82rem;color:var(--g600);margin-bottom:.4rem}
.field{position:relative}
.fi{position:absolute;right:.8rem;top:50%;transform:translateY(-50%);width:16px;height:16px;color:var(--g400);pointer-events:none}
.fe{position:absolute;left:.8rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--g400);padding:0;display:flex;transition:color .2s}
.fe:hover{color:var(--br)}
.fe svg{width:16px;height:16px}
.field input{width:100%;padding:.8rem 2.4rem .8rem 2.4rem;border:2px solid var(--g200);border-radius:12px;font-size:.9rem;font-family:inherit;color:var(--g800);background:var(--g50);transition:all .25s;outline:none}
body.dark .field input{background:var(--g200);border-color:rgba(255,255,255,.1);color:#f1f5f9}
.field input:focus{border-color:var(--br);background:var(--w);box-shadow:0 0 0 4px rgba(59,130,246,.1)}
body.dark .field input:focus{background:rgba(255,255,255,.08)}
.field input:disabled{opacity:.45;cursor:not-allowed}
.btn{padding:.85rem 1.4rem;border-radius:12px;font-weight:700;font-size:.9rem;font-family:inherit;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:.55rem;transition:all .25s;border:none;text-decoration:none}
.btn-pri{background:linear-gradient(135deg,var(--mid),var(--br));color:#fff;box-shadow:0 6px 18px rgba(30,58,138,.28)}
.btn-pri:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(30,58,138,.38)}
.btn-sec{background:var(--g200);color:var(--g800)}
body.dark .btn-sec{background:rgba(255,255,255,.1);color:#f1f5f9}
.btn-sec:hover{background:var(--g400);color:#fff}
.btn-danger{background:linear-gradient(135deg,var(--red),#dc2626);color:#fff;box-shadow:0 6px 18px rgba(239,68,68,.25)}
.btn-danger:hover{transform:translateY(-2px)}
.btn svg{width:17px;height:17px}
.btn-full{width:100%}
.cf{display:flex;gap:.7rem;margin-top:1.4rem;flex-wrap:wrap}

/* UPLOAD */
.upload-zone{border:2px dashed var(--g200);border-radius:14px;padding:2rem;text-align:center;cursor:pointer;transition:all .3s;background:var(--g50)}
body.dark .upload-zone{background:rgba(255,255,255,.04);border-color:rgba(255,255,255,.1)}
.upload-zone:hover{border-color:var(--br);background:rgba(37,99,235,.05)}
.upload-zone svg{width:44px;height:44px;color:var(--g400);margin-bottom:.75rem}
.upload-zone p{color:var(--g400);font-size:.85rem;line-height:1.6}
.upload-zone input{display:none}

/* ══ WISHLIST ══ */
.wishlist-toolbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:.75rem}
.wishlist-count{font-size:.88rem;color:var(--g400);font-weight:600;display:flex;align-items:center;gap:.4rem}
.wishlist-count strong{color:var(--g800)}
body.dark .wishlist-count strong{color:#f1f5f9}
.wishlist-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1.25rem}
.wcard{background:var(--w);border:1px solid var(--g200);border-radius:18px;overflow:hidden;display:flex;flex-direction:column;transition:all .35s cubic-bezier(.4,0,.2,1);position:relative}
body.dark .wcard{background:var(--g50);border-color:rgba(255,255,255,.07)}
.wcard:hover{transform:translateY(-8px);box-shadow:0 24px 50px rgba(0,0,0,.14);border-color:var(--br)}
.wcard-img-wrap{position:relative;padding-top:100%;overflow:hidden;background:var(--g100)}
.wcard-img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;transition:transform .55s ease}
.wcard:hover .wcard-img{transform:scale(1.08)}
.wcard-disc{position:absolute;top:10px;right:10px;background:linear-gradient(135deg,#ef4444,#f97316);color:#fff;padding:.25rem .65rem;border-radius:8px;font-size:.7rem;font-weight:800;box-shadow:0 4px 12px rgba(239,68,68,.35)}
.wcard-rm{position:absolute;top:10px;left:10px;width:32px;height:32px;border-radius:50%;background:rgba(0,0,0,.55);backdrop-filter:blur(6px);border:none;color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;opacity:0;transform:scale(.8);transition:all .25s}
.wcard:hover .wcard-rm{opacity:1;transform:scale(1)}
.wcard-rm:hover{background:#ef4444;transform:scale(1.1)!important}
.wcard-rm svg{width:15px;height:15px}
.wcard-body{padding:1rem;flex:1;display:flex;flex-direction:column}
.wcard-name{font-size:.92rem;font-weight:700;color:var(--g800);line-height:1.4;margin-bottom:.7rem;flex:1;text-decoration:none;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;transition:color .2s}
.wcard-name:hover{color:var(--br)}
body.dark .wcard-name{color:#f1f5f9}
.wcard-price{display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;margin-bottom:.85rem}
.wcard-new{font-size:.97rem;font-weight:800;color:var(--green)}
.wcard-old{font-size:.77rem;color:var(--g400);text-decoration:line-through}
.wcard-btn{display:flex;align-items:center;justify-content:center;gap:.4rem;padding:.7rem;background:linear-gradient(135deg,var(--mid),var(--br));color:#fff;border-radius:11px;text-decoration:none;font-size:.82rem;font-weight:700;transition:all .3s}
.wcard-btn:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(37,99,235,.35)}
.wcard-btn svg{width:15px;height:15px}

/* empty state */
.wishlist-empty{text-align:center;padding:4rem 2rem;border:2px dashed var(--g200);border-radius:20px;background:var(--g50)}
body.dark .wishlist-empty{background:rgba(255,255,255,.03);border-color:rgba(255,255,255,.08)}
.wishlist-empty-icon{font-size:4rem;margin-bottom:1.2rem;display:block}
.wishlist-empty h3{font-size:1.3rem;font-weight:800;color:var(--g800);margin-bottom:.6rem}
body.dark .wishlist-empty h3{color:#f1f5f9}
.wishlist-empty p{color:var(--g400);font-size:.9rem;line-height:1.7;margin-bottom:1.5rem}
.wishlist-empty-btn{display:inline-flex;align-items:center;gap:.5rem;padding:.85rem 2rem;border-radius:50px;background:linear-gradient(135deg,var(--mid),var(--br));color:#fff;font-weight:700;font-size:.9rem;text-decoration:none;transition:all .3s;box-shadow:0 6px 18px rgba(30,58,138,.28)}
.wishlist-empty-btn:hover{transform:translateY(-3px);box-shadow:0 12px 28px rgba(30,58,138,.38)}

/* responsive */
@media(max-width:1000px){.cards-2{grid-template-columns:1fr}}
@media(max-width:768px){
    .pcard-body{padding:0 1.25rem 1.75rem}
    .cover{height:150px}
    .av-outer{width:110px;height:110px;margin-top:-45px}
    .av-letter{font-size:2.6rem}
    .av-section{flex-direction:column;align-items:center;text-align:center;gap:.75rem;margin-top:-55px}
    .av-name{font-size:1.55rem}
    .stats-bar{grid-template-columns:repeat(2,1fr)}
    .wishlist-grid{grid-template-columns:repeat(2,1fr)}
    .main{padding:1.5rem 1rem 3rem}
}
@media(max-width:480px){
    .wishlist-grid{grid-template-columns:1fr}
    .tbtn span{display:none}
    .tbtn{padding:.5rem}
}
</style>
</head>
<body>

<!-- TOPBAR -->
<div class="top">
<div class="top-in">
<a href="index.php" class="logo">📚 <span><?php echo escape(SITE_NAME); ?></span></a>
<div class="top-act">
<a href="index.php" class="tbtn tbtn-home">
<svg viewBox="0 0 24 24" fill="currentColor"><path d="M10,20V14H14V20H19V12H22L12,3L2,12H5V20H10Z"/></svg><span>خانه</span></a>
<button class="tbtn tbtn-dark" id="darkToggle">
<svg viewBox="0 0 24 24" fill="currentColor" id="darkIcon"><path d="M17.75,4.09L15.22,6.03L16.13,9.09L13.5,7.28L10.87,9.09L11.78,6.03L9.25,4.09L12.44,4L13.5,1L14.56,4L17.75,4.09M21.25,11L19.61,12.25L20.2,14.23L18.5,13.06L16.8,14.23L17.39,12.25L15.75,11L17.81,10.95L18.5,9L19.19,10.95L21.25,11M18.97,15.95C19.8,15.87 20.69,17.05 20.16,17.8C19.84,18.25 19.5,18.67 19.08,19.07C15.17,23 8.84,23 4.94,19.07C1.03,15.17 1.03,8.83 4.94,4.93C5.34,4.53 5.76,4.17 6.21,3.85C6.96,3.32 8.14,4.21 8.06,5.04C7.79,7.9 8.75,10.87 10.95,13.06C13.14,15.26 16.1,16.22 18.97,15.95M17.33,17.97C14.5,17.81 11.7,16.64 9.53,14.5C7.36,12.31 6.2,9.5 6.04,6.68C3.23,9.82 3.34,14.64 6.35,17.66C9.37,20.67 14.19,20.78 17.33,17.97Z"/></svg>
<span>دارک مود</span></button>
<a href="logout.php" class="tbtn tbtn-out">
<svg viewBox="0 0 24 24" fill="currentColor"><path d="M16,17V14H9V10H16V7L21,12L16,17M14,2A2,2 0 0,1 16,4V6H14V4H5V20H14V18H16V20A2,2 0 0,1 14,22H5A2,2 0 0,1 3,20V4A2,2 0 0,1 5,2H14Z"/></svg>
<span>خروج</span></a>
</div>
</div>
</div>

<div class="main">
<!-- PROFILE CARD -->
<div class="pcard">
<div class="cover"><div style="position:absolute;width:400px;height:400px;border-radius:50%;background:radial-gradient(circle,rgba(255,255,255,.1),transparent 65%);top:-100px;right:-50px"></div></div>
<div class="pcard-body">
<div class="av-section">
<div class="av-outer">
<div class="av-inner">
<?php if($avatar_src): ?>
<img class="av-img" src="<?php echo escape($avatar_src); ?>?v=<?php echo filemtime($avatar_src); ?>" alt="<?php echo escape($user['name']); ?>" width="140" height="140">
<?php else: ?>
<div class="av-letter"><?php echo $avatar_letter; ?></div>
<?php endif; ?>
</div>
<label for="avatarFileInput" class="av-edit-btn" title="تغییر تصویر">
<svg viewBox="0 0 24 24" fill="currentColor"><path d="M5,3C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V12H19V19H5V5H12V3H5M17.78,4C17.61,4 17.43,4.07 17.3,4.2L16.08,5.41L18.58,7.91L19.8,6.7C20.07,6.43 20.07,6 19.8,5.72L18.28,4.2C18.14,4.07 17.96,4 17.78,4M15.37,6.12L8,13.5V16H10.5L17.87,8.62L15.37,6.12Z"/></svg></label>
</div>
<div class="av-info">
<div class="av-name"><?php echo escape($user['name']); ?></div>
<div class="av-email">📧 <?php echo escape($user['email']); ?></div>
<div class="av-join"><svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px"><path d="M19,3H18V1H16V3H8V1H6V3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3M19,19H5V8H19V19Z"/></svg>عضو از: <?php echo $member_since; ?></div>
<div class="av-badges">
<span class="badge badge-blue">👤 کاربر فعال</span>
<?php if($wishlist_count>0): ?><span class="badge badge-purple">❤️ <?php echo $wishlist_count; ?> علاقه‌مندی</span><?php endif; ?>
<?php if($total_orders>0): ?><span class="badge badge-green">✅ خریدار</span><?php endif; ?>
</div>
</div>
</div>
<div class="stats-bar">
<div class="stat-box"><div class="stat-icon">🛍️</div><div class="stat-val"><?php echo $total_orders; ?></div><div class="stat-label">سفارشات</div></div>
<div class="stat-box"><div class="stat-icon">💰</div><div class="stat-val"><?php echo $total_spent>0?number_format($total_spent):'۰'; ?></div><div class="stat-label">تومان خرید</div></div>
<div class="stat-box"><div class="stat-icon">❤️</div><div class="stat-val"><?php echo $wishlist_count; ?></div><div class="stat-label">علاقه‌مندی</div></div>
<div class="stat-box"><div class="stat-icon">⭐</div><div class="stat-val">۰</div><div class="stat-label">نظرات</div></div>
</div>
</div>
</div>

<!-- پیام -->
<?php if($success): ?>
<div class="msg msg-ok"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/></svg><?php echo escape($success); ?></div>
<?php elseif($error): ?>
<div class="msg msg-err"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M13,13H11V7H13M13,17H11V15H13M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z"/></svg><?php echo escape($error); ?></div>
<?php endif; ?>

<!-- TABS -->
<div class="tabs-wrap">
<button class="tab active" onclick="showTab('wishlist',this)">❤️ علاقه‌مندی‌ها<span class="tab-badge"><?php echo $wishlist_count; ?></span></button>
<button class="tab" onclick="showTab('info',this)">👤 اطلاعات کاربری</button>
<button class="tab" onclick="showTab('avatar',this)">🖼️ تصویر پروفایل</button>
<button class="tab" onclick="showTab('password',this)">🔒 تغییر رمز</button>
</div>

<!-- TAB: علاقه‌مندی‌ها -->
<div class="tab-pane active" id="tab-wishlist">
<?php if(empty($wishlist_products)): ?>
<div class="wishlist-empty">
<span class="wishlist-empty-icon">💔</span>
<h3>لیست علاقه‌مندی‌ها خالی است</h3>
<p>هنوز هیچ کتابی به علاقه‌مندی‌ها اضافه نکرده‌اید.<br>روی آیکون ❤️ در صفحه محصولات کلیک کنید.</p>
<a href="products.php" class="wishlist-empty-btn">
<svg viewBox="0 0 24 24" fill="currentColor" style="width:18px;height:18px"><path d="M4,11V13H16L10.5,18.5L11.92,19.92L19.84,12L11.92,4.08L10.5,5.5L16,11H4Z"/></svg>
مشاهده محصولات</a>
</div>
<?php else: ?>
<div class="wishlist-toolbar">
<div class="wishlist-count"><svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;color:#ef4444"><path d="M12,21.35L10.55,20.03C5.4,15.36 2,12.27 2,8.5C2,5.41 4.42,3 7.5,3C9.24,3 10.91,3.81 12,5.08C13.09,3.81 14.76,3 16.5,3C19.58,3 22,5.41 22,8.5C22,12.27 18.6,15.36 13.45,20.03L12,21.35Z"/></svg><strong><?php echo $wishlist_count; ?></strong> کتاب در لیست</div>
<form method="POST" onsubmit="return confirm('همه آیتم‌ها حذف شوند؟')">
<button type="submit" name="clear_wishlist" class="btn btn-sec" style="padding:.5rem 1rem;font-size:.82rem">
<svg viewBox="0 0 24 24" fill="currentColor" style="width:15px;height:15px"><path d="M19,4H15.5L14.5,3H9.5L8.5,4H5V6H19M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19Z"/></svg>
پاک کردن همه</button>
</form>
</div>
<div class="wishlist-grid">
<?php foreach($wishlist_products as $p):
    $newP=(int)$p['new_price'];
    $oldP=(int)$p['old_price'];
    $disc=$oldP>0?round(($oldP-$newP)/$oldP*100):0;
?>
<div class="wcard">
<div class="wcard-img-wrap">
<img class="wcard-img" src="./upload/products/<?php echo escape($p['pic']); ?>" alt="<?php echo escape($p['name']); ?>" loading="lazy">
<?php if($disc>0): ?><span class="wcard-disc"><?php echo $disc; ?>% تخفیف</span><?php endif; ?>
<form method="POST" style="position:absolute;top:10px;left:10px;margin:0">
<input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
<button type="submit" name="remove_wishlist" class="wcard-rm" title="حذف از علاقه‌مندی‌ها">
<svg viewBox="0 0 24 24" fill="currentColor"><path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z"/></svg></button>
</form>
</div>
<div class="wcard-body">
<a class="wcard-name" href="single_product.php?product=<?php echo $p['id']; ?>"><?php echo escape($p['name']); ?></a>
<div class="wcard-price">
<span class="wcard-new"><?php echo number_format($newP); ?> تومان</span>
<?php if($oldP!==$newP): ?><span class="wcard-old"><?php echo number_format($oldP); ?></span><?php endif; ?>
</div>
<a href="single_product.php?product=<?php echo $p['id']; ?>" class="wcard-btn">
<svg viewBox="0 0 24 24" fill="currentColor"><path d="M17,18A2,2 0 0,1 19,20A2,2 0 0,1 17,22C15.89,22 15,21.1 15,20C15,18.89 15.89,18 17,18M1,2H4.27L5.21,4H20A1,1 0 0,1 21,5L17.3,11.97C16.96,12.58 16.3,13 15.55,13H8.1L7.2,14.63L7.17,14.75A0.25,0.25 0 0,0 7.42,15H19V17H7C5.89,17 5,16.1 5,15L6.6,11.59L3,4H1V2Z"/></svg>
مشاهده محصول</a>
</div>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>
</div>

<!-- TAB: اطلاعات -->
<div class="tab-pane" id="tab-info">
<div class="cards-2"><div class="card">
<div class="card-head"><div class="card-icon"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/></svg></div><div class="card-title">ویرایش اطلاعات</div></div>
<form method="POST">
<div class="fg"><label>نام کامل</label><div class="field"><svg class="fi" viewBox="0 0 24 24" fill="currentColor"><path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/></svg><input type="text" name="name" value="<?php echo escape($user['name']); ?>" required></div></div>
<div class="fg"><label>موبایل</label><div class="field"><svg class="fi" viewBox="0 0 24 24" fill="currentColor"><path d="M6.62,10.79C8.06,13.62 10.38,15.94 13.21,17.38L15.41,15.18C15.69,14.9 16.08,14.82 16.43,14.93C17.55,15.3 18.75,15.5 20,15.5A1,1 0 0,1 21,16.5V20A1,1 0 0,1 20,21A17,17 0 0,1 3,4A1,1 0 0,1 4,3H7.5A1,1 0 0,1 8.5,4C8.5,5.25 8.7,6.45 9.07,7.57C9.18,7.92 9.1,8.31 8.82,8.59L6.62,10.79Z"/></svg><input type="tel" name="phone" value="<?php echo escape($user['phone']??''); ?>" placeholder="09XX-XXX-XXXX"></div></div>
<div class="fg"><label>ایمیل (غیرقابل تغییر)</label><div class="field"><svg class="fi" viewBox="0 0 24 24" fill="currentColor"><path d="M20,8L12,13L4,8V6L12,11L20,6M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.11,4 20,4Z"/></svg><input type="email" value="<?php echo escape($user['email']); ?>" disabled></div></div>
<div class="cf"><button type="submit" name="update_info" class="btn btn-pri btn-full"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/></svg>ذخیره تغییرات</button></div>
</form>
</div></div>
</div>

<!-- TAB: آواتار -->
<div class="tab-pane" id="tab-avatar">
<div class="cards-2"><div class="card">
<div class="card-head"><div class="card-icon"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19M8.5,13.5L11,16.51L14.5,12L19,18H5L8.5,13.5Z"/></svg></div><div class="card-title">آپلود تصویر پروفایل</div></div>
<div style="text-align:center;margin-bottom:1.2rem">
<div style="width:90px;height:90px;border-radius:50%;overflow:hidden;margin:0 auto;background:var(--g100);display:flex;align-items:center;justify-content:center;border:3px solid var(--g200)">
<?php if($avatar_src): ?><img src="<?php echo escape($avatar_src); ?>?v=<?php echo filemtime($avatar_src); ?>" style="width:100%;height:100%;object-fit:cover">
<?php else: ?><span style="font-size:2.5rem;font-weight:900;color:var(--mid)"><?php echo $avatar_letter; ?></span><?php endif; ?>
</div>
<p style="font-size:.8rem;color:var(--g400);margin-top:.5rem">تصویر فعلی</p>
</div>
<form method="POST" enctype="multipart/form-data">
<label for="avatarFileInput" class="upload-zone" id="uploadZone">
<svg viewBox="0 0 24 24" fill="currentColor"><path d="M9,16V10H5L12,3L19,10H15V16H9M5,20V18H19V20H5Z"/></svg>
<p><strong>انتخاب تصویر</strong> یا بکشید و رها کنید<br>JPG, PNG, GIF, WebP (حداکثر ۳MB)</p>
<input type="file" name="avatar" id="avatarFileInput" accept="image/*" onchange="previewAvatar(this)">
</label>
<div id="previewBox" style="display:none;text-align:center;margin-top:1rem">
<img id="previewImg" style="width:90px;height:90px;border-radius:50%;object-fit:cover;border:3px solid var(--br)">
<p style="font-size:.8rem;color:var(--br);margin-top:.4rem;font-weight:700">پیش‌نمایش تصویر جدید</p>
</div>
<div class="cf" style="margin-top:1.2rem">
<button type="submit" name="upload_avatar" class="btn btn-pri" id="uploadBtn" style="display:none;flex:1"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M9,16V10H5L12,3L19,10H15V16H9M5,20V18H19V20H5Z"/></svg>آپلود تصویر</button>
<?php if($avatar_src): ?><button type="submit" name="delete_avatar" class="btn btn-danger" style="flex:1" onclick="return confirm('تصویر حذف شود؟')"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M19,4H15.5L14.5,3H9.5L8.5,4H5V6H19M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19Z"/></svg>حذف تصویر</button><?php endif; ?>
</div>
</form>
</div></div>
</div>

<!-- TAB: رمز -->
<div class="tab-pane" id="tab-password">
<div class="cards-2"><div class="card">
<div class="card-head"><div class="card-icon"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,17A2,2 0 0,0 14,15C14,13.89 13.1,13 12,13A2,2 0 0,0 10,15A2,2 0 0,0 12,17M18,8A2,2 0 0,1 20,10V20A2,2 0 0,1 18,22H6A2,2 0 0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0 0,1 12,1A5,5 0 0,1 17,6V8H18M12,3A3,3 0 0,0 9,6V8H15V6A3,3 0 0,0 12,3Z"/></svg></div><div class="card-title">تغییر رمز عبور</div></div>
<form method="POST">
<?php foreach([['current_password','p1','رمز فعلی'],['new_password','p2','رمز جدید'],['confirm_password','p3','تکرار رمز']] as [$n,$id,$lbl]): ?>
<div class="fg"><label><?php echo $lbl; ?></label><div class="field">
<svg class="fi" viewBox="0 0 24 24" fill="currentColor"><path d="M12,17A2,2 0 0,0 14,15C14,13.89 13.1,13 12,13A2,2 0 0,0 10,15A2,2 0 0,0 12,17M18,8A2,2 0 0,1 20,10V20A2,2 0 0,1 18,22H6A2,2 0 0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0 0,1 12,1A5,5 0 0,1 17,6V8H18M12,3A3,3 0 0,0 9,6V8H15V6A3,3 0 0,0 12,3Z"/></svg>
<input type="password" name="<?php echo $n; ?>" id="<?php echo $id; ?>">
<button type="button" class="fe" onclick="toggleEye('<?php echo $id; ?>',this)"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9M12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z"/></svg></button>
</div></div>
<?php endforeach; ?>
<div class="cf"><button type="submit" name="change_password" class="btn btn-pri btn-full"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/></svg>تغییر رمز</button></div>
</form>
</div></div>
</div>

</div><!-- /.main -->

<script>
function showTab(name,btn){
    document.querySelectorAll('.tab-pane').forEach(p=>p.classList.remove('active'));
    document.querySelectorAll('.tab').forEach(t=>t.classList.remove('active'));
    document.getElementById('tab-'+name).classList.add('active');
    if(btn)btn.classList.add('active');
}
const EO='<path d="M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9M12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z"/>';
const EC='<path d="M11.83,9L15,12.16C15,12.11 15,12.05 15,12A3,3 0 0,0 12,9C11.94,9 11.89,9 11.83,9M7.53,9.8L9.08,11.35C9.03,11.56 9,11.77 9,12A3,3 0 0,0 12,15C12.22,15 12.44,14.97 12.65,14.92L14.2,16.47C13.53,16.8 12.79,17 12,17A5,5 0 0,1 7,12C7,11.21 7.2,10.47 7.53,9.8M2,4.27L4.28,6.55L4.73,7C3.08,8.3 1.78,10 1,12C2.73,16.39 7,19.5 12,19.5C13.55,19.5 15.03,19.2 16.38,18.66L16.81,19.08L19.73,22L21,20.73L3.27,3M12,7C14.76,7 17,9.24 17,12C17,12.64 16.87,13.26 16.64,13.82L19.57,16.75C21.07,15.5 22.27,13.86 23,12C21.27,7.61 17,4.5 12,4.5C10.6,4.5 9.26,4.75 8,5.2L10.17,7.35C10.74,7.13 11.35,7 12,7Z"/>';
function toggleEye(id,btn){const i=document.getElementById(id);const h=i.type==='password';i.type=h?'text':'password';btn.querySelector('svg').innerHTML=h?EC:EO}
function previewAvatar(input){
    if(!input.files||!input.files[0])return;
    const r=new FileReader();
    r.onload=e=>{document.getElementById('previewImg').src=e.target.result;document.getElementById('previewBox').style.display='block';document.getElementById('uploadBtn').style.display='flex'};
    r.readAsDataURL(input.files[0]);
}
const zone=document.getElementById('uploadZone');
if(zone){
    zone.addEventListener('dragover',e=>{e.preventDefault();zone.style.borderColor='var(--br)'});
    zone.addEventListener('dragleave',()=>{zone.style.borderColor=''});
    zone.addEventListener('drop',e=>{e.preventDefault();zone.style.borderColor='';const f=e.dataTransfer.files[0];if(f){document.getElementById('avatarFileInput').files=e.dataTransfer.files;previewAvatar({files:[f]})}});
}
const MOON='<path d="M17.75,4.09L15.22,6.03L16.13,9.09L13.5,7.28L10.87,9.09L11.78,6.03L9.25,4.09L12.44,4L13.5,1L14.56,4L17.75,4.09M21.25,11L19.61,12.25L20.2,14.23L18.5,13.06L16.8,14.23L17.39,12.25L15.75,11L17.81,10.95L18.5,9L19.19,10.95L21.25,11M18.97,15.95C19.8,15.87 20.69,17.05 20.16,17.8C19.84,18.25 19.5,18.67 19.08,19.07C15.17,23 8.84,23 4.94,19.07C1.03,15.17 1.03,8.83 4.94,4.93C5.34,4.53 5.76,4.17 6.21,3.85C6.96,3.32 8.14,4.21 8.06,5.04C7.79,7.9 8.75,10.87 10.95,13.06C13.14,15.26 16.1,16.22 18.97,15.95M17.33,17.97C14.5,17.81 11.7,16.64 9.53,14.5C7.36,12.31 6.2,9.5 6.04,6.68C3.23,9.82 3.34,14.64 6.35,17.66C9.37,20.67 14.19,20.78 17.33,17.97Z"/>';
const SUN='<path d="M12,8A4,4 0 0,0 8,12A4,4 0 0,0 12,16A4,4 0 0,0 16,12A4,4 0 0,0 12,8M12,18A6,6 0 0,1 6,12A6,6 0 0,1 12,6A6,6 0 0,1 18,12A6,6 0 0,1 12,18M20,8.69V4H15.31L12,0.69L8.69,4H4V8.69L0.69,12L4,15.31V20H8.69L12,23.31L15.31,20H20V15.31L23.31,12L20,8.69Z"/>';
const dBtn=document.getElementById('darkToggle'),dIco=document.getElementById('darkIcon');
if(localStorage.getItem('darkMode')==='enabled'){document.body.classList.add('dark');dIco.innerHTML=SUN}
dBtn.onclick=()=>{const d=document.body.classList.toggle('dark');localStorage.setItem('darkMode',d?'enabled':'disabled');dIco.innerHTML=d?SUN:MOON};
</script>
</body>
</html>