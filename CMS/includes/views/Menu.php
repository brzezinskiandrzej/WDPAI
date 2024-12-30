<?php
class Menu {
    public static function display() {
        echo '
<div class="przerwa">
  <a href="index.php" class="link"><p id="title">IMAGE SPACE</p></a>
</div>

<!-- PRZYCISK HAMBURGERA (dodany) -->
<div class="hamburger" onclick="toggleMenu()">
  <!-- Ikonka trzech kresek -->
  <span></span>
  <span></span>
  <span></span>
</div>

<div class="nav" id="navMenu">
  <ol>
    <li id="zaloz"><a id="zalozid">Załóż album</a></li>
    <li id="dodaj"><a id="dodajid">Dodaj zdjęcie</a></li>
    <li id="oceniane"><a href="top-foto.php">Najlepiej oceniane</a></li>
    <li id="najnowsze"><a href="nowe-foto.php">Najnowsze</a></li>
    <li id="konto">
      <a href="konto.php">Moje konto</a>
      <ul>
        <li id="wyloguj"><a href="wyloguj.php">Wyloguj się</a></li>
      </ul>
    </li>
    <li id="logowanie2"><a href="logrej.php">Zaloguj się</a></li>
    <li id="rejestracja2"><a href="logrej.php?sort=1">Rejestracja</a></li>
    <li id="admin"><a id="panel" href="admin/index.php">Panel administracyjny</a></li>
  </ol>
</div>
<script>
function toggleMenu() {
  var nav = document.getElementById("navMenu");
  var hamburger = document.querySelector(".hamburger");

  nav.classList.toggle("menu-open");
  hamburger.classList.toggle("menu-open");
}

</script>

';
    }
}
?>