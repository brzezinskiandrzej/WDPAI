<?php
session_start();

if (isset($_POST['zmienkomentarz'])) {
    require '../javascript/databaseconnection.php';

    $title = str_replace("'", "''", $_POST['kom' . $_POST['textareanumber']]);
    $title = str_replace('"', '""', $title);
    echo $title;
    
    $sql = "UPDATE zdjecia_komentarze
            SET komentarz = $1
            WHERE id = $2";
    pg_query_params($conn, $sql, [$title, $_POST['id']]);
    
    $_SESSION['warning3'] = 'Komentarz został zedytowany 🙂';
    pg_close($conn);
    header('Location:index.php?type=kom');
}

if (isset($_POST['zaakceptuj'])) {
    require '../javascript/databaseconnection.php';
    
    $sql = "UPDATE zdjecia_komentarze
            SET zaakceptowany = 1
            WHERE id = $1";
    pg_query_params($conn, $sql, [$_POST['id']]);
    
    $_SESSION['warning3'] = 'Komentarz został zaakceptowany 🙂';
    pg_close($conn);
    header('Location:index.php?type=kom');
}

if (isset($_POST['usun2'])) {
    require '../javascript/databaseconnection.php';
    
    $sql2 = "DELETE FROM zdjecia_komentarze
             WHERE id = $1";
    pg_query_params($conn, $sql2, [$_POST['id']]);
    
    $_SESSION['warning3'] = 'Komentarz został usunięty'; 
    pg_close($conn);
    header('Location:index.php?type=kom');
}
?>
