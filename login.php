<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Se já estiver logado, redirecionar
if (isLoggedIn()) {
    header("Location: /dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    
    if (empty($email) || empty($senha)) {
        $error = 'Por favor, preencha todos os campos.';
    } else {
        try {
            $db = new Database();
            $user = $db->fetch(
                "SELECT * FROM utilizador WHERE email = ? AND senha = ?",
                [$email, $senha]
            );
            
            if ($user) {
                $_SESSION['user_id'] = $user['utilizador_id'];
                $_SESSION['user_name'] = $user['nome'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_type'] = $user['idTipoUtilizador'];
                
                // Atualizar último login
                $db->execute(
                    "UPDATE utilizador SET ultimo_login = NOW() WHERE utilizador_id = ?",
                    [$user['utilizador_id']]
                );
                
                showAlert('Login realizado com sucesso!', 'success');
                header("Location: /dashboard.php");
                exit();
            } else {
                $error = 'Email ou senha incorretos.';
            }
        } catch (Exception $e) {
            $error = 'Erro interno. Tente novamente.';
        }
    }
}

$pageTitle = 'Login';
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Mary Human Hair</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        
        .login-container {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-hover);
            padding: 3rem;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        
        .login-logo {
            margin-bottom: 2rem;
        }
        
        .login-logo img {
            height: 60px;
            margin-bottom: 1rem;
        }
        
        .login-title {
            color: var(--primary-color);
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 2rem;
        }
        
        .login-form {
            text-align: left;
        }
        
        .form-group {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .form-group i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-color);
        }
        
        .form-control {
            padding-left: 3rem;
        }
        
        .btn-login {
            width: 100%;
            padding: 1rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn-login:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }
        
        .login-footer {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border-color);
        }
        
        .login-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .login-footer a:hover {
            text-decoration: underline;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-logo">
            <img src="/assets/images/logo.png" alt="Mary Human Hair" onerror="this.style.display='none'">
            <h1 class="login-title">Mary Human Hair</h1>
        </div>
        
        <?php if ($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="login-form">
            <div class="form-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" class="form-control" placeholder="Email" required 
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="senha" class="form-control" placeholder="Senha" required>
            </div>
            
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i>
                Entrar
            </button>
        </form>
        
        <div class="login-footer">
            <p>Não tem uma conta? <a href="/registro.php">Criar conta</a></p>
            <p><a href="/recuperar-senha.php">Esqueceu sua senha?</a></p>
        </div>
    </div>
    
    <script src="/assets/js/main.js"></script>
</body>
</html>