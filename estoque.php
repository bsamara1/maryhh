<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

requireLogin();

$pageTitle = 'Controle de Estoque';
$db = new Database();

try {
    // Produtos com baixo estoque
    $produtosBaixoEstoque = $db->fetchAll("
        SELECT p.produto_id, p.nome, p.estoque, p.estoque_minimo, 
               c.nome as categoria_nome, p.preco
        FROM produtos p
        LEFT JOIN categorias c ON p.categoria_id = c.categoria_id
        WHERE p.ativo = 1 AND p.estoque <= p.estoque_minimo
        ORDER BY p.estoque ASC
    ");
    
    // Movimentos recentes
    $movimentosRecentes = $db->fetchAll("
        SELECT m.*, p.nome as produto_nome, u.nome as usuario_nome
        FROM movimentos_estoque m
        JOIN produtos p ON m.produto_id = p.produto_id
        LEFT JOIN utilizador u ON m.usuario_id = u.utilizador_id
        ORDER BY m.data_movimento DESC
        LIMIT 20
    ");
    
    // Estatísticas
    $totalProdutos = $db->fetch("SELECT COUNT(*) as total FROM produtos WHERE ativo = 1")['total'];
    $produtosSemEstoque = $db->fetch("SELECT COUNT(*) as total FROM produtos WHERE ativo = 1 AND estoque = 0")['total'];
    $valorTotalEstoque = $db->fetch("SELECT SUM(preco * estoque) as total FROM produtos WHERE ativo = 1")['total'] ?? 0;
    
} catch (Exception $e) {
    showAlert('Erro ao carregar dados do estoque.', 'danger');
    $produtosBaixoEstoque = $movimentosRecentes = [];
    $totalProdutos = $produtosSemEstoque = $valorTotalEstoque = 0;
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="main-content">
    <div class="page-header">
        <h1 class="page-title">Controle de Estoque</h1>
        <p class="page-subtitle">Monitore e gerencie seu estoque</p>
    </div>
    
    <!-- Estatísticas -->
    <div class="stats-grid mb-4">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-box"></i>
            </div>
            <div class="stat-number"><?php echo number_format($totalProdutos); ?></div>
            <div class="stat-label">Total de Produtos</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-number"><?php echo number_format(count($produtosBaixoEstoque)); ?></div>
            <div class="stat-label">Baixo Estoque</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-number"><?php echo number_format($produtosSemEstoque); ?></div>
            <div class="stat-label">Sem Estoque</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-number"><?php echo formatCurrency($valorTotalEstoque); ?></div>
            <div class="stat-label">Valor Total</div>
        </div>
    </div>
    
    <!-- Alertas de Baixo Estoque -->
    <?php if (!empty($produtosBaixoEstoque)): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-exclamation-triangle text-warning"></i>
                    Produtos com Baixo Estoque
                </h3>
                <span class="badge badge-warning"><?php echo count($produtosBaixoEstoque); ?> produtos</span>
            </div>
            
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Categoria</th>
                            <th>Estoque Atual</th>
                            <th>Estoque Mínimo</th>
                            <th>Valor Unitário</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produtosBaixoEstoque as $produto): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($produto['nome']); ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($produto['categoria_nome'] ?? 'Sem categoria'); ?></td>
                                <td>
                                    <span class="badge <?php echo $produto['estoque'] == 0 ? 'badge-danger' : 'badge-warning'; ?>">
                                        <?php echo $produto['estoque']; ?>
                                    </span>
                                </td>
                                <td><?php echo $produto['estoque_minimo']; ?></td>
                                <td><?php echo formatCurrency($produto['preco']); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" 
                                            onclick="adjustStock(<?php echo $produto['produto_id']; ?>, <?php echo $produto['estoque']; ?>)">
                                        <i class="fas fa-plus"></i> Repor
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Movimentos Recentes -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Movimentos Recentes</h3>
            <button class="btn btn-outline" onclick="exportMovimentos()">
                <i class="fas fa-download"></i> Exportar
            </button>
        </div>
        
        <?php if (!empty($movimentosRecentes)): ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Produto</th>
                            <th>Tipo</th>
                            <th>Quantidade</th>
                            <th>Estoque Anterior</th>
                            <th>Estoque Atual</th>
                            <th>Usuário</th>
                            <th>Motivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($movimentosRecentes as $movimento): ?>
                            <tr>
                                <td><?php echo formatDateTime($movimento['data_movimento']); ?></td>
                                <td><?php echo htmlspecialchars($movimento['produto_nome']); ?></td>
                                <td>
                                    <?php
                                    $badges = [
                                        'entrada' => 'badge-success',
                                        'saida' => 'badge-danger',
                                        'ajuste' => 'badge-info',
                                        'transferencia' => 'badge-warning'
                                    ];
                                    $class = $badges[$movimento['tipo']] ?? 'badge-secondary';
                                    ?>
                                    <span class="badge <?php echo $class; ?>">
                                        <?php echo ucfirst($movimento['tipo']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($movimento['tipo'] === 'entrada'): ?>
                                        <span class="text-success">+<?php echo $movimento['quantidade']; ?></span>
                                    <?php else: ?>
                                        <span class="text-danger"><?php echo $movimento['quantidade']; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $movimento['estoque_anterior']; ?></td>
                                <td><?php echo $movimento['estoque_atual']; ?></td>
                                <td><?php echo htmlspecialchars($movimento['usuario_nome'] ?? 'Sistema'); ?></td>
                                <td><?php echo htmlspecialchars($movimento['motivo'] ?? '-'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div style="padding: 2rem; text-align: center;">
                <i class="fas fa-history" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
                <h3>Nenhum movimento registrado</h3>
                <p class="text-muted">Os movimentos de estoque aparecerão aqui.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<!-- Modal Ajustar Estoque -->
<div class="modal" id="stockModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Ajustar Estoque</h3>
            <button class="modal-close" onclick="closeModal('stockModal')">&times;</button>
        </div>
        
        <form id="stockForm">
            <input type="hidden" id="produto_id" name="produto_id">
            
            <div class="form-group">
                <label class="form-label">Estoque Atual</label>
                <input type="number" id="estoque_atual" class="form-control" readonly>
            </div>
            
            <div class="form-group">
                <label class="form-label">Novo Estoque *</label>
                <input type="number" name="estoque" id="novo_estoque" class="form-control" min="0" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Motivo</label>
                <select name="motivo" class="form-control form-select">
                    <option value="Reposição">Reposição</option>
                    <option value="Ajuste de inventário">Ajuste de inventário</option>
                    <option value="Produto danificado">Produto danificado</option>
                    <option value="Transferência">Transferência</option>
                    <option value="Outros">Outros</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Observações</label>
                <textarea name="observacoes" class="form-control" rows="3"></textarea>
            </div>
            
            <div class="d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-outline" onclick="closeModal('stockModal')">
                    Cancelar
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Atualizar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function adjustStock(id, currentStock) {
    document.getElementById('produto_id').value = id;
    document.getElementById('estoque_atual').value = currentStock;
    document.getElementById('novo_estoque').value = currentStock;
    openModal('stockModal');
}

function exportMovimentos() {
    window.open('exports/movimentos_estoque.php', '_blank');
}

// Submissão do formulário de ajustar estoque
document.getElementById('stockForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = {
        produto_id: document.getElementById('produto_id').value,
        estoque: document.getElementById('novo_estoque').value
    };
    
    showLoading();
    
    fetch('actions/produto_adjust_stock.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showAlert('Estoque atualizado com sucesso!', 'success');
            closeModal('stockModal');
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert(data.message || 'Erro ao atualizar estoque.', 'danger');
        }
    })
    .catch(error => {
        hideLoading();
        showAlert('Erro ao atualizar estoque.', 'danger');
    });
});
</script>

<?php include 'includes/footer.php'; ?>