<?php
session_start();
require '../javascript/databaseconnection.php';
if(!isset($_GET['type']))
{

    echo"<p id='wskazowka'> <- Menu</p>";
    echo'<p id="tytulbeztype">PANEL ADMINISTRACYJNY</p>
    <p class="wybierz"> Wybierz czynność z menu</p>';
}
if(isset($_GET['type']) && $_GET['type']=='albumy')
{
    echo '<p id="tytullisty">Lista albumów : </p>';
    if(!isset($_SESSION['warning3']))
$_SESSION['warning3']='';
   echo ' <p class="cwarning">'.$_SESSION['warning3'].'</p>';
   if(isset($_SESSION['warning3']) && $_SESSION['warning3']!='')
	{
		
		unset($_SESSION['warning3']);
		
	}
    require '../javascript/databaseconnection.php';
    if(isset($_GET['numer']))
    {
        $_SESSION['strona']=($_GET['numer']-1)*30;
    }
    else
        $_SESSION['strona']=0;


        $records=array();
        $stronyzapytanie=pg_query($conn,"SELECT COUNT(*) as ile from (SELECT a.id,a.tytul,Count(z.opis) as ile,SUM(z.zaakceptowane) as accept from albumy as a 
        LEFT JOIN zdjecia as z on z.id_albumu=a.id
        GROUP BY a.id
        ORDER BY a.tytul) n");
       $row=pg_fetch_assoc($stronyzapytanie);
       $numerstron=ceil($row['ile']/30);
        
       
       $zapytanie = pg_query($conn, "
       SELECT a.id, a.tytul, a.data, COUNT(z.opis) as ile, SUM(z.zaakceptowane) as accept, 
              u.login, COUNT(z.opis) - SUM(z.zaakceptowane) as niezaakceptowane 
       FROM albumy as a 
       LEFT JOIN zdjecia as z ON z.id_albumu = a.id 
       INNER JOIN uzytkownicy as u ON u.id = a.id_uzytkownika 
       GROUP BY a.id, a.tytul, a.data, u.login 
       ORDER BY niezaakceptowane DESC, a.id 
       LIMIT 30 OFFSET " . $_SESSION['strona']
        );
       

       while($wynik=pg_fetch_assoc($zapytanie)) 
        {
            
            if($wynik['niezaakceptowane']!=0)
            echo '<div class="albumlist"><img src="../photo/folder.png" height="38" align="left"><p class="listelement">'.$wynik['tytul'].' '.str_repeat("&nbsp;", 2).'niezaakceptowane :'.$wynik['niezaakceptowane'].' '.str_repeat("&nbsp;", 2).'   Autor : '.$wynik['login'].' '.str_repeat("&nbsp;", 5).'   Data utworzenia : '.$wynik['data'].'</p> <div class="funkcje">  <button id="tytulzmien'.$wynik['id'].'" onclick="pokaz2('.$wynik['id'].')">Zmień tytuł</button><form class="zmiana3" method="post" action="admintytul.php"><input type="text" name="nowytytul" class="ntytul" id="ntytul'.$wynik['id'].'"><input type="submit" name="zmien" class="zmien" id="zmien'.$wynik['id'].'" value="Zmień Tytuł" ><input type="submit" name="usun2" id="usun" value="Usuń Album"><input type="hidden" name="id" value="'.$wynik['id'].'"></form></div></div>';
           else
            echo '<div class="albumlist"><img src="../photo/folder.png" height="38" align="left"><p class="listelement">'.$wynik['tytul'].' '.str_repeat("&nbsp;", 2).'    Autor : '.$wynik['login'].' '.str_repeat("&nbsp;", 5).'   Data utworzenia : '.$wynik['data'].'</p> <div class="funkcje">  <button id="tytulzmien'.$wynik['id'].'" onclick="pokaz2('.$wynik['id'].')">Zmień tytuł</button><form class="zmiana3" method="post" action="admintytul.php"><input type="text" name="nowytytul" class="ntytul" id="ntytul'.$wynik['id'].'"><input type="submit" name="zmien" class="zmien" id="zmien'.$wynik['id'].'" value="Zmień Tytuł" ><input type="submit" name="usun2" id="usun" value="Usuń Album"><input type="hidden" name="id" value="'.$wynik['id'].'"></form></div></div>';
            
    
        }
      

        if(isset($numerstron) && $numerstron>1)
{
echo'<form id="numery" method="post" action="paginalbum.php">';

for($i=1;$i<=$numerstron;$i++)
{
	echo '<input class="paging" type="submit" name="numer" value='.$i.'>';
}

echo'</form>';}
}

