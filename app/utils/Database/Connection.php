<?php

namespace App\Utils\Database;

use PDO;
use PDOException;

class Connection
{
    protected PDO $conn;

    public function connect(string $servername, string $username, string $password, string $database = 'baseApi'): bool
    {
        try {
            $connectString = sprintf("mysql:host=%s;dbname=%s", $servername, $database);
            $this->conn = new PDO($connectString, $username, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return true;
        } catch (PDOException $e) {
            var_dump($e);
            return false;
        }
    }

    public function getConnection(): PDO
    {
        return $this->conn;
    }
}
