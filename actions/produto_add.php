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
    $tipo = sanitizeInput($_POST['tipo'] ?? '');
    $comprimento = floatval($_POST['comprimento'] ?? 0);
    $textura = sanitizeInput($_POST['textura'] ?? '');
    $cor = sanitizeInput($_POST['cor'] ?? '');
    $preco = floatval($_POST['preco'] ?? 0);
    $estoque = intval($_POST['estoque'] ?? 0);
    $descricao = sanitizeInput($_POST['descricao'] ?? '');
    
    // Validações
    if (empty($nome)) {
        throw new Exception('Nome do produto é obrigatório.');
    }
    
    if ($preco <= 0) {
        throw new Exception('Preço deve ser maior que zero.');
    }
    
    if ($estoque < 0) {
        throw new Exception('Estoque não pode ser negativo.');
    }
    
    $db = new Database();
    
    // Inserir produto
    $result = $db->execute("
        INSERT INTO produtos (nome, tipo, comprimento, textura, cor, preco, estoque, descricao, data_criacao) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ", [
        $nome, 
        $tipo ?: null, 
        $comprimento ?: null, 
        $textura ?: null, 
        $cor ?: null, 
        $preco, 
        $estoque, 
        $descricao ?: null
    ]);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Produto adicionado com sucesso!']);
    } else {
        throw new Exception('Erro ao adicionar produto.');
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>