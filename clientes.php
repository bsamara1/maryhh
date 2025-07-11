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

// Consulta SQL para clientes
$sql = "SELECT idCliente, nome, email,telefone, data_registo FROM clientes";
$resultado = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt">

<head>
  <meta charset="UTF-8">
  <title>Gestão Mary Human Hair - Pedidos</title>
  <link rel="stylesheet" href="clientes.css">
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

  <!-- Sidebar + Conteúdo -->
  <div class="container">
    <aside class="sidebar">
      <nav>
        <a href="gestao.html"><img src="" alt="Logo Gestão" width="10" height="10">
          Painel de Administração</a>
        <a href="pedidos.php"><i class="fas fa-box"></i> Pedidos</a>
        <a href="clientes.html"><i class="fas fa-users"></i> Clientes</a>
        <a href="produtos.html"><i class="fas fa-boxes-stacked"></i> Produtos e Estoque</a>
        <a href="relatorio.html"><i class="fas fa-chart-line"></i> Relatórios de Vendas</a>
        <a href="formUtilizador.html"><i class="fas fa-user-cog"></i> Adicionar Utilizador</a>
        <a href="atualizarUtilizador.php"><i class="fas fa-sync-alt"></i> Atualizar dados dos utilizadores</a>
        <a href="http://localhost/LDAW/wordpress/wp-admin/" class="edit-icon" title="Editar site">
          <i class="fas fa-pen"></i>Editar site
        </a>
      </nav>
    </aside>

    <h1>Clientes</h1>

    <table class="tabela-clientes">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>Email</th>
          <th>Telefone</th>
          <th>Data de Registo</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if ($resultado->num_rows > 0) {
          while ($row = $resultado->fetch_assoc()) {
            $data_formatada = date("d/m/Y", strtotime($row["data_registo"]));
            echo "<tr>
                    <td>{$row["idCliente"]}</td>
                    <td>{$row["nome"]}</td>
                    <td>{$row["email"]}</td>
                    <td>{$row["telefone"]}</td>
                    <td>{$data_formatada}</td>
                  </tr>";
          }
        } else {
          echo "<tr><td colspan='6'>Nenhum cliente encontrado.</td></tr>";
        }
        $conn->close();
        ?>
      </tbody>
    </table>
  </div>
</body>

</html>