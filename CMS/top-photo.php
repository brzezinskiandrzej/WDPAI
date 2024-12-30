<?php
session_start();
?>
<html>
<head>
<meta charset="utf-8" />
<link href="style/menu.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" type="text/css" href="style/footerelement.css"/>
</head>
<body>
<div id="container">
<?php


include 'javascript/include/menu.php';

?>

	
<script src="javascript/jquery-3.6.0.min.js"></script>
<script src="javascript/include/footer.js"></script>
<script src="javascript/include/menunagorze.js"></script>
<?php include 'javascript/include/awyloguj.php'?>
<?php include 'javascript/include/arejestruj.php'?>
<?php include 'javascript/include/aloguj.php'?>
<?php include 'javascript/include/adodajalbum.php'?>
<?php include 'javascript/include/adodajzdjecie.php'?>
<?php include 'javascript/include/akonto.php'?>
<?php include 'javascript/include/aadmin.php'?>

</div>
<footer id="footer">
		
			<address>Autor: Andrzej Brzeziński 4Tb</address>	
		
	</footer>
</body>

</html>