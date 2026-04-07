<?php

require_once __DIR__ . '/../models/product.php';
require_once __DIR__ . '/../models/sale.php';
require_once __DIR__ . '/../config/helpers.php';

class DashboardController
{
    private $productModel;
    private $saleModel;

    public function __construct($pdo)
    {
        $this->productModel = new Product($pdo);
        $this->saleModel = new Sale($pdo);
    }

    //Get dashboard statistics
    public function index()
    {
        try {
            $totalProducts = $this->productModel->getCount();
            $totalStockValue = $this->productModel->getTotalStockValue();
            $totalSalesAmount = $this->saleModel->getTotalAmount();
            $totalSalesCount = $this->saleModel->getCount();
            $lowStockProducts = $this->productModel->getLowStock(10);
            $topProducts = $this->saleModel->getTopProducts(5);
            $recentSales = $this->saleModel->getRecent(10);

            $dashboard = [
                'summary' => [
                    'total_products' => intval($totalProducts),
                    'total_stock_value' => floatval($totalStockValue),
                    'total_sales_amount' => floatval($totalSalesAmount),
                    'total_sales_count' => intval($totalSalesCount)
                ],
                'low_stock_products' => $lowStockProducts,
                'top_products' => $topProducts,
                'recent_sales' => $recentSales
            ];

            sendResponse(true, 'Dashboard data retrieved successfully', $dashboard);
        } catch (Exception $e) {
            sendResponse(false, 'Error retrieving dashboard data: ' . $e->getMessage(), null, 500);
        }
    }

    //Get summary
    public function getSummary()
    {
        try {
            $summary = [
                'total_products' => intval($this->productModel->getCount()),
                'total_stock_value' => floatval($this->productModel->getTotalStockValue()),
                'total_sales_amount' => floatval($this->saleModel->getTotalAmount()),
                'total_sales_count' => intval($this->saleModel->getCount())
            ];

            sendResponse(true, 'Summary retrieved successfully', $summary);
        } catch (Exception $e) {
            sendResponse(false, 'Error retrieving summary: ' . $e->getMessage(), null, 500);
        }
    }

    // Get low stock alerts
    public function getLowStockAlerts($threshold = 10)
    {
        try {
            $lowStock = $this->productModel->getLowStock($threshold);
            sendResponse(true, 'Low stock alerts retrieved successfully', $lowStock);
        } catch (Exception $e) {
            sendResponse(false, 'Error retrieving low stock alerts: ' . $e->getMessage(), null, 500);
        }
    }

    //Get top selling products
    public function getTopProducts($limit = 5)
    {
        try {
            $topProducts = $this->saleModel->getTopProducts($limit);
            sendResponse(true, 'Top products retrieved successfully', $topProducts);
        } catch (Exception $e) {
            sendResponse(false, 'Error retrieving top products: ' . $e->getMessage(), null, 500);
        }
    }
}
