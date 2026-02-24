<?php
/**
 * صفحه مقالات کتاب‌نت
 * تم: Midnight Blue | بدون سایدبار | وسط‌چین
 */
require_once("./include/header.php");

// ── پارامترهای فیلتر ──
$category_id = isset($_GET['category']) ? filter_var($_GET['category'], FILTER_VALIDATE_INT) : null;
$search      = isset($_GET['search'])   ? trim($_GET['search'])   : '';
$sort        = isset($_GET['sort'])     ? $_GET['sort']           : 'newest';

$allowed_sorts = ['newest','oldest'];
if (!in_array($sort, $allowed_sorts)) $sort = 'newest';

// ── ساخت کوئری ──
$query  = "SELECT * FROM posts";
$params = [];
$where  = [];

if ($category_id) {
    $where[]  = "category_id = ?";
    $params[] = $category_id;
}
if ($search !== '') {
    $where[]  = "(title LIKE ? OR body LIKE ?)";
    $sp       = "%{$search}%";
    $params[] = $sp;
    $params[] = $sp;
}
if (!empty($where)) $query .= " WHERE " . implode(' AND ', $where);
$query .= $sort === 'oldest' ? " ORDER BY id ASC" : " ORDER BY id DESC";

$posts      = fetchAll($db, $query, $params);
$all_cats   = fetchAll($db, "SELECT DISTINCT c.id, c.title FROM categories c INNER JOIN posts p ON p.category_id = c.id ORDER BY c.title");
$total      = count($posts);

$cat_info = null;
if ($category_id) $cat_info = fetchOne($db, "SELECT title FROM categories WHERE id = ?", [$category_id]);

function truncateText($t, $l = 130) {
    $t = strip_tags($t);
    return mb_strlen($t) > $l ? mb_substr($t, 0, $l) . '...' : $t;
}
?>

<link rel="stylesheet" href="./css/style.css">

<style>
/* ═══════════════════════════════════════════════
   posts.php — Midnight Blue — Full Width
   ═══════════════════════════════════════════════ */
.pw { max-width: 1400px; margin: 0 auto; padding: 0 1.5rem; }

