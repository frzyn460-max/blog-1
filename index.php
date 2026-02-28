<?php
require_once("./include/header.php");
#3b82f6
// linear-gradient(90deg,#1d4ed8,#3b82f6);
$category_id = isset($_GET['category']) ? filter_var($_GET['category'], FILTER_VALIDATE_INT) : null;

if ($category_id) {
    $posts    = fetchAll($db, 'SELECT * FROM posts WHERE category_id = ? ORDER BY id DESC LIMIT 4', [$category_id]);
    $products = fetchAll($db, 'SELECT * FROM product WHERE category_id = ? ORDER BY id DESC LIMIT 6', [$category_id]);
} else {
    $posts    = fetchAll($db, "SELECT * FROM posts ORDER BY id DESC LIMIT 4");
    $products = fetchAll($db, "SELECT * FROM product ORDER BY id DESC LIMIT 6");
}

$total_products = fetchOne($db, "SELECT COUNT(*) as c FROM product")['c'] ?? 0;
$total_members  = fetchOne($db, "SELECT COUNT(*) as c FROM members")['c'] ?? 0;
$flash_product  = fetchOne($db, "SELECT *, (CAST(price AS UNSIGNED) - CAST(`new-price` AS UNSIGNED)) as disc FROM product ORDER BY disc DESC LIMIT 1");

function truncateText($t, $l = 130) { $t = strip_tags($t); return mb_strlen($t)>$l ? mb_substr($t,0,$l).'...' : $t; }
function formatPrice($p) { return number_format($p).' تومان'; }
function calcDisc($o, $n)  { return $o > 0 ? round((($o - $n) / $o) * 100) : 0; }
?>
<style>
/* ════════════════════════════════════════
   INDEX — Midnight Blue Premium
   Light + Dark Mode
════════════════════════════════════════ */

