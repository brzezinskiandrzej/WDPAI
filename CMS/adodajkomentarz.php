<?php
session_start();

require 'javascript/databaseconnection.php';

$title = str_replace("'", "''", $_POST['kom']);
$title = str_replace('"', '""', $_POST['kom']);
echo $title;

$date = date('Y-m-d H:i:s');

$sql = "INSERT INTO zdjecia_komentarze (
            id_zdjecia,
            id_uzytkownika,
            data,
            komentarz,
            zaakceptowany
        ) VALUES (
            " . $_POST["id_zdjecia"] . ",
            " . $_SESSION['tablica'][7] . ",
            '" . $date . "',
            '" . $title . "',
            0
        )";

$result = pg_query($conn, $sql);

if (!$result) {
    echo "Błąd zapytania: " . pg_last_error($conn);
}

$_SESSION['warning3'] = 'Twój komentarz czeka na akceptacje administratora';

echo "<script language=\"JavaScript\">     
document.location=\"foto.php?id=" . $_POST['id_zdjecia'] . "&id_albumu=" . $_POST['idalbm'] . "&r=1&width=\" + screen.width + \"&Height=\" + screen.height;     
</script>"; 
?>
