<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "maryhh";

// Criação da conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Consulta SQL
$sql = "SELECT p.pedido_id, c.nome, p.destino, p.total, p.status, p.data_pedido
FROM pedidos p
JOIN clientes c ON p.idCliente = c.idCliente;";

$resultado = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Gestão Mary Huma Hair - Pedidos</title>
    <link rel="stylesheet" href="pedidos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

    <!-- Topbar -->
    <div class="topbar">
       
        <div class="topbar-left">
            <img class="logo" src="./MaryHumanHair.png" width="210" height="60" alt="Logo Mary Human Hair">
        </div>

        <nav class="topbar-nav">
            <a href="gestao.html" id="menu-inicio">Inicio</a>
            <a href="perfil.html">Perfil</a>
            <a href="visao.html">Visão Geral</a>
            <a href="ajuda.html">Ajuda</a>
        </nav>

        <div class="topbar-right">

            <div class="search-wrapper">
                <input type="text" class="search" placeholder="Pesquisar...">
                <i class="fas fa-search search-icon"></i>
            </div>
            <div class="user">
                <img src="mary-removebg-preview (1).png" alt="Foto de perfil de Mary">
                <span class="admin">
                    <strong>Mary Admin</strong>
                    <span class="ad">maryhumanhair@gmail.com</span>
                </span>
            </div>
        </div>
    </div>

    <div class="container">
        <aside class="sidebar">
            <nav>
                <a href="gestao.html"><i class="fas fa-tachometer-alt"></i> Painel de Administração</a>
                <a href="pedidos.php"><i class="fas fa-box"></i> Pedidos</a>
                <a href="clientes.php"><i class="fas fa-users"></i> Clientes</a>
                <a href="produtos.html"><i class="fas fa-boxes-stacked"></i> Produtos e Estoque</a>
                <a href="relatorio.html"><i class="fas fa-chart-line"></i> Relatórios de Vendas</a>
                <a href="formUtilizador.html"><i class="fas fa-user-cog"></i> Adicionar Utilizador</a>
                <a href="atualizarUtilizador.php"><i class="fas fa-sync-alt"></i> Atualizar dados dos utilizadores</a>
                <a href="logout.php" class="edit-icon" title="Sair/Logout">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>

            </nav>
        </aside>

        
            <h1>Pedidos</h1>
            <p class="status-filtros">Todos | <span class="status em-espera">Em espera |</span> <span
                    class="status processando">Processando |</span> <span class="status concluido">Concluído |</span>
                Cancelado</p>

            <table class="tabela-clientes">
                <thead>
                    <tr>
                        <th># Pedido</th>
                        <th>Data</th>
                        <th>Cliente</th>
                        <th>Destino</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($resultado->num_rows > 0) {
                        while ($row = $resultado->fetch_assoc()) {
                            // Formatação de data (opcional)
                            $data_formatada = date("d/m/Y", strtotime($row["data_pedido"]));

                            // Badge de status (classe CSS com base no valor do banco)
                            $classe_status = strtolower($row["status"]); // exemplo: "concluido", "em-espera", etc.
                            echo "<tr>
                                    <td>#{$row["pedido_id"]}</td>
                                    <td>{$data_formatada}</td>
                                    <td>{$row["nome"]}</td>
                                    <td>{$row["destino"]}</td>
                                    <td>CVE {$row["total"]}</td>
                                    <td><span class='badge {$classe_status}'>{$row["status"]}</span></td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>Nenhum pedido encontrado.</td></tr>";
                    }
                    $conn->close();
                    ?>
                </tbody>
            </table>
       
    </div>
</body>

</html>