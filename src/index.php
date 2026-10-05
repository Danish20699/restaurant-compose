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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gourmet Bistro — Artisan Cuisine</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,800;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #090a0f;
            --card-bg: rgba(22, 27, 34, 0.75);
            --border-glow: rgba(212, 175, 55, 0.2);
            --gold: #d4af37;
            --gold-light: #f3e5ab;
            --text-main: #f0f3f6;
            --text-muted: #8b949e;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at top center, #161b22 0%, var(--bg-dark) 100%);
            color: var(--text-main);
            min-height: 100vh;
            line-height: 1.6;
        }

        header {
            padding: 4rem 2rem 3rem;
            text-align: center;
            background: linear-gradient(180deg, rgba(212, 175, 55, 0.08) 0%, transparent 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .header-tag {
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 4px;
            color: var(--gold);
            font-weight: 700;
            margin-bottom: 0.8rem;
        }

        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff 40%, var(--gold-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        .subtitle {
            color: var(--text-muted);
            font-style: italic;
            font-size: 1.15rem;
            font-family: 'Playfair Display', serif;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
            padding: 0.45rem 1.2rem;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 600;
        }

        .status-pill.online {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(52, 211, 153, 0.3);
        }

        .status-pill.offline {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 1px solid rgba(248, 113, 113, 0.3);
        }

        .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: currentColor;
            box-shadow: 0 0 10px currentColor;
        }

        main {
            max-width: 1100px;
            margin: 3.5rem auto;
            padding: 0 2rem;
        }

        .category-section {
            margin-bottom: 4rem;
        }

        .category-header {
            display: flex;
            align-items: center;
            gap: 1.2rem;
            margin-bottom: 2rem;
        }

        .category-title {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            color: var(--gold-light);
            white-space: nowrap;
        }

        .category-line {
            height: 1px;
            width: 100%;
            background: linear-gradient(90deg, var(--gold) 0%, rgba(212, 175, 55, 0.1) 70%, transparent 100%);
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.8rem;
        }

        .menu-card {
            background: var(--card-bg);
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 16px;
            padding: 1.6rem;
            backdrop-filter: blur(12px);
            transition: all 0.3s ease;
        }

        .menu-card:hover {
            transform: translateY(-4px);
            border-color: var(--border-glow);
            box-shadow: 0 12px 30px -10px rgba(0, 0, 0, 0.7);
        }

        .card-top {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 1rem;
            margin-bottom: 0.8rem;
        }

        .item-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: #ffffff;
        }

        .item-price {
            font-family: 'Playfair Display', serif;
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--gold);
        }

        .item-desc {
            color: var(--text-muted);
            font-size: 0.92rem;
            line-height: 1.5;
        }

        footer {
            text-align: center;
            padding: 3rem 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            color: #57606a;
            font-size: 0.88rem;
        }

        .chef-badge {
            color: var(--gold);
            font-weight: 600;
        }
    </style>
</head>
<body>

<header>
    <div class="header-tag">Michelin Star Experience</div>
    <h1>Gourmet Bistro</h1>
    <p class="subtitle">Artisanal Dining &amp; Fine Culinary Masterpieces</p>
    
    <?php if ($conn): ?>
        <div class="status-pill online">
            <span class="dot"></span>
            <span>PostgreSQL Active (Cluster Healthy)</span>
        </div>
    <?php else: ?>
        <div class="status-pill offline">
            <span class="dot"></span>
            <span>Database Error: <?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>
</header>

<main>
<?php if ($conn):
    $cats = $conn->query("SELECT id, name FROM categories ORDER BY display_order");
    while ($cat = $cats->fetch(PDO::FETCH_ASSOC)):
?>
    <section class="category-section">
        <div class="category-header">
            <h2 class="category-title"><?= htmlspecialchars($cat['name']) ?></h2>
            <div class="category-line"></div>
        </div>
        
        <div class="menu-grid">
            <?php
            $items = $conn->prepare("SELECT name, description, price FROM menu_items WHERE category_id = ? AND is_available = true ORDER BY id");
            $items->execute([$cat['id']]);
            while ($item = $items->fetch(PDO::FETCH_ASSOC)):
            ?>
            <article class="menu-card">
                <div class="card-top">
                    <h3 class="item-name"><?= htmlspecialchars($item['name']) ?></h3>
                    <div class="item-price">$<?= number_format($item['price'], 2) ?></div>
                </div>
                <p class="item-desc"><?= htmlspecialchars($item['description']) ?></p>
            </article>
            <?php endwhile; ?>
        </div>
    </section>
<?php endwhile;
else: ?>
    <p style="text-align: center; color: var(--text-muted); padding: 4rem;">Menu temporarily unavailable.</p>
<?php endif; ?>
</main>

<footer>
    Gourmet Bistro &bull; Orchestrated by <span class="chef-badge">Danish Nazir</span> &bull; MLOps &amp; DevOps Engineering
</footer>

</body>
</html>