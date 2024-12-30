<?php
if($_SESSION['zalogowany']==false)
echo '<script type="text/javascript">
document.getElementById("wyloguj").style.display="none";
</script>';
else echo '<script type="text/javascript">
document.getElementById("wyloguj").style.display="block";
</script>';
?>