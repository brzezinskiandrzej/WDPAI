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

        
        <?php include 'javascript/include/menu.php'; ?>

        
        <a id="sukceslink" href="index.php">Strona główna</a>

        
        <?php if (empty($photos)): ?>
            <p>Brak zaakceptowanych zdjęć w tym albumie.</p>
        <?php else: ?>
            <div class="album-gallery" style="text-align: center;">
                <?php foreach ($photos as $photo): ?>
                    <?php
                        
                        $photoId      = $photo['id'];
                        $photoFile    = $photo['opis'];        
                        $photoComment = $photo['opiszdjecia']; 
                    ?>
                    
                    <a href="foto.php?id=<?= $photoId ?>&id_albumu=<?= $albumId ?>">
                        <img src="photo/<?= $albumId ?>/<?= $photoFile ?>" height="180" alt="<?= htmlspecialchars($photoComment) ?>">
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        
        <?php if ($pagesCount > 1): ?>
            <form id="numery" method="get" action="album.php">
                
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
