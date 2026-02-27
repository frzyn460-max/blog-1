<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once(__DIR__ . "/config.php");
require_once(__DIR__ . "/db.php");

$cart_items       = $_SESSION['cart'] ?? [];
$total_cart_count = array_sum($cart_items);
$posts_slider     = fetchAll($db, "SELECT * FROM img");
$categories       = fetchAll($db, "SELECT * FROM categories ORDER BY title ASC");
$csrf_token       = generate_csrf_token();
$current_file     = basename($_SERVER['PHP_SELF'], '.php');
$logged_in        = isset($_SESSION['member_id']);
$user_name        = $_SESSION['member_name'] ?? '';
$user_avatar_letter = $user_name ? mb_substr($user_name, 0, 1) : '?';
$user_real_avatar = null;
if ($logged_in) {
    $member_row = fetchOne($db, "SELECT avatar FROM members WHERE id = ?", [$_SESSION['member_id']]);
    if ($member_row && !empty($member_row['avatar']) && file_exists($member_row['avatar'])) {
        $user_real_avatar = $member_row['avatar'];
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title><?=escape(SITE_NAME)?> | فروشگاه آنلاین کتاب</title>
<link rel="stylesheet" href="./css/style.css">

<style>
@import url('https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css');

:root {
  --c0:#020817; --c1:#0a1628; --c2:#0f2352;
  --ac:#2563eb; --ac2:#3b82f6; --ac3:#60a5fa;
  --acg:linear-gradient(135deg,#1d4ed8,#3b82f6);
  --wh:rgba(255,255,255,1); --wh8:rgba(255,255,255,.08);
  --wh12:rgba(255,255,255,.12); --wh18:rgba(255,255,255,.18);
  --wh25:rgba(255,255,255,.25); --wh60:rgba(255,255,255,.6);
  --wh80:rgba(255,255,255,.8);
}

/* ─ HERO WRAPPER ─ */
.kn-hero {
  position:relative; min-height:100vh; background:var(--c0);
  overflow:hidden; display:flex; flex-direction:column;
}
.kn-mesh {
  position:absolute; inset:0; z-index:0; pointer-events:none;
  background:
    radial-gradient(ellipse 90% 80% at 70% -10%, rgba(37,99,235,.28) 0%, transparent 55%),
    radial-gradient(ellipse 60% 70% at -5% 110%, rgba(29,78,216,.32) 0%, transparent 50%),
    radial-gradient(ellipse 50% 50% at 50% 60%,  rgba(96,165,250,.06) 0%, transparent 55%),
    linear-gradient(160deg,#020817 0%,#0b1d4a 50%,#020d24 100%);
  animation:meshShift 18s ease-in-out infinite alternate;
}
@keyframes meshShift{0%{filter:hue-rotate(0deg) brightness(1)}100%{filter:hue-rotate(8deg) brightness(1.05)}}
.kn-noise {
  position:absolute; inset:0; z-index:1; pointer-events:none; opacity:.025;
  background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
  background-size:180px 180px;
}
.kn-grid {
  position:absolute; inset:0; z-index:1; pointer-events:none;
  background-image:
    linear-gradient(rgba(59,130,246,.04) 1px,transparent 1px),
    linear-gradient(90deg,rgba(59,130,246,.04) 1px,transparent 1px);
  background-size:72px 72px;
}
.kn-orbs{position:absolute;inset:0;z-index:1;pointer-events:none;overflow:hidden}
.kn-orb{
  position:absolute;border-radius:50%;filter:blur(60px);opacity:0;
  animation:orbFloat ease-in-out infinite;
}
.kn-orb:nth-child(1){width:500px;height:500px;top:-120px;right:-80px;background:rgba(37,99,235,.18);animation-duration:25s}
.kn-orb:nth-child(2){width:320px;height:320px;bottom:-60px;left:-80px;background:rgba(29,78,216,.22);animation-duration:18s;animation-delay:-6s}
.kn-orb:nth-child(3){width:200px;height:200px;top:40%;right:20%;background:rgba(96,165,250,.12);animation-duration:14s;animation-delay:-11s}
@keyframes orbFloat{0%{opacity:.7;transform:translate(0,0) scale(1)}33%{opacity:1;transform:translate(30px,-40px) scale(1.05)}66%{opacity:.8;transform:translate(-20px,25px) scale(.95)}100%{opacity:.7;transform:translate(0,0) scale(1)}}

/* ─ NAVBAR ─ */
.kn-nav{
  position:fixed;top:0;left:0;right:0;z-index:900;
  transition:all .4s cubic-bezier(.4,0,.2,1);
}
.kn-nav-in{
  max-width:1440px;margin:0 auto;
  padding:1.3rem 2rem;
  display:flex;align-items:center;gap:1rem;
  transition:padding .4s;
}
.kn-nav.kn-scrolled{
  background:rgba(2,8,23,.85);
  backdrop-filter:blur(24px) saturate(180%);
  border-bottom:1px solid rgba(59,130,246,.12);
  box-shadow:0 8px 40px rgba(0,0,0,.35);
  border-radius: 0 0 12px 12px;
}
.kn-nav.kn-scrolled .kn-nav-in{padding:.8rem 2rem}

/* logo */
.kn-logo{display:flex;align-items:center;gap:.7rem;text-decoration:none;flex-shrink:0}
.kn-logo-mk{
  width:44px;height:44px;border-radius:13px;
  background:var(--acg);
  display:flex;align-items:center;justify-content:center;
  font-size:1.4rem;flex-shrink:0;
  box-shadow:0 6px 20px rgba(37,99,235,.4),inset 0 1px 0 rgba(255,255,255,.2);
  transition:transform .3s,box-shadow .3s;
}
.kn-logo:hover .kn-logo-mk{transform:scale(1.08) rotate(-4deg);box-shadow:0 10px 28px rgba(37,99,235,.55)}
.kn-logo-txt{font-size:1.25rem;font-weight:900;color:var(--wh);letter-spacing:-.02em}
.kn-logo-txt span{color:var(--ac3)}

/* nav links */
.kn-links{display:flex;align-items:center;gap:.25rem;list-style:none;flex:1}
.kn-link{
  color:var(--wh80);text-decoration:none;
  padding:.55rem 1rem;border-radius:10px;
  font-weight:600;font-size:.88rem;
  display:flex;align-items:center;gap:.4rem;
  transition:all .25s;position:relative;white-space:nowrap;
}
.kn-link:hover{color:var(--wh);background:var(--wh8)}
.kn-link.active{color:var(--wh);background:var(--wh12)}
.kn-link.active::after{
  content:'';position:absolute;bottom:4px;left:50%;transform:translateX(-50%);
  width:18px;height:2px;border-radius:2px;background:var(--ac2);
}

/* search pill */
.kn-spill{
  display:flex;align-items:center;gap:.65rem;
  background:var(--wh8);border:1px solid var(--wh12);
  border-radius:12px;padding:.6rem 1.1rem;
  cursor:pointer;transition:all .25s;min-width:200px;
  font-family:inherit;color:var(--wh60);font-size:.85rem;
}
.kn-spill:hover{background:var(--wh12);border-color:var(--wh25);color:var(--wh)}
.kn-spill svg{width:15px;height:15px;flex-shrink:0}
.kn-spill-txt{flex:1;text-align:right}
.kn-spill-kbd{
  font-size:.65rem;padding:.12rem .45rem;border-radius:5px;
  background:var(--wh8);border:1px solid var(--wh18);color:var(--wh60);
  font-family:monospace;flex-shrink:0;
}

/* actions */
.kn-actions{display:flex;align-items:center;gap:.55rem;flex-shrink:0}
.kn-ibtn{
  width:42px;height:42px;border-radius:12px;
  background:var(--wh8);border:1px solid var(--wh12);
  color:var(--wh80);cursor:pointer;
  display:flex;align-items:center;justify-content:center;
  transition:all .25s;text-decoration:none;position:relative;
  font-size:1rem;flex-shrink:0;
}
.kn-ibtn:hover{background:var(--wh18);color:var(--wh);border-color:var(--wh25);transform:translateY(-1px)}
.kn-badge{
  position:absolute;top:-5px;left:-5px;
  background:linear-gradient(135deg,#f43f5e,#ec4899);
  color:#fff;min-width:20px;height:20px;border-radius:10px;padding:0 4px;
  font-size:.62rem;display:flex;align-items:center;justify-content:center;
  font-weight:800;border:2px solid var(--c0);
  animation:badgePop 2.5s ease-in-out infinite;
}
@keyframes badgePop{0%,100%{transform:scale(1)}50%{transform:scale(1.15)}}

/* user */
.kn-uwrap{position:relative}
.kn-ubtn{
  display:flex;align-items:center;gap:.5rem;
  background:var(--wh8);border:1px solid var(--wh18);
  border-radius:50px;padding:.38rem .75rem .38rem .38rem;
  cursor:pointer;transition:all .25s;font-family:inherit;color:var(--wh);
}
.kn-ubtn:hover{background:var(--wh18);border-color:var(--wh25)}
.kn-uav{
  width:34px;height:34px;border-radius:50%;
  background:linear-gradient(135deg,#f59e0b,#ef4444);
  display:flex;align-items:center;justify-content:center;
  font-weight:800;font-size:.9rem;color:#fff;flex-shrink:0;
  overflow:hidden;position:relative;
  box-shadow:0 0 0 2px rgba(255,255,255,.25);
}
.kn-uav img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;border-radius:50%}
.kn-uname{font-size:.84rem;font-weight:700}
.kn-uchev{width:14px;height:14px;color:var(--wh60);transition:transform .3s}
.kn-ubtn.open .kn-uchev{transform:rotate(180deg)}
.kn-udrop{
  position:absolute;top:calc(100% + 10px);left:0;
  min-width:210px;background:#0c1832;
  border:1px solid rgba(59,130,246,.18);border-radius:16px;overflow:hidden;
  box-shadow:0 20px 60px rgba(0,0,0,.55),0 0 0 1px rgba(255,255,255,.04);
  opacity:0;visibility:hidden;transform:translateY(-8px) scale(.97);
  transition:all .28s cubic-bezier(.4,0,.2,1);z-index:999;
}
.kn-udrop.open{opacity:1;visibility:visible;transform:translateY(0) scale(1)}
.kn-uitem{
  display:flex;align-items:center;gap:.7rem;
  padding:.85rem 1.2rem;color:rgba(255,255,255,.8);
  text-decoration:none;font-size:.88rem;font-weight:500;transition:all .2s;
}
.kn-uitem:hover{background:rgba(59,130,246,.12);color:#fff}
.kn-uitem svg{width:17px;height:17px;color:var(--ac2);flex-shrink:0}
.kn-uitem.danger{color:rgba(248,113,113,.85)}
.kn-uitem.danger svg{color:#f87171}
.kn-uitem.danger:hover{background:rgba(248,113,113,.1);color:#f87171}
.kn-udiv{height:1px;background:rgba(255,255,255,.07);margin:.3rem 0}

/* auth btns */
.kn-auth{display:flex;align-items:center;gap:.55rem}
.kn-btn-in{
  padding:.55rem 1.1rem;border-radius:10px;
  border:1px solid var(--wh25);background:transparent;
  color:var(--wh80);font-size:.86rem;font-weight:700;
  text-decoration:none;transition:all .25s;white-space:nowrap;
  display:flex;align-items:center;gap:.4rem;
}
.kn-btn-in:hover{background:var(--wh12);color:var(--wh);border-color:var(--wh60)}
.kn-btn-up{
  padding:.55rem 1.15rem;border-radius:10px;
  background:var(--acg);border:none;
  color:#fff;font-size:.86rem;font-weight:800;
  text-decoration:none;transition:all .25s;white-space:nowrap;
  display:flex;align-items:center;gap:.4rem;
  box-shadow:0 4px 16px rgba(37,99,235,.35);
}
.kn-btn-up:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(37,99,235,.5)}
.kn-btn-in svg,.kn-btn-up svg{width:16px;height:16px}

/* hamburger */
.kn-ham{
  display:none;order:-1;
  width:42px;height:42px;border-radius:11px;
  background:var(--wh8);border:1px solid var(--wh12);
  flex-direction:column;align-items:center;justify-content:center;gap:5px;
  cursor:pointer;flex-shrink:0;transition:all .25s;
}
.kn-ham:hover{background:var(--wh18)}
.kn-hl{width:19px;height:2px;background:var(--wh80);border-radius:2px;transition:all .35s cubic-bezier(.4,0,.2,1)}
.kn-ham.open .kn-hl:nth-child(1){transform:translateY(7px) rotate(45deg)}
.kn-ham.open .kn-hl:nth-child(2){opacity:0;transform:scaleX(0)}
.kn-ham.open .kn-hl:nth-child(3){transform:translateY(-7px) rotate(-45deg)}

/* ─ DRAWER ─ */
.kn-overlay{
  position:fixed;inset:0;background:rgba(0,0,0,.65);
  backdrop-filter:blur(5px);z-index:800;
  opacity:0;pointer-events:none;transition:opacity .35s;
}
.kn-overlay.open{opacity:1;pointer-events:all}
.kn-drawer{
  position:fixed;top:0;right:0;
  width:300px;height:100dvh;
  background:linear-gradient(170deg,#080f24 0%,#0d1e47 60%,#0a1628 100%);
  border-left:1px solid rgba(59,130,246,.12);
  z-index:850;transform:translateX(100%);
  transition:transform .38s cubic-bezier(.4,0,.2,1);
  display:flex;flex-direction:column;overflow-y:auto;
}
.kn-drawer.open{transform:translateX(0)}
.kn-dhead{
  display:flex;align-items:center;justify-content:space-between;
  padding:1.5rem;border-bottom:1px solid rgba(255,255,255,.07);
}
.kn-dlogo{display:flex;align-items:center;gap:.6rem;text-decoration:none}
.kn-dlogo-mk{
  width:38px;height:38px;border-radius:11px;
  background:var(--acg);display:flex;align-items:center;justify-content:center;
  font-size:1.2rem;box-shadow:0 4px 14px rgba(37,99,235,.35);
}
.kn-dlogo-txt{font-size:1.1rem;font-weight:900;color:var(--wh)}
.kn-dclose{
  width:36px;height:36px;border-radius:10px;
  background:var(--wh8);border:none;color:var(--wh60);
  display:flex;align-items:center;justify-content:center;
  cursor:pointer;font-size:1.2rem;transition:all .25s;
}
.kn-dclose:hover{background:var(--wh18);color:var(--wh)}
.kn-dsearch{padding:1rem 1.25rem;border-bottom:1px solid rgba(255,255,255,.06)}
.kn-dsi{
  width:100%;background:var(--wh8);border:1px solid var(--wh12);
  border-radius:12px;padding:.75rem 1.1rem;
  color:var(--wh);font-family:inherit;font-size:.9rem;outline:none;
  transition:border-color .25s;
}
.kn-dsi:focus{border-color:var(--ac2)}
.kn-dsi::placeholder{color:rgba(255,255,255,.3)}
.kn-dnav{padding:1rem;flex:1;display:flex;flex-direction:column;gap:.3rem}
.kn-dsec{
  font-size:.65rem;letter-spacing:.2em;text-transform:uppercase;
  color:rgba(255,255,255,.25);font-weight:700;
  padding:.65rem .85rem .3rem;
}
.kn-dlink{
  display:flex;align-items:center;gap:.8rem;
  padding:.85rem 1rem;border-radius:13px;
  color:rgba(255,255,255,.7);text-decoration:none;
  font-weight:600;font-size:.92rem;transition:all .25s;
}
.kn-dlink:hover{background:var(--wh8);color:var(--wh);padding-right:1.2rem}
.kn-dlink.active{background:rgba(37,99,235,.18);color:var(--wh);border-right:2px solid var(--ac2)}
.kn-dlink-ic{
  width:34px;height:34px;border-radius:10px;
  background:var(--wh8);display:flex;align-items:center;justify-content:center;
  font-size:1rem;flex-shrink:0;transition:background .25s;
}
.kn-dlink.active .kn-dlink-ic,.kn-dlink:hover .kn-dlink-ic{background:rgba(37,99,235,.3)}
.kn-dlink-arr{margin-right:auto;opacity:.35;font-size:.8rem}
.kn-dauth-w{padding:1.1rem;border-top:1px solid rgba(255,255,255,.07);display:flex;flex-direction:column;gap:.65rem}
.kn-da{
  display:flex;align-items:center;justify-content:center;gap:.5rem;
  padding:.9rem;border-radius:13px;
  font-weight:700;font-size:.9rem;text-decoration:none;transition:all .3s;
  font-family:inherit;border:none;cursor:pointer;
}
.kn-da.login{background:var(--wh8);border:1px solid var(--wh18);color:var(--wh)}
.kn-da.login:hover{background:var(--wh18)}
.kn-da.reg{background:var(--acg);color:#fff;box-shadow:0 6px 20px rgba(37,99,235,.35)}
.kn-da.reg:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(37,99,235,.5)}
.kn-da svg{width:17px;height:17px}

/* ─ SEARCH MODAL ─ */
.kn-sm{
  position:fixed;inset:0;z-index:1000;
  display:flex;align-items:flex-start;justify-content:center;
  padding-top:8vh;opacity:0;pointer-events:none;transition:opacity .3s;
}
.kn-sm.open{opacity:1;pointer-events:all}
.kn-sm-bg{position:absolute;inset:0;background:rgba(2,8,23,.9);backdrop-filter:blur(20px)}
.kn-sm-box{
  position:relative;z-index:2;
  width:100%;max-width:680px;margin:0 1.5rem;
  background:#0c1832;border:1px solid rgba(59,130,246,.2);
  border-radius:22px;overflow:hidden;
  box-shadow:0 40px 100px rgba(0,0,0,.6),0 0 0 1px rgba(255,255,255,.04);
  transform:translateY(-20px) scale(.97);transition:all .35s cubic-bezier(.4,0,.2,1);
}
.kn-sm.open .kn-sm-box{transform:translateY(0) scale(1)}
.kn-sr{display:flex;align-items:center;gap:.8rem;padding:1.1rem 1.4rem}
.kn-sr-ic{
  width:38px;height:38px;border-radius:11px;background:var(--acg);
  display:flex;align-items:center;justify-content:center;flex-shrink:0;
  box-shadow:0 4px 14px rgba(37,99,235,.35);
}
.kn-sr-ic svg{width:17px;height:17px;color:#fff}
.kn-si{flex:1;background:none;border:none;outline:none;color:rgba(255,255,255,.9);font-family:inherit;font-size:1rem;direction:rtl}
.kn-si::placeholder{color:rgba(255,255,255,.3)}
.kn-spin{
  width:18px;height:18px;border-radius:50%;
  border:2px solid rgba(59,130,246,.2);border-top-color:var(--ac2);
  animation:kSpin .65s linear infinite;display:none;flex-shrink:0;
}
@keyframes kSpin{to{transform:rotate(360deg)}}
.kn-sesc{
  display:flex;align-items:center;gap:.35rem;
  color:rgba(255,255,255,.3);font-size:.75rem;cursor:pointer;
  flex-shrink:0;transition:color .2s;user-select:none;
}
.kn-sesc:hover{color:rgba(255,255,255,.6)}
.kn-sesc kbd{background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);padding:.12rem .4rem;border-radius:5px;font-family:monospace;font-size:.68rem}
.kn-sdiv{height:1px;background:rgba(255,255,255,.07);margin:0 1.4rem}
.kn-sres{padding:.6rem .75rem;max-height:400px;overflow-y:auto}
.kn-sres::-webkit-scrollbar{width:4px}
.kn-sres::-webkit-scrollbar-thumb{background:rgba(59,130,246,.3);border-radius:4px}
.kn-shint{display:flex;flex-direction:column;align-items:center;padding:2.5rem 1rem;color:rgba(255,255,255,.3);text-align:center}
.kn-shint-ic{font-size:2.8rem;margin-bottom:.9rem;animation:hintBob 3s ease-in-out infinite}
@keyframes hintBob{0%,100%{transform:translateY(0)}50%{transform:translateY(-7px)}}
.kn-shint p{font-size:.87rem;line-height:1.75}
.kn-shint strong{color:rgba(255,255,255,.55)}
.kn-ssec{
  display:flex;align-items:center;gap:.5rem;
  padding:.65rem .5rem .3rem;
  font-size:.67rem;letter-spacing:.18em;text-transform:uppercase;
  color:rgba(255,255,255,.3);font-weight:700;
}
.kn-ssec::after{content:'';flex:1;height:1px;background:rgba(255,255,255,.07)}
.kn-sitem{
  display:flex;align-items:center;gap:.85rem;
  padding:.7rem .6rem;border-radius:12px;
  text-decoration:none;color:rgba(255,255,255,.85);transition:all .2s;
}
.kn-sitem:hover{background:rgba(37,99,235,.15);color:#fff;transform:translateX(-2px)}
.kn-simg-w{width:46px;height:58px;flex-shrink:0;border-radius:9px;overflow:hidden;border:1px solid rgba(255,255,255,.07)}
.kn-simg{width:100%;height:100%;object-fit:cover}
.kn-sinfo{flex:1;min-width:0}
.kn-sname{font-size:.9rem;font-weight:700;color:rgba(255,255,255,.9);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.kn-smeta{display:flex;align-items:center;gap:.4rem;margin-top:.3rem;flex-wrap:wrap}
.kn-sprice{font-size:.82rem;color:#34d399;font-weight:800}
.kn-sold{font-size:.73rem;color:rgba(255,255,255,.3);text-decoration:line-through}
.kn-sdisc{background:linear-gradient(135deg,#ef4444,#f97316);color:#fff;padding:.04rem .35rem;border-radius:4px;font-size:.67rem;font-weight:800}
.kn-sarr{color:rgba(255,255,255,.2);opacity:0;transition:all .2s;flex-shrink:0}
.kn-sitem:hover .kn-sarr{opacity:1;transform:translateX(-3px)}
.kn-sempty{text-align:center;padding:2rem 1rem;color:rgba(255,255,255,.3)}
.kn-sempty span{font-size:2rem;display:block;margin-bottom:.5rem}
.kn-sall-w{padding:.6rem .75rem .9rem}
.kn-sall{
  display:flex;align-items:center;justify-content:center;gap:.5rem;
  padding:.85rem;border-radius:12px;
  background:rgba(37,99,235,.12);border:1px solid rgba(37,99,235,.25);
  color:var(--ac2);font-weight:700;font-size:.86rem;
  text-decoration:none;transition:all .25s;
}
.kn-sall:hover{background:var(--ac);border-color:transparent;color:#fff;box-shadow:0 6px 18px rgba(37,99,235,.35)}
.kn-sall svg{width:14px;height:14px}
.kn-scats-w{padding:.5rem 1.4rem 1.1rem}
.kn-scats-lbl{font-size:.65rem;letter-spacing:.18em;text-transform:uppercase;color:rgba(255,255,255,.25);font-weight:700;display:block;margin-bottom:.55rem}
.kn-scats{display:flex;gap:.4rem;flex-wrap:wrap}
.kn-scat{
  padding:.35rem .9rem;border-radius:50px;
  background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);
  color:rgba(255,255,255,.5);font-size:.77rem;font-weight:600;
  text-decoration:none;transition:all .2s;
}
.kn-scat:hover{background:rgba(37,99,235,.2);border-color:rgba(37,99,235,.4);color:var(--ac2)}

/* ─ HERO BODY ─ */
.kn-body{
  position:relative;z-index:10;
  flex:1;display:flex;align-items:center;
  padding:9rem 2rem 6rem;
  max-width:1440px;margin:0 auto;width:100%;
}
.kn-grid-h{
  display:grid;grid-template-columns:1fr 430px;
  gap:5rem;align-items:center;width:100%;
}
.kn-eyebrow{
  display:inline-flex;align-items:center;gap:.55rem;
  font-size:.72rem;letter-spacing:.22em;text-transform:uppercase;
  color:var(--ac3);font-weight:700;margin-bottom:1.6rem;
}
.kn-eyebrow::before,.kn-eyebrow::after{content:'';width:24px;height:1.5px;background:var(--ac3);display:inline-block}
.kn-h1{
  font-size:clamp(3rem,5.5vw,3.75rem);
  font-weight:750;color:var(--wh);line-height:1.08;
  margin-bottom:1.6rem;letter-spacing:-.025em;
}
.kn-h1-em{
  display:block;
 
  background:linear-gradient(var(--ac3) 60%,#a78bfa 100%);
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
}
.kn-desc{font-size:1.08rem;color:rgba(255,255,255,.6);line-height:1.9;margin-bottom:2.5rem;max-width:500px}
.kn-desc strong{color:var(--ac3)}
.kn-cta{display:flex;gap:1rem;flex-wrap:wrap;margin-bottom:3.5rem}
.kn-cta-p{
  padding:1rem 2.2rem;border-radius:14px;
  background:var(--acg);color:#fff;font-weight:800;font-size:.95rem;
  text-decoration:none;display:inline-flex;align-items:center;gap:.55rem;
  box-shadow:0 8px 28px rgba(37,99,235,.4),inset 0 1px 0 rgba(255,255,255,.15);
  transition:all .3s;position:relative;overflow:hidden;
}
.kn-cta-p::before{
  content:'';position:absolute;top:0;left:-100%;width:100%;height:100%;
  background:linear-gradient(90deg,transparent,rgba(255,255,255,.13),transparent);
  transition:left .5s;
}
.kn-cta-p:hover{transform:translateY(-3px);box-shadow:0 14px 36px rgba(37,99,235,.55)}
.kn-cta-p:hover::before{left:100%}
.kn-cta-s{
  padding:1rem 2rem;border-radius:14px;
  background:transparent;border:1.5px solid var(--wh25);
  color:var(--wh80);font-weight:700;font-size:.95rem;
  text-decoration:none;display:inline-flex;align-items:center;gap:.55rem;transition:all .3s;
}
.kn-cta-s:hover{border-color:var(--ac2);color:var(--ac2);background:rgba(59,130,246,.08)}
.kn-cta-p svg,.kn-cta-s svg{width:17px;height:17px}
.kn-stats{
  display:flex;gap:2.5rem;flex-wrap:wrap;
  padding-top:2.5rem;border-top:1px solid rgba(255,255,255,.08);
}
.kn-stat strong{display:block;font-size:1.9rem;font-weight:900;color:var(--ac3);line-height:1}
.kn-stat span{font-size:.75rem;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:.08em;margin-top:.25rem;display:block}

/* slider */
.kn-vis{position:relative}
.kn-frame{
  position:relative;border-radius:28px;overflow:hidden;
  aspect-ratio:3/4;max-width:430px;
  box-shadow:0 48px 100px rgba(0,0,0,.55),0 0 0 1px rgba(255,255,255,.07);
  border:1px solid rgba(255,255,255,.07);
}
.kn-slide{position:absolute;inset:0;opacity:0;transition:opacity .9s ease}
.kn-slide.on{opacity:1}
.kn-slide img{width:100%;height:100%;object-fit:cover}
.kn-frame::after{
  content:'';position:absolute;inset:0;
  background:linear-gradient(to top,rgba(2,8,23,.55) 0%,transparent 40%);
  pointer-events:none;z-index:2;
}
.kn-dots{
  position:absolute;bottom:-52px;left:50%;transform:translateX(-50%);
  display:flex;gap:.6rem;align-items:center;
}
.kn-dot{
  width:8px;height:8px;border-radius:50%;
  background:rgba(255,255,255,.2);border:none;cursor:pointer;padding:0;
  transition:all .3s;flex-shrink:0;
}
.kn-dot.on{width:32px;border-radius:5px;background:var(--ac2)}
.kn-chip{
  position:absolute;bottom:1.5rem;right:1.5rem;z-index:3;
  background:rgba(2,8,23,.8);backdrop-filter:blur(12px);
  border:1px solid rgba(59,130,246,.25);border-radius:14px;
  padding:.9rem 1.2rem;color:#fff;
}
.kn-chip-n{font-size:1.4rem;font-weight:900;color:#34d399;line-height:1}
.kn-chip-l{font-size:.72rem;color:rgba(255,255,255,.5);margin-top:.15rem}

/* scroll cue */
.kn-scroll{
  position:absolute;bottom:2.5rem;left:50%;transform:translateX(-50%);
  z-index:10;display:flex;flex-direction:column;align-items:center;gap:.45rem;
  color:rgba(255,255,255,.2);font-size:.67rem;letter-spacing:.18em;text-transform:uppercase;
}
.kn-scroll-l{
  width:1px;height:44px;
  background:linear-gradient(to bottom,rgba(255,255,255,.25),transparent);
  animation:scrollLine 2s ease-in-out infinite;
}
@keyframes scrollLine{0%,100%{opacity:.25}50%{opacity:.75}}

/* ─ RESPONSIVE ─ */
@media(max-width:1100px){
  .kn-links,.kn-spill{display:none}
  .kn-ham{display:flex}
}
@media(max-width:900px){
  .kn-grid-h{grid-template-columns:1fr;gap:2rem;text-align:center}
  .kn-vis{display:none}
  .kn-body{padding:8rem 1.5rem 5.5rem}
  .kn-desc,.kn-cta,.kn-stats{justify-content:center;margin-left:auto;margin-right:auto}
  .kn-eyebrow{justify-content:center}
}
@media(max-width:640px){
  .kn-nav-in{padding:1.1rem 1.25rem}
  .kn-nav.kn-scrolled .kn-nav-in{padding:.75rem 1.25rem}
  .kn-logo-txt{display:none}
  .kn-body{padding:7rem 1.25rem 5rem}
  .kn-cta{flex-direction:column;align-items:stretch}
  .kn-cta-p,.kn-cta-s{justify-content:center}
  .kn-h1{font-size:clamp(2.3rem,8vw,3.2rem)}
  .kn-stats{gap:1.5rem}
  .kn-stat strong{font-size:1.6rem}
  .kn-btn-in span,.kn-btn-up span{display:none}
  .kn-btn-in,.kn-btn-up{width:40px;height:40px;border-radius:11px;padding:0;justify-content:center}
  .kn-uname{display:none}
  .kn-sm-box{margin:0 1rem}
}
@media(max-width:400px){
  .kn-nav-in{padding:.9rem 1rem}
  .kn-body{padding:6.5rem 1rem 4.5rem}
}
</style>
</head>
<body>

<!-- SEARCH MODAL -->
<div class="kn-sm" id="knSM">
  <div class="kn-sm-bg" id="knSBg"></div>
  <div class="kn-sm-box">
    <div class="kn-sr">
      <div class="kn-sr-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
      </div>
      <input class="kn-si" id="knSI" type="text" placeholder="نام کتاب، نویسنده یا موضوع..." autocomplete="off">
      <div class="kn-spin" id="knSpin"></div>
      <div class="kn-sesc" id="knSClose"><kbd>ESC</kbd> بستن</div>
    </div>
    <div class="kn-sdiv"></div>
    <div class="kn-sres" id="knSRes">
      <div class="kn-shint">
        <div class="kn-shint-ic">📚</div>
        <p>نام <strong>کتاب</strong> یا <strong>نویسنده</strong> را وارد کنید<br>تا نتایج بلادرنگ نمایش داده شود</p>
      </div>
    </div>
    <div id="knSCW" class="kn-scats-w">
      <span class="kn-scats-lbl">دسته‌بندی سریع</span>
      <div class="kn-scats">
        <?php foreach(array_slice($categories,0,6) as $cat): ?>
          <a href="products.php?category=<?=$cat['id']?>" class="kn-scat"><?=escape($cat['title'])?></a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

<!-- OVERLAY -->
<div class="kn-overlay" id="knOvl"></div>

<!-- DRAWER -->
<div class="kn-drawer" id="knDrw">
  <div class="kn-dhead">
    <a href="index.php" class="kn-dlogo">
      <div class="kn-dlogo-mk">📚</div>
      <div class="kn-dlogo-txt"><?=escape(SITE_NAME)?></div>
    </a>
    <button class="kn-dclose" id="knDC">✕</button>
  </div>
  <div class="kn-dsearch">
    <input class="kn-dsi" id="knDS" type="text" placeholder="جستجوی کتاب...">
  </div>
  <nav class="kn-dnav">
    <div class="kn-dsec">منوی اصلی</div>
    <a href="index.php"    class="kn-dlink <?=$current_file==='index'   ?'active':''?>"><span class="kn-dlink-ic">🏠</span>صفحه اصلی<span class="kn-dlink-arr">‹</span></a>
    <a href="products.php" class="kn-dlink <?=$current_file==='products'?'active':''?>"><span class="kn-dlink-ic">📦</span>محصولات<span class="kn-dlink-arr">‹</span></a>
    <a href="posts.php"    class="kn-dlink <?=$current_file==='posts'   ?'active':''?>"><span class="kn-dlink-ic">📝</span>مقالات<span class="kn-dlink-arr">‹</span></a>
    <a href="about.php"    class="kn-dlink <?=$current_file==='about'   ?'active':''?>"><span class="kn-dlink-ic">ℹ️</span>درباره ما<span class="kn-dlink-arr">‹</span></a>
    <?php if($logged_in): ?>
    <div class="kn-dsec" style="margin-top:.4rem">حساب کاربری</div>
    <a href="profile.php" class="kn-dlink"><span class="kn-dlink-ic">👤</span>پروفایل<span class="kn-dlink-arr">‹</span></a>
    <a href="orders.php"  class="kn-dlink"><span class="kn-dlink-ic">🛒</span>سفارش‌ها<span class="kn-dlink-arr">‹</span></a>
    <a href="logout.php"  class="kn-dlink" style="color:rgba(248,113,113,.8)"><span class="kn-dlink-ic">🚪</span>خروج<span class="kn-dlink-arr">‹</span></a>
    <?php endif; ?>
  </nav>
  <?php if(!$logged_in): ?>
  <div class="kn-dauth-w">
    <a href="login.php"    class="kn-da login"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M10,17V14H3V10H10V7L15,12L10,17M10,2H19A2,2 0,0,1 21,4V20A2,2 0,0,1 19,22H10A2,2 0,0,1 8,20V18H10V20H19V4H10V6H8V4A2,2 0,0,1 10,2Z"/></svg>ورود</a>
    <a href="register.php" class="kn-da reg"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M15,14C12.33,14 7,15.33 7,18V20H23V18C23,15.33 17.67,14 15,14M6,10V7H4V10H1V12H4V15H6V12H9V10M15,12A4,4 0,0,0 19,8A4,4 0,0,0 15,4A4,4 0,0,0 11,8A4,4 0,0,0 15,12Z"/></svg>ثبت‌نام رایگان</a>
  </div>
  <?php endif; ?>
</div>

<!-- HERO -->
<div class="kn-hero">
  <div class="kn-mesh"></div>
  <div class="kn-noise"></div>
  <div class="kn-grid"></div>
  <div class="kn-orbs"><div class="kn-orb"></div><div class="kn-orb"></div><div class="kn-orb"></div></div>

  <!-- NAV -->
  <nav class="kn-nav" id="knNav">
    <div class="kn-nav-in">
      <button class="kn-ham" id="knHam" aria-label="منو">
        <span class="kn-hl"></span><span class="kn-hl"></span><span class="kn-hl"></span>
      </button>
      <a href="index.php" class="kn-logo">
        <div class="kn-logo-mk">📚</div>
        <span class="kn-logo-txt"><?=escape(SITE_NAME)?></span>
      </a>
      <ul class="kn-links">
        <li><a href="index.php"    class="kn-link <?=$current_file==='index'   ?'active':''?>">🏠 صفحه اصلی</a></li>
        <li><a href="products.php" class="kn-link <?=$current_file==='products'?'active':''?>">📦 محصولات</a></li>
        <li><a href="posts.php"    class="kn-link <?=$current_file==='posts'   ?'active':''?>">📝 مقالات</a></li>
        <li><a href="about.php"    class="kn-link <?=$current_file==='about'   ?'active':''?>">ℹ️ درباره ما</a></li>
      </ul>
      <button class="kn-spill" id="knSBtn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <span class="kn-spill-txt">جستجو در کتاب‌ها...</span>
        <span class="kn-spill-kbd">⌘K</span>
      </button>
      <div class="kn-actions">
        <button class="kn-ibtn" id="knDB" title="تغییر تم"><span id="knDI">🌙</span></button>
        <a href="cart.php" class="kn-ibtn" title="سبد خرید">🛒<?php if($total_cart_count>0):?><span class="kn-badge"><?=$total_cart_count?></span><?php endif;?></a>
        <?php if($logged_in): ?>
        <div class="kn-uwrap">
          <button class="kn-ubtn" id="knUB">
            <span class="kn-uav"><?php if($user_real_avatar):?><img src="<?=escape($user_real_avatar)?>?v=<?=time()?>" alt=""><?php else:?><?=$user_avatar_letter?><?php endif;?></span>
            <span class="kn-uname"><?=escape($user_name)?></span>
            <svg class="kn-uchev" viewBox="0 0 24 24" fill="currentColor"><path d="M7,10L12,15L17,10H7Z"/></svg>
          </button>
          <div class="kn-udrop" id="knUD">
            <a href="profile.php" class="kn-uitem"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,4A4,4 0,0,1 16,8A4,4 0,0,1 12,12A4,4 0,0,1 8,8A4,4 0,0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/></svg>پروفایل</a>
            <a href="orders.php"  class="kn-uitem"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M17,18A2,2 0,0,1 19,20A2,2 0,0,1 17,22C15.89,22 15,21.1 15,20C15,18.89 15.89,18 17,18M1,2H4.27L5.21,4H20A1,1 0,0,1 21,5L17.3,11.97C16.96,12.58 16.3,13 15.55,13H8.1L7.2,14.63L7.17,14.75A0.25,0.25 0,0,0 7.42,15H19V17H7C5.89,17 5,16.1 5,15L6.6,11.59L3,4H1V2Z"/></svg>سفارش‌ها</a>
            <div class="kn-udiv"></div>
            <a href="logout.php" class="kn-uitem danger"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M16,17V14H9V10H16V7L21,12L16,17M14,2A2,2 0,0,1 16,4V6H14V4H5V20H14V18H16V20A2,2 0,0,1 14,22H5A2,2 0,0,1 3,20V4A2,2 0,0,1 5,2H14Z"/></svg>خروج</a>
          </div>
        </div>
        <?php else: ?>
        <div class="kn-auth">
          <a href="login.php"    class="kn-btn-in"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M10,17V14H3V10H10V7L15,12L10,17M10,2H19A2,2 0,0,1 21,4V20A2,2 0,0,1 19,22H10A2,2 0,0,1 8,20V18H10V20H19V4H10V6H8V4A2,2 0,0,1 10,2Z"/></svg><span>ورود</span></a>
          <a href="register.php" class="kn-btn-up"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M15,14C12.33,14 7,15.33 7,18V20H23V18C23,15.33 17.67,14 15,14M6,10V7H4V10H1V12H4V15H6V12H9V10M15,12A4,4 0,0,0 19,8A4,4 0,0,0 15,4A4,4 0,0,0 11,8A4,4 0,0,0 15,12Z"/></svg><span>ثبت‌نام</span></a>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </nav>

  <!-- HERO BODY -->
  <div class="kn-body">
    <div class="kn-grid-h">
      <div class="kn-text">
        <div class="kn-eyebrow">فروشگاه آنلاین کتاب</div>
        <h1 class="kn-h1">
          دنیای بی‌پایان
          <em class="kn-h1-em">کتاب</em>
        </h1>
        <p class="kn-desc">
          بیش از <strong>۱۰,۰۰۰</strong> عنوان کتاب با بهترین کیفیت،<br>
          قیمت مناسب و ارسال سریع به سراسر ایران.
        </p>
        <div class="kn-cta">
          <a href="products.php" class="kn-cta-p">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17,18A2,2 0,0,1 19,20A2,2 0,0,1 17,22C15.89,22 15,21.1 15,20C15,18.89 15.89,18 17,18M1,2H4.27L5.21,4H20A1,1 0,0,1 21,5L17.3,11.97C16.96,12.58 16.3,13 15.55,13H8.1L7.2,14.63L7.17,14.75A0.25,0.25 0,0,0 7.42,15H19V17H7C5.89,17 5,16.1 5,15L6.6,11.59L3,4H1V2Z"/></svg>
            مشاهده محصولات
          </a>
          <a href="posts.php" class="kn-cta-s">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19,5V7H15V5H19M9,5V11H5V5H9M19,13V19H15V13H19M9,17V19H5V17H9M21,3H13V9H21V3M11,3H3V13H11V3M21,11H13V21H21V11M11,15H3V21H11V15Z"/></svg>
            مقالات آموزشی
          </a>
        </div>
        <div class="kn-stats">
          <div class="kn-stat"><strong>+۱۰K</strong><span>عنوان کتاب</span></div>
          <div class="kn-stat"><strong>+۵۰K</strong><span>کاربر فعال</span></div>
          <div class="kn-stat"><strong>۲۴/۷</strong><span>پشتیبانی</span></div>
          <div class="kn-stat"><strong>۴.۹⭐</strong><span>رضایت مشتری</span></div>
        </div>
      </div>
      <div class="kn-vis">
        <div class="kn-frame">
          <?php foreach($posts_slider as $i=>$s): ?>
          <div class="kn-slide <?=$i===0?'on':''?>">
            <img src="./img1/<?=escape($s['img'])?>" alt="کتاب <?=$i+1?>" loading="<?=$i===0?'eager':'lazy'?>">
          </div>
          <?php endforeach; ?>
        </div>
        <div class="kn-dots" id="knDots">
          <?php foreach($posts_slider as $i=>$s): ?>
          <button class="kn-dot <?=$i===0?'on':''?>" data-i="<?=$i?>"></button>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>

  <div class="kn-scroll">
    <span>اسکرول</span>
    <div class="kn-scroll-l"></div>
  </div>
</div>

<script>
(function(){
  /* Slider */
  var sl=document.querySelectorAll('.kn-slide'), dt=document.querySelectorAll('.kn-dot'), cur=0;
  function go(n){sl.forEach(function(s){s.classList.remove('on')});dt.forEach(function(d){d.classList.remove('on')});cur=(n+sl.length)%sl.length;sl[cur].classList.add('on');dt[cur].classList.add('on')}
  dt.forEach(function(d,i){d.addEventListener('click',function(){go(i)})});
  if(sl.length>1)setInterval(function(){go(cur+1)},5000);

  /* Sticky nav */
  var nav=document.getElementById('knNav');
  function onS(){nav.classList.toggle('kn-scrolled',window.scrollY>60)}
  window.addEventListener('scroll',onS,{passive:true});onS();

  /* Drawer */
  var ham=document.getElementById('knHam'),drw=document.getElementById('knDrw'),
      ovl=document.getElementById('knOvl'),dc=document.getElementById('knDC');
  function openD(){ham.classList.add('open');drw.classList.add('open');ovl.classList.add('open');document.body.style.overflow='hidden'}
  function closeD(){ham.classList.remove('open');drw.classList.remove('open');ovl.classList.remove('open');document.body.style.overflow=''}
  ham.addEventListener('click',function(){drw.classList.contains('open')?closeD():openD()});
  ovl.addEventListener('click',closeD);dc.addEventListener('click',closeD);
  var sx=0;
  drw.addEventListener('touchstart',function(e){sx=e.touches[0].clientX},{passive:true});
  drw.addEventListener('touchmove',function(e){if(e.touches[0].clientX-sx>65)closeD()},{passive:true});
  document.getElementById('knDS').addEventListener('keydown',function(e){
    if(e.key==='Enter'&&this.value.trim())window.location=SITE_BASE+'search.php?q='+encodeURIComponent(this.value.trim());
  });

  /* Search Modal */
  var sm=document.getElementById('knSM'),sbg=document.getElementById('knSBg'),
      si=document.getElementById('knSI'),sres=document.getElementById('knSRes'),
      scw=document.getElementById('knSCW'),spin=document.getElementById('knSpin'),
      sclose=document.getElementById('knSClose'),strig=document.getElementById('knSBtn');

  var HINT='<div class="kn-shint"><div class="kn-shint-ic">📚</div><p>نام <strong>کتاب</strong> یا <strong>نویسنده</strong> را وارد کنید<br>تا نتایج بلادرنگ نمایش داده شود</p></div>';
  function openS(){sm.classList.add('open');document.body.style.overflow='hidden';setTimeout(function(){si.focus()},120)}
  function closeS(){sm.classList.remove('open');document.body.style.overflow='';si.value='';sres.innerHTML=HINT;scw.style.display='';spin.style.display='none'}
  window.closeSearch=closeS;
  strig&&strig.addEventListener('click',openS);
  sbg.addEventListener('click',closeS);sclose.addEventListener('click',closeS);
  document.addEventListener('keydown',function(e){
    if((e.metaKey||e.ctrlKey)&&e.key==='k'){e.preventDefault();openS()}
    if(e.key==='Escape'){closeS();closeD()}
  });

  var SITE_BASE=(function(){
    var d='<?=rtrim(str_replace("\\\\","/",$_SERVER["SCRIPT_NAME"]),"/")?>'.replace(/\/[^\/]*$/,'')||'';
    return window.location.protocol+'//'+window.location.host+d+'/';
  })();

  var tmr;
  si.addEventListener('input',function(){
    clearTimeout(tmr);var q=this.value.trim();
    if(!q){sres.innerHTML=HINT;scw.style.display='';spin.style.display='none';return}
    scw.style.display='none';spin.style.display='block';sres.innerHTML='';
    tmr=setTimeout(function(){
      fetch(SITE_BASE+'search_live.php?q='+encodeURIComponent(q)+'&_='+Date.now())
        .then(function(r){if(!r.ok)throw 0;return r.json()})
        .then(function(d){spin.style.display='none';renderR(d,q)})
        .catch(function(){spin.style.display='none';sres.innerHTML='<div class="kn-sall-w"><a href="'+SITE_BASE+'search.php?q='+encodeURIComponent(q)+'" class="kn-sall">مشاهده نتایج برای «'+esc(q)+'»</a></div>'});
    },300);
  });

  function renderR(data,q){
    if(!data||(!data.products.length&&!data.posts.length)){
      sres.innerHTML='<div class="kn-sempty"><span>🔍</span>نتیجه‌ای یافت نشد</div>';
      var d=document.createElement('div');d.className='kn-sall-w';d.innerHTML=allB(q);sres.appendChild(d);return;
    }
    var h='';
    if(data.products&&data.products.length){
      h+='<div class="kn-ssec">📦 محصولات</div>';
      data.products.slice(0,3).forEach(function(p){
        var dc=(p.old_price>0&&p.old_price!=p.price)?'<span class="kn-sdisc">'+Math.round((p.old_price-p.price)/p.old_price*100)+'%</span>':'';
        var od=(p.old_price!=p.price)?'<span class="kn-sold">'+fmt(p.old_price)+'</span>':'';
        h+='<a href="'+SITE_BASE+'single_product.php?product='+p.id+'" class="kn-sitem" onclick="closeSearch()">'
          +'<div class="kn-simg-w"><img class="kn-simg" src="'+SITE_BASE+'upload/products/'+esc(p.pic)+'" loading="lazy"></div>'
          +'<div class="kn-sinfo"><div class="kn-sname">'+hl(p.name,q)+'</div><div class="kn-smeta"><span class="kn-sprice">'+fmt(p.price)+'</span>'+od+dc+'</div></div>'
          +'<span class="kn-sarr">←</span></a>';
      });
    }
    if(data.posts&&data.posts.length){
      h+='<div class="kn-ssec">📝 مقالات</div>';
      data.posts.slice(0,3).forEach(function(p){
        h+='<a href="'+SITE_BASE+'single.php?post='+p.id+'" class="kn-sitem" onclick="closeSearch()">'
          +'<div class="kn-simg-w"><img class="kn-simg" src="'+SITE_BASE+'upload/posts/'+esc(p.image)+'" loading="lazy"></div>'
          +'<div class="kn-sinfo"><div class="kn-sname">'+hl(p.title,q)+'</div><div class="kn-smeta"><span style="font-size:.78rem;color:rgba(255,255,255,.4)">✍️ '+esc(p.author)+'</span></div></div>'
          +'<span class="kn-sarr">←</span></a>';
      });
    }
    sres.innerHTML=h;
    var d=document.createElement('div');d.className='kn-sall-w';d.innerHTML=allB(q);sres.appendChild(d);
  }
  function allB(q){return'<a href="'+SITE_BASE+'search.php?q='+encodeURIComponent(q)+'" class="kn-sall" onclick="closeSearch()"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M9.5,3A6.5,6.5 0,0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0,0,1 3,9.5A6.5,6.5 0,0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z"/></svg>مشاهده همه نتایج برای «'+esc(q)+'»</a>'}
  function hl(t,q){if(!q)return esc(t);var re=new RegExp('('+q.replace(/[.*+?^${}()|[\]\\]/g,'\\$&')+')','gi');return esc(t).replace(re,'<mark style="background:rgba(250,204,21,.2);color:#fde68a;padding:0 2px;border-radius:3px;font-weight:700">$1</mark>')}
  function esc(s){var d=document.createElement('div');d.textContent=s||'';return d.innerHTML}
  function fmt(n){return Number(n).toLocaleString('fa-IR')+' تومان'}
  si.addEventListener('keydown',function(e){if(e.key==='Enter'&&this.value.trim())window.location=SITE_BASE+'search.php?q='+encodeURIComponent(this.value.trim())});

  /* User Dropdown */
  var ub=document.getElementById('knUB'),ud=document.getElementById('knUD');
  if(ub){ub.addEventListener('click',function(e){e.stopPropagation();ub.classList.toggle('open');ud.classList.toggle('open')});document.addEventListener('click',function(){if(ub){ub.classList.remove('open');ud.classList.remove('open')}})}

  /* Dark Mode */
  var db=document.getElementById('knDB'),di=document.getElementById('knDI');
  if(localStorage.getItem('darkMode')==='enabled'){document.body.classList.add('dark-mode');di.textContent='☀️'}
  db.addEventListener('click',function(){var d=document.body.classList.toggle('dark-mode');localStorage.setItem('darkMode',d?'enabled':'disabled');di.textContent=d?'☀️':'🌙'});
})();
</script>