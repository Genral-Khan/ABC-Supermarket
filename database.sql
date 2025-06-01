-- Create database
CREATE DATABASE IF NOT EXISTS abc_supermarket;
USE abc_supermarket;

-- Users table
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE categories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    image_url VARCHAR(255)
);

-- Products table
CREATE TABLE products (
    product_id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock_quantity INT NOT NULL,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

-- Cart table
CREATE TABLE cart (
    cart_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    product_id INT,
    quantity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- Orders table
CREATE TABLE orders (
    order_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    total_amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Order items table
CREATE TABLE order_items (
    order_item_id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price_at_time DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- Insert sample categories
INSERT INTO categories (name, description) VALUES
('Grocery', 'Fresh groceries and daily essentials'),
('Fashion', 'Trendy clothing and accessories'),
('Electronics', 'Latest gadgets and electronics');

-- Insert sample products
INSERT INTO products (category_id, name, description, price, stock_quantity, image_url) VALUES
(1, 'Chicken Breast Fillet 500g', 'Boneless skinless chicken breast fillet.', 28.00, 100, 'chicken_breast.jpg'),
(1, 'Apples per KG', 'Fresh red apples sold per kilogram.', 8.00, 100, 'apples.jpg'),
(1, 'Cherries 500g', 'Juicy fresh cherries, 500g pack.', 12.00, 100, 'cherries.jpg'),
(1, 'White Eggs 30 PCS', 'Fresh medium white eggs, 30 pieces.', 28.00, 100, 'eggs.jpg'),
(1, 'Salmon Fillet', 'Fresh Norwegian salmon fillet.', 26.00, 100, 'salmon.jpg'),
(1, 'Fresh Milk 3.8L', 'Full fat fresh milk in 3.8L bottle.', 8.00, 100, 'milk.jpg'),
(1, 'Tomato Ketchup 910g', 'Tomato ketchup in squeeze bottle.', 18.00, 100, 'ketchup.jpg'),
(1, 'Drinking Water 1.5L x12', 'Pack of 12 1.5L drinking water bottles.', 10.00, 100, 'water.jpg'),
(1, 'Potatoes 3Kg', 'Fresh potatoes, 3kg pack.', 12.50, 100, 'potatoes.jpg'),
(1, 'Jasmine Rice 5kg', 'Vietnam jasmine rice, 5kg bag.', 19.00, 100, 'rice.jpg');
