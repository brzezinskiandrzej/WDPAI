<!DOCTYPE html>
<html lang="pl-PL">
<head>
    <meta charset="utf-8">
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
        
        include 'javascript/include/menu.php';
        ?>

        <p id="top">LATEST OF IMAGE SPACE</p>

        <div class="galeria-top" style="text-align: center;">
            <?php if (empty($photos)): ?>
                <p>Brak najnowszych zdjęć do wyświetlenia.</p>
            <?php else: ?>
                <?php foreach ($photos as $photo): 
                    $photoId   = $photo['id_zdjecia'];
                    $albumId   = $photo['album_id'];
                    $filename  = $photo['opis'];
                    $albumTit  = $photo['tytul'];
                    $author    = $photo['login'];
                    $dateAdded = $photo['data'];
                ?>
                <div class="foto-container">
                    <a href="foto.php?id=<?= $photoId ?>">
                        <div class="img-overlay-container">
                            <img src="photo/<?= $albumId ?>/<?= $filename ?>" alt="Najnowsze zdjęcie">

                            <div class="overlay">
                                <p>Tytuł albumu: <?= htmlspecialchars($albumTit) ?></p>
                                <p>Autor: <?= htmlspecialchars($author) ?></p>
                                <p>Data dodania: <?= htmlspecialchars($dateAdded) ?></p>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</div>

<footer id="footer">
    <address>Autor: Andrzej Brzeziński 4Tb</address>
</footer>
</body>
</html>
