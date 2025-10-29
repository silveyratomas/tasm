<?php
// Archivo: tasm_db.php
// Conexión a la base de datos y utilidades. Prefijos: tasm_

function tasm_db_connect() {
    static $pdo = null;
    if ($pdo) return $pdo;
    $host = '127.0.0.1';
    $db   = 'tasm_foodexpress';
    $user = 'root';
    $pass = '';
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        return $pdo;
    } catch (Exception $e) {
        http_response_code(500);
        echo 'Error DB: ' . htmlspecialchars($e->getMessage());
        exit;
    }
}

function tasm_get_categories() {
    $pdo = tasm_db_connect();
    $stmt = $pdo->query("SELECT DISTINCT category FROM tasm_products ORDER BY category");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function tasm_get_products($category = null) {
    $pdo = tasm_db_connect();
    if ($category) {
        $stmt = $pdo->prepare("SELECT * FROM tasm_products WHERE category = :c ORDER BY id");
        $stmt->execute(['c' => $category]);
    } else {
        $stmt = $pdo->query("SELECT * FROM tasm_products ORDER BY id");
    }
    return $stmt->fetchAll();
}

function tasm_get_product($id) {
    $pdo = tasm_db_connect();
    $stmt = $pdo->prepare("SELECT * FROM tasm_products WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch();
}

?>
