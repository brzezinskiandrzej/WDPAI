<?php
if (isset($_POST['dodajalbum'])) {
    session_start();
    echo albumn($_POST['albumname']);
    $_SESSION['warning3'] = albumn($_POST['albumname']);

    if ($_SESSION['warning3'] == false) {
        $_SESSION['warning3'] = '';
        require 'javascript/databaseconnection.php';
        $date = date('Y-m-d H:i:s');
        $title = str_replace("'", "''", $_POST['albumname']);

      
        $sql = "INSERT INTO albumy (
                    tytul,
                    data,
                    id_uzytkownika
                ) VALUES (
                    '" . $title . "',
                    '" . $date . "',
                    " . $_SESSION['tablica'][7] . "
                )";

        $result = pg_query($conn, $sql);

        if (!$result) {
            echo "Błąd zapytania: " . pg_last_error($conn);
            exit();
        }

 
        $zapytanie3 = pg_query($conn, "SELECT * FROM albumy WHERE tytul = '$title' ORDER BY data DESC");
        $wynik3 = pg_fetch_assoc($zapytanie3);

        if ($wynik3) {
            mkdir("photo/" . $wynik3['id'] . "");
            header('Location:dodaj-foto.php?albumid=' . $wynik3['id'] . '');
        } else {
            echo "Nie znaleziono dodanego albumu.";
        }
    } else {
        header('Location:dodaj-album.php');
    }

    pg_close($conn);
}

function albumn($arg)
{
    $pattern = "/^.{1,100}$/";
    $pattern2 = "/[^\s]+/";
    if (preg_match($pattern, $arg)) {
        if (preg_match($pattern2, $arg)) {
            return false;
        } else {
            $warning = "nazwa albumu nie może być pusta";
            return $warning;
        }
    } else {
        $warning = "nazwa albumu musi mieć od 1 do 100 znaków";
        return $warning;
    }
}
?>
