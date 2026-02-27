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
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>ثبت‌نام | <?=escape(SITE_NAME)?></title>
<style>
@import url('https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css');

*,*::before,*::after { margin:0; padding:0; box-sizing:border-box }

:root {
  --night:   #060b18;
  --deep:    #0a1628;
  --mid:     #0f2352;
  --ac:      #1d4ed8;
  --ac2:     #3b82f6;
  --ac3:     #60a5fa;
  --glow:    rgba(59,130,246,.35);
  --surf:    rgba(255,255,255,.03);
  --surf2:   rgba(255,255,255,.06);
  --brd:     rgba(255,255,255,.09);
  --brd2:    rgba(59,130,246,.3);
  --tx:      #f0f6ff;
  --tx2:     #94a3b8;
  --tx3:     #475569;
  --red:     #f87171;
  --redl:    rgba(248,113,113,.12);
  --redbrd:  rgba(248,113,113,.25);
  --grn:     #34d399;
}

html, body { min-height: 100% }
body {
  font-family: 'Vazirmatn', Tahoma, sans-serif;
  direction: rtl;
  background: var(--night);
  color: var(--tx);
  display: flex;
  min-height: 100vh;
}

#cvs { position: fixed; inset: 0; z-index: 0; pointer-events: none }

/* ══ LEFT PANEL ══ */
.panel-left {
  display: none;
  position: relative; z-index: 1;
  width: 48%;
  background: linear-gradient(145deg, var(--deep) 0%, var(--mid) 100%);
  border-left: 1px solid var(--brd);
  overflow: hidden;
  align-items: center; justify-content: center;
  flex-direction: column; gap: 2rem;
  padding: 3rem 2.5rem;
}
@media(min-width:960px) { .panel-left { display: flex } }

.panel-left::before {
  content: '';
  position: absolute; inset: 0;
  background:
    radial-gradient(ellipse 70% 60% at 80% 20%, rgba(59,130,246,.16) 0%, transparent 60%),
    radial-gradient(ellipse 50% 80% at 20% 90%, rgba(30,58,138,.22) 0%, transparent 55%);
}
.grid-lines { position: absolute; inset: 0; overflow: hidden; opacity: .4 }
.grid-lines::before {
  content: ''; position: absolute; inset: -50%;
  background-image:
    linear-gradient(rgba(59,130,246,.08) 1px, transparent 1px),
    linear-gradient(90deg, rgba(59,130,246,.08) 1px, transparent 1px);
  background-size: 60px 60px;
  animation: gridMove 25s linear infinite;
}
@keyframes gridMove { from{transform:translate(0,0)} to{transform:translate(60px,60px)} }

.shape { position:absolute; border:1px solid rgba(59,130,246,.12); border-radius:50%; animation:shapeFloat linear infinite }
.shape:nth-child(1) { width:320px;height:320px;top:5%;right:-100px;animation-duration:28s }
.shape:nth-child(2) { width:180px;height:180px;bottom:10%;left:-50px;animation-duration:20s;animation-delay:-9s;border-color:rgba(96,165,250,.1) }
.shape:nth-child(3) { width:120px;height:120px;top:50%;right:35%;animation-duration:16s;animation-delay:-13s;border-radius:18px }
@keyframes shapeFloat {
  0%,100%{transform:translate(0,0) rotate(0deg)} 25%{transform:translate(20px,-28px) rotate(8deg)}
  50%{transform:translate(-14px,18px) rotate(-4deg)} 75%{transform:translate(22px,8px) rotate(6deg)}
}

