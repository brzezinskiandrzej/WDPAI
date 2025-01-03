<!-- admin/views/adminZdjeciaView.php -->

<?php
session_start();
$role = $_SESSION['tablica'][5] ?? '';
$co   = $_GET['co'] ?? null;
$id   = $_GET['id'] ?? null;

// Komunikat
if (!isset($_SESSION['warning3'])) {
    $_SESSION['warning3'] = '';
}

// Kontroler przekazał nam:
//   $photos – listę zdjęć (np. przy co=tylko, co=wszystko z id, itp.)
//   $albums – listę albumów (np. przy co=wszystko bez id)
// Jeśli $co=null => wyświetlamy "lubdiv"
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<link rel="stylesheet" href="../style/adminmenu.css">
<script>
function powrot() {
    document.getElementById('duzezdjecie').style.display="none";
}
function wiekszy(album,zdjecie) {
    document.getElementById('duzezdjecie').style.display="block";
    document.getElementById("duzyimage").src = "../photo/"+album+"/"+zdjecie;
}
</script>
</head>
<body>
<div id="container">
<div id="main">

<!-- MENU -->
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

<!-- Duże zdjęcie niewidoczne -->
<div id="duzezdjecie" style="display:none;">
    <img id="duzyimage" height="600" onclick="powrot()">
</div>

<?php
if (!$co) {
    // brak param co => "lubdiv"
    echo '<div class="lubdiv">
            <form method="post" action="zdjeciabuttons.php">
              <input type="submit" name="nie" class="lub" value="Wyświetl tylko niezaakceptowane zdjęcia">
              <input type="submit" name="tak" class="lub" value="Wyświetl albumy ze zdjęciami">
            </form>
          </div>';
} elseif ($co === 'wszystko') {
    if ($id) {
        // Wyświetlamy zdjęcia danego albumu => $photos
        echo '<div id="listazdjec">';
        foreach ($photos as $wynik) {
            echo '<div class="albumlistz">
                    <img class="zdj" title="Kliknij na zdjęcie by powiększyć"
                         src="../photo/'.$wynik['albumid'].'/'.$wynik['opis'].'"
                         height="180" align="left"
                         onclick="wiekszy(\''.$wynik['albumid'].'\',\''.$wynik['opis'].'\')">
                    <p class="listelement">Nazwa zdjęcia: '.$wynik['opiszdjecia'].'&nbsp;&nbsp;Album: '.$wynik['tytul'].'</p>
                    <div class="funkcje">';
            if ($wynik['zaakceptowane']=='0') {
                // przycisk akceptacji
                echo '<form class="zmiana2" method="post" action="index.php?type=zdjecia&action=accept">
                        <input type="hidden" name="id" value="'.$wynik['id'].'">
                        <input type="submit" name="zaakceptuj" class="zmien" id="zaakceptuj" value="Zaakceptuj Zdjęcie">
                        <input type="hidden" name="idalbumu" value="'.$wynik['albumid'].'">
                        <input type="hidden" name="opis" value="'.$wynik['opis'].'">
                      </form>';
            }
            // przycisk usuń
            echo '<form class="zmiana2" method="post" action="index.php?type=zdjecia&action=delete">
                    <input type="hidden" name="id" value="'.$wynik['id'].'">
                    <input type="hidden" name="idalbumu" value="'.$wynik['albumid'].'">
                    <input type="hidden" name="opis" value="'.$wynik['opis'].'">
                    <input type="submit" name="usun2" id="usun" value="Usuń Zdjęcie">
                  </form>';
            echo '</div></div>';
        }
        echo '</div>';
    } else {
        // Wyświetlamy listę albumów => $albums
        echo '<div id="wysrodkuj">';
        $_SESSION['warning3']='Wybierz album';
        echo '<p class="cwarning">'.$_SESSION['warning3'].'</p>';
        unset($_SESSION['warning3']);

        foreach ($albums as $wynik) {
            echo '<div class="tooltip">
                    <a href="index.php?type=zdjecia&co=wszystko&id='.$wynik['id'].'">
                        <img class="glowne" src="../photo/folder.png" height="180">
                    </a>
                    <p class="atitle">'.$wynik['tytul'].'</p>
                  </div>';
        }
        echo '</div>';
    }
} elseif ($co === 'tylko') {
    // Tylko niezaakceptowane => $photos
    echo '<div id="listazdjec">';
    foreach ($photos as $wynik) {
        echo '<div class="albumlistz">
                <img class="zdj" title="Kliknij na zdjęcie by powiększyć"
                     src="../photo/'.$wynik['albumid'].'/'.$wynik['opis'].'"
                     height="180" align="left"
                     onclick="wiekszy(\''.$wynik['albumid'].'\',\''.$wynik['opis'].'\')">
                <p class="listelement">Nazwa zdjęcia: '.$wynik['opiszdjecia'].'&nbsp;&nbsp;Album: '.$wynik['tytul'].'</p>
                <div class="funkcje">
                  <form class="zmiana2" method="post" action="index.php?type=zdjecia&action=accept">
                    <input type="hidden" name="id" value="'.$wynik['id'].'">
                    <input type="submit" name="zaakceptuj" class="zmien" id="zaakceptuj" value="Zaakceptuj Zdjęcie">
                    <input type="hidden" name="idalbumu" value="'.$wynik['albumid'].'">
                    <input type="hidden" name="opis" value="'.$wynik['opis'].'">
                  </form>
                  <form class="zmiana2" method="post" action="index.php?type=zdjecia&action=delete">
                    <input type="hidden" name="id" value="'.$wynik['id'].'">
                    <input type="hidden" name="idalbumu" value="'.$wynik['albumid'].'">
                    <input type="hidden" name="opis" value="'.$wynik['opis'].'">
                    <input type="submit" name="usun2" id="usun" value="Usuń Zdjęcie">
                  </form>
                </div>
              </div>';
    }
    echo '</div>';
}
?>

</div>
</div>
</body>
</html>
