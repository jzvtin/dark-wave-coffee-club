<?php

define('DATA_DIR', __DIR__ . '/data');
define('UPLOAD_DIR', __DIR__ . '/uploads');
define('UPLOAD_URL', 'uploads');
define('COMMENT_DIR', DATA_DIR . '/comments');

define('IG_URL', 'https://instagram.com/darkwavecoffeeclub');
define('IG_HANDLE', '@darkwavecoffeeclub');
define('CONTACT_EMAIL', 'andre@darkwavecoffeeclub.com');

define('NOTIFY_EMAIL', 'alprice370@gmail.com');

$__dg_host = $_SERVER['HTTP_HOST'] ?? 'darkwavecoffeeclub.com';
define('IS_STAGING', strpos($__dg_host, 'dynaradigital') !== false);
define('SITE_URL', IS_STAGING ? 'https://dynaradigital.com/andre' : 'https://' . $__dg_host);

$__cfg = __DIR__ . '/config.php';
if (is_file($__cfg)) require_once $__cfg;

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/content.php';

function all_posts() {
  if (sb_enabled()) return sb_all_posts();
  $out = [];
  foreach (glob(DATA_DIR . '/*.json') as $f) {
    $p = json_decode(file_get_contents($f), true);
    if ($p) $out[] = $p;
  }
  usort($out, fn($a, $b) => ($b['created'] ?? 0) <=> ($a['created'] ?? 0));
  return $out;
}
function get_post($slug) {
  if (sb_enabled()) return sb_get_post($slug);
  $f = DATA_DIR . '/' . basename($slug) . '.json';
  return is_file($f) ? json_decode(file_get_contents($f), true) : null;
}
function save_post($p) {
  if (sb_enabled()) return sb_save_post($p);
  if (!is_dir(DATA_DIR)) mkdir(DATA_DIR, 0755, true);
  file_put_contents(DATA_DIR . '/' . $p['slug'] . '.json',
    json_encode($p, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}
function delete_post($slug) {
  if (sb_enabled()) return sb_delete_post($slug);
  $f = DATA_DIR . '/' . basename($slug) . '.json';
  if (is_file($f)) unlink($f);
}

function comments_file($slug) { return COMMENT_DIR . '/' . basename($slug) . '.json'; }
function get_comments($slug) {
  if (sb_enabled()) return sb_get_comments($slug);
  $f = comments_file($slug);
  if (!is_file($f)) return [];
  $c = json_decode(file_get_contents($f), true);
  return is_array($c) ? $c : [];
}
function add_comment($slug, $name, $body) {
  if (sb_enabled()) return sb_add_comment($slug, $name, $body);
  if (!is_dir(COMMENT_DIR)) mkdir(COMMENT_DIR, 0755, true);
  $c = get_comments($slug);
  $c[] = ['name' => $name, 'body' => $body, 'created' => time()];
  file_put_contents(comments_file($slug),
    json_encode($c, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
}
function notify_new_comment($post, $name, $body) {
  $to = NOTIFY_EMAIL;
  if (!$to || strpos($to, '@') === false) return;
  $title = $post['title'] ?? 'a review';
  $url = SITE_URL . '/post.php?slug=' . rawurlencode($post['slug'] ?? '') . '#comments';
  $subject = 'New comment alert — ' . $title;
  $msg  = "New comment on \"$title\":\n\n";
  $msg .= "From: $name\n\n";
  $msg .= "$body\n\n";
  $msg .= "Read it here:\n$url\n";
  $headers = 'From: Dark Wave Coffee Club <andre@darkwavecoffeeclub.com>' . "\r\n"
           . 'Content-Type: text/plain; charset=UTF-8';
  @mail($to, $subject, $msg, $headers);
}
function slugify($s) {
  $s = strtolower(trim($s));
  $s = preg_replace('/[^a-z0-9]+/', '-', $s);
  $s = trim($s, '-');
  return $s ?: 'coffee';
}
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function val_or_dash($v) { return ($v === '' || $v === null) ? '—' : e($v); }

function dg_css() { ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Teko:wght@400;500;600;700&family=Bitter:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
<link rel="icon" type="image/png" href="assets/DWCC-mark.png">
<link rel="apple-touch-icon" href="assets/DWCC-mark.png">
<style>
  :root{
    --parchment:#e6d2b5;--parch-2:#dcc4a0;--panel:#efe0c6;--ink:#1c1712;
    --red:#c8102e;--gold:#e8b23a;--green:#4d7358;--blue:#3f5f73;--brown:#7a4a2b;
    --line:#c7ad86;--muted:#7c6a52;--sage:#9fb2a0;
  }
  *{box-sizing:border-box;margin:0;padding:0}
  body{font-family:'Bitter',Georgia,serif;background:var(--parchment);color:var(--ink);
    line-height:1.6;background-image:
      radial-gradient(circle at 20% 30%,rgba(0,0,0,.015),transparent 40%),
      radial-gradient(circle at 80% 70%,rgba(0,0,0,.015),transparent 40%);}
  .wrap{max-width:1040px;margin:0 auto;padding:0 24px}
  a{color:inherit}
  h1,h2,h3,h4,.brand,.nav a,.plot-label,.plot-val,.kicker,.score b,.score span,
  .card h3,.spec .country,.spec .est,.spec-grid .k,.spec-grid .v,.badge-atl,.ticker,
  .card .cscore,.card .cmeta,.btn,.staging b{
    font-family:'Teko',sans-serif;font-weight:600;letter-spacing:.5px;text-transform:uppercase;line-height:1}

  .staging{background:var(--gold);color:var(--ink);border-bottom:2px solid var(--ink);
    text-align:center;padding:7px 24px;font-size:.9rem}
  .staging b{letter-spacing:2px}

  .ticker-band{background:var(--sage);color:var(--ink);overflow:hidden;white-space:nowrap;
    border-bottom:2px solid var(--ink)}
  .ticker{display:inline-block;padding:7px 0;font-size:1.05rem;letter-spacing:3px;
    animation:scroll 26s linear infinite}
  .ticker span{margin:0 34px}
  @keyframes scroll{from{transform:translateX(0)}to{transform:translateX(-50%)}}

  header.site{background:var(--ink);color:var(--parchment);border-bottom:5px solid var(--red)}
  .site .wrap{display:flex;align-items:center;justify-content:space-between;padding:14px 24px}
  .brand{font-size:2.2rem;letter-spacing:1px}
  .brand span{color:var(--gold)}
  .brandlink{display:flex;align-items:center;gap:12px;text-decoration:none;color:inherit}
  .brandmark{height:48px;width:48px;flex:none}
  .brandtext{font-family:'Teko',sans-serif;font-weight:600;text-transform:uppercase;letter-spacing:1px;
    line-height:.9;font-size:1.7rem;color:var(--parchment)}
  .brandtext b{display:block;color:var(--gold);font-weight:600}
  @media(max-width:520px){.brandtext{font-size:1.35rem}.brandmark{height:40px;width:40px}}
  .nav a{color:var(--parchment);text-decoration:none;margin-left:26px;font-size:1.25rem}
  .nav a:hover{color:var(--gold)}

  .masthead{background:var(--panel);border-bottom:2px solid var(--ink);text-align:center;padding:26px 24px 22px}
  .masthead .kicker{font-size:1.05rem;letter-spacing:4px;color:var(--red)}
  .masthead h1{font-size:2.6rem;letter-spacing:2px;margin:2px 0}
  .masthead p{font-family:'Bitter',serif;font-style:italic;text-transform:none;color:var(--muted);font-size:.95rem}

  article{background:var(--panel);margin:30px auto;border:2px solid var(--ink);box-shadow:8px 8px 0 rgba(28,23,18,.18)}
  .post-pad{padding:34px 40px}
  .post-top{display:grid;grid-template-columns:1fr 340px;gap:36px;align-items:start}
  @media(max-width:760px){.post-top{grid-template-columns:1fr}}

  .photos{display:grid;gap:12px}
  .photos.n1{grid-template-columns:1fr}
  .photos.n2{grid-template-columns:1fr 1fr}
  .photos.n3{grid-template-columns:1fr 1fr}
  .ph{position:relative;aspect-ratio:4/3;overflow:hidden;border:2px solid var(--ink);
    background:repeating-linear-gradient(45deg,#dcc9a6,#dcc9a6 10px,#d6c19c 10px,#d6c19c 20px);
    display:flex;align-items:center;justify-content:center;color:#8a7550;font-size:.8rem;font-style:italic;text-align:center;padding:6px}
  .ph img{width:100%;height:100%;object-fit:cover;cursor:zoom-in;display:block}

  .photos.n1 .ph{aspect-ratio:3/2}
  .photos.n3 .ph.lg{grid-column:1 / -1;aspect-ratio:16/9}

  .lb{position:fixed;inset:0;background:rgba(28,23,18,.92);display:none;align-items:center;justify-content:center;
    z-index:9999;padding:24px;cursor:zoom-out}
  .lb.open{display:flex}
  .lb img{max-width:92vw;max-height:88vh;width:auto;height:auto;object-fit:contain;border:3px solid var(--parchment);
    box-shadow:0 0 0 2px var(--ink)}
  .lb .lbx{position:absolute;top:18px;right:24px;color:var(--parchment);font-family:'Teko',sans-serif;
    font-size:2.4rem;line-height:1;cursor:pointer;text-decoration:none}

  .spec{background:var(--ink);color:var(--parchment);border:2px solid var(--ink);
    border-top-left-radius:120px;border-top-right-radius:120px;padding:26px 22px 22px;text-align:center}
  .spec .cup{font-size:1.6rem;margin-bottom:2px}
  .spec .est{font-size:.85rem;letter-spacing:3px;color:var(--gold)}
  .spec .country{font-size:2.1rem;letter-spacing:1px;color:var(--parchment);margin:2px 0 4px}
  .spec .sub{font-family:'Bitter',serif;font-style:italic;text-transform:none;color:var(--sage);font-size:.82rem;margin-bottom:16px}
  .spec-grid{border:2px solid var(--parchment);text-align:left}
  .spec-grid .row{display:grid;border-bottom:2px solid var(--parchment)}
  .spec-grid .row:last-child{border-bottom:0}
  .spec-grid .row.two{grid-template-columns:1fr 1fr}
  .spec-grid .cell{padding:8px 12px}
  .spec-grid .cell+.cell{border-left:2px solid var(--parchment)}
  .spec-grid .k{display:block;font-size:.78rem;letter-spacing:2px;color:var(--gold)}
  .spec-grid .v{font-size:1.15rem;color:var(--parchment)}
  .dots{display:flex;gap:6px;margin-top:4px}
  .dots i{width:13px;height:13px;border-radius:50%;border:2px solid var(--parchment);display:inline-block}
  .dots i.on{background:var(--red);border-color:var(--red)}
  .badge-atl{margin-top:14px;background:var(--red);color:var(--parchment);display:inline-block;
    padding:6px 16px 3px;font-size:1.6rem;letter-spacing:2px;border:2px solid var(--parchment)}

  .body-cols{display:grid;grid-template-columns:1fr 1fr;gap:36px;margin-top:34px}
  @media(max-width:760px){.body-cols{grid-template-columns:1fr}}
  h2{font-size:1.7rem;letter-spacing:1px;margin-bottom:12px;border-bottom:3px solid var(--ink);padding-bottom:4px}
  .tasting p{margin-bottom:12px;white-space:pre-line}
  .placeholder-text{color:#a08a68;font-style:italic}

  .plots{background:var(--parchment);border:2px solid var(--ink);padding:22px 20px}
  .plot-title{text-align:center;font-family:'Teko',sans-serif;text-transform:uppercase;font-size:1.35rem;letter-spacing:2px;margin-bottom:2px}
  .plot-sub{text-align:center;font-family:'Bitter',serif;font-style:italic;color:var(--muted);font-size:.8rem;margin-bottom:18px}
  .plot-row{display:grid;grid-template-columns:110px 1fr;align-items:center;gap:12px;margin:12px 0}
  .plot-label{font-size:1.2rem;color:var(--ink);text-align:right}
  .track{position:relative;height:22px;background:transparent;border:2px solid var(--ink)}
  .fill{position:absolute;left:0;top:0;height:100%}
  .fill.red{background:var(--red)}.fill.gold{background:var(--gold)}.fill.green{background:var(--green)}
  .scale{display:grid;grid-template-columns:110px 1fr;gap:12px;color:var(--muted);font-size:.72rem;
    margin-top:6px;font-family:'Teko',sans-serif;letter-spacing:1px}
  .scale span:nth-child(2){display:flex;justify-content:space-between}
  .note{color:var(--muted);font-size:.78rem;font-style:italic;margin-top:12px;text-align:center}

  .quick{margin:34px 40px 0;padding:22px 26px;background:var(--parchment);border:2px solid var(--ink);border-left:8px solid var(--red)}
  .quick h3{color:var(--red);font-size:1.3rem;letter-spacing:2px;margin-bottom:6px;font-family:'Teko',sans-serif;text-transform:uppercase}
  .quick p{font-size:1.05rem;white-space:pre-line}

  .comments{margin:26px 40px 40px}
  .comments h2{margin-bottom:16px}

  footer.site{background:var(--ink);color:var(--gold);text-align:center;padding:22px;
    font-family:'Teko',sans-serif;text-transform:uppercase;letter-spacing:2px;border-top:5px solid var(--red);font-size:1.05rem}

  .index-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:26px;margin:30px auto 50px}
  @media(max-width:820px){.index-grid{grid-template-columns:1fr 1fr}}
  @media(max-width:540px){.index-grid{grid-template-columns:1fr}}
  .card{background:var(--panel);border:2px solid var(--ink);box-shadow:6px 6px 0 rgba(28,23,18,.15);
    text-decoration:none;color:var(--ink);display:flex;flex-direction:column;transition:transform .12s}
  .card:hover{transform:translate(-2px,-2px);box-shadow:8px 8px 0 rgba(28,23,18,.2)}
  .card .cimg{aspect-ratio:16/10;overflow:hidden;border-bottom:2px solid var(--ink);
    background:repeating-linear-gradient(45deg,#dcc9a6,#dcc9a6 10px,#d6c19c 10px,#d6c19c 20px);
    display:flex;align-items:center;justify-content:center;color:#8a7550;font-style:italic;font-size:.8rem}
  .card .cimg img{width:100%;height:100%;object-fit:cover}
  .card .cbody{padding:16px 18px}
  .card .cscore{float:right;background:var(--red);color:var(--parchment);font-size:1.05rem;
    padding:3px 10px 1px;border:2px solid var(--ink)}
  .card .cmeta{color:var(--red);font-size:.95rem;letter-spacing:1px}
  .card h3{font-size:1.55rem;letter-spacing:1px;margin:2px 0 6px}
  .card .cnote{font-size:.9rem;color:var(--muted)}
  .empty{text-align:center;padding:60px 20px;color:var(--muted);font-style:italic}

  .notify{background:var(--ink);color:var(--parchment);border-top:2px solid var(--ink);border-bottom:5px solid var(--red)}
  .notify .wrap{display:flex;align-items:center;justify-content:space-between;gap:24px;padding:24px}
  @media(max-width:640px){.notify .wrap{flex-direction:column;text-align:center}}
  .notify h3{font-size:1.8rem;letter-spacing:1px;color:var(--gold)}
  .notify p{font-family:'Bitter',serif;font-style:italic;color:var(--sage);font-size:.9rem}
  .nform{display:flex;gap:10px}
  @media(max-width:400px){.nform{flex-direction:column;width:100%}}
  .nform input{padding:11px 14px;border:2px solid var(--parchment);background:var(--parchment);
    font-family:'Bitter',serif;font-size:1rem;min-width:220px}
  .btn{display:inline-block;background:var(--red);color:var(--parchment);border:2px solid var(--parchment);
    padding:10px 22px 7px;font-family:'Teko',sans-serif;text-transform:uppercase;letter-spacing:1px;
    font-size:1.2rem;cursor:pointer;text-decoration:none}
  .btn:hover{background:var(--gold);color:var(--ink);border-color:var(--ink)}

  footer.site .fwrap{display:grid;grid-template-columns:1.4fr 1fr 1fr;gap:30px;text-align:left;
    padding:30px 24px;align-items:start}
  @media(max-width:640px){footer.site .fwrap{grid-template-columns:1fr;gap:20px}}
  .fbrand{font-family:'Teko',sans-serif;text-transform:uppercase;font-size:1.8rem;letter-spacing:1px;color:var(--parchment)}
  .fbrand span{color:var(--gold)}
  .ftag{font-family:'Bitter',serif;text-transform:none;letter-spacing:0;color:var(--sage);font-style:italic;font-size:.85rem;margin-top:4px}
  .fcol h4{color:var(--gold);font-size:1.1rem;letter-spacing:1px;margin-bottom:8px}
  .fcol a{display:block;color:var(--parchment);text-decoration:none;font-family:'Bitter',serif;
    text-transform:none;letter-spacing:0;font-size:.9rem;padding:3px 0}
  .fcol a:hover{color:var(--gold)}

  .eq-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:26px;margin:30px auto 50px}
  @media(max-width:820px){.eq-grid{grid-template-columns:1fr 1fr}}
  @media(max-width:540px){.eq-grid{grid-template-columns:1fr}}
  .eq-card{background:var(--panel);border:2px solid var(--ink);box-shadow:6px 6px 0 rgba(28,23,18,.15)}
  .eq-card .eimg{aspect-ratio:1/1;overflow:hidden;border-bottom:2px solid var(--ink);
    background:repeating-linear-gradient(45deg,#dcc9a6,#dcc9a6 10px,#d6c19c 10px,#d6c19c 20px);
    display:flex;align-items:center;justify-content:center;color:#8a7550;font-style:italic;font-size:.8rem}
  .eq-card .eimg img{width:100%;height:100%;object-fit:cover}
  .eq-card{display:flex;flex-direction:column}
  .eq-card .ebody{padding:16px 18px;display:flex;flex-direction:column;flex:1}
  .eq-card .etype{font-family:'Teko',sans-serif;text-transform:uppercase;letter-spacing:1px;color:var(--red);font-size:1rem}
  .eq-card h3{font-family:'Teko',sans-serif;text-transform:uppercase;font-size:1.5rem;letter-spacing:1px;margin:2px 0 6px}
  .eq-card .enote{font-size:.9rem;color:var(--muted);flex:1}
  .eq-card .eprice{font-family:'Teko',sans-serif;letter-spacing:1px;color:var(--brown);font-size:1.15rem;margin:10px 0 12px}
  .eq-card .ebtn{margin-top:auto;text-align:center}
  .aff-note{max-width:1040px;margin:0 auto;padding:0 24px;color:var(--muted);font-style:italic;font-size:.82rem;text-align:center}

  .sortbar{display:flex;align-items:center;justify-content:flex-end;gap:10px;margin:26px auto 0}
  .sortbar .slabel{font-family:'Teko',sans-serif;text-transform:uppercase;letter-spacing:2px;color:var(--muted);font-size:1rem}
  .sortbar .stog{display:inline-flex;border:2px solid var(--ink);background:var(--panel)}
  .sortbar .stog button{font-family:'Teko',sans-serif;text-transform:uppercase;letter-spacing:1px;font-size:1.05rem;
    background:transparent;color:var(--ink);border:0;padding:7px 16px 5px;cursor:pointer}
  .sortbar .stog button+button{border-left:2px solid var(--ink)}
  .sortbar .stog button.on{background:var(--red);color:var(--parchment)}

  .about-grid{display:grid;grid-template-columns:1fr 1fr;gap:36px;margin:30px 0}
  @media(max-width:700px){.about-grid{grid-template-columns:1fr}}
  .contact-list a,.contact-list span{display:block;font-size:1.1rem;padding:6px 0;color:var(--ink);text-decoration:none}
  .contact-list a:hover{color:var(--red)}
  .contact-list .k{font-family:'Teko',sans-serif;text-transform:uppercase;letter-spacing:1px;color:var(--red);font-size:.95rem;margin-top:12px}

  .hero{background:var(--ink);color:var(--parchment);border-bottom:5px solid var(--red);
    background-image:radial-gradient(circle at 15% 20%,rgba(232,178,58,.10),transparent 45%),
      radial-gradient(circle at 85% 80%,rgba(200,16,46,.12),transparent 45%);}
  .hero .wrap{padding:60px 24px 54px;text-align:center}
  .hero .kicker{font-family:'Teko',sans-serif;text-transform:uppercase;letter-spacing:5px;color:var(--gold);font-size:1.1rem}
  .hero h1{font-family:'Teko',sans-serif;text-transform:uppercase;font-size:4rem;letter-spacing:2px;line-height:.95;margin:8px 0 12px}
  @media(max-width:600px){.hero h1{font-size:2.8rem}}
  .hero h1 span{color:var(--gold)}
  .hero p{font-family:'Bitter',serif;font-style:italic;color:var(--sage);font-size:1.15rem;max-width:600px;margin:0 auto 26px}
  .hero .cta{display:flex;gap:14px;justify-content:center;flex-wrap:wrap}

  .sec{margin:52px auto}
  .sec-head{display:flex;align-items:baseline;justify-content:space-between;border-bottom:3px solid var(--ink);
    padding-bottom:6px;margin-bottom:26px;gap:12px;flex-wrap:wrap}
  .sec-head h2{font-family:'Teko',sans-serif;text-transform:uppercase;font-size:2rem;letter-spacing:1px;border:0;padding:0;margin:0}
  .sec-head a{font-family:'Teko',sans-serif;text-transform:uppercase;letter-spacing:1px;color:var(--red);
    text-decoration:none;font-size:1.15rem}
  .sec-head a:hover{color:var(--brown)}

  .band{border:2px solid var(--ink);box-shadow:8px 8px 0 rgba(28,23,18,.16);margin:52px auto;
    display:grid;grid-template-columns:1fr auto;gap:24px;align-items:center;padding:30px 36px}
  @media(max-width:640px){.band{grid-template-columns:1fr;text-align:center}}
  .band.eq{background:var(--panel)}
  .band.mission{background:var(--ink);color:var(--parchment)}
  .band.about{background:var(--panel)}
  .band h3{font-family:'Teko',sans-serif;text-transform:uppercase;font-size:1.9rem;letter-spacing:1px;margin-bottom:6px}
  .band.mission h3{color:var(--gold)}
  .band p{font-family:'Bitter',serif;font-size:1rem;line-height:1.55;max-width:640px}
  .band.mission p{color:var(--sage)}
  .band .bcta{display:flex;gap:12px;flex-wrap:wrap;justify-content:center}

  .roaster-spot{background:var(--panel);border:2px solid var(--ink);box-shadow:8px 8px 0 rgba(28,23,18,.16);
    margin:30px auto;padding:28px 34px;display:grid;grid-template-columns:70px 1fr;gap:22px;align-items:start}
  @media(max-width:560px){.roaster-spot{grid-template-columns:1fr;text-align:center}}
  .roaster-spot .rav{width:70px;height:70px;border-radius:50%;background:var(--gold);border:2px solid var(--ink);
    display:flex;align-items:center;justify-content:center;font-family:'Teko',sans-serif;font-size:1.8rem;color:var(--ink);
    letter-spacing:1px;margin:0 auto}
  .roaster-spot .rk{font-family:'Teko',sans-serif;text-transform:uppercase;letter-spacing:2px;color:var(--red);font-size:.95rem}
  .roaster-spot h3{font-family:'Teko',sans-serif;text-transform:uppercase;font-size:1.9rem;letter-spacing:1px;margin:2px 0 8px}
  .roaster-spot p{font-family:'Bitter',serif;font-size:.98rem;line-height:1.55;color:var(--ink);margin-bottom:14px}
  .roaster-spot .rcta{display:flex;gap:12px;flex-wrap:wrap}
  .buy-band{max-width:1040px;margin:24px auto 0;padding:0 24px}
  .buy-band a{display:block;text-align:center;background:var(--red);color:var(--parchment);border:2px solid var(--ink);
    padding:16px;font-family:'Teko',sans-serif;text-transform:uppercase;letter-spacing:2px;font-size:1.5rem;text-decoration:none}
  .buy-band a:hover{background:var(--ink);color:var(--gold)}

  .subblock{margin-top:20px;border-top:2px solid var(--line);padding-top:14px}
  .mini-h{font-family:'Teko',sans-serif;text-transform:uppercase;letter-spacing:1px;color:var(--red);font-size:1.15rem;margin-bottom:4px}

  .plot-ticks{display:grid;grid-template-columns:110px 1fr;gap:12px;margin:-6px 0 12px}
  .plot-ticks .t{display:flex;justify-content:space-between;font-family:'Teko',sans-serif;letter-spacing:1px;font-size:.72rem;color:var(--muted)}

  .aroma{margin-top:16px;border-top:2px solid var(--ink);padding-top:12px}
  .aroma .ak{display:block;font-family:'Teko',sans-serif;text-transform:uppercase;letter-spacing:2px;color:var(--red);font-size:.85rem}
  .aroma .av{font-family:'Bitter',serif;font-size:.95rem;line-height:1.5}

  .crafted{margin:34px 40px 0;padding:24px 26px;background:var(--parchment);border:2px solid var(--ink);border-left:8px solid var(--gold)}
  .crafted>h3{font-family:'Teko',sans-serif;text-transform:uppercase;letter-spacing:2px;color:var(--brown);font-size:1.4rem;margin-bottom:14px}
  .craft-item{display:grid;grid-template-columns:64px 1fr;gap:18px;align-items:start}
  .craft-item+.craft-item{margin-top:18px;border-top:2px solid var(--line);padding-top:18px}
  @media(max-width:560px){.craft-item{grid-template-columns:1fr;text-align:center}}
  .craft-item .cav{width:64px;height:64px;border-radius:50%;background:var(--gold);border:2px solid var(--ink);
    display:flex;align-items:center;justify-content:center;font-family:'Teko',sans-serif;font-size:1.6rem;color:var(--ink);letter-spacing:1px;margin:0 auto}
  .craft-item .ck{font-family:'Teko',sans-serif;text-transform:uppercase;letter-spacing:2px;color:var(--red);font-size:.85rem}
  .craft-item .craft-body b{display:block;font-family:'Teko',sans-serif;text-transform:uppercase;font-size:1.5rem;letter-spacing:1px;margin:1px 0 6px}
  .craft-item .craft-body p{font-family:'Bitter',serif;font-size:.95rem;line-height:1.55}

  .quicktake{max-width:1040px;margin:30px auto 0;padding:0 24px}
  .quicktake .qt-inner{background:var(--panel);border:2px solid var(--ink);box-shadow:8px 8px 0 rgba(28,23,18,.16);border-left:8px solid #3300FF;padding:24px 30px}
  .quicktake h3{font-family:'Teko',sans-serif;text-transform:uppercase;letter-spacing:2px;color:#3300FF;font-size:1.5rem;margin-bottom:8px}
  .quicktake p{font-family:'Bitter',serif;font-size:1.05rem;line-height:1.6;white-space:pre-line}
  .quicktake .qt-cta{margin-top:12px;font-style:italic;color:var(--muted);font-size:.9rem}

  .comments{margin:40px auto}
  .comments h2{font-family:'Teko',sans-serif;text-transform:uppercase;font-size:2rem;letter-spacing:1px}
  .cok{background:var(--green);color:#fff;border:2px solid var(--ink);padding:12px 16px;margin:16px 0;
    font-family:'Teko',sans-serif;text-transform:uppercase;letter-spacing:1px}
  .cerr{background:var(--red);color:#fff;border:2px solid var(--ink);padding:12px 16px;margin:16px 0;
    font-family:'Teko',sans-serif;text-transform:uppercase;letter-spacing:1px}
  .clist{margin:20px 0}
  .cmt{background:var(--panel);border:2px solid var(--ink);box-shadow:4px 4px 0 rgba(28,23,18,.12);
    padding:16px 20px;margin-bottom:14px}
  .cmt .cwho{font-family:'Teko',sans-serif;text-transform:uppercase;letter-spacing:1px;font-size:1.2rem;color:var(--red)}
  .cmt .cwhen{font-family:'Bitter',serif;font-style:italic;color:var(--muted);font-size:.78rem;margin-left:8px}
  .cmt .cbody{margin-top:6px;white-space:pre-line}
  .cnone{color:var(--muted);font-style:italic;margin:14px 0}
  .cform{background:var(--panel);border:2px solid var(--ink);box-shadow:6px 6px 0 rgba(28,23,18,.15);padding:22px 26px;margin-top:22px}
  .cform label{display:block;font-family:'Teko',sans-serif;text-transform:uppercase;letter-spacing:1px;font-size:1.05rem;margin:10px 0 4px}
  .cform input[type=text],.cform textarea{width:100%;padding:10px 12px;border:2px solid var(--ink);
    background:var(--parchment);font-family:'Bitter',serif;font-size:1rem;color:var(--ink)}
  .cform textarea{min-height:90px;resize:vertical}
  .cform .hp{position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden}

  .reveal{opacity:0;transform:translateY(24px);
    transition:opacity .6s cubic-bezier(.2,.6,.2,1),transform .6s cubic-bezier(.2,.6,.2,1)}
  .reveal.in{opacity:1;transform:none}
  @media(prefers-reduced-motion:reduce){.reveal{opacity:1!important;transform:none!important;transition:none}}

  @media(max-width:640px){
    .wrap{padding:0 18px}
    .site .wrap{flex-direction:column;gap:12px;padding:16px 18px}
    .nav{display:flex;flex-wrap:wrap;justify-content:center;gap:4px 18px}
    .nav a{margin-left:0;font-size:1.15rem}
    .ticker{font-size:.95rem;letter-spacing:2px}
    .ticker span{margin:0 22px}
    .hero .wrap{padding:42px 18px 38px}
    .hero p{font-size:1.02rem}
    .masthead h1{font-size:2.1rem}
    .sec{margin:36px auto}
    .band{margin:36px auto;padding:24px 22px}
    .sec-head h2{font-size:1.7rem}
  }
  @media(max-width:400px){
    .hero h1{font-size:2.3rem}
    .brandtext{font-size:1.25rem}
    .nav a{font-size:1.08rem}
  }
</style>
<?php }

function dg_ticker() {  }
function dg_staging($msg) {
  if (!IS_STAGING) return;
?>
<div class="staging"><b>STAGING</b> — <?= $msg ?></div>
<?php }
function dg_header() { ?>
<header class="site"><div class="wrap">
  <div class="brand"><a class="brandlink" href="index.php">
    <img class="brandmark" src="assets/DWCC-mark.png" alt="Dark Wave Coffee Club">
    <span class="brandtext"><?= t('brand_top') ?> <b><?= t('brand_bottom') ?></b></span>
  </a></div>
  <nav class="nav">
    <a href="index.php"><?= t('nav_home') ?></a>
    <a href="reviews.php"><?= t('nav_reviews') ?></a>
    <a href="equipment.php"><?= t('nav_equipment') ?></a>
    <a href="about.php"><?= t('nav_contact') ?></a>
    <a href="<?= IG_URL ?>" target="_blank" rel="noopener"><?= t('nav_instagram') ?></a>
  </nav>
</div></header>
<?php }

function dg_notify($compact = false) { ?>
<div class="notify<?= $compact ? ' compact' : '' ?>">
  <div class="wrap">
    <div class="ntxt">
      <h3><?= t('notify_title') ?></h3>
      <p><?= t('notify_body') ?></p>
    </div>
    <form class="nform" method="post" action="subscribe.php">
      <input type="email" name="email" placeholder="<?= t('notify_placeholder') ?>" required>
      <button class="btn" type="submit"><?= t('notify_btn') ?></button>
    </form>
  </div>
</div>
<?php }

function dg_footer() { ?>
<footer class="site"><div class="wrap fwrap">
  <div class="fcol">
    <div class="fbrand"><?= t('brand_top') ?> <span><?= t('brand_bottom') ?></span></div>
    <p class="ftag"><?= t('footer_tagline') ?></p>
  </div>
  <div class="fcol">
    <h4><?= t('footer_col2_head') ?></h4>
    <a href="<?= IG_URL ?>" target="_blank" rel="noopener"><?= t('footer_ig_label') ?> <?= e(IG_HANDLE) ?></a>
    <a href="about.php"><?= t('footer_contact') ?></a>
  </div>
  <div class="fcol">
    <h4><?= t('footer_col3_head') ?></h4>
    <a href="reviews.php"><?= t('footer_reviews') ?></a>
    <a href="equipment.php"><?= t('footer_equipment') ?></a>
    <a href="about.php"><?= t('footer_about') ?></a>
  </div>
</div></footer>
<?php }

function dg_reveal_js() { ?>
<script>
(function(){
  if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  if (!('IntersectionObserver' in window)) return;
  var sel = '.hero .kicker,.hero h1,.hero p,.hero .cta,'
          + '.masthead .kicker,.masthead h1,.masthead p,'
          + '.sec-head,.index-grid>.card,.band,.eq-grid>.eq-card,'
          + 'article,.buy-band,.quicktake,.comments,.aff-note';
  var els = Array.prototype.slice.call(document.querySelectorAll(sel));
  if (!els.length) return;
  els.forEach(function(el){ el.classList.add('reveal'); });
  var io = new IntersectionObserver(function(entries){
    entries.forEach(function(en){
      if (!en.isIntersecting) return;
      var el = en.target;

      var delay = 0, p = el.parentNode;
      if (p && (p.classList.contains('index-grid') || p.classList.contains('eq-grid'))) {
        delay = Array.prototype.indexOf.call(p.children, el) % 3 * 90;
      }
      el.style.transitionDelay = delay + 'ms';
      el.classList.add('in');
      io.unobserve(el);
    });
  }, {threshold:0.12, rootMargin:'0px 0px -8% 0px'});
  els.forEach(function(el){ io.observe(el); });
})();
</script>
<?php }

function dg_lightbox() { ?>
<div class="lb" id="lb"><a class="lbx" id="lbx" href="#" aria-label="Close">&times;</a><img id="lbimg" src="" alt=""></div>
<script>
(function(){
  var lb=document.getElementById('lb'),img=document.getElementById('lbimg');
  if(!lb) return;
  function open(src){img.src=src;lb.classList.add('open');document.body.style.overflow='hidden';}
  function close(){lb.classList.remove('open');img.src='';document.body.style.overflow='';}
  document.addEventListener('click',function(e){
    var t=e.target;
    if(t&&t.matches('.photos .ph img[data-full]')){open(t.getAttribute('data-full'));return;}
    if(t===lb||t.id==='lbx'){e.preventDefault();close();}
  });
  document.addEventListener('keydown',function(e){if(e.key==='Escape')close();});
})();
</script>
<?php }

function render_post($p) {
  $roast = (int)($p['roast'] ?? 0);

  $photos = array_values(array_filter($p['photos'] ?? [], fn($x) => $x !== '' && $x !== null));
  $n = count($photos);
  $grid_cls = $n >= 3 ? 'n3' : ($n === 2 ? 'n2' : 'n1');
  ?>
  <article>
    <div class="post-pad">
      <div class="post-top">
        <div class="photos <?= $grid_cls ?>">
          <?php if ($n === 0): ?>
            <div class="ph">bag photo w/ details</div>
          <?php else: for ($i = 0; $i < $n; $i++):
            $cls = ($n >= 3 && $i === 0) ? 'ph lg' : 'ph';
            $src = UPLOAD_URL . '/' . e($photos[$i]); ?>
            <div class="<?= $cls ?>">
              <img src="<?= $src ?>" data-full="<?= $src ?>" alt="" loading="lazy">
            </div>
          <?php endfor; endif; ?>
        </div>

        <div class="spec">
          <div class="cup">☕</div>
          <div class="est">Est. <?= val_or_dash($p['est'] ?? '') ?> · Reviewed by André</div>
          <div class="country"><?= val_or_dash($p['title'] ?? '') ?></div>
          <div class="sub"><?= val_or_dash($p['sub'] ?? '') ?></div>
          <div class="spec-grid">
            <div class="row"><div class="cell">
              <span class="k">Tasting Notes</span>
              <span class="v"><?= val_or_dash($p['tasting_notes'] ?? '') ?></span>
            </div></div>
            <div class="row two">
              <div class="cell"><span class="k">Region</span><span class="v"><?= val_or_dash($p['region'] ?? '') ?></span></div>
              <div class="cell"><span class="k">Roast</span>
                <span class="dots"><?php for ($d = 1; $d <= 5; $d++): ?><i class="<?= $d <= $roast ? 'on' : '' ?>"></i><?php endfor; ?></span></div>
            </div>
            <div class="row two">
              <div class="cell"><span class="k">Varietal</span><span class="v"><?= val_or_dash($p['varietal'] ?? '') ?></span></div>
              <div class="cell"><span class="k">Process</span><span class="v"><?= val_or_dash($p['process'] ?? '') ?></span></div>
            </div>
          </div>
        </div>
      </div>

      <div class="body-cols">
        <div>
          <h2><?= t('post_experience_head') ?></h2>
          <div class="tasting">
            <?php if (!empty($p['experience'])): ?>
              <p><?= e($p['experience']) ?></p>
            <?php else: ?>
              <p class="placeholder-text">The story of the cup — first sip, how it changes as it cools, body, and finish.</p>
            <?php endif; ?>
          </div>
          <?php if (!empty($p['method'])): ?>
            <div class="subblock">
              <h3 class="mini-h">Preparation Method &amp; Grind Settings</h3>
              <p><?= e($p['method']) ?></p>
            </div>
          <?php endif; ?>
        </div>
        <div class="plots">
          <div class="plot-title"><?= t('post_attr_title') ?></div>
          <div class="plot-sub"><?= t('post_attr_sub') ?></div>
          <?php
          $bars = [
            ['Acidity','red','acidity','Low','High'],
            ['Body','gold','body','Low','High'],
            ['Aftertaste','green','aftertaste','Short','Long'],
          ];
          foreach ($bars as [$label,$color,$key,$lo,$hi]):
            $v = $p[$key] ?? '';
            $w = ($v === '' || $v === null) ? 0 : max(0, min(10, (float)$v)) * 10; ?>
            <div class="plot-row">
              <span class="plot-label"><?= $label ?></span>
              <span class="track"><span class="fill <?= $color ?>" style="width:<?= $w ?>%"></span></span>
            </div>
            <div class="plot-ticks"><span></span><span class="t"><em><?= $lo ?></em><em><?= $hi ?></em></span></div>
          <?php endforeach; ?>
          <?php if (!empty($p['aroma'])): ?>
            <div class="aroma"><span class="ak">Aroma</span><span class="av"><?= e($p['aroma']) ?></span></div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <?php
      $r_name = trim($p['roaster'] ?? '');      $r_bio = trim($p['roaster_bio'] ?? '');
      $c_name = trim($p['ceramicist'] ?? '');   $c_bio = trim($p['ceramicist_bio'] ?? '');
      $mk_init = function($n){ $i=''; foreach(preg_split('/\s+/', $n) as $w){ if($w!=='') $i.=strtoupper($w[0]); } return substr($i,0,2) ?: '☕'; };
    ?>
    <div class="crafted">
      <h3><?= t('post_crafted_head') ?></h3>
      <div class="craft-item">
        <div class="cav"><?= $r_name!=='' ? e($mk_init($r_name)) : '☕' ?></div>
        <div class="craft-body">
          <span class="ck">Roaster</span>
          <b><?= $r_name!=='' ? e($r_name) : 'This roaster' ?></b>
          <?php if ($r_bio!==''): ?><p><?= e($r_bio) ?></p>
          <?php else: ?><p class="placeholder-text">Who roasted this &amp; why they matter — a great spot to spotlight a small, minority-owned business.</p><?php endif; ?>
        </div>
      </div>
      <?php if ($c_name!=='' || $c_bio!==''): ?>
      <div class="craft-item">
        <div class="cav"><?= $c_name!=='' ? e($mk_init($c_name)) : '✷' ?></div>
        <div class="craft-body">
          <span class="ck">Ceramicist</span>
          <b><?= $c_name!=='' ? e($c_name) : 'Ceramicist' ?></b>
          <?php if ($c_bio!==''): ?><p><?= e($c_bio) ?></p><?php endif; ?>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </article>
  <?php
}
