<?php
if($_SESSION['zalogowany']==true)
echo '<script type="text/javascript">
document.getElementById("konto").style.display="block";
</script>';
else echo '<script type="text/javascript">
document.getElementById("konto").style.display="none";
</script>';
?>