/* ── PAGE HERO ── */
.ph {
    position: relative;
    background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #1e40af 100%);
    padding: 9rem 1.5rem 5rem;
    overflow: hidden; text-align: center;
}
.ph::before {
    content: '';
    position: absolute; inset: 0;
    background-image: radial-gradient(circle, rgba(255,255,255,.055) 1px, transparent 1px);
    background-size: 30px 30px; pointer-events: none;
}
.ph::after {
    content: '';
    position: absolute;
    width: 700px; height: 700px; border-radius: 50%;
    background: radial-gradient(circle, rgba(96,165,250,.15) 0%, transparent 65%);
    top: -200px; right: -150px; pointer-events: none;
}
.ph-in { position: relative; z-index: 2; }
.ph-ey {
    display: inline-flex; align-items: center; gap: .6rem;
    font-size: .72rem; letter-spacing: .2em; text-transform: uppercase;
    color: #93c5fd; font-weight: 700; margin-bottom: 1.2rem;
}
.ph-ey span { width: 24px; height: 1px; background: #93c5fd; display: inline-block; }
.ph-t {
    font-size: clamp(2rem, 4vw, 3.4rem);
    font-weight: 900; color: #fff;
    letter-spacing: -.02em; line-height: 1.12; margin-bottom: 1rem;
}
.ph-t em {
    font-style: normal;
    background: linear-gradient(135deg, #60a5fa, #a78bfa);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
}
.ph-sub { color: rgba(255,255,255,.55); font-size: .98rem; margin-bottom: 2.2rem; }
.ph-stats {
    display: inline-flex; gap: 2.5rem; flex-wrap: wrap; justify-content: center;
    background: rgba(255,255,255,.07); border: 1px solid rgba(255,255,255,.12);
    border-radius: 18px; padding: .9rem 2.2rem; backdrop-filter: blur(8px);
}
.ph-st strong { display: block; font-size: 1.5rem; font-weight: 900; color: #60a5fa; line-height: 1; }
.ph-st span   { font-size: .7rem; color: rgba(255,255,255,.4); letter-spacing: .08em; text-transform: uppercase; }

/* ── STICKY SEARCH ── */
.pst-sb {
    background: var(--bg-primary);
    border-bottom: 1px solid var(--border-color);
    padding: 1.4rem 1.5rem;
    position: sticky; top: 72px; z-index: 100;
    box-shadow: var(--shadow-md);
    border-radius: 0 0 12px 12px;
}
.pst-sb-in {
    max-width: 1400px; margin: 0 auto;
    display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;
}
.pst-sbox { flex: 1; position: relative; min-width: 240px; }
.pst-sinput {
    width: 100%;
    padding: .85rem 1.2rem .85rem 3rem;
    border: 1.5px solid var(--border-color); border-radius: 14px;
    font-size: .95rem; font-family: inherit;
    background: var(--bg-secondary); color: var(--text-primary);
    transition: all .3s; outline: none;
}
.pst-sinput:focus {
    border-color: var(--accent-primary);
    box-shadow: 0 0 0 3px rgba(37,99,235,.12);
    background: var(--bg-primary);
}
.pst-sicon {
    position: absolute; left: .9rem; top: 50%; transform: translateY(-50%);
    width: 18px; height: 18px; color: var(--text-secondary); pointer-events: none;
}
.pst-sbtn {
    padding: .85rem 1.7rem;
    background: linear-gradient(135deg, var(--accent-primary,#2563eb), var(--accent-hover,#1d4ed8));
    color: #fff; border: none; border-radius: 14px;
    font-weight: 700; font-size: .9rem; font-family: inherit;
    cursor: pointer; transition: all .3s;
    display: flex; align-items: center; gap: .5rem; white-space: nowrap;
}
.pst-sbtn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(37,99,235,.3); }
.pst-sbtn svg { width: 17px; height: 17px; }
.pst-rc { font-size: .83rem; color: var(--text-secondary); white-space: nowrap; padding: 0 .4rem; }
.pst-rc strong { color: var(--accent-primary); }

/* mobile filter btn */
.pst-mob-btn {
    display: none; align-items: center; gap: .5rem;
    padding: .75rem 1.3rem; border-radius: 12px;
    border: 1.5px solid var(--border-color);
    background: var(--bg-primary); color: var(--text-primary);
    font-family: inherit; font-size: .88rem; font-weight: 700;
    cursor: pointer; transition: all .3s; white-space: nowrap;
}
.pst-mob-btn svg { width: 17px; height: 17px; }
.pst-mob-btn:hover { border-color: var(--accent-primary); color: var(--accent-primary); }

/* ── MAIN ── */
.pst-main { padding: 3rem 0 5rem; }

/* ── FILTERS ROW ── */
.pst-fr { display: flex; gap: 1.5rem; align-items: flex-start; flex-wrap: wrap; margin-bottom: 2.5rem; }

/* cats */
.pst-cats-w { flex: 1; min-width: 0; }
.pst-flbl { font-size: .68rem; letter-spacing: .16em; text-transform: uppercase; color: var(--text-secondary); font-weight: 700; margin-bottom: .7rem; display: block; }
.pst-cats { display: flex; gap: .6rem; flex-wrap: wrap; }
.pst-cat {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .5rem 1.1rem; border-radius: 50px;
    border: 1.5px solid var(--border-color);
    background: transparent; color: var(--text-secondary);
    font-family: inherit; font-size: .83rem; font-weight: 600;
    cursor: pointer; transition: all .3s; text-decoration: none; white-space: nowrap;
}
.pst-cat:hover { border-color: var(--accent-primary); color: var(--accent-primary); }
.pst-cat.on { background: var(--accent-primary); border-color: var(--accent-primary); color: #fff; box-shadow: 0 4px 14px rgba(37,99,235,.25); }
.pst-cat .cc { background: rgba(255,255,255,.25); border-radius: 20px; padding: .08rem .42rem; font-size: .7rem; font-weight: 700; line-height: 1.4; }
.pst-cat:not(.on) .cc { background: var(--bg-secondary); color: var(--text-secondary); }

/* controls */
.pst-ctrl { display: flex; gap: .75rem; align-items: flex-end; }
.pst-cg   { display: flex; flex-direction: column; gap: .45rem; }
.pst-sel {
    padding: .62rem 2.4rem .62rem 1rem;
    border: 1.5px solid var(--border-color); border-radius: 12px;
    background: var(--bg-primary); color: var(--text-primary);
    font-family: inherit; font-size: .86rem; font-weight: 600;
    cursor: pointer; outline: none; transition: all .3s;
    appearance: none; -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='%2364748b'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: left .6rem center;
}
.pst-sel:focus { border-color: var(--accent-primary); box-shadow: 0 0 0 3px rgba(37,99,235,.1); }

.pst-clr {
    padding: .62rem 1.1rem; border-radius: 12px;
    border: 1.5px solid var(--border-color); background: transparent;
    color: var(--text-secondary); font-family: inherit; font-size: .82rem; font-weight: 600;
    cursor: pointer; transition: all .3s; display: flex; align-items: center; gap: .4rem;
    white-space: nowrap; align-self: flex-end;
}
.pst-clr:hover { border-color: #ef4444; color: #ef4444; }
.pst-clr svg { width: 13px; height: 13px; }

/* active tags */
.pst-atags { display: flex; gap: .5rem; flex-wrap: wrap; margin-bottom: 2rem; }
.pst-atag {
    display: inline-flex; align-items: center; gap: .4rem;
    background: rgba(37,99,235,.1); border: 1px solid rgba(37,99,235,.22);
    color: var(--accent-primary); padding: .32rem .8rem; border-radius: 50px;
    font-size: .78rem; font-weight: 600;
}
.pst-atag button { background: none; border: none; cursor: pointer; color: var(--accent-primary); padding: 0; display: flex; line-height: 1; opacity: .7; transition: opacity .2s; }
.pst-atag button:hover { opacity: 1; }
.pst-atag button svg { width: 12px; height: 12px; }

/* top bar */
.pst-topbar {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 1.8rem; flex-wrap: wrap; gap: 1rem;
}
.pst-tinfo { font-size: .87rem; color: var(--text-secondary); }
.pst-tinfo strong { color: var(--text-primary); }

/* view btns */
.pst-vbtns { display: flex; gap: .4rem; }
.pst-vbtn {
    width: 36px; height: 36px; border-radius: 10px;
    border: 1.5px solid var(--border-color); background: var(--bg-primary);
    color: var(--text-secondary); display: flex; align-items: center; justify-content: center;
    cursor: pointer; transition: all .3s;
}
.pst-vbtn:hover, .pst-vbtn.on { background: var(--accent-primary); border-color: var(--accent-primary); color: #fff; }
.pst-vbtn svg { width: 16px; height: 16px; }

/* ── POSTS GRID ── */
.pst-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(310px, 1fr));
    gap: 2rem;
}
/* featured: اولین کارت بزرگ */
.pst-grid.has-featured .pst-card:first-child {
    grid-column: span 2;
}
.pst-grid.has-featured .pst-card:first-child .pst-ciw { padding-top: 52%; }
.pst-grid.has-featured .pst-card:first-child .pst-title { font-size: 1.5rem; }

/* list view */
.pst-grid.lv { grid-template-columns: 1fr; gap: 1.25rem; }
.pst-grid.lv .pst-card { flex-direction: row; border-radius: 16px; }
.pst-grid.lv .pst-card:hover { transform: translateY(-4px); }
.pst-grid.lv .pst-ciw { width: 220px; min-height: 180px; padding-top: 0; flex-shrink: 0; }
.pst-grid.lv .pst-ciw img { position: static; width: 100%; height: 100%; object-fit: cover; }
.pst-grid.lv .pst-card:first-child { grid-column: auto; }
.pst-grid.lv .pst-card:first-child .pst-ciw { padding-top: 0; width: 220px; }
.pst-grid.lv .pst-card:first-child .pst-title { font-size: 1.1rem; }

/* card */
.pst-card {
    background: var(--bg-primary); border-radius: 20px; overflow: hidden;
    border: 1px solid var(--border-color);
    transition: all .4s cubic-bezier(.4,0,.2,1);
    display: flex; flex-direction: column;
    animation: cIn .45s ease both;
}
@keyframes cIn { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
.pst-card:hover { transform: translateY(-10px); box-shadow: var(--shadow-lg); }

/* image */
.pst-ciw { position: relative; padding-top: 60%; overflow: hidden; }
.pst-cimg {
    position: absolute; top: 0; left: 0; width: 100%; height: 100%;
    object-fit: cover; transition: transform .55s ease;
}
.pst-card:hover .pst-cimg { transform: scale(1.07); }

/* category badge */
.pst-cat-badge {
    position: absolute; bottom: 14px; right: 14px;
    background: rgba(0,0,0,.72); backdrop-filter: blur(8px);
    color: #fff; padding: .32rem .85rem; border-radius: 9px;
    font-weight: 700; font-size: .76rem; z-index: 3;
}

/* read time */
.pst-rt {
    position: absolute; top: 14px; left: 14px;
    background: rgba(0,0,0,.65); backdrop-filter: blur(8px);
    color: #fff; padding: .28rem .78rem; border-radius: 8px;
    font-size: .72rem; font-weight: 600; z-index: 3;
}

/* card body */
.pst-body { padding: 1.5rem; flex: 1; display: flex; flex-direction: column; }
.pst-title {
    font-size: 1.12rem; font-weight: 700; color: var(--text-primary);
    margin-bottom: .75rem; line-height: 1.4;
}
.pst-title a { color: inherit; text-decoration: none; transition: color .3s; }
.pst-title a:hover { color: var(--accent-primary); }
.pst-excerpt {
    color: var(--text-secondary); font-size: .9rem;
    line-height: 1.8; flex: 1; margin-bottom: 1.25rem;
}
.pst-foot {
    display: flex; align-items: center; justify-content: space-between;
    padding-top: 1rem; border-top: 1px solid var(--border-color);
}
.pst-author { display: flex; align-items: center; gap: .55rem; color: var(--text-secondary); font-size: .84rem; }
.pst-av {
    width: 30px; height: 30px; border-radius: 50%;
    background: linear-gradient(135deg, var(--accent-primary,#2563eb), var(--accent-hover,#1d4ed8));
    color: #fff; display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: .82rem; flex-shrink: 0;
}
.pst-rm {
    display: flex; align-items: center; gap: .3rem;
    color: var(--accent-primary); text-decoration: none;
    font-weight: 700; font-size: .84rem; transition: gap .3s;
}
.pst-rm:hover { gap: .6rem; }
.pst-rm svg { width: 15px; height: 15px; }

/* no results */
.pst-nores {
    text-align: center; padding: 6rem 2rem;
    background: var(--bg-primary); border-radius: 24px;
    border: 2px dashed var(--border-color); grid-column: 1/-1;
}
.pst-nores-icon { font-size: 5rem; margin-bottom: 1.5rem; display: block; opacity: .45; }
.pst-nores h3 { font-size: 1.5rem; font-weight: 800; color: var(--text-primary); margin-bottom: .5rem; }
.pst-nores p  { color: var(--text-secondary); margin-bottom: 2rem; }
.pst-nores a  {
    display: inline-flex; align-items: center; gap: .5rem;
    padding: .95rem 2rem; border-radius: 14px;
    background: var(--accent-primary); color: #fff;
    font-weight: 700; text-decoration: none; transition: all .3s;
}
.pst-nores a:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(37,99,235,.3); }
.pst-nores a svg { width: 17px; height: 17px; }

/* toast */
.pst-toast {
    position: fixed; bottom: 28px; right: 28px;
    background: var(--bg-primary); color: var(--text-primary);
    border: 1px solid var(--border-color);
    padding: .82rem 1.6rem; border-radius: 14px;
    font-size: .86rem; font-weight: 600; box-shadow: var(--shadow-lg);
    z-index: 9999; transform: translateY(80px); opacity: 0;
    transition: all .35s cubic-bezier(.4,0,.2,1); pointer-events: none;
}
.pst-toast.show { transform: translateY(0); opacity: 1; }

/* ── MOBILE DRAWER ── */
.pst-drawer { position: fixed; inset: 0; z-index: 1000; pointer-events: none; }
.pst-dbg    { position: absolute; inset: 0; background: rgba(0,0,0,.5); opacity: 0; transition: opacity .3s; pointer-events: none; }
.pst-dp {
    position: absolute; bottom: 0; left: 0; right: 0;
    background: var(--bg-primary); border-radius: 24px 24px 0 0;
    padding: 1.8rem 1.5rem 3rem;
    transform: translateY(100%); transition: transform .4s cubic-bezier(.4,0,.2,1);
    max-height: 85vh; overflow-y: auto;
}
.pst-drawer.open { pointer-events: all; }
.pst-drawer.open .pst-dbg { opacity: 1; pointer-events: all; }
.pst-drawer.open .pst-dp  { transform: translateY(0); }
.pst-dhandle { width: 44px; height: 5px; border-radius: 99px; background: var(--border-color); margin: 0 auto 1.5rem; }
.pst-dtitle  { font-size: 1.1rem; font-weight: 800; color: var(--text-primary); margin-bottom: 1.5rem; }
.pst-dsec    { margin-bottom: 1.8rem; }
.pst-dslt    { font-size: .68rem; letter-spacing: .16em; text-transform: uppercase; color: var(--text-secondary); font-weight: 700; margin-bottom: .85rem; }

/* AOS */
[data-aos] { opacity: 0; transition-property: transform,opacity; transition-duration: .6s; transition-timing-function: cubic-bezier(.4,0,.2,1); }
[data-aos].aos-animate { opacity: 1; }
[data-aos="fade-up"] { transform: translateY(28px); } [data-aos="fade-up"].aos-animate { transform: translateY(0); }
[data-aos="zoom-in"] { transform: scale(.94); } [data-aos="zoom-in"].aos-animate { transform: scale(1); }

/* ── RESPONSIVE ── */
@media (max-width: 1024px) {
    .pst-fr { flex-direction: column; }
    .pst-ctrl { width: 100%; }
    .pst-grid.has-featured .pst-card:first-child { grid-column: 1/-1; }
}
@media (max-width: 768px) {
    .ph { padding: 7.5rem 1.25rem 3.5rem; }
    .pst-sb  { padding: 1.1rem 1.25rem; top: 64px; }
    .pst-sb-in { flex-direction: column; align-items: stretch; }
    .pst-sbox { min-width: 0; }
    .pw { padding: 0 1.25rem; }
    .pst-main { padding: 2rem 0 4rem; }
    .pst-fr, .pst-ctrl { display: none; }
    .pst-mob-btn { display: flex; }
    .pst-grid { grid-template-columns: repeat(2, 1fr); gap: 1.25rem; }
    .pst-grid.has-featured .pst-card:first-child { grid-column: 1/-1; }
    .pst-grid.lv { grid-template-columns: 1fr; }
    .pst-grid.lv .pst-ciw { width: 150px; }
}
@media (max-width: 576px) {
    .ph-t { font-size: 1.9rem; }
    .ph-stats { gap: 1.5rem; padding: .8rem 1.4rem; }
    .pst-grid { grid-template-columns: 1fr; gap: 1.1rem; }
    .pst-topbar { flex-direction: column; align-items: flex-start; }
    .pst-grid.has-featured .pst-card:first-child .pst-title { font-size: 1.2rem; }
}
@media (max-width: 420px) {
    .pw { padding: 0 1rem; }
    .pst-sb { padding: .9rem 1rem; }
}
</style>


<!-- STICKY SEARCH -->
<div class="pst-sb">
    <form class="pst-sb-in" method="GET" action="posts.php" id="searchForm">
        <?php if ($category_id): ?><input type="hidden" name="category" value="<?= $category_id ?>"><?php endif; ?>
        <input type="hidden" name="sort" value="<?= escape($sort) ?>">

        <div class="pst-sbox">
            <svg class="pst-sicon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input class="pst-sinput" type="text" name="search"
                   value="<?= escape($search) ?>"
                   placeholder="جستجو در مقالات..."
                   autocomplete="off" id="mainSearch">
        </div>

        <button type="submit" class="pst-sbtn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            جستجو
        </button>

        <button type="button" class="pst-mob-btn" id="mobileFilterBtn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="11" y1="18" x2="13" y2="18"/>
            </svg>
            فیلترها
            <?php if ($category_id || $sort !== 'newest'): ?>
                <span style="background:var(--accent-primary);color:#fff;border-radius:50%;width:18px;height:18px;font-size:.68rem;display:flex;align-items:center;justify-content:center;font-weight:800;">!</span>
            <?php endif; ?>
        </button>

        <span class="pst-rc"><strong><?= $total ?></strong> مقاله یافت شد</span>
    </form>
</div>

<div class="pw">
<div class="pst-main">

    <!-- FILTER ROW -->
    <div class="pst-fr" data-aos="fade-up">

        <!-- دسته‌بندی‌ها -->
        <div class="pst-cats-w">
            <span class="pst-flbl">دسته‌بندی</span>
            <div class="pst-cats">
                <a href="posts.php?sort=<?= escape($sort) ?>&search=<?= urlencode($search) ?>"
                   class="pst-cat <?= !$category_id ? 'on' : '' ?>">همه</a>
                <?php foreach ($all_cats as $cat):
                    $cc = fetchOne($db, "SELECT COUNT(*) as c FROM posts WHERE category_id = ?", [$cat['id']])['c'] ?? 0;
                ?>
                    <a href="posts.php?category=<?= $cat['id'] ?>&sort=<?= escape($sort) ?>&search=<?= urlencode($search) ?>"
                       class="pst-cat <?= $category_id == $cat['id'] ? 'on' : '' ?>">
                        <?= escape($cat['title']) ?>
                        <span class="cc"><?= $cc ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- مرتب‌سازی و پاک‌سازی -->
        <div class="pst-ctrl">
            <div class="pst-cg">
                <span class="pst-flbl">مرتب‌سازی</span>
                <select class="pst-sel" id="sortSel">
                    <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>جدیدترین</option>
                    <option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>قدیمی‌ترین</option>
                </select>
            </div>
            <?php if ($category_id || $sort !== 'newest' || $search !== ''): ?>
            <button type="button" class="pst-clr" id="clearFilters">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
                پاک‌سازی
            </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- ACTIVE TAGS -->
    <div class="pst-atags">
        <?php if ($search !== ''): ?>
        <span class="pst-atag">🔍 «<?= escape($search) ?>»
            <button onclick="rmFilter('search')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </span>
        <?php endif; ?>
        <?php if ($category_id && $cat_info): ?>
        <span class="pst-atag">📂 <?= escape($cat_info['title']) ?>
            <button onclick="rmFilter('category')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </span>
        <?php endif; ?>
        <?php if ($sort !== 'newest'): ?>
        <span class="pst-atag">↕️ قدیمی‌ترین
            <button onclick="rmFilter('sort')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </span>
        <?php endif; ?>
    </div>

    <!-- TOP BAR -->
    <div class="pst-topbar">
        <div class="pst-tinfo">
            نمایش <strong><?= $total ?></strong> مقاله
        </div>
        <div class="pst-vbtns">
            <button class="pst-vbtn on" id="vGrid" title="شبکه‌ای" onclick="setView('grid')">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M3,3H10V10H3V3M13,3H21V10H13V3M3,13H10V21H3V13M13,13H21V21H13V13Z"/></svg>
            </button>
            <button class="pst-vbtn" id="vList" title="لیستی" onclick="setView('list')">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9,5V9H21V5M9,19H21V15H9M9,14H21V10H9M4,9H8V5H4M4,19H8V15H4M4,14H8V10H4V14Z"/></svg>
            </button>
        </div>
    </div>

    <!-- POSTS GRID -->
    <div class="pst-grid <?= $total > 2 ? 'has-featured' : '' ?>" id="pGrid">
        <?php if (!empty($posts)): ?>
            <?php foreach ($posts as $i => $post):
                $pcat = fetchOne($db, "SELECT title FROM categories WHERE id = ?", [$post['category_id']]);
                $rt   = rand(3, 12);
            ?>
            <article class="pst-card" style="animation-delay: <?= min($i * 60, 500) ?>ms">
                <div class="pst-ciw">
                    <img class="pst-cimg"
                         src="./upload/posts/<?= escape($post['image']) ?>"
                         alt="<?= escape($post['title']) ?>"
                         loading="lazy">
                    <span class="pst-cat-badge"><?= escape($pcat['title'] ?? 'نامشخص') ?></span>
                    <span class="pst-rt">⏱ <?= $rt ?> دقیقه</span>
                </div>
                <div class="pst-body">
                    <h3 class="pst-title">
                        <a href="single.php?post=<?= $post['id'] ?>"><?= escape($post['title']) ?></a>
                    </h3>
                    <p class="pst-excerpt"><?= escape(truncateText($post['body'], 125)) ?></p>
                    <div class="pst-foot">
                        <div class="pst-author">
                            <div class="pst-av"><?= mb_substr($post['author'], 0, 1) ?></div>
                            <?= escape($post['author']) ?>
                        </div>
                        <a href="single.php?post=<?= $post['id'] ?>" class="pst-rm">
                            ادامه مطلب
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M4,11V13H16L10.5,18.5L11.92,19.92L19.84,12L11.92,4.08L10.5,5.5L16,11H4Z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="pst-nores">
                <span class="pst-nores-icon">📭</span>
                <h3>مقاله‌ای یافت نشد!</h3>
                <p><?= $search !== '' ? "جستجوی «".escape($search)."» نتیجه‌ای نداشت." : 'با این فیلترها مقاله‌ای وجود ندارد.' ?></p>
                <a href="posts.php">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M10,20V14H14V20H19V12H22L12,3L2,12H5V20H10Z"/></svg>
                    بازگشت به همه مقالات
                </a>
            </div>
        <?php endif; ?>
    </div>

</div>
</div>

<!-- MOBILE DRAWER -->
<div class="pst-drawer" id="filterDrawer">
    <div class="pst-dbg" id="drawerBg"></div>
    <div class="pst-dp">
        <div class="pst-dhandle"></div>
        <div class="pst-dtitle">فیلترها و مرتب‌سازی</div>

        <div class="pst-dsec">
            <div class="pst-dslt">دسته‌بندی</div>
            <div class="pst-cats" style="flex-wrap:wrap">
                <a href="posts.php?sort=<?= escape($sort) ?>&search=<?= urlencode($search) ?>"
                   class="pst-cat <?= !$category_id ? 'on' : '' ?>">همه</a>
                <?php foreach ($all_cats as $cat): ?>
                    <a href="posts.php?category=<?= $cat['id'] ?>&sort=<?= escape($sort) ?>&search=<?= urlencode($search) ?>"
                       class="pst-cat <?= $category_id == $cat['id'] ? 'on' : '' ?>">
                        <?= escape($cat['title']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="pst-dsec">
            <div class="pst-dslt">مرتب‌سازی</div>
            <select class="pst-sel" style="width:100%" id="drawerSort">
                <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>جدیدترین</option>
                <option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>قدیمی‌ترین</option>
            </select>
        </div>

        <button type="button" class="pst-sbtn" style="width:100%;justify-content:center;margin-top:.75rem" id="drawerApply">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20,6 9,17 4,12"/></svg>
            اعمال فیلترها
        </button>
        <button type="button" class="pst-clr" style="width:100%;justify-content:center;margin-top:.6rem" onclick="location='posts.php'">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            پاک‌سازی همه
        </button>
    </div>
</div>

<div class="pst-toast" id="pstToast"></div>

<?php require_once("./include/footer.php"); ?>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* AOS */
    var ao = new IntersectionObserver(function (es) {
        es.forEach(function (e) { if (e.isIntersecting) e.target.classList.add('aos-animate'); });
    }, { threshold: .1, rootMargin: '0px 0px -60px 0px' });
    document.querySelectorAll('[data-aos]').forEach(function (el) { ao.observe(el); });

    /* Toast */
    var toast = document.getElementById('pstToast');
    function showToast(m) { toast.textContent = m; toast.classList.add('show'); setTimeout(function () { toast.classList.remove('show'); }, 2500); }

    /* View Toggle */
    var grid = document.getElementById('pGrid');
    function setView(v) {
        if (v === 'list') {
            grid.classList.add('lv');
            document.getElementById('vList').classList.add('on');
            document.getElementById('vGrid').classList.remove('on');
            localStorage.setItem('pst_view', 'list');
        } else {
            grid.classList.remove('lv');
            document.getElementById('vGrid').classList.add('on');
            document.getElementById('vList').classList.remove('on');
            localStorage.setItem('pst_view', 'grid');
        }
    }
    window.setView = setView;
    if (localStorage.getItem('pst_view') === 'list') setView('list');

    /* Sort */
    var ss = document.getElementById('sortSel');
    if (ss) ss.addEventListener('change', function () {
        var u = new URL(window.location.href);
        u.searchParams.set('sort', this.value);
        window.location = u.toString();
    });

    /* Clear */
    var cl = document.getElementById('clearFilters');
    if (cl) cl.addEventListener('click', function () { window.location = 'posts.php'; });

    /* Remove tag */
    window.rmFilter = function (k) {
        var u = new URL(window.location.href);
        if (k === 'sort') u.searchParams.set('sort', 'newest');
        else u.searchParams.delete(k);
        window.location = u.toString();
    };

    /* Mobile Drawer */
    var drawer = document.getElementById('filterDrawer');
    var dbg    = document.getElementById('drawerBg');
    var mob    = document.getElementById('mobileFilterBtn');
    function openD()  { drawer.classList.add('open');    document.body.style.overflow = 'hidden'; }
    function closeD() { drawer.classList.remove('open'); document.body.style.overflow = ''; }
    if (mob) mob.addEventListener('click', openD);
    if (dbg) dbg.addEventListener('click', closeD);

    var da = document.getElementById('drawerApply');
    if (da) da.addEventListener('click', function () {
        var u = new URL(window.location.href);
        u.searchParams.set('sort', document.getElementById('drawerSort').value);
        window.location = u.toString();
    });

    /* swipe down to close */
    var panel = drawer.querySelector('.pst-dp'), sy = 0;
    if (panel) {
        panel.addEventListener('touchstart', function (e) { sy = e.touches[0].clientY; }, { passive: true });
        panel.addEventListener('touchmove',  function (e) { if (e.touches[0].clientY - sy > 80) closeD(); }, { passive: true });
    }

});
</script>