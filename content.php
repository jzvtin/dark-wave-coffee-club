<?php

define('SITE_TEXT_FILE', DATA_DIR . '/site_text.json');

function site_text_registry() {
  return [
    'Header & Navigation' => [
      'brand_top'      => ['Brand — line 1',            'Dark Wave',   'text'],
      'brand_bottom'   => ['Brand — line 2 (gold)',     'Coffee Club', 'text'],
      'nav_home'       => ['Nav link: Home',            'Home',        'text'],
      'nav_reviews'    => ['Nav link: Reviews',         'Reviews',     'text'],
      'nav_equipment'  => ['Nav link: Equipment',       'Equipment',   'text'],
      'nav_contact'    => ['Nav link: Contact',         'Contact',     'text'],
      'nav_instagram'  => ['Nav link: Instagram',       'Instagram',   'text'],
    ],

    'Homepage — Hero' => [
      'hero_kicker'    => ['Hero kicker (small top line)', 'A Coffee Tasting Journal', 'text'],
      'hero_title_1'   => ['Hero headline — white part',   'Be inspired,',             'text'],
      'hero_title_2'   => ['Hero headline — gold part',    'not influenced.',          'text'],
      'hero_sub'       => ['Hero subtitle', 'Honest tasting notes & flavor plots — spotlighting the roasters worth driving across town for.', 'textarea'],
      'hero_btn_1'     => ['Hero button 1', 'Read the Reviews',    'text'],
      'hero_btn_2'     => ['Hero button 2', 'Follow on Instagram', 'text'],
    ],

    'Homepage — Sections' => [
      'home_latest_head' => ['"Latest Reviews" heading', 'Latest Reviews',  'text'],
      'home_latest_link' => ['"All reviews" link',        'All reviews →',   'text'],
      'home_empty'       => ['Empty-state line (no reviews yet)', 'Reviews are brewing.', 'text'],
      'home_eq_title'    => ['Gear band — title',  'The Gear Behind Every Cup', 'text'],
      'home_eq_body'     => ['Gear band — text',   "The pour-over brewer, grinder, and kettle André actually uses — with honest notes on what's worth buying.", 'textarea'],
      'home_eq_btn'      => ['Gear band — button', 'See My Gear', 'text'],
      'home_mission_title' => ['Mission band — title', 'Mission and Vision', 'text'],
      'home_mission_body1' => ['Mission band — paragraph 1', 'At Dark Wave Coffee Club, we prioritize supporting Black or Indigenous owned, women owned, minority owned, or small businesses, whenever possible and we think you should too. We are intentional about supporting these businesses by paying for their products with our own money. We\'re also intentional about giving our honest opinions on the products listed on the site. If we are ever given free items that are reviewed we\'ll be sure to disclose what the item was with the goal of continuing to provide an unbiased opinion.', 'textarea'],
      'home_mission_body2' => ['Mission band — paragraph 2', 'The Dark Wave Coffee Club pays homage to W.E.B. Du Bois by emulating the style and color schemes of the work he and his team presented at the 1900 Paris Exposition.', 'textarea'],
      'home_mission_btn'   => ['Mission band — button', 'Follow', 'text'],
    ],

    'Notify Strip' => [
      'notify_title'       => ['Title',       'Join the Club', 'text'],
      'notify_body'        => ['Subtext',     "Notifications for new reviews — we'll never sell your contact info.", 'textarea'],
      'notify_placeholder' => ['Email box placeholder', 'you@email.com', 'text'],
      'notify_btn'         => ['Button',      'Notify Me', 'text'],
    ],

    'Footer' => [
      'footer_tagline'   => ['Tagline under brand', 'Be inspired, not influenced.', 'text'],
      'footer_col2_head' => ['Column 2 heading', 'Follow & Reach Out', 'text'],
      'footer_ig_label'  => ['Instagram link label', 'Instagram', 'text'],
      'footer_contact'   => ['Contact link label', 'Contact page', 'text'],
      'footer_col3_head' => ['Column 3 heading', 'Read', 'text'],
      'footer_reviews'   => ['Reviews link label', 'All Reviews', 'text'],
      'footer_equipment' => ['Equipment link label', 'My Equipment', 'text'],
      'footer_about'     => ['About link label', 'About André', 'text'],
    ],

    'Reviews Page' => [
      'reviews_kicker' => ['Kicker', 'The Full Journal', 'text'],
      'reviews_title'  => ['Title',  'All Reviews', 'text'],
      'reviews_sub'    => ['Subtitle', 'Every coffee André has tasted — scroll from newest to oldest.', 'textarea'],
    ],

    'Equipment Page' => [
      'eq_kicker'   => ['Kicker', 'What I Brew With', 'text'],
      'eq_title'    => ['Title',  'My Equipment', 'text'],
      'eq_sub'      => ['Subtitle', 'The pour-over gear, grinder, and kettle behind every cup on this blog.', 'textarea'],
      'eq_aff_note' => ['Affiliate disclosure line', 'Some links below are affiliate links — if you buy through them, André may earn a small commission at no extra cost to you.', 'textarea'],
    ],

    'Contact / About Page' => [
      'about_kicker'       => ['Kicker', 'Say Hello', 'text'],
      'about_title'        => ['Title',  'Contact & About', 'text'],
      'about_sub'          => ['Subtitle', 'Follow along, reach out, or sign up so you never miss a review.', 'textarea'],
      'about_mission_head' => ['Mission box — heading', 'The Mission', 'text'],
      'about_mission_body' => ['Mission box — text', 'This journal spotlights Black, Indigenous, women- and minority-owned roasters, cafés, and small businesses. Every review is a chance to send real customers and follows toward the people making exceptional coffee — and to build a community that lifts them up, one cup at a time.', 'textarea'],
      'about_andre_head'   => ['"Who\'s André?" — heading', "Who's André?", 'text'],
      'about_andre_body1'  => ['"Who\'s André?" — paragraph 1', "I don't consider myself an expert when it comes to coffee, and I'm definitely not an influencer. I created this space because I didn't see myself represented in other blog spaces that did not have the intention to sell me something.", 'textarea'],
      'about_andre_body2'  => ['"Who\'s André?" — paragraph 2', "I consider myself a regular guy who loves coffee, and I wanted to share my experiences. If I give advice on anything, it should be considered a way, not the way. With that said, welcome to the spot! I'll be sampling coffee from different roasters, testing equipment, and sharing my opinions along the way.", 'textarea'],
      'about_touch_head'   => ['"Get in Touch" — heading', 'Get in Touch', 'text'],
      'about_location'     => ['Location value', 'City / region placeholder', 'text'],
    ],

    'Review Post Page' => [
      'post_experience_head' => ['"The Experience" heading', 'The Experience', 'text'],
      'post_attr_title'      => ['Attributes plot — title', 'Attributes', 'text'],
      'post_attr_sub'        => ['Attributes plot — subtitle', "André's palate · relative intensity, not a grade", 'text'],
      'post_crafted_head'    => ['"Crafted by…" heading', 'Crafted by…', 'text'],
      'post_current_head'    => ['"The Current" heading', 'The Current', 'text'],
      'post_current_cta'     => ['"The Current" prompt', '↓ Got thoughts? Drop a comment.', 'text'],
      'post_comments_head'   => ['Comments heading', 'Comments', 'text'],
      'post_comment_none'    => ['No-comments line', 'No comments yet — be the first to weigh in.', 'text'],
      'post_comment_btn'     => ['Comment button', 'Post Comment', 'text'],
      'post_more_head'       => ['"More Reviews" heading', 'More Reviews', 'text'],
    ],
  ];
}

