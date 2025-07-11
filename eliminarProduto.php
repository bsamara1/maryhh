<?php
$conn = new mysqli("localhost", "root", "", "maryhh");
$id = $_GET['id'];
$conn->query("DELETE FROM produtos WHERE produto_id = $id");
header("Location: produtos.php");
?>