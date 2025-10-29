<?php

require_once __DIR__ . '/tasm_db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    $action = $_GET['action'];
    if ($action === 'remove') {
        $id = intval($_GET['id'] ?? 0);
        if ($id > 0 && isset($_SESSION['tasm_cart'][$id])) unset($_SESSION['tasm_cart'][$id]);
    }
    if ($action === 'clear') {
        unset($_SESSION['tasm_cart']);
    }
    // le mandas de vuelta al carrito
    header('Location: tasm_cart.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['ok' => false, 'msg' => 'ID inválido']); exit;
        }
        if (!isset($_SESSION['tasm_cart'])) $_SESSION['tasm_cart'] = [];
        if (!isset($_SESSION['tasm_cart'][$id])) $_SESSION['tasm_cart'][$id] = 0;
        $_SESSION['tasm_cart'][$id]++;
        echo json_encode(['ok' => true, 'count' => array_sum($_SESSION['tasm_cart'])]);
        exit;
    }
    if ($action === 'remove') {
        $id = intval($_POST['id'] ?? 0);
        if (isset($_SESSION['tasm_cart'][$id])) {
            unset($_SESSION['tasm_cart'][$id]);
        }
        echo json_encode(['ok' => true]); exit;
    }
    if ($action === 'clear') {
        unset($_SESSION['tasm_cart']);
        echo json_encode(['ok' => true]); exit;
    }
    if ($action === 'update') {
        $id = intval($_POST['id'] ?? 0);
        $qty = intval($_POST['qty'] ?? 0);
        if ($qty <= 0) { unset($_SESSION['tasm_cart'][$id]); }
        else { $_SESSION['tasm_cart'][$id] = $qty; }
        echo json_encode(['ok' => true]); exit;
    }
    echo json_encode(['ok' => false, 'msg' => 'acción desconocida']);
    exit;
}

// GET
header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html lang="es">
<head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Carrito - FoodExpress</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="tasm_assets/css/tasm_styles.css">
</head>
<body>
<header class="tasm-header">
    <div class="tasm-container tasm-header-inner">
        <div class="tasm-brand"><div class="tasm-logo">FE</div><div>FoodExpress</div></div>
        <nav class="tasm-nav">
            <a href="tasm_index.php">Menu</a>
            <a href="tasm_admin_login.php">Admin</a>
            <a class="tasm-cart-link" href="tasm_cart.php">Carrito (<span id="tasm-cart-count"><?php echo isset($_SESSION['tasm_cart'])?array_sum($_SESSION['tasm_cart']):0; ?></span>)</a>
        </nav>
    </div>
</header>
<main class="tasm-main">
    <div class="tasm-container">
<?php
if (empty($_SESSION['tasm_cart'])) {
    echo '<p>El carrito esta vacio.</p>';
} else {
    $items = $_SESSION['tasm_cart'];
    $ids = array_keys($items);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $pdo = tasm_db_connect();
    $stmt = $pdo->prepare("SELECT * FROM tasm_products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $products = [];
    while ($r = $stmt->fetch()) $products[$r['id']] = $r;

    echo '<form method="post" action="tasm_checkout.php">';
    echo '<table class="tasm-cart-table"><thead><tr><th>Producto</th><th>Cantidad</th><th>Subtotal</th><th></th></tr></thead><tbody>';
    $total = 0;
    foreach ($items as $id => $qty) {
        $p = $products[$id];
        $sub = $p['price'] * $qty;
        $total += $sub;
        echo '<tr>';
        echo '<td>'.htmlspecialchars($p['name']).'</td>';
        echo '<td><input type="number" name="qty['.$id.']" value="'.$qty.'" min="1"></td>';
        echo '<td>$'.number_format($sub,2).'</td>';
        echo '<td><a href="tasm_cart.php?action=remove&id='.$id.'">Eliminar</a></td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
    echo '<p class="tasm-total">Total: $'.number_format($total,2).'</p>';
    echo '<button type="submit" class="tasm-checkout-btn">Ir a pagar</button>';
    echo '</form>';
}
?>
  </div>
</main>

<footer class="tasm-footer">Hecho con &hearts; — FoodExpress</footer>

</body>
</html>
