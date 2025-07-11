<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "maryhh";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$idSelecionado = isset($_POST['id']) ? $_POST['id'] : null;

// Carregar todos os IDs de utilizador para o <select>
$sqlIds = "SELECT utilizador_id, nome FROM utilizador";
$resultIds = $conn->query($sqlIds);

// Se foi enviado um ID, buscar os dados dele
$utilizador = null;
if ($idSelecionado) {
  $stmt = $conn->prepare("SELECT nome, email, senha, telefone, endereco, idTipoUtilizador FROM utilizador WHERE utilizador_id = ?");
  $stmt->bind_param("i", $idSelecionado);
  $stmt->execute();
  $resultado = $stmt->get_result();
  if ($resultado->num_rows > 0) {
    $utilizador = $resultado->fetch_assoc();
  }
  $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <title>Gestão Mary Human Hair - Atualizar Utilizador</title>
  <link rel="stylesheet" href="atualizar.css">
</head>
<body>
  <div class="retangulo">
    <h2>Utilizador</h2>

    <form method="post">
      <label for="utilizador_id">Selecionar Utilizador:</label>
      <select name="id" id="utilizador_id" onchange="this.form.submit()">
        <option value="">-- Selecione um utilizador --</option>
        <?php
        if ($resultIds->num_rows > 0) {
          while ($row = $resultIds->fetch_assoc()) {
            $selected = ($row['utilizador_id'] == $idSelecionado) ? 'selected' : '';
            echo "<option value='{$row['utilizador_id']}' $selected>{$row['utilizador_id']} - {$row['nome']}</option>";
          }
        }
        ?>
      </select>
    </form>

    <?php if ($utilizador): ?>
      <p>Atualizar Dados do Utilizador</p>
      <form action="InserirActualizar.php" method="post">
        <input type="hidden" name="id" value="<?php echo $idSelecionado; ?>">
        <input type="text" name="fnome" value="<?php echo $utilizador['nome']; ?>"><br>
        <input type="email" name="femail" value="<?php echo $utilizador['email']; ?>"><br>
        <input type="password" name="fsenha" value="<?php echo $utilizador['senha']; ?>"><br>
        <input type="text" name="ftelefone" value="<?php echo $utilizador['telefone']; ?>"><br>
        <input type="text" name="fendereco" value="<?php echo $utilizador['endereco']; ?>"><br>
        <input type="text" name="fperfil" value="<?php echo $utilizador['idTipoUtilizador']; ?>"><br>
        <button type="submit">Atualizar</button>
      </form>
    <?php endif; ?>
  </div>
</body>
</html>
