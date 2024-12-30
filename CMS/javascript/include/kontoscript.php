<?php
if(!isset($_GET['type']) || $_GET['type']=="dane")
{
    if(isset($_GET['haslo'])&& $_GET['haslo']=="ok")
    {
       echo '
       <div id="srodek">
       <form class="zmiany" method="post" action="kontohaslo.php">
       <p>Zmień adres e-mail</p><br>
       <div id="emaildiv"><input type="text" name="cemail" id="cemail" required="required" placeholder="Wpisz E-mail">
       <input type="submit" name="ccheck" id="ccheck" value="Zmień"></div>
       <p class="cwarning">'.$_SESSION["warning"].'</p>
       </form>
       <form class="zmiany" method="post" action="kontohaslo.php">
       <p>Zmień haslo</p><br>
       <div id="haslodiv"><input type="password" name="chaslo" id="chaslo" required="required" placeholder="Wpisz hasło">
       <input type="submit" name="ccheck2" id="ccheck2" value="Zmień"></div>
       <p class="cwarning">'.$_SESSION["warning2"].'</p>
       </form></div>
       '; 
       if(isset($_SESSION['warning']) && $_SESSION['warning']!='')
	{
		
		unset($_SESSION['warning']);
		
	}
    if(isset($_SESSION['warning2']) && $_SESSION['warning2']!='')
	{
		
		unset($_SESSION['warning2']);
		
	}
    }
    else{
    echo '<p id="tytuldanych"> Moje dane :</p>';
   echo '<div id="daneuzytkownika">';
    echo '<p class="dane">Login: '.$_SESSION['tablica'][1].'</p>';
    echo '<p class="dane">E-mail: '.$_SESSION['tablica'][3].'</p>';
    echo '<p class="dane">Data założenia konta: '.$_SESSION['tablica'][4].'</p>';
    echo'</div>';
    echo '<div class="centre"><button id="zmien" onclick="isclicked()">Zmień Dane</button></div>';
    echo '<div id="podajhaslo"><form method="post" action="kontohaslo.php">
    <p> Aby zmienić adres e-mail bądź hasło najpierw podaj swoje hasło</p>
    <input type="password" name="checkpasswd" id="checkpasswd" required="required" placeholder="Wpisz Hasło">
    <input type="submit" name="checksubmit" id="checksubmit" value="Sprawdź hasło">
    </form></div>';
    if(isset($_GET['haslo'])&& $_GET['haslo']=="nieok")
    {
    echo'<p id=blad>Niepoprawne hasło</p>';
    echo'<script>document.getElementById("podajhaslo").style.display = "block";</script>';
    }
    }
}
if(isset($_GET['type']) && $_GET['type']=="albumy")
{
    $records=array();
    require 'javascript/databaseconnection.php';
    $zapytanie=pg_query($conn, "SELECT a.id,a.tytul,Count(z.opis) as ile,SUM(z.zaakceptowane) as accept,u.login from albumy as a 
    LEFT JOIN zdjecia as z on z.id_albumu=a.id
    INNER JOIN uzytkownicy as u on u.id=a.id_uzytkownika
    WHERE u.id=".$_SESSION['tablica'][7]."
    GROUP BY a.id, a.tytul, u.login
    ORDER BY a.tytul; ");
    if(!isset($_SESSION['warning3']))
$_SESSION['warning3']='';
   echo ' <p class="cwarning">'.$_SESSION['warning3'].'</p>';
   if(isset($_SESSION['warning3']) && $_SESSION['warning3']!='')
	{
		
		unset($_SESSION['warning3']);
		
	}
    if(pg_num_rows($zapytanie)==0)
    echo '<div class="tooltip"><p class="atitle">Brak Albumów</p></div>';
    else{
    while($wynik=pg_fetch_assoc($zapytanie))
    {
        $records[0]=$wynik['id'];
        $records[1]=$wynik['tytul'];
        $records[2]=$wynik['login'];
        
        if($wynik['accept']>0)
        {
              
        echo '<div class="tooltip"><a href="album.php?id='.$records[0].'"><img class="glowne" src="photo/folder.png" height="180"></a><p class="atitle">'.$records[1].'</p><span class="tooltiptext"><button id="pzmien'.$wynik['id'].'" onclick="pokaz('.$wynik['id'].')">Zmień tytuł</button></span><form class="zmiana" method="post" action="kontotytul.php"><span class="tooltiptext"><input type="text" name="nowytytul" class="ntytul" id="ntytul'.$wynik['id'].'"><input type="submit" name="zmien" class="zmien" id="zmien'.$wynik['id'].'" value="Zmień Tytuł" ><input type="submit" name="usun2" id="usun" value="Usuń Album"><input type="hidden" name="id" value="'.$wynik['id'].'"></span></form></div>';
        }
        else
        echo '<div class="tooltip"><img class="glowne" src="photo/folder.png" height="180"><p class="atitle">'.$records[1].'<br>(brak zaakceptowanych zdjęć)</p><span class="tooltiptext"><button id="pzmien'.$wynik['id'].'" onclick="pokaz('.$wynik['id'].')">Zmień tytuł</button></span><form class="zmiana" method="post" action="kontotytul.php"><span class="tooltiptext"><input type="text" name="nowytytul" class="ntytul" id="ntytul'.$wynik['id'].'"><input type="submit" name="zmien" class="zmien" id="zmien'.$wynik['id'].'" value="Zmień Tytuł" ><input type="submit" name="usun2" id="usun" value="Usuń Album"><input type="hidden" name="id" value="'.$wynik['id'].'"></span></form></div>';
   
    } }
    
    
    pg_close($conn);
    
}
if(isset($_GET['type']) && $_GET['type']=="zdjecia" && isset($_GET['id']))
    {
        $records=array();
    require 'javascript/databaseconnection.php';
$zapytanie=pg_query($conn,"SELECT zdjecia.id,zdjecia.opis,zdjecia.opiszdjecia FROM zdjecia
WHERE zdjecia.id_albumu=".$_GET['id'].";");
if(!isset($_SESSION['warning3']))
$_SESSION['warning3']='';
   echo ' <p class="cwarning">'.$_SESSION['warning3'].'</p>';
   if(isset($_SESSION['warning3']) && $_SESSION['warning3']!='')
	{
		
		unset($_SESSION['warning3']);
		
	}
    while($wynik=pg_fetch_assoc($zapytanie))
    {
        echo '<div class="tooltip"><img class="glowne" src="photo/'.$_GET['id'].'/'.$wynik['opis'].'" height="180"><p class="atitle">'.$wynik['opiszdjecia'].'</p><span class="tooltiptext"><button id="pzmien'.$wynik['id'].'" onclick="pokaz('.$wynik['id'].')">Zmień Opis</button></span><form class="zmiana" method="post" action="kontoopis.php"><span class="tooltiptext"><input type="text" name="nowyopis" class="ntytul" id="ntytul'.$wynik['id'].'"><input type="submit" name="zmien" class="zmien" id="zmien'.$wynik['id'].'" value="Zmień Opis" ><input type="submit" name="usun2" id="usun" value="Usuń Zdjęcie"><input type="hidden" name="id" value="'.$wynik['id'].'"><input type="hidden" name="idalbumu" value="'.$_GET['id'].'"><input type="hidden" name="opis" value="'.$wynik['opis'].'"></span></form></div>';
         
    }

    pg_close($conn);
    }
if(isset($_GET['type']) && $_GET['type']=="zdjecia" && (!isset($_GET['id'])))
{
    
    
    $records=array();
    require 'javascript/databaseconnection.php';
    $zapytanie=pg_query($conn, "SELECT a.id,a.tytul,Count(z.opis) as ile,SUM(z.zaakceptowane) as accept,u.login from albumy as a 
    LEFT JOIN zdjecia as z on z.id_albumu=a.id
    INNER JOIN uzytkownicy as u on u.id=a.id_uzytkownika
    WHERE u.id=".$_SESSION['tablica'][7]."
    GROUP BY a.id, a.tytul, u.login
    ORDER BY a.tytul; ");

    if(pg_num_rows($zapytanie)==0)
    echo '<div class="tooltip"><p class="atitle">Brak Albumów</p></div>';
    else{
    if(!isset($_SESSION['warning3']))
$_SESSION['warning3']='';
$_SESSION['warning3']='Wybierz album';
   echo ' <p class="cwarning">'.$_SESSION['warning3'].'</p>';
   if(isset($_SESSION['warning3']) && $_SESSION['warning3']!='')
	{
		
		unset($_SESSION['warning3']);
		
	}
    
   

    
    while($wynik=pg_fetch_assoc($zapytanie))
    {
        $records[0]=$wynik['id'];
        $records[1]=$wynik['tytul'];
        $records[2]=$wynik['login'];
        
        if($wynik['accept']>0)
        {
              
        echo '<div class="tooltip"><a href="konto.php?type=zdjecia&id='.$records[0].'"><img class="glowne" src="photo/folder.png" height="180"></a><p class="atitle">'.$records[1].'</p></div>';
        }
        else if($wynik['ile']==0)
        echo '<div class="tooltip"><img class="glowne" src="photo/folder.png" height="180"><p class="atitle">'.$records[1].'<br>(brak  zdjęć)</p></div>';
        else 
        echo '<div class="tooltip"><a href="konto.php?type=zdjecia&id='.$records[0].'"><img class="glowne" src="photo/folder.png" height="180"></a><p class="atitle">'.$records[1].'<br>(brak zaakceptowanych zdjęć)</p></div>';
   
    } }
    
    pg_close($conn);

}
if(isset($_GET['type']) && $_GET['type']=="usun")
{
    require 'javascript/databaseconnection.php';
    $zapytanie=pg_query($conn,"SELECT albumy.id FROM albumy
    WHERE albumy.id_uzytkownika=".$_SESSION['tablica'][7].";");
    while($wynik=pg_fetch_assoc($zapytanie))
    {
        $directory="photo/".$wynik['id']."";
        
        deleteAll(''.$directory.'');
    }
    

    $sql1="DELETE FROM zdjecia_oceny WHERE id_uzytkownika=".$_SESSION['tablica'][7]."";
    $sql2="DELETE FROM zdjecia_komentarze WHERE id_uzytkownika=".$_SESSION['tablica'][7]."";
    $sql3="DELETE zdjecia
    FROM zdjecia
    INNER JOIN albumy on zdjecia.id_albumu=albumy.id
    WHERE albumy.id_uzytkownika=".$_SESSION['tablica'][7]."";
    $sql4="DELETE FROM albumy WHERE id_uzytkownika=".$_SESSION['tablica'][7]."";
    $sql5="DELETE FROM uzytkownicy WHERE id=".$_SESSION['tablica'][7]."";
    pg_query($conn,$sql1);
    pg_query($conn,$sql2);
    pg_query($conn,$sql3);
    pg_query($conn,$sql4);
    pg_query($conn,$sql5);
    
    pg_close($conn);
    header('Location:wyloguj.php');
}
function deleteAll($dir) {
    foreach(glob($dir . '/*') as $file) {
    if(is_dir($file))
    deleteAll($file);
    else
    unlink($file);
    }
    rmdir($dir);
    }
?>
<script>function pokaz(numer)
{
    document.getElementById('ntytul'+numer).style.display = "block";
    document.getElementById("zmien"+numer).style.display = "block";
    document.getElementById("pzmien"+numer).style.display = "none";
}</script>