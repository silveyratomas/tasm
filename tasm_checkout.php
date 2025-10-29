<?php
require_once __DIR__ . '/tasm_db.php';
session_start();

// Si el formulario del carrito envía cantidades (qty[]), actualizamos la sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['qty']) && is_array($_POST['qty'])) {
    foreach ($_POST['qty'] as $pid => $q) {
        $id = intval($pid);
        $qty = max(0, intval($q));
        if ($qty > 0) {
            $_SESSION['tasm_cart'][$id] = $qty;
        } else {
            unset($_SESSION['tasm_cart'][$id]);
        }
    }
    // Después de actualizar cantidades, mostramos el formulario de checkout (no redirigimos)
}

// Si envían datos de cliente, procesar y guardar el pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['name']) || isset($_POST['address']))) {
    // Procesar formulario de checkout
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $cart = $_SESSION['tasm_cart'] ?? [];
    if (empty($name) || empty($address) || empty($cart)) {
        $error = 'Complete nombre, dirección y asegure que el carrito no esté vacío.';
    } else {
        $pdo = tasm_db_connect();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("INSERT INTO tasm_orders (customer_name, phone, address, created_at) VALUES (:n, :p, :a, NOW())");
            $stmt->execute(['n'=>$name,'p'=>$phone,'a'=>$address]);
            $orderId = $pdo->lastInsertId();
            $stmtP = $pdo->prepare("SELECT id, price FROM tasm_products WHERE id = :id LIMIT 1");
            $stmtI = $pdo->prepare("INSERT INTO tasm_order_items (order_id, product_id, qty, price) VALUES (:o, :p, :q, :pr)");
            foreach ($cart as $pid => $qty) {
                $stmtP->execute(['id'=>$pid]);
                $prod = $stmtP->fetch();
                if (!$prod) continue;
                $stmtI->execute(['o'=>$orderId,'p'=>$pid,'q'=>$qty,'pr'=>$prod['price']]);
            }
            $pdo->commit();
            unset($_SESSION['tasm_cart']);
            header('Location: tasm_checkout.php?ok=1&order='.$orderId);
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Error guardando pedido: '.$e->getMessage();
        }
    }
}

?><!doctype html>
<html lang="es">
<head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Checkout - FoodExpress</title>
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
<?php if (isset($_GET['ok'])): ?>
            <div class="tasm-product">
                <p class="tasm-success">Pedido creado correctamente. ID: <?php echo intval($_GET['order']); ?></p>
                <p><a href="tasm_index.php">Seguir comprando</a></p>
            </div>
<?php else: ?>
    <?php if (!empty($error)) echo '<p class="tasm-error">'.htmlspecialchars($error).'</p>'; ?>
            <div class="tasm-product">
                <h3>Datos de envio</h3>
                <form method="post" class="tasm-checkout-form">
                        <label>Nombre completo<input type="text" name="name" required></label>
                        <label>Telefono<input type="text" name="phone"></label>
                        <label>Direccion<textarea name="address" required></textarea></label>
                        <button type="submit" class="tasm-submit">Enviar pedido</button>
                </form>
            </div>
<?php endif; ?>
</main>
</body>
</html>
