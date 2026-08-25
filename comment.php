<?php
require __DIR__ . '/lib.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }

$slug = basename($_POST['slug'] ?? '');
$post = get_post($slug);
if (!$post) { http_response_code(404); echo 'No such review.'; exit; }

$back = 'post.php?slug=' . rawurlencode($slug);

if (!empty($_POST['website'])) { header('Location: ' . $back . '#comments'); exit; }

$name = trim($_POST['name'] ?? '');
$body = trim($_POST['body'] ?? '');
$name = mb_substr($name, 0, 60);
$body = mb_substr($body, 0, 2000);

if ($name === '' || $body === '') {
  header('Location: ' . $back . '&c=empty#comments'); exit;
}

add_comment($slug, $name, $body);
notify_new_comment($post, $name, $body);

header('Location: ' . $back . '&c=ok#comments');
exit;
