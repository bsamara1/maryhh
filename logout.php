<?php
require_once 'includes/functions.php';

// Destruir sessão
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
session_destroy();

// Limpar cookies se existirem
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Redirecionar para login
header("Location: login.php");
exit();
?>