<aside class="sidebar">
            <nav>
                <ul class="sidebar-nav">
                    <li>
                        <a href="/dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="/pedidos.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'pedidos.php' ? 'active' : ''; ?>">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Pedidos</span>
                        </a>
                    </li>
                    <li>
                        <a href="/clientes.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'clientes.php' ? 'active' : ''; ?>">
                            <i class="fas fa-users"></i>
                            <span>Clientes</span>
                        </a>
                    </li>
                    <li>
                        <a href="/produtos.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'produtos.php' ? 'active' : ''; ?>">
                            <i class="fas fa-box"></i>
                            <span>Produtos</span>
                        </a>
                    </li>
                    <li>
                        <a href="/estoque.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'estoque.php' ? 'active' : ''; ?>">
                            <i class="fas fa-warehouse"></i>
                            <span>Estoque</span>
                        </a>
                    </li>
                    <li>
                        <a href="/relatorios.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'relatorios.php' ? 'active' : ''; ?>">
                            <i class="fas fa-chart-bar"></i>
                            <span>Relatórios</span>
                        </a>
                    </li>
                    <li>
                        <a href="/usuarios.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'usuarios.php' ? 'active' : ''; ?>">
                            <i class="fas fa-user-cog"></i>
                            <span>Usuários</span>
                        </a>
                    </li>
                    <li>
                        <a href="/configuracoes.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'configuracoes.php' ? 'active' : ''; ?>">
                            <i class="fas fa-cog"></i>
                            <span>Configurações</span>
                        </a>
                    </li>
                    <li>
                        <a href="/backup.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'backup.php' ? 'active' : ''; ?>">
                            <i class="fas fa-database"></i>
                            <span>Backup</span>
                        </a>
                    </li>
                    <li style="margin-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1rem;">
                        <a href="/logout.php" onclick="return confirm('Tem certeza que deseja sair?')">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Sair</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>