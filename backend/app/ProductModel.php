<?php
use Dotenv\Exception\ValidationException;

class ProductModel
{
    private PDO $conn;

    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }

    public function getAllForUser(int $user_id): array
    {
        $sql = "SELECT *
            FROM products
            ORDER BY name";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute();

        $data = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return $data;
    }
    public function getForUser(int $user_id, string $id): array |false
    {
        $sql = "SELECT *
                FROM products
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_STR);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);


        return $data;
    }

    public function createForUser(int $user_id, array $data): string
    {
        $errors = [];
        $error_string = implode(',', $errors);
        if (empty($data['name'])) {
            $errors['name'] = 'Name is required';
        }
        if (empty($data['price']) || !is_numeric($data['price'])) {
            $errors['price'] = 'Price is required and must be a number';
        }
        if (!empty($errors)) {
            throw new ValidationException($error_string);
        }


        $sql = "INSERT INTO products (name, description, price, is_avaible, created_at, user_id)
VALUES (:name, :description, :price, :is_avaible, CURRENT_TIMESTAMP, :user_id)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":name", $data["name"], PDO::PARAM_STR);
        $stmt->bindValue(":description", $data["description"] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(":price", $data["price"], PDO::PARAM_INT);
        $stmt->bindValue(":is_avaible", $data["is_avaible"] ?? false, PDO::PARAM_BOOL);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->execute();

        return $this->conn->lastInsertId();
    }


    public function updateForUser(int $user_id, string $id, array $data): int
    {
        $fields = [];

        if (!empty($data["name"])) {
            $fields["name"] = [$data["name"], PDO::PARAM_STR];
        }

        if (!empty($data["description"])) {
            $fields["description"] = [$data["description"], PDO::PARAM_STR];
        }

        if (!empty($data["price"])) {
            $fields["price"] = [(float) $data["price"], PDO::PARAM_STR];
        }

        if (!empty($data["is_avaible"])) {
            $fields["is_avaible"] = [$data["is_avaible"], PDO::PARAM_BOOL];
        }

        if (empty($fields)) {
            return 0;
        }

        $sql = "UPDATE products SET " . implode(", ", array_map(function ($f) {
            return "$f = :$f";
        }, array_keys($fields))) . " WHERE id = :id AND user_id = :user_id";

        $stmt = $this->conn->prepare($sql);

        foreach ($fields as $field => $data) {
            $stmt->bindValue(":$field", $data[0], $data[1]);
        }

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function deleteForUser(int $user_id, string $id): int
    {
        $sql = "DELETE FROM products WHERE id = :id AND user_id = :user_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
?>