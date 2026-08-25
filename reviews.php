<?php require __DIR__ . '/lib.php';
$posts = all_posts();
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>All Reviews — Dark Wave Coffee Club</title>
<?php dg_css(); ?>
</head>
<body>
<?php dg_staging('Every review, newest first. Use the sort toggle to flip the order. Click any coffee to read the full write-up.'); ?>
<?php dg_ticker(); ?>
<?php dg_header(); ?>

<div class="masthead"><div class="wrap">
  <div class="kicker"><?= t('reviews_kicker') ?></div>
  <h1><?= t('reviews_title') ?></h1>
  <p><?= t('reviews_sub') ?></p>
</div></div>

<div class="wrap">
<?php if (!$posts): ?>
  <div class="empty" style="padding:50px 0">
    Reviews are brewing. ☕<br>
    Follow along on <a href="<?= IG_URL ?>" target="_blank" rel="noopener" style="color:var(--red)"><b>Instagram</b></a> to catch the first pour.
  </div>
<?php else: ?>
  <?php if (count($posts) > 1): ?>
  <div class="sortbar">
    <span class="slabel">Sort</span>
    <span class="stog">
      <button type="button" id="sortNew" class="on" onclick="dgSort('new')">Newest</button>
      <button type="button" id="sortOld" onclick="dgSort('old')">Oldest</button>
    </span>
  </div>
  <?php endif; ?>
  <div class="index-grid" id="reviewGrid">
    <?php foreach ($posts as $p):
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

<?php dg_notify(); ?>
<?php dg_footer(); ?>
<script>

function dgSort(dir){
  var grid=document.getElementById('reviewGrid');
  if(!grid) return;
  var cards=Array.prototype.slice.call(grid.children);
  if(dir==='old') cards.reverse();
  cards.forEach(function(c){grid.appendChild(c);});
  document.getElementById('sortNew').classList.toggle('on',dir==='new');
  document.getElementById('sortOld').classList.toggle('on',dir==='old');
}
</script>
<?php dg_reveal_js(); ?>
</body>
</html>
