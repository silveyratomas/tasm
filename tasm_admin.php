<?php
// Panel administrativo sencillo para gestionar productos
require_once __DIR__ . '/tasm_db.php';
session_start();
// comprobar sesión de admin
if (empty($_SESSION['tasm_admin_logged'])) {
    header('Location: tasm_admin_login.php'); exit;
}

$pdo = tasm_db_connect();

// Manejo de creación y eliminación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tasm_action']) && $_POST['tasm_action'] === 'create') {
        $name = $_POST['name'] ?? '';
        $desc = $_POST['description'] ?? '';
        $cat = $_POST['category'] ?? '';
        $price = floatval($_POST['price'] ?? 0);
        // manejar upload de imagen si existe
        $imageName = null;
        if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $f = $_FILES['image'];
            $allowed = ['image/jpeg','image/png','image/webp','image/gif','image/svg+xml'];
            $maxSize = 2 * 1024 * 1024; // 2MB
            if ($f['size'] <= $maxSize && in_array($f['type'],$allowed)) {
                $ext = pathinfo($f['name'], PATHINFO_EXTENSION);
                $imageName = 'tasm_prod_'.time().'_'.bin2hex(random_bytes(4)).'.'.($ext?:'png');
                $dstDir = __DIR__ . '/tasm_assets/images';
                if (!is_dir($dstDir)) mkdir($dstDir,0755,true);
                move_uploaded_file($f['tmp_name'], $dstDir . '/' . $imageName);
            }
        }

        // insertar incluyendo campo image si la columna existe
        try {
            if ($imageName !== null) {
                $stmt = $pdo->prepare("INSERT INTO tasm_products (name, description, category, price, image) VALUES (:n,:d,:c,:p,:img)");
                $stmt->execute(['n'=>$name,'d'=>$desc,'c'=>$cat,'p'=>$price,'img'=>$imageName]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO tasm_products (name, description, category, price) VALUES (:n,:d,:c,:p)");
                $stmt->execute(['n'=>$name,'d'=>$desc,'c'=>$cat,'p'=>$price]);
            }
        } catch (Exception $e) {
            // si falla por columna ausente, intentar insertar sin image
            $stmt = $pdo->prepare("INSERT INTO tasm_products (name, description, category, price) VALUES (:n,:d,:c,:p)");
            $stmt->execute(['n'=>$name,'d'=>$desc,'c'=>$cat,'p'=>$price]);
        }
        header('Location: tasm_admin.php'); exit;
    }
    if (isset($_POST['tasm_action']) && $_POST['tasm_action'] === 'delete') {
        $id = intval($_POST['id']);
        // obtener nombre de imagen para borrarla del disco
        $stmtImg = $pdo->prepare("SELECT image FROM tasm_products WHERE id = :id LIMIT 1");
        $stmtImg->execute(['id'=>$id]);
        $rowImg = $stmtImg->fetch();
        if ($rowImg && !empty($rowImg['image'])) {
            $path = __DIR__ . '/tasm_assets/images/' . $rowImg['image'];
            if (is_file($path)) {
                @unlink($path);
            }
        }
        $stmt = $pdo->prepare("DELETE FROM tasm_products WHERE id = :id");
        $stmt->execute(['id'=>$id]);
        header('Location: tasm_admin.php'); exit;
    }
}

$products = $pdo->query("SELECT * FROM tasm_products ORDER BY id")->fetchAll();

?>
<!doctype html>
<html lang="es">
<head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Admin - FoodExpress</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="tasm_assets/css/tasm_styles.css">
        <style> .tasm-admin {max-width:900px;margin:0 auto;padding:1rem;} </style>
</head>
<body>
<header class="tasm-header">
    <div class="tasm-container tasm-header-inner">
        <div class="tasm-brand"><div class="tasm-logo">FE</div><div>FoodExpress</div></div>
        <nav class="tasm-nav">
            <a href="tasm_index.php">Menu</a>
            <a href="tasm_cart.php">Carrito</a>
            <a href="tasm_admin_logout.php">Salir</a>
        </nav>
    </div>
</header>
<main class="tasm-main tasm-admin">
    <section>
        <h2>Crear producto</h2>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="tasm_action" value="create">
            <label>Nombre<input name="name" required></label>
            <label>Descripcion<input name="description"></label>
            <label>Categoria<input name="category"></label>
            <label>Precio<input name="price" type="number" step="0.01" required></label>
            <label>Imagen<input type="file" name="image" accept="image/*"></label>
            <button type="submit">Crear</button>
        </form>
    </section>

    <section>
        <h2>Listado</h2>
        <table class="tasm-admin-table"><thead><tr><th>ID</th><th>Nombre</th><th>Categoría</th><th>Precio</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($products as $p): ?>
            <tr>
                <td><?php echo $p['id'] ?></td>
                <td><?php echo htmlspecialchars($p['name']) ?></td>
                <td><?php echo htmlspecialchars($p['category']) ?></td>
                <td>$<?php echo number_format($p['price'],2) ?></td>
                <td>
                    <form method="post" class="tasm-delete-form" style="display:inline">
                        <input type="hidden" name="tasm_action" value="delete">
                        <input type="hidden" name="id" value="<?php echo $p['id'] ?>">
                        <button type="submit">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody></table>
    </section>
</main>
<script src="tasm_assets/js/tasm_admin.js"></script>
<script src="tasm_assets/js/tasm_toast.js"></script>
<script src="tasm_assets/js/tasm_app.js"></script>
</body>
</html>
