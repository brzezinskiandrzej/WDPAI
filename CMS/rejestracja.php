<?php
if(isset($_POST['sumbitsignin']))
{
require 'javascript/databaseconnection.php';
session_start();
$i=true;
$j=1;
$_SESSION['login']=$_POST['login'];
$_SESSION['haslo']=$_POST['haslo'];
$_SESSION['haslo2']=$_POST['haslo2'];
$_SESSION['email']=$_POST['email'];
//echo $_SESSION['warning'];  
while($i==true)
{
switch($j){
    case 1:
        $_SESSION['warning']=login($_POST['login'],$conn);
        if($_SESSION['warning']==false)
            {$_SESSION['warning']='';
            $j++;}
        else{pg_close($conn); 
            header('Location:logrej.php');
            $i=false;
        }
        break;
    case 2:
        $_SESSION['warning']=haslo($_POST['haslo']);
        if($_SESSION['warning']==false)
            {$_SESSION['warning']='';
            $j++;}
        else{pg_close($conn); 
            header('Location:logrej.php');
            $i=false;
        }
        break;
    case 3:
        $_SESSION['warning']=samehaslo($_POST['haslo2']);
        if($_SESSION['warning']==false)
        {$_SESSION['warning']='';
        $j++;}
    else{pg_close($conn);
         header('Location:logrej.php');
        $i=false;
    }
        break;
    case 4:
        $_SESSION['warning']=email($_POST['email']);
        if($_SESSION['warning']==false)
        {$_SESSION['warning']='';
        $j++;}
    else{pg_close($conn); 
        header('Location:logrej.php');
        $i=false;
    }
        break;    
    case 5:
        $i=false;
        break;


}

}
if($_SESSION['warning']=='')
{
    $date=date('Y-m-d H:i:s');
    $sql = "INSERT INTO uzytkownicy (login, haslo, email, zarejestrowany, aktywny)
        VALUES ($1, $2, $3, $4, $5)";
    pg_query_params($conn, $sql, [$_SESSION['login'], MD5($_SESSION['haslo']), $_SESSION['email'], $date, '1']);

    //pg_query($conn,$sql);
    $_SESSION['zalogowany']=true;
    $temporarylogin=$_SESSION['login'];
    $zapytanie3=pg_query($conn, "SELECT * FROM uzytkownicy WHERE login='$temporarylogin'");
    $wynik3=pg_fetch_assoc($zapytanie3);
    $_SESSION['tablica'][1]=$wynik3['login'];
    $_SESSION['tablica'][2]=$wynik3['haslo'];
    $_SESSION['tablica'][3]=$wynik3['email'];
    $_SESSION['tablica'][4]=$wynik3['zarejestrowany'];
    $_SESSION['tablica'][5]=$wynik3['uprawnienia'];
    $_SESSION['tablica'][6]=$wynik3['aktywny'];
    $_SESSION['tablica'][7]=$wynik3['id'];
    pg_close($conn);
    header('Location:rejestracja-ok.php');
}
//echo $_SESSION['warning'];



}
function login($arg,$conn)
{
    $bool=false;
$pattern="/^[0-9a-zA-Z]{8,16}$/";
if(preg_match($pattern,$arg))
{
  	
    
    $zapytanie=pg_query($conn, "SELECT * FROM uzytkownicy ORDER BY login");
    while($wynik=pg_fetch_assoc($zapytanie)) {
        //echo $wynik['login'];
        if($arg==$wynik['login'])
            $bool=true;
    }
    if($bool==true)
    {
        $warning="podany login juz istnieje";
        
        return $warning;
    }
    else return false;
    }
    
    

}
function haslo($arg)
{
    $bool=false;
    $pattern="/^.{8,20}$/";
    $pattern2="/[a-zźżąęćśłó]/";
    $pattern3="/[A-ZŻŹĄĘĆĆŚŁÓ]/";
    $pattern4="/[0-9]/";
    if(!preg_match($pattern,$arg))
    {
        $warning="hasło musi mieć od 8 do 20 znaków";
        
        return $warning;  
        }
        else if(!preg_match($pattern2,$arg))
        {
            $warning="hasło musi posiadać co najmniej 1 małą literę";
        
        return $warning; 
        }
        else if(!preg_match($pattern3,$arg))
        {
            $warning="hasło musi posiadać co najmniej 1 dużą literę";
        
        return $warning; 
        }
        else if(!preg_match($pattern4,$arg))
        {
            $warning="hasło musi posiadać co najmniej 1 liczbę";
        
        return $warning; 
        }
        else return false;
        
        
    
}
function samehaslo($arg)
{
    if($arg==$_SESSION['haslo'])
    return false;
    else{
        $warning="hasła muszą być identyczne";
        
        return $warning; 
    
    }
}
function email($arg)
{
    if (!filter_var($arg, FILTER_VALIDATE_EMAIL))
    {
        $warning="adres e-mail jest niepoprawny";
        
        return $warning; 
    }
    else return false;
}
//header('Location:rejestracja-ok.php');
?>