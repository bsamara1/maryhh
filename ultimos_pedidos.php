<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "maryhh";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Conexão falhou: " . $conn->connect_error);
}

$sql = "SELECT p.pedido_id, c.nome, p.destino, p.total, p.status, p.data_pedido
        FROM pedidos p
        JOIN clientes c ON p.idCliente = c.idCliente
        ORDER BY p.pedido_id DESC
        LIMIT 5";

$resultado = $conn->query($sql);

// Início da tabela
if ($resultado->num_rows > 0) {
  echo "<table class='tabela-ultimos'>";
  echo "<thead><tr>
            <th># Pedidos</th>
            <th>Data</th>
            <th>Cliente</th>
            <th>Status</th>
          </tr></thead><tbody>";

  while ($row = $resultado->fetch_assoc()) {
    $data = date("d/m/Y", strtotime($row["data_pedido"]));
    $statusClasse = strtolower($row["status"]);
    echo "<tr>
                <td>#{$row['pedido_id']}</td>
                <td>{$data}</td>
                <td>{$row['nome']}</td>
                <td><span class='badge {$statusClasse}'>{$row['status']}</span></td>
              </tr>";
  }

  echo "</tbody></table>";
} else {
  echo "<p>Nenhum pedido encontrado.</p>";
}

$conn->close();
?>