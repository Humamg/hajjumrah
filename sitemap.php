<?php
// ============================================================
// Shahin Travels – Dynamic XML Sitemap (sitemap.php)
// ============================================================
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/config.php';

$db = getDB();

header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Static Pages -->
    <url>
        <loc><?= SITE_URL ?>/</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc><?= SITE_URL ?>/umrah-packages</loc>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc><?= SITE_URL ?>/about-us</loc>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    <url>
        <loc><?= SITE_URL ?>/contact-us</loc>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc><?= SITE_URL ?>/blog</loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc><?= SITE_URL ?>/privacy-policy</loc>
        <changefreq>yearly</changefreq>
        <priority>0.3</priority>
    </url>
    <url>
        <loc><?= SITE_URL ?>/terms-and-conditions</loc>
        <changefreq>yearly</changefreq>
        <priority>0.3</priority>
    </url>
    <url>
        <loc><?= SITE_URL ?>/cancellation-policy</loc>
        <changefreq>yearly</changefreq>
        <priority>0.3</priority>
    </url>

    <!-- Categories -->
    <?php
    $categories = $db->query("SELECT slug, created_at FROM categories WHERE noindex = 0 ORDER BY sort_order ASC")->fetchAll();
    foreach ($categories as $cat):
    ?>
    <url>
        <loc><?= SITE_URL ?>/umrah-packages/category/<?= e($cat['slug']) ?></loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <?php endforeach; ?>

    <!-- Upcoming Months -->
    <?php
    $months = $db->query("
        SELECT DISTINCT DATE_FORMAT(departure_date, '%M-%Y') AS month_slug
        FROM packages
        WHERE status = 'published' AND departure_date >= CURDATE() AND noindex = 0
    ")->fetchAll();
    foreach ($months as $m):
    ?>
    <url>
        <loc><?= SITE_URL ?>/umrah-packages/month/<?= strtolower($m['month_slug']) ?></loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <?php endforeach; ?>

    <!-- Packages -->
    <?php
    $packages = $db->query("
        SELECT slug, updated_at
        FROM packages
        WHERE status = 'published' AND noindex = 0
        ORDER BY updated_at DESC
    ")->fetchAll();
    foreach ($packages as $pkg):
    ?>
    <url>
        <loc><?= SITE_URL ?>/umrah-packages/<?= e($pkg['slug']) ?></loc>
        <lastmod><?= date('Y-m-d', strtotime($pkg['updated_at'])) ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    <?php endforeach; ?>

    <!-- Blog Articles -->
    <?php
    $posts = $db->query("
        SELECT slug, updated_at
        FROM blog_posts
        WHERE status = 'published' AND noindex = 0
        ORDER BY published_at DESC
    ")->fetchAll();
    foreach ($posts as $post):
    ?>
    <url>
        <loc><?= SITE_URL ?>/blog/<?= e($post['slug']) ?></loc>
        <lastmod><?= date('Y-m-d', strtotime($post['updated_at'])) ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    <?php endforeach; ?>
</urlset>
