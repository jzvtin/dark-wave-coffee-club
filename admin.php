<?php
require __DIR__ . '/lib.php';
session_start();

if (!defined('ADMIN_PASS')) define('ADMIN_PASS', 'change-me');

function process_photos($slug, $existing = []) {
  if (!is_dir(UPLOAD_DIR)) @mkdir(UPLOAD_DIR, 0755, true);
  $ok_ext = ['jpg','jpeg','png','gif','webp'];
  $slots = [];
  for ($i = 0; $i < 3; $i++) $slots[$i] = $existing[$i] ?? '';
  $remove = array_map('strval', (array)($_POST['remove_photo'] ?? []));
  for ($i = 0; $i < 3; $i++) {

    if (in_array((string)$i, $remove, true) && $slots[$i] !== '') {
      @unlink(UPLOAD_DIR . '/' . basename($slots[$i]));
      $slots[$i] = '';
    }

    $err = $_FILES['photo']['error'][$i] ?? UPLOAD_ERR_NO_FILE;
    if ($err === UPLOAD_ERR_OK) {
      $ext = strtolower(pathinfo($_FILES['photo']['name'][$i], PATHINFO_EXTENSION));
      if (in_array($ext, $ok_ext)
          && $_FILES['photo']['size'][$i] <= 8 * 1024 * 1024
          && @getimagesize($_FILES['photo']['tmp_name'][$i]) !== false) {
        if ($slots[$i] !== '') @unlink(UPLOAD_DIR . '/' . basename($slots[$i]));
        $fname = $slug . '-' . $i . '-' . substr(dechex(time()), -3) . '.' . $ext;
        if (move_uploaded_file($_FILES['photo']['tmp_name'][$i], UPLOAD_DIR . '/' . $fname)) {
          $slots[$i] = $fname;
        }
      }
    }
  }
  return array_values(array_filter($slots, fn($x) => $x !== ''));
}

if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
$CSRF = $_SESSION['csrf'];

if (isset($_GET['logout'])) { session_destroy(); header('Location: admin.php'); exit; }

define('LOGIN_MAX', 5);

define('LOGIN_WINDOW', 900);

$__throttle_file = DATA_DIR . '/login_throttle.json';
$__ip = $_SERVER['REMOTE_ADDR'] ?? 'cli';
$__load_throttle = function() use ($__throttle_file) {
  $j = @file_get_contents($__throttle_file);
  $a = $j ? json_decode($j, true) : [];
  return is_array($a) ? $a : [];
};
$__save_throttle = function($a) use ($__throttle_file) {
  if (!is_dir(DATA_DIR)) @mkdir(DATA_DIR, 0755, true);
  @file_put_contents($__throttle_file, json_encode($a), LOCK_EX);
};
if (isset($_POST['login_pass'])) {
  $now = time();
  $store = $__load_throttle();
  $hits = array_values(array_filter($store[$__ip] ?? [], fn($t) => $t > $now - LOGIN_WINDOW));
  if (count($hits) >= LOGIN_MAX) {
    $wait = (int)ceil(($hits[0] + LOGIN_WINDOW - $now) / 60);
    $login_err = "Too many attempts. Try again in about {$wait} min.";
  } elseif (hash_equals(ADMIN_PASS, $_POST['login_pass'])) {
    unset($store[$__ip]);

    $__save_throttle($store);
    $_SESSION['dg_admin'] = true;
  } else {
    $hits[] = $now;
    $store[$__ip] = $hits;
    $__save_throttle($store);
    $left = LOGIN_MAX - count($hits);
    $login_err = 'Wrong password.' . ($left > 0 ? " {$left} attempt" . ($left === 1 ? '' : 's') . ' left.' : '');
  }
}
$authed = !empty($_SESSION['dg_admin']);

