<?php

$vnome = $_POST['fnome'];
$vtelefone = $_POST['ftelefone'];
$vemail = $_POST['femail'];
$vsenha = $_POST['fsenha'];
$vendereco = $_POST['fendereco'];
$vperfil = $_POST['fperfil'];



$servername = "localhost";
$username = "root";
$password = "";
$dbname = "maryhh";

try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $sql = "UPDATE utilizador SET nome='$vnome', 
    email='$vemail',
    senha='$vsenha', telefone='$vtelefone', endereco='$vendereco', idTipoUtilizador='$vperfil'";

  // Prepare statement
  $stmt = $conn->prepare($sql);

  // execute the query
  $stmt->execute();

  // echo a message to say the UPDATE succeeded
  echo $stmt->rowCount() . " records UPDATED successfully";
} catch(PDOException $e) {
  echo $sql . "<br>" . $e->getMessage();
}

$conn = null;
?>