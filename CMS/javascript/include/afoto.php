<?php
require 'javascript/databaseconnection.php';
$stronyzapytanie=pg_query($conn,"SELECT albumy.tytul,uzytkownicy.login,zdjecia.* from zdjecia
INNER JOIN albumy on zdjecia.id_albumu=albumy.id
INNER JOIN uzytkownicy on albumy.id_uzytkownika=uzytkownicy.id
WHERE zdjecia.id=".$_GET['id']."");
$row=pg_fetch_assoc($stronyzapytanie);
if(!isset($_GET['r']))     
{     
echo "<script language=\"JavaScript\">     
<!--      
document.location=\"$PHP_SELF?id=".$_GET['id']."&id_albumu=".$row['id_albumu']."&r=1&width=\"+screen.width+\"&Height=\"+screen.height;     
//-->     
</script>";    
}
$opis=$row['opis'];
$html='<meta charset="utf-8" /><img src="photo/'.$_GET['id_albumu'].'/'.$opis.'">';
$doc = new DOMDocument();
$doc->loadHTML($html);
$xpath = new DOMXPath($doc);
$src = $xpath->evaluate("string(//img/@src)");
    //echo $src;
    $a = getimagesize($src);
    if(!isset($_SESSION['warning3']))
    $_SESSION['warning3']='';
       echo ' <p class="cwarning">'.$_SESSION['warning3'].'</p>';
       if(isset($_SESSION['warning3']) && $_SESSION['warning3']!='')
        {
            
            unset($_SESSION['warning3']);
            
        }
        
echo '<p>Album: <span>'.$row['tytul'].'</span></p>
<p>Autor: <span>'.$row['login'].'</span></p>
<p>Data dodania: <span>'.$row['data'].'</span></p>
';
echo'
<div id="szczaly">';
$przewijanie=pg_query($conn,"SELECT albumy.tytul,uzytkownicy.login,zdjecia.* from zdjecia
INNER JOIN albumy on zdjecia.id_albumu=albumy.id
INNER JOIN uzytkownicy on albumy.id_uzytkownika=uzytkownicy.id
WHERE zdjecia.id_albumu=".$_GET['id_albumu']." and zdjecia.zaakceptowane=1
ORDER BY zdjecia.data");

$przewijanie2=pg_query($conn,"SELECT albumy.tytul,uzytkownicy.login,zdjecia.* from zdjecia
INNER JOIN albumy on zdjecia.id_albumu=albumy.id
INNER JOIN uzytkownicy on albumy.id_uzytkownika=uzytkownicy.id
WHERE zdjecia.id_albumu=".$_GET['id_albumu']." and zdjecia.zaakceptowane=1
ORDER BY zdjecia.data DESC;");

$current=pg_fetch_assoc(pg_query($conn,"SELECT zdjecia.* from zdjecia WHERE zdjecia.id=".$_GET['id'].""));

$last=pg_fetch_assoc($przewijanie2);
$first=pg_fetch_assoc($przewijanie);
$data=date('Y/m/d', strtotime($current['data']));
$hours=date( 'G:i:s',strtotime($current['data']));
if($current['data']!=$last['data'] || $current['id']!=$last['id'])
echo '<form method="post" action="przewijanie.php"><button type="submit" name="nast" id="wprawo"><img id="wprawos" src="photo/fast-forward-right.png" border="0"/></button><input type="hidden" name="id" value='.$_GET['id'].'>
<input type="hidden" name="idalbm" value='.$_GET['id_albumu'].'><input type="hidden" name="data" value="'.$data.' '.$hours.'"></form>';
if($current['data']!=$first['data']|| $current['id']!=$first['id'])
echo '<form method="post" action="przewijanie.php"><button type="submit" name="poprz" id="wlewo"><img id="wlewos" src="photo/fast-forward.png" border="0"/></button><input type="hidden" name="id" value='.$_GET['id'].'>
<input type="hidden" name="idalbm" value='.$_GET['id_albumu'].'><input type="hidden" name="data" value="'.$data.' '.$hours.'"></form>';
echo'</div>';
if(isset($_GET['nast']))
{
    echo "ZAJEBISCIE";
}
if($row['opiszdjecia']!='')
{
    echo'<p>Opis: <span>'.$row['opiszdjecia'].'</span></p>';
}
if($a[0]>($_GET['width']-100))
{
    $rozmiar=$_GET['width']-100;
    
    echo'<div class="center"><div id="zdjecie"><img width="'.$rozmiar.'" src="'.$src.'"></div></div>';
}
else{
    echo'<div class="center"><div id="zdjecie"><img src="'.$src.'"></div></div>';
}
$ocenazdjecia=0;$i=0;
$sredniaocen=pg_query($conn,'SELECT zdjecia_oceny.ocena from zdjecia_oceny
WHERE zdjecia_oceny.id_zdjecia='.$_GET['id'].'');
while($row2=pg_fetch_assoc($sredniaocen))
{
    $ocenazdjecia+=$row2['ocena'];
    $i++;
}

if($_SESSION['zalogowany']==true)
{
$oceny=pg_query($conn,'SELECT zdjecia_oceny.ocena from zdjecia_oceny
WHERE zdjecia_oceny.id_zdjecia='.$_GET['id'].' and zdjecia_oceny.id_uzytkownika='.$_SESSION['tablica'][7].'');
$czyocenil=pg_num_rows($oceny);
$ocena=pg_fetch_assoc($oceny);
}
echo'
<form method="post" action="afotoscript.php">
<div class="rating" id="ratings">
<input type="radio" name="star" id="star1" onchange="this.form.submit();" value=10><label for="star1"></label>
<input type="radio" name="star" id="star2" onchange="this.form.submit();" value=9><label for="star2"></label>
<input type="radio" name="star" id="star3" onchange="this.form.submit();" value=8><label for="star3"></label>
<input type="radio" name="star" id="star4" onchange="this.form.submit();" value=7><label for="star4"></label>
<input type="radio" name="star" id="star5" onchange="this.form.submit();" value=6><label for="star5"></label>
<input type="radio" name="star" id="star6" onchange="this.form.submit();" value=5><label for="star6"></label>
<input type="radio" name="star" id="star7" onchange="this.form.submit();" value=4><label for="star7"></label>
<input type="radio" name="star" id="star8" onchange="this.form.submit();" value=3><label for="star8"></label>
<input type="radio" name="star" id="star9" onchange="this.form.submit();" value=2><label for="star9"></label>
<input type="radio" name="star" id="star10" onchange="this.form.submit();" value=1><label for="star10"></label></div>
<input type="hidden" name="id" value='.$_GET['id'].'>
<input type="hidden" name="idalbm" value='.$_GET['id_albumu'].'>
</form>
';
if($_SESSION['zalogowany']==true)
{
    if($czyocenil==0)
    echo '<script type="text/javascript">
document.getElementById("ratings").style.display="flex";
</script>'; 
else{
    echo '<script type="text/javascript">
document.getElementById("ratings").style.display="none";
</script>';
echo'<br>';
    echo "<p>Oceniłeś to zdjęcie, twoja ocena to: ".$ocena['ocena']."</p>";
}


}
else{

 echo '<script type="text/javascript">
document.getElementById("ratings").style.display="none";
</script>';
echo'<p>Zaloguj się aby ocenić to zdjęcie</p>';
}
if($i>0){
    $ocenazdjecia=$ocenazdjecia/$i;
    echo '<br><p> Średnia ocen zdjęcia: <span>'.$ocenazdjecia.'</span> , oceniało  <span>'.$i.'</span> użytkowników</p>';
    }
    else if($i==0)
    {
        echo '<br><p> To zdjęcie nie ma jeszcze żadnej oceny</p>';
    }
if($_SESSION['zalogowany']==true)
{
    echo '<br><br><div id="komd"><textarea name="kom" form="usrform" placeholder="Dodaj Komentarz ..." required="required"></textarea><form method="post" action="adodajkomentarz.php" id="usrform">

<input type="hidden" name="id_zdjecia" value='.$_GET['id'].'>
<input type="hidden" name="idalbm" value='.$_GET['id_albumu'].'>
<button type="submit" name="dodajkom" id="strzalka"><img id="kphoto" src="photo/send.png" border="0"/></button>
</form>
</div>
';
}
else{
    echo '<br><br><br><p> Zaloguj się by móc skomentować to zdjęcie</p>';
}
echo '<br><br><p id=Komentarztytul>Komentarze : </p><br><br>';
$komentarze=pg_query($conn,"SELECT zdjecia_komentarze.*,uzytkownicy.login from zdjecia_komentarze
INNER JOIN uzytkownicy on zdjecia_komentarze.id_uzytkownika=uzytkownicy.id
WHERE zdjecia_komentarze.id_zdjecia=".$_GET['id']." AND zdjecia_komentarze.zaakceptowany=1 ORDER BY zdjecia_komentarze.data DESC");
$komliczba=pg_num_rows($komentarze);

if($komliczba>0)
{
    echo '<ol id="komentarze">';
while($kom=pg_fetch_assoc($komentarze))
{
echo '<li><p class="coment"><span id="user">'.$kom['login'].'</span>  '.$kom['komentarz'].'</p></li>';
}
echo '</ol>';
}
else{
echo'<p id="komunikat">To zdjęcie nie ma komentarzy</p>';

}
     pg_close($conn);
?>