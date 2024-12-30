<style>
    img {
        display: inline-block;
        padding: 10px;
        max-width: 100%;
    }
    input {
        text-align: center;
        padding: 5px;
        margin: 5px;
    }
    #numery {
        text-align: center;
    }
    body {
        text-align: center;
    }
</style>
<?php

require 'javascript/databaseconnection.php';
$_SESSION['album'] = $_GET['id'];

$stronyzapytanie = pg_query_params($conn, "SELECT COUNT(*) as ile FROM (
    SELECT albumy.id as katalog, zdjecia.* FROM albumy
    INNER JOIN zdjecia ON zdjecia.id_albumu = albumy.id 
    WHERE zdjecia.id_albumu = $1 AND zdjecia.zaakceptowane = 1
    ORDER BY zdjecia.data DESC
) n", [$_SESSION['album']]);

$row = pg_fetch_assoc($stronyzapytanie);
$numerstron = ceil($row['ile'] / 20);

if (isset($_GET['numer'])) {
    $_SESSION['strona'] = ($_GET['numer'] - 1) * 20;
} else {
    $_SESSION['strona'] = 0;
}

$zapytanie = pg_query_params($conn, "SELECT albumy.id as katalog, zdjecia.* FROM albumy
    INNER JOIN zdjecia ON zdjecia.id_albumu = albumy.id 
    WHERE zdjecia.id_albumu = $1 AND zdjecia.zaakceptowane = 1
    ORDER BY zdjecia.data DESC
    LIMIT 20 OFFSET $2", [$_SESSION['album'], $_SESSION['strona']]);

while ($wynik = pg_fetch_assoc($zapytanie)) {
    if ($wynik['zaakceptowane'] == 1) {
        echo '<a href="foto.php?id=' . $wynik['id'] . '"><img src="photo/' . $wynik['katalog'] . '/' . $wynik['opis'] . '" height="180"></a>';
    }
}

pg_close($conn);

?>
