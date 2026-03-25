<?php


class Product
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }


    public function getAll()
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM products
            ORDER BY date_uploaded DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM products
            WHERE id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO products (product_name, description, price, quantity_in_stock, product_image, date_uploaded)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");

        $result = $stmt->execute([
            $data['product_name'],
            $data['description'] ?? '',
            $data['price'],
            $data['quantity_in_stock'],
            $data['product_image'] ?? null
        ]);

        return $result ? $this->pdo->lastInsertId() : false;
    }

    public function update($id, $data)
    {
        $stmt = $this->pdo->prepare("
            UPDATE products
            SET product_name = ?,
                description = ?,
                price = ?,
                quantity_in_stock = ?
            WHERE id = ?
        ");

        return $stmt->execute([
            $data['product_name'],
            $data['description'],
            $data['price'],
            $data['quantity_in_stock'],
            $id
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function updateStock($id, $quantity)
    {
        $stmt = $this->pdo->prepare("
            UPDATE products
            SET quantity_in_stock = ?
            WHERE id = ?
        ");
        return $stmt->execute([$quantity, $id]);
    }

    public function hasSales($id)
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as count
    FROM sales
            WHERE product_id = ?
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    public function getLowStock($threshold = 10)
    {
        $stmt = $this->pdo->prepare("
            SELECT id, product_name, quantity_in_stock
            FROM products
            WHERE quantity_in_stock <= ?
            ORDER BY quantity_in_stock ASC
        ");
        $stmt->execute([$threshold]);
        return $stmt->fetchAll();
    }

    public function getForUpdate($id)
    {
        $stmt = $this->pdo->prepare("
            SELECT id, product_name, price, quantity_in_stock
            FROM products
            WHERE id = ?
            FOR UPDATE
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getTotalStockValue()
    {
        $stmt = $this->pdo->query("
            SELECT SUM(price * quantity_in_stock) as total
            FROM products
        ");
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    public function getCount()
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM products");
        $result = $stmt->fetch();
        return $result['total'];
    }
}
