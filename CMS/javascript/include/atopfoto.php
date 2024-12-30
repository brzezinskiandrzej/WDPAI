<?php
require 'javascript/databaseconnection.php';

$zapytanie = pg_query($conn, "SELECT AVG(zdjecia_oceny.ocena) as ocena, COUNT(zdjecia_oceny.id), zdjecia_oceny.id_zdjecia, 
    albumy.tytul, uzytkownicy.login, albumy.id, zdjecia.opis 
    FROM zdjecia_oceny
    INNER JOIN uzytkownicy ON zdjecia_oceny.id_uzytkownika = uzytkownicy.id
    INNER JOIN zdjecia ON zdjecia_oceny.id_zdjecia = zdjecia.id
    INNER JOIN albumy ON zdjecia.id_albumu = albumy.id
    GROUP BY zdjecia_oceny.id_zdjecia, albumy.tytul, uzytkownicy.login, albumy.id, zdjecia.opis
    ORDER BY ocena DESC
    LIMIT 20");

$i = 1;
echo'<div class="galeria-top" style="text-align: center;">';
while ($wynik = pg_fetch_assoc($zapytanie)) {
    echo '
<div class="foto-container">
  <a href="foto.php?id=' . $wynik['id_zdjecia'] . '">
    <div class="img-overlay-container">
      <img src="photo/' . $wynik['id'] . '/' . $wynik['opis'] . '" alt="">
      <div class="overlay">
        <p>Pozycja: ' . $i . '</p>
        <p>Tytuł albumu: ' . $wynik['tytul'] . '</p>
        <p>Autor: ' . $wynik['login'] . '</p>
        <p>Ocena: ' . round($wynik['ocena'],2) . '</p>
      </div>
    </div>
  </a>
</div>
';
    $i++;
}
echo '</div>';
pg_close($conn);
?>
