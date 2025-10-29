-- SQL para crear la base de datos y tablas para FoodExpress
-- Nombre DB: tasm_foodexpress

CREATE DATABASE IF NOT EXISTS `tasm_foodexpress` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `tasm_foodexpress`;

CREATE TABLE IF NOT EXISTS `tasm_products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(191) NOT NULL,
  `description` TEXT,
  `category` VARCHAR(100) DEFAULT 'General',
  `price` DECIMAL(10,2) NOT NULL
);

CREATE TABLE IF NOT EXISTS `tasm_orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `customer_name` VARCHAR(191) NOT NULL,
  `phone` VARCHAR(50),
  `address` TEXT NOT NULL,
  `created_at` DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS `tasm_order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `qty` INT NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES tasm_orders(id) ON DELETE CASCADE
);

-- Seed: mínimo 10 productos
INSERT INTO tasm_products (name,description,category,price) VALUES
('Pizza Margherita','Salsa de tomate, mozzarella y albahaca','Pizzas',8.50),
('Pizza Pepperoni','Con pepperoni picante','Pizzas',9.50),
('Hamburguesa Clásica','Carne, lechuga, tomate, queso','Hamburguesas',7.00),
('Hamburguesa Doble','Dos carnes, doble queso','Hamburguesas',9.00),
('Ensalada César','Lechuga, pollo, aderezo César','Ensaladas',6.50),
('Wrap de Pollo','Pollo a la plancha con vegetales','Wraps',5.50),
('Papas Fritas','Papas crujientes','Acompañamientos',2.50),
('Sopa del Día','Consulta al local','Entradas',3.00),
('Bebida Gaseosa','Lata 330ml','Bebidas',1.50),
('Postre Brownie','Brownie con helado','Postres',3.50);
