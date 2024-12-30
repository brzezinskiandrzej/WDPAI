<?php
echo '
<table id="sortowanie">
  <tbody>
    <tr>
      <td><p>Sortuj według:</p></td>
      <td><a class="sorttype" href="index.php?sort=tytul&type=">' . ((isset($_SESSION["sortowanie"]) && $_SESSION["sortowanie"] == "tytul") ? "<p>Tytuł ▼</p>" : "<p>Tytuł</p>") . '</a></td>
      <td><a class="sorttype" href="index.php?sort=data&type=">' . ((isset($_SESSION["sortowanie"]) && $_SESSION["sortowanie"] == "data" && $_SESSION["typ"] == "") ? "<p>Data(od najstarszych) ▼</p>" : "<p>Data(od najstarszych)</p>") . '</a></td>
      <td><a class="sorttype" href="index.php?sort=data&type=DESC">' . ((isset($_SESSION["sortowanie"]) && $_SESSION["sortowanie"] == "data" && $_SESSION["typ"] == "DESC") ? "<p>Data(od najnowszych) ▼</p>" : "<p>Data(od najnowszych)</p>") . '</a></td>
      <td><a class="sorttype" href="index.php?sort=login&type=">' . ((isset($_SESSION["sortowanie"]) && $_SESSION["sortowanie"] == "login") ? "<p>Autor ▼</p>" : "<p>Autor</p>") . '</a></td>
    </tr>
  </tbody>
</table>';

echo '<div class="galeria">';
foreach ($photos as $wynik) {
    if ($wynik['ile'] > 0) {
        $id = $wynik['id'];
        $coverPhoto = $covers[$id] ?? null;
        if ($coverPhoto) {
            echo '<div class="foto-container"><div class="img-overlay-container"><a href="album.php?id=' . $id . '"><img src="photo/' . $id . '/' . $coverPhoto['opis'] . '"><div class="overlay"><p>Tytuł albumu:' . $wynik['tytul'] . '</p><p> Autor: ' . $wynik['login'] . '</p><p> Data utworzenia: ' . $wynik['data'] . '</p></div></a></div></div>';
        }
    }
}
echo '</div>';
?>