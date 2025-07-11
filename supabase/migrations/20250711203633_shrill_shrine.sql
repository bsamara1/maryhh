-- Mary Human Hair - Database Schema
-- Execute este script no MySQL para criar todas as tabelas

CREATE DATABASE IF NOT EXISTS maryhh CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE maryhh;

-- Tabela de tipos de utilizador
CREATE TABLE IF NOT EXISTS tipo_utilizador (
    idTipoUtilizador INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(50) NOT NULL,
    descricao TEXT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Inserir tipos padrão
INSERT INTO tipo_utilizador (idTipoUtilizador, nome, descricao) VALUES 
(1, 'Administrador', 'Acesso total ao sistema'),
(2, 'Funcionário', 'Acesso limitado às funcionalidades básicas')
ON DUPLICATE KEY UPDATE nome = VALUES(nome);

-- Tabela de utilizadores
CREATE TABLE IF NOT EXISTS utilizador (
    utilizador_id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    idTipoUtilizador INT DEFAULT 2,
    ativo BOOLEAN DEFAULT TRUE,
    ultimo_login TIMESTAMP NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (idTipoUtilizador) REFERENCES tipo_utilizador(idTipoUtilizador)
);

-- Inserir usuário administrador padrão
INSERT INTO utilizador (nome, email, senha, idTipoUtilizador) VALUES 
('Administrador', 'admin@maryhumanhair.cv', 'admin123', 1)
ON DUPLICATE KEY UPDATE nome = VALUES(nome);

-- Tabela de clientes
CREATE TABLE IF NOT EXISTS clientes (
    idCliente INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    telefone VARCHAR(20),
    data_nascimento DATE,
    endereco TEXT,
    cidade VARCHAR(50),
    pais VARCHAR(50) DEFAULT 'Cabo Verde',
    observacoes TEXT,
    ativo BOOLEAN DEFAULT TRUE,
    data_registo TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nome (nome),
    INDEX idx_email (email),
    INDEX idx_telefone (telefone)
);

-- Tabela de categorias de produtos
CREATE TABLE IF NOT EXISTS categorias (
    categoria_id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(50) NOT NULL,
    descricao TEXT,
    ativo BOOLEAN DEFAULT TRUE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Inserir categorias padrão
INSERT INTO categorias (nome, descricao) VALUES 
('Perucas', 'Perucas de cabelo humano e sintético'),
('Extensões', 'Extensões de cabelo natural'),
('Acessórios', 'Produtos para cuidado capilar'),
('Ferramentas', 'Ferramentas para aplicação')
ON DUPLICATE KEY UPDATE nome = VALUES(nome);

-- Tabela de produtos
CREATE TABLE IF NOT EXISTS produtos (
    produto_id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    categoria_id INT,
    tipo VARCHAR(50),
    comprimento DECIMAL(4,1),
    textura VARCHAR(50),
    cor VARCHAR(50),
    preco DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    preco_custo DECIMAL(10,2) DEFAULT 0.00,
    estoque INT NOT NULL DEFAULT 0,
    estoque_minimo INT DEFAULT 5,
    descricao TEXT,
    especificacoes JSON,
    imagem VARCHAR(255),
    sku VARCHAR(50) UNIQUE,
    ativo BOOLEAN DEFAULT TRUE,
    destaque BOOLEAN DEFAULT FALSE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(categoria_id),
    INDEX idx_nome (nome),
    INDEX idx_categoria (categoria_id),
    INDEX idx_preco (preco),
    INDEX idx_estoque (estoque),
    INDEX idx_sku (sku)
);

-- Tabela de pedidos
CREATE TABLE IF NOT EXISTS pedidos (
    pedido_id INT PRIMARY KEY AUTO_INCREMENT,
    idCliente INT NOT NULL,
    numero_pedido VARCHAR(20) UNIQUE,
    destino VARCHAR(100),
    endereco_entrega TEXT,
    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    desconto DECIMAL(10,2) DEFAULT 0.00,
    taxa_entrega DECIMAL(10,2) DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status ENUM('pendente', 'confirmado', 'processando', 'enviado', 'entregue', 'concluido', 'cancelado') DEFAULT 'pendente',
    metodo_pagamento VARCHAR(50),
    status_pagamento ENUM('pendente', 'pago', 'parcial', 'cancelado') DEFAULT 'pendente',
    data_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_entrega_prevista DATE,
    data_entrega_real DATE,
    observacoes TEXT,
    observacoes_internas TEXT,
    criado_por INT,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (idCliente) REFERENCES clientes(idCliente),
    FOREIGN KEY (criado_por) REFERENCES utilizador(utilizador_id),
    INDEX idx_cliente (idCliente),
    INDEX idx_status (status),
    INDEX idx_data (data_pedido),
    INDEX idx_numero (numero_pedido)
);

-- Tabela de itens do pedido
CREATE TABLE IF NOT EXISTS itens_pedido (
    item_id INT PRIMARY KEY AUTO_INCREMENT,
    pedido_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL DEFAULT 1,
    preco_unitario DECIMAL(10,2) NOT NULL,
    desconto DECIMAL(10,2) DEFAULT 0.00,
    subtotal DECIMAL(10,2) NOT NULL,
    observacoes TEXT,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(pedido_id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(produto_id),
    INDEX idx_pedido (pedido_id),
    INDEX idx_produto (produto_id)
);

-- Tabela de movimentos de estoque
CREATE TABLE IF NOT EXISTS movimentos_estoque (
    movimento_id INT PRIMARY KEY AUTO_INCREMENT,
    produto_id INT NOT NULL,
    tipo ENUM('entrada', 'saida', 'ajuste', 'transferencia') NOT NULL,
    quantidade INT NOT NULL,
    estoque_anterior INT NOT NULL,
    estoque_atual INT NOT NULL,
    motivo VARCHAR(100),
    observacao TEXT,
    pedido_id INT NULL,
    usuario_id INT,
    data_movimento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (produto_id) REFERENCES produtos(produto_id),
    FOREIGN KEY (pedido_id) REFERENCES pedidos(pedido_id),
    FOREIGN KEY (usuario_id) REFERENCES utilizador(utilizador_id),
    INDEX idx_produto (produto_id),
    INDEX idx_tipo (tipo),
    INDEX idx_data (data_movimento)
);

-- Tabela de configurações do sistema
CREATE TABLE IF NOT EXISTS configuracoes (
    config_id INT PRIMARY KEY AUTO_INCREMENT,
    chave VARCHAR(100) UNIQUE NOT NULL,
    valor TEXT,
    descricao TEXT,
    tipo ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Inserir configurações padrão
INSERT INTO configuracoes (chave, valor, descricao, tipo) VALUES 
('empresa_nome', 'Mary Human Hair', 'Nome da empresa', 'string'),
('empresa_email', 'contato@maryhumanhair.cv', 'Email da empresa', 'string'),
('empresa_telefone', '+238 900-0000', 'Telefone da empresa', 'string'),
('empresa_endereco', 'Praia, Cabo Verde', 'Endereço da empresa', 'string'),
('moeda', 'CVE', 'Moeda padrão', 'string'),
('taxa_entrega_padrao', '500.00', 'Taxa de entrega padrão', 'number'),
('estoque_minimo_alerta', '10', 'Quantidade mínima para alerta de estoque', 'number')
ON DUPLICATE KEY UPDATE valor = VALUES(valor);

-- Tabela de logs do sistema
CREATE TABLE IF NOT EXISTS logs_sistema (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT,
    acao VARCHAR(100) NOT NULL,
    tabela VARCHAR(50),
    registro_id INT,
    dados_anteriores JSON,
    dados_novos JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    data_log TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES utilizador(utilizador_id),
    INDEX idx_usuario (usuario_id),
    INDEX idx_acao (acao),
    INDEX idx_data (data_log)
);

-- Triggers para atualizar estoque automaticamente
DELIMITER //

CREATE TRIGGER after_item_pedido_insert
AFTER INSERT ON itens_pedido
FOR EACH ROW
BEGIN
    DECLARE estoque_atual INT;
    
    -- Obter estoque atual
    SELECT estoque INTO estoque_atual FROM produtos WHERE produto_id = NEW.produto_id;
    
    -- Atualizar estoque
    UPDATE produtos 
    SET estoque = estoque - NEW.quantidade 
    WHERE produto_id = NEW.produto_id;
    
    -- Registrar movimento
    INSERT INTO movimentos_estoque (produto_id, tipo, quantidade, estoque_anterior, estoque_atual, motivo, pedido_id)
    VALUES (NEW.produto_id, 'saida', NEW.quantidade, estoque_atual, estoque_atual - NEW.quantidade, 'Venda', NEW.pedido_id);
END//

CREATE TRIGGER after_item_pedido_delete
AFTER DELETE ON itens_pedido
FOR EACH ROW
BEGIN
    DECLARE estoque_atual INT;
    
    -- Obter estoque atual
    SELECT estoque INTO estoque_atual FROM produtos WHERE produto_id = OLD.produto_id;
    
    -- Restaurar estoque
    UPDATE produtos 
    SET estoque = estoque + OLD.quantidade 
    WHERE produto_id = OLD.produto_id;
    
    -- Registrar movimento
    INSERT INTO movimentos_estoque (produto_id, tipo, quantidade, estoque_anterior, estoque_atual, motivo, pedido_id)
    VALUES (OLD.produto_id, 'entrada', OLD.quantidade, estoque_atual, estoque_atual + OLD.quantidade, 'Cancelamento', OLD.pedido_id);
END//

DELIMITER ;

-- Views úteis
CREATE VIEW view_produtos_estoque_baixo AS
SELECT 
    p.produto_id,
    p.nome,
    p.estoque,
    p.estoque_minimo,
    c.nome as categoria,
    p.preco
FROM produtos p
LEFT JOIN categorias c ON p.categoria_id = c.categoria_id
WHERE p.estoque <= p.estoque_minimo AND p.ativo = TRUE;

CREATE VIEW view_pedidos_resumo AS
SELECT 
    p.pedido_id,
    p.numero_pedido,
    c.nome as cliente_nome,
    c.email as cliente_email,
    p.total,
    p.status,
    p.data_pedido,
    COUNT(ip.item_id) as total_itens
FROM pedidos p
JOIN clientes c ON p.idCliente = c.idCliente
LEFT JOIN itens_pedido ip ON p.pedido_id = ip.pedido_id
GROUP BY p.pedido_id;

-- Inserir dados de exemplo (opcional)
INSERT INTO clientes (nome, email, telefone, endereco) VALUES 
('Maria Silva', 'maria@email.com', '+238 900-1111', 'Praia, Santiago'),
('Ana Costa', 'ana@email.com', '+238 900-2222', 'Mindelo, São Vicente'),
('Carla Santos', 'carla@email.com', '+238 900-3333', 'Sal Rei, Boa Vista');

INSERT INTO produtos (nome, categoria_id, tipo, comprimento, cor, preco, estoque, descricao) VALUES 
('Peruca Lisa Natural', 1, 'Peruca', 16.0, 'Preto Natural', 15000.00, 25, 'Peruca de cabelo humano 100% natural'),
('Extensão Cacheada', 2, 'Extensão', 20.0, 'Castanho', 8000.00, 30, 'Extensão de cabelo cacheado natural'),
('Shampoo Hidratante', 3, 'Cuidado', NULL, NULL, 1500.00, 50, 'Shampoo para cabelos tratados'),
('Aplicador de Extensões', 4, 'Ferramenta', NULL, NULL, 2500.00, 15, 'Ferramenta profissional para aplicação');

COMMIT;