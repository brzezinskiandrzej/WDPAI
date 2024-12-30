<?php
require 'javascript/databaseconnection.php';

if (isset($_GET['numer'])) {
    $_SESSION['strona'] = ($_GET['numer'] - 1) * 20;
} else {
    $_SESSION['strona'] = 0;
}

if (isset($_GET['sort'])) {
    $_SESSION['sortowanie'] = $_GET['sort'];
    $_SESSION['typ'] = $_GET['type'];
    $zapytanie = pg_query($conn, "SELECT a.id, a.tytul, a.data, COUNT(z.opis) as ile, SUM(z.zaakceptowane) as accept, u.login 
                                  FROM albumy as a
                                  INNER JOIN zdjecia as z ON z.id_albumu = a.id
                                  INNER JOIN uzytkownicy as u ON u.id = a.id_uzytkownika
                                  GROUP BY a.id, u.login, a.tytul, a.data
                                  HAVING SUM(z.zaakceptowane) > 0
                                  ORDER BY " . pg_escape_string($_SESSION['sortowanie']) . " " . pg_escape_string($_SESSION['typ']) . "
                                  LIMIT 20 OFFSET " . $_SESSION['strona']);
} else if (isset($_GET['numer'])) {
    $zapytanie = pg_query($conn, "SELECT a.id, a.tytul, a.data, COUNT(z.opis) as ile, SUM(z.zaakceptowane) as accept, u.login 
                                  FROM albumy as a
                                  INNER JOIN zdjecia as z ON z.id_albumu = a.id
                                  INNER JOIN uzytkownicy as u ON u.id = a.id_uzytkownika
                                  GROUP BY a.id, u.login, a.tytul, a.data
                                  HAVING SUM(z.zaakceptowane) > 0
                                  ORDER BY " . pg_escape_string($_SESSION['sortowanie']) . " " . pg_escape_string($_SESSION['typ']) . "
                                  LIMIT 20 OFFSET " . $_SESSION['strona']);
} else {
    $_SESSION['sortowanie'] = "tytul";
    $_SESSION['typ'] = ' ';
    $zapytanie = pg_query($conn, "SELECT a.id, a.tytul, a.data, COUNT(z.opis) as ile, SUM(z.zaakceptowane) as accept, u.login 
                                  FROM albumy as a
                                  INNER JOIN zdjecia as z ON z.id_albumu = a.id
                                  INNER JOIN uzytkownicy as u ON u.id = a.id_uzytkownika
                                  GROUP BY a.id, u.login, a.tytul, a.data
                                  HAVING SUM(z.zaakceptowane) > 0
                                  ORDER BY a.tytul
                                  LIMIT 20 OFFSET " . $_SESSION['strona']);
}

$records = array();
$stronyzapytanie = pg_query($conn, "SELECT COUNT(*) as ile 
                                    FROM (SELECT a.id, a.tytul, COUNT(z.opis) as ile, SUM(z.zaakceptowane) as accept 
                                          FROM albumy as a
                                          INNER JOIN zdjecia as z ON z.id_albumu = a.id
                                          GROUP BY a.id, a.tytul
                                          HAVING SUM(z.zaakceptowane) > 0
                                          ORDER BY a.tytul) n");
$row = pg_fetch_assoc($stronyzapytanie);
$numerstron = ceil($row['ile'] / 20);

echo '
<table id="sortowanie">
  <tbody>
    <tr>
      <!-- 1: Stały tekst: Sortuj według -->
      <td>
        <p>Sortuj według:</p>
      </td>

      <!-- 2: Tytuł -->
      <td>
        <a class="sorttype" href="index.php?sort=tytul&type=">'
        . ((isset($_SESSION["sortowanie"]) && $_SESSION["sortowanie"] == "tytul")
            ? "<p>Tytuł ▼</p>"
            : "<p>Tytuł</p>")
        . '</a>
      </td>

      <!-- 3: Data (od najstarszych) -->
      <td>
        <a class="sorttype" href="index.php?sort=data&type=">'
        . ((isset($_SESSION["sortowanie"])
            && $_SESSION["sortowanie"] == "data"
            && $_SESSION["typ"] == "")
            ? "<p>Data(od najstarszych) ▼</p>"
            : "<p>Data(od najstarszych)</p>")
        . '</a>
      </td>

      <!-- 4: Data (od najnowszych) -->
      <td>
        <a class="sorttype" href="index.php?sort=data&type=DESC">'
        . ((isset($_SESSION["sortowanie"])
            && $_SESSION["sortowanie"] == "data"
            && $_SESSION["typ"] == "DESC")
            ? "<p>Data(od najnowszych) ▼</p>"
            : "<p>Data(od najnowszych)</p>")
        . '</a>
      </td>

      <!-- 5: Autor -->
      <td>
        <a class="sorttype" href="index.php?sort=login&type=">'
        . ((isset($_SESSION["sortowanie"])
            && $_SESSION["sortowanie"] == "login")
            ? "<p>Autor ▼</p>"
            : "<p>Autor</p>")
        . '</a>
      </td>
    </tr>
  </tbody>
</table>';

echo '<div class="galeria">';
while ($wynik = pg_fetch_assoc($zapytanie)) {
    if ($wynik['ile'] > 0) {
        $id = $wynik['id'];
        $zapytanie2 = pg_query($conn, "SELECT a.id, a.data, u.login, a.tytul, z.opis 
                                       FROM albumy as a
                                       INNER JOIN zdjecia as z ON z.id_albumu = a.id
                                       INNER JOIN uzytkownicy as u ON a.id_uzytkownika = u.id
                                       WHERE a.id = $id AND z.zaakceptowane = 1
                                       ORDER BY a.tytul, z.opis;");
        $wynik2 = pg_fetch_assoc($zapytanie2);
        $records[$id][0] = $id;
        $records[$id][1] = $wynik2['tytul'];
        $records[$id][2] = $wynik2['login'];
        $records[$id][3] = $wynik2['data'];
        $records[$id][4] = $wynik2['opis'];

        echo '<div class="foto-container"><div class="img-overlay-container"><a href="album.php?id=' . $records[$id][0] . '"><img src="photo/' . $records[$id][0] . '/' . $records[$id][4] . '"><div class="overlay"><p>Tytuł albumu:' . $records[$id][1] . '</p><p> Autor: ' . $records[$id][2] . '</p><p> Data utworzenia: ' . $records[$id][3] . '</p></div></a></div></div>';
    }
}
echo '</div>';

pg_close($conn);
?>