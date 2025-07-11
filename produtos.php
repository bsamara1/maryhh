<?php
$conn = new mysqli("localhost", "root", "", "maryhh");
$result = $conn->query("SELECT * FROM produtos ORDER BY produto_id ASC");

?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <title>Estoque - Produtos</title>
  <style>
    table { border-collapse: collapse; width: 100%; }
    th, td { padding: 8px; border: 1px solid #ccc; text-align: center; }
    th { background-color: #eee; }
    .btn { padding: 4px 10px; border: 1px solid #000; margin: 2px; text-decoration: none; }
  </style>
</head>
<body>

<h2>Gestão de Produtos em Estoque</h2>
<a class="btn" href="adicionarProduto.php">+ Adicionar Produto</a>

<table>
  <tr>
    <th>ID</th>
    <th>Nome</th>
    <th>Tipo</th>
    <th>Comprimento</th>
    <th>Textura</th>
    <th>Cor</th>
    <th>Preço (€)</th>
    <th>Estoque</th>
    <th>Ações</th>
  </tr>
  <?php while ($p = $result->fetch_assoc()) { ?>
  <tr>
    
    <td><?= $p['produto_id'] ?></td>
    <td><?= $p['nome'] ?></td>
    <td><?= $p['tipo'] ?></td>
    <td><?= $p['comprimento'] ?> cm</td>
    <td><?= $p['textura'] ?></td>
    <td><?= $p['cor'] ?></td>
    <td><?= $p['preco'] ?> €</td>
    <td><?= $p['estoque'] ?></td>
    <td>
      <a class="btn" href="editarProduto.php?id=<?= $p['produto_id'] ?>">Editar</a>
      <a class="btn" href="eliminarProduto.php?id=<?= $p['produto_id'] ?>" onclick="return confirm('Eliminar este produto?')">Eliminar</a>
    </td>
  </tr>
  <?php } ?>
</table>

</body>
</html>
