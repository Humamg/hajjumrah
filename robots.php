<?php
// ============================================================
// Shahin Travels – Dynamic robots.txt (robots.php)
// ============================================================
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/config.php';

header('Content-Type: text/plain; charset=utf-8');

$customRobots = getSetting('robots_txt', '');

if (!empty($customRobots)) {
    echo $customRobots;
} else {
    echo "User-agent: *\n";
    echo "Disallow: /admin/\n";
    echo "Disallow: /admin\n";
    echo "Allow: /\n\n";
    echo "Sitemap: " . SITE_URL . "/sitemap.xml\n";
}
