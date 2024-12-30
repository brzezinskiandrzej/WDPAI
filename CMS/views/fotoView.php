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
    <!-- Wstawiamy menu -->
    <?php include 'javascript/include/menu.php'; ?>

    <!-- Powrót do albumu -->
    <a id="sukceslink3" href="album.php?id=<?= $photo['album_id'] ?>">Powrót do albumu</a>

    <?php if (!empty($_SESSION['warning3'])): ?>
      <p class="cwarning"><?= htmlspecialchars($_SESSION['warning3']) ?></p>
      <?php unset($_SESSION['warning3']); ?>
    <?php endif; ?>

    <!-- Informacje o zdjęciu -->
    <p>Album: <span><?= htmlspecialchars($photo['album_tytul']) ?></span></p>
    <p>Autor: <span><?= htmlspecialchars($photo['autor_login']) ?></span></p>
    <p>Data dodania: <span><?= htmlspecialchars($photo['data']) ?></span></p>

    <!-- Przyciski next/prev -->
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

    <!-- Ewentualny opis zdjęcia -->
    <?php if (!empty($photo['opiszdjecia'])): ?>
      <p>Opis: <span><?= htmlspecialchars($photo['opiszdjecia']) ?></span></p>
    <?php endif; ?>

    <!-- Wyświetlenie zdjęcia (dostosowanie rozmiaru, jeśli chcesz) -->
    <div class="center">
      <div id="zdjecie">
        <img src="photo/<?= $photo['album_id'] ?>/<?= $photo['opis'] ?>" alt="Zdjęcie #<?= $photo['id'] ?>">
      </div>
    </div>

    <!-- Ocena (gwiazdki) -->
    <?php
      $avg = round($ratingData['average'], 2);
      $count = $ratingData['count'];
    ?>
    <?php if ($_SESSION['zalogowany']): ?>
      <!-- Jeśli user zalogowany, pokazuj formularz oceniania -->
      <form method="post" action="foto.php?action=ratePhoto">
        <div class="rating" id="ratings">
          <!-- Możesz dać 10 radio inputów -->
          <input type="radio" name="star" id="star1" value="10" onchange="this.form.submit();"><label for="star1"></label>
          <input type="radio" name="star" id="star2" value="9" onchange="this.form.submit();"><label for="star2"></label>
          ...
          <input type="radio" name="star" id="star10" value="1" onchange="this.form.submit();"><label for="star10"></label>
        </div>
        <input type="hidden" name="id" value="<?= $photo['id'] ?>">
        <input type="hidden" name="idalbm" value="<?= $photo['album_id'] ?>">
      </form>
    <?php else: ?>
      <p>Zaloguj się, aby ocenić to zdjęcie.</p>
    <?php endif; ?>

    <!-- Wyświetlenie średniej -->
    <?php if ($count > 0): ?>
      <p>Średnia ocen zdjęcia: <span><?= $avg ?></span>, oceniało <span><?= $count ?></span> użytkowników</p>
    <?php else: ?>
      <p>To zdjęcie nie ma jeszcze żadnej oceny.</p>
    <?php endif; ?>

    <!-- Dodawanie komentarza -->
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

    <!-- Wyświetlenie komentarzy zaakceptowanych (jeśli pobrane w kontrolerze) -->
    
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

<!-- Powrót do albumu (drugi link, jak w oryginalnym kodzie) -->
<a id="sukceslink2" href="album.php?id=<?= $photo['album_id'] ?>">Powrót do albumu</a>

<footer id="footer">
  <address>Autor: Andrzej Brzeziński</address>
</footer>

</body>
</html>
