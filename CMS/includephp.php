<html>
<head>
</head>
<body style="margin:0">
<div style="width:200px;border-right:1px solid #777;float:left;height:100%">
<a href="includephp.php?strona=1">Strona 1</a><br>
<a href="includephp.php?strona=2">Strona 2</a><br>
<a href="includephp.php?strona=3">Strona 3</a><br>
</div>
<div style="width:calc(100%-200px);float:left;">
<?php
if(!isset($_GET['strona']))$_GET['strona'] = 1;
elseif(!isset($_GET['strona']))$_GET['strona'] = 2;
elseif(!isset($_GET['strona']))$_GET['strona'] = 3;
include $_GET['strona'].'.php';
?>
</div>


</body>
</html>