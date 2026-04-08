# nash# NashMart Backend - MVC Architecture

RESTful API built with PHP using Model-View-Controller (MVC) design pattern.

## Architecture

### MVC Pattern Implementation

```
nashmart/
├── models/                      # Data Layer
│   ├── Product.php             # Product database operations
│   └── Sale.php                # Sales database operations
│
├── controllers/                 # Business Logic Layer
│   ├── ProductController.php   # Product business logic
│   ├── SalesController.php     # Sales business logic with transactions
│   └── DashboardController.php # Dashboard analytics
│
├── api/                        # API Endpoints (Routes)
│   ├── get_products.php        # GET all products
│   ├── create_product.php      # POST create product
│   ├── update_product.php      # PUT update product
│   ├── delete_product.php      # DELETE product
│   ├── get_sales.php           # GET all sales
│   ├── create_sale.php         # POST create sale
│   └── dashboard.php           # GET dashboard stats
│
├── config/                     # Configuration
│   ├── database.php            # PDO connection
│   └── helpers.php             # Utility functions
│
├── uploads/                    # Product images
├── .htaccess                   # Apache configuration
├── index.php                   # API documentation page
└── test.html                   # API testing interface
```

## Key Features

### Architectural Benefits
- **Separation of Concerns** - Clear separation between data, logic, and presentation
- **Reusable Components** - Models and controllers can be used in different contexts
- **Scalable Structure** - Easy to add new features without modifying existing code
- **Maintainable Code** - Clear file organization and responsibility assignment
- **Testable** - Each layer can be unit tested independently

### Technical Features
- **PDO Prepared Statements** - Complete SQL injection prevention
- **Database Transactions** - ACID compliance for sales operations
- **Input Validation** - Comprehensive data validation and sanitization
- **File Upload Security** - Type checking, size limits, unique filenames
- **Error Handling** - Try-catch blocks with proper rollback
- **RESTful Design** - Standard HTTP methods (GET, POST, PUT, DELETE)
- **JSON Responses** - Consistent API response format

## Installation

### Prerequisites
- PHP 8.0 or higher
- MySQL 8.0 or higher
- Apache web server with mod_rewrite

### Step 1: Database Setup

```bash
# Create database and user
mysql -u root -p

CREATE DATABASE nashmart_db;
CREATE USER 'nashmart_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON nashmart_db.* TO 'nashmart_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Import schema (use the SQL file from the database folder)
mysql -u nashmart_user -p nashmart_db < nashmart_database.sql
```

### Step 2: Configure Database Connection

Edit `config/database.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'nashmart_db');
define('DB_USER', 'nashmart_user');
define('DB_PASS', 'your_password');
```

### Step 3: Deploy Files

```bash
# Copy to web server
sudo cp -r nashmart /var/www/html/

# Set permissions
sudo chown -R www-data:www-data /var/www/html/nashmart/
sudo chmod -R 755 /var/www/html/nashmart/
sudo chmod 777 /var/www/html/nashmart/uploads/
```

### Step 4: Enable Apache Modules

```bash
sudo a2enmod rewrite
sudo a2enmod headers
sudo systemctl restart apache2
```

### Step 5: Test Installation

Open in browser:
- API Documentation: `http://localhost/nashmart/`
- API Tester: `http://localhost/nashmart/test.html`

## API Documentation

### Products API

#### Get All Products
```
GET /api/get_products.php
```

#### Get Single Product
```
GET /api/get_products.php?id={id}
```

#### Create Product
```
POST /api/create_product.php
Content-Type: multipart/form-data

Parameters:
- product_name (required)
- price (required)
- quantity_in_stock (required)
- description (optional)
- product_image (optional, file)
```

#### Update Product
```
PUT /api/update_product.php?id={id}
Content-Type: application/json

Body:
{
  "product_name": "Updated Name",
  "price": 199.99,
  "quantity_in_stock": 50
}
```

#### Delete Product
```
DELETE /api/delete_product.php?id={id}
```

### Sales API

#### Get All Sales
```
GET /api/get_sales.php
```

#### Create Sale
```
POST /api/create_sale.php
Content-Type: application/json

Body:
{
  "product_id": 1,
  "quantity_sold": 2
}
```

