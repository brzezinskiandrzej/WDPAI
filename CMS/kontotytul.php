<?php
session_start();
if (isset($_POST['zmien'])) {
    $_SESSION['warning3'] = albumn($_POST['nowytytul']);
    if ($_SESSION['warning3'] == false) {
        require 'javascript/databaseconnection.php';
        $sql = "UPDATE albumy
                SET tytul='" . pg_escape_string($conn, $_POST['nowytytul']) . "'
                WHERE id=" . intval($_POST['id']);
        $result = pg_query($conn, $sql);
        if (!$result) {
            echo "Błąd zapytania: " . pg_last_error($conn);
            exit();
        }
        $_SESSION['warning3'] = 'Tytuł został poprawnie zmieniony 🙂';
        header('Location:konto.php?type=albumy');
    } else {
        header('Location:konto.php?type=albumy');
    }
    pg_close($conn);
}

if (isset($_POST['usun2'])) {
    require 'javascript/databaseconnection.php';

    $sql = "DELETE FROM albumy WHERE id=" . intval($_POST['id']);
    $sql2 = "DELETE FROM zdjecia_komentarze
             WHERE id_zdjecia IN (
                 SELECT id FROM zdjecia WHERE id_albumu=" . intval($_POST['id']) . "
             )";
    $sql3 = "DELETE FROM zdjecia_oceny
             WHERE id_zdjecia IN (
                 SELECT id FROM zdjecia WHERE id_albumu=" . intval($_POST['id']) . "
             )";
    $sql4 = "DELETE FROM zdjecia WHERE id_albumu=" . intval($_POST['id']);

    $result3 = pg_query($conn, $sql3);
    $result2 = pg_query($conn, $sql2);
    $result4 = pg_query($conn, $sql4);
    $result = pg_query($conn, $sql);

    if (!$result || !$result2 || !$result3 || !$result4) {
        echo "Błąd zapytania: " . pg_last_error($conn);
        exit();
    }

    $directory = "photo/" . intval($_POST['id']);
    deleteAll($directory);

    $_SESSION['warning3'] = 'Album został usunięty';
    pg_close($conn);
    header('Location:konto.php?type=albumy');
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
            $warning = "nazwa albumu nie może być pusta";
            return $warning;
        }
    } else {
        $warning = "nazwa albumu musi mięc od 1 do 100 znaków";
        return $warning;
    }
}
?>
