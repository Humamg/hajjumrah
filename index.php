<?php
// ============================================================
// Shahin Travels – Home Page (index.php)
// ============================================================
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/config.php';

$db = getDB();

// Page data
$pageTitle  = getSetting('meta_title_home', 'Shahin Travels – Hajj & Umrah Packages from India');
$metaDesc   = getSetting('meta_desc_home', 'Book affordable Umrah packages from India with direct flights, comfortable hotels near Haram, and complete assistance.');
$canonicalUrl = SITE_URL . '/';

// Hero
$heroTitle    = getSetting('hero_title', 'Your Spiritual Journey, <span>Our Commitment</span>');
$heroSubtitle = getSetting('hero_subtitle', 'Comfortable Packages | Premium Hotels | Direct Flights');
$heroImage    = getSetting('hero_image', '');

// Trending Packages
$trending = $db->query("
    SELECT p.*, c.name AS cat_name, c.slug AS cat_slug,
           MIN(pp.price) AS min_price,
           (SELECT pp2.price FROM package_prices pp2 WHERE pp2.package_id = p.id AND pp2.sharing_type = 'Double' LIMIT 1) AS double_price
    FROM packages p
    LEFT JOIN categories c ON c.id = p.category_id
    LEFT JOIN package_prices pp ON pp.package_id = p.id
    WHERE p.is_trending = 1 AND p.status = 'published' AND p.departure_date >= CURDATE()
    GROUP BY p.id
    ORDER BY p.departure_date ASC
    LIMIT 4
")->fetchAll();

// Categories
$categories = $db->query("SELECT * FROM categories ORDER BY sort_order ASC")->fetchAll();

// Browse by Month (upcoming months with packages)
$months = $db->query("
    SELECT DATE_FORMAT(departure_date, '%M %Y') AS month_label,
           DATE_FORMAT(departure_date, '%M-%Y') AS month_slug,
           COUNT(*) AS pkg_count
    FROM packages
    WHERE status = 'published' AND departure_date >= CURDATE()
    GROUP BY month_label, month_slug
    ORDER BY departure_date ASC
    LIMIT 6
")->fetchAll();

// Hotels (featured)
$makkahHotels  = $db->query("SELECT * FROM hotels WHERE city = 'Makkah' ORDER BY stars DESC LIMIT 3")->fetchAll();
$madinahHotels = $db->query("SELECT * FROM hotels WHERE city = 'Madinah' ORDER BY stars DESC LIMIT 3")->fetchAll();

// Testimonials
$testimonials = $db->query("SELECT * FROM testimonials WHERE is_active = 1 ORDER BY sort_order, id LIMIT 6")->fetchAll();

// Blog Posts
$blogPosts = $db->query("
    SELECT bp.*, bc.name AS cat_name
    FROM blog_posts bp
    LEFT JOIN blog_categories bc ON bc.id = bp.category_id
    WHERE bp.status = 'published'
    ORDER BY bp.published_at DESC
    LIMIT 4
")->fetchAll();

// WhatsApp
$waNumber  = getSetting('whatsapp', '919911446919');
$waGeneral = getSetting('wa_general_template', 'Assalamualaikum, I would like to enquire about Umrah packages.');
$waUrl     = waUrl($waNumber, $waGeneral);

// Schema
$schema = json_encode([
    '@context' => 'https://schema.org',
    '@type'    => 'WebPage',
    'name'     => $pageTitle,
    'description' => $metaDesc,
    'url'      => $canonicalUrl,
    'breadcrumb' => [
        '@type'           => 'BreadcrumbList',
        'itemListElement' => [['@type'=>'ListItem','position'=>1,'name'=>'Home','item'=>SITE_URL.'/']]
    ]
], JSON_UNESCAPED_SLASHES);

require_once __DIR__ . '/includes/header.php';
?>

<!-- ── Hero ──────────────────────────────────────────── -->
<section class="hero">
    <div class="hero-bg" <?= $heroImage ? 'style="background-image:url(' . e(SITE_URL . '/public/images/' . $heroImage) . ')"' : 'style="background-image:url(https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?w=1920&q=80)"' ?>></div>
    <div class="hero-overlay"></div>
    <div class="container">
        <div class="hero-content">
            <div class="hero-badge">
                🕋 Hajj & Umrah Specialists
            </div>
            <h1><?= $heroTitle ?></h1>
            <p class="hero-sub">
                <?php foreach (explode("\n", $heroSubtitle) as $line): ?>
                <span><?= e(trim($line)) ?></span>
                <?php endforeach; ?>
            </p>
            <div class="hero-actions">
                <a href="<?= SITE_URL ?>/umrah-packages" class="btn btn-primary btn-lg">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Explore Umrah Packages
                </a>
                <a href="<?= e($waUrl) ?>" class="btn btn-wa btn-lg btn-wa-pulse" target="_blank" rel="noopener">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Talk on WhatsApp
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ── USP Strip ─────────────────────────────────────── -->
<div class="usp-strip">
    <div class="container">
        <div class="usp-item">
            <div class="icon">✈️</div>
            <span>Direct Flights<br><small>Saudi Airlines</small></span>
        </div>
        <div class="usp-item">
            <div class="icon">🏨</div>
            <span>Premium Hotels<br><small>Makkah & Madinah</small></span>
        </div>
        <div class="usp-item">
            <div class="icon">📄</div>
            <span>Visa Assistance<br><small>Guaranteed</small></span>
        </div>
        <div class="usp-item">
            <div class="icon">👨‍👩‍👧‍👦</div>
            <span>Group & Family<br><small>Packages</small></span>
        </div>
        <div class="usp-item">
            <div class="icon">📞</div>
            <span>24/7 Support<br><small>Always here</small></span>
        </div>
    </div>
</div>

<!-- ── Trending Packages ──────────────────────────────── -->
<section class="section" id="trending-packages">
    <div class="container">
        <div class="section-header fade-up">
            <span class="label">Our Best Sellers</span>
            <h2>Trending Umrah Packages</h2>
            <p>Explore our most popular Umrah packages with direct flights, comfortable hotels and complete assistance.</p>
            <a href="<?= SITE_URL ?>/umrah-packages" class="view-all">View All Packages →</a>
        </div>
        <div class="packages-grid stagger-children">
            <?php foreach ($trending as $pkg):
                $badgeClass  = 'badge-' . strtolower($pkg['cat_name'] ?? 'budget');
                $minPrice    = $pkg['double_price'] ?? $pkg['min_price'] ?? 0;
                $waMsg       = getSetting('wa_package_template', '');
                $waMsg       = strtr($waMsg, [
                    '{package_name}'   => $pkg['title'],
                    '{departure_date}' => date('d M Y', strtotime($pkg['departure_date'])),
                    '{price}'          => formatINR((float)$minPrice),
                    '{sharing}'        => 'Double'
                ]);
                $pkgWaUrl = waUrl($waNumber, $waMsg);
            ?>
            <div class="package-card fade-up">
                <div class="package-card-image">
                    <?php if ($pkg['cover_image']): ?>
                    <img data-src="<?= e(SITE_URL . '/public/images/packages/' . $pkg['cover_image']) ?>" alt="<?= e($pkg['title']) ?>" loading="lazy">
                    <?php else: ?>
                    <img src="https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?w=600&q=70" alt="<?= e($pkg['title']) ?>">
                    <?php endif; ?>
                    <span class="badge <?= e($badgeClass) ?>"><?= e($pkg['cat_name']) ?></span>
                    <?php if ($pkg['is_trending']): ?><span class="trending-badge">🔥 Trending</span><?php endif; ?>
                </div>
                <div class="package-card-body">
                    <h3><?= e($pkg['title']) ?></h3>
                    <div class="package-meta">
                        <div class="package-meta-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            Makkah <?= $pkg['makkah_nights'] ?>N + Madinah <?= $pkg['madinah_nights'] ?>N
                        </div>
                        <div class="package-meta-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            Departure: <?= date('d M Y', strtotime($pkg['departure_date'])) ?>
                        </div>
                        <div class="package-meta-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                            Double Sharing Available
                        </div>
                    </div>
                </div>
                <div class="package-card-footer">
                    <div>
                        <div class="price-label">Starting from</div>
                        <div class="price-amount"><?= formatINR((float)$minPrice) ?></div>
                        <div class="price-per">per person (Double Sharing)</div>
                    </div>
                    <a href="<?= SITE_URL ?>/umrah-packages/<?= e($pkg['slug']) ?>" class="btn btn-primary btn-sm">View Details →</a>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($trending)): ?>
            <div style="grid-column:1/-1;text-align:center;padding:60px;color:#666;">
                <p>No trending packages available. <a href="<?= SITE_URL ?>/umrah-packages" style="color:var(--green);">View all packages →</a></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ── Explore by Category ────────────────────────────── -->
<section class="section" style="background:var(--gray-100);" id="categories">
    <div class="container">
        <div class="section-header fade-up">
            <span class="label">Find Your Package</span>
            <h2>Explore Packages by Category</h2>
            <p>Choose from Budget to Luxury – we have the perfect Umrah package for every pilgrim.</p>
        </div>
        <?php
        $catImages = [
            'budget'  => 'https://images.unsplash.com/photo-1564769625905-50e93615e769?w=600&q=70',
            'bronze'  => 'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=600&q=70',
            'silver'  => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=600&q=70',
            'gold'    => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=600&q=70',
            'ramadan' => 'https://images.unsplash.com/photo-1519817914152-22d216bb9170?w=600&q=70',
            'luxury'  => 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=600&q=70',
        ];
        $catDesc = [
            'budget'  => 'Affordable packages with essential facilities',
            'bronze'  => 'Better comfort and convenient stay',
            'silver'  => 'Premium hotels with great location',
            'gold'    => 'Best hotels, close to Haram',
            'ramadan' => 'Special Ramadan Umrah packages',
            'luxury'  => 'Ultimate 5-star luxury experience',
        ];
        ?>
        <div class="category-grid stagger-children">
            <?php foreach ($categories as $cat): ?>
            <a href="<?= SITE_URL ?>/umrah-packages/category/<?= e($cat['slug']) ?>" class="category-card fade-up">
                <img src="<?= e($cat['image'] ? SITE_URL . '/public/images/categories/' . $cat['image'] : ($catImages[$cat['slug']] ?? $catImages['budget'])) ?>" alt="<?= e($cat['name']) ?> Umrah Packages" loading="lazy">
                <div class="category-card-overlay"></div>
                <div class="category-card-content">
                    <h3><?= e($cat['name']) ?> Umrah</h3>
                    <p><?= e($cat['description'] ?? ($catDesc[$cat['slug']] ?? '')) ?></p>
                    <span class="btn btn-sm">View Packages →</span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ── Browse by Month ────────────────────────────────── -->
<section class="section" id="browse-month">
    <div class="container">
        <div class="section-header fade-up">
            <span class="label">Plan Ahead</span>
            <h2>Browse Packages by Month</h2>
            <p>Find the best Umrah packages for your preferred travel month.</p>
            <a href="<?= SITE_URL ?>/umrah-packages" class="view-all">View All Months →</a>
        </div>
        <?php
        $monthImages = [
            'October'   => 'https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?w=400&q=70',
            'November'  => 'https://images.unsplash.com/photo-1564769625905-50e93615e769?w=400&q=70',
            'December'  => 'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=400&q=70',
            'January'   => 'https://images.unsplash.com/photo-1519817914152-22d216bb9170?w=400&q=70',
            'February'  => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=400&q=70',
            'March'     => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=400&q=70',
        ];
        ?>
        <div class="month-grid stagger-children">
            <?php foreach ($months as $month):
                [$mName, $mYear] = explode(' ', $month['month_label']) + ['', ''];
                $imgUrl = $monthImages[$mName] ?? $monthImages['October'];
            ?>
            <a href="<?= SITE_URL ?>/umrah-packages/month/<?= e(strtolower($month['month_slug'])) ?>" class="month-card fade-up">
                <img src="<?= e($imgUrl) ?>" alt="<?= e($month['month_label']) ?> Umrah Packages" loading="lazy">
                <div class="month-card-overlay"></div>
                <div class="month-card-content">
                    <span class="month-name"><?= e($month['month_label']) ?></span>
                    <span class="pkg-count">📦 <?= $month['pkg_count'] ?> Package<?= $month['pkg_count'] > 1 ? 's' : '' ?></span>
                </div>
            </a>
            <?php endforeach; ?>
            <?php if (empty($months)): ?>
            <div style="grid-column:1/-1;text-align:center;color:#666;padding:40px;">No upcoming packages available.</div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ── Why Choose Shahin Travels ─────────────────────── -->
<section class="section" style="background:var(--gray-100);" id="why-choose">
    <div class="container">
        <div class="section-header fade-up">
            <span class="label">Our Promise</span>
            <h2>Why Choose Shahin Travels?</h2>
            <p>Trusted by thousands of pilgrims from across India for a spiritually fulfilling and comfortable Umrah experience.</p>
        </div>
        <div class="trust-grid stagger-children">
            <div class="trust-item fade-up">
                <div class="trust-icon">🏆</div>
                <span class="count" data-count="10" data-suffix="+">10+</span>
                <h4>Years Experience</h4>
                <p>Serving pilgrims from India with trusted services for over a decade.</p>
            </div>
            <div class="trust-item fade-up">
                <div class="trust-icon">😊</div>
                <span class="count" data-count="5000" data-suffix="+">5000+</span>
                <h4>Happy Travellers</h4>
                <p>Families and groups trust our services every year for a blessed journey.</p>
            </div>
            <div class="trust-item fade-up">
                <div class="trust-icon">✅</div>
                <span class="count" data-count="100" data-suffix="%">100%</span>
                <h4>Reliable Service</h4>
                <p>Transparent packages with honest pricing and genuine hotel distances.</p>
            </div>
            <div class="trust-item fade-up">
                <div class="trust-icon">📞</div>
                <span class="count" data-count="24" data-suffix="/7">24/7</span>
                <h4>Support</h4>
                <p>Our team is always here to assist you before, during and after your journey.</p>
            </div>
        </div>
    </div>
</section>

<!-- ── Hotels Near Haram ──────────────────────────────── -->
<section class="section hotel-section" id="hotels">
    <div class="container">
        <div class="section-header fade-up">
            <span class="label" style="background:rgba(255,255,255,0.1);color:var(--gold-light);">Comfortable Stay</span>
            <h2 style="color:var(--white);">Comfortable Stay Near Haram in Makkah & Madinah</h2>
            <p style="color:rgba(255,255,255,0.7);">We work with well-reputed hotels in Makkah and Madinah to ensure a comfortable and peaceful stay for our pilgrims.</p>
        </div>
        <div class="hotel-tabs fade-up">
            <button class="hotel-tab active" data-city="Makkah">🕋 Makkah Hotels</button>
            <button class="hotel-tab" data-city="Madinah">🕌 Madinah Hotels</button>
        </div>
        <!-- Makkah -->
        <div class="hotels-grid hotel-panel" data-city="Makkah">
            <?php foreach ($makkahHotels as $hotel): ?>
            <div class="hotel-card fade-in">
                <div class="hotel-card-image">
                    <img src="<?= $hotel['image'] ? e(SITE_URL . '/public/images/hotels/' . $hotel['image']) : 'https://images.unsplash.com/photo-1564769625905-50e93615e769?w=500&q=70' ?>" alt="<?= e($hotel['name']) ?>" loading="lazy">
                </div>
                <div class="hotel-card-body">
                    <h4><?= e($hotel['name']) ?></h4>
                    <div class="hotel-stars"><?= str_repeat('★', $hotel['stars']) ?><?= str_repeat('☆', 5 - $hotel['stars']) ?></div>
                    <div class="hotel-meta">
                        <span>📍 <?= $hotel['distance_haram'] ?>m from Haram</span>
                        <span>🏙️ <?= e($hotel['city']) ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <!-- Madinah -->
        <div class="hotels-grid hotel-panel" data-city="Madinah" style="display:none;">
            <?php foreach ($madinahHotels as $hotel): ?>
            <div class="hotel-card fade-in">
                <div class="hotel-card-image">
                    <img src="<?= $hotel['image'] ? e(SITE_URL . '/public/images/hotels/' . $hotel['image']) : 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=500&q=70' ?>" alt="<?= e($hotel['name']) ?>" loading="lazy">
                </div>
                <div class="hotel-card-body">
                    <h4><?= e($hotel['name']) ?></h4>
                    <div class="hotel-stars"><?= str_repeat('★', $hotel['stars']) ?><?= str_repeat('☆', 5 - $hotel['stars']) ?></div>
                    <div class="hotel-meta">
                        <span>📍 <?= $hotel['distance_haram'] ?>m from Nabawi</span>
                        <span>🏙️ <?= e($hotel['city']) ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ── Testimonials ───────────────────────────────────── -->
<?php if (!empty($testimonials)): ?>
<section class="section" id="testimonials" style="background:var(--off-white);">
    <div class="container">
        <div class="section-header fade-up">
            <span class="label">Happy Pilgrims</span>
            <h2>What Our Customers Say</h2>
            <p>Real experiences from pilgrims who trusted Shahin Travels for their Umrah journey.</p>
            <a href="#testimonials" class="view-all">View All Reviews →</a>
        </div>
        <div class="testimonials-slider fade-up">
            <div class="testimonials-track">
                <?php foreach ($testimonials as $t): ?>
                <div class="testimonial-card">
                    <div class="testimonial-stars"><?= str_repeat('★', (int)$t['rating']) ?></div>
                    <p class="testimonial-text">"<?= e($t['review']) ?>"</p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar">
                            <?php if ($t['avatar']): ?>
                            <img src="<?= e(SITE_URL . '/public/images/testimonials/' . $t['avatar']) ?>" alt="<?= e($t['customer_name']) ?>">
                            <?php else: ?>
                            <?= strtoupper(substr($t['customer_name'], 0, 1)) ?>
                            <?php endif; ?>
                        </div>
                        <div>
                            <div class="testimonial-name"><?= e($t['customer_name']) ?></div>
                            <div class="testimonial-location"><?= e($t['location'] ?? '') ?></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="slider-controls">
            <button class="slider-btn prev" aria-label="Previous">←</button>
            <div class="slider-dots"></div>
            <button class="slider-btn next" aria-label="Next">→</button>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ── Latest Blog ────────────────────────────────────── -->
<?php if (!empty($blogPosts)): ?>
<section class="section" id="blog">
    <div class="container">
        <div class="section-header fade-up">
            <span class="label">Travel Knowledge</span>
            <h2>Latest from Our Blog</h2>
            <p>Tips, guides and latest updates for your Umrah journey.</p>
            <a href="<?= SITE_URL ?>/blog" class="view-all">View All Articles →</a>
        </div>
        <div class="blog-grid stagger-children">
            <?php foreach ($blogPosts as $post): ?>
            <article class="blog-card fade-up">
                <div class="blog-card-image">
                    <img src="<?= $post['featured_image'] ? e(SITE_URL . '/public/images/blog/' . $post['featured_image']) : 'https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?w=400&q=70' ?>" alt="<?= e($post['title']) ?>" loading="lazy">
                    <?php if ($post['cat_name']): ?><span class="blog-card-category"><?= e($post['cat_name']) ?></span><?php endif; ?>
                </div>
                <div class="blog-card-body">
                    <div class="blog-card-date">📅 <?= date('d M Y', strtotime($post['published_at'] ?? 'now')) ?></div>
                    <h3><?= e($post['title']) ?></h3>
                    <p><?= e(mb_strimwidth($post['excerpt'] ?? '', 0, 100, '...')) ?></p>
                    <a href="<?= SITE_URL ?>/blog/<?= e($post['slug']) ?>" class="blog-read-more">Read More →</a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
