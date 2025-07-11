<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireLogin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit();
}

try {
    $id = intval($_GET['id'] ?? 0);
    
    if (!$id) {
        throw new Exception('ID do produto é obrigatório.');
    }
    
    $db = new Database();
    
    // Verificar se produto existe
    $produto = $db->fetch("SELECT produto_id FROM produtos WHERE produto_id = ?", [$id]);
    if (!$produto) {
        throw new Exception('Produto não encontrado.');
    }
    
    // Verificar se produto tem pedidos associados
    $pedidos = $db->fetch("SELECT COUNT(*) as total FROM itens_pedido WHERE produto_id = ?", [$id]);
    if ($pedidos && $pedidos['total'] > 0) {
        throw new Exception('Não é possível excluir produto com pedidos associados.');
    }
    
    // Excluir produto
    $result = $db->execute("DELETE FROM produtos WHERE produto_id = ?", [$id]);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Produto excluído com sucesso!']);
    } else {
        throw new Exception('Erro ao excluir produto.');
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>