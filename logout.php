<?php
session_start(); // Iniciar a sessão

// Eliminar todas as variáveis de sessão
$_SESSION = array();

// Se existir um cookie de sessão, apaga-o
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destruir a sessão
session_destroy();

// Redirecionar para a página de login (ou homepage)
header("Location: Login/index.html");
exit();
?>
