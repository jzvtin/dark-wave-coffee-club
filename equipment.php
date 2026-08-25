<?php require __DIR__ . '/lib.php';

$gear = [
  ['type' => 'Pour-Over Brewer', 'name' => 'Hario V60 (ceramic)',    'note' => 'Daily driver dripper — clean, bright cups. Size 02.',           'price' => '', 'url' => '', 'img' => ''],
  ['type' => 'Pour-Over Machine','name' => 'Automatic pour-over',    'note' => 'Hands-off pour-over brewer (e.g. Breville Precision / OXO).',    'price' => '', 'url' => '', 'img' => ''],
  ['type' => 'Grinder',          'name' => 'Burr grinder',           'note' => 'Conical burr, the setting André dials in for pour-over.',        'price' => '', 'url' => '', 'img' => ''],
  ['type' => 'Gooseneck Kettle', 'name' => 'Variable-temp kettle',   'note' => 'Precise pour + temperature control, why it matters.',            'price' => '', 'url' => '', 'img' => ''],
  ['type' => 'Scale',            'name' => 'Coffee scale + timer',   'note' => '0.1g resolution, built-in timer for ratio + brew time.',         'price' => '', 'url' => '', 'img' => ''],
  ['type' => 'Filters',          'name' => 'Filter of choice',       'note' => 'Paper / cloth / metal — how each changes the cup.',              'price' => '', 'url' => '', 'img' => ''],
  ['type' => 'Mug',              'name' => 'Favorite cup',           'note' => 'The one every photo on this blog ends up in.',                   'price' => '', 'url' => '', 'img' => ''],
];
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Equipment — Dark Wave Coffee Club</title>
<?php dg_css(); ?>
</head>
<body>
<?php dg_staging('Equipment page — affiliate-ready. Add your links &amp; prices in <b>equipment.php</b>; cards without a link just show the gear.'); ?>
<?php dg_ticker(); ?>
<?php dg_header(); ?>

<div class="masthead"><div class="wrap">
  <div class="kicker"><?= t('eq_kicker') ?></div>
  <h1><?= t('eq_title') ?></h1>
  <p><?= t('eq_sub') ?></p>
</div></div>

<p class="aff-note"><?= t('eq_aff_note') ?></p>

<div class="wrap">
  <div class="eq-grid">
    <?php foreach ($gear as $g): ?>
      <div class="eq-card">
        <div class="eimg"><?php if (!empty($g['img'])): ?><img src="<?= e($g['img']) ?>" alt="<?= e($g['name']) ?>"><?php else: ?>gear photo<?php endif; ?></div>
        <div class="ebody">
          <div class="etype"><?= e($g['type']) ?></div>
          <h3><?= e($g['name']) ?></h3>
          <div class="enote"><?= e($g['note']) ?></div>
          <?php if (!empty($g['price'])): ?><div class="eprice"><?= e($g['price']) ?></div><?php endif; ?>
          <?php if (!empty($g['url'])): ?>
            <a class="btn ebtn" href="<?= e($g['url']) ?>" target="_blank" rel="sponsored nofollow noopener">View / Buy</a>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php dg_notify(); ?>
<?php dg_footer(); ?>
<?php dg_reveal_js(); ?>
</body>
</html>
