<?php

require_once __DIR__ . "../models/product.php";
require_once __DIR__ . "../config/helpers.php";


class ProductController
{
    private $productModel;


    public function __construct($pdo)
    {
        $this->productModel = new Product($pdo);
    }


    //Get all products
    public function index()
    {
        try {
            $products = $this->productModel->getAll();
            sendResponse(true, 'Products retrieved successfully', $products);
        } catch (Exception $e) {
            sendResponse(false, 'Error retrieving products: ' . $e->getMessage(), null, 500);
        }
    }

    //Retrieve product
    public function show($id)
    {
        if (!$id) {
            sendResponse(false, "Product ID is required", null, 400);
        }

        try {
            $product = $this->productModel->getById($id);

            if ($product) {
                sendResponse(true, "Product available and retrieved successfully", $product);
            } else {
                sendResponse(false, "Product not found. Askies mate", null, 404);
            }
        } catch (Exception $e) {
            sendResponse(false, "Error retrieving product" . $e->getMessage(), null, 500);
        }
    }


    //create product
    public function store($data, $files = null)
    {
        $require = ["product_name", "price", "quantity_in_stock"];
        $missing = validateRequired($require, $data);


        //sanitize inputs
        $data["product_name"] = sanitizeInput($data["product_name"]);
        $data["price"] = floatval($data["price"]);
        $data["description"] = isset($data["description"]) ? sanitizeInput($data["description"]) : " ";
        $data["quantity_in_stock"] = intval($data["quantity_in_stock"]);


        //quantity and price validation
        if ($data["price"] <= 0) {
            sendResponse(false, "Price must be greater than Zero", null, 400);
        }

        if ($data["quantiy_in_stock"] <= 0) {
            sendResponse(false, "Quantity can not be negative", null, 500);
        }

        // Handle image upload
        if ($files && isset($files['product_image']) && $files['product_image']['error'] === UPLOAD_ERR_OK) {
            $imagePath = $this->handleImageUpload($files['product_image']);
            if ($imagePath === false) {
                sendResponse(false, 'Error uploading image', null, 500);
            }
            $data['product_image'] = $imagePath;
        }

        try {
            $productId = $this->productModel->create($data);

            if ($productId) {
                $product = $this->productModel->getById($productId);
                sendResponse(true, 'Product created successfully', $product, 201);
            } else {
                // Clean up uploaded image if database insert failed
                if (isset($data['product_image'])) {
                    $uploadPath = __DIR__ . '/../uploads/' . $data['product_image'];
                    if (file_exists($uploadPath)) {
                        unlink($uploadPath);
                    }
                }
                sendResponse(false, 'Failed to create product', null, 500);
            }
        } catch (Exception $e) {
            sendResponse(false, 'Error creating product: ' . $e->getMessage(), null, 500);
        }
    }

    //update existing product
    public function update($id, $data)
    {
        if (!$id) {
            sendResponse(false, 'Product ID is required', null, 400);
        }

        // Check if product exists
        $product = $this->productModel->getById($id);
        if (!$product) {
            sendResponse(false, 'Product not found', null, 404);
        }

        // Prepare update data (use existing values if not provided)
        $updateData = [
            'product_name' => isset($data['product_name']) ? sanitizeInput($data['product_name']) : $product['product_name'],
            'description' => isset($data['description']) ? sanitizeInput($data['description']) : $product['description'],
            'price' => isset($data['price']) ? floatval($data['price']) : $product['price'],
            'quantity_in_stock' => isset($data['quantity_in_stock']) ? intval($data['quantity_in_stock']) : $product['quantity_in_stock']
        ];

        // Validate price and quantity
        if ($updateData['price'] <= 0) {
            sendResponse(false, 'Price must be greater than 0', null, 400);
        }

        if ($updateData['quantity_in_stock'] < 0) {
            sendResponse(false, 'Quantity cannot be negative', null, 400);
        }

        try {
            $result = $this->productModel->update($id, $updateData);

            if ($result) {
                $updatedProduct = $this->productModel->getById($id);
                sendResponse(true, 'Product updated successfully', $updatedProduct);
            } else {
                sendResponse(false, 'Failed to update product', null, 500);
            }
        } catch (Exception $e) {
            sendResponse(false, 'Error updating product: ' . $e->getMessage(), null, 500);
        }
    }
    public function destroy($id)
    {
        if (!$id) {
            sendResponse(false, 'Product ID is required', null, 400);
        }

        // Check if product exists
        $product = $this->productModel->getById($id);
        if (!$product) {
            sendResponse(false, 'Product not found', null, 404);
        }

        // Check if product has sales records
        if ($this->productModel->hasSales($id)) {
            sendResponse(false, 'Cannot delete product with existing sales records', null, 400);
        }

        try {
            $result = $this->productModel->delete($id);

            if ($result) {
                // Delete product image if exists
                if ($product['product_image']) {
                    $imagePath = __DIR__ . '/../uploads/' . $product['product_image'];
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
                sendResponse(true, 'Product deleted successfully');
            } else {
                sendResponse(false, 'Failed to delete product', null, 500);
            }
        } catch (Exception $e) {
            sendResponse(false, 'Error deleting product: ' . $e->getMessage(), null, 500);
        }
    }

    private function handleImageUpload($file)
    {
        $uploadDir = __DIR__ . '/../uploads/';

        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            return false;
        }

        // Validate file size (max 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            return false;
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $targetPath = $uploadDir . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return $filename;
        }

        return false;
    }
}
