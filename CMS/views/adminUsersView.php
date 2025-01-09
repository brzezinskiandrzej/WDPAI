<!-- admin/views/adminUsersView.php -->

<?php
session_start();
$role = $_SESSION['tablica'][5] ?? '';
$co = $_GET['co'] ?? null;

// $users – przekazane z kontrolera, np. $users = $this->userService->getUsersByCo($co,...)
if (!isset($users)) {
    $users = [];
}

// Komunikat
if (!isset($_SESSION['warning3'])) {
    $_SESSION['warning3'] = '';
}
?>
<!DOCTYPE html>
<html>
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

if (!$co) {
    echo '<div class="lubdiv2">
            <form method="get" action="index.php">
              <input type="hidden" name="type" value="users">
              <button type="submit" name="co" value="zwykly" class="lub">Wyświetl użytkowników grupy: Użytkownicy</button>
              <button type="submit" name="co" value="mod" class="lub">Wyświetl użytkowników grupy: Moderatorzy</button>
              <button type="submit" name="co" value="admin" class="lub">Wyświetl użytkowników grupy: Administratorzy</button>
              <button type="submit" name="co" value="wszystko" class="lub">Wyświetl wszystkich użytkowników</button>
            </form>
          </div>';
} else {
    
    echo '<div id="listazdjec">';

    foreach ($users as $u) {
        echo '<div class="komlist">
                <p class="listelement2">';

        if ($u['uprawnienia']=='użytkownik') {
            echo 'Użytkownik : ';
        } elseif ($u['uprawnienia']=='moderator') {
            echo 'Moderator : ';
        } elseif ($u['uprawnienia']=='administrator') {
            echo 'Administrator : ';
        }

        echo str_repeat("&nbsp;",1).htmlspecialchars($u['login']).' '.str_repeat("&nbsp;",2)
             .'Aktywny : ' . ($u['aktywny']==1 ? 'Tak' : 'Nie')
             . '</p>';

        if ($role=='administrator') {
            
            echo '<button id="uprawnieniazmien'.$u['id'].'" onclick="pokaz3('.$u['id'].')">Zmień uprawnienia</button>
                  <form class="zmiana2" method="post" action="index.php?type=users&action=change" style="display:inline;">
                    <div class="funkcje">
                      <select name="wybor" id="wybor'.$u['id'].'" class="zmien3" style="display:none;">
                        <option value="użytkownik">użytkownik</option>
                        <option value="moderator">moderator</option>
                        <option value="administrator">administrator</option>
                      </select>
                      <input type="hidden" name="id" value="'.$u['id'].'">
                      <button type="submit" name="zmien" class="zmien" id="zmien'.$u['id'].'" style="display:none;">Zmień</button>
                    </div>
                  </form>';

            
            if ($u['aktywny']==1) {
                echo '<form class="zmiana2" method="post" action="index.php?type=users&action=block" style="display:inline;">
                        <input type="hidden" name="id" value="' . htmlspecialchars($u["id"]) . '">
                        <button type="submit" name="blokuj" class="zmien" id="zaakceptuj">Zablokuj</button>
                    </form>';
            } else {
                echo '<form class="zmiana2" method="post" action="index.php?type=users&action=unblock" style="display:inline;">
                        <input type="hidden" name="id" value="' . htmlspecialchars($u["id"]) . '">
                        <button type="submit" name="odblokuj" class="zmien" id="zaakceptuj">Odblokuj</button>
                      </form>';
            }

           
            echo '<form class="zmiana2" method="post" action="index.php?type=users&action=delete" style="display:inline;">
                    <input type="hidden" name="id" value="'.$u['id'].'">
                    <button type="submit" name="usun2" id="usun">Usuń Konto</button>
                  </form>';
        } elseif ($role=='moderator') {
            echo '<p>(Moderator nie ma akcji na użytkownikach)</p>';
        }

        echo '</div>';
    }

    
    if ($co=='admin') {
        $myLogin = $_SESSION['tablica'][1] ?? '';
        echo '<div class="komlist"><p class="listelement2">Administrator : '.str_repeat("&nbsp;",1).$myLogin.'</p></div>';
    }

    echo '</div>';
}
?>

<script>
function pokaz3(numer) {
    document.getElementById('wybor'+numer).style.display = "inline";
    document.getElementById("zmien"+numer).style.display = "inline";
    document.getElementById("uprawnieniazmien"+numer).style.display = "none";
}
</script>

</div>
</div>
</body>
</html>
