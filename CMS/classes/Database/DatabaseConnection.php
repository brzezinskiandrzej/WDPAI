<?php

namespace App\Database;

use Exception;

class DatabaseConnection
{
    private static ?DatabaseConnection $instance = null;
    private $connection;

    private function __construct()
    {
        $conn = pg_connect("host=db dbname=postgres user=postgres password=root");
        if (!$conn) {
            throw new Exception("Błąd połączenia z bazą danych: " . pg_last_error());
        }
        pg_set_client_encoding($conn, 'UTF8');
        pg_query($conn, "SET search_path TO public");

        $this->connection = $conn;
    }

    public static function getInstance(): DatabaseConnection
    {
        if (self::$instance === null) {
            self::$instance = new DatabaseConnection();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
