<!DOCTYPE html>
<html lang="pl-PL">
<head>
    <meta charset="utf-8" />
    <title><?= $title ?></title>
    <link href="style/menu.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="style/albumcss.css"/>
    <link rel="stylesheet" type="text/css" href="style/numerystron.css"/>
    <link rel="stylesheet" type="text/css" href="style/rejestracjaokcss.css"/>
    <link rel="stylesheet" type="text/css" href="style/footerelement.css"/>
</head>
<body>
<div id="container">
    <div id="main">

        <!-- Wstawiamy menu (korzystające z MenuService) -->
        <?php include 'javascript/include/menu.php'; ?>

        <!-- Ewentualnie link powrotu do strony głównej -->
        <a id="sukceslink" href="index.php">Strona główna</a>

        <!-- Tutaj wyświetlamy zdjęcia z pętli $photos -->
        <?php if (empty($photos)): ?>
            <p>Brak zaakceptowanych zdjęć w tym albumie.</p>
        <?php else: ?>
            <div class="album-gallery" style="text-align: center;">
                <?php foreach ($photos as $photo): ?>
                    <?php
                        // $photo zawiera: id, id_albumu, opis, data, zaakceptowane, opiszdjecia
                        $photoId      = $photo['id'];
                        $photoFile    = $photo['opis'];        // nazwa pliku
                        $photoComment = $photo['opiszdjecia']; // opis zdjęcia
                    ?>
                    <!-- Link do szczegółów zdjęcia (foto.php?id=...) -->
                    <a href="foto.php?id=<?= $photoId ?>&id_albumu=<?= $albumId ?>">
                        <img src="photo/<?= $albumId ?>/<?= $photoFile ?>" height="180" alt="<?= htmlspecialchars($photoComment) ?>">
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Paginacja -->
        <?php if ($pagesCount > 1): ?>
            <form id="numery" method="get" action="album.php">
                <!-- Zachowujemy id albumu -->
                <input type="hidden" name="id" value="<?= $albumId ?>">
                <?php for ($i = 1; $i <= $pagesCount; $i++): ?>
                    <input type="submit" name="numer" value="<?= $i ?>">
                <?php endfor; ?>
            </form>
        <?php endif; ?>

    </div>
</div>

<footer id="footer">
    <address>Autor: Andrzej Brzeziński</address>
</footer>
</body>
</html>
