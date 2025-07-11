// Inicialização quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

function initializeApp() {
    // Inicializar componentes
    initializeAlerts();
    initializeModals();
    initializeSearch();
    initializeForms();
    initializeCharts();
    initializeTables();
    
    // Auto-fechar alertas após 5 segundos
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            if (alert.style.display !== 'none') {
                closeAlert(alert);
            }
        });
    }, 5000);
}

// Sistema de Alertas
function initializeAlerts() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        const closeBtn = alert.querySelector('.alert-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => closeAlert(alert));
        }
    });
}

function closeAlert(alert = null) {
    if (!alert) {
        alert = document.getElementById('alert');
    }
    if (alert) {
        alert.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            alert.remove();
        }, 300);
    }
}

function showAlert(message, type = 'info') {
    const alertHTML = `
        <div class="alert alert-${type}" id="alert">
            <span>${message}</span>
            <button onclick="closeAlert()" class="alert-close">&times;</button>
        </div>
    `;
    
    const container = document.querySelector('.main-content');
    if (container) {
        container.insertAdjacentHTML('afterbegin', alertHTML);
        
        // Auto-fechar após 5 segundos
        setTimeout(() => closeAlert(), 5000);
    }
}

// Sistema de Modais
function initializeModals() {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        const closeBtn = modal.querySelector('.modal-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => closeModal(modal.id));
        }
        
        // Fechar ao clicar fora do modal
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal(modal.id);
            }
        });
    });
}

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = 'auto';
    }
}

// Sistema de Busca
function initializeSearch() {
    const searchInput = document.querySelector('.search-box input');
    if (searchInput) {
        let searchTimeout;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performSearch(this.value);
            }, 300);
        });
    }
}

function performSearch(query) {
    if (query.length < 2) return;
    
    // Implementar busca específica baseada na página atual
    const currentPage = window.location.pathname;
    
    if (currentPage.includes('clientes')) {
        searchClientes(query);
    } else if (currentPage.includes('produtos')) {
        searchProdutos(query);
    } else if (currentPage.includes('pedidos')) {
        searchPedidos(query);
    }
}

function searchClientes(query) {
    const rows = document.querySelectorAll('.table tbody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(query.toLowerCase()) ? '' : 'none';
    });
}

function searchProdutos(query) {
    const rows = document.querySelectorAll('.table tbody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(query.toLowerCase()) ? '' : 'none';
    });
}

function searchPedidos(query) {
    const rows = document.querySelectorAll('.table tbody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(query.toLowerCase()) ? '' : 'none';
    });
}

// Validação de Formulários
function initializeForms() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
            }
        });
        
        // Validação em tempo real
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', () => validateField(input));
            input.addEventListener('input', () => clearFieldError(input));
        });
    });
}

function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    
    inputs.forEach(input => {
        if (!validateField(input)) {
            isValid = false;
        }
    });
    
    return isValid;
}

function validateField(field) {
    const value = field.value.trim();
    const type = field.type;
    let isValid = true;
    let message = '';
    
    // Verificar se é obrigatório
    if (field.hasAttribute('required') && !value) {
        isValid = false;
        message = 'Este campo é obrigatório';
    }
    
    // Validações específicas por tipo
    if (value && type === 'email') {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            isValid = false;
            message = 'Email inválido';
        }
    }
    
    if (value && type === 'tel') {
        const phoneRegex = /^[\d\s\-\+\(\)]+$/;
        if (!phoneRegex.test(value)) {
            isValid = false;
            message = 'Telefone inválido';
        }
    }
    
    if (value && type === 'number') {
        if (isNaN(value) || value < 0) {
            isValid = false;
            message = 'Número inválido';
        }
    }
    
    // Mostrar/esconder erro
    if (!isValid) {
        showFieldError(field, message);
    } else {
        clearFieldError(field);
    }
    
    return isValid;
}

function showFieldError(field, message) {
    clearFieldError(field);
    
    field.classList.add('is-invalid');
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.textContent = message;
    errorDiv.style.color = 'var(--danger-color)';
    errorDiv.style.fontSize = '0.875rem';
    errorDiv.style.marginTop = '0.25rem';
    
    field.parentNode.appendChild(errorDiv);
}

