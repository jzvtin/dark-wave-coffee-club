<?php require __DIR__ . '/lib.php';
$msg = 'Something went wrong.'; $ok = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
    if (!is_dir(DATA_DIR)) mkdir(DATA_DIR, 0755, true);
    $file = DATA_DIR . '/subscribers.txt';
    $existing = is_file($file) ? file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
    $emails = array_map(fn($l) => explode("\t", $l)[0], $existing);
    if (in_array(strtolower($email), array_map('strtolower', $emails))) {
      $ok = true; $msg = "You're already on the list — see you soon.";
    } else {
      file_put_contents($file, $email . "\t" . date('c') . "\n", FILE_APPEND | LOCK_EX);
      $ok = true; $msg = "You're in! We'll send the next review your way.";
    }
  } else {
    $msg = 'That email did not look right — try again.';
  }
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Subscribed — Dark Wave Coffee Club</title>
<?php dg_css(); ?>
</head>
<body>
<?php dg_staging('Notification signup handler — saves emails to data/subscribers.txt (staging store).'); ?>
<?php dg_header(); ?>

<div class="masthead"><div class="wrap">
  <div class="kicker"><?= $ok ? 'Thank You' : 'Hmm' ?></div>
  <h1><?= $ok ? 'You&rsquo;re Subscribed' : 'Try Again' ?></h1>
  <p><?= e($msg) ?></p>
</div></div>

<div class="wrap">
  <div class="empty">
    <a href="index.php" style="color:var(--red)"><b>&larr; Back to the reviews</b></a>
  </div>
</div>

<?php dg_footer(); ?>
</body>
</html>
