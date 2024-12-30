<?php

require 'javascript/databaseconnection.php';

$zapytanie = pg_query($conn, "SELECT zdjecia.data, albumy.tytul, uzytkownicy.login, albumy.id, zdjecia.opis, zdjecia.id as id_zdjecia 
    FROM zdjecia
    INNER JOIN albumy ON zdjecia.id_albumu = albumy.id
    INNER JOIN uzytkownicy ON albumy.id_uzytkownika = uzytkownicy.id
    WHERE zdjecia.zaakceptowane = 1
    ORDER BY zdjecia.data DESC
    LIMIT 20");

$i = 1;
echo'<div class="galeria-top" style="text-align: center;">';
while ($wynik = pg_fetch_assoc($zapytanie)) {
    $i++;
    echo '
    <div class="foto-container">
      <a href="foto.php?id=' . $wynik['id_zdjecia'] . '">
        <div class="img-overlay-container">
          <img src="photo/' . $wynik['id'] . '/' . $wynik['opis'] . '" alt="">
          <div class="overlay">
            <p>Tytuł albumu: ' . $wynik['tytul'] . '</p>
            <p>Autor: ' . $wynik['login'] . '</p>
            <p>Data dodania: ' . $wynik['data'] . '</p>
          </div>
        </div>
      </a>
    </div>
    ';
}
echo '</div>';
pg_close($conn);
?>
