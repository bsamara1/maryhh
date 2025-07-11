<?php

$vnome = $_POST['fnome'];
$vemail = $_POST['femail'];
$vsenha = $_POST['fsenha'];
$vtelefone = $_POST['ftelefone'];
$vendereco= $_POST['fendereco'];
$vperfil = 2;





$servername = "localhost";
$username = "root";
$password = "";
$dbname = "maryhh";

try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $sql = "INSERT INTO utilizador (nome, email,  senha, telefone,endereco, idTipoUtilizador )
  VALUES ('$vnome','$vemail' ,'$vsenha','$vtelefone','$vendereco',$vperfil )";
  // use exec() because no results are returned
  $conn->exec($sql);
  echo "Utilizador inserido com sucesso!";
} catch (PDOException $e) {
  echo $sql . "<br>" . $e->getMessage();
}

$conn = null;

header("Location: ");
exit();
?>