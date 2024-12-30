<?php
if($_SESSION['zalogowany']==true){
echo '<script> document.getElementById("dodajid").href="dodaj-foto.php" </script>';
}
else echo '<script> document.getElementById("dodajid").href="logrej.php" </script>';
?>