if(isset($_GET['type']) && $_GET['type']=='zdjecia')
{
    if(!isset($_SESSION['warning3']))
$_SESSION['warning3']='';
   echo ' <p class="cwarning">'.$_SESSION['warning3'].'</p>';
   if(isset($_SESSION['warning3']) && $_SESSION['warning3']!='')
	{
		
		unset($_SESSION['warning3']);
		
	}
require '../javascript/databaseconnection.php';
if(!isset($_GET['co']))
{  
$ilenie=pg_query($conn,"SELECT COUNT(zdjecia.id) from zdjecia
WHERE zdjecia.zaakceptowane=0;");
if(pg_num_rows($ilenie)==0)
{
    header("Location:index.php?type=zdjecia&co=wszystko");
}
else
{
    echo '<div class="lubdiv"><form method="post" action="zdjeciabuttons.php"><input type="submit" name="nie" class="lub" value="Wyświetl tylko niezaakceptowane zdjęcia">
    <input type="submit" name="tak" class="lub" value="Wyświetl albumy ze zdjęciami"></form></div>
    ';
} 
}

if(isset($_GET['co']) && $_GET['co']=='wszystko')
{
    if(isset($_GET['id']))
    {
        
        $zapytanie=pg_query($conn,"SELECT zdjecia.id,zdjecia.opis,zdjecia.opiszdjecia,zdjecia.zaakceptowane,albumy.tytul,albumy.id as albumid from zdjecia
        INNER JOIN albumy on zdjecia.id_albumu=albumy.id
        WHERE albumy.id=".$_GET['id'].";") ;
         echo'<div id="duzezdjecie"><img id="duzyimage" height="600" align="center" onclick="powrot()"></div>';
         echo'<div id="listazdjec">';
          while($wynik=pg_fetch_assoc($zapytanie)) 
          {
              if($wynik['zaakceptowane']==0)
              echo '<div class="albumlistz"><img class="zdj" title="Kliknij na zdjęcie by powiększyć" id="'.$wynik['id'].'" src="../photo/'.$wynik['albumid'].'/'.$wynik['opis'].'" height="180" align="left" onclick="wiekszy(\''.$wynik['albumid'].'\',\''.$wynik['opis'].'\')"><p class="listelement">Nazwa zdjęcia :'.str_repeat("&nbsp;", 1).''.$wynik['opiszdjecia'].' '.str_repeat("&nbsp;", 2).'Album :'.str_repeat("&nbsp;", 1).''.$wynik['tytul'].'    </p> <div class="funkcje">  <form class="zmiana2" method="post" action="adminzdjecia.php"><input type="submit" name="zaakceptuj" class="zmien" id="zaakceptuj" value="Zaakceptuj Zdjęcie" ><input type="submit" name="usun2" id="usun" value="Usuń Zdjęcie"><input type="hidden" name="id" value="'.$wynik['id'].'"><input type="hidden" name="opis" value="'.$wynik['opis'].'"><input type="hidden" name="idalbumu" value="'.$wynik['albumid'].'"></form></div></div>';
              else
              echo '<div class="albumlistz"><img class="zdj" title="Kliknij na zdjęcie by powiększyć" id="'.$wynik['id'].'" src="../photo/'.$wynik['albumid'].'/'.$wynik['opis'].'" height="180" align="left" onclick="wiekszy(\''.$wynik['albumid'].'\',\''.$wynik['opis'].'\')"><p class="listelement">Nazwa zdjęcia :'.str_repeat("&nbsp;", 1).''.$wynik['opiszdjecia'].' '.str_repeat("&nbsp;", 2).'Album :'.str_repeat("&nbsp;", 1).''.$wynik['tytul'].'    </p> <div class="funkcje">  <form class="zmiana2" method="post" action="adminzdjecia.php"><input type="submit" name="usun2" id="usun" value="Usuń Zdjęcie"><input type="hidden" name="id" value="'.$wynik['id'].'"><input type="hidden" name="opis" value="'.$wynik['opis'].'"><input type="hidden" name="idalbumu" value="'.$wynik['albumid'].'"></form></div></div>';
             
              
              
      
          }
          echo'</div>';
    }
    else{

    
    $zapytanie=pg_query($conn,"SELECT albumy.id,albumy.tytul,COUNT(zdjecia.id) as ile from albumy
    INNER JOIN zdjecia on zdjecia.id_albumu=albumy.id
    GROUP BY albumy.id;");
    if(!isset($_SESSION['warning3']))
    $_SESSION['warning3']='';
    $_SESSION['warning3']='Wybierz album';
       echo ' <p class="cwarning">'.$_SESSION['warning3'].'</p>';
       if(isset($_SESSION['warning3']) && $_SESSION['warning3']!='')
        {
            
            unset($_SESSION['warning3']);
            
        }
        echo '<div id="wysrodkuj">';
        while($wynik=pg_fetch_assoc($zapytanie))
        {
            echo '<div class="tooltip"><a href="index.php?type=zdjecia&co=wszystko&id='.$wynik['id'].'"><img class="glowne" src="../photo/folder.png" height="180"></a><p class="atitle">'.$wynik['tytul'].'</p></div>';
        }
        echo'</div>';}
}
if(isset($_GET['co']) && $_GET['co']=='tylko')
{
    
    $zapytanie=pg_query($conn, " SELECT zdjecia.id,zdjecia.opis,zdjecia.opiszdjecia,albumy.tytul,albumy.id as albumid from zdjecia
    INNER JOIN albumy on zdjecia.id_albumu=albumy.id
    WHERE zdjecia.zaakceptowane=0");
    echo'<div id="duzezdjecie"><img id="duzyimage" height="600" align="center" onclick="powrot()"></div>';
    echo'<div id="listazdjec">';
     while($wynik=pg_fetch_assoc($zapytanie)) 
     {
         
         
         echo '<div class="albumlistz"><img class="zdj" title="Kliknij na zdjęcie by powiększyć" id="'.$wynik['id'].'" src="../photo/'.$wynik['albumid'].'/'.$wynik['opis'].'" height="180" align="left" onclick="wiekszy(\''.$wynik['albumid'].'\',\''.$wynik['opis'].'\')"><p class="listelement">Nazwa zdjęcia :'.str_repeat("&nbsp;", 1).''.$wynik['opiszdjecia'].' '.str_repeat("&nbsp;", 2).'Album :'.str_repeat("&nbsp;", 1).''.$wynik['tytul'].'    </p> <div class="funkcje">  <form class="zmiana2" method="post" action="adminzdjecia.php"><input type="submit" name="zaakceptuj" class="zmien" id="zaakceptuj" value="Zaakceptuj Zdjęcie" ><input type="submit" name="usun2" id="usun" value="Usuń Zdjęcie"><input type="hidden" name="id" value="'.$wynik['id'].'"><input type="hidden" name="opis" value="'.$wynik['opis'].'"><input type="hidden" name="idalbumu" value="'.$wynik['albumid'].'"></form></div></div>';
        
         
         
 
     }
     echo'</div>';
}
pg_close($conn);
}

if(isset($_GET['type']) && $_GET['type']=='kom')
{
    if(!isset($_SESSION['warning3']))
$_SESSION['warning3']='';
   echo ' <p class="cwarning">'.$_SESSION['warning3'].'</p>';
   if(isset($_SESSION['warning3']) && $_SESSION['warning3']!='')
	{
		
		unset($_SESSION['warning3']);
		
	}

    require '../javascript/databaseconnection.php';
if(!isset($_GET['co']))
{  
$ilenie=pg_query($conn,"SELECT COUNT(zdjecia_komentarze.id) from zdjecia_komentarze
WHERE zdjecia_komentarze.zaakceptowany=0;");
if(pg_num_rows($ilenie)==0)
{
    header("Location:index.php?type=kom&co=wszystko");
}
else
{
    echo '<div class="lubdiv"><form method="post" action="kombuttons.php"><input type="submit" name="nie" class="lub" value="Wyświetl tylko niezaakceptowane komentarze">
    <input type="submit" name="tak" class="lub" value="Wyświetl wszystkie komentarze"></form></div>
    ';
} 
}

if(isset($_GET['co']) && $_GET['co']=='wszystko')
{
    
    if($_SESSION['tablica'][5]=='administrator')
    {
        
        $zapytanie=pg_query($conn,"SELECT zdjecia_komentarze.id,zdjecia_komentarze.komentarz,zdjecia_komentarze.zaakceptowany,zdjecia.opiszdjecia,uzytkownicy.login from zdjecia_komentarze
        INNER JOIN zdjecia on zdjecia_komentarze.id_zdjecia=zdjecia.id
        INNER JOIN uzytkownicy on zdjecia_komentarze.id_uzytkownika=uzytkownicy.id
        ORDER BY zdjecia_komentarze.zaakceptowany;") ;
         echo'<div id="listazdjec">';
         $i=0;
          while($wynik=pg_fetch_assoc($zapytanie)) 
          {
              
              if($wynik['zaakceptowany']==0)
              echo '<div class="komlist"><form class="zmiana2" method="post" action="adminzkom.php" id="nowykom"><input type="text" class="textarea" name="kom'.$i.'" value="'.$wynik['komentarz'].'"><input type="submit" name="zmienkomentarz" id="zmienkomentarz" value="Potwierdź Edycje Komentarza"><p class="listelement">Zdjęcie :'.str_repeat("&nbsp;", 1).''.$wynik['opiszdjecia'].' '.str_repeat("&nbsp;", 2).'Autor :'.str_repeat("&nbsp;", 1).''.$wynik['login'].'    </p> <div class="funkcje">  <input type="submit" name="zaakceptuj" class="zmien" id="zaakceptuj" value="Zaakceptuj Komentarz" ><input type="submit" name="usun2" id="usun" value="Usuń Komentarz"><input type="hidden" name="id" value='.$wynik['id'].'><input type="hidden" name="textareanumber" value="'.$i.'"></form></div></div>';
              else
              echo '<div class="komlist"><form class="zmiana2" method="post" action="adminzkom.php" id="nowykom"><input type="text" class="textarea" name="kom'.$i.'" value="'.$wynik['komentarz'].'"><input type="submit" name="zmienkomentarz" id="zmienkomentarz" value="Potwierdź Edycje Komentarza"><p class="listelement">Zdjęcie :'.str_repeat("&nbsp;", 1).''.$wynik['opiszdjecia'].' '.str_repeat("&nbsp;", 2).'Autor :'.str_repeat("&nbsp;", 1).''.$wynik['login'].'    </p> <div class="funkcje">  <input type="submit" name="usun2" id="usun" value="Usuń Komentarz"><input type="hidden" name="id" value="'.$wynik['id'].'"><input type="hidden" name="textareanumber" value='.$i.'></form></div></div>';
              
             
              $i++;
              
      
          }
          echo'</div>';
    
        }
        else if($_SESSION['tablica'][5]=='moderator') 
        {
            $zapytanie=pg_query($conn,"SELECT zdjecia_komentarze.id,zdjecia_komentarze.komentarz,zdjecia_komentarze.zaakceptowany,zdjecia.opiszdjecia,uzytkownicy.login from zdjecia_komentarze
            INNER JOIN zdjecia on zdjecia_komentarze.id_zdjecia=zdjecia.id
            INNER JOIN uzytkownicy on zdjecia_komentarze.id_uzytkownika=uzytkownicy.id
            ORDER BY zdjecia_komentarze.zaakceptowany;") ;
             echo'<div id="listazdjec">';
             $i=0;
              while($wynik=pg_fetch_assoc($zapytanie)) 
              {
                  
                  if($wynik['zaakceptowany']==0)
                  echo '<div class="komlist"><form class="zmiana2" method="post" action="adminzkom.php" id="nowykom"><input type="text" readonly="readonly" class="textarea" name="kom'.$i.'" value="'.$wynik['komentarz'].'"><p class="listelement">Zdjęcie :'.str_repeat("&nbsp;", 1).''.$wynik['opiszdjecia'].' '.str_repeat("&nbsp;", 2).'Autor :'.str_repeat("&nbsp;", 1).''.$wynik['login'].'    </p> <div class="funkcje">  <input type="submit" name="zaakceptuj" class="zmien" id="zaakceptuj" value="Zaakceptuj Komentarz" ><input type="submit" name="usun2" id="usun" value="Usuń Komentarz"><input type="hidden" name="id" value='.$wynik['id'].'><input type="hidden" name="textareanumber" value="'.$i.'"></form></div></div>';
                  else
                  echo '<div class="komlist"><form class="zmiana2" method="post" action="adminzkom.php" id="nowykom"><input type="text" readonly="readonly" class="textarea" name="kom'.$i.'" value="'.$wynik['komentarz'].'"><p class="listelement">Zdjęcie :'.str_repeat("&nbsp;", 1).''.$wynik['opiszdjecia'].' '.str_repeat("&nbsp;", 2).'Autor :'.str_repeat("&nbsp;", 1).''.$wynik['login'].'    </p> <div class="funkcje">  <input type="submit" name="usun2" id="usun" value="Usuń Komentarz"><input type="hidden" name="id" value="'.$wynik['id'].'"><input type="hidden" name="textareanumber" value='.$i.'></form></div></div>';
                  
                 
                  $i++;
                  
          
              }
              echo'</div>';  
        }
}
if(isset($_GET['co']) && $_GET['co']=='tylko')
{
    if($_SESSION['tablica'][5]=='administrator')
    {
    $zapytanie=pg_query($conn,"SELECT zdjecia_komentarze.id,zdjecia_komentarze.komentarz,zdjecia_komentarze.zaakceptowany,zdjecia.opiszdjecia,uzytkownicy.login from zdjecia_komentarze
        INNER JOIN zdjecia on zdjecia_komentarze.id_zdjecia=zdjecia.id
        INNER JOIN uzytkownicy on zdjecia_komentarze.id_uzytkownika=uzytkownicy.id
        WHERE zdjecia_komentarze.zaakceptowany=0") ;
         echo'<div id="listazdjec">';
         $i=0;
          while($wynik=pg_fetch_assoc($zapytanie)) 
          {
              
              
              echo '<div class="komlist"><form class="zmiana2" method="post" action="adminzkom.php" id="nowykom"><input type="text" class="textarea" name="kom'.$i.'" value="'.$wynik['komentarz'].'"><input type="submit" name="zmienkomentarz" id="zmienkomentarz" value="Potwierdź Edycje Komentarza"><p class="listelement">Zdjęcie :'.str_repeat("&nbsp;", 1).''.$wynik['opiszdjecia'].' '.str_repeat("&nbsp;", 2).'Autor :'.str_repeat("&nbsp;", 1).''.$wynik['login'].'    </p> <div class="funkcje">  <input type="submit" name="zaakceptuj" class="zmien" id="zaakceptuj" value="Zaakceptuj Komentarz" ><input type="submit" name="usun2" id="usun" value="Usuń Komentarz"><input type="hidden" name="id" value='.$wynik['id'].'><input type="hidden" name="textareanumber" value="'.$i.'"></form></div></div>';
              
              
              
             
              $i++;
              
      
          }
          echo'</div>';
        }
        else if($_SESSION['tablica'][5]=='moderator') 
        {
            $zapytanie=pg_query($conn,"SELECT zdjecia_komentarze.id,zdjecia_komentarze.komentarz,zdjecia_komentarze.zaakceptowany,zdjecia.opiszdjecia,uzytkownicy.login from zdjecia_komentarze
        INNER JOIN zdjecia on zdjecia_komentarze.id_zdjecia=zdjecia.id
        INNER JOIN uzytkownicy on zdjecia_komentarze.id_uzytkownika=uzytkownicy.id
        WHERE zdjecia_komentarze.zaakceptowany=0") ;
         echo'<div id="listazdjec">';
         $i=0;
          while($wynik=pg_fetch_assoc($zapytanie)) 
          {
              
              
              echo '<div class="komlist"><form class="zmiana2" method="post" action="adminzkom.php" id="nowykom"><input type="text" readonly="readonly" class="textarea" name="kom'.$i.'" value="'.$wynik['komentarz'].'"><p class="listelement">Zdjęcie :'.str_repeat("&nbsp;", 1).''.$wynik['opiszdjecia'].' '.str_repeat("&nbsp;", 2).'Autor :'.str_repeat("&nbsp;", 1).''.$wynik['login'].'    </p> <div class="funkcje">  <input type="submit" name="zaakceptuj" class="zmien" id="zaakceptuj" value="Zaakceptuj Komentarz" ><input type="submit" name="usun2" id="usun" value="Usuń Komentarz"><input type="hidden" name="id" value='.$wynik['id'].'><input type="hidden" name="textareanumber" value="'.$i.'"></form></div></div>';
              
              
              
             
              $i++;
              
      
          }
          echo'</div>'; 
        }
}
pg_close($conn);
}
if(isset($_GET['type']) && $_GET['type']=='users')
{
    if(!isset($_GET['co']))
    {
    if(!isset($_SESSION['warning3']))
$_SESSION['warning3']='';
   echo ' <p class="cwarning">'.$_SESSION['warning3'].'</p>';
   if(isset($_SESSION['warning3']) && $_SESSION['warning3']!='')
	{
		
		unset($_SESSION['warning3']);
		
	}

    

    echo '<div class="lubdiv2"><form method="post" action="usersbuttons.php"><input type="submit" name="zwykly" class="lub" value="Wyświetl użytkowników grupy: Użytkownicy">
    <input type="submit" name="mod" class="lub" value="Wyświetl użytkowników grupy: Moderatorzy">
    <input type="submit" name="admin" class="lub" value="Wyświetl użytkowników grupy: Administratorzy">
    <input type="submit" name="tak" class="lub" value="Wyświetl wszystkich użytkowników"></form></div>
    ';
    }
    if(isset($_GET['co']) && $_GET['co']=='zwykly')
    {
        require '../javascript/databaseconnection.php';
        $zapytanie=pg_query($conn,"SELECT uzytkownicy.id,uzytkownicy.login,uzytkownicy.uprawnienia,uzytkownicy.aktywny FROM uzytkownicy
        WHERE uzytkownicy.uprawnienia='użytkownik';");
        echo'<div id="listazdjec">';
        while($wynik=pg_fetch_assoc($zapytanie)) 
        {
            if($wynik['aktywny']==1)
            echo '<div class="komlist"><p class="listelement2">Użytkownik :'.str_repeat("&nbsp;", 1).''.$wynik['login'].' '.str_repeat("&nbsp;", 2).'Aktywny :'.str_repeat("&nbsp;", 1).'Tak    </p><button id="uprawnieniazmien'.$wynik['id'].'" onclick="pokaz3('.$wynik['id'].')">Zmień uprawnienia</button> <form class="zmiana2" method="post" action="adminusers.php" id="nowykom"><div class="funkcje">  <select name="wybor" id="wybor'.$wynik['id'].'"class="zmien3"><option value="moderator">moderator</option><option value="administrator">administrator</option></select><input type="submit" name="zmien" class="zmien" id="zmien'.$wynik['id'].'" value="Zmień " ><input type="submit" name="blokuj" class="zmien" id="zaakceptuj" value="Zablokuj" ><input type="submit" name="usun2" id="usun" value="Usuń Konto"><input type="hidden" name="id" value='.$wynik['id'].'></form></div></div>';
            else
            echo '<div class="komlist"><p class="listelement2">Użytkownik :'.str_repeat("&nbsp;", 1).''.$wynik['login'].' '.str_repeat("&nbsp;", 2).'Aktywny :'.str_repeat("&nbsp;", 1).'Nie    </p><button id="uprawnieniazmien'.$wynik['id'].'" onclick="pokaz3('.$wynik['id'].')">Zmień uprawnienia</button> <form class="zmiana2" method="post" action="adminusers.php" id="nowykom"><div class="funkcje">  <select name="wybor" id="wybor'.$wynik['id'].'"class="zmien3"><option value="moderator">moderator</option><option value="administrator">administrator</option></select><input type="submit" name="zmien" class="zmien" id="zmien'.$wynik['id'].'" value="Zmień " ><input type="submit" name="odblokuj" class="zmien" id="zaakceptuj" value="Odblokuj" ><input type="submit" name="usun2" id="usun" value="Usuń Konto"><input type="hidden" name="id" value='.$wynik['id'].'></form></div></div>';
        }
        echo'</div>';
        pg_close($conn); 

    }
    if(isset($_GET['co']) && $_GET['co']=='mod')
    {
        require '../javascript/databaseconnection.php';
        $zapytanie=pg_query($conn,"SELECT uzytkownicy.id,uzytkownicy.login,uzytkownicy.uprawnienia,uzytkownicy.aktywny FROM uzytkownicy
        WHERE uzytkownicy.uprawnienia='moderator';");
        echo'<div id="listazdjec">';
        while($wynik=pg_fetch_assoc($zapytanie)) 
        {
            if($wynik['aktywny']==1)
            echo '<div class="komlist"><p class="listelement2">Moderator :'.str_repeat("&nbsp;", 1).''.$wynik['login'].' '.str_repeat("&nbsp;", 2).'Aktywny :'.str_repeat("&nbsp;", 1).'Tak    </p><button id="uprawnieniazmien'.$wynik['id'].'" onclick="pokaz3('.$wynik['id'].')">Zmień uprawnienia</button> <form class="zmiana2" method="post" action="adminusers.php" id="nowykom"><div class="funkcje">  <select name="wybor" id="wybor'.$wynik['id'].'"class="zmien3"><option value="użytkownik">użytkownik</option><option value="administrator">administrator</option></select><input type="submit" name="zmien" class="zmien" id="zmien'.$wynik['id'].'" value="Zmień " ><input type="submit" name="blokuj" class="zmien" id="zaakceptuj" value="Zablokuj" ><input type="submit" name="usun2" id="usun" value="Usuń Konto"><input type="hidden" name="id" value='.$wynik['id'].'></form></div></div>';
            else
            echo '<div class="komlist"><p class="listelement2">Moderator :'.str_repeat("&nbsp;", 1).''.$wynik['login'].' '.str_repeat("&nbsp;", 2).'Aktywny :'.str_repeat("&nbsp;", 1).'Nie    </p><button id="uprawnieniazmien'.$wynik['id'].'" onclick="pokaz3('.$wynik['id'].')">Zmień uprawnienia</button> <form class="zmiana2" method="post" action="adminusers.php" id="nowykom"><div class="funkcje">  <select name="wybor" id="wybor'.$wynik['id'].'"class="zmien"><option value="użytkownik">użytkownik</option><option value="administrator">administrator</option></select><input type="submit" name="zmien" class="zmien" id="zmien'.$wynik['id'].'" value="Zmień " ><input type="submit" name="odblokuj" class="zmien" id="zaakceptuj" value="Odblokuj" ><input type="submit" name="usun2" id="usun" value="Usuń Konto"><input type="hidden" name="id" value='.$wynik['id'].'></form></div></div>';
        }
        echo'</div>';
        pg_close($conn); 
    }
    if(isset($_GET['co']) && $_GET['co']=='admin')
    {
        require '../javascript/databaseconnection.php';
        $zapytanie=pg_query($conn,"SELECT uzytkownicy.id,uzytkownicy.login,uzytkownicy.uprawnienia,uzytkownicy.aktywny FROM uzytkownicy
        WHERE uzytkownicy.uprawnienia='administrator' AND uzytkownicy.id!=".$_SESSION['tablica'][7].";");
        echo'<div id="listazdjec">';
        while($wynik=pg_fetch_assoc($zapytanie)) 
        {
            if($wynik['aktywny']==1)
            echo '<div class="komlist"><p class="listelement2">Administrator :'.str_repeat("&nbsp;", 1).''.$wynik['login'].' '.str_repeat("&nbsp;", 2).'Aktywny :'.str_repeat("&nbsp;", 1).'Tak    </p><button id="uprawnieniazmien'.$wynik['id'].'" onclick="pokaz3('.$wynik['id'].')">Zmień uprawnienia</button> <form class="zmiana2" method="post" action="adminusers.php" id="nowykom"><div class="funkcje">  <select name="wybor" id="wybor'.$wynik['id'].'"class="zmien3"><option value="użytkownik">użytkownik</option><option value="moderator">moderator</option></select><input type="submit" name="zmien" class="zmien" id="zmien'.$wynik['id'].'" value="Zmień " ><input type="submit" name="blokuj" class="zmien" id="zaakceptuj" value="Zablokuj" ><input type="submit" name="usun2" id="usun" value="Usuń Konto"><input type="hidden" name="id" value='.$wynik['id'].'></form></div></div>';
            else
            echo '<div class="komlist"><p class="listelement2">Administrator :'.str_repeat("&nbsp;", 1).''.$wynik['login'].' '.str_repeat("&nbsp;", 2).'Aktywny :'.str_repeat("&nbsp;", 1).'Nie    </p><button id="uprawnieniazmien'.$wynik['id'].'" onclick="pokaz3('.$wynik['id'].')">Zmień uprawnienia</button> <form class="zmiana2" method="post" action="adminusers.php" id="nowykom"><div class="funkcje">  <select name="wybor" id="wybor'.$wynik['id'].'"class="zmien"><option value="użytkownik">użytkownik</option><option value="moderator">moderator</option></select><input type="submit" name="zmien" class="zmien" id="zmien'.$wynik['id'].'" value="Zmień " ><input type="submit" name="odblokuj" class="zmien" id="zaakceptuj" value="Odblokuj" ><input type="submit" name="usun2" id="usun" value="Usuń Konto"><input type="hidden" name="id" value='.$wynik['id'].'></form></div></div>';
        }
        $zapytanie2=pg_query($conn,"SELECT uzytkownicy.id,uzytkownicy.login,uzytkownicy.uprawnienia,uzytkownicy.aktywny FROM uzytkownicy
        WHERE uzytkownicy.id=".$_SESSION['tablica'][7].";");
        $wynik2=pg_fetch_assoc($zapytanie2);
        echo '<div class="komlist"><p class="listelement2">Administrator :'.str_repeat("&nbsp;", 1).''.$wynik2['login'].' </p></div></div>';
        echo'</div>';
        pg_close($conn); 
    }
    if(isset($_GET['co']) && $_GET['co']=='wszystko')
    {
        require '../javascript/databaseconnection.php';
        $zapytanie=pg_query($conn,"SELECT uzytkownicy.id,uzytkownicy.login,uzytkownicy.uprawnienia,uzytkownicy.aktywny FROM uzytkownicy WHERE uzytkownicy.id!=".$_SESSION['tablica'][7]." ORDER BY uzytkownicy.uprawnienia;");
        echo'<div id="listazdjec">';
        while($wynik=pg_fetch_assoc($zapytanie)) 
        {
            if($wynik['uprawnienia']=='użytkownik')
            {
                if($wynik['aktywny']==1)
            echo '<div class="komlist"><p class="listelement2">Użytkownik :'.str_repeat("&nbsp;", 1).''.$wynik['login'].' '.str_repeat("&nbsp;", 2).'Aktywny :'.str_repeat("&nbsp;", 1).'Tak    </p><button id="uprawnieniazmien'.$wynik['id'].'" onclick="pokaz3('.$wynik['id'].')">Zmień uprawnienia</button> <form class="zmiana2" method="post" action="adminusers.php" id="nowykom"><div class="funkcje">  <select name="wybor" id="wybor'.$wynik['id'].'"class="zmien3"><option value="moderator">moderator</option><option value="administrator">administrator</option></select><input type="submit" name="zmien" class="zmien" id="zmien'.$wynik['id'].'" value="Zmień " ><input type="submit" name="blokuj" class="zmien" id="zaakceptuj" value="Zablokuj" ><input type="submit" name="usun2" id="usun" value="Usuń Konto"><input type="hidden" name="id" value='.$wynik['id'].'></form></div></div>';
            else
            echo '<div class="komlist"><p class="listelement2">Użytkownik :'.str_repeat("&nbsp;", 1).''.$wynik['login'].' '.str_repeat("&nbsp;", 2).'Aktywny :'.str_repeat("&nbsp;", 1).'Nie    </p><button id="uprawnieniazmien'.$wynik['id'].'" onclick="pokaz3('.$wynik['id'].')">Zmień uprawnienia</button> <form class="zmiana2" method="post" action="adminusers.php" id="nowykom"><div class="funkcje">  <select name="wybor" id="wybor'.$wynik['id'].'"class="zmien"><option value="moderator">moderator</option><option value="administrator">administrator</option></select><input type="submit" name="zmien" class="zmien" id="zmien'.$wynik['id'].'" value="Zmień " ><input type="submit" name="odblokuj" class="zmien" id="zaakceptuj" value="Odblokuj" ><input type="submit" name="usun2" id="usun" value="Usuń Konto"><input type="hidden" name="id" value='.$wynik['id'].'></form></div></div>'; 
            }
            else if($wynik['uprawnienia']=='moderator')
            {
                if($wynik['aktywny']==1)
            echo '<div class="komlist"><p class="listelement2">Moderator :'.str_repeat("&nbsp;", 1).''.$wynik['login'].' '.str_repeat("&nbsp;", 2).'Aktywny :'.str_repeat("&nbsp;", 1).'Tak    </p><button id="uprawnieniazmien'.$wynik['id'].'" onclick="pokaz3('.$wynik['id'].')">Zmień uprawnienia</button> <form class="zmiana2" method="post" action="adminusers.php" id="nowykom"><div class="funkcje">  <select name="wybor" id="wybor'.$wynik['id'].'"class="zmien3"><option value="użytkownik">użytkownik</option><option value="administrator">administrator</option></select><input type="submit" name="zmien" class="zmien" id="zmien'.$wynik['id'].'" value="Zmień " ><input type="submit" name="blokuj" class="zmien" id="zaakceptuj" value="Zablokuj" ><input type="submit" name="usun2" id="usun" value="Usuń Konto"><input type="hidden" name="id" value='.$wynik['id'].'></form></div></div>';
            else 
            echo '<div class="komlist"><p class="listelement2">Moderator :'.str_repeat("&nbsp;", 1).''.$wynik['login'].' '.str_repeat("&nbsp;", 2).'Aktywny :'.str_repeat("&nbsp;", 1).'Nie    </p><button id="uprawnieniazmien'.$wynik['id'].'" onclick="pokaz3('.$wynik['id'].')">Zmień uprawnienia</button> <form class="zmiana2" method="post" action="adminusers.php" id="nowykom"><div class="funkcje">  <select name="wybor" id="wybor'.$wynik['id'].'"class="zmien"><option value="użytkownik">użytkownik</option><option value="administrator">administrator</option></select><input type="submit" name="zmien" class="zmien" id="zmien'.$wynik['id'].'" value="Zmień " ><input type="submit" name="odblokuj" class="zmien" id="zaakceptuj" value="Odblokuj" ><input type="submit" name="usun2" id="usun" value="Usuń Konto"><input type="hidden" name="id" value='.$wynik['id'].'></form></div></div>';
            }
            else if($wynik['uprawnienia']=='administrator')
            {
                if($wynik['aktywny']==1)
                echo '<div class="komlist"><p class="listelement2">Administrator :'.str_repeat("&nbsp;", 1).''.$wynik['login'].' '.str_repeat("&nbsp;", 2).'Aktywny :'.str_repeat("&nbsp;", 1).'Tak    </p><button id="uprawnieniazmien'.$wynik['id'].'" onclick="pokaz3('.$wynik['id'].')">Zmień uprawnienia</button> <form class="zmiana2" method="post" action="adminusers.php" id="nowykom"><div class="funkcje">  <select name="wybor" id="wybor'.$wynik['id'].'"class="zmien3"><option value="użytkownik">użytkownik</option><option value="moderator">moderator</option></select><input type="submit" name="zmien" class="zmien" id="zmien'.$wynik['id'].'" value="Zmień " ><input type="submit" name="blokuj" class="zmien" id="zaakceptuj" value="Zablokuj" ><input type="submit" name="usun2" id="usun" value="Usuń Konto"><input type="hidden" name="id" value='.$wynik['id'].'></form></div></div>';
                else
                echo '<div class="komlist"><p class="listelement2">Administrator :'.str_repeat("&nbsp;", 1).''.$wynik['login'].' '.str_repeat("&nbsp;", 2).'Aktywny :'.str_repeat("&nbsp;", 1).'Nie    </p><button id="uprawnieniazmien'.$wynik['id'].'" onclick="pokaz3('.$wynik['id'].')">Zmień uprawnienia</button> <form class="zmiana2" method="post" action="adminusers.php" id="nowykom"><div class="funkcje">  <select name="wybor" id="wybor'.$wynik['id'].'"class="zmien"><option value="użytkownik">użytkownik</option><option value="moderator">moderator</option></select><input type="submit" name="zmien" class="zmien" id="zmien'.$wynik['id'].'" value="Zmień " ><input type="submit" name="odblokuj" class="zmien" id="zaakceptuj" value="Odblokuj" ><input type="submit" name="usun2" id="usun" value="Usuń Konto"><input type="hidden" name="id" value='.$wynik['id'].'></form></div></div>';  
            }

           
            
           
        }
        echo '<div class="komlist"><p class="listelement2">Administrator :'.str_repeat("&nbsp;", 1).''.$_SESSION['tablica'][1].' </p></div></div>';
        echo'</div>';
        pg_close($conn); 
    }
}
?>

<script>
    function powrot()
    {
        document.getElementById('duzezdjecie').style.display="none";
    
    document.getElementsByClassName('listazdjec').style['pointer-events'] = "auto";
    }
function wiekszy(album,zdjecie)
{
    document.getElementById('duzezdjecie').style.display="block";
    document.getElementById("duzyimage").src = "../photo/"+album+"/"+zdjecie;
    document.getElementsByClassName('listazdjec').style['pointer-events'] = "none";
}
function pokaz2(numer)
{
    document.getElementById('ntytul'+numer).style.display = "inline";
    document.getElementById("zmien"+numer).style.display = "inline";
    document.getElementById("tytulzmien"+numer).style.display = "none";
}
function pokaz3(numer)
{
    document.getElementById('wybor'+numer).style.display = "inline";
    document.getElementById("zmien"+numer).style.display = "inline";
    document.getElementById("uprawnieniazmien"+numer).style.display = "none";
}
</script>
