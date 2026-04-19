<!doctype html>
<html lang="<?= htmlspecialchars(hs_locale(), ENT_QUOTES, 'UTF-8') ?>" dir="<?= hs_is_rtl() ? 'rtl' : 'ltr' ?>">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($settings['site_title'] ?? 'NEWS HDSPTV') ?></title>
  <meta name="description" content="<?= htmlspecialchars($settings['seo_meta_description'] ?? ($settings['tagline'] ?? '')) ?>">
  <meta name="robots" content="index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="canonical" href="<?= htmlspecialchars(hs_base_url('/')) ?>">
  <?= hs_hreflang_links('/') ?>
  <link rel="sitemap" type="application/xml" title="Sitemap" href="<?= htmlspecialchars(hs_base_url('sitemap.xml')) ?>">
  <link rel="stylesheet" href="<?= hs_base_url('assets/css/style.css') ?>">
  <?= hs_pwa_head_tags() ?>
  <script defer src="<?= hs_base_url('assets/js/pwa.js') ?>"></script>
  <script defer src="<?= hs_base_url('assets/js/localized-datetime.js') ?>"></script>
  <script defer src="<?= hs_base_url('assets/js/pwa-notifications.js') ?>"></script>
  <script type="application/ld+json">
  {
    "@context":"https://schema.org",
    "@type":"WebSite",
    "name": <?= json_encode($settings['site_title'] ?? 'NEWS HDSPTV') ?>,
    "url": <?= json_encode(hs_base_url('/')) ?>,
    "inLanguage": <?= json_encode(hs_locale()) ?>,
    "potentialAction": {
      "@type": "SearchAction",
      "target": <?= json_encode(hs_base_url('search/{search_term_string}')) ?>,
      "query-input": "required name=search_term_string"
    }
  }
  </script>
</head>
<body>
<?php
  $safePosts = is_array($posts ?? null) ? $posts : [];
  $safeFeatured = is_array($featured ?? null) ? $featured : [];
  $safeTrending = is_array($trending ?? null) ? $trending : [];
  $safeBreaking = is_array($breaking ?? null) ? $breaking : [];
  $safeVideos = is_array($video_posts ?? null) ? $video_posts : [];
  $safeGallery = is_array($gallery_posts ?? null) ? $gallery_posts : [];
  $homepageSections = is_array($homepage_sections ?? null) ? $homepage_sections : hs_homepage_sections($settings ?? []);
  $sidebarAd = is_array($sidebar_ad ?? null) ? $sidebar_ad : null;

  $lead = $safeFeatured[0] ?? ($safePosts[0] ?? null);
  $headlineDeck = array_slice($safePosts, 1, 4);
  $secondary = array_slice($safeFeatured ?: $safePosts, 1, 4);
  $latest = array_slice($safePosts, 0, 9);
  $editorsPicks = array_slice($safePosts, 9, 5);
  $mostViewed = array_slice($safeTrending ?: $safePosts, 0, 6);

  $grouped = [];
  foreach ($safePosts as $row) {
      $key = $row['category_name'] ?? 'News';
      if (!isset($grouped[$key])) $grouped[$key] = [];
      $grouped[$key][] = $row;
  }
  $topCategories = array_slice(array_keys($grouped), 0, 3);

  $formatDate = static function(array $row): string {
      $date = $row['created_at'] ?? null;
      if (!$date) return 'Latest update';
      $ts = strtotime((string)$date);
      return $ts ? date('M j, Y · g:i A', $ts) : 'Latest update';
  };

  $relativeTime = static function(array $row): string {
      $date = $row['created_at'] ?? null;
      $ts = $date ? strtotime((string)$date) : false;
      if (!$ts) {
          return 'Recently updated';
      }

      $seconds = max(60, time() - $ts);
      if ($seconds < 3600) {
          return (string)floor($seconds / 60) . ' min ago';
      }
      if ($seconds < 86400) {
          return (string)floor($seconds / 3600) . ' hrs ago';
      }
      return (string)floor($seconds / 86400) . ' days ago';
  };

  $articleLink = static function(array $row): string {
      return hs_post_url($row['slug'] ?? '');
  };
?>

