<?php
namespace T3Stores\Core;

use \PDO;
use \PDOException;

class Database
{
    // DB connection object.
    private $db = NULL;



    /**
     * Creates DB connection.
     * @return object PDO object.
     */
    private function createConnection()
    {
        $dbHost     = DB_HOST;
        $dbName     = DB_NAME;
        $charset    = "utf8";

        $dsn = "mysql:host={$dbHost};dbname={$dbName};charset={$charset}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        try {
            return (new PDO($dsn, DB_USERNAME, DB_PASSWORD, $options));
        } catch (PDOException $ex) {
            throw new PDOException($ex->getMessage(), (int) $ex->getCode());
        }
    }



    /**
     * Returns db connection object.
     * Singleton.
     * @return object PDO object.
     */
    public function getInstance()
    {
        if ($this->db == NULL) {
            return $this->createConnection();
        }

        return $this->db;
    }
}