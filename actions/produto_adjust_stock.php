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
    $input = json_decode(file_get_contents('php://input'), true);
    $produto_id = intval($input['produto_id'] ?? 0);
    $estoque = intval($input['estoque'] ?? 0);
    
    if (!$produto_id) {
        throw new Exception('ID do produto é obrigatório.');
    }
    
    if ($estoque < 0) {
        throw new Exception('Estoque não pode ser negativo.');
    }
    
    $db = new Database();
    
    // Verificar se produto existe
    $produto = $db->fetch("SELECT produto_id, estoque FROM produtos WHERE produto_id = ?", [$produto_id]);
    if (!$produto) {
        throw new Exception('Produto não encontrado.');
    }
    
    // Atualizar estoque
    $result = $db->execute("UPDATE produtos SET estoque = ? WHERE produto_id = ?", [$estoque, $produto_id]);
    
    if ($result) {
        // Registrar movimento de estoque
        $db->execute("
            INSERT INTO movimentos_estoque (produto_id, tipo, quantidade, estoque_anterior, estoque_atual, observacao, data_movimento) 
            VALUES (?, 'ajuste', ?, ?, ?, 'Ajuste manual', NOW())
        ", [$produto_id, $estoque - $produto['estoque'], $produto['estoque'], $estoque]);
        
        echo json_encode(['success' => true, 'message' => 'Estoque atualizado com sucesso!']);
    } else {
        throw new Exception('Erro ao atualizar estoque.');
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>