<div class="top-strip">
  <div class="container top-strip-inner">
    <span data-localized-datetime><?= date('l, F j, Y · g:i A') ?></span>
    <span class="divider-dot">•</span>
    <span><?= htmlspecialchars(hs_t('global_edition')) ?></span>
    <span class="divider-dot">•</span>
    <span style="color:var(--success); font-weight:700;">● <?= htmlspecialchars(hs_t('live_desk_active')) ?></span>
    <span class="divider-dot">•</span>
    <label class="sr-only" for="datetime-timezone">Timezone</label>
    <select id="datetime-timezone" class="datetime-select" data-timezone-override>
      <option value="auto">Timezone: Auto</option>
    </select>
    <label class="sr-only" for="datetime-locale">Format locale</label>
    <select id="datetime-locale" class="datetime-select" data-locale-override>
      <option value="auto">Format: Auto</option>
      <option value="en-US">English (US)</option>
      <option value="en-GB">English (UK)</option>
      <option value="ml-IN">Malayalam</option>
      <option value="hi-IN">Hindi</option>
      <option value="ar-AE">Arabic</option>
    </select>
  </div>
</div>

<header class="site-header sticky-header">
  <div class="container nav-shell">
    <a class="brand" href="<?= hs_route('home') ?>">
      <span class="brand-mark" style="background:var(--primary)">H</span>
      <span>
        <strong style="color:var(--navy); font-size:22px;"><?= htmlspecialchars($settings['site_title'] ?? 'HDSPTV') ?></strong>
        <small style="text-transform:uppercase; letter-spacing:0.05em; font-weight:700;"><?= htmlspecialchars(hs_t('international_news_network')) ?></small>
      </span>
    </a>
    <button class="mobile-menu-btn" data-nav-toggle aria-controls="top-nav" aria-expanded="false">Menu</button>

    <nav class="top-nav" id="top-nav" data-top-nav aria-label="Main navigation">
      <a href="<?= hs_route('home') ?>" class="active"><?= htmlspecialchars(hs_t('home')) ?></a>
      <a href="<?= hs_category_url('india') ?>"><?= htmlspecialchars(hs_t('india')) ?></a>
      <a href="<?= hs_category_url('gcc') ?>"><?= htmlspecialchars(hs_t('gcc')) ?></a>
      <a href="<?= hs_category_url('world') ?>"><?= htmlspecialchars(hs_t('world')) ?></a>
      <a href="<?= hs_category_url('sports') ?>"><?= htmlspecialchars(hs_t('sports')) ?></a>
      <a class="live-btn" href="<?= hs_route('live') ?>"><?= htmlspecialchars(hs_t('live_tv')) ?></a>
    </nav>

    <form class="search-inline" method="get" action="<?= hs_route('search') ?>">
      <input type="text" name="q" placeholder="<?= htmlspecialchars(hs_t('search_stories')) ?>" aria-label="<?= htmlspecialchars(hs_t('search_stories')) ?>">
    </form>

    <div class="header-utils">
      <form method="get" class="lang-form" action="<?= hs_route('home') ?>">
        <label class="sr-only" for="language-picker">Language</label>
        <select id="language-picker" name="lang" aria-label="Language selector" class="lang-selector" onchange="this.form.submit()">
          <?php foreach (hs_available_locales() as $code => $label): ?>
            <option value="<?= htmlspecialchars($code) ?>" <?= hs_locale() === $code ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
          <?php endforeach; ?>
        </select>
      </form>
      <a href="<?= hs_route('auth_login') ?>" style="font-weight:600; color:var(--text);"><?= htmlspecialchars(hs_t('login')) ?></a>
      <a href="<?= hs_route('admin_login') ?>" class="btn" style="height:40px; background:var(--navy); color:#fff; border-radius:8px;">Admin</a>
    </div>
  </div>
</header>

<?php if (!empty($homepageSections['breaking'])): ?>
<section class="breaking-strip">
  <div class="container breaking-inner ticker-track">
    <span class="badge badge-breaking" style="background:#fff; color:var(--primary); font-weight:900;">BREAKING</span>
    <?php if (empty($safeBreaking)): ?>
      <span class="breaking-item">No active breaking headlines at this hour.</span>
    <?php else: ?>
      <?php foreach (array_slice($safeBreaking, 0, 8) as $b): ?>
        <span class="breaking-item"><?= htmlspecialchars($b['title'] ?? '') ?></span>
        <span class="divider-dot" style="color:rgba(255,255,255,0.4)">•</span>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>
<?php endif; ?>

