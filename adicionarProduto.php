<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $conn = new mysqli("localhost", "root", "", "maryhh");

  $nome = $_POST['nome'];
  $tipo = $_POST['tipo'];
  $comprimento = $_POST['comprimento'];
  $textura = $_POST['textura'];
  $cor = $_POST['cor'];
  $preco = $_POST['preco'];
  $estoque = $_POST['estoque'];
  $descricao = $_POST['descricao'];

  $stmt = $conn->prepare("INSERT INTO produtos (nome, tipo, comprimento, textura, cor, preco, estoque, descricao) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("ssdssdis", $nome, $tipo, $comprimento, $textura, $cor, $preco, $estoque, $descricao);
  $stmt->execute();

  header("Location: produtos.php");
}
?>

<form method="POST">
  <h2>Adicionar Produto</h2>
  <label>Nome:</label><input type="text" name="nome" required><br>
  <label>Tipo:</label><input type="text" name="tipo"><br>
  <label>Comprimento (cm):</label><input type="number" step="0.01" name="comprimento"><br>
  <label>Textura:</label><input type="text" name="textura"><br>
  <label>Cor:</label><input type="text" name="cor"><br>
  <label>Preço (€):</label><input type="number" step="0.01" name="preco" required><br>
  <label>Estoque:</label><input type="number" name="estoque" required><br>
  <label>Descrição:</label><br>
  <textarea name="descricao" rows="4" cols="50"></textarea><br>
  <button type="submit">Salvar</button>
</form>
