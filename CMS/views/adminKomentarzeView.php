<!-- admin/views/adminKomentarzeView.php -->

<?php
session_start();
$role = $_SESSION['tablica'][5] ?? '';
$co = $_GET['co'] ?? null;

// Komunikat
if (!isset($_SESSION['warning3'])) {
    $_SESSION['warning3'] = '';
}

// W starym kodzie mieliśmy param co=tylko/wszystko/null => 
// - co=null => lubdiv z guzikami 
// - co=wszystko => pętla po $comments (zaakceptowane i nie)
// - co=tylko => pętla po $comments (niezaakceptowane)

?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="../style/adminmenu.css">
</head>
<body>
<div id="container">
<div id="main">

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

<!-- adminmenucheck -->
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

<p class="cwarning"><?= htmlspecialchars($_SESSION['warning3']) ?></p>
<?php unset($_SESSION['warning3']); ?>

<?php
// param $comments – kontroler przekazał listę komentarzy (lub pustą)
if (!isset($comments)) {
    $comments = []; 
}

// co=null => pokazywaliśmy lubdiv do "tylko niezaakceptowane" / "wszystko"
if (!$co) {
    echo '<div class="lubdiv">
            <form method="post" action="kombuttons.php">
              <input type="submit" name="nie" class="lub" value="Wyświetl tylko niezaakceptowane komentarze">
              <input type="submit" name="tak" class="lub" value="Wyświetl wszystkie komentarze">
            </form>
          </div>';
} else {
    // co=wszystko lub co=tylko => pętla po $comments
    echo '<div id="listazdjec">';
    $i=0;
    foreach ($comments as $wynik) {
        echo '<div class="komlist">
                <form class="zmiana2" method="post" action="index.php?type=kom&action=edit" id="nowykom">
                  <input type="hidden" name="id" value="'.$wynik['id'].'">
                  <input type="hidden" name="textareanumber" value="'.$i.'">';

        if ($role=='administrator') {
            // Admin może edytować
            echo '<input type="text" class="textarea" name="kom'.$i.'" value="'.htmlspecialchars($wynik['komentarz']).'">
                  <input type="submit" name="zmienkomentarz" id="zmienkomentarz" value="Potwierdź Edycje">';
        } elseif ($role=='moderator') {
            // Moderator read-only
            if ($wynik['zaakceptowany']=='0') {
                echo '<input type="text" readonly="readonly" class="textarea" name="kom'.$i.'" value="'.htmlspecialchars($wynik['komentarz']).'">';
            } else {
                echo '<input type="text" readonly="readonly" class="textarea" name="kom'.$i.'" value="'.htmlspecialchars($wynik['komentarz']).'">';
            }
        }
        echo '</form>';

        echo '<p class="listelement">Zdjęcie: '.htmlspecialchars($wynik['opiszdjecia'])
             .str_repeat("&nbsp;",2).'Autor: '.htmlspecialchars($wynik['login']).'</p>
             <div class="funkcje">';

        if ($wynik['zaakceptowany']=='0') {
            echo '<form method="post" action="index.php?type=kom&action=accept" style="display:inline;">
                    <input type="hidden" name="id" value="'.$wynik['id'].'">
                    <input type="submit" name="zaakceptuj" class="zmien" id="zaakceptuj" value="Zaakceptuj">
                  </form>';
        }
        echo '<form method="post" action="index.php?type=kom&action=delete" style="display:inline;">
                <input type="hidden" name="id" value="'.$wynik['id'].'">
                <input type="submit" name="usun2" id="usun" value="Usuń">
              </form>';

        echo '</div></form></div>';
        $i++;
    }
    echo '</div>';
}
?>

</div>
</div>
</body>
</html>
