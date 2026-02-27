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
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>ورود | <?=escape(SITE_NAME)?></title>
<style>
@import url('https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css');
@import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap');

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
}

html, body { height: 100%; overflow: hidden }
body {
  font-family: 'Vazirmatn', Tahoma, sans-serif;
  direction: rtl;
  background: var(--night);
  color: var(--tx);
  display: flex;
  min-height: 100vh;
  overflow: auto;
}

/* ══ CANVAS BG ══ */
#cvs {
  position: fixed; inset: 0; z-index: 0;
  pointer-events: none;
}

/* ══ LEFT PANEL (decorative) ══ */
.panel-left {
  display: none;
  position: relative; z-index: 1;
  width: 52%;
  background: linear-gradient(145deg, var(--deep) 0%, var(--mid) 100%);
  border-left: 1px solid var(--brd);
  overflow: hidden;
  align-items: center; justify-content: center;
  flex-direction: column; gap: 2rem;
  padding: 3rem;
}
@media(min-width:960px) { .panel-left { display: flex } }

.panel-left::before {
  content: '';
  position: absolute; inset: 0;
  background:
    radial-gradient(ellipse 70% 60% at 80% 20%, rgba(59,130,246,.18) 0%, transparent 60%),
    radial-gradient(ellipse 50% 80% at 20% 90%, rgba(30,58,138,.25) 0%, transparent 55%);
}

/* grid lines */
.grid-lines {
  position: absolute; inset: 0; overflow: hidden; opacity: .4;
}
.grid-lines::before {
  content: '';
  position: absolute; inset: -50%;
  background-image:
    linear-gradient(rgba(59,130,246,.08) 1px, transparent 1px),
    linear-gradient(90deg, rgba(59,130,246,.08) 1px, transparent 1px);
  background-size: 60px 60px;
  animation: gridMove 25s linear infinite;
}
@keyframes gridMove {
  from { transform: translate(0,0) }
  to   { transform: translate(60px,60px) }
}

/* floating shapes */
.shape {
  position: absolute;
  border: 1px solid rgba(59,130,246,.15);
  border-radius: 50%;
  animation: shapeFloat linear infinite;
}
.shape:nth-child(1) { width:380px;height:380px;top:10%;right:-120px;animation-duration:30s;animation-delay:0s }
.shape:nth-child(2) { width:220px;height:220px;bottom:15%;left:-60px;animation-duration:22s;animation-delay:-8s;border-color:rgba(96,165,250,.12) }
.shape:nth-child(3) { width:140px;height:140px;top:55%;right:40%;animation-duration:18s;animation-delay:-14s;border-radius:20px;border-color:rgba(30,58,138,.2) }
@keyframes shapeFloat {
  0%,100% { transform: translate(0,0) rotate(0deg) }
  25%     { transform: translate(20px,-30px) rotate(10deg) }
  50%     { transform: translate(-15px,20px) rotate(-5deg) }
  75%     { transform: translate(25px,10px) rotate(8deg) }
}

