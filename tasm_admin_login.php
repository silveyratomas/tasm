<?php
require_once __DIR__ . '/tasm_admin_conf.php';
require_once __DIR__ . '/tasm_db.php';
session_start();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';
    $logged = false;
    // intentar validar contra tabla tasm_admins si existe
    try {
        $pdo = tasm_db_connect();
        $stmt = $pdo->prepare("SELECT password_hash FROM tasm_admins WHERE username = :u LIMIT 1");
        $stmt->execute(['u'=>$u]);
        $row = $stmt->fetch();
        if ($row && password_verify($p, $row['password_hash'])) {
            $logged = true;
        }
    } catch (Exception $e) {
        // si falla DB, fallback a config file
    }
    if (!$logged) {
        // fallback a archivo de config (desarrollo)
        if ($u === $tasm_admin_user && $p === $tasm_admin_pass) $logged = true;
    }
    if ($logged) {
        $_SESSION['tasm_admin_logged'] = true;
        header('Location: tasm_admin.php'); exit;
    } else {
        $error = 'Usuario o contrasena incorrectos.';
    }
}
<!doctype html>
<html lang="es">
<head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Admin Login - FoodExpress</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="tasm_assets/css/tasm_styles.css">
        <style>.tasm-login{max-width:420px;margin:40px auto;padding:16px;border-radius:12px;background:var(--card);box-shadow:var(--shadow)} .tasm-login label{display:block;margin-bottom:8px}</style>
</head>
<body>
<header class="tasm-header">
    <div class="tasm-container tasm-header-inner">
        <div class="tasm-brand"><div class="tasm-logo">FE</div><div>FoodExpress</div></div>
        <nav class="tasm-nav">
            <a href="tasm_index.php">Menu</a>
            <a href="tasm_cart.php">Carrito</a>
        </nav>
    </div>
</header>
<main class="tasm-main">
        <div class="tasm-container">
            <div class="tasm-login">
                    <h2>Ingreso Admin</h2>
                    <?php if ($error) echo '<p class="tasm-error">'.htmlspecialchars($error).'</p>'; ?>
                    <form method="post">
                            <label>Usuario<input name="username" required></label>
                            <label>Contraseña<input type="password" name="password" required></label>
                            <button type="submit" class="tasm-submit">Ingresar</button>
                    </form>
                    <p style="margin-top:12px;font-size:.9rem;color:#666">Usuario por defecto: <code>admin</code> / Contraseña: <code>admin123</code>. Cambialo en <code>tasm_admin_conf.php</code>.</p>
            </div>
        </div>
    </main>
    <footer class="tasm-footer">Hecho con &hearts; — FoodExpress</footer>
</body>
</html>