/* steps */
.steps {
  position: relative; z-index: 2;
  display: flex; flex-direction: column; gap: 1.1rem;
  width: 100%; max-width: 320px;
}
.step {
  display: flex; align-items: center; gap: 1rem;
  background: var(--surf2); border: 1px solid var(--brd);
  border-radius: 14px; padding: 1rem 1.2rem;
  backdrop-filter: blur(6px);
  animation: stepIn .5s ease both;
}
.step:nth-child(1) { animation-delay: .1s }
.step:nth-child(2) { animation-delay: .2s }
.step:nth-child(3) { animation-delay: .3s }
.step:nth-child(4) { animation-delay: .4s }
@keyframes stepIn { from{opacity:0;transform:translateX(20px)} to{opacity:1;transform:none} }
.step-num {
  width: 34px; height: 34px; border-radius: 10px; flex-shrink: 0;
  background: linear-gradient(135deg, var(--ac), var(--ac2));
  display: flex; align-items: center; justify-content: center;
  font-size: .85rem; font-weight: 900; color: #fff;
  box-shadow: 0 4px 12px rgba(29,78,216,.3);
}
.step-text { font-size: .84rem; font-weight: 600; color: var(--tx2); line-height: 1.4 }
.step-text strong { color: var(--tx); display: block; font-size: .88rem; margin-bottom: .1rem }

/* panel heading */
.panel-head { position: relative; z-index: 2; text-align: center }
.panel-logo {
  width: 72px; height: 72px; border-radius: 20px;
  background: linear-gradient(135deg, var(--ac), var(--ac2));
  display: flex; align-items: center; justify-content: center;
  font-size: 2rem; margin: 0 auto 1.2rem;
  box-shadow: 0 0 0 1px var(--brd2), 0 16px 50px rgba(29,78,216,.38), 0 0 70px rgba(59,130,246,.12);
  animation: logoPulse 4s ease-in-out infinite;
}
@keyframes logoPulse {
  0%,100%{box-shadow:0 0 0 1px var(--brd2),0 16px 50px rgba(29,78,216,.38),0 0 70px rgba(59,130,246,.12)}
  50%    {box-shadow:0 0 0 1px var(--brd2),0 16px 50px rgba(29,78,216,.55),0 0 110px rgba(59,130,246,.22)}
}
.panel-title {
  font-size: 1.75rem; font-weight: 800;
  background: linear-gradient(135deg, #fff 30%, var(--ac3) 100%);
  -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
  margin-bottom: .4rem;
}
.panel-sub { color: var(--tx2); font-size: .88rem }

/* ══ RIGHT PANEL ══ */
.panel-right {
  flex: 1; display: flex; align-items: center; justify-content: center;
  position: relative; z-index: 1;
  padding: 2rem 1.5rem;
  min-height: 100vh;
}

.form-box {
  width: 100%; max-width: 460px;
  animation: boxIn .55s cubic-bezier(.22,1,.36,1) both;
}
@keyframes boxIn { from{opacity:0;transform:translateY(22px)} to{opacity:1;transform:none} }

.mob-logo {
  display: flex; align-items: center; gap: .75rem;
  margin-bottom: 2rem; justify-content: center;
}
@media(min-width:960px) { .mob-logo { display: none } }
.mob-logo-ic {
  width: 44px; height: 44px; border-radius: 12px;
  background: linear-gradient(135deg, var(--ac), var(--ac2));
  display: flex; align-items: center; justify-content: center; font-size: 1.3rem;
  box-shadow: 0 8px 22px rgba(29,78,216,.35);
}
.mob-logo-name { font-size: 1.25rem; font-weight: 800 }

.back-link {
  display: inline-flex; align-items: center; gap: .35rem;
  color: var(--tx2); font-size: .8rem; text-decoration: none;
  margin-bottom: 1.75rem; transition: color .2s;
}
.back-link:hover { color: var(--ac2) }
.back-link svg { width: 14px; height: 14px }

.form-title { font-size: 1.55rem; font-weight: 800; color: var(--tx); margin-bottom: .3rem }
.form-sub { color: var(--tx2); font-size: .86rem; margin-bottom: 1.75rem; line-height: 1.6 }

/* alert */
.alert {
  display: flex; align-items: center; gap: .6rem;
  padding: .9rem 1.1rem; border-radius: 12px;
  margin-bottom: 1.4rem; font-size: .85rem; font-weight: 600;
  border: 1px solid var(--redbrd); background: var(--redl); color: var(--red);
  animation: alertIn .3s ease;
}
@keyframes alertIn { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:none} }
.alert svg { width: 16px; height: 16px; flex-shrink: 0 }

