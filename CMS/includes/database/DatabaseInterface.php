<?php
namespace Database;

interface DatabaseInterface {
    public function connect();
    public function query(string $query, array $params = []): resource;
    public function fetchAll($result): array;
    public function close();
}
?>
