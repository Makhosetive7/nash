-- NashMart Mobile Shop Management System - Database Schema

-- Create database
CREATE DATABASE IF NOT EXISTS nashmart_db 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE nashmart_db;

-- Table: products - Stores product information including inventory
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL CHECK (price > 0),
    quantity_in_stock INT NOT NULL DEFAULT 0 CHECK (quantity_in_stock >= 0),
    product_image VARCHAR(255) DEFAULT NULL,
    date_uploaded TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_product_name (product_name),
    INDEX idx_date_uploaded (date_uploaded),
    INDEX idx_quantity (quantity_in_stock)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: sales - Stores sales transactions with product references
CREATE TABLE IF NOT EXISTS sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    quantity_sold INT NOT NULL CHECK (quantity_sold > 0),
    total_price DECIMAL(10, 2) NOT NULL CHECK (total_price > 0),
    date_of_sale TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_product_id (product_id),
    INDEX idx_date_of_sale (date_of_sale),
    
    -- Foreign key constraint with CASCADE delete prevention
    CONSTRAINT fk_sales_product 
        FOREIGN KEY (product_id) 
        REFERENCES products(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample Data for Testing

-- Insert sample products
INSERT INTO products (product_name, description, price, quantity_in_stock, product_image) VALUES
('iPhone 14 Pro', 'Latest Apple smartphone with A16 Bionic chip, 6.1-inch display', 999.99, 15, NULL),
('Samsung Galaxy S23', 'Flagship Android phone with 8GB RAM, 256GB storage', 899.99, 20, NULL),
('Google Pixel 7', 'Pure Android experience with excellent camera', 699.99, 12, NULL),
('OnePlus 11', '5G smartphone with Snapdragon 8 Gen 2', 749.99, 18, NULL),
('Xiaomi 13 Pro', 'High-performance phone with Leica camera', 799.99, 10, NULL),
('Phone Case - Universal', 'Protective silicone case for most smartphones', 19.99, 50, NULL),
('Screen Protector Pack', 'Tempered glass screen protectors - 3 pack', 14.99, 100, NULL),
('USB-C Cable', 'Fast charging USB-C cable - 2 meters', 9.99, 75, NULL),
('Wireless Charger', '15W Qi wireless charging pad', 29.99, 30, NULL),
('Power Bank 20000mAh', 'Portable charger with dual USB ports', 39.99, 25, NULL),
('Bluetooth Earbuds', 'True wireless earbuds with noise cancellation', 79.99, 40, NULL),
('Phone Holder - Car', 'Magnetic car mount for smartphones', 24.99, 35, NULL),
('MicroSD Card 128GB', 'High-speed memory card for storage expansion', 34.99, 60, NULL),
('Cleaning Kit', 'Phone cleaning solution with microfiber cloth', 12.99, 80, NULL),
('Pop Socket', 'Collapsible phone grip and stand', 7.99, 120, NULL);

-- Insert sample sales (with realistic transactions)
INSERT INTO sales (product_id, quantity_sold, total_price, date_of_sale) VALUES
-- Recent sales (last 7 days)
(1, 2, 1999.98, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(6, 5, 99.95, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(11, 3, 239.97, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(7, 10, 149.90, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(2, 1, 899.99, DATE_SUB(NOW(), INTERVAL 4 DAY)),
(8, 8, 79.92, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(10, 2, 79.98, DATE_SUB(NOW(), INTERVAL 6 DAY)),
(15, 15, 119.85, DATE_SUB(NOW(), INTERVAL 7 DAY)),

-- Older sales (last 30 days)
(3, 1, 699.99, DATE_SUB(NOW(), INTERVAL 10 DAY)),
(9, 4, 119.96, DATE_SUB(NOW(), INTERVAL 12 DAY)),
(4, 2, 1499.98, DATE_SUB(NOW(), INTERVAL 15 DAY)),
(12, 3, 74.97, DATE_SUB(NOW(), INTERVAL 18 DAY)),
(1, 1, 999.99, DATE_SUB(NOW(), INTERVAL 20 DAY)),
(6, 8, 159.92, DATE_SUB(NOW(), INTERVAL 22 DAY)),
(13, 5, 174.95, DATE_SUB(NOW(), INTERVAL 25 DAY));


-- Schema Notes:
-- 1. Foreign Key Constraint: Sales cannot be created for non-existent products
-- 2. ON DELETE RESTRICT: Products with sales cannot be deleted (business rule)
-- 3. CHECK Constraints: Ensures data integrity (price > 0, quantity >= 0, etc.)
-- 4. Indexes: Added for frequently queried columns (product_id, date fields)
-- 5. Charset: utf8mb4 for full Unicode support including emojis
-- 6. Engine: InnoDB for transaction support and foreign keys
-- 7. Sample Data: 15 products and 15 sales for realistic testing