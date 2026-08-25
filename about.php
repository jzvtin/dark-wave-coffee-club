<?php require __DIR__ . '/lib.php'; ?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact — Dark Wave Coffee Club</title>
<?php dg_css(); ?>
</head>
<body>
<?php dg_staging('Contact &amp; about page — Instagram link, contact details, notification signup. Details are placeholders.'); ?>
<?php dg_ticker(); ?>
<?php dg_header(); ?>

<div class="masthead"><div class="wrap">
  <div class="kicker"><?= t('about_kicker') ?></div>
  <h1><?= t('about_title') ?></h1>
  <p><?= t('about_sub') ?></p>
</div></div>

<div class="wrap">
  <article><div class="post-pad">
    <div style="text-align:center;margin:0 0 30px">
      <img src="assets/DWCC-wordmark.png" alt="Dark Wave Coffee Club" style="max-width:min(560px,100%);height:auto">
    </div>
    <div class="quick" style="margin:0 0 28px">
      <h3><?= t('about_mission_head') ?></h3>
      <p style="white-space:normal"><?= t('about_mission_body') ?></p>
    </div>
    <div class="about-grid">
      <div>
        <img src="assets/andre.jpg" onerror="this.onerror=null;this.src='assets/DWCC-emblem.png'"
          alt="André" style="width:170px;height:170px;object-fit:cover;border:2px solid var(--ink);
          float:right;margin:0 0 10px 16px">
        <h2><?= t('about_andre_head') ?></h2>
        <p><?= t('about_andre_body1') ?></p>
        <p style="margin-top:12px"><?= t('about_andre_body2') ?></p>
      </div>
      <div>
        <h2><?= t('about_touch_head') ?></h2>
        <div class="contact-list">
          <span class="k">Instagram</span>
          <a href="<?= IG_URL ?>" target="_blank" rel="noopener"><?= e(IG_HANDLE) ?></a>
          <?php if (CONTACT_EMAIL && strpos(CONTACT_EMAIL, 'example') === false): ?>
            <span class="k">Email</span>
            <a href="mailto:<?= e(CONTACT_EMAIL) ?>"><?= e(CONTACT_EMAIL) ?></a>
          <?php endif; ?>
          <span class="k">Location</span>
          <span class="placeholder-text"><?= t('about_location') ?></span>
        </div>
      </div>
    </div>
  </div></article>
</div>

<?php dg_notify(); ?>
<?php dg_footer(); ?>
<?php dg_reveal_js(); ?>
</body>
</html>
