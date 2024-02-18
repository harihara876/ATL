<?php

namespace Plat4m\App;

use PDO;
use PDOException;

class DB
{
    // DB connection object.
    private $conn;

    /**
     * Creates DB connection.
     * @return object PDO object.
     */
    private function createConnection($host, $port, $user, $password, $dbName)
    {
        $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        try {
            return (new PDO($dsn, $user, $password, $options));
        } catch (PDOException $ex) {
            throw new PDOException($ex->getMessage(), 500);
        }
    }

    /**
     * Returns db connection object.
     * @return object PDO object.
     */
    public function getConn()
    {
        if ($this->conn == NULL) {
            return $this->createConnection(
                DB_HOST,
                DB_PORT,
                DB_USERNAME,
                DB_PASSWORD,
                DB_NAME
            );
        }

        return $this->conn;
    }
}
