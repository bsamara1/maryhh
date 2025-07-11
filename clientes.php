<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

requireLogin();

$pageTitle = 'Clientes';
$db = new Database();

// Filtros
$search = sanitizeInput($_GET['search'] ?? '');
$cidade = sanitizeInput($_GET['cidade'] ?? '');

$whereClause = 'WHERE ativo = 1';
$params = [];

if ($search) {
    $whereClause .= " AND (nome LIKE ? OR email LIKE ? OR telefone LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($cidade) {
    $whereClause .= " AND cidade = ?";
    $params[] = $cidade;
}

try {
    $clientes = $db->fetchAll("
        SELECT idCliente, nome, email, telefone, cidade, data_registo
        FROM clientes
        $whereClause
        ORDER BY nome ASC
    ", $params);
    
    // Estatísticas
    $totalClientes = $db->fetch("SELECT COUNT(*) as total FROM clientes WHERE ativo = 1")['total'];
    $clientesRecentes = $db->fetch("SELECT COUNT(*) as total FROM clientes WHERE ativo = 1 AND data_registo >= DATE_SUB(NOW(), INTERVAL 30 DAY)")['total'];
    $cidades = $db->fetchAll("SELECT DISTINCT cidade FROM clientes WHERE cidade IS NOT NULL AND cidade != '' ORDER BY cidade");
    
} catch (Exception $e) {
    showAlert('Erro ao carregar clientes.', 'danger');
    $clientes = [];
    $totalClientes = $clientesRecentes = 0;
    $cidades = [];
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="main-content">
    <div class="page-header">
        <h1 class="page-title">Clientes</h1>
        <p class="page-subtitle">Gerencie todos os seus clientes</p>
    </div>
    
    <!-- Estatísticas -->
    <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 2rem;">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-number"><?php echo number_format($totalClientes); ?></div>
            <div class="stat-label">Total de Clientes</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <div class="stat-number"><?php echo number_format($clientesRecentes); ?></div>
            <div class="stat-label">Novos (30 dias)</div>
        </div>
    </div>
    
    <!-- Filtros e Lista -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Clientes</h3>
            <div class="d-flex gap-2">
                <button class="btn btn-primary" onclick="openModal('addClientModal')">
                    <i class="fas fa-plus"></i> Novo Cliente
                </button>
                <button class="btn btn-outline" onclick="exportClientes()">
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
                           placeholder="Nome, email ou telefone..." 
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
                
                <div>
                    <label class="form-label">Cidade</label>
                    <select name="cidade" class="form-control form-select">
                        <option value="">Todas</option>
                        <?php foreach ($cidades as $c): ?>
                            <option value="<?php echo htmlspecialchars($c['cidade']); ?>" 
                                    <?php echo $cidade === $c['cidade'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($c['cidade']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filtrar
                </button>
                
                <?php if ($search || $cidade): ?>
                    <a href="clientes.php" class="btn btn-outline">
                        <i class="fas fa-times"></i> Limpar
                    </a>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Tabela -->
        <?php if (!empty($clientes)): ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th data-sort>Nome</th>
                            <th data-sort>Email</th>
                            <th data-sort>Telefone</th>
                            <th data-sort>Cidade</th>
                            <th data-sort>Data Registro</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clientes as $cliente): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($cliente['nome']); ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($cliente['email'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($cliente['telefone'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($cliente['cidade'] ?? '-'); ?></td>
                                <td><?php echo formatDate($cliente['data_registo']); ?></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-sm btn-outline" 
                                                onclick="viewClient(<?php echo $cliente['idCliente']; ?>)"
                                                title="Ver detalhes">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline" 
                                                onclick="editClient(<?php echo $cliente['idCliente']; ?>)"
                                                title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline" 
                                                onclick="deleteClient(<?php echo $cliente['idCliente']; ?>)"
                                                title="Excluir">
                                            <i class="fas fa-trash"></i>
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
                <i class="fas fa-users" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
                <h3>Nenhum cliente encontrado</h3>
                <p class="text-muted">
                    <?php echo ($search || $cidade) ? 'Tente ajustar os filtros.' : 'Comece adicionando seu primeiro cliente.'; ?>
                </p>
                <?php if (!$search && !$cidade): ?>
                    <button class="btn btn-primary" onclick="openModal('addClientModal')">
                        <i class="fas fa-plus"></i> Adicionar Cliente
                    </button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<!-- Modal Adicionar Cliente -->
<div class="modal" id="addClientModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Novo Cliente</h3>
            <button class="modal-close" onclick="closeModal('addClientModal')">&times;</button>
        </div>
        
        <form id="addClientForm">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nome *</label>
                    <input type="text" name="nome" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Telefone *</label>
                    <input type="tel" name="telefone" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Data de Nascimento</label>
                    <input type="date" name="data_nascimento" class="form-control">
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Endereço</label>
                <textarea name="endereco" class="form-control" rows="3"></textarea>
            </div>
            
            <div class="d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-outline" onclick="closeModal('addClientModal')">
                    Cancelar
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Salvar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function viewClient(id) {
    alert('Visualizar cliente ID: ' + id);
}

function editClient(id) {
    alert('Editar cliente ID: ' + id);
}

function deleteClient(id) {
    if (confirm('Tem certeza que deseja excluir este cliente?')) {
        fetch(`actions/cliente_delete.php?id=${id}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Cliente excluído com sucesso!', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showAlert(data.message || 'Erro ao excluir cliente.', 'danger');
            }
        })
        .catch(error => {
            showAlert('Erro ao excluir cliente.', 'danger');
        });
    }
}

function exportClientes() {
    const params = new URLSearchParams(window.location.search);
    window.open(`exports/clientes.php?${params.toString()}`, '_blank');
}

// Submissão do formulário de adicionar cliente
document.getElementById('addClientForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    showLoading();
    
    fetch('actions/cliente_add.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showAlert('Cliente adicionado com sucesso!', 'success');
            closeModal('addClientModal');
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert(data.message || 'Erro ao adicionar cliente.', 'danger');
        }
    })
    .catch(error => {
        hideLoading();
        showAlert('Erro ao adicionar cliente.', 'danger');
    });
});
</script>

<?php include 'includes/footer.php'; ?>