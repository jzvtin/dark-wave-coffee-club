# Dark Wave Coffee Club

A coffee tasting journal — honest reviews, flavor plots, and a spotlight on Black, Indigenous, women- and minority-owned roasters and cafés. Styled after W.E.B. Du Bois' 1900 Paris Exposition data-viz plots.

Live at **https://darkwavecoffeeclub.com**.

## Stack

Hand-rolled PHP, no framework. Content is stored as flat JSON files — no database. Runs on any PHP 8.1+ host.

## Layout

| File | Purpose |
|------|---------|
| `index.php` | Homepage — hero, latest reviews, equipment + mission bands |
| `reviews.php` | Full archive with a newest/oldest sort toggle |
| `post.php` | A single review — tasting notes, attribute plots, roaster spotlight, comments |
| `equipment.php` | Gear list (affiliate-ready) |
| `about.php` | Contact / about |
| `subscribe.php` | Notification signup handler |
| `comment.php` | Comment POST handler (honeypot-protected) |
| `admin.php` | Password-gated dashboard — publish/edit reviews, upload photos, edit site text |
| `lib.php` | Shared library — styles, page chrome, storage helpers, post renderer |
| `content.php` | Editable site-text registry and the `t()` helper |
| `config.php` | Local secrets (admin password) — **not committed** |
| `assets/` | Brand logos |
| `data/` | Reviews, comments, subscribers, site-text overrides — **not committed** |
| `uploads/` | Review photos — **not committed** |

## Running locally

```
cp config.sample.php config.php     # then set your admin password in config.php
php -S 127.0.0.1:8000
```

Open http://127.0.0.1:8000. The `data/` and `uploads/` folders are created on first write.

## Admin

Visit `/admin.php` and log in with the password from `config.php`. From there you can:

- Publish, edit, and delete reviews, with up to three photos each.
- Edit every fixed string on the site under **Site Text** — hero, headings, mission copy, notify strip, footer, page intros, and more. A blank field falls back to the built-in default, so the site can never break from an empty box.

The login is rate-limited and all write actions are CSRF-protected.

## Editing site text in code

Fixed copy lives in `content.php` as a registry of `key => [label, default, type]`, rendered on each page with `t('key')`. To make a new string editable, add a key to `site_text_registry()` and swap the literal in the template for `<?= t('your_key') ?>`.

## Equipment list

Gear items are defined as an array at the top of `equipment.php`. Each item takes a `type`, `name`, `note`, optional `price`, optional affiliate `url`, and optional `img` (a file in `assets/gear/`). The Buy button renders only when a `url` is set, with `rel="sponsored nofollow noopener"`.

## Deploying

Copy the PHP files, `assets/`, and `config.php` to the web root over SFTP. `data/` and `uploads/` persist on the server and are never overwritten by a deploy.
