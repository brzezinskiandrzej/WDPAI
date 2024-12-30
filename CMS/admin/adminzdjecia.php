<?php
session_start();
if (isset($_POST['zaakceptuj'])) {
    require '../javascript/databaseconnection.php';
    $sql = "UPDATE zdjecia
            SET zaakceptowane = 1
            WHERE id = $1";
    pg_query_params($conn, $sql, [$_POST['id']]);
    $_SESSION['warning3'] = 'Zdjęcie zostało zaakceptowane 🙂';
    pg_close($conn);
    header('Location:index.php?type=zdjecia');
}

if (isset($_POST['usun2'])) {
    require '../javascript/databaseconnection.php';

    $sql2 = "DELETE FROM zdjecia_komentarze
             USING zdjecia
             WHERE zdjecia_komentarze.id_zdjecia = zdjecia.id AND zdjecia.id = $1";
    $sql3 = "DELETE FROM zdjecia_oceny
             USING zdjecia
             WHERE zdjecia_oceny.id_zdjecia = zdjecia.id AND zdjecia.id = $1";
    $sql4 = "DELETE FROM zdjecia WHERE id = $1";

    pg_query_params($conn, $sql2, [$_POST['id']]);
    pg_query_params($conn, $sql3, [$_POST['id']]);
    pg_query_params($conn, $sql4, [$_POST['id']]);

    $directory = "../photo/" . $_POST['idalbumu'] . "/" . $_POST['opis'];
    $directory2 = "../photo/" . $_POST['idalbumu'] . "/min/" . $_POST['id'] . "-min.jpg";
    echo $directory;
    echo $directory2;
    unlink($directory);
    unlink($directory2);

    $_SESSION['warning3'] = 'Zdjęcie zostało usunięte';
    pg_close($conn);
    header('Location:index.php?type=zdjecia');
}
?>
