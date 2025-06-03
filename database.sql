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
INSERT INTO categories (name, description, image_url) VALUES
('Grocery', 'Fresh groceries and daily essentials', 'Grocery.jpg'),
('Fashion', 'Trendy clothing and accessories', 'Fashion.jpg'),
('Electronics', 'Latest gadgets and electronics', 'Electronics.jpg');

-- Insert sample products
-- Grocery
INSERT INTO products (category_id, name, description, price, stock_quantity, image_url) VALUES
(1, 'Chicken Fillet 500g', 'Boneless skinless chicken fillet.', 8.00, 100, 'chicken.jpg'),
(1, 'Eggs 30 PCS', 'Fresh medium-sized eggs, 30 pieces.', 15.00, 100, 'eggs.jpg'),
(1, 'Fresh Milk 1L', 'High-quality full-fat fresh milk.', 8.00, 100, 'milk.jpg'),
(1, 'Tomato Ketchup 460g', 'Tomato ketchup in a squeeze bottle.', 10.00, 100, 'ketchup.jpg'),
(1, 'Drinking Water 1.5L x12', 'Pack of 12 water bottles.', 10.00, 100, 'water.jpg'),
(1, 'Jasmine Rice 5kg', 'Premium jasmine rice, 5kg bag.', 19.00, 100, 'rice.jpg'),
(1, 'Fresh Salmon Fillet', 'Norwegian salmon fillet.', 26.00, 100, 'salmon.jpg'),
(1, 'Fresh Strawberries 250g', 'Sweet and juicy strawberries.', 12.00, 100, 'strawberries.jpg');

-- Fashion
INSERT INTO products (category_id, name, description, price, stock_quantity, image_url) VALUES
(2, 'Anime T-Shirt', 'Stylish anime-printed shirt.', 39.00, 50, 'animeshirt.jpg'),
(2, 'Red T-Shirt', 'Plain red cotton t-shirt.', 50.00, 60, 'Redshirt.jpg'),
(2, 'Hoodie', 'Warm and soft cotton hoodie.', 10.00, 40, 'hoodie.jpg'),
(2, 'Cap', 'Classic baseball cap.', 20.00, 100, 'cap.jpg'),
(2, 'Skinny Jeans', 'Blue skinny jeans, stretchable.', 100.00, 30, 'skinnyjeans.jpg'),
(2, 'Coolbag', 'Trendy fashion coolbag for outings.', 60.00, 25, 'coolbag.jpg');

-- Electronics
INSERT INTO products (category_id, name, description, price, stock_quantity, image_url) VALUES
(3, 'iPhone 14 Pro', 'Latest iPhone 14 Pro with A16 chip.', 999.00, 20, 'iphone.jpg'),
(3, 'Samsung Galaxy S22', 'Samsung Galaxy S22 flagship phone.', 899.00, 15, 'samsung.jpg'),
(3, 'Huawei Mate 50', 'Huawei’s high-end smartphone.', 850.00, 15, 'huwawei.jpg'),
(3, 'JBL Speaker', 'Portable wireless JBL speaker.', 149.00, 50, 'jbl.jpg'),
(3, 'Smart TV 55"', 'Ultra HD Smart TV with built-in apps.', 1599.00, 10, 'tv.jpg'),
(3, 'Apple Headphones', 'Classic Apple wired headphones with lightning connector.', 529.00, 50, 'apple.jpg'),
(3, 'Apple AirPods Pro', 'Wireless noise-cancelling earbuds with MagSafe charging case.', 799.00, 50, 'airpods.jpeg');