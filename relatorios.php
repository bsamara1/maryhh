<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

requireLogin();

$pageTitle = 'Relatórios';
$db = new Database();

// Período padrão (último mês)
$data_inicio = $_GET['data_inicio'] ?? date('Y-m-01');
$data_fim = $_GET['data_fim'] ?? date('Y-m-t');

try {
    // Relatório de vendas
    $vendasPeriodo = $db->fetch("
        SELECT 
            COUNT(*) as total_pedidos,
            SUM(total) as total_vendas,
            AVG(total) as ticket_medio
        FROM pedidos 
        WHERE DATE(data_pedido) BETWEEN ? AND ? 
        AND status IN ('concluido', 'entregue')
    ", [$data_inicio, $data_fim]);
    
    // Top produtos
    $topProdutos = $db->fetchAll("
        SELECT 
            p.nome,
            SUM(ip.quantidade) as quantidade_vendida,
            SUM(ip.subtotal) as total_vendas
        FROM itens_pedido ip
        JOIN produtos p ON ip.produto_id = p.produto_id
        JOIN pedidos pd ON ip.pedido_id = pd.pedido_id
        WHERE DATE(pd.data_pedido) BETWEEN ? AND ?
        AND pd.status IN ('concluido', 'entregue')
        GROUP BY p.produto_id, p.nome
        ORDER BY quantidade_vendida DESC
        LIMIT 10
    ", [$data_inicio, $data_fim]);
    
    // Vendas por mês
    $vendasMensais = $db->fetchAll("
        SELECT 
            DATE_FORMAT(data_pedido, '%Y-%m') as mes,
            COUNT(*) as pedidos,
            SUM(total) as vendas
        FROM pedidos 
        WHERE status IN ('concluido', 'entregue')
        AND data_pedido >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(data_pedido, '%Y-%m')
        ORDER BY mes
    ");
    
    // Clientes mais ativos
    $topClientes = $db->fetchAll("
        SELECT 
            c.nome,
            COUNT(p.pedido_id) as total_pedidos,
            SUM(p.total) as total_gasto
        FROM clientes c
        JOIN pedidos p ON c.idCliente = p.idCliente
        WHERE DATE(p.data_pedido) BETWEEN ? AND ?
        AND p.status IN ('concluido', 'entregue')
        GROUP BY c.idCliente, c.nome
        ORDER BY total_gasto DESC
        LIMIT 10
    ", [$data_inicio, $data_fim]);
    
} catch (Exception $e) {
    showAlert('Erro ao carregar relatórios.', 'danger');
    $vendasPeriodo = ['total_pedidos' => 0, 'total_vendas' => 0, 'ticket_medio' => 0];
    $topProdutos = $vendasMensais = $topClientes = [];
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="main-content">
    <div class="page-header">
        <h1 class="page-title">Relatórios</h1>
        <p class="page-subtitle">Análise de desempenho do negócio</p>
    </div>
    
    <!-- Filtro de Período -->
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">Período de Análise</h3>
        </div>
        <div style="padding: 1rem;">
            <form method="GET" class="d-flex gap-2 align-items-end">
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
                    <i class="fas fa-search"></i> Atualizar
                </button>
                
                <button type="button" class="btn btn-outline" onclick="exportRelatorio()">
                    <i class="fas fa-download"></i> Exportar PDF
                </button>
            </form>
        </div>
    </div>
    
    <!-- Resumo de Vendas -->
    <div class="stats-grid mb-4">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stat-number"><?php echo number_format($vendasPeriodo['total_pedidos'] ?? 0); ?></div>
            <div class="stat-label">Pedidos no Período</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-number"><?php echo formatCurrency($vendasPeriodo['total_vendas'] ?? 0); ?></div>
            <div class="stat-label">Total de Vendas</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-number"><?php echo formatCurrency($vendasPeriodo['ticket_medio'] ?? 0); ?></div>
            <div class="stat-label">Ticket Médio</div>
        </div>
    </div>
    
    <!-- Gráficos -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; margin-bottom: 2rem;">
        <!-- Gráfico de Vendas Mensais -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Vendas dos Últimos 12 Meses</h3>
            </div>
            <canvas id="salesChart" height="100"></canvas>
        </div>
        
        <!-- Top Produtos -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Produtos Mais Vendidos</h3>
            </div>
            <?php if (!empty($topProdutos)): ?>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Qtd</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($topProdutos, 0, 5) as $produto): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($produto['nome']); ?></td>
                                    <td><?php echo $produto['quantidade_vendida']; ?></td>
                                    <td><?php echo formatCurrency($produto['total_vendas']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted" style="padding: 1rem;">Nenhum produto vendido no período.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Tabelas Detalhadas -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
        <!-- Top Produtos Completo -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Ranking de Produtos</h3>
            </div>
            <?php if (!empty($topProdutos)): ?>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Produto</th>
                                <th>Quantidade</th>
                                <th>Total Vendas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($topProdutos as $index => $produto): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($produto['nome']); ?></td>
                                    <td><?php echo $produto['quantidade_vendida']; ?></td>
                                    <td><?php echo formatCurrency($produto['total_vendas']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted" style="padding: 1rem;">Nenhum produto vendido no período.</p>
            <?php endif; ?>
        </div>
        
        <!-- Top Clientes -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Melhores Clientes</h3>
            </div>
            <?php if (!empty($topClientes)): ?>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Cliente</th>
                                <th>Pedidos</th>
                                <th>Total Gasto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($topClientes as $index => $cliente): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($cliente['nome']); ?></td>
                                    <td><?php echo $cliente['total_pedidos']; ?></td>
                                    <td><?php echo formatCurrency($cliente['total_gasto']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted" style="padding: 1rem;">Nenhum cliente no período.</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<script>
// Dados para o gráfico de vendas mensais
const vendasData = <?php echo json_encode($vendasMensais); ?>;

// Preparar dados para o gráfico
const chartLabels = [];
const chartData = [];

vendasData.forEach(item => {
    const [year, month] = item.mes.split('-');
    const monthNames = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 
                       'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
    chartLabels.push(monthNames[parseInt(month) - 1] + '/' + year.substr(2));
    chartData.push(parseFloat(item.vendas));
});

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

function exportRelatorio() {
    const params = new URLSearchParams(window.location.search);
    window.open(`exports/relatorio.php?${params.toString()}`, '_blank');
}
</script>

<?php include 'includes/footer.php'; ?>