$notice = '';
if ($authed && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
 if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
   $notice = 'Security check failed — refresh the page and try again.';
 } else {

  if ($_POST['action'] === 'delete') {
    $slug = $_POST['slug'] ?? '';
    $p = get_post($slug);
    if ($p) {
      foreach (($p['photos'] ?? []) as $ph) { @unlink(UPLOAD_DIR . '/' . basename($ph)); }
      delete_post($slug);
    }
    $notice = 'Review deleted.';
  }

  if ($_POST['action'] === 'create') {
    $title = trim($_POST['title'] ?? '');
    if ($title === '') {
      $notice = 'Coffee name is required.';
    } else {
      $slug = slugify($title) . '-' . substr(dechex(time()), -4);

      $photos = process_photos($slug, []);

      $num = fn($k) => ($_POST[$k] ?? '') === '' ? '' : (string)max(0, min(10, (float)$_POST[$k]));
      $post = [
        'slug'          => $slug,
        'title'         => $title,
        'roaster'       => trim($_POST['roaster'] ?? ''),
        'roaster_url'   => trim($_POST['roaster_url'] ?? ''),
        'roaster_bio'   => trim($_POST['roaster_bio'] ?? ''),
        'ceramicist'    => trim($_POST['ceramicist'] ?? ''),
        'ceramicist_bio'=> trim($_POST['ceramicist_bio'] ?? ''),
        'sub'           => trim($_POST['sub'] ?? ''),
        'est'           => trim($_POST['est'] ?? ''),
        'region'        => trim($_POST['region'] ?? ''),
        'varietal'      => trim($_POST['varietal'] ?? ''),
        'process'       => trim($_POST['process'] ?? ''),
        'roast'         => (int)($_POST['roast'] ?? 0),
        'tasting_notes' => trim($_POST['tasting_notes'] ?? ''),
        'experience'    => trim($_POST['experience'] ?? ''),
        'method'        => trim($_POST['method'] ?? ''),
        'aroma'         => trim($_POST['aroma'] ?? ''),
        'quick_take'    => trim($_POST['quick_take'] ?? ''),
        'acidity'       => $num('acidity'),
        'body'          => $num('body'),
        'aftertaste'    => $num('aftertaste'),
        'photos'        => $photos,
        'created'       => time(),
      ];
      save_post($post);
      $notice = 'Published! ';
      $new_slug = $slug;
    }
  }

  if ($_POST['action'] === 'update') {
    $slug = basename($_POST['slug'] ?? '');
    $existing = get_post($slug);
    $title = trim($_POST['title'] ?? '');
    if (!$existing) {
      $notice = 'That review no longer exists.';
    } elseif ($title === '') {
      $notice = 'Coffee name is required.';
    } else {

      $photos = process_photos($slug, $existing['photos'] ?? []);
      $num = fn($k) => ($_POST[$k] ?? '') === '' ? '' : (string)max(0, min(10, (float)$_POST[$k]));
      $post = [
        'slug'          => $slug,
        'title'         => $title,
        'roaster'       => trim($_POST['roaster'] ?? ''),
        'roaster_url'   => trim($_POST['roaster_url'] ?? ''),
        'roaster_bio'   => trim($_POST['roaster_bio'] ?? ''),
        'ceramicist'    => trim($_POST['ceramicist'] ?? ''),
        'ceramicist_bio'=> trim($_POST['ceramicist_bio'] ?? ''),
        'sub'           => trim($_POST['sub'] ?? ''),
        'est'           => trim($_POST['est'] ?? ''),
        'region'        => trim($_POST['region'] ?? ''),
        'varietal'      => trim($_POST['varietal'] ?? ''),
        'process'       => trim($_POST['process'] ?? ''),
        'roast'         => (int)($_POST['roast'] ?? 0),
        'tasting_notes' => trim($_POST['tasting_notes'] ?? ''),
        'experience'    => trim($_POST['experience'] ?? ''),
        'method'        => trim($_POST['method'] ?? ''),
        'aroma'         => trim($_POST['aroma'] ?? ''),
        'quick_take'    => trim($_POST['quick_take'] ?? ''),
        'acidity'       => $num('acidity'),
        'body'          => $num('body'),
        'aftertaste'    => $num('aftertaste'),
        'photos'        => $photos,
        'created'       => $existing['created'] ?? time(),
        'updated'       => time(),
      ];
      save_post($post);
      $notice = 'Saved changes. ';
      $new_slug = $slug;
    }
  }

  if ($_POST['action'] === 'save_content') {
    $n = save_site_text($_POST['st'] ?? []);
    $notice = 'Site text saved.' . ($n ? " {$n} custom value" . ($n === 1 ? '' : 's') . ' in effect.' : ' All fields back to defaults.');
    $content_open = true;

  }
 }
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin — Dark Wave Coffee Club</title>
<?php dg_css(); ?>
<style>
  .adminwrap{max-width:820px;margin:0 auto;padding:0 24px}
  .abox{background:var(--panel);border:2px solid var(--ink);box-shadow:6px 6px 0 rgba(28,23,18,.15);
    padding:26px 30px;margin:24px auto}
  .abox h2{margin-bottom:18px}
  label{display:block;font-family:'Teko',sans-serif;text-transform:uppercase;letter-spacing:1px;
    font-size:1.1rem;color:var(--ink);margin:14px 0 4px}
  .hint{font-family:'Bitter',serif;text-transform:none;letter-spacing:0;color:var(--muted);
    font-style:italic;font-size:.8rem;margin-left:8px}
  input[type=text],input[type=number],input[type=password],textarea,select{
    width:100%;padding:10px 12px;border:2px solid var(--ink);background:var(--parchment);
    font-family:'Bitter',serif;font-size:1rem;color:var(--ink)}
  textarea{min-height:80px;resize:vertical}
  input[type=file]{width:100%;padding:8px;border:2px dashed var(--ink);background:var(--parchment);font-family:'Bitter',serif}
  .grid2{display:grid;grid-template-columns:1fr 1fr;gap:16px}
  .grid3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px}
  @media(max-width:560px){.grid2,.grid3{grid-template-columns:1fr}}
  .btn{display:inline-block;background:var(--red);color:var(--parchment);border:2px solid var(--ink);
    padding:11px 26px 8px;font-size:1.25rem;letter-spacing:1px;cursor:pointer;text-decoration:none}
  .btn:hover{background:var(--ink)}
  .btn.ghost{background:var(--parchment);color:var(--ink)}
  .btn.sm{padding:6px 14px 4px;font-size:1rem}
  .notice{background:var(--green);color:#fff;border:2px solid var(--ink);padding:12px 16px;margin:18px 0;
    font-family:'Teko',sans-serif;text-transform:uppercase;letter-spacing:1px;font-size:1.15rem}
  .err{background:var(--red)}
  .plist{list-style:none}
  .plist li{display:flex;align-items:center;justify-content:space-between;gap:12px;
    border-bottom:1px solid var(--line);padding:12px 0}
  .plist .t{font-family:'Teko',sans-serif;text-transform:uppercase;font-size:1.35rem;letter-spacing:1px}
  .plist .m{color:var(--muted);font-size:.85rem;font-style:italic}
  .row-actions{display:flex;gap:8px;align-items:center}
  .fieldnote{background:var(--parchment);border-left:6px solid var(--gold);padding:10px 14px;
    font-style:italic;color:var(--muted);font-size:.85rem;margin-bottom:16px}
  .photoslots{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-top:6px}
  @media(max-width:560px){.photoslots{grid-template-columns:1fr}}
  .pslot{border:2px solid var(--ink);background:var(--parchment);padding:12px}
  .pslot .pnum{font-family:'Teko',sans-serif;text-transform:uppercase;letter-spacing:1px;font-size:1.05rem;margin-bottom:8px}
  .pslot img{width:100%;height:120px;object-fit:cover;border:2px solid var(--ink);display:block;margin-bottom:6px}
  .pslot .premove{display:flex;align-items:center;gap:6px;font-family:'Bitter',serif;text-transform:none;
    letter-spacing:0;font-size:.85rem;color:var(--red);margin:0 0 6px}
  .pslot .premove input{width:auto}
  .pslot input[type=file]{padding:6px;font-size:.85rem}

  .stgroup{font-family:'Teko',sans-serif;text-transform:uppercase;letter-spacing:2px;color:var(--red);
    font-size:1.35rem;margin:26px 0 4px;border-bottom:2px solid var(--ink);padding-bottom:3px}
  .stgroup:first-of-type{margin-top:6px}
  .stfield{margin:12px 0}
  .stfield label{margin:0 0 3px}
  details.stwrap>summary{cursor:pointer;font-family:'Teko',sans-serif;text-transform:uppercase;
    letter-spacing:1px;font-size:1.3rem;color:var(--ink)}
  .adminnav{display:flex;gap:10px;flex-wrap:wrap;margin:16px 0}