function site_text_defaults() {
  static $flat = null;
  if ($flat !== null) return $flat;
  $flat = [];
  foreach (site_text_registry() as $group) {
    foreach ($group as $k => $meta) $flat[$k] = $meta[1];
  }
  return $flat;
}

function site_text_all() {
  static $cache = null;
  if ($cache !== null) return $cache;
  if (sb_enabled()) { $cache = sb_site_text_all(); return $cache; }
  $j = @file_get_contents(SITE_TEXT_FILE);
  $a = $j ? json_decode($j, true) : [];
  $cache = is_array($a) ? $a : [];
  return $cache;
}

function t($key) {
  $ov  = site_text_all()[$key] ?? '';
  $def = site_text_defaults()[$key] ?? '';
  return e(($ov === '') ? $def : $ov);
}

function save_site_text($input) {
  $out = [];
  foreach (site_text_defaults() as $k => $def) {
    $v = trim((string)($input[$k] ?? ''));
    if ($v !== '' && $v !== $def) $out[$k] = $v;
  }
  if (sb_enabled()) { sb_save_site_text($out); return count($out); }
  if (!is_dir(DATA_DIR)) @mkdir(DATA_DIR, 0755, true);
  file_put_contents(SITE_TEXT_FILE,
    json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    LOCK_EX);
  return count($out);
}
