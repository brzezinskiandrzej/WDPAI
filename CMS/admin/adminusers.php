<?php
session_start();
if (isset($_POST['zmien'])) {
    require '../javascript/databaseconnection.php';
    echo $_POST['wybor'];
    $sql = "UPDATE uzytkownicy
            SET uprawnienia = $1
            WHERE id = $2";
    pg_query_params($conn, $sql, [$_POST['wybor'], $_POST['id']]);
    $_SESSION['warning3'] = 'Uprawnienia użytkownika zostały pomyślnie zmienione 🙂';
    pg_close($conn);
    header('Location:index.php?type=users');
}

if (isset($_POST['blokuj'])) {
    require '../javascript/databaseconnection.php';
    $sql = "UPDATE uzytkownicy
            SET aktywny = 0
            WHERE id = $1";
    pg_query_params($conn, $sql, [$_POST['id']]);
    $_SESSION['warning3'] = 'Konto użytkownika zostało zablokowane 🙂';
    pg_close($conn);
    header('Location:index.php?type=users');
}

if (isset($_POST['odblokuj'])) {
    require '../javascript/databaseconnection.php';
    $sql = "UPDATE uzytkownicy
            SET aktywny = 1
            WHERE id = $1";
    pg_query_params($conn, $sql, [$_POST['id']]);
    $_SESSION['warning3'] = 'Konto użytkownika zostało odblokowane 🙂';
    pg_close($conn);
    header('Location:index.php?type=users');
}

if (isset($_POST['usun2'])) {
    require '../javascript/databaseconnection.php';
    $query = "SELECT id FROM albumy WHERE id_uzytkownika = $1";
    $result = pg_query_params($conn, $query, [$_POST['id']]);
    while ($row = pg_fetch_assoc($result)) {
        $directory = "../photo/" . $row['id'];
        deleteAll($directory);
    }

    $sql1 = "DELETE FROM zdjecia_oceny WHERE id_uzytkownika = $1";
    $sql2 = "DELETE FROM zdjecia_komentarze WHERE id_uzytkownika = $1";
    $sql3 = "DELETE FROM zdjecia USING albumy 
             WHERE zdjecia.id_albumu = albumy.id AND albumy.id_uzytkownika = $1";
    $sql4 = "DELETE FROM albumy WHERE id_uzytkownika = $1";
    $sql5 = "DELETE FROM uzytkownicy WHERE id = $1";

    pg_query_params($conn, $sql1, [$_POST['id']]);
    pg_query_params($conn, $sql2, [$_POST['id']]);
    pg_query_params($conn, $sql3, [$_POST['id']]);
    pg_query_params($conn, $sql4, [$_POST['id']]);
    pg_query_params($conn, $sql5, [$_POST['id']]);

    $_SESSION['warning3'] = 'Konto użytkownika zostało usunięte 🙂';
    pg_close($conn);
    header('Location:index.php?type=users');
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
?>
