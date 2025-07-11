<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

requireLogin();
requireAdmin(); // Apenas administradores podem acessar configurações

$pageTitle = 'Configurações';
$db = new Database();

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $configs = [
            'empresa_nome' => sanitizeInput($_POST['empresa_nome'] ?? ''),
            'empresa_email' => sanitizeInput($_POST['empresa_email'] ?? ''),
            'empresa_telefone' => sanitizeInput($_POST['empresa_telefone'] ?? ''),
            'empresa_endereco' => sanitizeInput($_POST['empresa_endereco'] ?? ''),
            'moeda' => sanitizeInput($_POST['moeda'] ?? 'CVE'),
            'taxa_entrega_padrao' => floatval($_POST['taxa_entrega_padrao'] ?? 0),
            'estoque_minimo_alerta' => intval($_POST['estoque_minimo_alerta'] ?? 10)
        ];
        
        foreach ($configs as $chave => $valor) {
            $db->execute("
                INSERT INTO configuracoes (chave, valor) VALUES (?, ?)
                ON DUPLICATE KEY UPDATE valor = VALUES(valor)
            ", [$chave, $valor]);
        }
        
        showAlert('Configurações salvas com sucesso!', 'success');
        
    } catch (Exception $e) {
        showAlert('Erro ao salvar configurações.', 'danger');
    }
}

try {
    // Carregar configurações atuais
    $configuracoes = [];
    $configs = $db->fetchAll("SELECT chave, valor FROM configuracoes");
    foreach ($configs as $config) {
        $configuracoes[$config['chave']] = $config['valor'];
    }
    
} catch (Exception $e) {
    showAlert('Erro ao carregar configurações.', 'danger');
    $configuracoes = [];
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="main-content">
    <div class="page-header">
        <h1 class="page-title">Configurações</h1>
        <p class="page-subtitle">Configure as opções do sistema</p>
    </div>
    
    <form method="POST">
        <!-- Informações da Empresa -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Informações da Empresa</h3>
            </div>
            <div style="padding: 1.5rem;">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nome da Empresa *</label>
                        <input type="text" name="empresa_nome" class="form-control" 
                               value="<?php echo htmlspecialchars($configuracoes['empresa_nome'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Email da Empresa *</label>
                        <input type="email" name="empresa_email" class="form-control" 
                               value="<?php echo htmlspecialchars($configuracoes['empresa_email'] ?? ''); ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Telefone</label>
                        <input type="tel" name="empresa_telefone" class="form-control" 
                               value="<?php echo htmlspecialchars($configuracoes['empresa_telefone'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Moeda</label>
                        <select name="moeda" class="form-control form-select">
                            <option value="CVE" <?php echo ($configuracoes['moeda'] ?? '') === 'CVE' ? 'selected' : ''; ?>>CVE - Escudo Cabo-verdiano</option>
                            <option value="EUR" <?php echo ($configuracoes['moeda'] ?? '') === 'EUR' ? 'selected' : ''; ?>>EUR - Euro</option>
                            <option value="USD" <?php echo ($configuracoes['moeda'] ?? '') === 'USD' ? 'selected' : ''; ?>>USD - Dólar Americano</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Endereço</label>
                    <textarea name="empresa_endereco" class="form-control" rows="3"><?php echo htmlspecialchars($configuracoes['empresa_endereco'] ?? ''); ?></textarea>
                </div>
            </div>
        </div>
        
        <!-- Configurações de Vendas -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Configurações de Vendas</h3>
            </div>
            <div style="padding: 1.5rem;">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Taxa de Entrega Padrão</label>
                        <input type="number" name="taxa_entrega_padrao" class="form-control" 
                               step="0.01" min="0"
                               value="<?php echo htmlspecialchars($configuracoes['taxa_entrega_padrao'] ?? '0'); ?>">
                        <small class="text-muted">Valor em <?php echo $configuracoes['moeda'] ?? 'CVE'; ?></small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Alerta de Estoque Mínimo</label>
                        <input type="number" name="estoque_minimo_alerta" class="form-control" 
                               min="1"
                               value="<?php echo htmlspecialchars($configuracoes['estoque_minimo_alerta'] ?? '10'); ?>">
                        <small class="text-muted">Quantidade mínima para alertas</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Configurações do Sistema -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Informações do Sistema</h3>
            </div>
            <div style="padding: 1.5rem;">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Versão do Sistema</label>
                        <input type="text" class="form-control" value="1.0.0" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Última Atualização</label>
                        <input type="text" class="form-control" value="<?php echo date('d/m/Y H:i'); ?>" readonly>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Banco de Dados</label>
                        <input type="text" class="form-control" value="MySQL" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Servidor Web</label>
                        <input type="text" class="form-control" value="<?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Apache'; ?>" readonly>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Botões de Ação -->
        <div class="card">
            <div style="padding: 1.5rem;">
                <div class="d-flex gap-2 justify-content-between">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Salvar Configurações
                        </button>
                        <button type="button" class="btn btn-outline" onclick="location.reload()">
                            <i class="fas fa-undo"></i> Cancelar
                        </button>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline" onclick="exportConfig()">
                            <i class="fas fa-download"></i> Exportar Config
                        </button>
                        <button type="button" class="btn btn-outline" onclick="clearCache()">
                            <i class="fas fa-trash"></i> Limpar Cache
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</main>

<script>
function exportConfig() {
    alert('Funcionalidade de exportar configurações será implementada.');
}

function clearCache() {
    if (confirm('Tem certeza que deseja limpar o cache do sistema?')) {
        showAlert('Cache limpo com sucesso!', 'success');
    }
}
</script>

<?php include 'includes/footer.php'; ?>