/* grid */
.fgrid { display: grid; grid-template-columns: 1fr 1fr; gap: .9rem 1rem }
.full { grid-column: 1 / -1 }

/* field */
.fg { }
.fg-label { display: block; font-size: .8rem; font-weight: 700; color: var(--tx2); margin-bottom: .48rem }
.field { position: relative }
.fic { position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); width: 15px; height: 15px; color: var(--tx3); pointer-events: none; transition: color .2s }
.feye { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--tx3); padding: 0; display: flex; transition: color .2s }
.feye:hover { color: var(--ac2) }
.feye svg { width: 15px; height: 15px }
.finput {
  width: 100%;
  padding: .84rem 2.6rem .84rem 1rem;
  background: var(--surf2); border: 1px solid var(--brd); border-radius: 11px;
  color: var(--tx); font-size: .88rem; font-family: 'Vazirmatn', Tahoma, sans-serif;
  outline: none; transition: all .22s;
}
.finput:focus { border-color: var(--ac2); background: rgba(255,255,255,.07); box-shadow: 0 0 0 3px rgba(59,130,246,.12) }
.finput:focus ~ .fic { color: var(--ac2) }
.finput::placeholder { color: var(--tx3) }
.has-eye .finput { padding-left: 2.6rem }

/* strength */
.strength { margin-top: .45rem }
.sbar { height: 3px; border-radius: 3px; background: var(--brd); overflow: hidden }
.sfill { height: 100%; border-radius: 3px; width: 0; transition: all .4s }
.stxt { font-size: .72rem; color: var(--tx3); margin-top: .3rem }

/* checkbox */
.chk {
  display: flex; align-items: center; gap: .65rem;
  cursor: pointer; font-size: .84rem; color: var(--tx2);
}
.chk input[type=checkbox] { width: 16px; height: 16px; accent-color: var(--ac2); cursor: pointer; flex-shrink: 0 }
.chk a { color: var(--ac2); font-weight: 700; text-decoration: none }
.chk a:hover { color: var(--ac3) }

/* submit */
.submit-btn {
  width: 100%; padding: .95rem; margin-top: 1.2rem;
  background: linear-gradient(135deg, var(--ac) 0%, var(--ac2) 100%);
  border: none; border-radius: 13px;
  color: #fff; font-size: .96rem; font-weight: 800;
  font-family: 'Vazirmatn', Tahoma, sans-serif;
  cursor: pointer; display: flex; align-items: center; justify-content: center; gap: .6rem;
  position: relative; overflow: hidden;
  box-shadow: 0 8px 28px rgba(29,78,216,.35), inset 0 1px 0 rgba(255,255,255,.15);
  transition: all .28s;
}
.submit-btn::before {
  content: ''; position: absolute;
  top: 0; left: -100%; width: 100%; height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,.12), transparent);
  transition: left .55s;
}
.submit-btn:hover { transform: translateY(-2px); box-shadow: 0 14px 38px rgba(29,78,216,.5), inset 0 1px 0 rgba(255,255,255,.15) }
.submit-btn:hover::before { left: 100% }
.submit-btn:active { transform: translateY(0) }
.submit-btn svg { width: 18px; height: 18px }

/* divider */
.divider { display: flex; align-items: center; gap: .85rem; color: var(--tx3); font-size: .78rem; margin: 1.5rem 0 }
.divider::before,.divider::after { content:''; flex:1; height:1px; background:var(--brd) }

/* switch */
.switch-text { text-align: center; color: var(--tx2); font-size: .84rem }
.switch-text a { color: var(--ac2); font-weight: 700; text-decoration: none }
.switch-text a:hover { color: var(--ac3) }

@media(max-width:540px) {
  .fgrid { grid-template-columns: 1fr }
  .full { grid-column: 1 }
}
</style>
</head>
<body>

