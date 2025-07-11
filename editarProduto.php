<?php
$conn = new mysqli("localhost", "root", "", "maryhh");
$id = $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nome = $_POST['nome'];
  $tipo = $_POST['tipo'];
  $comprimento = $_POST['comprimento'];
  $textura = $_POST['textura'];
  $cor = $_POST['cor'];
  $preco = $_POST['preco'];
  $estoque = $_POST['estoque'];
  $descricao = $_POST['descricao'];

  $stmt = $conn->prepare("UPDATE produtos SET nome=?, tipo=?, comprimento=?, textura=?, cor=?, preco=?, estoque=?, descricao=? WHERE produto_id=?");
  $stmt->bind_param("ssdssdisi", $nome, $tipo, $comprimento, $textura, $cor, $preco, $estoque, $descricao, $id);
  $stmt->execute();

  header("Location: produtos.php");
}

$produto = $conn->query("SELECT * FROM produtos WHERE produto_id = $id")->fetch_assoc();
?>

<form method="POST">
  <h2>Editar Produto</h2>
  <input type="text" name="nome" value="<?= $produto['nome'] ?>" required><br>
  <input type="text" name="tipo" value="<?= $produto['tipo'] ?>"><br>
  <input type="number" step="0.01" name="comprimento" value="<?= $produto['comprimento'] ?>"><br>
  <input type="text" name="textura" value="<?= $produto['textura'] ?>"><br>
  <input type="text" name="cor" value="<?= $produto['cor'] ?>"><br>
  <input type="number" step="0.01" name="preco" value="<?= $produto['preco'] ?>" required><br>
  <input type="number" name="estoque" value="<?= $produto['estoque'] ?>" required><br>
  <textarea name="descricao"><?= $produto['descricao'] ?></textarea><br>
  <button type="submit">Atualizar</button>
</form>