</style>
</head>
<body>
<?php dg_staging('Publishing dashboard. Everything you save here goes live immediately.'); ?>
<?php dg_header(); ?>

<div class="adminwrap">

<?php if (!$authed): ?>
  <div class="abox" style="max-width:420px">
    <h2>Admin Login</h2>
    <?php if (!empty($login_err)): ?><div class="notice err"><?= e($login_err) ?></div><?php endif; ?>
    <form method="post">
      <label>Password</label>
      <input type="password" name="login_pass" autofocus>
      <div style="margin-top:18px"><button class="btn" type="submit">Enter</button></div>
    </form>
  </div>

<?php else: ?>

  <?php if ($notice): ?>
    <div class="notice">
      <?= e($notice) ?>
      <?php if (!empty($new_slug)): ?><a href="post.php?slug=<?= e($new_slug) ?>" style="color:#fff;text-decoration:underline">View it &rarr;</a><?php endif; ?>
    </div>
  <?php endif; ?>

  <?php
    $edit_slug = isset($_GET['edit']) ? basename($_GET['edit']) : '';
    $d = $edit_slug ? (get_post($edit_slug) ?: []) : [];
    $editing = !empty($d);
    $fv = fn($k) => e($d[$k] ?? '');
    $roast_cur = (int)($d['roast'] ?? 0);
  ?>
  <div class="abox">
    <div style="display:flex;justify-content:space-between;align-items:baseline">
      <h2><?= $editing ? 'Edit Review' : 'New Coffee Review' ?></h2>
      <div class="row-actions">
        <a class="btn ghost sm" href="#sitetext">Edit Site Text ↓</a>
        <a class="btn ghost sm" href="admin.php?logout=1">Log out</a>
      </div>
    </div>
    <?php if ($editing): ?>
      <div class="fieldnote">Editing <b><?= $fv('title') ?></b>. Change anything and Save. Photos left empty stay as-is. <a href="admin.php" style="color:var(--red)">Cancel &amp; start a new post</a></div>
    <?php else: ?>
      <div class="fieldnote">Only the <b>Coffee name</b> is required. Leave anything blank and it shows a dash/placeholder — fill it in later.</div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="action" value="<?= $editing ? 'update' : 'create' ?>">
      <input type="hidden" name="csrf" value="<?= e($CSRF) ?>">
      <?php if ($editing): ?><input type="hidden" name="slug" value="<?= $fv('slug') ?>"><?php endif; ?>

      <label>Coffee name <span class="hint">e.g. Ethiopia Yirgacheffe</span></label>
      <input type="text" name="title" value="<?= $fv('title') ?>" required>

      <div class="grid2">
        <div><label>Roaster</label><input type="text" name="roaster" value="<?= $fv('roaster') ?>"></div>
        <div><label>Est. year <span class="hint">roast/harvest yr</span></label><input type="text" name="est" value="<?= $fv('est') ?>"></div>
      </div>

      <label>Roaster website / shop link <span class="hint">powers the "Buy from Roaster" button — leave blank to hide it</span></label>
      <input type="text" name="roaster_url" placeholder="https://" value="<?= $fv('roaster_url') ?>">

      <label>About the roaster <span class="hint">who they are &amp; why they matter — shows in "Crafted by…"</span></label>
      <textarea name="roaster_bio"><?= $fv('roaster_bio') ?></textarea>

      <label>Ceramicist <span class="hint">optional — the maker of the cup</span></label>
      <input type="text" name="ceramicist" value="<?= $fv('ceramicist') ?>">

      <label>Ceramicist notes <span class="hint">your comments about the cup &amp; its maker — these show under the ceramicist in "Crafted by…"</span></label>
      <textarea name="ceramicist_bio"><?= $fv('ceramicist_bio') ?></textarea>

      <label>One-liner <span class="hint">roaster &amp; origin, shown under the name</span></label>
      <input type="text" name="sub" value="<?= $fv('sub') ?>">

      <div class="grid3">
        <div><label>Region</label><input type="text" name="region" value="<?= $fv('region') ?>"></div>
        <div><label>Varietal</label><input type="text" name="varietal" value="<?= $fv('varietal') ?>"></div>
        <div><label>Process</label><input type="text" name="process" value="<?= $fv('process') ?>"></div>
      </div>

      <label>Roast level <span class="hint">1 = lightest, 5 = darkest (dot scale)</span></label>
      <select name="roast">
        <?php foreach ([0=>'— not set —',1=>'1 · Light',2=>'2 · Light-Medium',3=>'3 · Medium',4=>'4 · Medium-Dark',5=>'5 · Dark'] as $rv=>$rl): ?>
          <option value="<?= $rv ?>"<?= $roast_cur === $rv ? ' selected' : '' ?>><?= $rl ?></option>
        <?php endforeach; ?>
      </select>

      <label>Tasting notes <span class="hint">comma-separated, e.g. Jasmine, Blueberry, Honey</span></label>
      <input type="text" name="tasting_notes" value="<?= $fv('tasting_notes') ?>">

      <label>The Experience <span class="hint">a paragraph or two, André's words</span></label>
      <textarea name="experience"><?= $fv('experience') ?></textarea>

      <label>Preparation Method &amp; Grind Settings <span class="hint">shows under "The Experience"</span></label>
      <textarea name="method"><?= $fv('method') ?></textarea>

      <div class="fieldnote">These three set the <b>bar length</b> on the <b>Attributes</b> plot (0 = none, 10 = intense). Readers see bars only — no numbers, no grade. Aftertaste ticks read Short → Long.</div>
      <div class="grid3">
        <div><label>Acidity <span class="hint">bar length 0–10</span></label><input type="number" name="acidity" min="0" max="10" step="0.1" value="<?= $fv('acidity') ?>"></div>
        <div><label>Body <span class="hint">bar length 0–10</span></label><input type="number" name="body" min="0" max="10" step="0.1" value="<?= $fv('body') ?>"></div>
        <div><label>Aftertaste <span class="hint">bar length 0–10</span></label><input type="number" name="aftertaste" min="0" max="10" step="0.1" value="<?= $fv('aftertaste') ?>"></div>
      </div>

      <label>Aroma <span class="hint">shows under the Aftertaste bar in Attributes</span></label>
      <input type="text" name="aroma" value="<?= $fv('aroma') ?>">

      <label>Quick Take <span class="hint">your little post — coffee or not — to spark reader comments</span></label>
      <textarea name="quick_take"><?= $fv('quick_take') ?></textarea>

      <label>Photos <span class="hint">up to 3 · each slot is separate — adding one never removes another · jpg/png/webp, 8MB</span></label>
      <div class="photoslots">
        <?php for ($i = 0; $i < 3; $i++): $cur = $d['photos'][$i] ?? ''; ?>
          <div class="pslot">
            <div class="pnum">Photo <?= $i + 1 ?><?= $i === 0 ? ' (main)' : '' ?></div>
            <?php if ($cur !== ''): ?>
              <img src="<?= UPLOAD_URL.'/'.e($cur) ?>" alt="">
              <label class="premove"><input type="checkbox" name="remove_photo[]" value="<?= $i ?>"> Remove</label>
              <div class="hint" style="margin:0 0 4px">Pick a file to replace it:</div>
            <?php endif; ?>
            <input type="file" name="photo[<?= $i ?>]" accept="image/*">
          </div>
        <?php endfor; ?>
      </div>

      <div style="margin-top:22px"><button class="btn" type="submit"><?= $editing ? 'Save Changes' : 'Publish Review' ?></button></div>
    </form>
  </div>

  <div class="abox">
    <h2>Published Reviews</h2>
    <?php $posts = all_posts(); if (!$posts): ?>
      <p class="m" style="font-style:italic;color:var(--muted)">Nothing published yet.</p>
    <?php else: ?>
      <ul class="plist">
        <?php foreach ($posts as $p): ?>
          <li>
            <div>
              <div class="t"><?= e($p['title']) ?></div>
              <div class="m"><?= val_or_dash($p['roaster'] ?? '') ?> · <?= date('M j, Y', $p['created'] ?? time()) ?></div>
            </div>
            <div class="row-actions">
              <a class="btn ghost sm" href="post.php?slug=<?= e($p['slug']) ?>" target="_blank">View</a>
              <a class="btn ghost sm" href="admin.php?edit=<?= e($p['slug']) ?>#top">Edit</a>
              <form method="post" onsubmit="return confirm('Delete this review permanently?')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="csrf" value="<?= e($CSRF) ?>">
                <input type="hidden" name="slug" value="<?= e($p['slug']) ?>">
                <button class="btn sm" type="submit">Delete</button>
              </form>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>

  <div class="abox" id="sitetext">
    <details class="stwrap"<?= !empty($content_open) ? ' open' : '' ?>>
      <summary><h2 style="display:inline-block;margin:0">Site Text — edit the words on every page</h2></summary>
      <div class="fieldnote">Change any wording here and it goes live instantly. Leave a box <b>blank</b> to keep the standard wording (shown as the grey placeholder). This edits the fixed page copy — hero, headings, mission, footer, etc. — <b>not</b> the reviews (those are above).</div>
      <form method="post">
        <input type="hidden" name="action" value="save_content">
        <input type="hidden" name="csrf" value="<?= e($CSRF) ?>">
        <?php $ov = site_text_all();
        foreach (site_text_registry() as $group => $fields): ?>
          <div class="stgroup"><?= e($group) ?></div>
          <?php foreach ($fields as $k => $meta): [$flabel, $fdef, $ftype] = $meta; $cur = $ov[$k] ?? ''; ?>
            <div class="stfield">
              <label><?= e($flabel) ?></label>
              <?php if ($ftype === 'textarea'): ?>
                <textarea name="st[<?= $k ?>]" placeholder="<?= e($fdef) ?>"><?= e($cur) ?></textarea>
              <?php else: ?>
                <input type="text" name="st[<?= $k ?>]" value="<?= e($cur) ?>" placeholder="<?= e($fdef) ?>">
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        <?php endforeach; ?>
        <div style="margin-top:22px"><button class="btn" type="submit">Save Site Text</button></div>
      </form>
    </details>
  </div>

<?php endif; ?>
</div>

<?php dg_footer(); ?>
</body>
</html>
