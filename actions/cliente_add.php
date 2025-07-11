<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireLogin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit();
}

try {
    $nome = sanitizeInput($_POST['nome'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $telefone = sanitizeInput($_POST['telefone'] ?? '');
    $data_nascimento = $_POST['data_nascimento'] ?? null;
    $endereco = sanitizeInput($_POST['endereco'] ?? '');
    
    // Validações
    if (empty($nome) || empty($email) || empty($telefone)) {
        throw new Exception('Nome, email e telefone são obrigatórios.');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Email inválido.');
    }
    
    $db = new Database();
    
    // Verificar se email já existe
    $existingClient = $db->fetch("SELECT idCliente FROM clientes WHERE email = ?", [$email]);
    if ($existingClient) {
        throw new Exception('Já existe um cliente com este email.');
    }
    
    // Inserir cliente
    $result = $db->execute("
        INSERT INTO clientes (nome, email, telefone, data_nascimento, endereco, data_registo) 
        VALUES (?, ?, ?, ?, ?, NOW())
    ", [$nome, $email, $telefone, $data_nascimento ?: null, $endereco]);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Cliente adicionado com sucesso!']);
    } else {
        throw new Exception('Erro ao adicionar cliente.');
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>