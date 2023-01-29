<?php
class RefreshTokenModel
{
    private $conn;
    private $key;

    public function __construct(Database $database, string $key)
    {
        $this->conn = $database->getConnection();
        $this->key = $key;
    }

    public function create(string $token, int $expiry, int $user_id): bool
    {
        $check_user_id = "SELECT * FROM user WHERE id = :user_id";
        $stmt = $this->conn->prepare($check_user_id);
        $stmt->bindValue(':user_id', $user_id);
        $stmt->execute();
        if ($stmt->rowCount() == 0) {
            return false;
        }
        $hash = hash_hmac("sha256", $token, $this->key);

        $sql = "INSERT INTO refresh_token (token_hash, expires_at, user_id)
                VALUES (:token_hash, :expires_at, :user_id)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":token_hash", $hash);
        $stmt->bindValue(":expires_at", $expiry);
        $stmt->bindValue(":user_id", $user_id);

        return $stmt->execute();
    }

    public function delete(string $token): int
    {
        $hash = hash_hmac("sha256", $token, $this->key);

        $sql = "DELETE FROM refresh_token
                WHERE token_hash = :token_hash";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":token_hash", $hash);

        $stmt->execute();

        return $stmt->rowCount();
    }

    public function getByToken(string $token): array |false
    {
        $hash = hash_hmac("sha256", $token, $this->key);

        $sql = "SELECT *
                FROM refresh_token
                JOIN user ON refresh_token.user_id = user.id
                WHERE refresh_token.token_hash = :token_hash";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":token_hash", $hash);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteExpired(): int
    {
        $sql = "DELETE FROM refresh_token
                WHERE expires_at < UNIX_TIMESTAMP()";

        $stmt = $this->conn->query($sql);

        return $stmt->rowCount();
    }
}
?>