.panel-content {
  position: relative; z-index: 2;
  text-align: center;
}
.panel-logo {
  width: 80px; height: 80px;
  background: linear-gradient(135deg, var(--ac) 0%, var(--ac2) 100%);
  border-radius: 22px;
  display: flex; align-items: center; justify-content: center;
  font-size: 2.2rem;
  margin: 0 auto 1.5rem;
  box-shadow: 0 0 0 1px var(--brd2), 0 20px 60px rgba(29,78,216,.4), 0 0 80px rgba(59,130,246,.15);
  animation: logoPulse 4s ease-in-out infinite;
}
@keyframes logoPulse {
  0%,100% { box-shadow: 0 0 0 1px var(--brd2), 0 20px 60px rgba(29,78,216,.4), 0 0 80px rgba(59,130,246,.15) }
  50%     { box-shadow: 0 0 0 1px var(--brd2), 0 20px 60px rgba(29,78,216,.6), 0 0 120px rgba(59,130,246,.25) }
}
.panel-title {
  font-size: 2rem; font-weight: 800;
  background: linear-gradient(135deg, #fff 30%, var(--ac3) 100%);
  -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
  margin-bottom: .6rem; line-height: 1.2;
}
.panel-sub { color: var(--tx2); font-size: .95rem; line-height: 1.8; max-width: 320px }

/* stats row */
.panel-stats {
  display: flex; gap: 1.5rem; margin-top: 2.5rem;
  position: relative; z-index: 2;
}
.pstat {
  background: var(--surf2);
  border: 1px solid var(--brd);
  border-radius: 14px;
  padding: 1rem 1.5rem;
  text-align: center;
  backdrop-filter: blur(8px);
}
.pstat-n { font-size: 1.5rem; font-weight: 900; color: var(--ac2) }
.pstat-l { font-size: .72rem; color: var(--tx2); margin-top: .15rem; font-weight: 600 }

/* ══ RIGHT PANEL (form) ══ */
.panel-right {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative; z-index: 1;
  padding: 2rem 1.5rem;
  min-height: 100vh;
}

.form-box {
  width: 100%;
  max-width: 420px;
  animation: boxIn .55s cubic-bezier(.22,1,.36,1) both;
}
@keyframes boxIn {
  from { opacity:0; transform: translateY(24px) }
  to   { opacity:1; transform: translateY(0) }
}

/* mobile logo */
.mob-logo {
  display: flex; align-items: center; gap: .75rem;
  margin-bottom: 2.2rem; justify-content: center;
}
@media(min-width:960px) { .mob-logo { display: none } }
.mob-logo-ic {
  width: 46px; height: 46px; border-radius: 13px;
  background: linear-gradient(135deg, var(--ac), var(--ac2));
  display: flex; align-items: center; justify-content: center;
  font-size: 1.4rem;
  box-shadow: 0 8px 24px rgba(29,78,216,.35);
}
.mob-logo-name { font-size: 1.3rem; font-weight: 800; color: var(--tx) }

/* heading */
.form-title {
  font-size: 1.65rem; font-weight: 800; color: var(--tx);
  margin-bottom: .35rem; line-height: 1.2;
}
.form-sub { color: var(--tx2); font-size: .88rem; margin-bottom: 2rem; line-height: 1.6 }

/* back link */
.back-link {
  display: inline-flex; align-items: center; gap: .35rem;
  color: var(--tx2); font-size: .8rem; text-decoration: none;
  margin-bottom: 2rem; transition: color .2s;
}
.back-link:hover { color: var(--ac2) }
.back-link svg { width: 14px; height: 14px }

/* alert */
.alert {
  display: flex; align-items: center; gap: .65rem;
  padding: .9rem 1.1rem; border-radius: 12px;
  margin-bottom: 1.5rem; font-size: .86rem; font-weight: 600;
  border: 1px solid var(--redbrd);
  background: var(--redl); color: var(--red);
  animation: alertIn .3s ease;
}
@keyframes alertIn { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:none} }
.alert svg { width: 16px; height: 16px; flex-shrink: 0 }

/* field group */
.fg { margin-bottom: 1.25rem }
.fg-label {
  display: block; font-size: .82rem; font-weight: 700;
  color: var(--tx2); margin-bottom: .55rem; letter-spacing: .01em;
}
.field { position: relative }
.fic {
  position: absolute; right: 1rem; top: 50%; transform: translateY(-50%);
  width: 16px; height: 16px; color: var(--tx3); pointer-events: none; transition: color .2s;
}
.feye {
  position: absolute; left: 1rem; top: 50%; transform: translateY(-50%);
  background: none; border: none; cursor: pointer; color: var(--tx3); padding: 0;
  display: flex; transition: color .2s;
}
.feye:hover { color: var(--ac2) }
.feye svg { width: 16px; height: 16px }

.finput {
  width: 100%;
  padding: .88rem 2.75rem .88rem 1rem;
  background: var(--surf2);
  border: 1px solid var(--brd);
  border-radius: 12px;
  color: var(--tx);
  font-size: .91rem; font-family: 'Vazirmatn', Tahoma, sans-serif;
  outline: none; transition: all .22s;
}
.finput:focus {
  border-color: var(--ac2);
  background: rgba(255,255,255,.07);
  box-shadow: 0 0 0 3px rgba(59,130,246,.12), inset 0 1px 0 rgba(255,255,255,.05);
}
.finput:focus ~ .fic { color: var(--ac2) }
.finput::placeholder { color: var(--tx3) }

/* has eye icon */
.has-eye .finput { padding-left: 2.75rem }

