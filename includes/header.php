<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Mary Human Hair</title>
    <link rel="icon" type="image/x-icon" href="/assets/images/favicon.ico">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <?php if (isset($extraCSS)): ?>
        <?php foreach ($extraCSS as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <?php displayAlert(); ?>
    
    <header class="header">
        <div class="header-left">
            <a href="/dashboard.php" class="logo">
                <img src="/assets/images/logo.png" alt="Mary Human Hair">
                <span>Mary Human Hair</span>
            </a>
        </div>
        
        <nav class="header-nav">
            <a href="/dashboard.php">Dashboard</a>
            <a href="/pedidos.php">Pedidos</a>
            <a href="/clientes.php">Clientes</a>
            <a href="/produtos.php">Produtos</a>
            <a href="/relatorios.php">Relatórios</a>
        </nav>
        
        <div class="header-right">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Pesquisar...">
            </div>
            
            <div class="user-menu" onclick="toggleUserMenu()">
                <img src="/assets/images/user-avatar.png" alt="Avatar" class="user-avatar">
                <span><?php echo $_SESSION['user_name'] ?? 'Admin'; ?></span>
                <i class="fas fa-chevron-down"></i>
            </div>
            
            <div class="user-dropdown" id="userDropdown" style="display: none;">
                <a href="/perfil.php"><i class="fas fa-user"></i> Perfil</a>
                <a href="/configuracoes.php"><i class="fas fa-cog"></i> Configurações</a>
                <hr>
                <a href="/logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
            </div>
        </div>
    </header>
    
    <div class="main-layout">