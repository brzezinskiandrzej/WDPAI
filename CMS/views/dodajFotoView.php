<!DOCTYPE html>
<html lang="pl-PL">
<head>
  <meta charset="utf-8" />
  <link href="style/menu.css" rel="stylesheet" type="text/css"/>
  <link rel="stylesheet" type="text/css" href="style/footerelement.css"/>
  <link rel="stylesheet" type="text/css" href="style/dodaj-foto.css"/>
  <link rel="stylesheet" type="text/css" href="style/listaalbumow.css"/>
  <title>Dodaj zdjęcie</title>
</head>
<body>
<div id="container">

  <script src="javascript/jquery-3.6.0.min.js"></script>
 
  <script src="javascript/include/dodaj-fotojs.js"></script>

  
  <?php include 'javascript/include/menu.php'; ?>

  <div id="main">
    <?php if (count($albums) > 1 && $albumId === 0): ?>
      
      <p>Wybierz album, do którego chcesz dodać zdjęcie:</p>
      <div class="album-grid">
        <?php foreach ($albums as $alb): ?>
          <div class="album-item">
            <a href="dodaj-foto.php?albumid=<?= $alb['id'] ?>" class="album-link">
              
              <img src="photo/folder.png" alt="Album <?= htmlspecialchars($alb['tytul']) ?>" class="album-icon">
              <div class="album-title"><?= htmlspecialchars($alb['tytul']) ?></div>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      
      <?php if (!empty($errors)): ?>
        <div class="error-box">
          <?php foreach ($errors as $err): ?>
            <p><?= htmlspecialchars($err) ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <?php
        if (!empty($_SESSION['warning4'])) {
          echo '<p class="success-message">'.htmlspecialchars($_SESSION['warning4']).'</p>';
          unset($_SESSION['warning4']);
        }
      ?>

      <form id="dodajzdj" method="post" action="dodaj-foto.php?action=store" enctype="multipart/form-data">
        <div class="form-group">
          <label for="foto">Wybierz zdjęcie:</label>
          <input type="file" required name="photo" id="foto" onchange="validatePhotoFile()" /><br>
          <p id="fotomistake" class="warnings"></p>
        </div>

        <div class="form-group">
          <label for="opis">Opis zdjęcia:</label>
          <input type="text" name="opis" id="opis" placeholder="Opis Zdjęcia" onchange="fotonamecheck()" /><br>
          <p id="fotomistake2" class="warnings"></p>
        </div>

        <input type="submit" name="dodajzdjecie" id="dodajzdjecie" value="Dodaj Zdjęcie">

        
        <input type="hidden" name="ida" value="<?= htmlspecialchars($albumId) ?>">
      </form>

      
      <?php if (!empty($photos)): ?>
        <div id="listazdjec">
          <p>Zdjęcia w albumie:</p>
          <div class="photo-grid">
            <?php foreach ($photos as $ph): ?>
              <div class="photo-item">
                <img src="photo/<?= $albumId ?>/<?= htmlspecialchars($ph['opis']) ?>" alt="Zdjęcie <?= htmlspecialchars($ph['id']) ?>" class="photo-thumbnail">
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php else: ?>
        <p>(Brak zaakceptowanych zdjęć w tym albumie)</p>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</div>

<footer id="footer">
  <address>Autor: Andrzej Brzeziński</address>
</footer>
</body>
</html>
