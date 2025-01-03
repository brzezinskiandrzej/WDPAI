<!-- admin/views/adminAlbumyView.php -->
<?php
// Ten widok jest wywoływany przez AdminAlbumController->showAlbumy()
// Zakładamy, że mamy zmienne:
//  - $albums (tablica albumów z polami: id, tytul, data, ile, accept, login, niezaakceptowane...)
//  - $numerstron (liczba stron do paginacji)
//  - $_SESSION['warning3'] (komunikat)
session_start();
$role = $_SESSION['tablica'][5] ?? '';
?>
<html>
<head>
<meta charset="utf-8" />
<link rel="stylesheet" type="text/css" href="../style/adminmenu.css"/>
</head>
<body>
<div id="container">
<div id="main">

<?php
// Pokazujemy ewentualny komunikat
if (!isset($_SESSION['warning3'])) {
    $_SESSION['warning3'] = '';
}
echo ' <p class="cwarning">'.$_SESSION['warning3'].'</p>';
if ($_SESSION['warning3'] !== '') {
    unset($_SESSION['warning3']);
}
?>

<!-- MENU identyczne jak w dawnym index.php i adminmenucheck -->
<div id="menutoogle">
    <input type="checkbox">
    <span></span>
    <span></span>
    <span></span>
    <ul id="menukonto">
        <li id="albumy"><a href="index.php?type=albumy">Albumy</a></li>
        <li id="zdjecia"><a href="index.php?type=zdjecia">Zdjęcia</a></li>
        <li id="kom"><a href="index.php?type=kom">Komentarze</a></li>
        <li id="users"><a href="index.php?type=users">Użytkownicy</a></li>
        <li><a href="../index.php">Powrót do galerii</a></li>
    </ul>
</div>

<script src="../javascript/jquery-3.6.0.min.js"></script>
<script src="../javascript/include/footer.js"></script>

<!-- Logika ukrywania / pokazywania linków w zależności od uprawnień -->
<script type="text/javascript">
<?php if ($role=='administrator'): ?>
document.getElementById("albumy").style.display="block";
document.getElementById("zdjecia").style.display="block";
document.getElementById("kom").style.display="block";
document.getElementById("users").style.display="block";
<?php elseif ($role=='moderator'): ?>
document.getElementById("albumy").style.display="none";
document.getElementById("zdjecia").style.display="block";
document.getElementById("kom").style.display="block";
document.getElementById("users").style.display="none";
<?php endif; ?>
</script>

<!-- Tytuł listy albumów -->
<p id="tytullisty">Lista albumów : </p>

<?php
// Wyświetlamy listę albumów jak w starym adminscript.php
if (!empty($albums)) {
    foreach ($albums as $wynik) {
        $niezaakceptowane = (int)$wynik['niezaakceptowane'];
        $tytul            = $wynik['tytul'];
        $login            = $wynik['login'];
        $data             = $wynik['data'];
        $albumId          = (int)$wynik['id'];

        echo '<div class="albumlist">
                <img src="../photo/folder.png" height="38" align="left">
                <p class="listelement">'
                     . $tytul . str_repeat("&nbsp;",2);
        if ($niezaakceptowane != 0) {
            echo 'niezaakceptowane :' . $niezaakceptowane . str_repeat("&nbsp;",2);
        }
        echo 'Autor : ' . $login . str_repeat("&nbsp;",5)
             . 'Data utworzenia : ' . $data
             . '</p>
            <div class="funkcje">
                <button id="tytulzmien'.$albumId.'" onclick="pokaz2('.$albumId.')">Zmień tytuł</button>
                <form class="zmiana3" method="post" action="index.php?type=albumy&action=edit">
                    <input type="text" name="nowytytul" class="ntytul" id="ntytul'.$albumId.'" style="display:none;">
                    <input type="hidden" name="id" value="'.$albumId.'">
                    <input type="submit" name="zmien" class="zmien" id="zmien'.$albumId.'" value="Zmień Tytuł" style="display:none;">
                </form>

                <form class="zmiana3" method="post" action="index.php?type=albumy&action=delete">
                    <input type="hidden" name="id" value="'.$albumId.'">
                    <input type="submit" name="usun2" id="usun" value="Usuń Album">
                </form>
            </div>
        </div>';
    }
} else {
    echo '<div class="albumlist"><p class="listelement">Brak albumów</p></div>';
}

// Paginacja
if (!empty($numerstron) && $numerstron>1) {
    echo '<form id="numery" method="get" action="index.php">';
    echo '<input type="hidden" name="type" value="albumy">';
    for ($i=1; $i<=$numerstron; $i++) {
        echo '<button class="paging" type="submit" name="numer" value="'.$i.'">'.$i.'</button>';
    }
    echo '</form>';
}
?>

</div>
</div>

<script>
function pokaz2(numer) {
    document.getElementById('ntytul'+numer).style.display = "inline";
    document.getElementById("zmien"+numer).style.display = "inline";
    document.getElementById("tytulzmien"+numer).style.display = "none";
}
</script>

</body>
</html>
