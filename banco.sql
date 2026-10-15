-- Importe este arquivo no phpMyAdmin (aba "Importar") ou cole na aba SQL
CREATE DATABASE IF NOT EXISTS loja_sapatos
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE loja_sapatos;

CREATE TABLE IF NOT EXISTS produtos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  codigo CHAR(1) NOT NULL UNIQUE,
  nome VARCHAR(100) NOT NULL,
  descricao VARCHAR(255) DEFAULT NULL,
  preco DECIMAL(10,2) NOT NULL,
  cor VARCHAR(7) NOT NULL DEFAULT '#cccccc',
  ativo TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS pedidos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cliente_nome VARCHAR(120) NOT NULL,
  cliente_email VARCHAR(120) NOT NULL,
  cliente_telefone VARCHAR(30) NOT NULL,
  endereco VARCHAR(255) NOT NULL,
  total DECIMAL(10,2) NOT NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS itens_pedido (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pedido_id INT NOT NULL,
  produto_id INT NOT NULL,
  quantidade INT NOT NULL,
  preco_unitario DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
  FOREIGN KEY (produto_id) REFERENCES produtos(id)
) ENGINE=InnoDB;

-- Preços são exemplos: ajuste como quiser
INSERT INTO produtos (codigo, nome, descricao, preco, cor) VALUES
('A', 'Sapatos Azuis Malhado', 'Azul com acabamento malhado, estiloso e confortável.', 129.90, '#2f6fdb'),
('B', 'Sapatos Preto', 'Clássico preto para todas as ocasiões.', 119.90, '#222222'),
('C', 'Sapatos Rosa Arthur', 'Edição rosa Arthur, cheia de personalidade.', 139.90, '#ee7fb0'),
('D', 'Sapatos Outros (D)', 'Modelo versátil para o dia a dia.', 99.90, '#8a8f98'),
('E', 'Sapatos Agineligena', 'Modelo exclusivo Agineligena.', 149.90, '#7a4fd1'),
('F', 'Sapatos Marombas', 'Reforçado, feito para quem treina pesado.', 159.90, '#d9402b'),
('G', 'Sapatos Outros (G)', 'Modelo versátil, outra opção de cor e estilo.', 99.90, '#2e9e6b');
