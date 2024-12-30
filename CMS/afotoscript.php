<?php
session_start();
require 'javascript/databaseconnection.php';

$sql = "INSERT INTO zdjecia_oceny (
            id_zdjecia,
            id_uzytkownika,
            ocena
        ) VALUES (
            " . $_POST['id'] . ",
            " . $_SESSION['tablica'][7] . ",
            " . $_POST['star'] . "
        )";


$result = pg_query($conn, $sql);

if (!$result) {
    echo "Błąd zapytania: " . pg_last_error($conn);
}

echo "<script language=\"JavaScript\">     
document.location=\"foto.php?id=" . $_POST['id'] . "&id_albumu=" . $_POST['idalbm'] . "&r=1&width=\" + screen.width + \"&Height=\" + screen.height;     
</script>"; 
?>
