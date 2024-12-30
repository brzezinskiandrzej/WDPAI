<?php
namespace Database;

require_once 'DatabaseInterface.php';

class PostgresDatabase implements DatabaseInterface {
    private $conn;

    public function connect() {
        $this->conn = pg_connect("host=db port=5432 dbname=postgres user=postgres password=root");
        if (!$this->conn) {
            throw new \Exception("Nie udało się połączyć z bazą danych: " . pg_last_error());
        }
    }

    public function query(string $query, array $params = []): resource {
        if (!empty($params)) {
            $result = pg_query_params($this->conn, $query, $params);
        } else {
            $result = pg_query($this->conn, $query);
        }
        if (!$result) {
            throw new \Exception("Błąd zapytania: " . pg_last_error($this->conn));
        }
        return $result;
    }

    public function fetchAll($result): array {
        return pg_fetch_all($result) ?: [];
    }

    public function close() {
        pg_close($this->conn);
    }
}
?>