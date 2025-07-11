<?php
// Redirecionar para login se não estiver logado
require_once 'includes/functions.php';

if (isLoggedIn()) {
    header("Location: dashboard.php");
} else {
    header("Location: login.php");
}
exit();
?>