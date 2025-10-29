<?php
session_start();
unset($_SESSION['tasm_admin_logged']);
session_destroy();
header('Location: tasm_admin_login.php');
exit;
