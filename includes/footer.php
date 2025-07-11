</div> <!-- fim main-layout -->
    
    <script src="assets/js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <?php if (isset($extraJS)): ?>
        <?php foreach ($extraJS as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <script>
        // Toggle do menu do usuário
        function toggleUserMenu() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
        }
        
        // Fechar dropdown ao clicar fora
        document.addEventListener('click', function(e) {
            const userMenu = document.querySelector('.user-menu');
            const dropdown = document.getElementById('userDropdown');
            
            if (!userMenu.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });
        
        // Adicionar estilos para o dropdown
        const dropdownStyles = `
            .user-dropdown {
                position: absolute;
                top: 100%;
                right: 0;
                background: white;
                border: 1px solid var(--border-color);
                border-radius: var(--border-radius);
                box-shadow: var(--shadow);
                min-width: 200px;
                z-index: 1000;
                margin-top: 0.5rem;
            }
            
            .user-dropdown a {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.75rem 1rem;
                color: var(--dark-color);
                text-decoration: none;
                transition: var(--transition);
            }
            
            .user-dropdown a:hover {
                background: var(--accent-color);
                color: var(--primary-color);
            }
            
            .user-dropdown hr {
                margin: 0.5rem 0;
                border: none;
                border-top: 1px solid var(--border-color);
            }
            
            .header-right {
                position: relative;
            }
        `;
        
        const style = document.createElement('style');
        style.textContent = dropdownStyles;
        document.head.appendChild(style);
    </script>
</body>
</html>