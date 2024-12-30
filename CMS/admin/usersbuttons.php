<?php

if(isset($_POST['zwykly']))
{
    header('Location:index.php?type=users&co=zwykly');
}
if(isset($_POST['mod']))
{
    header('Location:index.php?type=users&co=mod');
}
if(isset($_POST['admin']))
{
    header('Location:index.php?type=users&co=admin');
}
if(isset($_POST['tak']))
{
    header('Location:index.php?type=users&co=wszystko');
}

?>