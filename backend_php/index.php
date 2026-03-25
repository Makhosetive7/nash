<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NashMart API - MVC Architecture</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        
        .badge {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9em;
            margin-top: 10px;
        }
        
        .content {
            padding: 40px;
        }
        
        .section {
            margin-bottom: 40px;
        }
        
        .section h2 {
            color: #667eea;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .endpoint {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        
        .method {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.85em;
            margin-right: 10px;
        }
        
        .get { background: #28a745; color: white; }
        .post { background: #007bff; color: white; }
        .put { background: #ffc107; color: black; }
        .delete { background: #dc3545; color: white; }
        
        .path {
            font-family: 'Courier New', monospace;
            color: #495057;
            font-size: 0.95em;
        }
        
        .description {
            margin-top: 10px;
            color: #6c757d;
            font-size: 0.9em;
        }
        
        .architecture {
            background: #e7f3ff;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .architecture pre {
            background: white;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            font-size: 0.85em;
            line-height: 1.6;
        }
        
        .test-link {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 20px;
            transition: transform 0.2s;
        }
        
        .test-link:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>NashMart API</h1>
            <p>RESTful API with MVC Architecture</p>
            <span class="badge">PHP 8.x • MySQL • PDO • MVC Pattern</span>
        </div>
        
        <div class="content">
            <!-- Architecture Overview -->
            <div class="section">
                <h2>Architecture Overview</h2>
                <div class="architecture">
                    <p><strong>Model-View-Controller (MVC) Pattern</strong></p>
                    <pre>
nashmart/
├── models/              # Data layer - Database operations
│   ├── Product.php
│   └── Sale.php
├── controllers/         # Business logic layer
│   ├── ProductController.php
│   ├── SalesController.php
│   └── DashboardController.php
├── api/                 # API endpoints - Routes
│   ├── get_products.php
│   ├── create_product.php
│   ├── update_product.php
│   ├── delete_product.php
│   ├── get_sales.php
│   ├── create_sale.php
│   └── dashboard.php
├── config/              # Configuration
│   ├── database.php
│   └── helpers.php
└── uploads/             # Product images
                    </pre>
                </div>
            </div>
            
            <!-- Products API -->
            <div class="section">
                <h2> Products API</h2>
                
                <div class="endpoint">
                    <span class="method get">GET</span>
                    <span class="path">/api/get_products.php</span>
                    <div class="description">Get all products</div>
                </div>
                
                <div class="endpoint">
                    <span class="method get">GET</span>
                    <span class="path">/api/get_products.php?id={id}</span>
                    <div class="description">Get single product by ID</div>
                </div>
                
                <div class="endpoint">
                    <span class="method post">POST</span>
                    <span class="path">/api/create_product.php</span>
                    <div class="description">
                        Create new product<br>
                        <small>Parameters: product_name, price, quantity_in_stock, description (optional), product_image (optional)</small>
                    </div>
                </div>
                
                <div class="endpoint">
                    <span class="method put">PUT</span>
                    <span class="path">/api/update_product.php?id={id}</span>
                    <div class="description">
                        Update existing product<br>
                        <small>Body: JSON with fields to update</small>
                    </div>
                </div>
                
                <div class="endpoint">
                    <span class="method delete">DELETE</span>
                    <span class="path">/api/delete_product.php?id={id}</span>
                    <div class="description">Delete product</div>
                </div>
            </div>
            
            <!-- Sales API -->
            <div class="section">
                <h2> Sales API</h2>
                
                <div class="endpoint">
                    <span class="method get">GET</span>
                    <span class="path">/api/get_sales.php</span>
                    <div class="description">Get all sales with product details</div>
                </div>
                
                <div class="endpoint">
                    <span class="method post">POST</span>
                    <span class="path">/api/create_sale.php</span>
                    <div class="description">
                        Record new sale (automatic stock management)<br>
                        <small>Body: { "product_id": 1, "quantity_sold": 2 }</small>
                    </div>
                </div>
            </div>
            
            <!-- Dashboard API -->
            <div class="section">
                <h2> Dashboard API</h2>
                
                <div class="endpoint">
                    <span class="method get">GET</span>
                    <span class="path">/api/dashboard.php</span>
                    <div class="description">Get dashboard statistics (summary, low stock, top products, recent sales)</div>
                </div>
            </div>
            
            <!-- Features -->
            <div class="section">
                <h2> Features</h2>
                <ul style="list-style: none; padding: 0;">
                    <li style="padding: 8px 0;"> <strong>MVC Architecture</strong> - Separation of concerns</li>
                    <li style="padding: 8px 0;"> <strong>PDO Prepared Statements</strong> - SQL injection prevention</li>
                    <li style="padding: 8px 0;"> <strong>Database Transactions</strong> - Data integrity for sales</li>
                    <li style="padding: 8px 0;"> <strong>Input Validation</strong> - Secure data handling</li>
                    <li style="padding: 8px 0;"> <strong>File Upload Security</strong> - Image validation & unique naming</li>
                    <li style="padding: 8px 0;"> <strong>RESTful Design</strong> - Standard HTTP methods</li>
                    <li style="padding: 8px 0;"> <strong>JSON Responses</strong> - Consistent API format</li>
                </ul>
            </div>
            
            <center>
                <a href="test.html" class="test-link">🧪 Test API Endpoints</a>
            </center>
        </div>
    </div>
</body>
</html>