function clearFieldError(field) {
    field.classList.remove('is-invalid');
    const error = field.parentNode.querySelector('.field-error');
    if (error) {
        error.remove();
    }
}

// Gráficos
function initializeCharts() {
    // Verificar se Chart.js está disponível
    if (typeof Chart !== 'undefined') {
        initializeDashboardCharts();
    }
}

function initializeDashboardCharts() {
    // Gráfico de vendas mensais
    const salesChart = document.getElementById('salesChart');
    if (salesChart) {
        new Chart(salesChart, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
                datasets: [{
                    label: 'Vendas',
                    data: [12, 19, 8, 15, 23, 16],
                    borderColor: '#892e82',
                    backgroundColor: 'rgba(137, 46, 130, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
    
    // Gráfico de produtos mais vendidos
    const productsChart = document.getElementById('productsChart');
    if (productsChart) {
        new Chart(productsChart, {
            type: 'doughnut',
            data: {
                labels: ['Perucas', 'Extensões', 'Acessórios'],
                datasets: [{
                    data: [45, 35, 20],
                    backgroundColor: ['#892e82', '#e782a0', '#f5e6f3']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
}

// Tabelas
function initializeTables() {
    // Ordenação de tabelas
    const tables = document.querySelectorAll('.table');
    tables.forEach(table => {
        const headers = table.querySelectorAll('th[data-sort]');
        headers.forEach(header => {
            header.style.cursor = 'pointer';
            header.addEventListener('click', () => sortTable(table, header));
        });
    });
}

function sortTable(table, header) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    const columnIndex = Array.from(header.parentNode.children).indexOf(header);
    const isAscending = header.classList.contains('sort-asc');
    
    // Remover classes de ordenação de todos os headers
    table.querySelectorAll('th').forEach(th => {
        th.classList.remove('sort-asc', 'sort-desc');
    });
    
    // Adicionar classe apropriada
    header.classList.add(isAscending ? 'sort-desc' : 'sort-asc');
    
    // Ordenar linhas
    rows.sort((a, b) => {
        const aValue = a.children[columnIndex].textContent.trim();
        const bValue = b.children[columnIndex].textContent.trim();
        
        // Tentar converter para número
        const aNum = parseFloat(aValue.replace(/[^\d.-]/g, ''));
        const bNum = parseFloat(bValue.replace(/[^\d.-]/g, ''));
        
        if (!isNaN(aNum) && !isNaN(bNum)) {
            return isAscending ? bNum - aNum : aNum - bNum;
        } else {
            return isAscending ? 
                bValue.localeCompare(aValue) : 
                aValue.localeCompare(bValue);
        }
    });
    
    // Reordenar no DOM
    rows.forEach(row => tbody.appendChild(row));
}

// Utilitários
function formatCurrency(value) {
    return new Intl.NumberFormat('pt-CV', {
        style: 'currency',
        currency: 'CVE'
    }).format(value);
}

function formatDate(date) {
    return new Intl.DateTimeFormat('pt-CV').format(new Date(date));
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Confirmação de ações
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// Loading
function showLoading() {
    const loading = document.createElement('div');
    loading.id = 'loading';
    loading.innerHTML = `
        <div style="
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        ">
            <div style="
                background: white;
                padding: 2rem;
                border-radius: 12px;
                text-align: center;
            ">
                <div style="
                    width: 40px;
                    height: 40px;
                    border: 4px solid #f3f3f3;
                    border-top: 4px solid #892e82;
                    border-radius: 50%;
                    animation: spin 1s linear infinite;
                    margin: 0 auto 1rem;
                "></div>
                <p>Carregando...</p>
            </div>
        </div>
    `;
    
    document.body.appendChild(loading);
}

function hideLoading() {
    const loading = document.getElementById('loading');
    if (loading) {
        loading.remove();
    }
}

// Adicionar CSS para animação de loading
const style = document.createElement('style');
style.textContent = `
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(-100%); opacity: 0; }
    }
    
    .is-invalid {
        border-color: var(--danger-color) !important;
        box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1) !important;
    }
`;
document.head.appendChild(style);