<canvas id="cvs"></canvas>

<!-- LEFT PANEL -->
<div class="panel-left">
  <div class="grid-lines"></div>
  <div class="shape"></div>
  <div class="shape"></div>
  <div class="shape"></div>

  <div class="panel-head">
    <div class="panel-logo">📚</div>
    <div class="panel-title"><?=escape(SITE_NAME)?></div>
    <p class="panel-sub">ثبت‌نام رایگان — در کمتر از یک دقیقه</p>
  </div>

  <div class="steps">
    <div class="step">
      <div class="step-num">۱</div>
      <div class="step-text"><strong>حساب بسازید</strong>ایمیل و رمز عبور وارد کنید</div>
    </div>
    <div class="step">
      <div class="step-num">۲</div>
      <div class="step-text"><strong>کتاب انتخاب کنید</strong>از هزاران عنوان کتاب</div>
    </div>
    <div class="step">
      <div class="step-num">۳</div>
      <div class="step-text"><strong>خرید امن</strong>درگاه پرداخت اختصاصی</div>
    </div>
    <div class="step">
      <div class="step-num">۴</div>
      <div class="step-text"><strong>تحویل سریع</strong>درب منزل یا دانلود فوری</div>
    </div>
  </div>
</div>

<!-- RIGHT PANEL -->
<div class="panel-right">
  <div class="form-box">

    <div class="mob-logo">
      <div class="mob-logo-ic">📚</div>
      <div class="mob-logo-name"><?=escape(SITE_NAME)?></div>
    </div>

    <a href="index.php" class="back-link">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M10,20V14H14V20H19V12H22L12,3L2,12H5V20H10Z"/></svg>
      بازگشت به خانه
    </a>

    <h1 class="form-title">حساب رایگان بسازید ✨</h1>
    <p class="form-sub">به جامعه کتاب‌خوانان کتاب نت بپیوندید</p>

    <?php if($error): ?>
    <div class="alert">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M13,13H11V7H13M13,17H11V15H13M12,2A10,10 0,0,0 2,12A10,10 0,0,0 12,22A10,10 0,0,0 22,12A10,10 0,0,0 12,2Z"/></svg>
      <?=escape($error)?>
    </div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <div class="fgrid">

        <div class="fg">
          <label class="fg-label">نام کامل *</label>
          <div class="field">
            <input class="finput" type="text" name="name"
              placeholder="نام و نام‌خانوادگی"
              value="<?=escape($_POST['name']??'')?>" required>
            <svg class="fic" viewBox="0 0 24 24" fill="currentColor"><path d="M12,4A4,4 0,0,1 16,8A4,4 0,0,1 12,12A4,4 0,0,1 8,8A4,4 0,0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/></svg>
          </div>
        </div>

        <div class="fg">
          <label class="fg-label">شماره موبایل</label>
          <div class="field">
            <input class="finput" type="tel" name="phone"
              placeholder="09XX-XXX-XXXX"
              value="<?=escape($_POST['phone']??'')?>">
            <svg class="fic" viewBox="0 0 24 24" fill="currentColor"><path d="M6.62,10.79C8.06,13.62 10.38,15.94 13.21,17.38L15.41,15.18C15.69,14.9 16.08,14.82 16.43,14.93C17.55,15.3 18.75,15.5 20,15.5A1,1 0,0,1 21,16.5V20A1,1 0,0,1 20,21A17,17 0,0,1 3,4A1,1 0,0,1 4,3H7.5A1,1 0,0,1 8.5,4C8.5,5.25 8.7,6.45 9.07,7.57C9.18,7.92 9.1,8.31 8.82,8.59L6.62,10.79Z"/></svg>
          </div>
        </div>

        <div class="fg full">
          <label class="fg-label">آدرس ایمیل *</label>
          <div class="field">
            <input class="finput" type="email" name="email"
              placeholder="example@email.com"
              value="<?=escape($_POST['email']??'')?>" required>
            <svg class="fic" viewBox="0 0 24 24" fill="currentColor"><path d="M20,8L12,13L4,8V6L12,11L20,6M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0,0,0 4,20H20A2,2 0,0,0 22,18V6C22,4.89 21.11,4 20,4Z"/></svg>
          </div>
        </div>

        <div class="fg">
          <label class="fg-label">رمز عبور *</label>
          <div class="field has-eye">
            <input class="finput" type="password" name="password" id="pass1"
              placeholder="حداقل ۶ کاراکتر"
              oninput="checkStr(this.value)" required>
            <svg class="fic" viewBox="0 0 24 24" fill="currentColor"><path d="M12,17A2,2 0,0,0 14,15C14,13.89 13.1,13 12,13A2,2 0,0,0 10,15A2,2 0,0,0 12,17M18,8A2,2 0,0,1 20,10V20A2,2 0,0,1 18,22H6A2,2 0,0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0,0,1 12,1A5,5 0,0,1 17,6V8H18Z"/></svg>
            <button type="button" class="feye" onclick="eyeToggle('pass1',this)">
              <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,9A3,3 0,0,0 9,12A3,3 0,0,0 12,15A3,3 0,0,0 15,12A3,3 0,0,0 12,9M12,17A5,5 0,0,1 7,12A5,5 0,0,1 12,7A5,5 0,0,1 17,12A5,5 0,0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z"/></svg>
            </button>
          </div>
          <div class="strength">
            <div class="sbar"><div class="sfill" id="sf"></div></div>
            <div class="stxt" id="st"></div>
          </div>
        </div>

        <div class="fg">
          <label class="fg-label">تکرار رمز عبور *</label>
          <div class="field has-eye">
            <input class="finput" type="password" name="confirm" id="pass2"
              placeholder="رمز را تکرار کنید" required>
            <svg class="fic" viewBox="0 0 24 24" fill="currentColor"><path d="M12,17A2,2 0,0,0 14,15C14,13.89 13.1,13 12,13A2,2 0,0,0 10,15A2,2 0,0,0 12,17M18,8A2,2 0,0,1 20,10V20A2,2 0,0,1 18,22H6A2,2 0,0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0,0,1 12,1A5,5 0,0,1 17,6V8H18Z"/></svg>
            <button type="button" class="feye" onclick="eyeToggle('pass2',this)">
              <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,9A3,3 0,0,0 9,12A3,3 0,0,0 12,15A3,3 0,0,0 15,12A3,3 0,0,0 12,9M12,17A5,5 0,0,1 7,12A5,5 0,0,1 12,7A5,5 0,0,1 17,12A5,5 0,0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z"/></svg>
            </button>
          </div>
        </div>

        <div class="fg full">
          <label class="chk">
            <input type="checkbox" name="agree" required>
            با <a href="#">قوانین و مقررات</a>&nbsp;کتاب نت موافقم
          </label>
        </div>

      </div>

      <button type="submit" class="submit-btn">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M15,14C12.33,14 7,15.33 7,18V20H23V18C23,15.33 17.67,14 15,14M6,10V7H4V10H1V12H4V15H6V12H9V10M15,12A4,4 0,0,0 19,8A4,4 0,0,0 15,4A4,4 0,0,0 11,8A4,4 0,0,0 15,12Z"/></svg>
        ساخت حساب کاربری
      </button>

    </form>

    <div class="divider">یا</div>
    <div class="switch-text">قبلاً ثبت‌نام کرده‌اید؟ <a href="login.php">وارد شوید</a></div>

  </div>
