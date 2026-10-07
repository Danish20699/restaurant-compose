<?php
$host = getenv('PGHOST');
$db   = getenv('PGDATABASE');
$user = getenv('PGUSER');
$pass = getenv('PGPASSWORD');
$port = getenv('PGPORT') ?: '5432';

$conn = null;
$error = null;

try {
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $error = $e->getMessage();
}

// Curated high-res culinary photography mappings
$dish_images = [
    'Garlic Bread'         => 'https://images.unsplash.com/photo-1619860860774-1e2e17343432?w=700&auto=format&fit=crop&q=80',
    'Caesar Salad'         => 'https://images.unsplash.com/photo-1550304943-4f24f54ddde9?w=700&auto=format&fit=crop&q=80',
    'Soup of the Day'      => 'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=700&auto=format&fit=crop&q=80',
    'Grilled Chicken'      => 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?w=700&auto=format&fit=crop&q=80',
    'Lamb Biryani'         => 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?w=700&auto=format&fit=crop&q=80',
    'Paneer Tikka Masala'  => 'https://images.unsplash.com/photo-1631452180519-c014fe946bc7?w=700&auto=format&fit=crop&q=80',
    'Fish and Chips'       => 'https://images.unsplash.com/photo-1579208570378-8c970854bc23?w=700&auto=format&fit=crop&q=80',
    'Tiramisu'             => 'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?w=700&auto=format&fit=crop&q=80',
    'Gulab Jamun'          => 'https://images.unsplash.com/photo-1601050690597-df0568f70950?w=700&auto=format&fit=crop&q=80',
    'Masala Chai'          => 'https://images.unsplash.com/photo-1576092768241-dec231879fc3?w=700&auto=format&fit=crop&q=80',
    'Fresh Lime Soda'      => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?w=700&auto=format&fit=crop&q=80',
];
$fallback_image = 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=700&auto=format&fit=crop&q=80';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gourmet Bistro — Michelin Experience</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,700;1,400;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-deep: #08090d;
            --bg-card: rgba(18, 21, 28, 0.85);
            --gold: #dfb15b;
            --gold-light: #f7e2a9;
            --gold-gradient: linear-gradient(135deg, #dfb15b 0%, #ecd08e 50%, #b88b32 100%);
            --text-main: #f3f5f8;
            --text-muted: #9aa4b2;
            --border-subtle: rgba(223, 177, 91, 0.18);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-deep);
            color: var(--text-main);
            min-height: 100vh;
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Ambient Hero Banner */
        .hero {
            position: relative;
            padding: 5.5rem 2rem 4.5rem;
            text-align: center;
            background: 
                radial-gradient(circle at center, rgba(223, 177, 91, 0.08) 0%, transparent 65%),
                linear-gradient(180deg, rgba(8, 9, 13, 0.4) 0%, var(--bg-deep) 100%),
                url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1920&auto=format&fit=crop&q=80') center/cover no-repeat;
            border-bottom: 1px solid var(--border-subtle);
        }

        .michelin-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            text-transform: uppercase;
            font-size: 0.78rem;
            letter-spacing: 5px;
            color: var(--gold);
            font-weight: 700;
            margin-bottom: 1.2rem;
            padding: 0.35rem 1.2rem;
            border: 1px solid var(--border-subtle);
            border-radius: 999px;
            background: rgba(223, 177, 91, 0.06);
            backdrop-filter: blur(8px);
        }

        .hero h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 4.8rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, #ffffff 30%, var(--gold-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.8rem;
            line-height: 1.1;
        }

        .hero-desc {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.45rem;
            color: var(--text-muted);
            font-style: italic;
            max-width: 650px;
            margin: 0 auto 1.8rem;
        }

        /* Cluster Status Indicator */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.5rem 1.3rem;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(10px);
        }

        .status-badge.online {
            background: rgba(16, 185, 129, 0.12);
            color: #34d399;
            border: 1px solid rgba(52, 211, 153, 0.35);
        }

        .status-badge.offline {
            background: rgba(239, 68, 68, 0.12);
            color: #f87171;
            border: 1px solid rgba(248, 113, 113, 0.35);
        }

        .pulse-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: currentColor;
            position: relative;
        }

        .pulse-dot::after {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            border: 1.5px solid currentColor;
            animation: pulse 2s infinite ease-out;
        }

        @keyframes pulse {
            0% { transform: scale(0.6); opacity: 1; }
            100% { transform: scale(2.2); opacity: 0; }
        }

        /* Container & Categories */
        main {
            max-width: 1200px;
            margin: 3.5rem auto 5rem;
            padding: 0 2rem;
        }

        .category-group {
            margin-bottom: 5rem;
        }

        .category-header {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 2.2rem;
        }

        .category-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.5rem;
            color: var(--gold-light);
            font-weight: 700;
            white-space: nowrap;
        }

        .category-separator {
            height: 1px;
            width: 100%;
            background: linear-gradient(90deg, var(--gold) 0%, rgba(223, 177, 91, 0.1) 70%, transparent 100%);
        }

        /* Culinary Grid & Cards */
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 2.2rem;
        }

        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 20px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            backdrop-filter: blur(14px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
        }

        .card:hover {
            transform: translateY(-8px);
            border-color: rgba(223, 177, 91, 0.5);
            box-shadow: 0 20px 45px -10px rgba(0, 0, 0, 0.65), 0 0 25px rgba(223, 177, 91, 0.15);
        }

        .card-image-wrap {
            position: relative;
            height: 220px;
            overflow: hidden;
            background: #111;
        }

        .card-image-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .card:hover .card-image-wrap img {
            transform: scale(1.08);
        }

        .price-pill {
            position: absolute;
            bottom: 14px;
            right: 14px;
            background: rgba(8, 9, 13, 0.88);
            border: 1px solid var(--gold);
            color: var(--gold-light);
            padding: 0.35rem 0.9rem;
            border-radius: 999px;
            font-size: 1rem;
            font-weight: 700;
            font-family: 'Cormorant Garamond', serif;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
        }

        .card-body {
            padding: 1.6rem 1.6rem 1.8rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .dish-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.6rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 0.5rem;
        }

        .dish-desc {
            color: var(--text-muted);
            font-size: 0.92rem;
            line-height: 1.55;
            flex-grow: 1;
        }

        .card-footer {
            margin-top: 1.3rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .badge-artisan {
            text-transform: uppercase;
            font-size: 0.72rem;
            letter-spacing: 1.5px;
            color: var(--gold);
            font-weight: 600;
        }

        .order-hint {
            font-size: 0.8rem;
            color: #6ee7b7;
            font-weight: 500;
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 4rem 2rem;
            border-top: 1px solid var(--border-subtle);
            background: rgba(8, 9, 13, 0.95);
        }

        .footer-brand {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.8rem;
            color: var(--gold-light);
            margin-bottom: 0.4rem;
        }

        .footer-credit {
            color: var(--text-muted);
            font-size: 0.88rem;
        }

        .devops-badge {
            color: var(--gold);
            font-weight: 600;
        }
    </style>
</head>
<body>

<header class="hero">
    <div class="michelin-tag">★ 3-Star Culinary Haute Cuisine</div>
    <h1>Gourmet Bistro</h1>
    <p class="hero-desc">Artisanal Dining &amp; Epicurean Gastronomy Crafted with Passion</p>

    <?php if ($conn): ?>
        <div class="status-badge online">
            <span class="pulse-dot"></span>
            <span>PostgreSQL Active (Cluster Healthy)</span>
        </div>
    <?php else: ?>
        <div class="status-badge offline">
            <span class="pulse-dot"></span>
            <span>Cluster Error: <?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>
</header>

<main>
<?php if ($conn):
    $cats = $conn->query("SELECT id, name FROM categories ORDER BY display_order");
    while ($cat = $cats->fetch(PDO::FETCH_ASSOC)):
?>
    <section class="category-group">
        <div class="category-header">
            <h2 class="category-title"><?= htmlspecialchars($cat['name']) ?></h2>
            <div class="category-separator"></div>
        </div>

        <div class="menu-grid">
            <?php
            $items = $conn->prepare("SELECT name, description, price FROM menu_items WHERE category_id = ? AND is_available = true ORDER BY id");
            $items->execute([$cat['id']]);
            while ($item = $items->fetch(PDO::FETCH_ASSOC)):
                $photo = $dish_images[$item['name']] ?? $fallback_image;
            ?>
            <article class="card">
                <div class="card-image-wrap">
                    <img src="<?= htmlspecialchars($photo) ?>" alt="<?= htmlspecialchars($item['name']) ?>" loading="lazy">
                    <div class="price-pill">$<?= number_format($item['price'], 2) ?></div>
                </div>
                <div class="card-body">
                    <h3 class="dish-name"><?= htmlspecialchars($item['name']) ?></h3>
                    <p class="dish-desc"><?= htmlspecialchars($item['description']) ?></p>
                    <div class="card-footer">
                        <span class="badge-artisan">Chef Crafted</span>
                        <span class="order-hint">In Stock</span>
                    </div>
                </div>
            </article>
            <?php endwhile; ?>
        </div>
    </section>
<?php endwhile;
else: ?>
    <p style="text-align: center; color: var(--text-muted); padding: 5rem; font-size: 1.2rem;">
        Menu temporarily unavailable while connecting to database.
    </p>
<?php endif; ?>
</main>

<footer>
    <div class="footer-brand">Gourmet Bistro</div>
    <p class="footer-credit">
        DevOps &amp; Continuous Deployment Architecture by <span class="devops-badge">Danish Nazir</span> &bull; MLOps APR26
    </p>
</footer>

</body>
</html>