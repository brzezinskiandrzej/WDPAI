<?php
ob_start();
session_start();
?>
<html>
<head>
<meta charset="utf-8" />
<link href="../style/menu.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" type="text/css" href="../style/footerelement.css"/>
<link rel="stylesheet" type="text/css" href="../style/adminmenu.css"/>


</head>
<body>
<div id="container">
<div id="main">


<?php
session_start();
if(!isset($_SESSION['warning']))
$_SESSION['warning']='';
if(!isset($_SESSION['warning2']))
$_SESSION['warning2']='';
if(!isset($_SESSION['warning3']))
$_SESSION['warning3']='';
//include '../javascript/include/menu.php';

?>
<?php include 'adminscript.php'?>
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
<?php include 'adminmenucheck.php'?>
<?php

?>



</div>
</div>
<footer id="footer">
		
			<address>Autor: Andrzej Brzeziński 4Tb</address>	
		
	</footer>
</body>

</html>
<?php
ob_end_flush();
?>