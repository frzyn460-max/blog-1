<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once("./include/config.php");
require_once("./include/db.php");

if (isset($_SESSION['member_id'])) { header("Location: index.php"); exit(); }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');
    if (!$email || !$password) {
        $error = 'لطفاً همه فیلدها را پر کنید.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'فرمت ایمیل صحیح نیست.';
    } else {
        try {
            $user = fetchOne($db, "SELECT * FROM members WHERE email = ? AND status = 1", [$email]);
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['member_id']   = $user['id'];
                $_SESSION['member_name'] = $user['name'];
                header("Location: index.php"); exit();
            } else { $error = 'ایمیل یا رمز عبور اشتباه است.'; }
        } catch (PDOException $e) { $error = 'خطا در اتصال به دیتابیس.'; }
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود | <?= escape(SITE_NAME) ?></title>
    <style>
@import url('https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css');
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
:root{
    --dp:#0f172a; --mid:#1e3a8a; --br:#3b82f6; --lt:#60a5fa;
    --g50:#f8fafc; --g100:#f1f5f9; --g200:#e2e8f0;
    --g400:#94a3b8; --g600:#475569; --g800:#1e293b;
    --red:#ef4444; --green:#10b981;
}
html,body{min-height:100%}
body{
    font-family:'Vazirmatn',Tahoma,Arial,sans-serif;
    direction:rtl; min-height:100vh;
    display:grid; place-items:center; padding:1.5rem;
    background:var(--dp); overflow-x:hidden;
}