<main class="page-bg">
  <div class="container grid-12 main-home">
    <section class="col-8 col-md-12 stack-32">
      <section class="hero-card newspaper-hero">
        <div class="newspaper-masthead">
          <span>Sunday Edition</span>
          <strong><?= htmlspecialchars($settings['site_title'] ?? 'HDSPTV') ?> Daily</strong>
          <span data-localized-datetime><?= date('F j, Y') ?></span>
        </div>
        <div class="newspaper-lead-grid">
          <article class="newspaper-lead-main">
            <?php if ($lead && !empty($lead['image_main'])): ?>
              <img class="hero-media" src="<?= hs_base_url($lead['image_main']) ?>" alt="<?= htmlspecialchars($lead['title']) ?>">
            <?php endif; ?>
            <div class="hero-content newspaper-lead-content">
              <span class="badge newspaper-kicker"><?= htmlspecialchars($lead['category_name'] ?? 'Top Story') ?></span>
              <h1 class="newspaper-headline"><?= htmlspecialchars($lead['title'] ?? 'Welcome to HDSPTV') ?></h1>
              <p class="newspaper-summary"><?= htmlspecialchars($lead['excerpt'] ?? ($settings['tagline'] ?? 'Trusted global coverage from HDSPTV newsroom.')) ?></p>
              <?php if ($lead): ?>
                <div class="newspaper-lead-footer">
                  <div class="meta"><?= $formatDate($lead) ?></div>
                  <a class="btn btn-primary" href="<?= $articleLink($lead) ?>" style="border-radius:8px; padding:0 24px;">Read full story</a>
                </div>
              <?php endif; ?>
            </div>
          </article>
          <aside class="newspaper-lead-side">
            <?php foreach ($headlineDeck as $deckItem): ?>
              <article class="headline-teaser">
                <span class="meta"><?= htmlspecialchars($deckItem['category_name'] ?? 'News') ?></span>
                <h3><a href="<?= $articleLink($deckItem) ?>"><?= htmlspecialchars($deckItem['title']) ?></a></h3>
                <div class="meta"><?= $relativeTime($deckItem) ?></div>
              </article>
            <?php endforeach; ?>
            <?php if (empty($headlineDeck)): ?>
              <article class="headline-teaser">
                <h3>More stories coming soon.</h3>
              </article>
            <?php endif; ?>
          </aside>
        </div>
      </section>
      <?php if (!empty($secondary)): ?>
      <section class="newswire-strip panel">
        <?php foreach (array_slice($secondary, 0, 3) as $item): ?>
          <article>
            <span><?= htmlspecialchars($item['category_name'] ?? 'News') ?></span>
            <a href="<?= $articleLink($item) ?>"><?= htmlspecialchars($item['title']) ?></a>
          </article>
        <?php endforeach; ?>
      </section>
      <?php endif; ?>

      <?php if (!empty($homepageSections['featured'])): ?>
      <section>
        <div class="section-head"><h2 style="color:var(--navy);">Featured Stories</h2></div>
        <div class="card-grid card-grid-2">
          <?php foreach ($secondary as $item): ?>
            <article class="news-card">
              <span class="meta" style="color:var(--primary); font-weight:700; text-transform:uppercase; font-size:11px;"><?= htmlspecialchars($item['category_name'] ?? 'News') ?></span>
              <h3 style="font-size:24px;"><a href="<?= $articleLink($item) ?>"><?= htmlspecialchars($item['title']) ?></a></h3>
              <p style="color:var(--text);"><?= htmlspecialchars($item['excerpt'] ?? 'Coverage from the HDSPTV editorial desk.') ?></p>
              <div class="meta" style="margin-top:auto; padding-top:12px; border-top:1px solid var(--border);"><?= $formatDate($item) ?></div>
            </article>
          <?php endforeach; ?>
        </div>
      </section>
      <?php endif; ?>

      <section>
        <div class="section-head"><h2 style="color:var(--navy);">Latest News</h2></div>
        <div class="card-grid card-grid-3 latest-news-grid">
          <?php foreach ($latest as $index => $item): ?>
            <article class="news-card news-card-compact latest-news-card">
              <?php if (!empty($item['image_main'])): ?>
                <img class="latest-news-image" src="<?= hs_base_url($item['image_main']) ?>" alt="<?= htmlspecialchars($item['title']) ?>">
              <?php endif; ?>
              <div class="latest-news-meta">
                <span class="news-pill"><?= htmlspecialchars($item['category_name'] ?? 'News') ?></span>
                <span class="news-age"><?= $relativeTime($item) ?></span>
              </div>
              <h3 class="latest-news-title"><a href="<?= $articleLink($item) ?>"><?= htmlspecialchars($item['title']) ?></a></h3>
              <div class="meta latest-news-footer">
                <span><?= $formatDate($item) ?></span>
                <?php if ($index < 3): ?><span class="latest-news-priority">Top Update</span><?php endif; ?>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </section>

      <section>
        <div class="section-head"><h2 style="color:var(--navy);">Category Blocks</h2></div>
        <div class="card-grid card-grid-3">
          <?php foreach ($topCategories as $cat): ?>
            <article class="panel" style="padding:24px;">
              <h3 style="font-size:20px; border-bottom:2px solid var(--primary); display:inline-block; margin-bottom:20px; padding-bottom:4px;"><?= htmlspecialchars($cat) ?></h3>
              <ul class="list-clean">
                <?php foreach (array_slice($grouped[$cat], 0, 3) as $row): ?>
                  <li>
                    <a href="<?= $articleLink($row) ?>" style="color:var(--text-dark); font-weight:700;"><?= htmlspecialchars($row['title']) ?></a>
                    <div class="meta" style="margin-top:4px; font-size:12px;"><?= $formatDate($row) ?></div>
                  </li>
                <?php endforeach; ?>
              </ul>
              <a href="<?= hs_category_url(hs_content_slugify($cat)) ?>" style="display:inline-block; margin-top:16px; color:var(--primary); font-weight:700; font-size:13px; text-transform:uppercase;">View all in <?= htmlspecialchars($cat) ?> →</a>
            </article>
          <?php endforeach; ?>
        </div>
      </section>

      <?php if (!empty($homepageSections['gallery'])): ?>
      <section>
        <div class="section-head"><h2><a href="<?= hs_route('gallery') ?>">Photo Gallery</a></h2></div>
        <div class="card-grid card-grid-3">
          <?php foreach (array_slice($safeGallery, 0, 6) as $item): ?>
            <article class="news-card news-card-compact">
              <?php if (!empty($item['image_main'])): ?><img src="<?= hs_base_url($item['image_main']) ?>" alt="<?= htmlspecialchars($item['title']) ?>"><?php endif; ?>
              <h3><a href="<?= $articleLink($item) ?>"><?= htmlspecialchars($item['title']) ?></a></h3>
              <div class="meta"><?= $formatDate($item) ?></div>
            </article>
          <?php endforeach; ?>
          <?php if (empty($safeGallery)): ?>
            <article class="panel">
              <h3>Photo Gallery</h3>
              <p class="meta">No galleries are published yet.</p>
            </article>
          <?php endif; ?>
        </div>
      </section>
      <?php endif; ?>

      <section class="live-promo">
        <h2>Watch HDSPTV Live</h2>
        <p>Follow live programs, breaking updates, and rolling coverage from our international desk.</p>
        <a class="btn btn-primary" href="<?= hs_route('live') ?>">Open Live TV</a>
      </section>
    </section>

    <aside class="col-4 col-md-12 stack-24">
      <?php if (!empty($homepageSections['trending'])): ?>
      <section class="panel" style="border-top: 4px solid var(--primary);">
        <div class="section-head" style="margin-bottom:20px;"><h2 style="font-size:20px; border:0; padding:0; color:var(--navy);">Trending Now</h2></div>
        <ul class="list-clean">
          <?php foreach (array_slice($safeTrending, 0, 6) as $index => $item): ?>
            <li style="display:flex; gap:16px; align-items:flex-start;">
              <span style="font-size:24px; font-weight:900; color:var(--border); line-height:1;"><?= $index + 1 ?></span>
              <div>
                <a href="<?= $articleLink($item) ?>" style="color:var(--text-dark); font-weight:700; line-height:1.4; display:block;"><?= htmlspecialchars($item['title']) ?></a>
                <div class="meta" style="margin-top:4px;"><?= htmlspecialchars($item['category_name'] ?? 'News') ?></div>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      </section>
      <?php endif; ?>

      <section class="panel" style="background:var(--navy); color:#fff; border:0;">
        <div class="section-head" style="margin-bottom:20px;"><h2 style="color:#fff; font-size:20px; border:0; padding:0;">Most Viewed</h2></div>
        <ul class="list-clean">
          <?php foreach ($mostViewed as $item): ?>
            <li style="border-color:rgba(255,255,255,0.1);">
              <a href="<?= $articleLink($item) ?>" style="color:#fff; font-weight:600;"><?= htmlspecialchars($item['title']) ?></a>
            </li>
          <?php endforeach; ?>
        </ul>
      </section>

      <?php if (!empty($homepageSections['video'])): ?>
      <section class="panel">
        <div class="section-head" style="margin-bottom:20px;"><h2 style="font-size:20px; border:0; padding:0; color:var(--navy);">Video News</h2></div>
        <ul class="list-clean">
          <?php foreach (array_slice($safeVideos, 0, 4) as $item): ?>
            <li style="display:flex; gap:12px; align-items:center;">
              <div style="width:10px; height:10px; border-radius:50%; background:var(--primary);"></div>
              <a href="<?= $articleLink($item) ?>" style="color:var(--text-dark); font-weight:700;"><?= htmlspecialchars($item['title']) ?></a>
            </li>
          <?php endforeach; ?>
        </ul>
        <a href="<?= hs_route('video') ?>" style="display:inline-block; margin-top:16px; color:var(--primary); font-weight:700; font-size:13px; text-transform:uppercase;">More Videos →</a>
      </section>
      <?php endif; ?>

      <?php if (!empty($homepageSections['ads_sidebar'])): ?>
      <section class="panel">
        <div class="section-head"><h2>Sponsored</h2></div>
        <?php if (!empty($sidebarAd['image_url'])): ?>
          <a href="<?= htmlspecialchars($sidebarAd['link_url'] ?: '#') ?>" target="_blank" rel="noopener sponsored">
            <img src="<?= htmlspecialchars($sidebarAd['image_url']) ?>" alt="Sponsored content">
          </a>
        <?php else: ?>
          <p class="meta">Sidebar ads are enabled, but no ad is configured yet.</p>
        <?php endif; ?>
      </section>
      <?php endif; ?>

      <section class="panel newsletter" style="background:var(--bg); border: 2px dashed var(--border);">
        <div class="section-head" style="margin-bottom:12px;"><h2 style="font-size:20px; border:0; padding:0; color:var(--navy);">Newsletter</h2></div>
        <p class="meta" style="margin-bottom:20px; line-height:1.4;">Get top headlines and breaking updates delivered daily to your inbox.</p>
        <form class="newsletter-form" method="post" action="#">
          <input type="email" placeholder="Enter your email address" aria-label="Email" style="border-radius:8px; border:1px solid var(--border);">
          <button class="btn btn-primary" type="submit" style="border-radius:8px; font-weight:800;">SUBSCRIBE NOW</button>
        </form>
      </section>
    </aside>
  </div>
