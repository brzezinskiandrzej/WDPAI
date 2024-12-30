<?php
if($_SESSION['zalogowany']==true)
echo '<script> document.getElementById("zalozid").href="dodaj-album.php" </script>';
else echo '<script> document.getElementById("zalozid").href="logrej.php" </script>';
?>