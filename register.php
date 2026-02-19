<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once("./include/config.php");
require_once("./include/db.php");

if (isset($_SESSION['member_id'])) { header("Location: index.php"); exit(); }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm  = trim($_POST['confirm']  ?? '');
    $phone    = trim($_POST['phone']    ?? '');

    if (!$name || !$email || !$password || !$confirm) {
        $error = 'لطفاً همه فیلدهای ضروری را پر کنید.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'فرمت ایمیل صحیح نیست.';
    } elseif (mb_strlen($password) < 6) {
        $error = 'رمز عبور باید حداقل ۶ کاراکتر باشد.';
    } elseif ($password !== $confirm) {
        $error = 'رمز عبور و تکرار آن یکسان نیستند.';
    } else {
        try {
            $exists = fetchOne($db, "SELECT id FROM members WHERE email = ?", [$email]);
            if ($exists) {
                $error = 'این ایمیل قبلاً ثبت شده است.';
            } else {
                $hashed = password_hash($password, PASSWORD_BCRYPT);
                $result = executeQuery($db,
                    "INSERT INTO members (name, email, password, phone) VALUES (?, ?, ?, ?)",
                    [$name, $email, $hashed, $phone ?: null]
                );
                if ($result) {
                    $newUser = fetchOne($db, "SELECT * FROM members WHERE email = ?", [$email]);
                    $_SESSION['member_id']   = $newUser['id'];
                    $_SESSION['member_name'] = $newUser['name'];
                    header("Location: index.php"); exit();
                } else {
                    $error = 'خطایی رخ داد. لطفاً دوباره تلاش کنید.';
                }
            }
        } catch (PDOException $e) { $error = 'خطا در اتصال به دیتابیس.'; }
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ثبت‌نام | <?= escape(SITE_NAME) ?></title>
    <style>
@import url('https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css');
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
:root{
    --dp:#0f172a;--mid:#1e3a8a;--br:#3b82f6;--lt:#60a5fa;
    --g50:#f8fafc;--g100:#f1f5f9;--g200:#e2e8f0;
    --g400:#94a3b8;--g600:#475569;--g800:#1e293b;
    --red:#ef4444;--green:#10b981;
}
html,body{min-height:100%}
body{
    font-family:'Vazirmatn',Tahoma,Arial,sans-serif;
    direction:rtl;min-height:100vh;
    display:grid;place-items:center;padding:1.5rem;
    background:var(--dp);overflow-x:hidden;
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
    width:100%;max-width:520px;
    background:rgba(255,255,255,.975);
    border-radius:26px;
    padding:2.4rem 2.4rem;
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
.logo{text-align:center;margin-bottom:1.7rem}
.logo-box{
    display:inline-flex;align-items:center;justify-content:center;
    width:66px;height:66px;
    background:linear-gradient(135deg,var(--mid),var(--br));
    border-radius:17px;font-size:2rem;margin-bottom:.75rem;
    box-shadow:0 8px 22px rgba(30,58,138,.32);
    animation:pop .65s cubic-bezier(.34,1.56,.64,1) both .08s;
}
@keyframes pop{from{transform:scale(.3) rotate(-12deg);opacity:0} to{transform:scale(1) rotate(0);opacity:1}}
.logo-name{
    font-size:1.55rem;font-weight:900;
    background:linear-gradient(135deg,var(--mid),var(--br));
    -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
    margin-bottom:.22rem;
}
.logo-sub{color:var(--g400);font-size:.87rem}

/* هشدار */
.alert{
    display:flex;align-items:center;gap:.6rem;
    padding:.85rem 1rem;border-radius:11px;
    margin-bottom:1.3rem;font-size:.87rem;font-weight:600;
    animation:slideIn .3s ease;
}
@keyframes slideIn{from{opacity:0;transform:translateY(-7px)} to{opacity:1;transform:none}}
.alert svg{width:18px;height:18px;flex-shrink:0}
.alert-err{background:rgba(239,68,68,.08);color:var(--red);border:1.5px solid rgba(239,68,68,.18)}

/* گرید فرم */
.fgrid{display:grid;grid-template-columns:1fr 1fr;gap:1rem 1.1rem}
.full{grid-column:1/-1}

/* گروه فیلد */
.fg{/* هیچ margin پیش‌فرض ندارد؛ فاصله از gap */}
.fg label{display:block;font-weight:700;font-size:.83rem;color:var(--g800);margin-bottom:.42rem}
.field{position:relative}
.fi{
    position:absolute;right:.85rem;top:50%;transform:translateY(-50%);
    width:16px;height:16px;color:var(--g400);pointer-events:none;transition:color .2s;
}
.fe{
    position:absolute;left:.85rem;top:50%;transform:translateY(-50%);
    background:none;border:none;cursor:pointer;color:var(--g400);padding:0;display:flex;
    transition:color .2s;
}
.fe:hover{color:var(--br)}
.fe svg{width:16px;height:16px;display:block}
.field input{
    width:100%;
    padding:.78rem 2.4rem .78rem 2.4rem;
    border:2px solid var(--g200);border-radius:11px;
    font-size:.91rem;font-family:'Vazirmatn',Tahoma,Arial,sans-serif;
    color:var(--g800);background:var(--g50);
    transition:border-color .25s,box-shadow .25s,background .25s;outline:none;
}
.field input:focus{
    border-color:var(--br);background:#fff;
    box-shadow:0 0 0 3px rgba(59,130,246,.1);
}
.field input::placeholder{color:var(--g400)}

/* نوار قدرت رمز */
.strength{margin-top:.4rem}
.sbar{height:3.5px;border-radius:3px;background:var(--g200);overflow:hidden}
.sfill{height:100%;border-radius:3px;transition:all .4s;width:0}
.stxt{font-size:.76rem;color:var(--g400);margin-top:.28rem}

/* چک‌باکس */
.chk{display:flex;align-items:center;gap:.65rem;cursor:pointer;font-size:.87rem;color:var(--g600)}
.chk input[type=checkbox]{width:17px;height:17px;accent-color:var(--br);cursor:pointer;padding:0;flex-shrink:0}
.chk a{color:var(--br);font-weight:700;text-decoration:none}

/* دکمه */
.btn{
    width:100%;padding:.95rem;margin-top:1.3rem;
    background:linear-gradient(135deg,var(--mid),var(--br));
    color:#fff;border:none;border-radius:13px;
    font-size:1rem;font-weight:800;
    font-family:'Vazirmatn',Tahoma,Arial,sans-serif;
    cursor:pointer;display:flex;align-items:center;justify-content:center;gap:.65rem;
    box-shadow:0 8px 22px rgba(30,58,138,.28);
    transition:transform .25s,box-shadow .25s;
    position:relative;overflow:hidden;
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
.div{display:flex;align-items:center;gap:.9rem;margin:1.4rem 0;color:var(--g400);font-size:.82rem}
.div::before,.div::after{content:'';flex:1;height:1px;background:var(--g200)}

/* سوئیچ */
.sw{text-align:center;color:var(--g600);font-size:.87rem}
.sw a{color:var(--br);font-weight:700;text-decoration:none;transition:opacity .2s}
.sw a:hover{opacity:.75}

/* responsive */
@media(max-width:560px){
    .fgrid{grid-template-columns:1fr}
    .full{grid-column:1}
    .card{padding:2rem 1.4rem;border-radius:20px}
    .logo-box{width:58px;height:58px;font-size:1.75rem}
}
@media(max-width:380px){body{padding:1rem .6rem}.card{padding:1.6rem 1rem}}
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
        <p class="logo-sub">حساب کاربری رایگان بسازید</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-err">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M13,13H11V7H13M13,17H11V15H13M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z"/></svg>
            <?= escape($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" novalidate>
        <div class="fgrid">

            <!-- نام -->
            <div class="fg">
                <label>نام کامل *</label>
                <div class="field">
                    <svg class="fi" viewBox="0 0 24 24" fill="currentColor"><path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/></svg>
                    <input type="text" name="name" placeholder="نام و نام‌خانوادگی"
                           value="<?= escape($_POST['name'] ?? '') ?>" required>
                </div>
            </div>

            <!-- موبایل -->
            <div class="fg">
                <label>شماره موبایل</label>
                <div class="field">
                    <svg class="fi" viewBox="0 0 24 24" fill="currentColor"><path d="M6.62,10.79C8.06,13.62 10.38,15.94 13.21,17.38L15.41,15.18C15.69,14.9 16.08,14.82 16.43,14.93C17.55,15.3 18.75,15.5 20,15.5A1,1 0 0,1 21,16.5V20A1,1 0 0,1 20,21A17,17 0 0,1 3,4A1,1 0 0,1 4,3H7.5A1,1 0 0,1 8.5,4C8.5,5.25 8.7,6.45 9.07,7.57C9.18,7.92 9.1,8.31 8.82,8.59L6.62,10.79Z"/></svg>
                    <input type="tel" name="phone" placeholder="09XX-XXX-XXXX"
                           value="<?= escape($_POST['phone'] ?? '') ?>">
                </div>
            </div>

            <!-- ایمیل -->
            <div class="fg full">
                <label>ایمیل *</label>
                <div class="field">
                    <svg class="fi" viewBox="0 0 24 24" fill="currentColor"><path d="M20,8L12,13L4,8V6L12,11L20,6M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.11,4 20,4Z"/></svg>
                    <input type="email" name="email" placeholder="example@email.com"
                           value="<?= escape($_POST['email'] ?? '') ?>" required>
                </div>
            </div>

            <!-- رمز عبور -->
            <div class="fg">
                <label>رمز عبور *</label>
                <div class="field">
                    <svg class="fi" viewBox="0 0 24 24" fill="currentColor"><path d="M12,17A2,2 0 0,0 14,15C14,13.89 13.1,13 12,13A2,2 0 0,0 10,15A2,2 0 0,0 12,17M18,8A2,2 0 0,1 20,10V20A2,2 0 0,1 18,22H6A2,2 0 0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0 0,1 12,1A5,5 0 0,1 17,6V8H18M12,3A3,3 0 0,0 9,6V8H15V6A3,3 0 0,0 12,3Z"/></svg>
                    <input type="password" name="password" id="pass1"
                           placeholder="حداقل ۶ کاراکتر"
                           oninput="checkStr(this.value)" required>
                    <button type="button" class="fe" onclick="toggleEye('pass1',this)">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9M12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z"/></svg>
                    </button>
                </div>
                <div class="strength">
                    <div class="sbar"><div class="sfill" id="sf"></div></div>
                    <div class="stxt" id="st"></div>
                </div>
            </div>

            <!-- تکرار رمز -->
            <div class="fg">
                <label>تکرار رمز عبور *</label>
                <div class="field">
                    <svg class="fi" viewBox="0 0 24 24" fill="currentColor"><path d="M12,17A2,2 0 0,0 14,15C14,13.89 13.1,13 12,13A2,2 0 0,0 10,15A2,2 0 0,0 12,17M18,8A2,2 0 0,1 20,10V20A2,2 0 0,1 18,22H6A2,2 0 0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0 0,1 12,1A5,5 0 0,1 17,6V8H18M12,3A3,3 0 0,0 9,6V8H15V6A3,3 0 0,0 12,3Z"/></svg>
                    <input type="password" name="confirm" id="pass2"
                           placeholder="رمز عبور را تکرار کنید" required>
                    <button type="button" class="fe" onclick="toggleEye('pass2',this)">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9M12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z"/></svg>
                    </button>
                </div>
            </div>

            <!-- موافقت -->
            <div class="fg full">
                <label class="chk">
                    <input type="checkbox" name="agree" required>
                    با <a href="#">قوانین و مقررات</a>&nbsp;کتاب نت موافقم
                </label>
            </div>

        </div>

        <button type="submit" class="btn">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M15,14C12.33,14 7,15.33 7,18V20H23V18C23,15.33 17.67,14 15,14M6,10V7H4V10H1V12H4V15H6V12H9V10M15,12A4,4 0 0,0 19,8A4,4 0 0,0 15,4A4,4 0 0,0 11,8A4,4 0 0,0 15,12Z"/></svg>
            ساخت حساب کاربری
        </button>

    </form>

    <div class="div">یا</div>
    <div class="sw">قبلاً ثبت‌نام کرده‌اید؟ <a href="login.php">وارد شوید</a></div>

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

function checkStr(v){
    const sf=document.getElementById('sf');
    const st=document.getElementById('st');
    if(!v){sf.style.width='0';st.textContent='';return}
    let s=0;
    if(v.length>=6)s++;
    if(v.length>=10)s++;
    if(/[A-Za-z]/.test(v))s++;
    if(/[0-9]/.test(v))s++;
    if(/[^A-Za-z0-9]/.test(v))s++;
    const lvl=[
        {w:'20%',c:'#ef4444',l:'خیلی ضعیف'},
        {w:'40%',c:'#f97316',l:'ضعیف'},
        {w:'60%',c:'#eab308',l:'متوسط'},
        {w:'80%',c:'#22c55e',l:'قوی'},
        {w:'100%',c:'#10b981',l:'خیلی قوی'},
    ];
    const idx=Math.min(s-1,4);
    const L=idx>=0?lvl[idx]:lvl[0];
    sf.style.width=L.w;
    sf.style.background=L.c;
    st.textContent='قدرت رمز: '+L.l;
    st.style.color=L.c;
}

if(localStorage.getItem('darkMode')==='enabled') document.body.classList.add('dark-mode');
</script>
</body>
</html>