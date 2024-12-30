<?php
if($_SESSION['zalogowany']==true)
{
if($_SESSION['tablica'][5]=='administrator' || $_SESSION['tablica'][5]=='moderator' )
    echo '<script type="text/javascript">
document.getElementById("admin").style.display="block";
</script>';
else echo '<script type="text/javascript">
document.getElementById("admin").style.display="none";
</script>';
}
else echo '<script type="text/javascript">
document.getElementById("admin").style.display="none";
</script>';
?>