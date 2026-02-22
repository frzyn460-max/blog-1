<?php
/**
 * صفحه محصولات کتاب‌نت
 * تم: Midnight Blue | بدون سایدبار | فیلتر پیشرفته
 */
require_once("./include/header.php");

// ── پارامترهای فیلتر ──
$category_id = isset($_GET['category']) ? filter_var($_GET['category'], FILTER_VALIDATE_INT) : null;
$search      = isset($_GET['search'])   ? trim($_GET['search'])   : '';
$sort        = isset($_GET['sort'])     ? $_GET['sort']           : 'newest';
$price_min   = isset($_GET['price_min']) ? (int)$_GET['price_min'] : 0;
$price_max   = isset($_GET['price_max']) ? (int)$_GET['price_max'] : 0;

$allowed_sorts = ['newest','oldest','price_asc','price_desc','discount'];
if (!in_array($sort, $allowed_sorts)) $sort = 'newest';

// ── دسته‌بندی‌های مجاز ──
$allowed_categories = [14,15,16,17,18];

// ── ساخت کوئری ──
if ($category_id && in_array($category_id, $allowed_categories)) {
    $query  = "SELECT * FROM product WHERE category_id = ?";
    $params = [$category_id];
} else {
    $query  = "SELECT * FROM product WHERE category_id IN (".implode(',',array_fill(0,count($allowed_categories),'?')).")";
    $params = $allowed_categories;
}

if ($search !== '') {
    $query .= " AND (name LIKE ? OR description LIKE ?)";
    $sp = "%{$search}%"; $params[] = $sp; $params[] = $sp;
}
if ($price_min > 0) { $query .= " AND CAST(`new-price` AS UNSIGNED) >= ?"; $params[] = $price_min; }
if ($price_max > 0) { $query .= " AND CAST(`new-price` AS UNSIGNED) <= ?"; $params[] = $price_max; }

$order_map = [
    'newest'     => 'id DESC',
    'oldest'     => 'id ASC',
    'price_asc'  => 'CAST(`new-price` AS UNSIGNED) ASC',
    'price_desc' => 'CAST(`new-price` AS UNSIGNED) DESC',
    'discount'   => '(CAST(price AS UNSIGNED)-CAST(`new-price` AS UNSIGNED)) DESC',
];
$query .= " ORDER BY ".$order_map[$sort];

$products   = fetchAll($db, $query, $params);
$categories = fetchAll($db, "SELECT * FROM categories WHERE id IN (".implode(',',$allowed_categories).")");

$total_count      = count($products);
$discounted_count = 0;
foreach ($products as $p) if ((int)$p['price']>(int)$p['new-price']) $discounted_count++;

function truncateText($t,$l=100){$t=strip_tags($t);return mb_strlen($t)>$l?mb_substr($t,0,$l).'...':$t;}
function formatPrice($p){return number_format($p).' تومان';}
function calcDisc($o,$n){return $o>0?round((($o-$n)/$o)*100):0;}

$cat_info = null;
if ($category_id) $cat_info = fetchOne($db,"SELECT title FROM categories WHERE id=?",[$category_id]);
?>

<link rel="stylesheet" href="./css/style.css">

<style>
/* ═══════════════════════════════════════════════
   products.php — Midnight Blue — Full Width
   ═══════════════════════════════════════════════ */
.pw{max-width:1400px;margin:0 auto;padding:0 1.5rem;}