</div>

<script>
/* ── Particle canvas ── */
const cvs = document.getElementById('cvs');
const ctx = cvs.getContext('2d');
let W, H, pts = [];
function resize() { W = cvs.width = window.innerWidth; H = cvs.height = window.innerHeight }
resize();
window.addEventListener('resize', () => { resize(); initPts() });
function initPts() {
  pts = [];
  const n = Math.floor((W * H) / 20000);
  for (let i = 0; i < n; i++) pts.push({ x:Math.random()*W, y:Math.random()*H, vx:(Math.random()-.5)*.3, vy:(Math.random()-.5)*.3, r:Math.random()*1.4+.4, a:Math.random()*.5+.1 });
}
initPts();
function draw() {
  ctx.clearRect(0,0,W,H);
  for (let i = 0; i < pts.length; i++) {
    const p = pts[i]; p.x+=p.vx; p.y+=p.vy;
    if(p.x<0||p.x>W) p.vx*=-1;
    if(p.y<0||p.y>H) p.vy*=-1;
    ctx.beginPath(); ctx.arc(p.x,p.y,p.r,0,Math.PI*2);
    ctx.fillStyle=`rgba(96,165,250,${p.a})`; ctx.fill();
    for (let j=i+1;j<pts.length;j++) {
      const q=pts[j]; const dx=p.x-q.x,dy=p.y-q.y,d=Math.sqrt(dx*dx+dy*dy);
      if(d<100) { ctx.beginPath(); ctx.moveTo(p.x,p.y); ctx.lineTo(q.x,q.y); ctx.strokeStyle=`rgba(59,130,246,${.15*(1-d/100)})`; ctx.lineWidth=.5; ctx.stroke() }
    }
  }
  requestAnimationFrame(draw);
}
draw();

