<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="utf-8" />
  <link href="style/menu.css" rel="stylesheet" type="text/css"/>
  <link href="style/foto.css" rel="stylesheet" type="text/css"/>
  <link rel="stylesheet" type="text/css" href="style/footerelement.css"/>
  <link rel="stylesheet" type="text/css" href="style/komentarze.css"/>
  <link rel="stylesheet" type="text/css" href="style/powrotokcss.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/fontawesome.min.css" >
  <title><?= $title ?></title>
</head>
<body>
<div id="container">
  <div id="main">
    
    <?php include 'javascript/include/menu.php'; ?>

    
    <a id="sukceslink3" href="album.php?id=<?= $photo['album_id'] ?>">Powrót do albumu</a>

    <?php if (!empty($_SESSION['warning3'])): ?>
      <p class="cwarning"><?= htmlspecialchars($_SESSION['warning3']) ?></p>
      <?php unset($_SESSION['warning3']); ?>
    <?php endif; ?>

   
    <p>Album: <span><?= htmlspecialchars($photo['album_tytul']) ?></span></p>
    <p>Autor: <span><?= htmlspecialchars($photo['autor_login']) ?></span></p>
    <p>Data dodania: <span><?= htmlspecialchars($photo['data']) ?></span></p>

    
    <div id="szczaly">
      
      <?php if ($prevPhotoId): ?>
        <a href="foto.php?id=<?= $prevPhotoId ?>&id_albumu=<?= $photo['album_id'] ?>">
            <img id="szczalaprevious" src="photo/fast-forward.png" alt="Poprzednie" />
        </a>
      <?php endif; ?>
      <?php if ($nextPhotoId): ?>
        <a href="foto.php?id=<?= $nextPhotoId ?>&id_albumu=<?= $photo['album_id'] ?>">
            <img id="szczalanext" src="photo/fast-forward-right.png" />
        </a>
      <?php endif; ?>
    </div>

    
    <?php if (!empty($photo['opiszdjecia'])): ?>
      <p>Opis: <span><?= htmlspecialchars($photo['opiszdjecia']) ?></span></p>
    <?php endif; ?>

    
    <div class="center">
      <div id="zdjecie">
        <img src="photo/<?= $photo['album_id'] ?>/<?= $photo['opis'] ?>" alt="Zdjęcie #<?= $photo['id'] ?>">
      </div>
    </div>

    
    <?php
      $avg = round($ratingData['average'], 2);
      $count = $ratingData['count'];
    ?>
    <?php if ($_SESSION['zalogowany']): ?>
      
      <form method="post" action="foto.php?action=ratePhoto">
        <div class="rating" id="ratings">
          <input type="radio" name="star" id="star1" value="10"<?php if ($userRating === 10) echo 'checked'; ?> onchange="this.form.submit();"><label for="star1"></label>
          <input type="radio" name="star" id="star2" value="9" <?php if ($userRating === 9) echo 'checked'; ?> onchange="this.form.submit();"><label for="star2"></label>
          <input type="radio" name="star" id="star3" value="8" <?php if ($userRating === 8) echo 'checked'; ?> onchange="this.form.submit();"><label for="star3"></label>
          <input type="radio" name="star" id="star4" value="7" <?php if ($userRating === 7) echo 'checked'; ?> onchange="this.form.submit();"><label for="star4"></label>
          <input type="radio" name="star" id="star5" value="6" <?php if ($userRating === 6) echo 'checked'; ?> onchange="this.form.submit();"><label for="star5"></label>
          <input type="radio" name="star" id="star6" value="5" <?php if ($userRating === 5) echo 'checked'; ?> onchange="this.form.submit();"><label for="star6"></label>
          <input type="radio" name="star" id="star7" value="4" <?php if ($userRating === 4) echo 'checked'; ?> onchange="this.form.submit();"><label for="star7"></label>
          <input type="radio" name="star" id="star8" value="3" <?php if ($userRating === 3) echo 'checked'; ?> onchange="this.form.submit();"><label for="star8"></label>
          <input type="radio" name="star" id="star9" value="2" <?php if ($userRating === 2) echo 'checked'; ?> onchange="this.form.submit();"><label for="star9"></label>
          <input type="radio" name="star" id="star10" value="1" <?php if ($userRating === 1) echo 'checked'; ?> onchange="this.form.submit();"><label for="star10"></label>
        </div>
        <input type="hidden" name="id" value="<?= $photo['id'] ?>">
        <input type="hidden" name="idalbm" value="<?= $photo['album_id'] ?>">
      </form>
    <?php else: ?>
      <p>Zaloguj się, aby ocenić to zdjęcie.</p>
    <?php endif; ?>

    
    <?php if ($count > 0): ?>
      <p>Średnia ocen zdjęcia: <span><?= $avg ?></span>, oceniało <span><?= $count ?></span> użytkowników</p>
    <?php else: ?>
      <p>To zdjęcie nie ma jeszcze żadnej oceny.</p>
    <?php endif; ?>

    
    <?php if ($_SESSION['zalogowany']): ?>
      <div id="komd">
        <form id="usrform" method="post" action="foto.php?action=addComment">
          <textarea name="kom" placeholder="Dodaj komentarz ..." required></textarea>
          <button type="submit" id="strzalka">
            <img id="kphoto" src="photo/send.png" border="0"/>
          </button>
          <input type="hidden" name="id_zdjecia" value="<?= $photo['id'] ?>">
          <input type="hidden" name="idalbm" value="<?= $photo['album_id'] ?>">
        </form>
      </div>
    <?php else: ?>
      <p>Zaloguj się, by móc skomentować to zdjęcie.</p>
    <?php endif; ?>

    
    
    <p>Komentarze:</p>
    <?php if (!empty($comments)): ?>
      <ol id="komentarze">
        <?php foreach ($comments as $c): ?>
          <li><p class="coment">
            <span id="user"><?= htmlspecialchars($c['login']) ?></span> 
            <?= htmlspecialchars($c['komentarz']) ?>
          </p></li>
        <?php endforeach; ?>
      </ol>
    <?php else: ?>
      <p>To zdjęcie nie ma komentarzy.</p>
    <?php endif; ?>
    

  </div>
</div>


<a id="sukceslink2" href="album.php?id=<?= $photo['album_id'] ?>">Powrót do albumu</a>

<footer id="footer">
  <address>Autor: Andrzej Brzeziński</address>
</footer>

</body>
</html>
