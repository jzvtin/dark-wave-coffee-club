<?php require __DIR__ . '/lib.php';
$p = get_post($_GET['slug'] ?? '');
if (!$p) { http_response_code(404); }

$more = [];
if ($p) {
  foreach (all_posts() as $o) {
    if (($o['slug'] ?? '') !== ($p['slug'] ?? '')) $more[] = $o;
    if (count($more) >= 3) break;
  }
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $p ? e($p['title']) : 'Not found' ?> — Dark Wave Coffee Club</title>
<?php dg_css(); ?>
</head>
<body>
<?php dg_staging('One published review. Edit or delete it in <b style="letter-spacing:1px">Admin</b>.'); ?>
<?php dg_ticker(); ?>
<?php dg_header(); ?>

<div class="masthead"><div class="wrap">
  <div class="kicker"><a href="reviews.php" style="color:var(--red);text-decoration:none">&larr; All Reviews</a></div>
  <h1><?= $p ? e($p['title']) : 'Not Found' ?></h1>
  <p><?= $p ? val_or_dash($p['sub'] ?? '') : 'That coffee does not exist yet.' ?></p>
</div></div>

<div class="wrap">
<?php if ($p): render_post($p); else: ?>
  <div class="empty">No such review. <a href="reviews.php" style="color:var(--red)">Back to all reviews</a>.</div>
<?php endif; ?>
</div>

<?php if ($p):
  $r_url  = trim($p['roaster_url'] ?? '');
  $r_name = trim($p['roaster'] ?? '');
?>

  <?php if ($r_url): ?>
  <div class="buy-band">
    <a href="<?= e($r_url) ?>" target="_blank" rel="noopener">
      Buy from <?= $r_name !== '' ? e($r_name) : 'the Roaster' ?> &rarr;
    </a>
  </div>
  <?php endif; ?>

  <?php  ?>
  <div class="quicktake"><div class="qt-inner">
    <h3><?= t('post_current_head') ?></h3>
    <?php if (!empty($p['quick_take'])): ?>
      <p><?= e($p['quick_take']) ?></p>
    <?php else: ?>
      <p class="placeholder-text">A few of André's own words — could be about the coffee, could be about anything. Jump in below.</p>
    <?php endif; ?>
    <p class="qt-cta"><?= t('post_current_cta') ?></p>
  </div></div>

  <?php

  $comments = get_comments($p['slug']);
  $cflag = $_GET['c'] ?? '';
  ?>
  <div class="wrap"><div class="comments" id="comments">
    <h2><?= t('post_comments_head') ?><?php if ($comments): ?> <span style="color:var(--muted)">(<?= count($comments) ?>)</span><?php endif; ?></h2>

    <?php if ($cflag === 'ok'): ?>
      <div class="cok">Thanks — your comment is posted.</div>
    <?php elseif ($cflag === 'empty'): ?>
      <div class="cerr">Please add your name and a comment.</div>
    <?php endif; ?>

    <div class="clist">
      <?php if (!$comments): ?>
        <p class="cnone"><?= t('post_comment_none') ?></p>
      <?php else: foreach ($comments as $c): ?>
        <div class="cmt">
          <span class="cwho"><?= e($c['name']) ?></span>
          <span class="cwhen"><?= date('M j, Y', $c['created'] ?? time()) ?></span>
          <div class="cbody"><?= e($c['body']) ?></div>
        </div>
      <?php endforeach; endif; ?>
    </div>

    <form class="cform" method="post" action="comment.php">
      <input type="hidden" name="slug" value="<?= e($p['slug']) ?>">
      <div class="hp"><label>Website</label><input type="text" name="website" tabindex="-1" autocomplete="off"></div>
      <label>Name</label>
      <input type="text" name="name" maxlength="60" required>
      <label>Comment</label>
      <textarea name="body" maxlength="2000" required></textarea>
      <div style="margin-top:16px"><button class="btn" type="submit"><?= t('post_comment_btn') ?></button></div>
    </form>
  </div></div>

  <?php if ($more): ?>
  <div class="wrap"><div class="sec">
    <div class="sec-head">
      <h2><?= t('post_more_head') ?></h2>
      <a href="reviews.php"><?= t('home_latest_link') ?></a>
    </div>
    <div class="index-grid">
      <?php foreach ($more as $m):
        $img = !empty($m['photos'][0]) ? UPLOAD_URL . '/' . e($m['photos'][0]) : ''; ?>
        <a class="card" href="post.php?slug=<?= e($m['slug']) ?>">
          <div class="cimg"><?php if ($img): ?><img src="<?= $img ?>" alt=""><?php else: ?>photo<?php endif; ?></div>
          <div class="cbody">
            <div class="cmeta"><?= val_or_dash($m['roaster'] ?? '') ?></div>
            <h3><?= val_or_dash($m['title'] ?? '') ?></h3>
            <div class="cnote"><?= val_or_dash($m['tasting_notes'] ?? '') ?></div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div></div>
  <?php endif; ?>

<?php endif; ?>

<?php dg_notify(); ?>
<?php dg_footer(); ?>
<?php dg_lightbox(); ?>
<?php dg_reveal_js(); ?>
</body>
</html>
