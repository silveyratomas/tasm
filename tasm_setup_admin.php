<?php
// Script simple para crear un admin inicial (usa POST o definir variables abajo)
require_once __DIR__ . '/tasm_db.php';
try {
    $pdo = tasm_db_connect();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user = trim($_POST['username'] ?? 'admin');
        $pass = trim($_POST['password'] ?? 'admin123');
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO tasm_admins (username, password_hash) VALUES (:u, :h)");
        $stmt->execute(['u'=>$user,'h'=>$hash]);
        echo "Admin creado: " . htmlspecialchars($user);
        exit;
    }
} catch (Exception $e) {
    echo 'Error: '.htmlspecialchars($e->getMessage());
    exit;
}
?><!doctype html>
<html><head><meta charset="utf-8"><title>Crear admin</title></head><body>
<h3>Crear admin inicial</h3>
<form method="post">
  <label>Usuario<input name="username" value="admin"></label><br>
  <label>Password<input name="password" value="admin123"></label><br>
  <button type="submit">Crear</button>
</form>
</body></html>
