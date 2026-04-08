-- Create Database
CREATE DATABASE IF NOT EXISTS soft_drink_store;
USE soft_drink_store;

-- Admin Users Table
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories Table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products Table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    stock_quantity INT DEFAULT 0,
    image VARCHAR(255),
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Customers Table
CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders Table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT,
    total_amount DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL
);

-- Order Details Table
CREATE TABLE order_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

-- Insert Default Admin (password: admin123)
INSERT INTO admin_users (username, password, full_name, email) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@softdrink.com');

-- Insert Sample Categories
INSERT INTO categories (name, description) VALUES
('Carbonated Drinks', 'Fizzy soft drinks with carbonation'),
('Juice', 'Natural and artificial fruit juices'),
('Energy Drinks', 'High caffeine energy beverages'),
('Water', 'Bottled water and flavored water'),
('Tea & Coffee', 'Ready to drink tea and coffee');

-- Insert Sample Products
INSERT INTO products (category_id, name, description, price, stock_quantity) VALUES
(1, 'Coca-Cola 330ml', 'Classic Coca-Cola can', 1.50, 200),
(1, 'Pepsi 330ml', 'Pepsi Cola can', 1.50, 180),
(1, 'Sprite 330ml', 'Lemon-lime flavored drink', 1.50, 150),
(1, 'Fanta Orange 330ml', 'Orange flavored carbonated drink', 1.50, 160),
(2, 'Tropicana Orange Juice 1L', 'Pure orange juice', 3.99, 80),
(2, 'Apple Juice 500ml', 'Natural apple juice', 2.50, 100),
(3, 'Red Bull 250ml', 'Energy drink', 2.99, 120),
(3, 'Monster Energy 500ml', 'Energy drink', 3.50, 90),
(4, 'Evian Water 500ml', 'Natural mineral water', 1.00, 300),
(5, 'Lipton Ice Tea 500ml', 'Lemon iced tea', 2.00, 140);

-- Insert Sample Customers
INSERT INTO customers (full_name, email, phone, address) VALUES
('John Doe', 'john@example.com', '0123456789', '123 Main St, City'),
('Jane Smith', 'jane@example.com', '0987654321', '456 Oak Ave, Town'),
('Bob Wilson', 'bob@example.com', '0112233445', '789 Pine Rd, Village');

-- Insert Sample Orders
INSERT INTO orders (customer_id, total_amount, status) VALUES
(1, 12.50, 'completed'),
(2, 8.99, 'processing'),
(3, 25.00, 'pending');

-- Insert Sample Order Details
INSERT INTO order_details (order_id, product_id, quantity, price, subtotal) VALUES
(1, 1, 5, 1.50, 7.50),
(1, 5, 1, 3.99, 3.99),
(1, 9, 1, 1.00, 1.00),
(2, 7, 3, 2.99, 8.97),
(3, 2, 10, 1.50, 15.00),
(3, 10, 5, 2.00, 10.00);