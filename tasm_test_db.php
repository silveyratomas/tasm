<?php
// Archivo de prueba para verificar conexión y contenido de la BD tasm_foodexpress
require_once __DIR__ . '/tasm_db.php';

try {
    $pdo = tasm_db_connect();
    $stmt = $pdo->query("SELECT COUNT(*) AS c FROM tasm_products");
    $row = $stmt->fetch();
    $count = $row ? intval($row['c']) : 0;
    echo '<!doctype html><html lang="es"><head><meta charset="utf-8"><title>Prueba DB</title></head><body>';
    echo '<h1>Conexión OK</h1>';
    echo '<p>Base de datos: <strong>tasm_foodexpress</strong></p>';
    echo '<p>Productos en tabla <code>tasm_products</code>: <strong>' . $count . '</strong></p>';
    echo '<p>Si ves este mensaje, la conexión funciona y el seed probablemente se ejecutó.</p>';
    echo '</body></html>';
} catch (Exception $e) {
    echo '<h1>Error de conexión</h1>';
    echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
}

?>
