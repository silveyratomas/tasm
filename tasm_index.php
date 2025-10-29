<?php
require_once __DIR__ . '/tasm_db.php';
session_start();

$category = isset($_GET['category']) ? $_GET['category'] : null;
$products = tasm_get_products($category);
$categories = tasm_get_categories();
?>
<!doctype html>
<html lang="es">
<head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>FoodExpress - Menu</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="tasm_assets/css/tasm_styles.css">
</head>
<body>
<header class="tasm-header">
    <div class="tasm-container tasm-header-inner">
        <div class="tasm-brand">
            <div class="tasm-logo">FE</div>
            <div>FoodExpress</div>
        </div>
        <nav class="tasm-nav">
            <a href="tasm_index.php">Menu</a>
            <a href="tasm_admin_login.php">Admin</a>
            <a class="tasm-cart-link" href="tasm_cart.php">Carrito (<span id="tasm-cart-count"><?php echo isset($_SESSION['tasm_cart'])?array_sum($_SESSION['tasm_cart']):0; ?></span>)</a>
        </nav>
    </div>
</header>

<main class="tasm-main">
    <div class="tasm-container">
    <section class="tasm-filters tasm-side">
        <h2>Categorías</h2>
        <ul class="tasm-cat-list">
            <li><a href="tasm_index.php">Todas</a></li>
            <?php foreach ($categories as $cat): ?>
                <li><a href="?category=<?php echo urlencode($cat) ?>"><?php echo htmlspecialchars($cat) ?></a></li>
            <?php endforeach; ?>
        </ul>
    </section>
    <section class="tasm-menu">
        <h2>Menu</h2>
        <div class="tasm-products">
            <?php foreach ($products as $p): ?>
                                <article class="tasm-product">
                                        <div class="tasm-product-head">
                                            <div style="display:flex;gap:12px;align-items:center">
                                                <?php $img = $p['image'] ?? null; $img = $img ? 'tasm_assets/images/'.htmlspecialchars($img) : 'tasm_assets/images/placeholder.svg'; ?>
                                                <img src="<?php echo $img ?>" alt="<?php echo htmlspecialchars($p['name']) ?>" loading="lazy" decoding="async" style="width:72px;height:72px;border-radius:8px;object-fit:cover">
                                                <div>
                                                    <h3 class="tasm-product-name"><?php echo htmlspecialchars($p['name']) ?></h3>
                                                    <p class="tasm-product-desc"><?php echo htmlspecialchars($p['description']) ?></p>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="tasm-price">$<?php echo number_format($p['price'],2) ?></div>
                                            </div>
                                        </div>
                                        <div class="tasm-product-footer">
                                                <button class="tasm-add-btn" data-id="<?php echo $p['id'] ?>">Anadir</button>
                                        </div>
                                </article>
            <?php endforeach; ?>
        </div>
    </section>
  </div>
</main>

<footer class="tasm-footer">Hecho con &hearts; — FoodExpress</footer>

<script src="tasm_assets/js/tasm_toast.js"></script>
<script src="tasm_assets/js/tasm_app.js"></script>
</body>
</html>
