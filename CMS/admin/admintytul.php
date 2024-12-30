<?php

session_start();

if (isset($_POST['zmien'])) {
    $_SESSION['warning3'] = albumn($_POST['nowytytul']);
    if ($_SESSION['warning3'] == false) {
        require '../javascript/databaseconnection.php';
        $sql = "UPDATE albumy
                SET tytul = $1
                WHERE id = $2";
        $result = pg_query_params($conn, $sql, [$_POST['nowytytul'], $_POST['id']]);
        
        if ($result) {
            $_SESSION['warning3'] = 'Tytuł został poprawnie zmieniony 🙂';
        } else {
            $_SESSION['warning3'] = 'Wystąpił błąd podczas zmiany tytułu.';
        }
        pg_close($conn);
        header('Location:index.php?type=albumy');
    } else {
        header('Location:index.php?type=albumy');
    }
}

if (isset($_POST['usun2'])) {
    require '../javascript/databaseconnection.php';

    $sql = "DELETE FROM albumy WHERE id = $1";
    $sql2 = "DELETE FROM zdjecia_komentarze
             USING zdjecia
             WHERE zdjecia_komentarze.id_zdjecia = zdjecia.id AND zdjecia.id_albumu = $1";
    $sql3 = "DELETE FROM zdjecia_oceny
             USING zdjecia
             WHERE zdjecia_oceny.id_zdjecia = zdjecia.id AND zdjecia.id_albumu = $1";
    $sql4 = "DELETE FROM zdjecia WHERE id_albumu = $1";

    pg_query_params($conn, $sql3, [$_POST['id']]);
    pg_query_params($conn, $sql2, [$_POST['id']]);
    pg_query_params($conn, $sql4, [$_POST['id']]);
    pg_query_params($conn, $sql, [$_POST['id']]);

    $directory = "../photo/" . $_POST['id'];
    deleteAll($directory);

    $_SESSION['warning3'] = 'Album został usunięty';
    pg_close($conn);
    header('Location:index.php?type=albumy');
}

function deleteAll($dir) {
    foreach (glob($dir . '/*') as $file) {
        if (is_dir($file)) {
            deleteAll($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dir);
}

function albumn($arg) {
    $pattern = "/^.{1,100}$/";
    $pattern2 = "/[^\s]+/";

    if (preg_match($pattern, $arg)) {
        if (preg_match($pattern2, $arg)) {
            return false;
        } else {
            return "nazwa albumu nie może być pusta";
        }
    } else {
        return "nazwa albumu musi mięc od 1 do 100 znaków";
    }
}
?>
