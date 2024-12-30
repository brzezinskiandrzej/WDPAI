<?php
session_start();
session_destroy();
session_start();
$_SESSION['zalogowany']=false;
$_SESSION['uprawnienia']='';
header('Location:index.php');

?>