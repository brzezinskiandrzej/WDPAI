<?php
if($_SESSION['zalogowany']==true)
echo '<script type="text/javascript">
document.getElementById("logowanie2").style.display="none";
</script>';
else echo '<script type="text/javascript">
document.getElementById("logowanie2").style.display="block";
</script>';
?>