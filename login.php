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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #892e82;
            --primary-dark: #4b0c46;
            --secondary-color: #e782a0;
            --accent-color: #f5e6f3;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --border-color: #dee2e6;
            --shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            --shadow-hover: 0 4px 20px rgba(0, 0, 0, 0.15);
            --border-radius: 12px;
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
            max-width: 450px;
            text-align: center;
            animation: slideUp 0.6s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-logo {
            margin-bottom: 2rem;
        }
        
        .login-logo img {
            height: 80px;
            margin-bottom: 1rem;
            border-radius: 50%;
            box-shadow: var(--shadow);
        }
        
        .login-title {
            color: var(--primary-color);
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .login-subtitle {
            color: #666;
            font-size: 1rem;
            margin-bottom: 2rem;
        }
        
        .login-form {
            text-align: left;
        }
        
        .form-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--dark-color);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .form-group i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-color);
            font-size: 1.1rem;
            z-index: 2;
        }
        
        .form-control {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
            background: white;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(137, 46, 130, 0.1);
        }
        
        .btn-login {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 1rem;
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn-login:hover::before {
            left: 100%;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        .btn-login:active {
            transform: translateY(0);
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
            transition: var(--transition);
        }
        
        .login-footer a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            border: 1px solid #f5c6cb;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .demo-credentials {
            background: var(--accent-color);
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--primary-color);
        }

        .demo-credentials h4 {
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .demo-credentials p {
            margin: 0.25rem 0;
            font-size: 0.85rem;
            color: #666;
        }

        .demo-credentials code {
            background: white;
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            font-family: monospace;
            color: var(--primary-color);
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 2rem 1.5rem;
                margin: 1rem;
            }

            .login-title {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-logo">
            <img src="assets/images/logo.png" alt="Mary Human Hair" onerror="this.style.display='none'">
            <h1 class="login-title">Mary Human Hair</h1>
            <p class="login-subtitle">Sistema de Gestão</p>
        </div>
        
        <!-- Credenciais de demonstração -->
        <div class="demo-credentials">
            <h4><i class="fas fa-info-circle"></i> Credenciais de Acesso</h4>
            <p><strong>Email:</strong> <code>admin@maryhumanhair.cv</code></p>
            <p><strong>Senha:</strong> <code>admin123</code></p>
        </div>
        
        <?php if ($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="login-form" id="loginForm">
            <div class="form-group">
                <label class="form-label">Email</label>
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" class="form-control" placeholder="Digite seu email" required 
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label">Senha</label>
                <i class="fas fa-lock"></i>
                <input type="password" name="senha" class="form-control" placeholder="Digite sua senha" required>
            </div>
            
            <button type="submit" class="btn-login" id="loginBtn">
                <i class="fas fa-sign-in-alt"></i>
                Entrar no Sistema
            </button>
        </form>
        
        <div class="login-footer">
            <p>Não tem uma conta? <a href="#" onclick="alert('Entre em contato com o administrador')">Solicitar Acesso</a></p>
            <p><a href="#" onclick="alert('Entre em contato com o administrador para recuperar sua senha')">Esqueceu sua senha?</a></p>
        </div>
    </div>
    
    <script>
        // Adicionar efeitos visuais ao formulário
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('loginBtn');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Entrando...';
            btn.disabled = true;
        });

        // Auto-preencher campos para demonstração
        document.addEventListener('DOMContentLoaded', function() {
            const emailField = document.querySelector('input[name="email"]');
            const senhaField = document.querySelector('input[name="senha"]');
            
            // Se os campos estão vazios, preencher com dados demo
            if (!emailField.value) {
                emailField.value = 'admin@maryhumanhair.cv';
            }
            if (!senhaField.value) {
                senhaField.value = 'admin123';
            }
        });

        // Efeitos de foco nos campos
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>