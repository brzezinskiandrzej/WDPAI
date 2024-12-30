<?php
$conn = pg_connect("host=db dbname=postgres user=postgres password=root");
if (!$conn) {
    echo "Błąd połączenia: " . pg_last_error();
    exit();
}

// Ustawienie kodowania znaków na UTF-8
pg_set_client_encoding($conn, 'UTF8');
pg_query($conn, "SET search_path TO public");

?>
