<?php

class Sale
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll()
    {
        $stmt = $this->pdo->prepare("
            SELECT s.*, p.product_name, p.price
            FROM sales s
            INNER JOIN products p ON s.product_id = p.id
            ORDER BY s.date_of_sale DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $stmt = $this->pdo->prepare("
            SELECT s.*, p.product_name, p.price
            FROM sales s
            INNER JOIN products p ON s.product_id = p.id
            WHERE s.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO sales (product_id, quantity_sold, total_price, date_of_sale)
            VALUES (?, ?, ?, NOW())
        ");

        $result = $stmt->execute([
            $data['product_id'],
            $data['quantity_sold'],
            $data['total_price']
        ]);

        return $result ? $this->pdo->lastInsertId() : false;
    }

    public function getRecent($limit = 10)
    {
        $stmt = $this->pdo->prepare("
            SELECT s.*, p.product_name
            FROM sales s
            INNER JOIN products p ON s.product_id = p.id
            ORDER BY s.date_of_sale DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function getTotalAmount()
    {
        $stmt = $this->pdo->query("
            SELECT SUM(total_price) as total
            FROM sales
        ");
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    public function getCount()
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM sales");
        $result = $stmt->fetch();
        return $result['total'];
    }

    public function getTopProducts($limit = 5)
    {
        $stmt = $this->pdo->prepare("
            SELECT
                p.id,
                p.product_name,
                SUM(s.quantity_sold) as total_sold,
                SUM(s.total_price) as total_revenue
            FROM sales s
            INNER JOIN products p ON s.product_id = p.id
            GROUP BY p.id, p.product_name
            ORDER BY total_sold DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function getByProductId($productId)
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM sales
            WHERE product_id = ?
            ORDER BY date_of_sale DESC
        ");
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    public function getByDateRange($startDate, $endDate)
    {
        $stmt = $this->pdo->prepare("
            SELECT s.*, p.product_name
            FROM sales s
            INNER JOIN products p ON s.product_id = p.id
            WHERE s.date_of_sale BETWEEN ? AND ?
            ORDER BY s.date_of_sale DESC
        ");
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetchAll();
    }
}
