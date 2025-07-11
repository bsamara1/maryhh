<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

requireLogin();

$pageTitle = 'Pedidos';
$db = new Database();

// Filtros
$status = sanitizeInput($_GET['status'] ?? '');
$search = sanitizeInput($_GET['search'] ?? '');
$data_inicio = $_GET['data_inicio'] ?? '';
$data_fim = $_GET['data_fim'] ?? '';

$whereClause = 'WHERE 1=1';
$params = [];

if ($status) {
    $whereClause .= " AND p.status = ?";
    $params[] = $status;
}

if ($search) {
    $whereClause .= " AND (c.nome LIKE ? OR p.pedido_id LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($data_inicio) {
    $whereClause .= " AND DATE(p.data_pedido) >= ?";
    $params[] = $data_inicio;
}

if ($data_fim) {
    $whereClause .= " AND DATE(p.data_pedido) <= ?";
    $params[] = $data_fim;
}

try {
    $pedidos = $db->fetchAll("
        SELECT p.pedido_id, c.nome, p.destino, p.total, p.status, p.data_pedido, p.observacoes
        FROM pedidos p
        JOIN clientes c ON p.idCliente = c.idCliente
        $whereClause
        ORDER BY p.data_pedido DESC
    ", $params);
    
    // Estatísticas
    $totalPedidos = $db->fetch("SELECT COUNT(*) as total FROM pedidos")['total'];
    $pedidosPendentes = $db->fetch("SELECT COUNT(*) as total FROM pedidos WHERE status = 'pendente'")['total'];
    $pedidosProcessando = $db->fetch("SELECT COUNT(*) as total FROM pedidos WHERE status = 'processando'")['total'];
    $pedidosConcluidos = $db->fetch("SELECT COUNT(*) as total FROM pedidos WHERE status = 'concluido'")['total'];
    
} catch (Exception $e) {
    showAlert('Erro ao carregar pedidos.', 'danger');
    $pedidos = [];
    $totalPedidos = $pedidosPendentes = $pedidosProcessando = $pedidosConcluidos = 0;
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="main-content">
    <div class="page-header">
        <h1 class="page-title">Pedidos</h1>
        <p class="page-subtitle">Gerencie todos os pedidos</p>
    </div>
    
    <!-- Estatísticas -->
    <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 2rem;">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stat-number"><?php echo number_format($totalPedidos); ?></div>
            <div class="stat-label">Total de Pedidos</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-number"><?php echo number_format($pedidosPendentes); ?></div>
            <div class="stat-label">Pendentes</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-cog"></i>
            </div>
            <div class="stat-number"><?php echo number_format($pedidosProcessando); ?></div>
            <div class="stat-label">Processando</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-number"><?php echo number_format($pedidosConcluidos); ?></div>
            <div class="stat-label">Concluídos</div>
        </div>
    </div>
    
    <!-- Filtros e Lista -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Pedidos</h3>
            <div class="d-flex gap-2">
                <button class="btn btn-primary" onclick="openModal('addOrderModal')">
                    <i class="fas fa-plus"></i> Novo Pedido
                </button>
                <button class="btn btn-outline" onclick="exportPedidos()">
                    <i class="fas fa-download"></i> Exportar
                </button>
            </div>
        </div>
        
        <!-- Filtros -->
        <div style="padding: 1rem; border-bottom: 1px solid var(--border-color);">
            <form method="GET" class="d-flex gap-2 align-items-end">
                <div style="flex: 1;">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Cliente ou ID do pedido..." 
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
                
                <div>
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control form-select">
                        <option value="">Todos</option>
                        <option value="pendente" <?php echo $status === 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                        <option value="processando" <?php echo $status === 'processando' ? 'selected' : ''; ?>>Processando</option>
                        <option value="concluido" <?php echo $status === 'concluido' ? 'selected' : ''; ?>>Concluído</option>
                        <option value="cancelado" <?php echo $status === 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                    </select>
                </div>
                
                <div>
                    <label class="form-label">Data Início</label>
                    <input type="date" name="data_inicio" class="form-control" 
                           value="<?php echo htmlspecialchars($data_inicio); ?>">
                </div>
                
                <div>
                    <label class="form-label">Data Fim</label>
                    <input type="date" name="data_fim" class="form-control" 
                           value="<?php echo htmlspecialchars($data_fim); ?>">
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filtrar
                </button>
                
                <?php if ($search || $status || $data_inicio || $data_fim): ?>
                    <a href="/pedidos.php" class="btn btn-outline">
                        <i class="fas fa-times"></i> Limpar
                    </a>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Tabela -->
        <?php if (!empty($pedidos)): ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th data-sort>ID</th>
                            <th data-sort>Data</th>
                            <th data-sort>Cliente</th>
                            <th data-sort>Destino</th>
                            <th data-sort>Total</th>
                            <th data-sort>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pedidos as $pedido): ?>
                            <tr>
                                <td>
                                    <strong>#<?php echo $pedido['pedido_id']; ?></strong>
                                </td>
                                <td><?php echo formatDate($pedido['data_pedido']); ?></td>
                                <td><?php echo htmlspecialchars($pedido['nome']); ?></td>
                                <td><?php echo htmlspecialchars($pedido['destino'] ?? '-'); ?></td>
                                <td><?php echo formatCurrency($pedido['total']); ?></td>
                                <td><?php echo getStatusBadge($pedido['status']); ?></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-sm btn-outline" 
                                                onclick="viewOrder(<?php echo $pedido['pedido_id']; ?>)"
                                                title="Ver detalhes">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline" 
                                                onclick="editOrder(<?php echo $pedido['pedido_id']; ?>)"
                                                title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline" 
                                                onclick="updateStatus(<?php echo $pedido['pedido_id']; ?>, '<?php echo $pedido['status']; ?>')"
                                                title="Alterar status">
                                            <i class="fas fa-sync"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline" 
                                                onclick="printOrder(<?php echo $pedido['pedido_id']; ?>)"
                                                title="Imprimir">
                                            <i class="fas fa-print"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div style="padding: 2rem; text-align: center;">
                <i class="fas fa-shopping-cart" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
                <h3>Nenhum pedido encontrado</h3>
                <p class="text-muted">
                    <?php echo ($search || $status || $data_inicio || $data_fim) ? 'Tente ajustar os filtros.' : 'Comece criando seu primeiro pedido.'; ?>
                </p>
                <?php if (!$search && !$status && !$data_inicio && !$data_fim): ?>
                    <button class="btn btn-primary" onclick="openModal('addOrderModal')">
                        <i class="fas fa-plus"></i> Criar Pedido
                    </button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<!-- Modal Alterar Status -->
<div class="modal" id="statusModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Alterar Status do Pedido</h3>
            <button class="modal-close" onclick="closeModal('statusModal')">&times;</button>
        </div>
        
        <form id="statusForm">
            <input type="hidden" id="pedido_id" name="pedido_id">
            
            <div class="form-group">
                <label class="form-label">Novo Status</label>
                <select name="status" id="novo_status" class="form-control form-select" required>
                    <option value="pendente">Pendente</option>
                    <option value="processando">Processando</option>
                    <option value="concluido">Concluído</option>
                    <option value="cancelado">Cancelado</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Observações</label>
                <textarea name="observacoes" class="form-control" rows="3" 
                          placeholder="Adicione observações sobre a alteração..."></textarea>
            </div>
            
            <div class="d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-outline" onclick="closeModal('statusModal')">
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
function viewOrder(id) {
    window.location.href = `pedido_detalhes.php?id=${id}`;
}

function editOrder(id) {
    window.location.href = `pedido_editar.php?id=${id}`;
}

function updateStatus(id, currentStatus) {
    document.getElementById('pedido_id').value = id;
    document.getElementById('novo_status').value = currentStatus;
    openModal('statusModal');
}

function printOrder(id) {
    window.open(`pedido_imprimir.php?id=${id}`, '_blank');
}

function exportPedidos() {
    const params = new URLSearchParams(window.location.search);
    window.open(`exports/pedidos.php?${params.toString()}`, '_blank');
}

// Submissão do formulário de status
document.getElementById('statusForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    showLoading();
    
    fetch('actions/pedido_update_status.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showAlert('Status atualizado com sucesso!', 'success');
            closeModal('statusModal');
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert(data.message || 'Erro ao atualizar status.', 'danger');
        }
    })
    .catch(error => {
        hideLoading();
        showAlert('Erro ao atualizar status.', 'danger');
    });
});
</script>

<?php include 'includes/footer.php'; ?>