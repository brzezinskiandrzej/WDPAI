<!DOCTYPE html>
<html lang="pl-PL">
<head>
    <meta charset="utf-8" />
    <title><?= $title ?></title>

    <link href="style/menu.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="style/footerelement.css"/>
    <link rel="stylesheet" type="text/css" href="style/tooltips.css"/>
    <link rel="stylesheet" type="text/css" href="style/tooltip.css"/>
    <link rel="stylesheet" type="text/css" href="style/miejsca.css"/>
    <link rel="stylesheet" type="text/css" href="style/p.css"/>
</head>
<body>
<div id="container">
    <div id="main">
        <?php
        // Wczytujemy menu (już z MenuService)
        include 'javascript/include/menu.php';
        ?>

        <p id="top">TOP <?= $limit ?> OF IMAGE SPACE</p>

        <!-- Galeria top zdjęć -->
        <div class="galeria-top" style="text-align: center;">
            <?php if (empty($topPhotos)): ?>
                <p>Brak zdjęć do wyświetlenia.</p>
            <?php else: ?>
                <?php 
                $i = 1;
                foreach ($topPhotos as $photo):
                    // Pola z repozytorium:
                    // ocena, id_zdjecia, tytul, login, album_id, opis
                    $albumId   = $photo['album_id'];
                    $photoId   = $photo['id_zdjecia'];
                    $titleAlb  = $photo['tytul'];
                    $author    = $photo['login'];
                    $file      = $photo['opis'];
                    $rating    = round($photo['ocena'], 2);
                ?>
                <div class="foto-container">
                    <a href="foto.php?id=<?= $photoId ?>">
                        <div class="img-overlay-container">
                            <img src="photo/<?= $albumId ?>/<?= $file ?>" alt="Top foto #<?= $i ?>" />
                            <div class="overlay">
                                <p>Pozycja: <?= $i ?></p>
                                <p>Tytuł albumu: <?= htmlspecialchars($titleAlb) ?></p>
                                <p>Autor: <?= htmlspecialchars($author) ?></p>
                                <p>Ocena: <?= $rating ?></p>
                            </div>
                        </div>
                    </a>
                </div>
                <?php
                    $i++;
                endforeach; 
                ?>
            <?php endif; ?>
        </div>

    </div>
</div>

<footer id="footer">
    <address>Autor: Andrzej Brzeziński</address>
</footer>

</body>
</html>
