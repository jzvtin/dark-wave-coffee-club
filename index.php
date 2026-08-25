<?php require __DIR__ . '/lib.php';
$posts = all_posts();
$latest = array_slice($posts, 0, 6);
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dark Wave Coffee Club — A Coffee Tasting Journal</title>
<?php dg_css(); ?>
</head>
<body>
<?php dg_staging('Homepage. Click any coffee to read the full review. Full archive lives under <b>Reviews</b>.'); ?>
<?php dg_ticker(); ?>
<?php dg_header(); ?>

<div class="hero"><div class="wrap">
  <div class="kicker"><?= t('hero_kicker') ?></div>
  <h1><?= t('hero_title_1') ?> <span><?= t('hero_title_2') ?></span></h1>
  <p><?= t('hero_sub') ?></p>
  <div class="cta">
    <a class="btn" href="reviews.php"><?= t('hero_btn_1') ?></a>
    <a class="btn ghost" href="<?= IG_URL ?>" target="_blank" rel="noopener" style="background:var(--parchment);color:var(--ink);border-color:var(--parchment)"><?= t('hero_btn_2') ?></a>
  </div>
</div></div>

<div class="wrap">

  <div class="sec">
    <div class="sec-head">
      <h2><?= t('home_latest_head') ?></h2>
      <a href="reviews.php"><?= t('home_latest_link') ?></a>
    </div>
    <?php if (!$latest): ?>
      <div class="empty" style="padding:30px 0">
        <?= t('home_empty') ?> ☕<br>
        Follow along on <a href="<?= IG_URL ?>" target="_blank" rel="noopener" style="color:var(--red)"><b>Instagram</b></a> to catch the first pour.
      </div>
    <?php else: ?>
      <div class="index-grid">
        <?php foreach ($latest as $p):
          $img = !empty($p['photos'][0]) ? UPLOAD_URL . '/' . e($p['photos'][0]) : ''; ?>
          <a class="card" href="post.php?slug=<?= e($p['slug']) ?>">
            <div class="cimg"><?php if ($img): ?><img src="<?= $img ?>" alt=""><?php else: ?>photo<?php endif; ?></div>
            <div class="cbody">
              <div class="cmeta"><?= val_or_dash($p['roaster'] ?? '') ?></div>
              <h3><?= val_or_dash($p['title'] ?? '') ?></h3>
              <div class="cnote"><?= val_or_dash($p['tasting_notes'] ?? '') ?></div>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

</div><div class="wrap"><div class="band eq">
  <div>
    <h3><?= t('home_eq_title') ?></h3>
    <p><?= t('home_eq_body') ?></p>
  </div>
  <div class="bcta"><a class="btn" href="equipment.php"><?= t('home_eq_btn') ?></a></div>
</div></div>

<div class="wrap"><div class="band mission">
  <div>
    <h3><?= t('home_mission_title') ?></h3>
    <p><?= t('home_mission_body1') ?></p>
    <p style="margin-top:14px"><?= t('home_mission_body2') ?></p>
  </div>
  <div class="bcta"><a class="btn" href="<?= IG_URL ?>" target="_blank" rel="noopener"><?= t('home_mission_btn') ?> <?= e(IG_HANDLE) ?></a></div>
</div></div>

<?php dg_notify(); ?>
<?php dg_footer(); ?>
<?php dg_reveal_js(); ?>
</body>
</html>
