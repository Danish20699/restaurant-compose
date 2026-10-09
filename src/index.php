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
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg: #f7f4ed;
            --surface: #fffdf8;
            --text: #26271f;
            --muted: #77776d;
            --accent: #8a5936;
            --accent-dark: #63402a;
            --line: #ded7ca;
            --gold: #b38b50;
            --green: #287653;
            --red: #b83c36;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.7;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* Hero */
        header {
            position: relative;
            isolation: isolate;
            overflow: hidden;
            min-height: 470px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 90px 24px 78px;
            text-align: center;
            color: #fffaf1;
            background:
                linear-gradient(
                    180deg,
                    rgba(24, 24, 19, 0.52),
                    rgba(24, 24, 19, 0.78)
                ),
                url('https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=2000&q=85')
                center 55% / cover no-repeat;
        }

        header::after {
            content: "";
            position: absolute;
            inset: 14px;
            border: 1px solid rgba(255, 250, 241, 0.24);
            pointer-events: none;
            z-index: -1;
        }

        .header-tag {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 22px;
            color: #e7c796;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 4px;
            text-transform: uppercase;
        }

        .header-tag::before,
        .header-tag::after {
            content: "";
            width: 26px;
            height: 1px;
            background: #c5a16c;
        }

        h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(3rem, 7vw, 6rem);
            font-weight: 500;
            line-height: 1.08;
            letter-spacing: -2px;
            margin-bottom: 20px;
            color: #fffaf1;
        }

        .subtitle {
            max-width: 560px;
            color: rgba(255, 250, 241, 0.83);
            font-family: 'Playfair Display', serif;
            font-size: clamp(1rem, 2vw, 1.3rem);
            font-style: italic;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            margin-top: 30px;
            padding: 9px 15px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.5px;
            backdrop-filter: blur(8px);
        }

        .status-pill.online {
            color: #c4f0d7;
            background: rgba(24, 88, 58, 0.55);
            border: 1px solid rgba(150, 224, 180, 0.35);
        }

        .status-pill.offline {
            color: #ffd4ce;
            background: rgba(135, 37, 31, 0.55);
            border: 1px solid rgba(255, 170, 160, 0.35);
            max-width: 100%;
            overflow-wrap: anywhere;
        }

        .dot {
            width: 7px;
            height: 7px;
            flex-shrink: 0;
            border-radius: 50%;
            background: currentColor;
        }

        .online .dot {
            box-shadow: 0 0 9px rgba(150, 224, 180, 0.65);
        }

        /* Menu content */
        main {
            width: min(1160px, 100%);
            margin: 0 auto;
            padding: 82px 30px 95px;
        }

        .category-section {
            margin-bottom: 76px;
        }

        .category-section:last-child {
            margin-bottom: 0;
        }

        .category-header {
            display: flex;
            align-items: center;
            gap: 22px;
            margin-bottom: 30px;
        }

        .category-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.8rem, 4vw, 2.5rem);
            font-weight: 500;
            line-height: 1.2;
            color: var(--text);
            flex-shrink: 0;
        }

        .category-line {
            width: 100%;
            height: 1px;
            background: var(--line);
            position: relative;
        }

        .category-line::before {
            content: "";
            position: absolute;
            left: 0;
            top: -1px;
            width: 42px;
            height: 3px;
            background: var(--gold);
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .menu-card {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-width: 0;
            padding: 28px 30px;
            background: var(--surface);
            border: 1px solid #e9e2d7;
            border-radius: 4px;
            transition:
                transform 220ms ease,
                box-shadow 220ms ease,
                border-color 220ms ease;
        }

        .menu-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 3px;
            height: 0;
            background: var(--accent);
            transition: height 220ms ease;
        }

        .menu-card:hover {
            transform: translateY(-4px);
            border-color: #d6c4ac;
            box-shadow: 0 15px 35px rgba(59, 43, 27, 0.07);
        }

        .menu-card:hover::before {
            height: 100%;
        }

        .card-top {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 12px;
        }

        .item-name {
            min-width: 0;
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.1rem, 2vw, 1.35rem);
            font-weight: 600;
            line-height: 1.4;
            color: var(--text);
        }

        .item-price {
            flex-shrink: 0;
            font-family: 'Playfair Display', serif;
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--accent);
            white-space: nowrap;
        }

        .item-desc {
            color: var(--muted);
            font-size: 0.9rem;
            line-height: 1.8;
            overflow-wrap: anywhere;
        }

        /* Database fallback */
        main > p {
            padding: 65px 20px !important;
            color: var(--muted) !important;
            font-size: 1rem;
        }

        /* Footer */
        footer {
            padding: 38px 20px;
            text-align: center;
            color: #d5cabb;
            background: #28271f;
            border-top: 3px solid var(--gold);
            font-size: 0.82rem;
            letter-spacing: 0.3px;
        }

        .chef-badge {
            color: #e3bd83;
            font-weight: 700;
        }

        /* Responsive layout */
        @media (max-width: 760px) {
            header {
                min-height: 410px;
                padding: 75px 25px 65px;
            }

            h1 {
                letter-spacing: -1px;
            }

            main {
                padding: 60px 22px 70px;
            }

            .category-section {
                margin-bottom: 55px;
            }

            .menu-grid {
                grid-template-columns: 1fr;
                gap: 14px;
            }

            .menu-card {
                padding: 24px;
            }

            .category-header {
                gap: 15px;
                margin-bottom: 23px;
            }
        }

        @media (max-width: 420px) {
            header {
                min-height: 370px;
                padding: 65px 20px 55px;
            }

            header::after {
                inset: 9px;
            }

            .header-tag {
                gap: 8px;
                font-size: 9px;
                letter-spacing: 2.5px;
            }

            .header-tag::before,
            .header-tag::after {
                width: 17px;
            }

            .subtitle {
                font-size: 1rem;
            }

            .status-pill {
                font-size: 10px;
                padding: 8px 10px;
            }

            main {
                padding: 45px 16px 55px;
            }

            .category-title {
                font-size: 1.65rem;
                white-space: normal;
            }

            .menu-card {
                padding: 21px 18px;
            }

            .card-top {
                gap: 10px;
            }

            .item-name {
                font-size: 1.1rem;
            }

            .item-price {
                font-size: 1.05rem;
            }

            .item-desc {
                font-size: 0.86rem;
            }

            footer {
                padding: 30px 18px;
                font-size: 0.75rem;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                scroll-behavior: auto !important;
                transition-duration: 0.01ms !important;
                animation-duration: 0.01ms !important;
            }
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
    Gourmet Bistro &bull; Orchestrated by
    <span class="chef-badge">Danish Nazir</span>
    &bull; MLOps &amp; DevOps Engineering
</footer>

</body>
</html>