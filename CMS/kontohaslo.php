<?php
session_start();
if (isset($_POST["checksubmit"])) {
    if (md5($_POST['checkpasswd']) == $_SESSION['tablica'][2]) {
        header('Location:konto.php?haslo=ok');
    } else {
        header('Location:konto.php?haslo=nieok');
    }
}

if (isset($_POST["ccheck"])) {
    $_SESSION['warning'] = email($_POST['cemail']);
    if ($_SESSION['warning'] == false) {
        require 'javascript/databaseconnection.php';
        $sql = "UPDATE uzytkownicy
                SET email='" . $_POST['cemail'] . "'
                WHERE id=" . $_SESSION['tablica'][7];
        $result = pg_query($conn, $sql);
        if (!$result) {
            echo "Błąd zapytania: " . pg_last_error($conn);
            exit();
        }
        pg_close($conn);
        $_SESSION['tablica'][3] = $_POST['cemail'];
        $_SESSION['warning'] = 'e-mail został poprawnie zmieniony 🙂';
        header('Location:konto.php?haslo=ok');
    } else {
        header('Location:konto.php?haslo=ok');
    }
}

if (isset($_POST["ccheck2"])) {
    $_SESSION['warning2'] = haslo($_POST['chaslo']);
    if ($_SESSION['warning2'] == false) {
        require 'javascript/databaseconnection.php';
        $sql = "UPDATE uzytkownicy
                SET haslo='" . md5($_POST['chaslo']) . "'
                WHERE id=" . $_SESSION['tablica'][7];
        $result = pg_query($conn, $sql);
        if (!$result) {
            echo "Błąd zapytania: " . pg_last_error($conn);
            exit();
        }
        pg_close($conn);
        $_SESSION['tablica'][2] = md5($_POST['chaslo']);
        $_SESSION['warning2'] = 'hasło zostało poprawnie zmienione 🙂';
        header('Location:konto.php?haslo=ok');
    } else {
        header('Location:konto.php?haslo=ok');
    }
}

function haslo($arg) {
    $pattern = "/^.{8,20}$/";
    $pattern2 = "/[a-zźżąęćśłó]/";
    $pattern3 = "/[A-ZŻŹĄĘĆŚŁÓ]/";
    $pattern4 = "/[0-9]/";

    if (!preg_match($pattern, $arg)) {
        $warning = "hasło musi mieć od 8 do 20 znaków";
        return $warning;
    } elseif (!preg_match($pattern2, $arg)) {
        $warning = "hasło musi posiadać co najmniej 1 małą literę";
        return $warning;
    } elseif (!preg_match($pattern3, $arg)) {
        $warning = "hasło musi posiadać co najmniej 1 dużą literę";
        return $warning;
    } elseif (!preg_match($pattern4, $arg)) {
        $warning = "hasło musi posiadać co najmniej 1 liczbę";
        return $warning;
    } else {
        return false;
    }
}

function email($arg) {
    if (!filter_var($arg, FILTER_VALIDATE_EMAIL)) {
        $warning = "adres e-mail jest niepoprawny";
        return $warning;
    } else {
        return false;
    }
}
?>
