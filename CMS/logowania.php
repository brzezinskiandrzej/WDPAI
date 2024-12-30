<?php

if (isset($_POST['formlog'])) {
    session_start();
    $_SESSION['loginlog'] = $_POST['loginlog'];
    $_SESSION['haslolog'] = $_POST['haslolog'];
    $_SESSION['tablica'] = array();
    $_SESSION['tablica'] = login($_POST['loginlog'], $_POST['haslolog']);
    $_SESSION['warning2'] = $_SESSION['tablica'][0];

    if ($_SESSION['warning2'] == false) {
        $_SESSION['warning2'] = '';
        $_SESSION['zalogowany'] = true;
        header('Location:index.php');
    } else {
        header('Location:logrej.php');
    }
}

function login($arg1, $arg2)
{
    require 'javascript/databaseconnection.php';
    $zapytanie = pg_query_params($conn, "SELECT * FROM uzytkownicy WHERE login = $1", array($arg1));
    $i = array();

    if ($wynik = pg_fetch_assoc($zapytanie)) {
        $bool = true;
        if ($arg1 == $wynik['login']) {
            if (md5($arg2) == $wynik['haslo']) {
                if ($wynik['aktywny'] == '1') {
                    $i[0] = false;
                    $i[1] = $wynik['login'];
                    $i[2] = $wynik['haslo'];
                    $i[3] = $wynik['email'];
                    $i[4] = $wynik['zarejestrowany'];
                    $i[5] = $wynik['uprawnienia'];
                    $i[6] = $wynik['aktywny'];
                    $i[7] = $wynik['id'];
                } else {
                    $warning = "użytkownik został zablokowany";
                    $i[0] = $warning;
                }
            } else {
                $warning = "podane hasło jest nieprawidłowe";
                print_r($i[2]);
                $i[0] = $warning;
            }
        } else {
            $bool = false;
        }
    } else {
        $bool = false;
    }

    if ($bool == false) {
        $warning = "podanego loginu nie ma w bazie danych";
        $i[0] = $warning;
    }

    pg_close($conn);
    return $i;
}
?>
