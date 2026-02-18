<?php
/**
 * صفحه ثبت‌نام - کتاب نت
 */

if (session_status() === PHP_SESSION_NONE) session_start();

require_once("./include/config.php");
require_once("./include/db.php");

if (isset($_SESSION['member_id'])) {
    header("Location: index.php");
    exit();
}

$error   = '';
$success = '';

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
                    // لاگین خودکار
                    $newUser = fetchOne($db, "SELECT * FROM members WHERE email = ?", [$email]);
                    $_SESSION['member_id']   = $newUser['id'];
                    $_SESSION['member_name'] = $newUser['name'];
                    
                    header("Location: index.php");
                    exit();
                } else {
                    $error = 'خطایی رخ داد. لطفاً دوباره تلاش کنید.';
                }
            }
        } catch (PDOException $e) {
            $error = 'خطا در اتصال به دیتابیس.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ثبت‌نام | کتاب نت</title>
    <link rel="stylesheet" href="./css/style.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }

        body {
            font-family: Tanha, 'Vazirmatn', Tahoma, sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #3b82f6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            direction: rtl;
        }

        .auth-bg { position:fixed; inset:0; overflow:hidden; z-index:0; }
        .auth-bg span { position:absolute; border-radius:50%; background:rgba(255,255,255,0.05); animation:floatBg 15s infinite ease-in-out; }
        .auth-bg span:nth-child(1) { width:300px;height:300px;top:-50px;right:-50px;animation-delay:0s; }
        .auth-bg span:nth-child(2) { width:200px;height:200px;bottom:100px;left:-80px;animation-delay:5s; }
        .auth-bg span:nth-child(3) { width:150px;height:150px;bottom:-30px;right:30%;animation-delay:10s; }

        @keyframes floatBg {
            0%,100% { transform:translate(0,0) rotate(0deg); }
            50%     { transform:translate(30px,-30px) rotate(180deg); }
        }

        .auth-card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 520px;
            background: rgba(255,255,255,0.98);
            border-radius: 30px;
            padding: 3rem;
            box-shadow: 0 40px 100px rgba(0,0,0,0.3);
            animation: cardIn 0.6s cubic-bezier(0.4,0,0.2,1);
        }
        body.dark-mode .auth-card { background:#1e293b; color:#f1f5f9; }

        @keyframes cardIn {
            from { opacity:0; transform:translateY(40px) scale(0.96); }
            to   { opacity:1; transform:translateY(0) scale(1); }
        }

        .auth-logo { text-align:center; margin-bottom:2rem; }
        .auth-logo-icon { font-size:3.5rem; display:block; margin-bottom:0.5rem; animation:logoFloat 3s ease-in-out infinite; }
        @keyframes logoFloat { 0%,100%{transform:translateY(0);} 50%{transform:translateY(-8px);} }
        .auth-logo h1 {
            font-size:1.8rem; font-weight:900;
            background:linear-gradient(135deg,#1e3a8a,#3b82f6);
            -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;
        }
        .auth-logo p { color:#64748b; font-size:0.95rem; margin-top:0.3rem; }

        /* grid دو ستونه */
        .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:1.2rem; }
        .form-full  { grid-column:1/-1; }

        .form-group { margin-bottom: 0; }
        .form-group label { display:block; font-weight:700; color:#1e293b; margin-bottom:0.5rem; font-size:0.9rem; }
        body.dark-mode .form-group label { color:#cbd5e1; }

        .input-wrapper { position:relative; }
        .input-wrapper svg {
            position:absolute; right:1rem; top:50%; transform:translateY(-50%);
            width:18px; height:18px; color:#94a3b8; pointer-events:none;
        }
        .form-group input {
            width:100%; padding:0.9rem 2.8rem 0.9rem 1rem;
            border:2px solid #e2e8f0; border-radius:12px;
            font-size:0.95rem; font-family:inherit; color:#1e293b;
            background:#f8fafc; transition:all 0.3s;
        }
        body.dark-mode .form-group input { background:#0f172a; border-color:#334155; color:#f1f5f9; }
        .form-group input:focus {
            outline:none; border-color:#3b82f6;
            box-shadow:0 0 0 3px rgba(59,130,246,0.1); background:white;
        }
        body.dark-mode .form-group input:focus { background:#1e293b; }

        /* قدرت رمز */
        .password-strength { margin-top:0.5rem; }
        .strength-bar { height:4px; border-radius:4px; background:#e2e8f0; overflow:hidden; }
        .strength-fill { height:100%; border-radius:4px; transition:all 0.4s; width:0; }
        .strength-text { font-size:0.8rem; color:#94a3b8; margin-top:0.3rem; }

        .toggle-password {
            position:absolute; left:1rem; top:50%; transform:translateY(-50%);
            background:none; border:none; cursor:pointer; color:#94a3b8; padding:0;
        }
        .toggle-password svg { width:18px; height:18px; display:block; }

        .btn-submit {
            width:100%; padding:1.1rem;
            background:linear-gradient(135deg,#1e3a8a,#3b82f6);
            color:white; border:none; border-radius:14px;
            font-size:1.1rem; font-weight:700; font-family:inherit;
            cursor:pointer; transition:all 0.3s;
            display:flex; align-items:center; justify-content:center; gap:0.75rem;
            margin-top:1.5rem;
        }
        .btn-submit:hover { transform:translateY(-3px); box-shadow:0 15px 40px rgba(30,58,138,0.4); }
        .btn-submit svg { width:22px; height:22px; }

        .alert { padding:1rem 1.2rem; border-radius:12px; margin-bottom:1.5rem; font-weight:600; font-size:0.95rem; display:flex; align-items:center; gap:0.75rem; }
        .alert svg { width:20px; height:20px; flex-shrink:0; }
        .alert-error { background:rgba(239,68,68,0.1); color:#ef4444; border:1px solid rgba(239,68,68,0.2); }

        .auth-divider { display:flex; align-items:center; gap:1rem; margin:1.5rem 0; color:#94a3b8; font-size:0.9rem; }
        .auth-divider::before, .auth-divider::after { content:''; flex:1; height:1px; background:#e2e8f0; }
        body.dark-mode .auth-divider::before, body.dark-mode .auth-divider::after { background:#334155; }

        .auth-switch { text-align:center; color:#64748b; font-size:0.95rem; }
        .auth-switch a { color:#3b82f6; font-weight:700; text-decoration:none; }
        .auth-switch a:hover { text-decoration:underline; }

        .auth-back {
            display:inline-flex; align-items:center; gap:0.5rem;
            color:rgba(255,255,255,0.7); text-decoration:none;
            font-size:0.9rem; font-weight:600;
            position:fixed; top:2rem; right:2rem; z-index:100; transition:color 0.3s;
        }
        .auth-back:hover { color:white; }
        .auth-back svg { width:20px; height:20px; }

        /* چک‌باکس */
        .form-check {
            display:flex; align-items:center; gap:0.75rem; cursor:pointer;
        }
        .form-check input[type="checkbox"] {
            width:20px; height:20px; accent-color:#3b82f6;
            border-radius:5px; cursor:pointer; padding:0;
        }
        .form-check span { font-size:0.9rem; color:#475569; }
        body.dark-mode .form-check span { color:#94a3b8; }
        .form-check a { color:#3b82f6; text-decoration:none; font-weight:600; }

        @media (max-width:480px) {
            .form-grid { grid-template-columns:1fr; }
            .auth-card { padding:2rem; }
        }
    </style>
</head>
<body>

    <div class="auth-bg">
        <span></span><span></span><span></span>
    </div>

    <a href="index.php" class="auth-back">
        <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M10,20V14H14V20H19V12H22L12,3L2,12H5V20H10Z"/>
        </svg>
        بازگشت به خانه
    </a>

    <div class="auth-card">

        <div class="auth-logo">
            <span class="auth-logo-icon">📚</span>
            <h1>کتاب نت</h1>
            <p>حساب کاربری رایگان بسازید</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12,2C17.53,2 22,6.47 22,12C22,17.53 17.53,22 12,22C6.47,22 2,17.53 2,12C2,6.47 6.47,2 12,2M15.59,7L12,10.59L8.41,7L7,8.41L10.59,12L7,15.59L8.41,17L12,13.41L15.59,17L17,15.59L13.41,12L17,8.41L15.59,7Z"/>
                </svg>
                <?= escape($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" novalidate>

            <div class="form-grid">

                <div class="form-group">
                    <label>نام کامل *</label>
                    <div class="input-wrapper">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/></svg>
                        <input type="text" name="name" placeholder="نام و نام‌خانوادگی"
                               value="<?= escape($_POST['name'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>شماره موبایل</label>
                    <div class="input-wrapper">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M6.62,10.79C8.06,13.62 10.38,15.94 13.21,17.38L15.41,15.18C15.69,14.9 16.08,14.82 16.43,14.93C17.55,15.3 18.75,15.5 20,15.5A1,1 0 0,1 21,16.5V20A1,1 0 0,1 20,21A17,17 0 0,1 3,4A1,1 0 0,1 4,3H7.5A1,1 0 0,1 8.5,4C8.5,5.25 8.7,6.45 9.07,7.57C9.18,7.92 9.1,8.31 8.82,8.59L6.62,10.79Z"/></svg>
                        <input type="tel" name="phone" placeholder="09XX-XXX-XXXX"
                               value="<?= escape($_POST['phone'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-group form-full">
                    <label>ایمیل *</label>
                    <div class="input-wrapper">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20,8L12,13L4,8V6L12,11L20,6M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.11,4 20,4Z"/></svg>
                        <input type="email" name="email" placeholder="example@email.com"
                               value="<?= escape($_POST['email'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>رمز عبور *</label>
                    <div class="input-wrapper">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,17A2,2 0 0,0 14,15C14,13.89 13.1,13 12,13A2,2 0 0,0 10,15A2,2 0 0,0 12,17M18,8A2,2 0 0,1 20,10V20A2,2 0 0,1 18,22H6A2,2 0 0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0 0,1 12,1A5,5 0 0,1 17,6V8H18M12,3A3,3 0 0,0 9,6V8H15V6A3,3 0 0,0 12,3Z"/></svg>
                        <input type="password" name="password" id="passReg"
                               placeholder="حداقل ۶ کاراکتر"
                               oninput="checkStrength(this.value)" required>
                        <button type="button" class="toggle-password" onclick="togglePass('passReg', this)">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9M12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z"/></svg>
                        </button>
                    </div>
                    <div class="password-strength">
                        <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
                        <div class="strength-text" id="strengthText"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>تکرار رمز عبور *</label>
                    <div class="input-wrapper">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,17A2,2 0 0,0 14,15C14,13.89 13.1,13 12,13A2,2 0 0,0 10,15A2,2 0 0,0 12,17M18,8A2,2 0 0,1 20,10V20A2,2 0 0,1 18,22H6A2,2 0 0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0 0,1 12,1A5,5 0 0,1 17,6V8H18M12,3A3,3 0 0,0 9,6V8H15V6A3,3 0 0,0 12,3Z"/></svg>
                        <input type="password" name="confirm" id="confirmReg"
                               placeholder="رمز عبور را تکرار کنید" required>
                        <button type="button" class="toggle-password" onclick="togglePass('confirmReg', this)">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9M12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z"/></svg>
                        </button>
                    </div>
                </div>

                <div class="form-group form-full">
                    <label class="form-check">
                        <input type="checkbox" name="agree" required>
                        <span>با <a href="#">قوانین و مقررات</a> کتاب نت موافقم</span>
                    </label>
                </div>

            </div>

            <button type="submit" class="btn-submit">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M15,14C12.33,14 7,15.33 7,18V20H23V18C23,15.33 17.67,14 15,14M6,10V7H4V10H1V12H4V15H6V12H9V10M15,12A4,4 0 0,0 19,8A4,4 0 0,0 15,4A4,4 0 0,0 11,8A4,4 0 0,0 15,12Z"/>
                </svg>
                ساخت حساب کاربری
            </button>
        </form>

        <div class="auth-divider">یا</div>

        <div class="auth-switch">
            قبلاً ثبت‌نام کرده‌اید؟
            <a href="login.php">وارد شوید</a>
        </div>

    </div>

    <script>
        function togglePass(id, btn) {
            const inp = document.getElementById(id);
            const isHidden = inp.type === 'password';
            inp.type = isHidden ? 'text' : 'password';
            btn.querySelector('svg').innerHTML = isHidden
                ? '<path d="M11.83,9L15,12.16C15,12.11 15,12.05 15,12A3,3 0 0,0 12,9C11.94,9 11.89,9 11.83,9M7.53,9.8L9.08,11.35C9.03,11.56 9,11.77 9,12A3,3 0 0,0 12,15C12.22,15 12.44,14.97 12.65,14.92L14.2,16.47C13.53,16.8 12.79,17 12,17A5,5 0 0,1 7,12C7,11.21 7.2,10.47 7.53,9.8M2,4.27L4.28,6.55L4.73,7C3.08,8.3 1.78,10 1,12C2.73,16.39 7,19.5 12,19.5C13.55,19.5 15.03,19.2 16.38,18.66L16.81,19.08L19.73,22L21,20.73L3.27,3M12,7C14.76,7 17,9.24 17,12C17,12.64 16.87,13.26 16.64,13.82L19.57,16.75C21.07,15.5 22.27,13.86 23,12C21.27,7.61 17,4.5 12,4.5C10.6,4.5 9.26,4.75 8,5.2L10.17,7.35C10.74,7.13 11.35,7 12,7Z"/>'
                : '<path d="M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9M12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z"/>';
        }

        function checkStrength(val) {
            const fill = document.getElementById('strengthFill');
            const text = document.getElementById('strengthText');
            if (!val) { fill.style.width='0'; text.textContent=''; return; }

            let score = 0;
            if (val.length >= 6)  score++;
            if (val.length >= 10) score++;
            if (/[A-Z]/.test(val) || /[a-z]/.test(val)) score++;
            if (/[0-9]/.test(val))    score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            const levels = [
                { w:'20%',  color:'#ef4444', label:'خیلی ضعیف' },
                { w:'40%',  color:'#f97316', label:'ضعیف' },
                { w:'60%',  color:'#eab308', label:'متوسط' },
                { w:'80%',  color:'#22c55e', label:'قوی' },
                { w:'100%', color:'#10b981', label:'خیلی قوی' },
            ];
            const l = levels[Math.min(score - 1, 4)] || levels[0];
            fill.style.width  = l.w;
            fill.style.background = l.color;
            text.textContent  = 'قدرت رمز: ' + l.label;
            text.style.color  = l.color;
        }

        if (localStorage.getItem('darkMode') === 'enabled') {
            document.body.classList.add('dark-mode');
        }
    </script>
</body>
</html>