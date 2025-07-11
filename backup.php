<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

requireLogin();
requireAdmin(); // Apenas administradores podem fazer backup

$pageTitle = 'Backup e Restauração';

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="main-content">
    <div class="page-header">
        <h1 class="page-title">Backup e Restauração</h1>
        <p class="page-subtitle">Gerencie backups do sistema</p>
    </div>
    
    <!-- Criar Backup -->
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">Criar Backup</h3>
        </div>
        <div style="padding: 1.5rem;">
            <p class="text-muted mb-3">
                Crie um backup completo do banco de dados. O arquivo será gerado em formato SQL.
            </p>
            
            <div class="d-flex gap-2">
                <button class="btn btn-primary" onclick="createBackup()">
                    <i class="fas fa-database"></i> Criar Backup Completo
                </button>
                <button class="btn btn-outline" onclick="createBackup('data')">
                    <i class="fas fa-table"></i> Backup Apenas Dados
                </button>
            </div>
        </div>
    </div>
    
    <!-- Restaurar Backup -->
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">Restaurar Backup</h3>
        </div>
        <div style="padding: 1.5rem;">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Atenção:</strong> A restauração irá substituir todos os dados atuais. 
                Certifique-se de fazer um backup antes de prosseguir.
            </div>
            
            <form id="restoreForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-label">Selecionar Arquivo de Backup</label>
                    <input type="file" name="backup_file" class="form-control" accept=".sql" required>
                    <small class="text-muted">Apenas arquivos .sql são aceitos</small>
                </div>
                
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-upload"></i> Restaurar Backup
                </button>
            </form>
        </div>
    </div>
    
    <!-- Backups Automáticos -->
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">Backup Automático</h3>
        </div>
        <div style="padding: 1.5rem;">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Frequência</label>
                    <select class="form-control form-select">
                        <option value="disabled">Desabilitado</option>
                        <option value="daily">Diário</option>
                        <option value="weekly">Semanal</option>
                        <option value="monthly">Mensal</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Horário</label>
                    <input type="time" class="form-control" value="02:00">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Manter Backups</label>
                    <select class="form-control form-select">
                        <option value="7">7 dias</option>
                        <option value="30">30 dias</option>
                        <option value="90">90 dias</option>
                        <option value="365">1 ano</option>
                    </select>
                </div>
            </div>
            
            <button class="btn btn-primary">
                <i class="fas fa-save"></i> Salvar Configurações
            </button>
        </div>
    </div>
    
    <!-- Histórico de Backups -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Histórico de Backups</h3>
        </div>
        
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Data/Hora</th>
                        <th>Tipo</th>
                        <th>Tamanho</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo date('d/m/Y H:i'); ?></td>
                        <td><span class="badge badge-info">Completo</span></td>
                        <td>2.5 MB</td>
                        <td><span class="badge badge-success">Sucesso</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm btn-outline" title="Download">
                                    <i class="fas fa-download"></i>
                                </button>
                                <button class="btn btn-sm btn-outline" title="Restaurar">
                                    <i class="fas fa-undo"></i>
                                </button>
                                <button class="btn btn-sm btn-outline" title="Excluir">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo date('d/m/Y H:i', strtotime('-1 day')); ?></td>
                        <td><span class="badge badge-secondary">Dados</span></td>
                        <td>1.8 MB</td>
                        <td><span class="badge badge-success">Sucesso</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm btn-outline" title="Download">
                                    <i class="fas fa-download"></i>
                                </button>
                                <button class="btn btn-sm btn-outline" title="Restaurar">
                                    <i class="fas fa-undo"></i>
                                </button>
                                <button class="btn btn-sm btn-outline" title="Excluir">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script>
function createBackup(type = 'full') {
    const backupType = type === 'data' ? 'apenas dados' : 'completo';
    
    if (confirm(`Criar backup ${backupType}? Este processo pode levar alguns minutos.`)) {
        showLoading();
        
        // Simular criação de backup
        setTimeout(() => {
            hideLoading();
            showAlert(`Backup ${backupType} criado com sucesso!`, 'success');
            
            // Simular download do arquivo
            const link = document.createElement('a');
            link.href = '#';
            link.download = `backup_${type}_${new Date().toISOString().slice(0, 10)}.sql`;
            link.click();
        }, 3000);
    }
}

document.getElementById('restoreForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const fileInput = this.querySelector('input[type="file"]');
    if (!fileInput.files.length) {
        showAlert('Selecione um arquivo de backup.', 'warning');
        return;
    }
    
    if (confirm('ATENÇÃO: Esta ação irá substituir todos os dados atuais. Tem certeza que deseja continuar?')) {
        showLoading();
        
        // Simular restauração
        setTimeout(() => {
            hideLoading();
            showAlert('Backup restaurado com sucesso!', 'success');
            setTimeout(() => location.reload(), 2000);
        }, 5000);
    }
});
</script>

<?php include 'includes/footer.php'; ?>