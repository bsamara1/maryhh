<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

requireLogin();

$pageTitle = 'Produtos';
$db = new Database();

// Filtros
$search = sanitizeInput($_GET['search'] ?? '');
$categoria = sanitizeInput($_GET['categoria'] ?? '');
$baixo_estoque = isset($_GET['baixo_estoque']);

$whereClause = 'WHERE p.ativo = 1';
$params = [];

if ($search) {
    $whereClause .= " AND p.nome LIKE ?";
    $params[] = "%$search%";
}

if ($categoria) {
    $whereClause .= " AND p.categoria_id = ?";
    $params[] = $categoria;
}

if ($baixo_estoque) {
    $whereClause .= " AND p.estoque <= p.estoque_minimo";
}

try {
    $produtos = $db->fetchAll("
        SELECT p.produto_id, p.nome, p.preco, p.estoque, p.estoque_minimo, 
               c.nome as categoria_nome, p.data_criacao
        FROM produtos p
        LEFT JOIN categorias c ON p.categoria_id = c.categoria_id
        $whereClause
        ORDER BY p.nome ASC
    ", $params);
    
    // Estatísticas
    $totalProdutos = $db->fetch("SELECT COUNT(*) as total FROM produtos WHERE ativo = 1")['total'];
    $produtosBaixoEstoque = $db->fetch("SELECT COUNT(*) as total FROM produtos WHERE ativo = 1 AND estoque <= estoque_minimo")['total'];
    $valorTotalEstoque = $db->fetch("SELECT SUM(preco * estoque) as total FROM produtos WHERE ativo = 1")['total'] ?? 0;
    
    // Categorias para filtro
    $categorias = $db->fetchAll("SELECT categoria_id, nome FROM categorias WHERE ativo = 1 ORDER BY nome");
    
} catch (Exception $e) {
    showAlert('Erro ao carregar produtos.', 'danger');
    $produtos = [];
    $totalProdutos = $produtosBaixoEstoque = $valorTotalEstoque = 0;
    $categorias = [];
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="main-content">
    <div class="page-header">
        <h1 class="page-title">Produtos</h1>
        <p class="page-subtitle">Gerencie seu catálogo de produtos</p>
    </div>
    
    <!-- Estatísticas -->
    <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 2rem;">
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
            <div class="stat-number"><?php echo number_format($produtosBaixoEstoque); ?></div>
            <div class="stat-label">Baixo Estoque</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-number"><?php echo formatCurrency($valorTotalEstoque); ?></div>
            <div class="stat-label">Valor do Estoque</div>
        </div>
    </div>
    
    <!-- Filtros e Lista -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Catálogo de Produtos</h3>
            <div class="d-flex gap-2">
                <button class="btn btn-primary" onclick="openModal('addProductModal')">
                    <i class="fas fa-plus"></i> Novo Produto
                </button>
                <button class="btn btn-outline" onclick="exportProdutos()">
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
                           placeholder="Nome do produto..." 
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
                
                <div>
                    <label class="form-label">Categoria</label>
                    <select name="categoria" class="form-control form-select">
                        <option value="">Todas</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo $cat['categoria_id']; ?>" 
                                    <?php echo $categoria == $cat['categoria_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex align-items-center">
                        <input type="checkbox" name="baixo_estoque" id="baixo_estoque" 
                               <?php echo $baixo_estoque ? 'checked' : ''; ?>>
                        <label for="baixo_estoque" style="margin-left: 0.5rem;">Baixo estoque</label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filtrar
                </button>
                
                <?php if ($search || $categoria || $baixo_estoque): ?>
                    <a href="produtos.php" class="btn btn-outline">
                        <i class="fas fa-times"></i> Limpar
                    </a>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Tabela -->
        <?php if (!empty($produtos)): ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th data-sort>Nome</th>
                            <th data-sort>Categoria</th>
                            <th data-sort>Preço</th>
                            <th data-sort>Estoque</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produtos as $produto): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($produto['nome']); ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($produto['categoria_nome'] ?? 'Sem categoria'); ?></td>
                                <td><?php echo formatCurrency($produto['preco']); ?></td>
                                <td>
                                    <span class="badge <?php echo $produto['estoque'] <= $produto['estoque_minimo'] ? 'badge-warning' : 'badge-success'; ?>">
                                        <?php echo $produto['estoque']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($produto['estoque'] <= $produto['estoque_minimo']): ?>
                                        <span class="badge badge-warning">Baixo Estoque</span>
                                    <?php else: ?>
                                        <span class="badge badge-success">Disponível</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-sm btn-outline" 
                                                onclick="viewProduct(<?php echo $produto['produto_id']; ?>)"
                                                title="Ver detalhes">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline" 
                                                onclick="editProduct(<?php echo $produto['produto_id']; ?>)"
                                                title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline" 
                                                onclick="adjustStock(<?php echo $produto['produto_id']; ?>, <?php echo $produto['estoque']; ?>)"
                                                title="Ajustar estoque">
                                            <i class="fas fa-warehouse"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline" 
                                                onclick="deleteProduct(<?php echo $produto['produto_id']; ?>)"
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
                <i class="fas fa-box" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
                <h3>Nenhum produto encontrado</h3>
                <p class="text-muted">
                    <?php echo ($search || $categoria || $baixo_estoque) ? 'Tente ajustar os filtros.' : 'Comece adicionando seu primeiro produto.'; ?>
                </p>
                <?php if (!$search && !$categoria && !$baixo_estoque): ?>
                    <button class="btn btn-primary" onclick="openModal('addProductModal')">
                        <i class="fas fa-plus"></i> Adicionar Produto
                    </button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<!-- Modal Adicionar Produto -->
<div class="modal" id="addProductModal">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h3 class="modal-title">Novo Produto</h3>
            <button class="modal-close" onclick="closeModal('addProductModal')">&times;</button>
        </div>
        
        <form id="addProductForm">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nome *</label>
                    <input type="text" name="nome" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Categoria</label>
                    <select name="categoria_id" class="form-control form-select">
                        <option value="">Selecione...</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo $cat['categoria_id']; ?>">
                                <?php echo htmlspecialchars($cat['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Preço *</label>
                    <input type="number" name="preco" class="form-control" step="0.01" min="0" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Estoque Inicial</label>
                    <input type="number" name="estoque" class="form-control" min="0" value="0">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Tipo</label>
                    <input type="text" name="tipo" class="form-control" placeholder="Ex: Peruca, Extensão...">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Comprimento</label>
                    <input type="number" name="comprimento" class="form-control" step="0.1" min="0" placeholder="Em polegadas">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Textura</label>
                    <input type="text" name="textura" class="form-control" placeholder="Ex: Lisa, Cacheada...">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Cor</label>
                    <input type="text" name="cor" class="form-control" placeholder="Ex: Preto Natural...">
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Descrição</label>
                <textarea name="descricao" class="form-control" rows="3"></textarea>
            </div>
            
            <div class="d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-outline" onclick="closeModal('addProductModal')">
                    Cancelar
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Salvar
                </button>
            </div>
        </form>
    </div>
</div>

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
function viewProduct(id) {
    alert('Visualizar produto ID: ' + id);
}

function editProduct(id) {
    alert('Editar produto ID: ' + id);
}

function adjustStock(id, currentStock) {
    document.getElementById('produto_id').value = id;
    document.getElementById('estoque_atual').value = currentStock;
    document.getElementById('novo_estoque').value = currentStock;
    openModal('stockModal');
}

function deleteProduct(id) {
    if (confirm('Tem certeza que deseja excluir este produto?')) {
        fetch(`actions/produto_delete.php?id=${id}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Produto excluído com sucesso!', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showAlert(data.message || 'Erro ao excluir produto.', 'danger');
            }
        })
        .catch(error => {
            showAlert('Erro ao excluir produto.', 'danger');
        });
    }
}

function exportProdutos() {
    const params = new URLSearchParams(window.location.search);
    window.open(`exports/produtos.php?${params.toString()}`, '_blank');
}

// Submissão do formulário de adicionar produto
document.getElementById('addProductForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    showLoading();
    
    fetch('actions/produto_add.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showAlert('Produto adicionado com sucesso!', 'success');
            closeModal('addProductModal');
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert(data.message || 'Erro ao adicionar produto.', 'danger');
        }
    })
    .catch(error => {
        hideLoading();
        showAlert('Erro ao adicionar produto.', 'danger');
    });
});

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