**Note:** Creating a sale automatically:
- Validates stock availability
- Reduces product stock
- Uses database transaction for data integrity
- Returns new stock level

### Dashboard API

#### Get Statistics
```
GET /api/dashboard.php
```

Returns:
- Total products count
- Total stock value
- Total sales amount
- Total sales count
- Low stock products (≤10 units)
- Top 5 selling products
- Recent 10 sales

## Security Implementation

### SQL Injection Prevention
```php
// All database queries use PDO prepared statements
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
```

### Input Validation
```php
// Helper function validates required fields
$required = ['product_name', 'price', 'quantity_in_stock'];
$missing = validateRequired($required, $_POST);

// Sanitization prevents XSS
$name = sanitizeInput($_POST['product_name']);
```

### Transaction Management
```php
// Sales use transactions for data integrity
$pdo->beginTransaction();
try {
    // Create sale record
    // Update product stock
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
}
```

### File Upload Security
```php
// File type validation
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];

// Size limit check (5MB)
if ($file['size'] > 5 * 1024 * 1024) {
    return false;
}

// Unique filename generation
$filename = uniqid() . '_' . time() . '.' . $extension;
```

## Testing

### Using Web Interface
1. Open `http://localhost/nashmart/test.html`
2. Click buttons to test each endpoint
3. View JSON responses

### Using cURL

**Get all products:**
```bash
curl http://localhost/nashmart/api/get_products.php
```

**Create product:**
```bash
curl -X POST http://localhost/nashmart/api/create_product.php \
  -F "product_name=Test Product" \
  -F "price=99.99" \
  -F "quantity_in_stock=50"
```

**Record sale:**
```bash
curl -X POST http://localhost/nashmart/api/create_sale.php \
  -H "Content-Type: application/json" \
  -d '{"product_id": 1, "quantity_sold": 2}'
```

## 📚 Code Examples

### Adding a New Model Method

**In `models/Product.php`:**
```php
public function getExpensiveProducts($minPrice) {
    $stmt = $this->pdo->prepare("
        SELECT * FROM products 
        WHERE price >= ? 
        ORDER BY price DESC
    ");
    $stmt->execute([$minPrice]);
    return $stmt->fetchAll();
}
```

### Adding a New Controller Method

**In `controllers/ProductController.php`:**
```php
public function getExpensive($minPrice) {
    try {
        $products = $this->productModel->getExpensiveProducts($minPrice);
        sendResponse(true, 'Expensive products retrieved', $products);
    } catch (Exception $e) {
        sendResponse(false, 'Error: ' . $e->getMessage(), null, 500);
    }
}
```

### Creating a New API Endpoint

**Create `api/expensive_products.php`:**
```php
<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../controllers/ProductController.php';

setCorsHeaders();
handlePreflight();

$controller = new ProductController($pdo);
$minPrice = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 100;
$controller->getExpensive($minPrice);
?>
```

## 🎓 Interview Talking Points

### Why MVC?
*"I implemented MVC architecture to demonstrate understanding of software design patterns. It separates concerns: Models handle data persistence, Controllers manage business logic, and API endpoints route requests. This makes the codebase more maintainable and testable."*

### Transaction Management
*"For sales operations, I use database transactions with row-level locking (SELECT FOR UPDATE) to prevent race conditions. If either the sale record creation or stock update fails, the entire transaction rolls back, maintaining data integrity."*

### Security
*"Security is implemented at multiple layers: PDO prepared statements prevent SQL injection, input sanitization prevents XSS, file upload validation prevents malicious uploads, and transactions prevent data corruption. All user inputs are validated and type-cast appropriately."*

### Scalability
*"The MVC structure makes it easy to add features. Need product categories? Add a Category model, create CategoryController methods, and add API endpoints. The existing code doesn't need modification."*

## Contributing

This is an assessment project for educational purposes.

## License

Created as a technical assessment for a PHP Developer position.

## Acknowledgments

- Assessment provided by hiring company
- AI assistance used for development (disclosed as required)
- MVC pattern implementation based on industry best practices

---

**Built with:** PHP 8.x • MySQL • PDO • MVC Pattern • RESTful API Design