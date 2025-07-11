# Mary Human Hair - Sistema de Gestão

Sistema completo de gestão para loja de produtos capilares desenvolvido em PHP.

## 🚀 Funcionalidades

### Dashboard
- Visão geral do negócio
- Estatísticas em tempo real
- Gráficos de vendas
- Produtos com baixo estoque
- Pedidos recentes

### Gestão de Clientes
- Cadastro completo de clientes
- Busca e filtros avançados
- Histórico de pedidos
- Exportação de dados

### Gestão de Produtos
- Catálogo completo de produtos
- Controle de estoque
- Categorização por tipo
- Alertas de baixo estoque
- Ajuste rápido de estoque

### Gestão de Pedidos
- Criação e edição de pedidos
- Controle de status
- Filtros por data e status
- Impressão de pedidos
- Relatórios de vendas

### Sistema de Usuários
- Autenticação segura
- Diferentes níveis de acesso
- Perfis de usuário
- Controle de sessão

## 🛠️ Tecnologias Utilizadas

- **Backend**: PHP 7.4+
- **Banco de Dados**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript
- **Frameworks CSS**: Custom CSS com variáveis
- **Ícones**: Font Awesome 6
- **Gráficos**: Chart.js

## 📋 Requisitos do Sistema

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Apache/Nginx
- Extensões PHP: PDO, PDO_MySQL

## 🔧 Instalação

1. **Clone o repositório**
   ```bash
   git clone [url-do-repositorio]
   cd mary-human-hair
   ```

2. **Configure o banco de dados**
   - Crie um banco de dados MySQL
   - Execute o script SQL fornecido
   - Configure as credenciais em `config/database.php`

3. **Configure o servidor web**
   - Aponte o document root para a pasta do projeto
   - Certifique-se de que o mod_rewrite está habilitado

4. **Permissões**
   ```bash
   chmod 755 -R .
   chmod 777 -R uploads/ (se existir)
   ```

## 🗄️ Estrutura do Banco de Dados

### Tabelas Principais

- `utilizador` - Usuários do sistema
- `clientes` - Clientes da loja
- `produtos` - Catálogo de produtos
- `pedidos` - Pedidos realizados
- `itens_pedido` - Itens de cada pedido
- `movimentos_estoque` - Histórico de movimentações

## 🎨 Estrutura do Projeto

```
/
├── config/
│   └── database.php          # Configuração do banco
├── includes/
│   ├── functions.php         # Funções auxiliares
│   ├── header.php           # Cabeçalho comum
│   ├── sidebar.php          # Menu lateral
│   └── footer.php           # Rodapé comum
├── assets/
│   ├── css/
│   │   └── style.css        # Estilos principais
│   ├── js/
│   │   └── main.js          # JavaScript principal
│   └── images/              # Imagens do sistema
├── actions/                 # Scripts de ação (CRUD)
├── exports/                 # Scripts de exportação
├── login.php               # Página de login
├── dashboard.php           # Dashboard principal
├── clientes.php           # Gestão de clientes
├── produtos.php           # Gestão de produtos
├── pedidos.php            # Gestão de pedidos
└── index.php              # Página inicial
```

## 🔐 Segurança

- Sanitização de inputs
- Prepared statements (PDO)
- Controle de sessão
- Validação de permissões
- Proteção contra SQL Injection
- Proteção contra XSS

## 📱 Responsividade

O sistema é totalmente responsivo e funciona em:
- Desktop (1200px+)
- Tablet (768px - 1199px)
- Mobile (até 767px)

## 🎯 Funcionalidades Avançadas

### Dashboard Inteligente
- Métricas em tempo real
- Gráficos interativos
- Alertas automáticos
- Resumo executivo

### Busca Avançada
- Busca em tempo real
- Filtros múltiplos
- Ordenação dinâmica
- Exportação de resultados

### Controle de Estoque
- Alertas de baixo estoque
- Movimentações automáticas
- Relatórios de estoque
- Ajustes manuais

### Interface Moderna
- Design clean e profissional
- Animações suaves
- Feedback visual
- Experiência intuitiva

## 🚀 Como Usar

### Primeiro Acesso
1. Acesse `/login.php`
2. Use as credenciais padrão ou crie um usuário
3. Complete seu perfil
4. Comece adicionando produtos e clientes

### Fluxo de Trabalho
1. **Cadastre produtos** no catálogo
2. **Registre clientes** conforme chegam
3. **Crie pedidos** para os clientes
4. **Acompanhe o status** dos pedidos
5. **Monitore o estoque** regularmente
6. **Analise relatórios** para tomar decisões

## 🔄 Atualizações

O sistema está em constante evolução. Principais melhorias implementadas:

- ✅ Interface moderna e responsiva
- ✅ Sistema de busca avançado
- ✅ Controle de estoque inteligente
- ✅ Dashboard com métricas
- ✅ Exportação de dados
- ✅ Sistema de alertas
- ✅ Validação de formulários
- ✅ Segurança aprimorada

## 📞 Suporte

Para suporte técnico ou dúvidas:
- Email: suporte@maryhumanhair.cv
- Telefone: (+238) 900-0000

## 📄 Licença

Este projeto é propriedade da Mary Human Hair. Todos os direitos reservados.

---

**Mary Human Hair** - Beleza e qualidade em cada fio! 💇‍♀️✨