/* forgot */
.forgot-row {
  display: flex; justify-content: flex-start; margin-top: .6rem;
}
.forgot {
  font-size: .8rem; color: var(--ac3); text-decoration: none;
  font-weight: 600; transition: opacity .2s;
}
.forgot:hover { opacity: .7 }

/* submit btn */
.submit-btn {
  width: 100%; padding: 1rem;
  background: linear-gradient(135deg, var(--ac) 0%, var(--ac2) 100%);
  border: none; border-radius: 13px;
  color: #fff; font-size: .97rem; font-weight: 800;
  font-family: 'Vazirmatn', Tahoma, sans-serif;
  cursor: pointer; margin-top: .5rem;
  display: flex; align-items: center; justify-content: center; gap: .6rem;
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
.divider {
  display: flex; align-items: center; gap: .85rem;
  color: var(--tx3); font-size: .8rem; margin: 1.75rem 0;
}
.divider::before,.divider::after { content:''; flex:1; height:1px; background:var(--brd) }

/* switch */
.switch-text { text-align: center; color: var(--tx2); font-size: .86rem }
.switch-text a { color: var(--ac2); font-weight: 700; text-decoration: none; transition: color .2s }
.switch-text a:hover { color: var(--ac3) }
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

  <div class="panel-content">
    <div class="panel-logo">📚</div>
    <div class="panel-title"><?=escape(SITE_NAME)?></div>
    <p class="panel-sub">بزرگ‌ترین فروشگاه آنلاین کتاب<br>هزاران عنوان در دسترس شما</p>
  </div>

  <div class="panel-stats">
    <div class="pstat">
      <div class="pstat-n">+۱۰K</div>
      <div class="pstat-l">عنوان کتاب</div>
    </div>
    <div class="pstat">
      <div class="pstat-n">+۵۰K</div>
      <div class="pstat-l">کاربر فعال</div>
    </div>
    <div class="pstat">
      <div class="pstat-n">۲۴/۷</div>
      <div class="pstat-l">پشتیبانی</div>
    </div>
  </div>
</div>

<!-- RIGHT PANEL (form) -->
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

    <h1 class="form-title">خوش برگشتید 👋</h1>
    <p class="form-sub">برای ادامه خرید وارد حساب خود شوید</p>

    <?php if($error): ?>
    <div class="alert">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M13,13H11V7H13M13,17H11V15H13M12,2A10,10 0,0,0 2,12A10,10 0,0,0 12,22A10,10 0,0,0 22,12A10,10 0,0,0 12,2Z"/></svg>
      <?=escape($error)?>
    </div>
    <?php endif; ?>

    <form method="POST" novalidate>

      <div class="fg">
        <label class="fg-label">آدرس ایمیل</label>
        <div class="field">
          <input class="finput" type="email" name="email"
            placeholder="example@email.com"
            value="<?=escape($_POST['email']??'')?>" autocomplete="email" required>
          <svg class="fic" viewBox="0 0 24 24" fill="currentColor"><path d="M20,8L12,13L4,8V6L12,11L20,6M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0,0,0 4,20H20A2,2 0,0,0 22,18V6C22,4.89 21.11,4 20,4Z"/></svg>
        </div>
      </div>

      <div class="fg">
        <label class="fg-label">رمز عبور</label>
        <div class="field has-eye">
          <input class="finput" type="password" name="password" id="pass1"
            placeholder="رمز عبور خود را وارد کنید"
            autocomplete="current-password" required>
          <svg class="fic" viewBox="0 0 24 24" fill="currentColor"><path d="M12,17A2,2 0,0,0 14,15C14,13.89 13.1,13 12,13A2,2 0,0,0 10,15A2,2 0,0,0 12,17M18,8A2,2 0,0,1 20,10V20A2,2 0,0,1 18,22H6A2,2 0,0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0,0,1 12,1A5,5 0,0,1 17,6V8H18Z"/></svg>
          <button type="button" class="feye" onclick="eyeToggle('pass1',this)">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,9A3,3 0,0,0 9,12A3,3 0,0,0 12,15A3,3 0,0,0 15,12A3,3 0,0,0 12,9M12,17A5,5 0,0,1 7,12A5,5 0,0,1 12,7A5,5 0,0,1 17,12A5,5 0,0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z"/></svg>
          </button>
        </div>
        <div class="forgot-row">
          <a href="forgot_password.php" class="forgot">فراموشی رمز عبور؟</a>
        </div>
      </div>

      <button type="submit" class="submit-btn">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M10,17V14H3V10H10V7L15,12L10,17M10,2H19A2,2 0,0,1 21,4V20A2,2 0,0,1 19,22H10A2,2 0,0,1 8,20V18H10V20H19V4H10V6H8V4A2,2 0,0,1 10,2Z"/></svg>
        ورود به حساب
      </button>

    </form>

    <div class="divider">یا</div>
    <div class="switch-text">حساب ندارید؟ <a href="register.php">ثبت‌نام کنید</a></div>

  </div>
</div>

<script>
/* ── Particle canvas ── */
const cvs = document.getElementById('cvs');
const ctx = cvs.getContext('2d');
let W, H, pts = [];

function resize() {
  W = cvs.width  = window.innerWidth;
  H = cvs.height = window.innerHeight;
}
resize();
window.addEventListener('resize', () => { resize(); initPts() });

function initPts() {
  pts = [];
  const n = Math.floor((W * H) / 18000);
  for (let i = 0; i < n; i++) {
    pts.push({
      x: Math.random() * W, y: Math.random() * H,
      vx: (Math.random() - .5) * .35, vy: (Math.random() - .5) * .35,
      r: Math.random() * 1.5 + .4,
      a: Math.random() * .6 + .15,
    });
  }
}
initPts();

function draw() {
  ctx.clearRect(0, 0, W, H);
  for (let i = 0; i < pts.length; i++) {
    const p = pts[i];
    p.x += p.vx; p.y += p.vy;
    if (p.x < 0 || p.x > W) p.vx *= -1;
    if (p.y < 0 || p.y > H) p.vy *= -1;
    ctx.beginPath();
    ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
    ctx.fillStyle = `rgba(96,165,250,${p.a})`;
    ctx.fill();
    for (let j = i + 1; j < pts.length; j++) {
      const q = pts[j];
      const dx = p.x - q.x, dy = p.y - q.y;
      const d = Math.sqrt(dx*dx + dy*dy);
      if (d < 110) {
        ctx.beginPath();
        ctx.moveTo(p.x, p.y); ctx.lineTo(q.x, q.y);
        ctx.strokeStyle = `rgba(59,130,246,${.18 * (1 - d/110)})`;
        ctx.lineWidth = .6;
        ctx.stroke();
      }
    }
  }
  requestAnimationFrame(draw);
}
draw();

/* ── Eye toggle ── */
const EO='<path d="M12,9A3,3 0,0,0 9,12A3,3 0,0,0 12,15A3,3 0,0,0 15,12A3,3 0,0,0 12,9M12,17A5,5 0,0,1 7,12A5,5 0,0,1 12,7A5,5 0,0,1 17,12A5,5 0,0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z"/>';
const EC='<path d="M11.83,9L15,12.16C15,12.11 15,12.05 15,12A3,3 0,0,0 12,9C11.94,9 11.89,9 11.83,9M7.53,9.8L9.08,11.35C9.03,11.56 9,11.77 9,12A3,3 0,0,0 12,15C12.22,15 12.44,14.97 12.65,14.92L14.2,16.47C13.53,16.8 12.79,17 12,17A5,5 0,0,1 7,12C7,11.21 7.2,10.47 7.53,9.8M2,4.27L4.28,6.55L4.73,7C3.08,8.3 1.78,10 1,12C2.73,16.39 7,19.5 12,19.5C13.55,19.5 15.03,19.2 16.38,18.66L16.81,19.08L19.73,22L21,20.73L3.27,3M12,7C14.76,7 17,9.24 17,12C17,12.64 16.87,13.26 16.64,13.82L19.57,16.75C21.07,15.5 22.27,13.86 23,12C21.27,7.61 17,4.5 12,4.5C10.6,4.5 9.26,4.75 8,5.2L10.17,7.35C10.74,7.13 11.35,7 12,7Z"/>';
function eyeToggle(id, btn) {
  const el = document.getElementById(id);
  const h = el.type === 'password';
  el.type = h ? 'text' : 'password';
  btn.querySelector('svg').innerHTML = h ? EC : EO;
}
</script>
</body>
</html>