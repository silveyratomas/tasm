<?php
// Script de migracion ligero: agrega columna image y tabla tasm_admins si no existen
require_once __DIR__ . '/tasm_db.php';
try {
    $pdo = tasm_db_connect();
    // agregar columna image si no existe
    $check = $pdo->prepare("SELECT COUNT(*) as c FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tasm_products' AND COLUMN_NAME = 'image'");
    $check->execute(); $r = $check->fetch();
    if (intval($r['c']) === 0) {
        $pdo->exec("ALTER TABLE tasm_products ADD COLUMN image VARCHAR(255) DEFAULT NULL");
        echo "Columna 'image' agregada a tasm_products.<br>";
    } else {
        echo "Columna 'image' ya existe.<br>";
    }

    // crear tabla tasm_admins si no existe
    $sql = "CREATE TABLE IF NOT EXISTS tasm_admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(191) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $pdo->exec($sql);
    echo "Tabla 'tasm_admins' verificada/creada.<br>";
    echo "Si queres crear un admin ejecuta tasm_setup_admin.php desde el navegador o CLI.";
} catch (Exception $e) {
    echo 'Error migracion: '.htmlspecialchars($e->getMessage());
}
