<?php

class UserModel
{
    private PDO $conn;

    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }

    public function getByIdentifier(string $identifier, string $identifierType = 'id'): array |false
    {
        $sql = "SELECT * FROM user WHERE $identifierType = :identifier";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(':identifier', $identifier, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}