/* ── Eye toggle ── */
const EO='<path d="M12,9A3,3 0,0,0 9,12A3,3 0,0,0 12,15A3,3 0,0,0 15,12A3,3 0,0,0 12,9M12,17A5,5 0,0,1 7,12A5,5 0,0,1 12,7A5,5 0,0,1 17,12A5,5 0,0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z"/>';
const EC='<path d="M11.83,9L15,12.16C15,12.11 15,12.05 15,12A3,3 0,0,0 12,9C11.94,9 11.89,9 11.83,9M7.53,9.8L9.08,11.35C9.03,11.56 9,11.77 9,12A3,3 0,0,0 12,15C12.22,15 12.44,14.97 12.65,14.92L14.2,16.47C13.53,16.8 12.79,17 12,17A5,5 0,0,1 7,12C7,11.21 7.2,10.47 7.53,9.8M2,4.27L4.28,6.55L4.73,7C3.08,8.3 1.78,10 1,12C2.73,16.39 7,19.5 12,19.5C13.55,19.5 15.03,19.2 16.38,18.66L16.81,19.08L19.73,22L21,20.73L3.27,3M12,7C14.76,7 17,9.24 17,12C17,12.64 16.87,13.26 16.64,13.82L19.57,16.75C21.07,15.5 22.27,13.86 23,12C21.27,7.61 17,4.5 12,4.5C10.6,4.5 9.26,4.75 8,5.2L10.17,7.35C10.74,7.13 11.35,7 12,7Z"/>';
function eyeToggle(id,btn) {
  const el=document.getElementById(id),h=el.type==='password';
  el.type=h?'text':'password'; btn.querySelector('svg').innerHTML=h?EC:EO;
}

/* ── Password strength ── */
function checkStr(v) {
  const sf=document.getElementById('sf'), st=document.getElementById('st');
  if(!v){sf.style.width='0';st.textContent='';return}
  let s=0;
  if(v.length>=6)s++; if(v.length>=10)s++;
  if(/[A-Za-z]/.test(v))s++; if(/[0-9]/.test(v))s++; if(/[^A-Za-z0-9]/.test(v))s++;
  const lvl=[{w:'20%',c:'#f87171',l:'خیلی ضعیف'},{w:'40%',c:'#fb923c',l:'ضعیف'},{w:'60%',c:'#fbbf24',l:'متوسط'},{w:'80%',c:'#4ade80',l:'قوی'},{w:'100%',c:'#34d399',l:'خیلی قوی'}];
  const L=lvl[Math.min(s-1,4)];
  sf.style.width=L.w; sf.style.background=L.c;
  st.textContent='قدرت رمز: '+L.l; st.style.color=L.c;
}
</script>
</body>
</html>