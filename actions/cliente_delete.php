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
        throw new Exception('ID do cliente é obrigatório.');
    }
    
    $db = new Database();
    
    // Verificar se cliente existe
    $cliente = $db->fetch("SELECT idCliente FROM clientes WHERE idCliente = ?", [$id]);
    if (!$cliente) {
        throw new Exception('Cliente não encontrado.');
    }
    
    // Verificar se cliente tem pedidos
    $pedidos = $db->fetch("SELECT COUNT(*) as total FROM pedidos WHERE idCliente = ?", [$id]);
    if ($pedidos['total'] > 0) {
        throw new Exception('Não é possível excluir cliente com pedidos associados.');
    }
    
    // Excluir cliente
    $result = $db->execute("DELETE FROM clientes WHERE idCliente = ?", [$id]);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Cliente excluído com sucesso!']);
    } else {
        throw new Exception('Erro ao excluir cliente.');
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>