<?php

require_once __DIR__ . '/../models/Sale.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../config/helpers.php';

class SalesController
{
    private $saleModel;
    private $productModel;
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->saleModel = new Sale($pdo);
        $this->productModel = new Product($pdo);
    }

    // Get all sales
    public function index()
    {
        try {
            $sales = $this->saleModel->getAll();
            sendResponse(true, 'Sales retrieved successfully', $sales);
        } catch (Exception $e) {
            sendResponse(false, 'Error retrieving sales: ' . $e->getMessage(), null, 500);
        }
    }

    // Create new sale with automatic stock management
    public function store($data)
    {

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

    // Update existing sale with stock adjustment
    public function update($id, $data)
    {
        if (!$id) {
            sendResponse(false, 'Sale ID is required', null, 400);
        }

        // Check if sale exists
        $existingSale = $this->saleModel->getById($id);
        if (!$existingSale) {
            sendResponse(false, 'Sale not found', null, 404);
        }

        // Validate new quantity if provided
        $newQuantity = isset($data['quantity_sold']) ? intval($data['quantity_sold']) : $existingSale['quantity_sold'];

        if ($newQuantity <= 0) {
            sendResponse(false, 'Quantity sold must be greater than 0', null, 400);
        }

        try {
            $this->pdo->beginTransaction();

            // Get product with lock
            $product = $this->productModel->getForUpdate($existingSale['product_id']);

            if (!$product) {
                $this->pdo->rollBack();
                sendResponse(false, 'Product not found', null, 404);
            }

            // Calculate stock adjustment
            $oldQuantity = $existingSale['quantity_sold'];
            $quantityDifference = $newQuantity - $oldQuantity;

            // Check if we have enough stock for the adjustment
            if ($quantityDifference > 0) {
                // Increasing sale quantity - need more stock
                if ($product['quantity_in_stock'] < $quantityDifference) {
                    $this->pdo->rollBack();
                    sendResponse(false, 'Insufficient stock for this update. Available: ' . $product['quantity_in_stock'], null, 400);
                }
            }

            // Calculate new total price
            $newTotalPrice = $product['price'] * $newQuantity;

            // Update sale record
            $updateData = [
                'quantity_sold' => $newQuantity,
                'total_price' => $newTotalPrice
            ];

            $saleUpdated = $this->saleModel->update($id, $updateData);

            if (!$saleUpdated) {
                $this->pdo->rollBack();
                sendResponse(false, 'Failed to update sale', null, 500);
            }

            // Adjust stock
            $newStockLevel = $product['quantity_in_stock'] - $quantityDifference;
            $stockUpdated = $this->productModel->updateStock($existingSale['product_id'], $newStockLevel);

            if (!$stockUpdated) {
                $this->pdo->rollBack();
                sendResponse(false, 'Failed to update stock', null, 500);
            }

            $this->pdo->commit();

            // Get updated sale
            $updatedSale = $this->saleModel->getById($id);
            $updatedSale['stock_adjusted'] = $quantityDifference;
            $updatedSale['new_stock_level'] = $newStockLevel;

            sendResponse(true, 'Sale updated successfully', $updatedSale);
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            sendResponse(false, 'Error updating sale: ' . $e->getMessage(), null, 500);
        }
    }

    // Delete sale and restore stock
    public function destroy($id)
    {
        if (!$id) {
            sendResponse(false, 'Sale ID is required', null, 400);
        }

        // Check if sale exists
        $sale = $this->saleModel->getById($id);
        if (!$sale) {
            sendResponse(false, 'Sale not found', null, 404);
        }

        try {
            $this->pdo->beginTransaction();

            // Get product with lock
            $product = $this->productModel->getForUpdate($sale['product_id']);

            if (!$product) {
                $this->pdo->rollBack();
                sendResponse(false, 'Product not found', null, 404);
            }

            // Delete sale record
            $saleDeleted = $this->saleModel->delete($id);

            if (!$saleDeleted) {
                $this->pdo->rollBack();
                sendResponse(false, 'Failed to delete sale', null, 500);
            }

            // Restore stock
            $newStockLevel = $product['quantity_in_stock'] + $sale['quantity_sold'];
            $stockUpdated = $this->productModel->updateStock($sale['product_id'], $newStockLevel);

            if (!$stockUpdated) {
                $this->pdo->rollBack();
                sendResponse(false, 'Failed to restore stock', null, 500);
            }

            $this->pdo->commit();

            sendResponse(true, 'Sale deleted successfully. Stock restored: ' . $sale['quantity_sold'] . ' units', [
                'deleted_sale_id' => $id,
                'product_id' => $sale['product_id'],
                'product_name' => $sale['product_name'],
                'quantity_restored' => $sale['quantity_sold'],
                'new_stock_level' => $newStockLevel
            ]);
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            sendResponse(false, 'Error deleting sale: ' . $e->getMessage(), null, 500);
        }
    }

    // Get recent sales
    public function getRecent($limit = 10)
    {
        try {
            $sales = $this->saleModel->getRecent($limit);
            sendResponse(true, 'Recent sales retrieved successfully', $sales);
        } catch (Exception $e) {
            sendResponse(false, 'Error retrieving recent sales: ' . $e->getMessage(), null, 500);
        }
    }

    // Get sales by product
    public function getByProduct($productId)
    {
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
