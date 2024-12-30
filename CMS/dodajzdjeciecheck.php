<?php
if (isset($_POST['dodajzdjecie'])) {
    session_start();
    $_SESSION['warning4'] = foton($_POST['opis']);
    if ($_SESSION['warning4'] == false) {
        if ($_FILES['photo']["error"] == UPLOAD_ERR_OK) {
            $filename = $_FILES['photo']["tmp_name"];
            $a = getimagesize($filename);
            $image_type = $a[2];

            if (in_array($image_type, array(IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF))) {
                move_uploaded_file($_FILES["photo"]["tmp_name"], "photo/" . $_POST['ida'] . "/" . $_FILES["photo"]["name"]);
                $filename = "photo/" . $_POST['ida'] . "/" . $_FILES["photo"]["name"];
                $_SESSION['warning4'] = 'Zdjęcie zostało pomyślnie dodane do albumu';

                require 'javascript/databaseconnection.php';
                $date = date('Y-m-d H:i:s');
                $title = str_replace("'", "''", $_POST['opis']);
                $title = str_replace('"', '""', $_POST['opis']);

               
                $sql = "INSERT INTO zdjecia (
                            opis,
                            id_albumu,
                            data,
                            zaakceptowane,
                            opiszdjecia
                        ) VALUES (
                            '" . $_FILES["photo"]["name"] . "',
                            " . $_POST['ida'] . ",
                            '" . $date . "',
                            0,
                            '" . $title . "'
                        )";

                $result = pg_query($conn, $sql);
                if (!$result) {
                    echo "Błąd zapytania: " . pg_last_error($conn);
                    exit();
                }

                $sql2 = pg_query($conn, "SELECT zdjecia.id FROM zdjecia ORDER BY zdjecia.data DESC LIMIT 1");
                $wynik5 = pg_fetch_assoc($sql2);
                $_SESSION['latestphotoid'] = $wynik5['id'];

                $directory = 'photo/' . $_POST['ida'] . '/min/';
                if (!file_exists($directory)) {
                    mkdir($directory);
                }

                
                if (in_array($image_type, array(IMAGETYPE_JPEG))) {
                    $width = $a[0];
                    $height = $a[1];
                    $new_width = intval($width * (180 / $height));
                    $new_height = 180;
                    $small = ImageCreateTrueColor($new_width, $new_height);
                    $source = ImageCreateFromJPEG($filename);
                    ImageCopyResampled($small, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                    ImageJPEG($small, 'photo/' . $_POST['ida'] . '/min/' . $_SESSION['latestphotoid'] . '-min.jpg');

                    if ($height > $width && $height > 1200) {
                        $new_width = intval($width * (1200 / $height));
                        $new_height = 1200;
                        $small = ImageCreateTrueColor($new_width, $new_height);
                        $source = ImageCreateFromJPEG($filename);
                        ImageCopyResampled($small, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                        imageJPEG($small, $filename);
                    } else if ($width > 1200) {
                        $new_width = 1200;
                        $new_height = intval($height * (1200 / $width));
                        $small = imagecreatetruecolor($new_width, $new_height);
                        $source = imagecreatefromjpeg($filename);
                        imagecopyresized($small, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                        imagejpeg($small, $filename);
                    }
                } elseif (in_array($image_type, array(IMAGETYPE_PNG))) {
                   
                    $width = $a[0];
                    $height = $a[1];
                    $new_width = intval($width * (180 / $height));
                    $new_height = 180;
                    $small = imagecreatetruecolor($new_width, $new_height);
                    $source = ImageCreateFromPNG($filename);
                    imagecopyresampled($small, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                    ImageJPEG($small, 'photo/' . $_POST['ida'] . '/min/' . $_SESSION['latestphotoid'] . '-min.jpg');

                    if ($height > $width && $height > 1200) {
                        $new_width = intval($width * (1200 / $height));
                        $new_height = 1200;
                        $small = ImageCreateTrueColor($new_width, $new_height);
                        $source = ImageCreateFromPNG($filename);
                        ImageCopyResampled($small, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                        imageJPEG($small, $filename);
                    } else if ($width > 1200) {
                        $new_width = 1200;
                        $new_height = intval($height * (1200 / $width));
                        $small = imagecreatetruecolor($new_width, $new_height);
                        $source = imagecreatefromPNG($filename);
                        imagecopyresized($small, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                        imagejpeg($small, $filename);
                    }
                } else {
                    
                    $width = $a[0];
                    $height = $a[1];
                    $new_width = intval($width * (180 / $height));
                    $new_height = 180;
                    $small = ImageCreateTrueColor($new_width, $new_height);
                    $source = ImageCreateFromGIF($filename);
                    ImageCopyResampled($small, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                    ImageJPEG($small, 'photo/' . $_POST['ida'] . '/min/' . $_SESSION['latestphotoid'] . '-min.jpg');

                    if ($height > $width && $height > 1200) {
                        $new_width = intval($width * (1200 / $height));
                        $new_height = 1200;
                        $small = ImageCreateTrueColor($new_width, $new_height);
                        $source = ImageCreateFromGIF($filename);
                        ImageCopyResampled($small, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                        imageJPEG($small, $filename);
                    } else if ($width > 1200) {
                        $new_width = 1200;
                        $new_height = intval($height * (1200 / $width));
                        $small = imagecreatetruecolor($new_width, $new_height);
                        $source = imagecreatefromGIF($filename);
                        imagecopyresized($small, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                        imagejpeg($small, $filename);
                    }
                }
            } else {
                $_SESSION['warning4'] = "Przesłany plik nie jest plikiem graficznym";
            }
        } elseif ($_FILES["photo"]["error"] == UPLOAD_ERR_NO_FILE) {
            $_SESSION['warning4'] = "Nie wysłałeś pliku!";
        } else {
            $_SESSION['warning4'] = "Błąd przesyłania pliku. Spróbuj ponownie.";
        }

        header('Location:dodaj-foto.php?albumid=' . $_POST['ida'] . '');
    } else {
        header('Location:dodaj-foto.php?albumid=' . $_POST['ida'] . '');
    }

    pg_close($conn);
}

function foton($arg)
{
    $pattern = "/^.{0,255}$/";
    if (preg_match($pattern, $arg)) {
        return false;
    } else {
        $warning = "nazwa albumu musi mieć max 255 znaków";
        return $warning;
    }
}
?>
