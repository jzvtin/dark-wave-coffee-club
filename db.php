<?php

function sb_enabled() {
  return defined('SUPABASE_URL') && defined('SUPABASE_KEY') && SUPABASE_URL !== '' && SUPABASE_KEY !== '';
}

function sb_req($method, $path, $body = null, $prefer = null) {
  $ch = curl_init(SUPABASE_URL . '/rest/v1/' . $path);
  $headers = [
    'apikey: ' . SUPABASE_KEY,
    'Authorization: Bearer ' . SUPABASE_KEY,
    'Content-Type: application/json',
    'Accept: application/json',
  ];
  if ($prefer) $headers[] = 'Prefer: ' . $prefer;
  $opts = [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => $method,
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_TIMEOUT => 15,
    CURLOPT_CONNECTTIMEOUT => 8,
  ];
  if ($body !== null) $opts[CURLOPT_POSTFIELDS] = json_encode($body);
  curl_setopt_array($ch, $opts);
  $res = curl_exec($ch);
  $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  $data = ($res === false || $res === '') ? null : json_decode($res, true);
  return [$code, $data];
}

function sb_ok($code) { return $code >= 200 && $code < 300; }

function sb_cache_path($name) { return DATA_DIR . '/cache_' . $name . '.json'; }
function sb_cache_write($name, $data) {
  if (!is_dir(DATA_DIR)) @mkdir(DATA_DIR, 0755, true);
  @file_put_contents(sb_cache_path($name), json_encode($data), LOCK_EX);
}
function sb_cache_read($name) {
  $j = @file_get_contents(sb_cache_path($name));
  $d = $j ? json_decode($j, true) : null;
  return is_array($d) ? $d : null;
}

const SB_POST_COLS = ['slug','title','roaster','roaster_url','roaster_bio','ceramicist','ceramicist_bio',
  'sub','est','region','varietal','process','roast','tasting_notes','experience','method',
  'aroma','quick_take','acidity','body','aftertaste','photos','created','updated'];

function sb_shape_post($p) {
  $out = [];
  foreach (SB_POST_COLS as $c) {
    $v = $p[$c] ?? null;
    if ($c === 'photos') $v = array_values($v ?? []);
    elseif ($c === 'roast' || $c === 'created') $v = (int)($v ?? 0);
    elseif ($c === 'updated') $v = ($v === null || $v === '') ? null : (int)$v;
    else $v = ($v === null) ? '' : (string)$v;
    $out[$c] = $v;
  }
  return $out;
}

function sb_all_posts() {
  [$code, $data] = sb_req('GET', 'posts?select=*&order=created.desc');
  if (sb_ok($code) && is_array($data)) { sb_cache_write('posts', $data); return $data; }
  $cached = sb_cache_read('posts');
  return $cached ?? [];
}

function sb_get_post($slug) {
  $q = 'posts?select=*&slug=eq.' . rawurlencode($slug) . '&limit=1';
  [$code, $data] = sb_req('GET', $q);
  if (sb_ok($code) && is_array($data)) return $data[0] ?? null;
  $cached = sb_cache_read('posts');
  if ($cached) foreach ($cached as $p) if (($p['slug'] ?? '') === $slug) return $p;
  return null;
}

function sb_save_post($p) {
  $row = sb_shape_post($p);
  [$code] = sb_req('POST', 'posts', $row, 'resolution=merge-duplicates,return=minimal');
  return sb_ok($code);
}

function sb_delete_post($slug) {
  sb_req('DELETE', 'posts?slug=eq.' . rawurlencode($slug), null, 'return=minimal');
}

function sb_get_comments($slug) {
  $q = 'comments?select=name,body,created&slug=eq.' . rawurlencode($slug) . '&order=created.asc';
  [$code, $data] = sb_req('GET', $q);
  return (sb_ok($code) && is_array($data)) ? $data : [];
}

function sb_add_comment($slug, $name, $body) {
  sb_req('POST', 'comments', ['slug' => $slug, 'name' => $name, 'body' => $body, 'created' => time()], 'return=minimal');
}

function sb_site_text_all() {
  [$code, $data] = sb_req('GET', 'site_text?select=key,value');
  if (sb_ok($code) && is_array($data)) {
    $map = [];
    foreach ($data as $r) $map[$r['key']] = $r['value'];
    sb_cache_write('site_text', $map);
    return $map;
  }
  return sb_cache_read('site_text') ?? [];
}

function sb_save_site_text($map) {
  sb_req('DELETE', 'site_text?key=neq.__none__', null, 'return=minimal');
  if ($map) {
    $rows = [];
    foreach ($map as $k => $v) $rows[] = ['key' => $k, 'value' => $v];
    sb_req('POST', 'site_text', $rows, 'return=minimal');
  }
  sb_cache_write('site_text', $map);
}

function sb_subscriber_exists($email) {
  $q = 'subscribers?select=email&email=eq.' . rawurlencode(strtolower($email)) . '&limit=1';
  [$code, $data] = sb_req('GET', $q);
  return sb_ok($code) && is_array($data) && count($data) > 0;
}

function sb_add_subscriber($email) {
  sb_req('POST', 'subscribers', ['email' => strtolower($email)], 'return=minimal');
}
