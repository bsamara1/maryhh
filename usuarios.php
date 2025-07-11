<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

requireLogin();
requireAdmin(); // Apenas administradores podem gerenciar usuários

$pageTitle = 'Usuários';
$db = new Database();

try {
    $usuarios = $db->fetchAll("
        SELECT u.utilizador_id, u.nome, u.email, u.ativo, u.ultimo_login, u.data_criacao,
               t.nome as tipo_nome
        FROM utilizador u
        JOIN tipo_utilizador t ON u.idTipoUtilizador = t.idTipoUtilizador
        ORDER BY u.nome ASC
    ");
    
    $tiposUsuario = $db->fetchAll("SELECT * FROM tipo_utilizador ORDER BY nome");
    
} catch (Exception $e) {
    showAlert('Erro ao carregar usuários.', 'danger');
    $usuarios = $tiposUsuario = [];
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="main-content">
    <div class="page-header">
        <h1 class="page-title">Usuários</h1>
        <p class="page-subtitle">Gerencie os usuários do sistema</p>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Usuários</h3>
            <button class="btn btn-primary" onclick="openModal('addUserModal')">
                <i class="fas fa-plus"></i> Novo Usuário
            </button>
        </div>
        
        <?php if (!empty($usuarios)): ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Tipo</th>
                            <th>Status</th>
                            <th>Último Login</th>
                            <th>Data Criação</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($usuario['nome']); ?></strong></td>
                                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['tipo_nome']); ?></td>
                                <td>
                                    <span class="badge <?php echo $usuario['ativo'] ? 'badge-success' : 'badge-danger'; ?>">
                                        <?php echo $usuario['ativo'] ? 'Ativo' : 'Inativo'; ?>
                                    </span>
                                </td>
                                <td><?php echo $usuario['ultimo_login'] ? formatDateTime($usuario['ultimo_login']) : 'Nunca'; ?></td>
                                <td><?php echo formatDate($usuario['data_criacao']); ?></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-sm btn-outline" 
                                                onclick="editUser(<?php echo $usuario['utilizador_id']; ?>)"
                                                title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php if ($usuario['utilizador_id'] != $_SESSION['user_id']): ?>
                                            <button class="btn btn-sm btn-outline" 
                                                    onclick="toggleUserStatus(<?php echo $usuario['utilizador_id']; ?>, <?php echo $usuario['ativo'] ? 'false' : 'true'; ?>)"
                                                    title="<?php echo $usuario['ativo'] ? 'Desativar' : 'Ativar'; ?>">
                                                <i class="fas fa-<?php echo $usuario['ativo'] ? 'ban' : 'check'; ?>"></i>
                                            </button>
                                        <?php endif; ?>
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
                <h3>Nenhum usuário encontrado</h3>
                <p class="text-muted">Comece adicionando usuários ao sistema.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<!-- Modal Adicionar Usuário -->
<div class="modal" id="addUserModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Novo Usuário</h3>
            <button class="modal-close" onclick="closeModal('addUserModal')">&times;</button>
        </div>
        
        <form id="addUserForm">
            <div class="form-group">
                <label class="form-label">Nome *</label>
                <input type="text" name="nome" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Email *</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Senha *</label>
                <input type="password" name="senha" class="form-control" required minlength="6">
            </div>
            
            <div class="form-group">
                <label class="form-label">Tipo de Usuário *</label>
                <select name="idTipoUtilizador" class="form-control form-select" required>
                    <?php foreach ($tiposUsuario as $tipo): ?>
                        <option value="<?php echo $tipo['idTipoUtilizador']; ?>">
                            <?php echo htmlspecialchars($tipo['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-outline" onclick="closeModal('addUserModal')">
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
function editUser(id) {
    alert('Editar usuário ID: ' + id);
}

function toggleUserStatus(id, newStatus) {
    const action = newStatus === 'true' ? 'ativar' : 'desativar';
    if (confirm(`Tem certeza que deseja ${action} este usuário?`)) {
        // Implementar chamada AJAX para alterar status
        alert(`Usuário ${action}do com sucesso!`);
    }
}

// Submissão do formulário de adicionar usuário
document.getElementById('addUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    showLoading();
    
    // Simular envio (implementar endpoint real)
    setTimeout(() => {
        hideLoading();
        showAlert('Usuário adicionado com sucesso!', 'success');
        closeModal('addUserModal');
        setTimeout(() => location.reload(), 1000);
    }, 1000);
});
</script>

<?php include 'includes/footer.php'; ?>