</main>

<footer class="site-footer" style="background:var(--navy); padding:64px 0 32px; border-top: 8px solid var(--primary);">
  <div class="container">
    <div class="footer-row" style="align-items: flex-start; margin-bottom:48px;">
      <div style="max-width:400px;">
        <strong style="font-size:24px; color:#fff; display:block; margin-bottom:12px;"><?= htmlspecialchars($settings['site_title'] ?? 'HDSPTV') ?></strong>
        <p class="meta" style="color:rgba(255,255,255,0.6); font-size:14px; line-height:1.6;"><?= htmlspecialchars($settings['tagline'] ?? 'Comprehensive international newsroom coverage delivering real-time updates across India, GCC, Kerala and the global landscape.') ?></p>
      </div>
      <div class="footer-links" style="display:grid; grid-template-columns: 1fr 1fr; gap:16px 40px;">
        <a href="<?= hs_route('home') ?>" style="font-weight:600;">Home</a>
        <a href="<?= hs_route('about') ?>" style="font-weight:600;">About Us</a>
        <a href="<?= hs_route('contact') ?>" style="font-weight:600;">Contact</a>
        <a href="<?= hs_route('profile') ?>" style="font-weight:600;">Account</a>
        <a href="<?= hs_route('trending') ?>" style="font-weight:600;">Trending</a>
        <a href="<?= hs_route('video') ?>" style="font-weight:600;">Videos</a>
      </div>
    </div>
    <div style="border-top:1px solid rgba(255,255,255,0.1); padding-top:32px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
      <p class="meta" style="color:rgba(255,255,255,0.4); font-size:12px;">&copy; <?= date('Y') ?> <?= htmlspecialchars($settings['site_title'] ?? 'HDSPTV') ?>. All rights reserved. Professional News Network.</p>
      <div style="display:flex; gap:20px;">
        <a href="<?= hs_route('admin_login') ?>" style="color:rgba(255,255,255,0.4); font-size:12px; font-weight:700;">EDITORIAL LOGIN</a>
      </div>
    </div>
  </div>
</footer>
</body>
</html>
