<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

requireLogin();

$pageTitle = 'Dashboard';
$db = new Database();

// Estatísticas gerais
try {
    $totalPedidos = $db->fetch("SELECT COUNT(*) as total FROM pedidos")['total'];
    $totalClientes = $db->fetch("SELECT COUNT(*) as total FROM clientes")['total'];
    $totalProdutos = $db->fetch("SELECT COUNT(*) as total FROM produtos")['total'];
    $totalVendas = $db->fetch("SELECT COALESCE(SUM(total), 0) as total FROM pedidos WHERE status = 'concluido'")['total'] ?? 0;
    
    // Pedidos recentes
    $pedidosRecentes = $db->fetchAll("
        SELECT p.pedido_id, c.nome, p.total, p.status, p.data_pedido 
        FROM pedidos p 
        JOIN clientes c ON p.idCliente = c.idCliente 
        ORDER BY p.data_pedido DESC 
        LIMIT 5
    ");
    
    // Produtos com baixo estoque
    $produtosBaixoEstoque = $db->fetchAll("
        SELECT nome, estoque 
        FROM produtos 
        WHERE estoque < 10 
        ORDER BY estoque ASC 
        LIMIT 5
    ");
    
    // Dados para gráficos
    $vendasMensais = $db->fetchAll("
        SELECT 
            MONTH(data_pedido) as mes,
            COUNT(*) as pedidos,
            SUM(total) as vendas
        FROM pedidos 
        WHERE YEAR(data_pedido) = YEAR(CURDATE())
        GROUP BY MONTH(data_pedido)
        ORDER BY mes
    ");
    
} catch (Exception $e) {
    showAlert('Erro ao carregar dados do dashboard.', 'danger');
    $totalPedidos = $totalClientes = $totalProdutos = $totalVendas = 0;
    $pedidosRecentes = $produtosBaixoEstoque = $vendasMensais = [];
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="main-content">
    <div class="page-header">
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">Visão geral do seu negócio</p>
    </div>
    
    <!-- Estatísticas -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stat-number"><?php echo number_format($totalPedidos); ?></div>
            <div class="stat-label">Total de Pedidos</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-number"><?php echo number_format($totalClientes); ?></div>
            <div class="stat-label">Clientes Ativos</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-box"></i>
            </div>
            <div class="stat-number"><?php echo number_format($totalProdutos); ?></div>
            <div class="stat-label">Produtos</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-number"><?php echo formatCurrency($totalVendas); ?></div>
            <div class="stat-label">Total de Vendas</div>
        </div>
    </div>
    
    <!-- Gráficos e Tabelas -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; margin-bottom: 2rem;">
        <!-- Gráfico de Vendas -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Vendas Mensais</h3>
            </div>
            <canvas id="salesChart" height="100"></canvas>
        </div>
        
        <!-- Produtos com Baixo Estoque -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Baixo Estoque</h3>
                <a href="estoque.php" class="btn btn-sm btn-outline">Ver Todos</a>
            </div>
            <?php if (!empty($produtosBaixoEstoque)): ?>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Estoque</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($produtosBaixoEstoque as $produto): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($produto['nome']); ?></td>
                                    <td>
                                        <span class="badge badge-warning">
                                            <?php echo $produto['estoque']; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted">Todos os produtos têm estoque adequado.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Pedidos Recentes -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Pedidos Recentes</h3>
            <a href="pedidos.php" class="btn btn-sm btn-outline">Ver Todos</a>
        </div>
        
        <?php if (!empty($pedidosRecentes)): ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pedidosRecentes as $pedido): ?>
                            <tr>
                                <td>#<?php echo $pedido['pedido_id']; ?></td>
                                <td><?php echo htmlspecialchars($pedido['nome']); ?></td>
                                <td><?php echo formatCurrency($pedido['total']); ?></td>
                                <td><?php echo getStatusBadge($pedido['status']); ?></td>
                                <td><?php echo formatDate($pedido['data_pedido']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted">Nenhum pedido encontrado.</p>
        <?php endif; ?>
    </div>
</main>

<script>
// Dados para o gráfico de vendas
const vendasData = <?php echo json_encode($vendasMensais); ?>;
const meses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];

// Preparar dados para o gráfico
const chartLabels = [];
const chartData = [];

for (let i = 1; i <= 12; i++) {
    chartLabels.push(meses[i - 1]);
    const mesData = vendasData.find(v => v.mes == i);
    chartData.push(mesData ? mesData.vendas : 0);
}

// Criar gráfico
const ctx = document.getElementById('salesChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: chartLabels,
        datasets: [{
            label: 'Vendas (CVE)',
            data: chartData,
            borderColor: '#892e82',
            backgroundColor: 'rgba(137, 46, 130, 0.1)',
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#892e82',
            pointBorderColor: '#fff',
            pointHoverRadius: 6,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString('pt-CV') + ' CVE';
                    }
                }
            }
        },
        interaction: {
            intersect: false,
            mode: 'index'
        }
    }
});
</script>

<?php include 'includes/footer.php'; ?>