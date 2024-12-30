<?php
if (isset($_POST['nast'])) {
    require 'javascript/databaseconnection.php';
    $przewijanie = pg_query_params($conn, "
        SELECT albumy.tytul, uzytkownicy.login, zdjecia.* 
        FROM zdjecia
        INNER JOIN albumy ON zdjecia.id_albumu = albumy.id
        INNER JOIN uzytkownicy ON albumy.id_uzytkownika = uzytkownicy.id
        WHERE zdjecia.id_albumu = $1 
        AND zdjecia.data >= $2 
        AND zdjecia.zaakceptowane = 1 
        AND zdjecia.id != $3
        ORDER BY zdjecia.data 
        LIMIT 1
    ", array($_POST['idalbm'], $_POST['data'], $_POST['id']));

    $row = pg_fetch_assoc($przewijanie);

    if ($row) {
        echo "<script language=\"JavaScript\">     
        <!--      
        document.location=\"foto.php?id=" . $row['id'] . "&id_albumu=" . $row['id_albumu'] . "&r=1&width=\" + screen.width + \"&Height=\" + screen.height;     
        //-->     
        </script>";
    }

    pg_close($conn);
}

if (isset($_POST['poprz'])) {
    require 'javascript/databaseconnection.php';
    $przewijanie = pg_query_params($conn, "
        SELECT albumy.tytul, uzytkownicy.login, zdjecia.* 
        FROM zdjecia
        INNER JOIN albumy ON zdjecia.id_albumu = albumy.id
        INNER JOIN uzytkownicy ON albumy.id_uzytkownika = uzytkownicy.id
        WHERE zdjecia.id_albumu = $1 
        AND zdjecia.data <= $2 
        AND zdjecia.zaakceptowane = 1 
        AND zdjecia.id != $3
        ORDER BY zdjecia.data DESC 
        LIMIT 1
    ", array($_POST['idalbm'], $_POST['data'], $_POST['id']));

    $row = pg_fetch_assoc($przewijanie);

    if ($row) {
        echo "<script language=\"JavaScript\">     
        <!--      
        document.location=\"foto.php?id=" . $row['id'] . "&id_albumu=" . $row['id_albumu'] . "&r=1&width=\" + screen.width + \"&Height=\" + screen.height;     
        //-->     
        </script>";
    }

    pg_close($conn);
}
?>
