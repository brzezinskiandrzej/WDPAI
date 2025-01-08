
<?php
session_start();
$role = $_SESSION['tablica'][5] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<link rel="stylesheet" type="text/css" href="../style/adminmenu.css"/>
<link rel="stylesheet" type="text/css" href="../style/footerelement.css"/>
</head>
<body>
<div id="container">
<div id="main">


<p id="wskazowka"> <- Menu</p>
<p id="tytulbeztype">PANEL ADMINISTRACYJNY</p>
<p class="wybierz"> Wybierz czynność z menu</p>


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

</div>
</div>
<footer id="footer">
    <address>Autor: Andrzej Brzeziński</address>
</footer>
</body>
</html>