/* ── PAGE HERO ── */
.ph{
  position:relative;min-height:auto;
  background:linear-gradient(135deg,#0f172a 0%,#1e3a8a 60%,#1e40af 100%);
  padding:9rem 1.5rem 5rem;
  overflow:hidden;text-align:center;
}
.ph::before{
  content:'';position:absolute;inset:0;
  background-image:radial-gradient(circle,rgba(255,255,255,.055) 1px,transparent 1px);
  background-size:30px 30px;pointer-events:none;
}
.ph::after{
  content:'';position:absolute;
  width:700px;height:700px;border-radius:50%;
  background:radial-gradient(circle,rgba(96,165,250,.15) 0%,transparent 65%);
  top:-200px;right:-150px;pointer-events:none;
}
.ph-in{position:relative;z-index:2;}
.ph-ey{
  display:inline-flex;align-items:center;gap:.6rem;
  font-size:.72rem;letter-spacing:.2em;text-transform:uppercase;
  color:#93c5fd;font-weight:700;margin-bottom:1.2rem;
}
.ph-ey span{width:24px;height:1px;background:#93c5fd;display:inline-block;}
.ph-t{
  font-size:clamp(2rem,4vw,3.4rem);
  font-weight:900;color:#fff;letter-spacing:-.02em;line-height:1.12;margin-bottom:1rem;
}
.ph-t em{
  font-style:normal;
  background:linear-gradient(135deg,#60a5fa,#a78bfa);
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
}
.ph-sub{color:rgba(255,255,255,.55);font-size:.98rem;margin-bottom:2.2rem;}
.ph-stats{
  display:inline-flex;gap:2.5rem;flex-wrap:wrap;justify-content:center;
  background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);
  border-radius:18px;padding:.9rem 2.2rem;backdrop-filter:blur(8px);
}
.ph-st strong{display:block;font-size:1.5rem;font-weight:900;color:#60a5fa;line-height:1;}
.ph-st span{font-size:.7rem;color:rgba(255,255,255,.4);letter-spacing:.08em;text-transform:uppercase;}

/* ── STICKY SEARCH ── */
.prd-sb{
  background:var(--bg-primary);
  border-bottom:1px solid var(--border-color);
  padding:1.4rem 1.5rem;
  position:sticky;top:72px;z-index:100;
  box-shadow:var(--shadow-md);
  border-radius: 0 0 12px 12px;
}
.prd-sb-in{
  max-width:1400px;margin:0 auto;
  display:flex;gap:1rem;align-items:center;flex-wrap:wrap;
}
.prd-sbox{flex:1;position:relative;min-width:240px;}
.prd-sinput{
  width:100%;
  padding:.85rem 1.2rem .85rem 3rem;
  border:1.5px solid var(--border-color);border-radius:14px;
  font-size:.95rem;font-family:inherit;
  background:var(--bg-secondary);color:var(--text-primary);
  transition:all .3s;outline:none;
}
.prd-sinput:focus{border-color:var(--accent-primary);box-shadow:0 0 0 3px rgba(37,99,235,.12);background:var(--bg-primary);}
.prd-sicon{position:absolute;left:.9rem;top:50%;transform:translateY(-50%);width:18px;height:18px;color:var(--text-secondary);pointer-events:none;}
.prd-sbtn{
  padding:.85rem 1.7rem;
  background:linear-gradient(135deg,var(--accent-primary,#2563eb),var(--accent-hover,#1d4ed8));
  color:#fff;border:none;border-radius:14px;
  font-weight:700;font-size:.9rem;font-family:inherit;
  cursor:pointer;transition:all .3s;display:flex;align-items:center;gap:.5rem;white-space:nowrap;
}
.prd-sbtn:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(37,99,235,.3);}
.prd-sbtn svg{width:17px;height:17px;}
.prd-rc{font-size:.83rem;color:var(--text-secondary);white-space:nowrap;padding:0 .4rem;}
.prd-rc strong{color:var(--accent-primary);}

/* mobile filter btn */
.prd-mob-btn{
  display:none;align-items:center;gap:.5rem;
  padding:.75rem 1.3rem;border-radius:12px;
  border:1.5px solid var(--border-color);
  background:var(--bg-primary);color:var(--text-primary);
  font-family:inherit;font-size:.88rem;font-weight:700;
  cursor:pointer;transition:all .3s;white-space:nowrap;
}
.prd-mob-btn svg{width:17px;height:17px;}
.prd-mob-btn:hover{border-color:var(--accent-primary);color:var(--accent-primary);}
.prd-mob-badge{
  background:var(--accent-primary);color:#fff;border-radius:50%;
  width:18px;height:18px;font-size:.68rem;display:flex;align-items:center;justify-content:center;font-weight:800;
}

/* ── MAIN ── */
.prd-main{padding:3rem 0 5rem;}

/* ── FILTERS ROW ── */
.prd-fr{display:flex;gap:1.5rem;align-items:flex-start;flex-wrap:wrap;margin-bottom:2.5rem;}

/* cats */
.prd-cats-w{flex:1;min-width:0;}
.prd-flbl{font-size:.68rem;letter-spacing:.16em;text-transform:uppercase;color:var(--text-secondary);font-weight:700;margin-bottom:.7rem;display:block;}
.prd-cats{display:flex;gap:.6rem;flex-wrap:wrap;}
.prd-cat{
  display:inline-flex;align-items:center;gap:.4rem;
  padding:.5rem 1.1rem;border-radius:50px;
  border:1.5px solid var(--border-color);
  background:transparent;color:var(--text-secondary);
  font-family:inherit;font-size:.83rem;font-weight:600;
  cursor:pointer;transition:all .3s;text-decoration:none;white-space:nowrap;
}
.prd-cat:hover{border-color:var(--accent-primary);color:var(--accent-primary);}
.prd-cat.on{background:var(--accent-primary);border-color:var(--accent-primary);color:#fff;box-shadow:0 4px 14px rgba(37,99,235,.25);}
.prd-cat .cc{
  background:rgba(255,255,255,.25);border-radius:20px;
  padding:.08rem .42rem;font-size:.7rem;font-weight:700;line-height:1.4;
}
.prd-cat:not(.on) .cc{background:var(--bg-secondary);color:var(--text-secondary);}

/* controls */
.prd-ctrl{display:flex;gap:.75rem;align-items:flex-end;flex-wrap:wrap;}
.prd-cg{display:flex;flex-direction:column;gap:.45rem;}

.prd-sel{
  padding:.62rem 2.4rem .62rem 1rem;
  border:1.5px solid var(--border-color);border-radius:12px;
  background:var(--bg-primary);color:var(--text-primary);
  font-family:inherit;font-size:.86rem;font-weight:600;
  cursor:pointer;outline:none;transition:all .3s;
  appearance:none;-webkit-appearance:none;
  background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='%2364748b'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
  background-repeat:no-repeat;background-position:left .6rem center;
}
.prd-sel:focus{border-color:var(--accent-primary);box-shadow:0 0 0 3px rgba(37,99,235,.1);}

.prd-prow{display:flex;gap:.5rem;align-items:center;}
.prd-pin{
  width:120px;padding:.62rem .9rem;
  border:1.5px solid var(--border-color);border-radius:12px;
  background:var(--bg-primary);color:var(--text-primary);
  font-family:inherit;font-size:.82rem;outline:none;transition:all .3s;
}
.prd-pin:focus{border-color:var(--accent-primary);box-shadow:0 0 0 3px rgba(37,99,235,.1);}
.prd-psep{color:var(--text-secondary);font-size:.84rem;}
.prd-papply{
  padding:.62rem 1.1rem;border-radius:12px;
  background:linear-gradient(135deg,var(--accent-primary,#2563eb),var(--accent-hover,#1d4ed8));
  color:#fff;border:none;cursor:pointer;
  font-family:inherit;font-size:.82rem;font-weight:700;transition:all .3s;white-space:nowrap;
}
.prd-papply:hover{transform:translateY(-1px);box-shadow:0 6px 16px rgba(37,99,235,.25);}

.prd-clr{
  padding:.62rem 1.1rem;border-radius:12px;
  border:1.5px solid var(--border-color);background:transparent;
  color:var(--text-secondary);font-family:inherit;font-size:.82rem;font-weight:600;
  cursor:pointer;transition:all .3s;display:flex;align-items:center;gap:.4rem;
  white-space:nowrap;align-self:flex-end;
}
.prd-clr:hover{border-color:#ef4444;color:#ef4444;}
.prd-clr svg{width:13px;height:13px;}

/* active tags */
.prd-atags{display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:2rem;}
.prd-atag{
  display:inline-flex;align-items:center;gap:.4rem;
  background:rgba(37,99,235,.1);border:1px solid rgba(37,99,235,.22);
  color:var(--accent-primary);padding:.32rem .8rem;border-radius:50px;
  font-size:.78rem;font-weight:600;
}
.prd-atag button{background:none;border:none;cursor:pointer;color:var(--accent-primary);padding:0;display:flex;line-height:1;opacity:.7;transition:opacity .2s;}
.prd-atag button:hover{opacity:1;}
.prd-atag button svg{width:12px;height:12px;}

/* top bar */
.prd-topbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:1.8rem;flex-wrap:wrap;gap:1rem;}
.prd-tinfo{font-size:.87rem;color:var(--text-secondary);}
.prd-tinfo strong{color:var(--text-primary);}
.prd-tinfo .disc-count{color:#10b981;font-weight:700;}
.prd-vbtns{display:flex;gap:.4rem;}
.prd-vbtn{
  width:36px;height:36px;border-radius:10px;
  border:1.5px solid var(--border-color);background:var(--bg-primary);
  color:var(--text-secondary);display:flex;align-items:center;justify-content:center;
  cursor:pointer;transition:all .3s;
}
.prd-vbtn:hover,.prd-vbtn.on{background:var(--accent-primary);border-color:var(--accent-primary);color:#fff;}
.prd-vbtn svg{width:16px;height:16px;}

/* ── GRID ── */
.prd-grid{
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(255px,1fr));
  gap:2rem;transition:all .3s;
}
.prd-grid.lv{grid-template-columns:1fr;gap:1.25rem;}

/* card */
.prd-card{
  background:var(--bg-primary);border-radius:20px;overflow:hidden;
  border:1px solid var(--border-color);
  transition:all .4s cubic-bezier(.4,0,.2,1);
  display:flex;flex-direction:column;
  animation:cIn .45s ease both;
}
@keyframes cIn{from{opacity:0;transform:translateY(22px)}to{opacity:1;transform:translateY(0)}}
.prd-card:hover{transform:translateY(-10px);box-shadow:var(--shadow-lg);}

/* list card */
.lv .prd-card{flex-direction:row;border-radius:16px;}
.lv .prd-card:hover{transform:translateY(-4px);}
.lv .prd-ciw{width:190px;min-height:190px;padding-top:0;flex-shrink:0;}
.lv .prd-ciw img{position:static;width:100%;height:100%;object-fit:cover;}

.prd-ciw{position:relative;padding-top:100%;overflow:hidden;}
.prd-cimg{position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover;transition:transform .5s;}
.prd-card:hover .prd-cimg{transform:scale(1.08);}

/* badges */
.prd-cbadges{position:absolute;top:12px;right:12px;display:flex;flex-direction:column;gap:5px;z-index:3;}
.prd-bd{background:linear-gradient(135deg,#ef4444,#f97316);color:#fff;padding:.28rem .72rem;border-radius:7px;font-size:.74rem;font-weight:800;}
.prd-bh{background:linear-gradient(135deg,#8b5cf6,#6d28d9);color:#fff;padding:.28rem .72rem;border-radius:7px;font-size:.7rem;font-weight:700;}
.prd-bl{background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;padding:.28rem .72rem;border-radius:7px;font-size:.7rem;font-weight:700;}

/* wish */
.prd-wl{
  position:absolute;top:12px;left:12px;z-index:3;
  width:35px;height:35px;border-radius:50%;
  background:rgba(255,255,255,.92);border:none;cursor:pointer;
  display:flex;align-items:center;justify-content:center;
  transition:all .3s;color:#bbb;box-shadow:0 2px 10px rgba(0,0,0,.14);
}
.prd-wl:hover,.prd-wl.on{color:#ef4444;transform:scale(1.15);}
.prd-wl svg{width:16px;height:16px;}

/* overlay */
.prd-cov{
  position:absolute;inset:0;background:rgba(0,0,0,.68);
  display:flex;align-items:center;justify-content:center;
  opacity:0;transition:opacity .3s;
}
.prd-card:hover .prd-cov{opacity:1;}
.prd-qv{
  background:#fff;color:var(--accent-primary);
  padding:.72rem 1.4rem;border-radius:12px;
  font-weight:700;text-decoration:none;font-size:.86rem;
  transform:translateY(18px);transition:transform .3s ease;
}
.prd-card:hover .prd-qv{transform:translateY(0);}

/* card body */
.prd-cb{padding:1.4rem;display:flex;flex-direction:column;flex:1;}
.prd-cc{
  display:inline-block;
  background:rgba(37,99,235,.1);color:var(--accent-primary);
  padding:.26rem .72rem;border-radius:7px;font-size:.7rem;font-weight:700;
  margin-bottom:.6rem;letter-spacing:.05em;
}
.prd-cn{font-size:1.02rem;font-weight:700;margin-bottom:.5rem;line-height:1.4;flex:1;}
.prd-cn a{color:var(--text-primary);text-decoration:none;transition:color .3s;}
.prd-cn a:hover{color:var(--accent-primary);}
.prd-cstars{color:#f59e0b;font-size:.84rem;letter-spacing:1px;margin-bottom:.85rem;}
.prd-cstars em{color:var(--text-secondary);font-style:normal;font-size:.74rem;margin-right:.3rem;}

.prd-cstk{margin-bottom:.9rem;}
.prd-csb{height:4px;background:var(--bg-secondary);border-radius:99px;overflow:hidden;margin-bottom:.3rem;}
.prd-csf{height:100%;background:linear-gradient(90deg,#10b981,#34d399);border-radius:99px;}
.prd-cst{font-size:.7rem;color:var(--text-secondary);}

.prd-cpr{display:flex;align-items:baseline;gap:.75rem;margin-bottom:1.1rem;flex-wrap:wrap;}
.prd-cold{font-size:.84rem;color:var(--text-secondary);text-decoration:line-through;}
.prd-cnew{font-size:1.22rem;font-weight:900;color:#10b981;}

.prd-cbtn{
  width:100%;padding:.9rem;
  background:linear-gradient(135deg,var(--accent-primary,#2563eb),var(--accent-hover,#1d4ed8));
  color:#fff;border:none;border-radius:13px;
  font-weight:700;font-size:.86rem;font-family:inherit;
  display:flex;align-items:center;justify-content:center;gap:.5rem;
  cursor:pointer;transition:all .3s;text-decoration:none;
}
.prd-cbtn:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(37,99,235,.3);}
.prd-cbtn svg{width:16px;height:16px;}

/* no results */
.prd-nores{
  text-align:center;padding:6rem 2rem;
  background:var(--bg-primary);border-radius:24px;
  border:2px dashed var(--border-color);grid-column:1/-1;
}
.prd-nores-icon{font-size:5rem;margin-bottom:1.5rem;display:block;opacity:.45;}
.prd-nores h3{font-size:1.5rem;font-weight:800;color:var(--text-primary);margin-bottom:.5rem;}
.prd-nores p{color:var(--text-secondary);margin-bottom:2rem;}
.prd-nores a{
  display:inline-flex;align-items:center;gap:.5rem;
  padding:.95rem 2rem;border-radius:14px;
  background:var(--accent-primary);color:#fff;
  font-weight:700;text-decoration:none;transition:all .3s;
}
.prd-nores a:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(37,99,235,.3);}
.prd-nores a svg{width:17px;height:17px;}

/* toast */
.prd-toast{
  position:fixed;bottom:28px;right:28px;
  background:var(--bg-primary);color:var(--text-primary);
  border:1px solid var(--border-color);
  padding:.82rem 1.6rem;border-radius:14px;
  font-size:.86rem;font-weight:600;box-shadow:var(--shadow-lg);
  z-index:9999;transform:translateY(80px);opacity:0;
  transition:all .35s cubic-bezier(.4,0,.2,1);pointer-events:none;
}
.prd-toast.show{transform:translateY(0);opacity:1;}

/* ── MOBILE DRAWER ── */
.prd-drawer{position:fixed;inset:0;z-index:1000;pointer-events:none;}
.prd-drawer-bg{position:absolute;inset:0;background:rgba(0,0,0,.5);opacity:0;transition:opacity .3s;pointer-events:none;}
.prd-drawer-p{
  position:absolute;bottom:0;left:0;right:0;
  background:var(--bg-primary);
  border-radius:24px 24px 0 0;
  padding:1.8rem 1.5rem 3rem;
  transform:translateY(100%);transition:transform .4s cubic-bezier(.4,0,.2,1);
  max-height:85vh;overflow-y:auto;
}
.prd-drawer.open{pointer-events:all;}
.prd-drawer.open .prd-drawer-bg{opacity:1;pointer-events:all;}
.prd-drawer.open .prd-drawer-p{transform:translateY(0);}
.prd-dhandle{width:44px;height:5px;border-radius:99px;background:var(--border-color);margin:0 auto 1.5rem;}
.prd-dtitle{font-size:1.1rem;font-weight:800;color:var(--text-primary);margin-bottom:1.5rem;}
.prd-dsec{margin-bottom:1.8rem;}
.prd-dstitle{font-size:.68rem;letter-spacing:.16em;text-transform:uppercase;color:var(--text-secondary);font-weight:700;margin-bottom:.85rem;}

/* AOS */
[data-aos]{opacity:0;transition-property:transform,opacity;transition-duration:.6s;transition-timing-function:cubic-bezier(.4,0,.2,1);}
[data-aos].aos-animate{opacity:1;}
[data-aos="fade-up"]{transform:translateY(28px);}[data-aos="fade-up"].aos-animate{transform:translateY(0);}
[data-aos="zoom-in"]{transform:scale(.94);}[data-aos="zoom-in"].aos-animate{transform:scale(1);}

/* ── RESPONSIVE ── */
@media(max-width:1024px){.prd-fr{flex-direction:column;}.prd-ctrl{width:100%;}}
@media(max-width:768px){
  .ph{padding:7.5rem 1.25rem 3.5rem;}
  .prd-sb{padding:1.1rem 1.25rem;top:64px;}
  .prd-sb-in{flex-direction:column;align-items:stretch;}
  .prd-sbox{min-width:0;}
  .pw{padding:0 1.25rem;}
  .prd-main{padding:2rem 0 4rem;}
  .prd-fr,.prd-ctrl{display:none;}
  .prd-mob-btn{display:flex;}
  .prd-grid{grid-template-columns:repeat(2,1fr);gap:1.25rem;}
  .lv.prd-grid{grid-template-columns:1fr;}
  .lv .prd-ciw{width:150px;min-height:160px;}
}
@media(max-width:576px){
  .ph-t{font-size:1.9rem;}
  .ph-stats{gap:1.5rem;padding:.8rem 1.4rem;}
  .prd-grid{grid-template-columns:repeat(2,1fr);gap:1rem;}
  .prd-topbar{flex-direction:column;align-items:flex-start;}
  .prd-rc{white-space:normal;}
}
@media(max-width:420px){
  .prd-grid{grid-template-columns:1fr;}
  .pw{padding:0 1rem;}
  .prd-sb{padding:.9rem 1rem;}
}
</style>

<!-- PAGE HERO -->
<section class="ph">
  <div class="ph-in" data-aos="fade-up">
    <div class="ph-ey"><span></span> فروشگاه کتاب‌نت <span></span></div>
    <h1 class="ph-t">
      <?php if($search!==''): ?>
        نتایج جستجو برای <em>«<?=escape($search)?>»</em>
      <?php elseif($category_id&&$cat_info): ?>
        کتاب‌های <em><?=escape($cat_info['title'])?></em>
      <?php else: ?>
        همه <em>محصولات</em>
      <?php endif; ?>
    </h1>
    <p class="ph-sub">بهترین کتاب‌ها با قیمت مناسب، ارسال سریع و ضمانت اصالت کالا</p>
    <div class="ph-stats">
      <div class="ph-st"><strong><?=$total_count?></strong><span>محصول موجود</span></div>
      <div class="ph-st"><strong><?=$discounted_count?></strong><span>دارای تخفیف</span></div>
      <div class="ph-st"><strong>24/7</strong><span>پشتیبانی</span></div>
    </div>
  </div>
</section>

<!-- STICKY SEARCH -->
<div class="prd-sb">
  <form class="prd-sb-in" method="GET" action="products.php" id="searchForm">
    <?php if($category_id): ?><input type="hidden" name="category" value="<?=$category_id?>"> <?php endif; ?>
    <input type="hidden" name="sort"      value="<?=escape($sort)?>">
    <input type="hidden" name="price_min" value="<?=$price_min?>">
    <input type="hidden" name="price_max" value="<?=$price_max?>">

    <div class="prd-sbox">
      <svg class="prd-sicon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
      </svg>
      <input class="prd-sinput" type="text" name="search"
             value="<?=escape($search)?>"
             placeholder="جستجو در محصولات..."
             autocomplete="off" id="mainSearch">
    </div>

    <button type="submit" class="prd-sbtn">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
      </svg>
      جستجو
    </button>

    <button type="button" class="prd-mob-btn" id="mobileFilterBtn">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="11" y1="18" x2="13" y2="18"/>
      </svg>
      فیلترها
      <?php if($category_id||$price_min||$price_max||$sort!=='newest'): ?>
        <span class="prd-mob-badge">!</span>
      <?php endif; ?>
    </button>

    <span class="prd-rc"><strong><?=$total_count?></strong> محصول یافت شد</span>
  </form>
</div>

<div class="pw">
<div class="prd-main">

  <!-- FILTER ROW -->
  <div class="prd-fr" data-aos="fade-up">
    <!-- دسته‌بندی‌ها -->
    <div class="prd-cats-w">
      <span class="prd-flbl">دسته‌بندی</span>
      <div class="prd-cats">
        <a href="products.php?sort=<?=escape($sort)?>&search=<?=urlencode($search)?>&price_min=<?=$price_min?>&price_max=<?=$price_max?>"
           class="prd-cat <?=!$category_id?'on':''?>">همه</a>
        <?php foreach($categories as $cat):
          $cc=fetchOne($db,"SELECT COUNT(*) as c FROM product WHERE category_id=?",[$cat['id']])['c']??0;
        ?>
        <a href="products.php?category=<?=$cat['id']?>&sort=<?=escape($sort)?>&search=<?=urlencode($search)?>&price_min=<?=$price_min?>&price_max=<?=$price_max?>"
           class="prd-cat <?=$category_id==$cat['id']?'on':''?>">
          <?=escape($cat['title'])?>
          <span class="cc"><?=$cc?></span>
        </a>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- کنترل‌ها -->
    <div class="prd-ctrl">
      <div class="prd-cg">
        <span class="prd-flbl">مرتب‌سازی</span>
        <select class="prd-sel" id="sortSel">
          <option value="newest"     <?=$sort==='newest'    ?'selected':''?>>جدیدترین</option>
          <option value="oldest"     <?=$sort==='oldest'    ?'selected':''?>>قدیمی‌ترین</option>
          <option value="price_asc"  <?=$sort==='price_asc' ?'selected':''?>>ارزان‌ترین</option>
          <option value="price_desc" <?=$sort==='price_desc'?'selected':''?>>گران‌ترین</option>
          <option value="discount"   <?=$sort==='discount'  ?'selected':''?>>بیشترین تخفیف</option>
        </select>
      </div>
      <div class="prd-cg">
        <span class="prd-flbl">محدوده قیمت (تومان)</span>
        <div class="prd-prow">
          <input class="prd-pin" type="number" id="pMin" placeholder="از" value="<?=$price_min?:''?>" min="0">
          <span class="prd-psep">تا</span>
          <input class="prd-pin" type="number" id="pMax" placeholder="تا" value="<?=$price_max?:''?>" min="0">
          <button type="button" class="prd-papply" id="applyPrice">اعمال</button>
        </div>
      </div>
      <?php if($category_id||$price_min||$price_max||$sort!=='newest'||$search!==''): ?>
      <button type="button" class="prd-clr" id="clearFilters">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        پاک‌سازی
      </button>
      <?php endif; ?>
    </div>
  </div>

  <!-- ACTIVE TAGS -->
  <div class="prd-atags">
    <?php if($search!==''): ?>
    <span class="prd-atag">🔍 «<?=escape($search)?>»
      <button onclick="rmFilter('search')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </span>
    <?php endif; ?>
    <?php if($category_id&&$cat_info): ?>
    <span class="prd-atag">📂 <?=escape($cat_info['title'])?>
      <button onclick="rmFilter('category')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </span>
    <?php endif; ?>
    <?php if($price_min>0): ?>
    <span class="prd-atag">💰 از <?=formatPrice($price_min)?>
      <button onclick="rmFilter('price_min')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </span>
    <?php endif; ?>
    <?php if($price_max>0): ?>
    <span class="prd-atag">💰 تا <?=formatPrice($price_max)?>
      <button onclick="rmFilter('price_max')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </span>
    <?php endif; ?>
    <?php if($sort!=='newest'):
      $sl=['oldest'=>'قدیمی‌ترین','price_asc'=>'ارزان‌ترین','price_desc'=>'گران‌ترین','discount'=>'بیشترین تخفیف'];
    ?>
    <span class="prd-atag">↕️ <?=$sl[$sort]??$sort?>
      <button onclick="rmFilter('sort')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </span>
    <?php endif; ?>
  </div>

  <!-- TOP BAR -->
  <div class="prd-topbar">
    <div class="prd-tinfo">
      نمایش <strong><?=$total_count?></strong> محصول
      <?php if($discounted_count>0): ?>
        · <span class="disc-count"><?=$discounted_count?> عدد تخفیف‌دار</span>
      <?php endif; ?>
    </div>
    <div class="prd-vbtns">
      <button class="prd-vbtn on" id="vGrid" title="شبکه‌ای" onclick="setView('grid')">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M3,3H10V10H3V3M13,3H21V10H13V3M3,13H10V21H3V13M13,13H21V21H13V13Z"/></svg>
      </button>
      <button class="prd-vbtn" id="vList" title="لیستی" onclick="setView('list')">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9,5V9H21V5M9,19H21V15H9M9,14H21V10H9M4,9H8V5H4M4,19H8V15H4M4,14H8V10H4V14Z"/></svg>
      </button>
    </div>
  </div>

  <!-- PRODUCTS GRID -->
  <div class="prd-grid" id="pGrid">
    <?php if(!empty($products)): ?>
      <?php foreach($products as $i=>$p):
        $pcat=fetchOne($db,"SELECT title FROM categories WHERE id=?",[$p['category_id']]);
        $d=calcDisc((int)$p['price'],(int)$p['new-price']);
        $stk=(int)$p['number'];
        $sp=min(100,($stk/20)*100);
        $hot=$i<3;
      ?>
      <article class="prd-card" style="animation-delay:<?=min($i*55,500)?>ms">
        <div class="prd-ciw">
          <img class="prd-cimg"
               src="./upload/products/<?=escape($p['pic'])?>"
               alt="<?=escape($p['name'])?>"
               loading="lazy">
          <div class="prd-cbadges">
            <?php if($d>0): ?><span class="prd-bd"><?=$d?>% تخفیف</span><?php endif; ?>
            <?php if($hot): ?><span class="prd-bh">🔥 پرفروش</span><?php endif; ?>
            <?php if($stk>0&&$stk<=5): ?><span class="prd-bl">آخرین موجودی</span><?php endif; ?>
          </div>
          <button class="prd-wl" data-id="<?=$p['id']?>" aria-label="علاقه‌مندی">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
          </button>
          <div class="prd-cov">
            <a href="single_product.php?product=<?=$p['id']?>" class="prd-qv">مشاهده سریع</a>
          </div>
        </div>
        <div class="prd-cb">
          <span class="prd-cc"><?=escape($pcat['title']??'نامشخص')?></span>
          <h3 class="prd-cn"><a href="single_product.php?product=<?=$p['id']?>"><?=escape($p['name'])?></a></h3>
          <div class="prd-cstars">★★★★<?=rand(0,1)?'★':'☆'?> <em>(<?=rand(12,98)?>)</em></div>
          <?php if($stk>0): ?>
            <div class="prd-cstk">
              <div class="prd-csb"><div class="prd-csf" style="width:<?=$sp?>%"></div></div>
              <div class="prd-cst">موجودی: <?=$stk?> عدد</div>
            </div>
          <?php else: ?>
            <div class="prd-cst" style="color:#ef4444;margin-bottom:.9rem;">ناموجود</div>
          <?php endif; ?>
          <div class="prd-cpr">
            <?php if((int)$p['price']!=(int)$p['new-price']): ?>
              <span class="prd-cold"><?=formatPrice((int)$p['price'])?></span>
            <?php endif; ?>
            <span class="prd-cnew"><?=formatPrice((int)$p['new-price'])?></span>
          </div>
          <a href="single_product.php?product=<?=$p['id']?>" class="prd-cbtn">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17,18A2,2 0 0,1 19,20A2,2 0 0,1 17,22C15.89,22 15,21.1 15,20C15,18.89 15.89,18 17,18M1,2H4.27L5.21,4H20A1,1 0 0,1 21,5L17.3,11.97C16.96,12.58 16.3,13 15.55,13H8.1L7.2,14.63L7.17,14.75A0.25,0.25 0 0,0 7.42,15H19V17H7C5.89,17 5,16.1 5,15L6.6,11.59L3,4H1V2Z"/></svg>
            <?=$stk>0?'افزودن به سبد':'اطلاع‌رسانی موجودی'?>
          </a>
        </div>
      </article>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="prd-nores">
        <span class="prd-nores-icon">📭</span>
        <h3>محصولی یافت نشد!</h3>
        <p><?=$search!==''?"جستجوی «".escape($search)."» نتیجه‌ای نداشت.":'با این فیلترها محصولی وجود ندارد.'?></p>
        <a href="products.php">
          <svg viewBox="0 0 24 24" fill="currentColor"><path d="M10,20V14H14V20H19V12H22L12,3L2,12H5V20H10Z"/></svg>
          بازگشت به همه
        </a>
      </div>
    <?php endif; ?>
  </div>

</div>
</div>

<!-- MOBILE DRAWER -->
<div class="prd-drawer" id="filterDrawer">
  <div class="prd-drawer-bg" id="drawerBg"></div>
  <div class="prd-drawer-p">
    <div class="prd-dhandle"></div>
    <div class="prd-dtitle">فیلترها و مرتب‌سازی</div>

    <div class="prd-dsec">
      <div class="prd-dstitle">دسته‌بندی</div>
      <div class="prd-cats" style="flex-wrap:wrap">
        <a href="products.php?sort=<?=escape($sort)?>&search=<?=urlencode($search)?>"
           class="prd-cat <?=!$category_id?'on':''?>">همه</a>
        <?php foreach($categories as $cat): ?>
        <a href="products.php?category=<?=$cat['id']?>&sort=<?=escape($sort)?>&search=<?=urlencode($search)?>"
           class="prd-cat <?=$category_id==$cat['id']?'on':''?>"><?=escape($cat['title'])?></a>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="prd-dsec">
      <div class="prd-dstitle">مرتب‌سازی</div>
      <select class="prd-sel" style="width:100%" id="drawerSort">
        <option value="newest"     <?=$sort==='newest'    ?'selected':''?>>جدیدترین</option>
        <option value="oldest"     <?=$sort==='oldest'    ?'selected':''?>>قدیمی‌ترین</option>
        <option value="price_asc"  <?=$sort==='price_asc' ?'selected':''?>>ارزان‌ترین</option>
        <option value="price_desc" <?=$sort==='price_desc'?'selected':''?>>گران‌ترین</option>
        <option value="discount"   <?=$sort==='discount'  ?'selected':''?>>بیشترین تخفیف</option>
      </select>
    </div>

    <div class="prd-dsec">
      <div class="prd-dstitle">محدوده قیمت (تومان)</div>
      <div class="prd-prow" style="flex-wrap:wrap">
        <input class="prd-pin" style="flex:1;min-width:100px" type="number" id="dPMin" placeholder="از" value="<?=$price_min?:''?>">
        <span class="prd-psep">تا</span>
        <input class="prd-pin" style="flex:1;min-width:100px" type="number" id="dPMax" placeholder="تا" value="<?=$price_max?:''?>">
      </div>
    </div>

    <button type="button" class="prd-sbtn" style="width:100%;justify-content:center;margin-top:.75rem" id="drawerApply">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20,6 9,17 4,12"/></svg>
      اعمال فیلترها
    </button>
    <button type="button" class="prd-clr" style="width:100%;justify-content:center;margin-top:.6rem" onclick="location='products.php'">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      پاک‌سازی همه
    </button>
  </div>
</div>

<div class="prd-toast" id="prdToast"></div>

<?php require_once("./include/footer.php"); ?>

<script>
document.addEventListener('DOMContentLoaded',function(){

  /* AOS */
  var ao=new IntersectionObserver(function(es){es.forEach(function(e){if(e.isIntersecting)e.target.classList.add('aos-animate');});},{threshold:.1,rootMargin:'0px 0px -60px 0px'});
  document.querySelectorAll('[data-aos]').forEach(function(el){ao.observe(el);});

  /* Toast */
  var toast=document.getElementById('prdToast');
  function showToast(m){toast.textContent=m;toast.classList.add('show');setTimeout(function(){toast.classList.remove('show');},2500);}

  /* View */
  var grid=document.getElementById('pGrid');
  function setView(v){
    if(v==='list'){grid.classList.add('lv');document.getElementById('vList').classList.add('on');document.getElementById('vGrid').classList.remove('on');localStorage.setItem('pv','list');}
    else{grid.classList.remove('lv');document.getElementById('vGrid').classList.add('on');document.getElementById('vList').classList.remove('on');localStorage.setItem('pv','grid');}
  }
  window.setView=setView;
  if(localStorage.getItem('pv')==='list')setView('list');

  /* Sort */
  var ss=document.getElementById('sortSel');
  if(ss)ss.addEventListener('change',function(){var u=new URL(window.location.href);u.searchParams.set('sort',this.value);window.location=u.toString();});

  /* Price */
  function applyPrice(mn,mx){
    var u=new URL(window.location.href);
    var a=document.getElementById(mn).value,b=document.getElementById(mx).value;
    a?u.searchParams.set('price_min',a):u.searchParams.delete('price_min');
    b?u.searchParams.set('price_max',b):u.searchParams.delete('price_max');
    window.location=u.toString();
  }
  var ap=document.getElementById('applyPrice');
  if(ap)ap.addEventListener('click',function(){applyPrice('pMin','pMax');});
  ['pMin','pMax'].forEach(function(id){var el=document.getElementById(id);if(el)el.addEventListener('keydown',function(e){if(e.key==='Enter')applyPrice('pMin','pMax');});});

  /* Clear */
  var cl=document.getElementById('clearFilters');
  if(cl)cl.addEventListener('click',function(){window.location='products.php';});

  /* Remove filter tag */
  window.rmFilter=function(k){
    var u=new URL(window.location.href);
    if(k==='sort')u.searchParams.set('sort','newest');else u.searchParams.delete(k);
    window.location=u.toString();
  };

  /* Wishlist */
  document.querySelectorAll('.prd-wl').forEach(function(btn){
    var k='wish_'+btn.dataset.id;
    if(localStorage.getItem(k)){btn.classList.add('on');btn.querySelector('svg').setAttribute('fill','#ef4444');}
    btn.addEventListener('click',function(e){
      e.preventDefault();e.stopPropagation();
      btn.classList.toggle('on');
      var on=btn.classList.contains('on');
      btn.querySelector('svg').setAttribute('fill',on?'#ef4444':'none');
      on?localStorage.setItem(k,'1'):localStorage.removeItem(k);
      showToast(on?'❤️ به علاقه‌مندی‌ها اضافه شد':'🤍 از علاقه‌مندی‌ها حذف شد');
    });
  });

  /* Drawer */
  var drawer=document.getElementById('filterDrawer');
  var dbg=document.getElementById('drawerBg');
  var mob=document.getElementById('mobileFilterBtn');
  function openD(){drawer.classList.add('open');document.body.style.overflow='hidden';}
  function closeD(){drawer.classList.remove('open');document.body.style.overflow='';}
  if(mob)mob.addEventListener('click',openD);
  if(dbg)dbg.addEventListener('click',closeD);

  var da=document.getElementById('drawerApply');
  if(da)da.addEventListener('click',function(){
    var u=new URL(window.location.href);
    var s=document.getElementById('drawerSort').value;
    var a=document.getElementById('dPMin').value,b=document.getElementById('dPMax').value;
    u.searchParams.set('sort',s);
    a?u.searchParams.set('price_min',a):u.searchParams.delete('price_min');
    b?u.searchParams.set('price_max',b):u.searchParams.delete('price_max');
    window.location=u.toString();
  });

  /* swipe down to close */
  var panel=drawer.querySelector('.prd-drawer-p'),sy=0;
  if(panel){
    panel.addEventListener('touchstart',function(e){sy=e.touches[0].clientY;},{passive:true});
    panel.addEventListener('touchmove',function(e){if(e.touches[0].clientY-sy>80)closeD();},{passive:true});
  }

});
</script>