/* ── LIGHT MODE (default) ── */
:root {
  --ac:#1d4ed8; --ac2:#3b82f6; --ac3:#60a5fa;
  --green:#059669; --red:#dc2626; --orange:#ea580c;
  --sh:0 4px 24px rgba(0,0,0,.08);
  --ease:cubic-bezier(.4,0,.2,1);

  /* backgrounds */
  --body-bg:#f1f5f9;
  --card:     #ffffff;
  --card2:    #f8fafc;
  --cardbrd:  #e2e8f0;
  --cardhov:  #f1f5f9;

  /* text */
  --tx:  #0f172a;
  --tx2: #64748b;
  --tx3: #94a3b8;

  /* flash right panel */
  --flash-right-bg: #f8fafc;

  /* countdown */
  --cd-box-bg:   rgba(255, 255, 255, 0.88);
  --cd-box-brd:  rgba(29,78,216,.18);
  --cd-val-c:    #1d4ed8;
  --cd-lbl2-c:   #3b82f6;

  /* timer */
  --ft-bg:  #f1f5f9;
  --ft-brd: #e2e8f0;
  --ft-val: #0f172a;

  /* feat hover line */
  --feat-line: linear-gradient(90deg,#1d4ed8,#3b82f6);

  /* section label */
  --sec-label-c: #1d4ed8;

  /* toast */
  --toast-bg:  #ffffff;
  --toast-brd: #e2e8f0;
  --toast-tx:  #0f172a;
}

/* ── DARK MODE ── */
body.dark-mode {
  --body-bg:   #060d1f;
  --card:      rgba(255,255,255,.04);
  --card2:     rgba(255,255,255,.06);
  --cardbrd:   rgba(255,255,255,.08);
  --cardhov:   rgba(255,255,255,.07);
  --tx:        #f0f6ff;
  --tx2:       #94a3b8;
  --tx3:       #475569;
  --green:     #34d399;
  --red:       #f87171;
  --orange:    #fb923c;
  --sh:        0 20px 60px rgba(0,0,0,.4);

  --flash-right-bg: rgba(255,255,255,.03);

  --cd-box-bg:   rgba(255,255,255,.12);
  --cd-box-brd:  rgba(255,255,255,.2);
  --cd-val-c:    #ffffff;
  --cd-lbl2-c:   rgba(255,255,255,.5);

  --ft-bg:  rgba(255,255,255,.06);
  --ft-brd: rgba(255,255,255,.08);
  --ft-val: #f0f6ff;

  --feat-line: linear-gradient(90deg,#3b82f6,#60a5fa);
  --sec-label-c: #60a5fa;

  --toast-bg:  rgba(9,20,48,.96);
  --toast-brd: rgba(255,255,255,.08);
  --toast-tx:  #f0f6ff;
}

/* ── BASE ── */
body { background: var(--body-bg) !important; color: var(--tx) !important; transition: background .3s, color .3s }

.idx{max-width:1400px;margin:0 auto;padding:0 1.5rem}
@media(max-width:768px){.idx{padding:0 1.1rem}}
@media(max-width:480px){.idx{padding:0 .9rem}}

/* ── TICKER ── */
.ticker{
  background:linear-gradient(90deg,var(--ac),var(--ac2));
  padding:.95rem 0;overflow:hidden;
}
.ticker-track{display:flex;white-space:nowrap;animation:tickScroll 28s linear infinite}
@keyframes tickScroll{from{transform:translateX(0)}to{transform:translateX(-50%)}}
.ticker-item{
  display:inline-flex;align-items:center;gap:.6rem;
  color:#fff;font-weight:700;font-size:.86rem;
  padding:0 2.2rem;border-left:1px solid rgba(255,255,255,.2);flex-shrink:0;
}

/* ── SECTION HEADER ── */
.sec{padding:5rem 0}
@media(max-width:768px){.sec{padding:3.5rem 0}}
@media(max-width:480px){.sec{padding:2.8rem 0}}

.sec-head{text-align:center;margin-bottom:3rem}
.sec-label{
  display:inline-flex;align-items:center;gap:.55rem;
  font-size:.68rem;letter-spacing:.22em;text-transform:uppercase;
  color:var(--sec-label-c);font-weight:700;margin-bottom:.7rem;
}
.sec-label::before,.sec-label::after{content:'';width:20px;height:1px;background:var(--sec-label-c)}
.sec-title{font-size:clamp(1.7rem,3vw,2.6rem);font-weight:900;color:var(--tx);letter-spacing:-.02em;line-height:1.2}
.sec-sub{color:var(--tx2);margin-top:.5rem;font-size:.93rem}

.view-all{
  display:inline-flex;align-items:center;gap:.45rem;
  color:var(--ac2);font-weight:700;font-size:.84rem;
  border:1.5px solid var(--ac2);padding:.55rem 1.4rem;
  border-radius:50px;transition:all .3s;margin-top:1.4rem;text-decoration:none;
}
.view-all:hover{background:var(--ac2);color:#fff;box-shadow:0 6px 20px rgba(59,130,246,.3)}
.view-all svg{width:14px;height:14px}

/* ── AOS ── */
[data-aos]{opacity:0;transition:transform .65s var(--ease),opacity .65s var(--ease)}
[data-aos="up"]{transform:translateY(32px)}
[data-aos="zoom"]{transform:scale(.93)}
[data-aos="left"]{transform:translateX(-30px)}
[data-aos].on{opacity:1!important;transform:none!important}

/* ── FLASH SALE ── */
.flash{
  background:var(--card);border:1px solid var(--cardbrd);
  border-radius:24px;overflow:hidden;
  box-shadow:var(--sh);
}
.flash-in{display:grid;grid-template-columns:1fr 1fr;align-items:center}
@media(max-width:860px){.flash-in{grid-template-columns:1fr}}
.flash-left{padding:3rem 3.5rem}
@media(max-width:860px){.flash-left{padding:2.5rem 2rem}}
@media(max-width:480px){.flash-left{padding:2rem 1.5rem}}

.flash-tag{
  display:inline-flex;align-items:center;gap:.5rem;
  background:linear-gradient(135deg,#ef4444,#f97316);color:#fff;
  padding:.38rem .95rem;border-radius:8px;font-weight:800;font-size:.78rem;letter-spacing:.06em;
  margin-bottom:1.3rem;animation:tagPulse 1.8s ease-in-out infinite;
}
@keyframes tagPulse{0%,100%{opacity:1}50%{opacity:.75}}
.flash-name{font-size:1.9rem;font-weight:900;color:var(--tx);margin-bottom:.4rem;line-height:1.2}
@media(max-width:480px){.flash-name{font-size:1.5rem}}
.flash-prices{display:flex;align-items:baseline;gap:.9rem;margin:1.1rem 0 1.6rem;flex-wrap:wrap}
.flash-old{font-size:.88rem;color:var(--tx3);text-decoration:line-through}
.flash-new{font-size:1.8rem;font-weight:900;color:var(--green)}
@media(max-width:480px){.flash-new{font-size:1.5rem}}
.flash-pct{background:linear-gradient(135deg,#ef4444,#f97316);color:#fff;padding:.22rem .7rem;border-radius:7px;font-weight:800;font-size:.82rem}

.flash-timer{display:flex;gap:.45rem;align-items:center;margin-bottom:1.8rem}
.ft{
  background:var(--ft-bg);border:1px solid var(--ft-brd);
  border-radius:10px;padding:.65rem .85rem;min-width:54px;text-align:center;
}
.ftv{display:block;font-size:1.45rem;font-weight:900;color:var(--ft-val);line-height:1}
.ftl{font-size:.58rem;color:var(--tx2);text-transform:uppercase;letter-spacing:.1em}
.fts{color:var(--tx2);font-size:1.2rem;font-weight:700}

.flash-btn{
  display:inline-flex;align-items:center;gap:.6rem;
  background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff;
  padding:.95rem 2.2rem;border-radius:14px;font-weight:800;font-size:.93rem;
  transition:all .3s;box-shadow:0 8px 24px rgba(239,68,68,.35);text-decoration:none;
}
.flash-btn:hover{transform:translateY(-3px);box-shadow:0 14px 32px rgba(239,68,68,.5)}
.flash-btn svg{width:17px;height:17px}

.flash-right{
  display:flex;align-items:center;justify-content:center;
  padding:2.5rem;
  background:var(--flash-right-bg);
  border-right:1px solid var(--cardbrd);
  min-height:340px;
  position:relative;
}
@media(max-width:860px){.flash-right{display:none}}
.flash-img{width:200px;height:260px;object-fit:cover;border-radius:16px;box-shadow:var(--sh);border:1px solid var(--cardbrd)}
.flash-ring{
  position:absolute;top:1.5rem;left:1.5rem;
  width:66px;height:66px;border-radius:50%;
  background:linear-gradient(135deg,#ef4444,#f97316);
  display:flex;flex-direction:column;align-items:center;justify-content:center;
  color:#fff;font-weight:900;line-height:1.1;
  box-shadow:0 8px 22px rgba(239,68,68,.45);
}
.flash-ring big{font-size:1.15rem}.flash-ring small{font-size:.58rem;letter-spacing:.05em}

/* ── PRODUCT GRID ── */
.pgrid{display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:1.8rem}
@media(max-width:768px){.pgrid{grid-template-columns:repeat(2,1fr);gap:1.2rem}}
@media(max-width:420px){.pgrid{grid-template-columns:1fr;gap:1rem}}

.pcard{
  background:var(--card);border:1px solid var(--cardbrd);
  border-radius:20px;overflow:hidden;
  transition:all .4s var(--ease);display:flex;flex-direction:column;
}
.pcard:hover{transform:translateY(-8px);box-shadow:var(--sh);border-color:rgba(29,78,216,.25)}

.pcard-img{position:relative;padding-top:95%;overflow:hidden}
.pcard-img img{
  position:absolute;top:0;left:0;width:100%;height:100%;
  object-fit:cover;transition:transform .5s var(--ease);
}
.pcard:hover .pcard-img img{transform:scale(1.08)}
.pcard-disc{
  position:absolute;top:12px;right:12px;z-index:5;
  background:linear-gradient(135deg,#ef4444,#f97316);color:#fff;
  padding:.35rem .7rem;border-radius:9px;font-size:.78rem;font-weight:800;
  box-shadow:0 4px 14px rgba(239,68,68,.4);
}

.pcard-ov{
  position:absolute;inset:0;
  background:rgba(0,0,0,.72);display:flex;align-items:center;justify-content:center;
  opacity:0;transition:opacity .3s;
}
.pcard:hover .pcard-ov{opacity:1}
.pcard-qv{
  background:#fff;color:var(--ac);padding:.72rem 1.4rem;border-radius:11px;
  font-weight:700;font-size:.88rem;text-decoration:none;
  transform:translateY(18px);transition:transform .3s;
}
.pcard:hover .pcard-qv{transform:translateY(0)}

.pcard-body{padding:1.4rem;display:flex;flex-direction:column;flex:1}
.pcard-name{font-size:1rem;font-weight:700;color:var(--tx);margin-bottom:.9rem;line-height:1.4;flex:1}
.pcard-name a{color:inherit;text-decoration:none;transition:color .25s}
.pcard-name a:hover{color:var(--ac2)}
.pcard-prices{display:flex;align-items:center;gap:.85rem;margin-bottom:1.1rem;flex-wrap:wrap}
.pcard-old{font-size:.86rem;color:var(--tx3);text-decoration:line-through}
.pcard-new{font-size:1.25rem;font-weight:800;color:var(--green)}

.pcard-btn{
  width:100%;padding:.85rem;
  background:linear-gradient(135deg,var(--ac),var(--ac2));
  color:#fff;border:none;border-radius:12px;
  font-weight:700;font-size:.88rem;font-family:inherit;
  display:flex;align-items:center;justify-content:center;gap:.5rem;
  cursor:pointer;transition:all .3s;text-decoration:none;
}
.pcard-btn:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(29,78,216,.35)}
.pcard-btn svg{width:16px;height:16px}

/* ── COUNTDOWN ── */
.countdown{
  background:linear-gradient(135deg,var(--ac),var(--ac2));
  border-radius:24px;padding:4.5rem 2rem;text-align:center;
  position:relative;overflow:hidden;
}
.countdown::before{
  content:'';position:absolute;inset:0;
  background-image:radial-gradient(circle,rgba(255,255,255,.06) 1px,transparent 1px);
  background-size:24px 24px;pointer-events:none;
}
.countdown::after{
  content:'';position:absolute;
  width:500px;height:500px;border-radius:50%;
  background:radial-gradient(circle,rgba(255,255,255,.06) 0%,transparent 65%);
  top:50%;left:50%;transform:translate(-50%,-50%);pointer-events:none;
}
@media(max-width:640px){.countdown{padding:3rem 1.5rem}}
.cd-inner{position:relative;z-index:2}
.cd-lbl{color:rgba(255,255,255,.65);font-size:.76rem;letter-spacing:.2em;text-transform:uppercase;font-weight:700;display:block;margin-bottom:.9rem}
.cd-title{font-size:clamp(1.7rem,3.5vw,2.6rem);font-weight:900;color:#fff;margin-bottom:.5rem}
.cd-sub{color:rgba(255,255,255,.6);margin-bottom:2.3rem;font-size:.92rem}
.cd-wrap{display:inline-flex;gap:.7rem;align-items:center;justify-content:center;flex-wrap:wrap}
.cd-box{
  background:var(--cd-box-bg);border:1px solid var(--cd-box-brd);
  border-radius:16px;padding:1.3rem 1.1rem;min-width:86px;text-align:center;
  backdrop-filter:blur(4px);
}
@media(max-width:480px){.cd-box{min-width:70px;padding:1rem .85rem}}
.cd-val{display:block;font-size:2.5rem;font-weight:900;color:var(--cd-val-c);line-height:1;margin-bottom:.35rem}
@media(max-width:480px){.cd-val{font-size:2rem}}
.cd-lbl2{font-size:.64rem;color:var(--cd-lbl2-c);text-transform:uppercase;letter-spacing:.12em}
.cd-sep{color:rgba(255,255,255,.3);font-size:1.9rem;font-weight:700}
.cd-btn{
  display:inline-flex;align-items:center;gap:.55rem;
  margin-top:2.2rem;padding:.95rem 2.4rem;border-radius:14px;
  background:rgba(255,255,255,.15);border:1.5px solid rgba(255,255,255,.35);
  color:#fff;font-weight:700;font-size:.93rem;text-decoration:none;transition:all .3s;
}
.cd-btn:hover{background:rgba(255,255,255,.25);transform:translateY(-2px)}
.cd-btn svg{width:16px;height:16px}

/* ── POSTS ── */
.posts-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(290px,1fr));gap:1.8rem}
@media(max-width:768px){.posts-grid{grid-template-columns:repeat(2,1fr);gap:1.2rem}}
@media(max-width:520px){.posts-grid{grid-template-columns:1fr;gap:1rem}}

.post{
  background:var(--card);border:1px solid var(--cardbrd);
  border-radius:20px;overflow:hidden;
  display:flex;flex-direction:column;
  transition:all .4s var(--ease);
}
.post:hover{transform:translateY(-7px);box-shadow:var(--sh);border-color:rgba(29,78,216,.2)}
.post-img{position:relative;padding-top:56%;overflow:hidden}
.post-img img{position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover;transition:transform .5s}
.post:hover .post-img img{transform:scale(1.07)}
.post-cat{
  position:absolute;bottom:12px;right:12px;
  background:rgba(0,0,0,.72);backdrop-filter:blur(6px);
  color:#fff;padding:.28rem .78rem;border-radius:7px;
  font-weight:700;font-size:.72rem;
}
.post-time{
  position:absolute;top:12px;left:12px;
  background:rgba(0,0,0,.65);backdrop-filter:blur(6px);
  color:#fff;padding:.28rem .75rem;border-radius:7px;font-size:.7rem;font-weight:600;
}
.post-body{padding:1.4rem;flex:1;display:flex;flex-direction:column}
.post-title{font-size:1.05rem;font-weight:700;color:var(--tx);margin-bottom:.7rem;line-height:1.4}
.post-title a{color:inherit;text-decoration:none;transition:color .25s}
.post-title a:hover{color:var(--ac2)}
.post-excerpt{color:var(--tx2);font-size:.88rem;line-height:1.78;flex:1;margin-bottom:1.2rem}
.post-foot{
  display:flex;align-items:center;justify-content:space-between;
  padding-top:.9rem;border-top:1px solid var(--cardbrd);
}
.post-author{display:flex;align-items:center;gap:.55rem;color:var(--tx2);font-size:.83rem}
.post-av{
  width:28px;height:28px;border-radius:50%;
  background:linear-gradient(135deg,var(--ac),var(--ac2));
  color:#fff;display:flex;align-items:center;justify-content:center;
  font-weight:800;font-size:.78rem;flex-shrink:0;
}
.read-more{
  display:flex;align-items:center;gap:.3rem;
  color:var(--ac2);text-decoration:none;font-weight:700;font-size:.83rem;
  transition:gap .25s;
}
.read-more:hover{gap:.55rem}
.read-more svg{width:14px;height:14px}

/* ── NEWSLETTER ── */
.nl{
  background:var(--card);border:1px solid var(--cardbrd);
  border-radius:24px;padding:4rem 2rem;text-align:center;
  position:relative;overflow:hidden;
}
.nl::before{content:'📚';font-size:8rem;opacity:.04;position:absolute;top:-1rem;right:0;pointer-events:none;line-height:1}
.nl-title{font-size:clamp(1.5rem,2.8vw,2.2rem);font-weight:900;color:var(--tx);margin-bottom:.6rem}
.nl-sub{color:var(--tx2);margin-bottom:2rem;font-size:.92rem}
.nl-form{display:flex;gap:.85rem;max-width:500px;margin:0 auto;flex-wrap:wrap}
.nl-input{
  flex:1;padding:.95rem 1.3rem;
  border:1.5px solid var(--cardbrd);border-radius:13px;
  font-size:.93rem;font-family:inherit;
  background:var(--card2);color:var(--tx);
  transition:all .3s;outline:none;min-width:200px;
}
.nl-input:focus{border-color:var(--ac2);box-shadow:0 0 0 3px rgba(59,130,246,.12)}
.nl-input::placeholder{color:var(--tx3)}
.nl-btn{
  padding:.95rem 1.9rem;border-radius:13px;
  background:linear-gradient(135deg,var(--ac),var(--ac2));
  color:#fff;font-weight:700;font-size:.93rem;
  border:none;cursor:pointer;font-family:inherit;transition:all .3s;white-space:nowrap;
}
.nl-btn:hover{transform:translateY(-2px);box-shadow:0 8px 22px rgba(29,78,216,.35)}
@media(max-width:480px){.nl-form{flex-direction:column}.nl-btn{width:100%}}

/* ── FEATURES ── */
.feats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:1.4rem}
@media(max-width:600px){.feats-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:380px){.feats-grid{grid-template-columns:1fr}}

.feat{
  background:var(--card);border:1px solid var(--cardbrd);
  border-radius:18px;padding:2rem 1.7rem;text-align:center;
  transition:all .38s var(--ease);position:relative;overflow:hidden;
}
.feat::after{
  content:'';position:absolute;bottom:0;left:0;right:0;height:3px;
  background:linear-gradient(90deg,var(--ac),var(--ac2));
  transform:scaleX(0);transition:transform .35s var(--ease);transform-origin:right;
}
.feat:hover{transform:translateY(-7px);box-shadow:var(--sh)}
.feat:hover::after{transform:scaleX(1);transform-origin:left}
.feat-ic{font-size:2.4rem;margin-bottom:1rem;display:block}
.feat-t{font-weight:800;color:var(--tx);margin-bottom:.35rem;font-size:.97rem}
.feat-d{color:var(--tx2);font-size:.82rem;line-height:1.7}

/* ── TESTIMONIALS ── */
.testi-wrap{overflow:hidden;border-radius:20px}
.testi-track{display:flex;transition:transform .55s var(--ease)}
.tcard{
  min-width:100%;background:var(--card);border:1px solid var(--cardbrd);
  border-radius:20px;padding:2.8rem 2rem;text-align:center;position:relative;overflow:hidden;
}
.tcard::before{
  content:'"';font-size:7rem;color:var(--ac2);opacity:.07;
  position:absolute;top:-1.5rem;right:2rem;line-height:1;pointer-events:none;
}
@media(max-width:640px){.tcard{padding:2rem 1.4rem}}
.tcard-stars{font-size:1.2rem;color:#f59e0b;letter-spacing:3px;margin-bottom:1.2rem}
.tcard-txt{font-size:1.1rem;color:var(--tx);line-height:1.9;margin-bottom:1.8rem;font-style:italic;max-width:640px;margin-left:auto;margin-right:auto}
@media(max-width:640px){.tcard-txt{font-size:.95rem}}
.tcard-av{
  width:50px;height:50px;border-radius:50%;
  background:linear-gradient(135deg,var(--ac),var(--ac2));
  color:#fff;font-weight:900;font-size:1.2rem;
  display:flex;align-items:center;justify-content:center;margin:0 auto .9rem;
}
.tcard-name{font-weight:800;color:var(--tx);font-size:.97rem}
.tcard-role{font-size:.78rem;color:var(--tx2);margin-top:.18rem}

.testi-ctrl{display:flex;align-items:center;justify-content:center;gap:.9rem;margin-top:1.8rem}
.t-btn{
  width:40px;height:40px;border-radius:50%;
  border:1.5px solid var(--cardbrd);background:var(--card);
  color:var(--tx2);font-size:1.2rem;cursor:pointer;
  display:flex;align-items:center;justify-content:center;transition:all .3s;font-family:inherit;line-height:1;
}
.t-btn:hover{background:var(--ac);border-color:var(--ac);color:#fff}
.t-dots{display:flex;gap:.45rem;align-items:center}
.t-dot{
  width:8px;height:8px;border-radius:50%;
  background:var(--cardbrd);border:none;cursor:pointer;padding:0;transition:all .28s;
}
.t-dot.on{width:24px;border-radius:4px;background:var(--ac2)}

/* ── TOAST ── */
.toast{
  position:fixed;bottom:26px;right:26px;
  background:var(--card);backdrop-filter:blur(16px);
  color:var(--tx);border:1px solid var(--cardbrd);
  padding:.82rem 1.6rem;border-radius:13px;font-size:.86rem;font-weight:600;
  box-shadow:var(--sh);z-index:9999;
  transform:translateY(70px);opacity:0;
  transition:all .33s var(--ease);pointer-events:none;
}
.toast.show{transform:translateY(0);opacity:1}

.no-data{
  grid-column:1/-1;text-align:center;padding:3.5rem 2rem;
  border-radius:18px;border:2px dashed var(--cardbrd);color:var(--tx2);font-size:.95rem;
}
</style>

<!-- ═════ TICKER ═════ -->
<div class="ticker" aria-hidden="true">
  <div class="ticker-track">
    <?php for($t=0;$t<2;$t++): ?>
      <span class="ticker-item">📦 ارسال رایگان بالای ۲۰۰ هزار تومان</span>
      <span class="ticker-item">⚡ تخفیف ویژه تا ۵۰٪</span>
      <span class="ticker-item">🔒 پرداخت امن و رمزگذاری‌شده</span>
      <span class="ticker-item">🏆 کتاب‌های اصل و با کیفیت</span>
      <span class="ticker-item">🎧 پشتیبانی ۲۴ ساعته</span>
      <span class="ticker-item">🚀 تحویل سریع در سراسر ایران</span>
    <?php endfor; ?>
  </div>
</div>

<div class="idx">

  <!-- ═════ FLASH SALE ═════ -->
  <?php if ($flash_product):
    $fdisc = calcDisc((int)$flash_product['price'], (int)$flash_product['new-price']); ?>
  <section class="sec" style="padding-bottom:0">
    <div class="flash" data-aos="zoom">
      <div class="flash-in">
        <div class="flash-left">
          <div class="flash-tag">⚡ فلش سیل — فقط امروز</div>
          <div class="flash-name"><?= escape($flash_product['name']) ?></div>
          <div class="flash-prices">
            <span class="flash-old"><?= formatPrice((int)$flash_product['price']) ?></span>
            <span class="flash-new"><?= formatPrice((int)$flash_product['new-price']) ?></span>
            <span class="flash-pct"><?= $fdisc ?>% تخفیف</span>
          </div>
          <div class="flash-timer">
            <div class="ft"><span class="ftv" id="fh">12</span><span class="ftl">ساعت</span></div>
            <span class="fts">:</span>
            <div class="ft"><span class="ftv" id="fm">00</span><span class="ftl">دقیقه</span></div>
            <span class="fts">:</span>
            <div class="ft"><span class="ftv" id="fs">00</span><span class="ftl">ثانیه</span></div>
          </div>
          <a href="single_product.php?product=<?= $flash_product['id'] ?>" class="flash-btn">
            همین الان بخر
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M4,11V13H16L10.5,18.5L11.92,19.92L19.84,12L11.92,4.08L10.5,5.5L16,11H4Z"/></svg>
          </a>
        </div>
        <div class="flash-right">
          <img class="flash-img" src="./upload/products/<?= escape($flash_product['pic']) ?>" alt="<?= escape($flash_product['name']) ?>">
          <div class="flash-ring"><big><?= $fdisc ?>%</big><small>تخفیف</small></div>
        </div>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- ═════ PRODUCTS ═════ -->
  <section class="sec">
    <div class="sec-head" data-aos="up">
      <div class="sec-label">محصولات ویژه</div>
      <h2 class="sec-title">بهترین کتاب‌های ما</h2>
      <p class="sec-sub">منتخب ویراستاران کتاب‌نت برای شما</p>
      <a href="products.php" class="view-all">مشاهده همه <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z"/></svg></a>
    </div>
    <div class="pgrid">
      <?php if (!empty($products)): ?>
        <?php foreach ($products as $idx => $p): ?>
          <?php $disc = calcDisc((int)$p['price'], (int)$p['new-price']); ?>
          <article class="pcard" data-aos="up" style="transition-delay:<?= $idx*70 ?>ms">
            <div class="pcard-img">
              <img src="./upload/products/<?= escape($p['pic']) ?>" alt="<?= escape($p['name']) ?>" loading="lazy">
              <?php if ($disc>0): ?><span class="pcard-disc"><?= $disc ?>% تخفیف</span><?php endif; ?>
              <div class="pcard-ov"><a href="single_product.php?product=<?= $p['id'] ?>" class="pcard-qv">مشاهده سریع</a></div>
            </div>
            <div class="pcard-body">
              <h3 class="pcard-name"><a href="single_product.php?product=<?= $p['id'] ?>"><?= escape($p['name']) ?></a></h3>
              <div class="pcard-prices">
                <?php if ((int)$p['price'] !== (int)$p['new-price']): ?><span class="pcard-old"><?= formatPrice((int)$p['price']) ?></span><?php endif; ?>
                <span class="pcard-new"><?= formatPrice((int)$p['new-price']) ?></span>
              </div>
              <a href="single_product.php?product=<?= $p['id'] ?>" class="pcard-btn">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17,18A2,2 0 0,1 19,20A2,2 0 0,1 17,22C15.89,22 15,21.1 15,20C15,18.89 15.89,18 17,18M1,2H4.27L5.21,4H20A1,1 0 0,1 21,5L17.3,11.97C16.96,12.58 16.3,13 15.55,13H8.1L7.2,14.63L7.17,14.75A0.25,0.25 0 0,0 7.42,15H19V17H7C5.89,17 5,16.1 5,15L6.6,11.59L3,4H1V2Z"/></svg>
                افزودن به سبد
              </a>
            </div>
          </article>
        <?php endforeach; ?>
      <?php else: ?><div class="no-data">محصولی یافت نشد.</div><?php endif; ?>
    </div>
  </section>

  <!-- ═════ COUNTDOWN ═════ -->
  <div class="countdown" data-aos="zoom">
    <div class="cd-inner">
      <span class="cd-lbl">پیشنهاد ویژه هفته</span>
      <h2 class="cd-title">تخفیف ۵۰٪ تا پایان این هفته</h2>
      <p class="cd-sub">فرصت را از دست ندهید — با بهترین قیمت بخرید</p>
      <div class="cd-wrap">
        <div class="cd-box"><span class="cd-val" id="cd-d">00</span><span class="cd-lbl2">روز</span></div>
        <span class="cd-sep">:</span>
        <div class="cd-box"><span class="cd-val" id="cd-h">00</span><span class="cd-lbl2">ساعت</span></div>
        <span class="cd-sep">:</span>
        <div class="cd-box"><span class="cd-val" id="cd-m">00</span><span class="cd-lbl2">دقیقه</span></div>
        <span class="cd-sep">:</span>
        <div class="cd-box"><span class="cd-val" id="cd-s">00</span><span class="cd-lbl2">ثانیه</span></div>
      </div>
      <a href="products.php" class="cd-btn">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17,18A2,2 0 0,1 19,20A2,2 0 0,1 17,22C15.89,22 15,21.1 15,20C15,18.89 15.89,18 17,18M1,2H4.27L5.21,4H20A1,1 0 0,1 21,5L17.3,11.97C16.96,12.58 16.3,13 15.55,13H8.1L7.2,14.63L7.17,14.75A0.25,0.25 0 0,0 7.42,15H19V17H7C5.89,17 5,16.1 5,15L6.6,11.59L3,4H1V2Z"/></svg>
        خرید ویژه
      </a>
    </div>
  </div>

  <!-- ═════ POSTS ═════ -->
  <section class="sec">
    <div class="sec-head" data-aos="up">
      <div class="sec-label">مقالات آموزشی</div>
      <h2 class="sec-title">جدیدترین مقالات</h2>
      <p class="sec-sub">دانش خود را با بهترین مطالب گسترش دهید</p>
      <a href="posts.php" class="view-all">مشاهده همه <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z"/></svg></a>
    </div>
    <div class="posts-grid">
      <?php if (!empty($posts)): ?>
        <?php foreach ($posts as $idx => $post): ?>
          <?php $pcat = fetchOne($db, "SELECT title FROM categories WHERE id = ?", [$post['category_id']]); ?>
          <article class="post" data-aos="up" style="transition-delay:<?= $idx*80 ?>ms">
            <div class="post-img">
              <img src="./upload/posts/<?= escape($post['image']) ?>" alt="<?= escape($post['title']) ?>" loading="lazy">
              <span class="post-cat"><?= escape($pcat['title'] ?? 'نامشخص') ?></span>
              <span class="post-time">⏱ <?= rand(3,12) ?> دقیقه</span>
            </div>
            <div class="post-body">
              <h3 class="post-title"><a href="single.php?post=<?= $post['id'] ?>"><?= escape($post['title']) ?></a></h3>
              <p class="post-excerpt"><?= escape(truncateText($post['body'], 115)) ?></p>
              <div class="post-foot">
                <div class="post-author">
                  <div class="post-av"><?= mb_substr($post['author'],0,1) ?></div>
                  <?= escape($post['author']) ?>
                </div>
                <a href="single.php?post=<?= $post['id'] ?>" class="read-more">
                  ادامه <svg viewBox="0 0 24 24" fill="currentColor"><path d="M4,11V13H16L10.5,18.5L11.92,19.92L19.84,12L11.92,4.08L10.5,5.5L16,11H4Z"/></svg>
                </a>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      <?php else: ?><div class="no-data">مقاله‌ای یافت نشد.</div><?php endif; ?>
    </div>
  </section>

  <!-- ═════ NEWSLETTER ═════ -->
  <div class="nl" data-aos="zoom" style="margin-bottom:5rem">
    <div class="sec-label" style="justify-content:center">خبرنامه</div>
    <h2 class="nl-title">همیشه یک قدم جلوتر باش</h2>
    <p class="nl-sub">از جدیدترین کتاب‌ها، مقالات و تخفیف‌های ویژه باخبر شو</p>
    <div class="nl-form">
      <input class="nl-input" type="email" id="nlEmail" placeholder="ایمیل خود را وارد کنید">
      <button class="nl-btn" id="nlBtn">عضو می‌شوم</button>
    </div>
  </div>

  <!-- ═════ FEATURES ═════ -->
  <section class="sec" style="padding-top:0;margin-bottom:2rem">
    <div class="sec-head" data-aos="up">
      <div class="sec-label">چرا کتاب‌نت؟</div>
      <h2 class="sec-title">تجربه‌ای متفاوت از خرید</h2>
    </div>
    <div class="feats-grid">
      <?php $feats=[['🚚','ارسال رایگان','برای خریدهای بالای ۲۰۰ هزار تومان در سراسر ایران'],['🔒','پرداخت امن','درگاه پرداخت کاملاً ایمن و رمزگذاری‌شده'],['🎧','پشتیبانی ۲۴/۷','تیم ما همیشه آماده پاسخگویی است'],['🏆','کتاب‌های اصل','تضمین اصالت و کیفیت تمام محصولات'],['🔄','بازگشت آسان','۷ روز ضمانت بازگشت کالا']];
      foreach($feats as $i=>$f): ?>
      <div class="feat" data-aos="up" style="transition-delay:<?= $i*55 ?>ms">
        <span class="feat-ic"><?= $f[0] ?></span>
        <div class="feat-t"><?= $f[1] ?></div>
        <div class="feat-d"><?= $f[2] ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- ═════ TESTIMONIALS ═════ -->
  <section class="sec" style="padding-top:0;padding-bottom:5rem">
    <div class="sec-head" data-aos="up">
      <div class="sec-label">نظرات مشتریان</div>
      <h2 class="sec-title">آن‌ها چه می‌گویند</h2>
    </div>
    <div class="testi-wrap">
      <div class="testi-track" id="testiTrack">
        <?php $ts=[
          ['ع','علی احمدی','کتاب‌خوان حرفه‌ای','★★★★★','سایت فوق‌العاده‌ای! کتاب‌های با کیفیت، ارسال سریع و قیمت مناسب. پیدا کردن کتاب تا پرداخت فقط چند دقیقه طول کشید.'],
          ['س','سارا محمدی','نویسنده','★★★★★','بهترین تجربه خرید آنلاین کتاب! پشتیبانی عالی و تحویل به موقع. کاملاً راضیم.'],
          ['ر','رضا کریمی','دانشجو','★★★★☆','تنوع کتاب‌ها عالیه. هر چی دنبالش بودم پیدا کردم. قیمت‌ها هم رقابتیه.'],
          ['ن','نازنین رضایی','مدیر فروش','★★★★★','از بسته‌بندی تا تحویل همه چیز عالی بود. کتاب‌نت رو به همه توصیه می‌کنم.'],
        ];
        foreach($ts as $t): ?>
        <div class="tcard">
          <div class="tcard-stars"><?= $t[3] ?></div>
          <p class="tcard-txt">«<?= $t[4] ?>»</p>
          <div class="tcard-av"><?= $t[0] ?></div>
          <div class="tcard-name"><?= $t[1] ?></div>
          <div class="tcard-role"><?= $t[2] ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="testi-ctrl">
      <button class="t-btn" id="tPrev">‹</button>
      <div class="t-dots" id="tDots"></div>
      <button class="t-btn" id="tNext">›</button>
    </div>
  </section>

</div><!-- /idx -->

<div class="toast" id="toast"></div>

<?php require_once("./include/footer.php"); ?>

<script>
document.addEventListener('DOMContentLoaded',function(){

  /* AOS */
  var obs=new IntersectionObserver(function(entries){
    entries.forEach(function(e){if(e.isIntersecting)e.target.classList.add('on');});
  },{threshold:.1,rootMargin:'0px 0px -55px 0px'});
  document.querySelectorAll('[data-aos]').forEach(function(el){obs.observe(el);});

  /* Toast */
  var tEl=document.getElementById('toast');
  function toast(m){tEl.textContent=m;tEl.classList.add('show');setTimeout(function(){tEl.classList.remove('show');},2600);}

  /* Flash timer */
  var fe=new Date();fe.setHours(fe.getHours()+12);
  function flashTick(){
    var d=fe-Date.now();if(d<0)return;
    document.getElementById('fh').textContent=String(Math.floor(d/3600000)).padStart(2,'0');
    document.getElementById('fm').textContent=String(Math.floor(d/60000%60)).padStart(2,'0');
    document.getElementById('fs').textContent=String(Math.floor(d/1000%60)).padStart(2,'0');
  }
  flashTick();setInterval(flashTick,1000);

  /* Countdown */
  var ce=new Date();ce.setDate(ce.getDate()+7);
  function cdTick(){
    var d=ce-Date.now();if(d<0)return;
    document.getElementById('cd-d').textContent=String(Math.floor(d/86400000)).padStart(2,'0');
    document.getElementById('cd-h').textContent=String(Math.floor(d/3600000%24)).padStart(2,'0');
    document.getElementById('cd-m').textContent=String(Math.floor(d/60000%60)).padStart(2,'0');
    document.getElementById('cd-s').textContent=String(Math.floor(d/1000%60)).padStart(2,'0');
  }
  cdTick();setInterval(cdTick,1000);

  /* Testimonials */
  var track=document.getElementById('testiTrack');
  var dotsEl=document.getElementById('tDots');
  if(track){
    var n=track.children.length,cur=0;
    for(var i=0;i<n;i++){
      var d=document.createElement('button');
      d.className='t-dot'+(i===0?' on':'');
      (function(idx){d.onclick=function(){goT(idx);};})(i);
      dotsEl.appendChild(d);
    }
    function goT(idx){
      cur=(idx+n)%n;
      track.style.transform='translateX('+(cur*100)+'%)';
      dotsEl.querySelectorAll('.t-dot').forEach(function(dot,j){dot.classList.toggle('on',j===cur);});
    }
    document.getElementById('tNext').onclick=function(){goT(cur+1);};
    document.getElementById('tPrev').onclick=function(){goT(cur-1);};
    setInterval(function(){goT(cur+1);},5500);
  }

  /* Newsletter */
  var nlBtn=document.getElementById('nlBtn');
  if(nlBtn){
    nlBtn.addEventListener('click',function(){
      var v=document.getElementById('nlEmail').value||'';
      if(v&&v.includes('@')){toast('✅ عضویت با موفقیت ثبت شد!');document.getElementById('nlEmail').value='';}
      else{toast('⚠️ لطفاً ایمیل معتبر وارد کنید');}
    });
  }

});
</script>