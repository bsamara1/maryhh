<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "maryhh";

// Verifica se os dados foram enviados
if (!isset($_POST['femail']) || !isset($_POST['fsenha'])) {
    echo "Preencha todos os campos.";
    exit;
}

$vemail = $_POST['femail'];
$vsenha = $_POST['fsenha'];

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Busca utilizador com email e senha corretos
    $sql = "SELECT * FROM utilizador WHERE email = :email AND senha = :senha";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $vemail);
    $stmt->bindParam(':senha', $vsenha);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $utilizador = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION['email'] = $utilizador['email'];
        $_SESSION['tipo'] = $utilizador['tipo'];

        // Redirecionamento por tipo
        if ($utilizador['idTipoUtilizador'] === 1) {
            header("Location: http://localhost/Maryhh/gestao.html");
        } else {
            header("Location: http://localhost/LDAW/wordpress/");
        }
        exit;
    } else {
        echo "Email ou palavra-passe incorretos!";
    }

} catch(PDOException $e) {
    echo "Erro: " . $e->getMessage();
}

$conn = null;
?>
