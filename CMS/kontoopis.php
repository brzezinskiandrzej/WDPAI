<?php
session_start();
if (isset($_POST['zmien'])) {
    $_SESSION['warning3'] = foton($_POST['nowyopis']);
    if ($_SESSION['warning3'] == false) {
        require 'javascript/databaseconnection.php';
        $sql = "UPDATE zdjecia
                SET opiszdjecia='" . pg_escape_string($conn, $_POST['nowyopis']) . "'
                WHERE id=" . intval($_POST['id']);
        $result = pg_query($conn, $sql);
        if (!$result) {
            echo "Błąd zapytania: " . pg_last_error($conn);
            exit();
        }
        $_SESSION['warning3'] = 'Opis został poprawnie zmieniony 🙂';
        header('Location:konto.php?type=zdjecia&id=' . intval($_POST['idalbumu']));
    } else {
        header('Location:konto.php?type=zdjecia&id=' . intval($_POST['idalbumu']));
    }
    pg_close($conn);
}

if (isset($_POST['usun2'])) {
    require 'javascript/databaseconnection.php';
    $sql2 = "DELETE FROM zdjecia_komentarze
             WHERE id_zdjecia=" . intval($_POST['id']);
    $sql3 = "DELETE FROM zdjecia_oceny
             WHERE id_zdjecia=" . intval($_POST['id']);
    $sql4 = "DELETE FROM zdjecia
             WHERE id=" . intval($_POST['id']);
    
    $result3 = pg_query($conn, $sql3);
    $result2 = pg_query($conn, $sql2);
    $result4 = pg_query($conn, $sql4);
    
    if (!$result3 || !$result2 || !$result4) {
        echo "Błąd zapytania: " . pg_last_error($conn);
        exit();
    }
    
    $directory = "photo/" . intval($_POST['idalbumu']) . "/" . pg_escape_string($conn, $_POST['opis']);
    $directory2 = "photo/" . intval($_POST['idalbumu']) . "/min/" . intval($_POST['id']) . "-min.jpg";
    echo $directory;
    echo $directory2;
    
    unlink($directory);
    unlink($directory2);

    $_SESSION['warning3'] = 'Zdjęcie zostało usunięte';
    pg_close($conn);
    header('Location:konto.php?type=zdjecia&id=' . intval($_POST['idalbumu']));
}

function foton($arg) {
    $pattern = "/^.{0,255}$/";
    if (preg_match($pattern, $arg)) {
        return false;
    } else {
        $warning = "nazwa albumu musi mieć max 255 znaków";
        return $warning;
    }
}
?>
