<!DOCTYPE html>
<html lang="pl-PL">
<head>
  <meta charset="utf-8" />
  <link href="style/menu.css" rel="stylesheet" type="text/css"/>
  <link rel="stylesheet" type="text/css" href="style/footerelement.css"/>
  <link rel="stylesheet" type="text/css" href="style/dodaj-album.css"/>
  <title>Dodaj nowy album</title>
</head>
<body>
<div id="container">
  <!-- Menu -->
  <?php include 'javascript/include/menu.php'; ?>

  <div id="main">
    <!-- Formularz -->
    <form method="post" action="dodaj-album.php?action=store">
      <input type="text" required="required" name="albumname" id="albumname"
             placeholder="Wpisz nazwę albumu" onchange="albumnamecheck()"><br>
      <p id="albummistake" class="warnings"></p>

      <input type="submit" name="dodajalbum" id="dodajalbum" value="Dodaj Album">

      <!-- Wyświetlanie błędów, jeśli są -->
      <?php if (!empty($errors)): ?>
        <div class="error-box">
          <?php foreach ($errors as $err): ?>
            <p><?= htmlspecialchars($err) ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </form>
  </div>
</div>
<script src="javascript/jquery-3.6.0.min.js"></script>
<script src="javascript/include/dodaj-albumjs.js"></script>
<footer id="footer">
  <address>Autor: Andrzej Brzeziński</address>
</footer>
</body>
</html>
