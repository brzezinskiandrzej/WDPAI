<!DOCTYPE html>
<html lang="pl-PL">
<head>
    <meta charset="utf-8" />
    <title><?= $title ?></title>
    <link href="style/menu.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="style/footerelement.css"/>
    <link rel="stylesheet" type="text/css" href="style/tooltips.css"/>
    <link rel="stylesheet" type="text/css" href="style/numerystron.css"/>
    <link rel="stylesheet" type="text/css" href="style/tabela.css"/>
    <link rel="stylesheet" type="text/css" href="style/loading.css"/>
    <link rel="stylesheet" type="text/css" href="style/p.css"/>
</head>
<body>
<div id="container">
    <div id="main">
        <?php
       
        include 'javascript/include/menu.php';
        ?>

      
        <table id="sortowanie">
            <tbody>
                <tr>
                    <td><p>Sortuj według:</p></td>
                    <td>
                       
                        <a class="sorttype" href="index.php?sort=tytul&type=">
                            <?= ($currentSort === "tytul" && $currentSortType === "") ? "Tytuł ▼" : "Tytuł"; ?>
                        </a>
                    </td>
                    <td>
                        
                        <a class="sorttype" href="index.php?sort=data&type=">
                            <?= ($currentSort === "data" && $currentSortType === "") ? "Data (od najstarszych) ▼" : "Data (od najstarszych)"; ?>
                        </a>
                    </td>
                    <td>
                        
                        <a class="sorttype" href="index.php?sort=data&type=DESC">
                            <?= ($currentSort === "data" && $currentSortType === "DESC") ? "Data (od najnowszych) ▼" : "Data (od najnowszych)"; ?>
                        </a>
                    </td>
                    <td>
                        
                        <a class="sorttype" href="index.php?sort=login&type=">
                            <?= ($currentSort === "login") ? "Autor ▼" : "Autor"; ?>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>

        
        <div class="galeria">
            <?php if (count($albums) === 0): ?>
                <p>Brak albumów.</p>
            <?php else: ?>
                <?php foreach ($albums as $album): ?>
                    <?php
                        $albumId      = $album['id'];
                        $albumTitle   = $album['tytul'];
                        $albumAuthor  = $album['login'];
                        $albumDate    = $album['data'];
                        $firstPhoto   = $album['first_photo']; 
                    ?>

                    <div class="foto-container">
                        <div class="img-overlay-container">
                            <a href="album.php?id=<?= $albumId ?>">
                                
                                <?php if ($firstPhoto): ?>
                                    <img src="photo/<?= $albumId ?>/<?= $firstPhoto ?>" alt="Album photo" />
                                <?php else: ?>
                                    <img src="photo/folder.png" alt="Album placeholder" />
                                <?php endif; ?>

                                <div class="overlay">
                                    <p>Tytuł albumu: <?= htmlspecialchars($albumTitle) ?></p>
                                    <p>Autor: <?= htmlspecialchars($albumAuthor) ?></p>
                                    <p>Data utworzenia: <?= htmlspecialchars($albumDate) ?></p>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>


        
        <?php if ($pagesCount > 1): ?>
            <?= $paginationHtml ?>
        <?php endif; ?>
        

    </div>
</div>

<div class="loader-wrapper">
    <span class="loader"><span class="loader-inner"></span></span>
</div>

<footer id="footer">
    <address>Autor: Andrzej Brzeziński</address>	
</footer>


<script src="javascript/jquery-3.6.0.min.js"></script>
<script src="javascript/include/footer.js"></script>
<script src="javascript/include/menunagorze.js"></script>
<script>
    $(window).on("load", function() {
        $(".loader-wrapper").fadeOut(3000, function() {
            document.body.style.overflow = "unset";
        });
    });
</script>

</body>
</html>
