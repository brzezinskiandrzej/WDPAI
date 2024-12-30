<?php
session_start();
ob_start(); // Rozpocznij buforowanie

require 'javascript/databaseconnection.php';

if (isset($_GET['albumid'])) {
    $album = $_GET['albumid'];
    $zapytanie = pg_query_params($conn, "SELECT albumy.tytul, zdjecia.* FROM albumy
                                         INNER JOIN zdjecia ON zdjecia.id_albumu = albumy.id
                                         WHERE zdjecia.id_albumu = $1", [$album]);

    if ($zapytanie) {
        echo '<script>document.getElementById("main").style.display="block";</script>';
        while ($wynik = pg_fetch_assoc($zapytanie)) {
            echo '<img src="photo/' . $album . '/' . $wynik['opis'] . '" height="180">';
        }
    }
    pg_close($conn);
} else {
    $zapytanie4 = pg_query_params($conn, "SELECT albumy.id, albumy.tytul, albumy.data, COUNT(zdjecia.id) 
                                          FROM albumy
                                          LEFT JOIN zdjecia ON zdjecia.id_albumu = albumy.id
                                          WHERE albumy.id_uzytkownika = $1
                                          GROUP BY albumy.id", [$_SESSION['tablica'][7]]);

    if (headers_sent($file, $line)) {
        die("Headers already sent in $file on line $line");
    }

    if ($zapytanie4 && pg_num_rows($zapytanie4) == 0) {
        pg_close($conn);
        header('Location:dodaj-album.php?albumy=0');
        exit;
    } elseif ($zapytanie4 && pg_num_rows($zapytanie4) == 1) {
        $wynik4 = pg_fetch_assoc($zapytanie4);
        pg_close($conn);
        header('Location:dodaj-foto.php?albumid=' . $wynik4['id']);
        exit;
    } else {
        if ($zapytanie4) {
            echo '<ol id="listaalbumow">';
            while ($wynik4 = pg_fetch_assoc($zapytanie4)) {
                echo '<li><a class="numerlistyalbumow" id=' . $wynik4['id'] . ' name="clickedalbum" href="dodaj-foto.php?albumid=' . $wynik4['id'] . '">' . $wynik4['tytul'] . '</a> ' . $wynik4['data'] . ' ' . $wynik4['count'] . '</li>';
            }
            echo '</ol>';
        }
        pg_close($conn);
    }
}
ob_end_flush(); // Wysyłamy dane na koniec
?>
