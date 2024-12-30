<?php

if($_SESSION['tablica'][5]=='administrator')
{
    echo '<script type="text/javascript">
document.getElementById("albumy").style.display="block";
</script>';
echo '<script type="text/javascript">
document.getElementById("zdjecia").style.display="block";
</script>';
echo '<script type="text/javascript">
document.getElementById("kom").style.display="block";
</script>';
echo '<script type="text/javascript">
document.getElementById("users").style.display="block";
</script>';
}
if($_SESSION['tablica'][5]=='moderator')
{
    echo '<script type="text/javascript">
    document.getElementById("albumy").style.display="none";
    </script>';
    echo '<script type="text/javascript">
    document.getElementById("zdjecia").style.display="block";
    </script>';
    echo '<script type="text/javascript">
    document.getElementById("kom").style.display="block";
    </script>';
    echo '<script type="text/javascript">
    document.getElementById("users").style.display="none";
    </script>';   
}
?>