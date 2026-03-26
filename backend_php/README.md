# NashMart Mobile Shop Management System

A complete mobile shop inventory and sales management system built with PHP backend (MVC architecture) and Flutter mobile app.

[![PHP Version](https://img.shields.io/badge/PHP-8.4%2B-blue)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-orange)](https://mysql.com)
[![License](https://img.shields.io/badge/License-MIT-green)](LICENSE)

---

## Table of Contents
- [Features](#features)
- [Technology Stack](#technology-stack)
- [Installation](#installation)
- [API Documentation](#api-documentation)
- [Testing the API](#testing-the-api)
- [Project Structure](#project-structure)
- [Security Features](#security-features)
- [Troubleshooting](#troubleshooting)

---

## Features

### Backend (PHP + MySQL)
- **Product Management**: Full CRUD operations with image upload support
- **Sales Recording**: Automatic stock management with database transactions
- **Dashboard Analytics**: Real-time statistics, low stock alerts, top products
- **MVC Architecture**: Clean separation of concerns (Models, Views, Controllers)
- **Security**: SQL injection prevention, input validation, secure file uploads
- **RESTful API**: Clean endpoint design with consistent JSON responses

---

## Technology Stack

**Backend:**
- PHP 8.4+
- MySQL 8.0+
- Apache/Nginx web server
- PDO for database operations
- MVC Design Pattern

---

## Installation

### Prerequisites
- PHP 8.0 or higher
- MySQL 8.0 or higher
- Apache or Nginx web server (or PHP built-in server for development)
- Git

### Backend Setup

#### 1. Clone the repository
```bash
git clone https://github.com/yourusername/nashmart-mobile-shop.git
cd nashmart-mobile-shop
```

#### 2. Set up database
```bash
# Login to MySQL
mysql -u root -p

# Run these commands:
CREATE DATABASE nashmart_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'nashmart_user'@'localhost' IDENTIFIED BY 'nashmart123';
GRANT ALL PRIVILEGES ON nashmart_db.* TO 'nashmart_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Import database schema and sample data
mysql -u nashmart_user -p nashmart_db < database/nashmart_database.sql
```

#### 3. Configure database connection

Edit `backend_php/config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'nashmart_db');
define('DB_USER', 'nashmart_user');
define('DB_PASS', 'nashmart123');
```

#### 4. Create uploads directory
```bash
cd backend_php
mkdir -p uploads
chmod 777 uploads
```

#### 5. Start the server

**Option A: PHP Built-in Server (Development)**
```bash
cd backend_php
php -S localhost:8001
```

# Set permissions
sudo chown -R www-data:www-data /var/www/html/nashmart/
sudo chmod -R 755 /var/www/html/nashmart/
sudo chmod -R 777 /var/www/html/nashmart/uploads

# Enable Apache modules
sudo a2enmod rewrite
sudo a2enmod headers
sudo systemctl restart apache2
```

#### 6. Verify installation
Open in browser: `http://localhost:8001/test.html` (or your Apache URL)

---

## API Documentation

### Base URL
```
Development: http://localhost:8001/api
Production:  http://your-domain.com/nashmart/api
```

### Response Format

All endpoints return JSON in this format:

**Success Response:**
```json
{
  "success": true,
  "message": "Operation completed successfully",
  "data": { /* ... */ }
}
```

**Error Response:**
```json
{
  "success": false,
  "message": "Error description",
  "data": null
}
```

---

## Complete API Endpoints Reference

### Products API

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/api/get_products.php` | Get all products | No |
| GET | `/api/get_products.php?id={id}` | Get single product by ID | No |
| POST | `/api/create_product.php` | Create new product | No |
| PUT | `/api/update_products.php?id={id}` | Update existing product | No |
| DELETE | `/api/delete_product.php?id={id}` | Delete product | No |

### Sales API

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/api/get_sales.php` | Get all sales records | No |
| POST | `/api/create_sale.php` | Record new sale (auto-reduces stock) | No |

### Dashboard API

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/api/dashboard.php` | Get complete statistics | No |

---

## Detailed Endpoint Documentation

### 1. Get All Products

**Endpoint:** `GET /api/get_products.php`

**Description:** Retrieves a list of all products in the inventory.

**Response Example:**
```json
{
  "success": true,
  "message": "Products retrieved successfully",
  "data": [
    {
      "id": 1,
      "product_name": "iPhone 14 Pro",
      "description": "Latest Apple smartphone",
      "price": 999.99,
      "quantity_in_stock": 15,
      "product_image": "abc123_1234567890.jpg",
      "date_uploaded": "2024-03-20 10:30:00"
    }
  ]
}
```

---

### 2. Get Single Product

**Endpoint:** `GET /api/get_products.php?id={id}`

**Parameters:**
- `id` (query parameter, integer, required): Product ID

**Response Example:**
```json
{
  "success": true,
  "message": "Product available and retrieved successfully",
  "data": {
    "id": 1,
    "product_name": "iPhone 14 Pro",
    "description": "Latest Apple smartphone",
    "price": 999.99,
    "quantity_in_stock": 15,
    "product_image": "abc123_1234567890.jpg",
    "date_uploaded": "2024-03-20 10:30:00"
  }
}
```

**Error Response (404):**
```json
{
  "success": false,
  "message": "Product not found. Askies mate",
  "data": null
}
```

---

### 3. Create Product

**Endpoint:** `POST /api/create_product.php`

**Content-Type:** `multipart/form-data` (for file upload)

**Parameters:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| product_name | string | Yes | Product name |
| price | decimal | Yes | Product price (must be > 0) |
| quantity_in_stock | integer | Yes | Stock quantity (must be >= 0) |
| description | string | No | Product description |
| product_image | file | No | Product image (JPG, PNG, GIF, max 5MB) |

**Response Example (201):**
```json
{
  "success": true,
  "message": "Product created successfully",
  "data": {
    "id": 16,
    "product_name": "Samsung Galaxy S23",
    "description": "Latest Samsung flagship",
    "price": 899.99,
    "quantity_in_stock": 20,
    "product_image": "def456_1234567890.jpg",
    "date_uploaded": "2024-03-26 15:30:00"
  }
}
```

**Validation Errors (400):**
```json
{
  "success": false,
  "message": "Missing required fields: product_name, price",
  "data": null
}
```

---

### 4. Update Product

**Endpoint:** `PUT /api/update_products.php?id={id}`

**Content-Type:** `application/json`

**Parameters:**
- `id` (query parameter, integer, required): Product ID

**Request Body:**
```json
{
  "product_name": "iPhone 14 Pro Max",
  "description": "Updated description",
  "price": 1099.99,
  "quantity_in_stock": 25
}
```

**Note:** All fields are optional. Only provided fields will be updated.

**Response Example:**
```json
{
  "success": true,
  "message": "Product updated successfully",
  "data": {
    "id": 1,
    "product_name": "iPhone 14 Pro Max",
    "description": "Updated description",
    "price": 1099.99,
    "quantity_in_stock": 25,
    "product_image": "abc123_1234567890.jpg",
    "date_uploaded": "2024-03-20 10:30:00"
  }
}
```

---

### 5. Delete Product

**Endpoint:** `DELETE /api/delete_product.php?id={id}`

**Parameters:**
- `id` (query parameter, integer, required): Product ID

**Response Example:**
```json
{
  "success": true,
  "message": "Product deleted successfully",
  "data": null
}
```

**Error (Product has sales):**
```json
{
  "success": false,
  "message": "Cannot delete product with existing sales records",
  "data": null
}
```

---

### 6. Get All Sales

**Endpoint:** `GET /api/get_sales.php`

**Description:** Retrieves all sales records with product details (joined from products table).

**Response Example:**
```json
{
  "success": true,
  "message": "Sales retrieved successfully",
  "data": [
    {
      "id": 1,
      "product_id": 1,
      "product_name": "iPhone 14 Pro",
      "quantity_sold": 2,
      "total_price": 1999.98,
      "price": 999.99,
      "date_of_sale": "2024-03-25 14:30:00"
    }
  ]
}
```

---

### 7. Record Sale

**Endpoint:** `POST /api/create_sale.php`

**Content-Type:** `application/json`

**Description:** Records a new sale and automatically reduces product stock using database transactions.

**Request Body:**
```json
{
  "product_id": 1,
  "quantity_sold": 2
}
```

**Parameters:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| product_id | integer | Yes | Product ID |
| quantity_sold | integer | Yes | Quantity to sell (must be > 0) |

**Response Example (201):**
```json
{
  "success": true,
  "message": "Sale recorded successfully",
  "data": {
    "id": 11,
    "product_id": 1,
    "product_name": "iPhone 14 Pro",
    "quantity_sold": 2,
    "total_price": 1999.98,
    "price": 999.99,
    "date_of_sale": "2024-03-26 15:45:00",
    "new_stock_level": 13
  }
}
```

**Error (Insufficient Stock):**
```json
{
  "success": false,
  "message": "Insufficient stock. Available: 5",
  "data": null
}
```

**Note:** This endpoint uses database transactions with row-level locking to prevent race conditions.

---

### 8. Get Dashboard Statistics

**Endpoint:** `GET /api/dashboard.php`

**Description:** Returns comprehensive dashboard statistics including summary, low stock alerts, top products, and recent sales.

**Response Example:**
```json
{
  "success": true,
  "message": "Dashboard data retrieved successfully",
  "data": {
    "summary": {
      "total_products": 15,
      "total_stock_value": 45678.50,
      "total_sales_amount": 12345.67,
      "total_sales_count": 48
    },
    "low_stock_products": [
      {
        "id": 12,
        "product_name": "Phone Case",
        "quantity_in_stock": 3
      }
    ],
    "top_products": [
      {
        "id": 1,
        "product_name": "iPhone 14 Pro",
        "total_sold": 25,
        "total_revenue": 24999.75
      }
    ],
    "recent_sales": [
      {
        "id": 48,
        "product_id": 1,
        "product_name": "iPhone 14 Pro",
        "quantity_sold": 1,
        "total_price": 999.99,
        "date_of_sale": "2024-03-26 15:45:00"
      }
    ]
  }
}
```

---

## Testing the API

### Method 1: Browser-Based Test Interface (Easiest)

Open the included test page:
```
http://localhost:8001/test.html
```

This provides a visual interface with buttons to test all endpoints.

---

### Method 2: cURL Commands

#### Get All Products
```bash
curl http://localhost:8001/api/get_products.php
```

#### Get Single Product
```bash
curl http://localhost:8001/api/get_products.php?id=1
```

#### Create Product (without image)
```bash
curl -X POST http://localhost:8001/api/create_product.php \
  -F "product_name=Test Product" \
  -F "description=Test description" \
  -F "price=99.99" \
  -F "quantity_in_stock=50"
```

#### Create Product (with image)
```bash
curl -X POST http://localhost:8001/api/create_product.php \
  -F "product_name=Product with Image" \
  -F "description=Has an image" \
  -F "price=149.99" \
  -F "quantity_in_stock=25" \
  -F "product_image=@/path/to/image.jpg"
```

#### Update Product
```bash
curl -X PUT http://localhost:8001/api/update_products.php?id=1 \
  -H "Content-Type: application/json" \
  -d '{
    "product_name": "Updated Product Name",
    "price": 199.99,
    "quantity_in_stock": 30
  }'
```

#### Delete Product
```bash
curl -X DELETE http://localhost:8001/api/delete_product.php?id=5
```

#### Get All Sales
```bash
curl http://localhost:8001/api/get_sales.php
```

#### Record Sale
```bash
curl -X POST http://localhost:8001/api/create_sale.php \
  -H "Content-Type: application/json" \
  -d '{
    "product_id": 1,
    "quantity_sold": 2
  }'
```

#### Get Dashboard
```bash
curl http://localhost:8001/api/dashboard.php
```

#### Pretty Print JSON (with jq)
```bash
curl http://localhost:8001/api/get_products.php | jq
```

#### Pretty Print JSON (with Python)
```bash
curl http://localhost:8001/api/get_products.php | python3 -m json.tool
```

---

### Method 3: Postman

#### Setup

1. **Import Collection:**
   - Open Postman
   - Click "Import" → "Raw Text"
   - Paste the Postman collection JSON (see below)

2. **Set Base URL Variable:**
   - Go to Collection → Variables
   - Set `base_url` to `http://localhost:8001/api`

#### Postman Collection JSON

```json
{
  "info": {
    "name": "NashMart API",
    "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
  },
  "variable": [
    {
      "key": "base_url",
      "value": "http://localhost:8001/api"
    }
  ],
  "item": [
    {
      "name": "Products",
      "item": [
        {
          "name": "Get All Products",
          "request": {
            "method": "GET",
            "header": [],
            "url": {
              "raw": "{{base_url}}/get_products.php",
              "host": ["{{base_url}}"],
              "path": ["get_products.php"]
            }
          }
        },
        {
          "name": "Get Single Product",
          "request": {
            "method": "GET",
            "header": [],
            "url": {
              "raw": "{{base_url}}/get_products.php?id=1",
              "host": ["{{base_url}}"],
              "path": ["get_products.php"],
              "query": [{"key": "id", "value": "1"}]
            }
          }
        },
        {
          "name": "Create Product",
          "request": {
            "method": "POST",
            "header": [],
            "body": {
              "mode": "formdata",
              "formdata": [
                {"key": "product_name", "value": "Test Product", "type": "text"},
                {"key": "description", "value": "Test description", "type": "text"},
                {"key": "price", "value": "99.99", "type": "text"},
                {"key": "quantity_in_stock", "value": "50", "type": "text"},
                {"key": "product_image", "type": "file", "src": ""}
              ]
            },
            "url": {
              "raw": "{{base_url}}/create_product.php",
              "host": ["{{base_url}}"],
              "path": ["create_product.php"]
            }
          }
        },
        {
          "name": "Update Product",
          "request": {
            "method": "PUT",
            "header": [{"key": "Content-Type", "value": "application/json"}],
            "body": {
              "mode": "raw",
              "raw": "{\n  \"product_name\": \"Updated Product\",\n  \"price\": 149.99,\n  \"quantity_in_stock\": 75\n}"
            },
            "url": {
              "raw": "{{base_url}}/update_products.php?id=1",
              "host": ["{{base_url}}"],
              "path": ["update_products.php"],
              "query": [{"key": "id", "value": "1"}]
            }
          }
        },
        {
          "name": "Delete Product",
          "request": {
            "method": "DELETE",
            "header": [],
            "url": {
              "raw": "{{base_url}}/delete_product.php?id=5",
              "host": ["{{base_url}}"],
              "path": ["delete_product.php"],
              "query": [{"key": "id", "value": "5"}]
            }
          }
        }
      ]
    },
    {
      "name": "Sales",
      "item": [
        {
          "name": "Get All Sales",
          "request": {
            "method": "GET",
            "header": [],
            "url": {
              "raw": "{{base_url}}/get_sales.php",
              "host": ["{{base_url}}"],
              "path": ["get_sales.php"]
            }
          }
        },
        {
          "name": "Record Sale",
          "request": {
            "method": "POST",
            "header": [{"key": "Content-Type", "value": "application/json"}],
            "body": {
              "mode": "raw",
              "raw": "{\n  \"product_id\": 1,\n  \"quantity_sold\": 2\n}"
            },
            "url": {
              "raw": "{{base_url}}/create_sale.php",
              "host": ["{{base_url}}"],
              "path": ["create_sale.php"]
            }
          }
        }
      ]
    },
    {
      "name": "Dashboard",
      "item": [
        {
          "name": "Get Dashboard Stats",
          "request": {
            "method": "GET",
            "header": [],
            "url": {
              "raw": "{{base_url}}/dashboard.php",
              "host": ["{{base_url}}"],
              "path": ["dashboard.php"]
            }
          }
        }
      ]
    }
  ]
}
```

---

## Project Structure

```
nashmart-mobile-shop/
├── backend_php/
│   ├── api/                    # API endpoints (routes)
│   │   ├── get_products.php
│   │   ├── create_product.php
│   │   ├── update_products.php
│   │   ├── delete_product.php
│   │   ├── get_sales.php
│   │   ├── create_sale.php
│   │   └── dashboard.php
│   ├── config/                 # Configuration files
│   │   ├── database.php        # Database connection
│   │   └── helpers.php         # Helper functions
│   ├── controllers/            # Business logic
│   │   ├── ProductController.php
│   │   ├── SalesController.php
│   │   └── DashboardController.php
│   ├── models/                 # Data layer
│   │   ├── product.php
│   │   └── sale.php
│   ├── uploads/                # Product images
│   ├── .htaccess              # Apache configuration
│   ├── index.php              # API documentation page
│   └── test.html              # Interactive test interface
├── database/
│   └── nashmart_database.sql  # Database schema & sample data
├── docs/
│   ├── API_DOCUMENTATION.md
│   ├── TESTING_GUIDE.md
│   └── BUG_FIXES.md
├── flutter_app/               # Mobile app (if included)
├── .gitignore
└── README.md
```

---

## Security Features

### SQL Injection Prevention
- PDO prepared statements for all queries
- Input sanitization (htmlspecialchars, stripslashes, trim)
- Type casting (intval, floatval)
- Parameter binding

### Transaction Management
- ACID compliance for sales operations
- Row-level locking (SELECT FOR UPDATE)
- Automatic rollback on errors
- Prevents race conditions on stock updates

### File Upload Security
- File type validation (MIME type checking)
- File size limits (5MB maximum)
- Unique filename generation (prevents overwrites)
- Extension validation
- No execution permissions on upload directory

### Input Validation
- Required field validation
- Data type validation
- Range validation (price > 0, quantity >= 0)
- Comprehensive error messages

### CORS Headers
- Configurable CORS policy
- Preflight request handling
- Custom headers support

---

## Troubleshooting

### Issue: "Connection refused" or "Server not running"

**Solution:**
```bash
# Check if server is running
ps aux | grep php

# Start server
cd backend_php
php -S localhost:8001
```

---

### Issue: "Database connection failed"

**Solutions:**
1. Verify MySQL is running: `sudo systemctl status mysql`
2. Test credentials: `mysql -u nashmart_user -p nashmart_db`
3. Check `config/database.php` has correct credentials
4. Recreate user if needed

---

### Issue: "Cannot redeclare function"

**Solution:**
Make sure `config/database.php` doesn't have duplicate function declarations. It should only contain the database connection code.

---

### Issue: Image upload fails

**Solutions:**
1. Create uploads directory: `mkdir -p backend_php/uploads`
2. Set permissions: `chmod 777 backend_php/uploads` (dev only)
3. Check PHP upload limits: `php -i | grep upload_max_filesize`
4. Increase limits in `.htaccess` or `php.ini`

---

### Issue: CORS errors in browser

**Solution:**
Add to `.htaccess`:
```apache
Header set Access-Control-Allow-Origin "*"
Header set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"
Header set Access-Control-Allow-Headers "Content-Type, Authorization"
```

---

### Issue: "Method not allowed" (405)

**Solution:**
Verify you're using the correct HTTP method:
- GET: get_products.php, get_sales.php, dashboard.php
- POST: create_product.php, create_sale.php
- PUT: update_products.php
- DELETE: delete_product.php

---

## Performance Considerations

- Database indexes on frequently queried columns
- Optimized JOIN queries for sales retrieval
- Efficient image storage (filesystem, not database)
- Connection pooling via PDO
- Prepared statement caching

---

## Acknowledgments

- AI assistance was used in the development and debugging of this project, as disclosed per assessment requirements
- PHP community for best practices and security guidelines

---

## Support

For issues, questions, or suggestions:
- Create an issue on GitHub
- Email: sibandanakhosetive7@gmail.com
- Documentation: See `docs/` folder for detailed guides

---
**Made with ❤️ and PHP**