/* پس‌زمینه */
.bg{position:fixed;inset:0;z-index:0;overflow:hidden}
.bg::before{
    content:'';position:absolute;inset:0;
    background:
        radial-gradient(ellipse 70% 55% at 15% 0%,   rgba(59,130,246,.38) 0%,transparent 65%),
        radial-gradient(ellipse 55% 70% at 92% 105%, rgba(30,58,138,.55)  0%,transparent 60%),
        linear-gradient(155deg,#0f172a 0%,#0d1e4a 55%,#0f172a 100%);
}
.bg-dots{
    position:absolute;inset:0;
    background-image:radial-gradient(circle,rgba(255,255,255,.06) 1px,transparent 1px);
    background-size:28px 28px;
}
.orb{position:absolute;border-radius:50%;filter:blur(55px);opacity:.22;animation:orb 20s ease-in-out infinite}
.orb:nth-child(2){width:380px;height:380px;top:-8%;right:-4%;background:#3b82f6;animation-delay:0s}
.orb:nth-child(3){width:250px;height:250px;bottom:5%;left:-6%;background:#1e3a8a;animation-delay:7s}
.orb:nth-child(4){width:160px;height:160px;bottom:22%;right:8%;background:#60a5fa;animation-delay:14s}
@keyframes orb{
    0%,100%{transform:translate(0,0) scale(1)}
    33%     {transform:translate(28px,-38px) scale(1.07)}
    66%     {transform:translate(-18px,22px) scale(.93)}
}

/* بازگشت */
.back{
    position:fixed;top:1.4rem;right:1.6rem;z-index:99;
    display:inline-flex;align-items:center;gap:.45rem;
    color:rgba(255,255,255,.6);text-decoration:none;
    font-size:.85rem;font-weight:600;transition:color .25s;
}
.back:hover{color:#fff}
.back svg{width:18px;height:18px}

/* کارت */
.card{
    position:relative;z-index:10;
    width:100%;max-width:448px;
    background:rgba(255,255,255,.975);
    border-radius:26px;
    padding:2.6rem 2.4rem;
    box-shadow:0 0 0 1px rgba(255,255,255,.1),0 28px 72px rgba(0,0,0,.42),0 6px 20px rgba(15,23,42,.5);
    animation:cardIn .5s cubic-bezier(.22,1,.36,1) both;
}
.card::before{
    content:'';position:absolute;
    top:0;left:0;right:0;height:4px;
    background:linear-gradient(90deg,var(--mid),var(--br),var(--lt));
    border-radius:26px 26px 0 0;
}
@keyframes cardIn{
    from{opacity:0;transform:translateY(30px) scale(.97)}
    to  {opacity:1;transform:translateY(0)    scale(1)}
}

/* لوگو */
.logo{text-align:center;margin-bottom:1.9rem}
.logo-box{
    display:inline-flex;align-items:center;justify-content:center;
    width:70px;height:70px;
    background:linear-gradient(135deg,var(--mid),var(--br));
    border-radius:18px;font-size:2.1rem;margin-bottom:.8rem;
    box-shadow:0 8px 22px rgba(30,58,138,.32);
    animation:pop .65s cubic-bezier(.34,1.56,.64,1) both .08s;
}
@keyframes pop{from{transform:scale(.3) rotate(-12deg);opacity:0} to{transform:scale(1) rotate(0);opacity:1}}
.logo-name{
    font-size:1.6rem;font-weight:900;
    background:linear-gradient(135deg,var(--mid),var(--br));
    -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
    margin-bottom:.25rem;
}
.logo-sub{color:var(--g400);font-size:.88rem}

/* هشدار */
.alert{
    display:flex;align-items:center;gap:.6rem;
    padding:.85rem 1rem;border-radius:11px;
    margin-bottom:1.4rem;font-size:.88rem;font-weight:600;
    animation:slideIn .3s ease;
}
@keyframes slideIn{from{opacity:0;transform:translateY(-7px)} to{opacity:1;transform:none}}
.alert svg{width:18px;height:18px;flex-shrink:0}
.alert-err{background:rgba(239,68,68,.08);color:var(--red);border:1.5px solid rgba(239,68,68,.18)}

/* فرم */
.fg{margin-bottom:1.2rem}
.fg label{display:block;font-weight:700;font-size:.85rem;color:var(--g800);margin-bottom:.45rem}
.field{position:relative}
.fi{/* field-icon */
    position:absolute;right:.9rem;top:50%;transform:translateY(-50%);
    width:17px;height:17px;color:var(--g400);pointer-events:none;transition:color .2s;
}
.fe{/* field-eye */
    position:absolute;left:.9rem;top:50%;transform:translateY(-50%);
    background:none;border:none;cursor:pointer;color:var(--g400);padding:0;display:flex;
    transition:color .2s;
}
.fe:hover{color:var(--br)}
.fe svg{width:17px;height:17px;display:block}
.field input{
    width:100%;
    padding:.82rem 2.5rem .82rem 2.5rem;
    border:2px solid var(--g200);border-radius:12px;
    font-size:.93rem;font-family:'Vazirmatn',Tahoma,Arial,sans-serif;
    color:var(--g800);background:var(--g50);
    transition:border-color .25s,box-shadow .25s,background .25s;outline:none;
}
.field input:focus{
    border-color:var(--br);background:#fff;
    box-shadow:0 0 0 3.5px rgba(59,130,246,.11);
}
.field input::placeholder{color:var(--g400)}
.field input:focus + .fi{color:var(--br)}

.forgot{
    display:block;text-align:left;
    font-size:.8rem;font-weight:600;color:var(--br);
    text-decoration:none;margin-top:.4rem;transition:opacity .2s;
}
.forgot:hover{opacity:.7}

/* دکمه */
.btn{
    width:100%;padding:.98rem;margin-top:.4rem;
    background:linear-gradient(135deg,var(--mid),var(--br));
    color:#fff;border:none;border-radius:13px;
    font-size:1rem;font-weight:800;
    font-family:'Vazirmatn',Tahoma,Arial,sans-serif;
    cursor:pointer;display:flex;align-items:center;justify-content:center;gap:.65rem;
    box-shadow:0 8px 22px rgba(30,58,138,.28);
    transition:transform .25s,box-shadow .25s;position:relative;overflow:hidden;
}
.btn::after{
    content:'';position:absolute;inset:0;
    background:linear-gradient(135deg,rgba(255,255,255,.14),transparent);
    opacity:0;transition:opacity .25s;
}
.btn:hover{transform:translateY(-3px);box-shadow:0 14px 34px rgba(30,58,138,.38)}
.btn:hover::after{opacity:1}
.btn:active{transform:none}
.btn svg{width:19px;height:19px}

/* جداکننده */
.div{display:flex;align-items:center;gap:.9rem;margin:1.5rem 0;color:var(--g400);font-size:.83rem}
.div::before,.div::after{content:'';flex:1;height:1px;background:var(--g200)}

/* سوئیچ */
.sw{text-align:center;color:var(--g600);font-size:.88rem}
.sw a{color:var(--br);font-weight:700;text-decoration:none;transition:opacity .2s}
.sw a:hover{opacity:.75}

/* responsive */
@media(max-width:500px){.card{padding:2rem 1.4rem;border-radius:20px}.logo-box{width:60px;height:60px;font-size:1.8rem}}
@media(max-width:360px){body{padding:1rem .6rem}.card{padding:1.6rem 1.1rem}}
    </style>
</head>
<body>

<div class="bg">
    <div class="bg-dots"></div>
    <div class="orb"></div>
    <div class="orb"></div>
    <div class="orb"></div>
</div>

<a href="index.php" class="back">
    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M10,20V14H14V20H19V12H22L12,3L2,12H5V20H10Z"/></svg>
    بازگشت به خانه
</a>

<div class="card">

    <div class="logo">
        <div class="logo-box">📚</div>
        <div class="logo-name"><?= escape(SITE_NAME) ?></div>
        <p class="logo-sub">به حساب کاربری خود وارد شوید</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-err">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M13,13H11V7H13M13,17H11V15H13M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z"/></svg>
            <?= escape($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" novalidate>

        <div class="fg">
            <label>ایمیل</label>
            <div class="field">
                <svg class="fi" viewBox="0 0 24 24" fill="currentColor"><path d="M20,8L12,13L4,8V6L12,11L20,6M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.11,4 20,4Z"/></svg>
                <input type="email" name="email" placeholder="example@email.com"
                       value="<?= escape($_POST['email'] ?? '') ?>" autocomplete="email" required>
            </div>
        </div>

        <div class="fg">
            <label>رمز عبور</label>
            <div class="field">
                <svg class="fi" viewBox="0 0 24 24" fill="currentColor"><path d="M12,17A2,2 0 0,0 14,15C14,13.89 13.1,13 12,13A2,2 0 0,0 10,15A2,2 0 0,0 12,17M18,8A2,2 0 0,1 20,10V20A2,2 0 0,1 18,22H6A2,2 0 0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0 0,1 12,1A5,5 0 0,1 17,6V8H18M12,3A3,3 0 0,0 9,6V8H15V6A3,3 0 0,0 12,3Z"/></svg>
                <input type="password" name="password" id="pass1" placeholder="رمز عبور خود را وارد کنید" autocomplete="current-password" required>
                <button type="button" class="fe" onclick="toggleEye('pass1',this)">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9M12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z"/></svg>
                </button>
            </div>
            <a href="forgot_password.php" class="forgot">فراموشی رمز عبور؟</a>
        </div>

        <button type="submit" class="btn">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M10,17V14H3V10H10V7L15,12L10,17M10,2H19A2,2 0 0,1 21,4V20A2,2 0 0,1 19,22H10A2,2 0 0,1 8,20V18H10V20H19V4H10V6H8V4A2,2 0 0,1 10,2Z"/></svg>
            ورود به حساب
        </button>

    </form>

    <div class="div">یا</div>
    <div class="sw">حساب کاربری ندارید؟ <a href="register.php">همین الان ثبت‌نام کنید</a></div>

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
if(localStorage.getItem('darkMode')==='enabled') document.body.classList.add('dark-mode');
</script>
</body>
</html>