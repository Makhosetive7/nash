<?php

require_once __DIR__ . '/../models/Sale.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../config/helpers.php';

class SalesController {
    private $saleModel;
    private $productModel;
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->saleModel = new Sale($pdo);
        $this->productModel = new Product($pdo);
    }
    
    //get all sales
    public function index() {
        try {
            $sales = $this->saleModel->getAll();
            sendResponse(true, 'Sales retrieved successfully', $sales);
        } catch (Exception $e) {
            sendResponse(false, 'Error retrieving sales: ' . $e->getMessage(), null, 500);
        }
    }
    
    //Create new sale with automatic stock management
    public function store($data) {
        
        $required = ['product_id', 'quantity_sold'];
        $missing = validateRequired($required, $data);
        
        if (!empty($missing)) {
            sendResponse(false, 'Missing required fields: ' . implode(', ', $missing), null, 400);
        }
        
        $productId = intval($data['product_id']);
        $quantitySold = intval($data['quantity_sold']);
        
        // Validate quantity
        if ($quantitySold <= 0) {
            sendResponse(false, 'Quantity sold must be greater than 0', null, 400);
        }
        
        try {
            
            $this->pdo->beginTransaction();
            
            
            $product = $this->productModel->getForUpdate($productId);
            
            if (!$product) {
                $this->pdo->rollBack();
                sendResponse(false, 'Product not found', null, 404);
            }
            
            
            if ($product['quantity_in_stock'] < $quantitySold) {
                $this->pdo->rollBack();
                sendResponse(false, 'Insufficient stock. Available: ' . $product['quantity_in_stock'], null, 400);
            }
            
            
            $totalPrice = $product['price'] * $quantitySold;
            
            $saleData = [
                'product_id' => $productId,
                'quantity_sold' => $quantitySold,
                'total_price' => $totalPrice
            ];
            
            $saleId = $this->saleModel->create($saleData);
            
            if (!$saleId) {
                $this->pdo->rollBack();
                sendResponse(false, 'Failed to create sale record', null, 500);
            }
            
            $newStock = $product['quantity_in_stock'] - $quantitySold;
            $stockUpdated = $this->productModel->updateStock($productId, $newStock);
            
            if (!$stockUpdated) {
                $this->pdo->rollBack();
                sendResponse(false, 'Failed to update stock', null, 500);
            }
            
            $this->pdo->commit();
            
            $sale = $this->saleModel->getById($saleId);
            $sale['new_stock_level'] = $newStock;
            
            sendResponse(true, 'Sale recorded successfully', $sale, 201);
            
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            sendResponse(false, 'Error recording sale: ' . $e->getMessage(), null, 500);
        }
    }
    
    //Get recent sales
    public function getRecent($limit = 10) {
        try {
            $sales = $this->saleModel->getRecent($limit);
            sendResponse(true, 'Recent sales retrieved successfully', $sales);
        } catch (Exception $e) {
            sendResponse(false, 'Error retrieving recent sales: ' . $e->getMessage(), null, 500);
        }
    }
    
    //Get sales by product
    public function getByProduct($productId) {
        if (!$productId) {
            sendResponse(false, 'Product ID is required', null, 400);
        }
        
        try {
            $sales = $this->saleModel->getByProductId($productId);
            sendResponse(true, 'Product sales retrieved successfully', $sales);
        } catch (Exception $e) {
            sendResponse(false, 'Error retrieving product sales: ' . $e->getMessage(), null, 500);
        }
    }
}
?>