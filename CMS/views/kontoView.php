<!DOCTYPE html>
<html lang="pl-PL">
<head>
    <meta charset="utf-8" />
    <link href="style/menu.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="style/footerelement.css"/>
    <link rel="stylesheet" type="text/css" href="style/kontomenu.css"/>
    <link rel="stylesheet" type="text/css" href="style/albumykonto.css"/>
    <title>Konto Użytkownika</title>
    <!-- JavaScript includes -->
    <script src="javascript/include/kontojs.js"></script>
    <script src="javascript/jquery-3.6.0.min.js"></script>
    <script src="javascript/include/footer.js"></script>
    <script src="javascript/include/menunagorze.js"></script>
</head>
<body>
<div id="container">
    <div id="main">
        <?php include 'javascript/include/menu.php'; ?>

        <div id="menutoogle">
            <input type="checkbox">
            <span></span>
            <span></span>
            <span></span>
            <ul id="menukonto">
                <li><a href="konto.php?type=dane">Moje dane</a></li>
                <li><a href="konto.php?type=albumy">Moje albumy</a></li>
                <li><a href="konto.php?type=zdjecia">Moje zdjęcia</a></li>
                <li><a href="konto.php?type=usun" onclick="return confirm('Czy na pewno chcesz usunąć swoje konto?');">Usuń Konto</a></li>
            </ul>
        </div>

        <!-- Komunikaty o błędach -->
        <?php if (!empty($warning) || !empty($warning2) || !empty($warning3)): ?>
            <div class="error-box">
                <?php 
                    if (!empty($warning)) echo '<p>' . htmlspecialchars($warning) . '</p>'; 
                    if (!empty($warning2)) echo '<p>' . htmlspecialchars($warning2) . '</p>'; 
                    if (!empty($warning3)) echo '<p>' . htmlspecialchars($warning3) . '</p>'; 
                ?>
            </div>
        <?php endif; ?>

        <!-- Komunikaty o sukcesie -->
        <?php if (!empty($success)): ?>
            <div class="success-message">
                <p><?= htmlspecialchars($success) ?></p>
            </div>
        <?php endif; ?>
        
        <!-- Sekcje konta -->
        <div class="konto-sekcja" id="dane">
        
            <?php if ($type === 'dane'): ?>
                
                <?php if (isset($_GET['haslo']) && $_GET['haslo'] === "ok"): ?>
                    <div id="srodek">
                        <form class="zmiany" method="post" action="konto.php?type=dane&action=updateEmail">
                            <p>Zmień adres e-mail</p><br>
                            <div id="emaildiv">
                                <input type="text" name="cemail" id="cemail" required="required" placeholder="Wpisz E-mail" onchange="validateEmail()">
                                <input type="submit" name="ccheck" id="ccheck" value="Zmień">
                            </div>
                            <p class="cwarning"><?= htmlspecialchars($warning ?? '') ?></p>
                        </form>
                        <form class="zmiany" method="post" action="konto.php?type=dane&action=updatePassword">
                            <p>Zmień hasło</p><br>
                            <div id="haslodiv">
                                <input type="password" name="chaslo" id="chaslo" required="required" placeholder="Wpisz hasło" onchange="validatePassword()">
                                <input type="submit" name="ccheck2" id="ccheck2" value="Zmień">
                            </div>
                            <p class="cwarning"><?= htmlspecialchars($warning2 ?? '') ?></p>
                        </form>
                    </div>
                <?php else: ?>
                    <p id="tytuldanych">Moje dane:</p>
                    <div id="daneuzytkownika">
                        <p class="dane"><strong>Login:</strong> <?= htmlspecialchars($user['login']) ?></p>
                        <p class="dane"><strong>E-mail:</strong> <?= htmlspecialchars($user['email']) ?></p>
                        <p class="dane"><strong>Data założenia konta:</strong> <?= htmlspecialchars($user['created_at']) ?></p>
                    </div>
                    <div class="centre">
                        <button id="zmien" onclick="isclicked()">Zmień Dane</button>
                    </div>
                    <div id="podajhaslo" style="display:none;">
                        <form method="post" action="kontohaslo.php">
                            <p>Aby zmienić adres e-mail bądź hasło najpierw podaj swoje hasło:</p>
                            <input type="password" name="checkpasswd" id="checkpasswd" required="required" placeholder="Wpisz Hasło">
                            <input type="submit" name="checksubmit" id="checksubmit" value="Sprawdź hasło">
                        </form>
                    </div>
                    <?php if (isset($_GET['haslo']) && $_GET['haslo'] === "nieok"): ?>
                        <p id="blad">Niepoprawne hasło</p>
                        <script>
                            document.getElementById("podajhaslo").style.display = "block";
                        </script>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <div class="konto-sekcja" id="albumy">
            <?php if ($type === 'albumy'): ?>
                <?php if ($albums): ?>
                    <div class="album-grid">
                        <?php foreach ($albums as $album): ?>
                            <div class="tooltip">
                                <!-- Ikona albumu -->
                                <img class="glowne" src="photo/folder.png" height="180" alt="Album <?= htmlspecialchars($album['tytul']) ?>">
                                
                                <!-- Tytuł albumu z informacją -->
                                <p class="atitle">
                                    <?= htmlspecialchars($album['tytul']) ?>
                                    <?php
                                        // Upewnienie się, że 'accept' nie jest NULL
                                        $accept = isset($album['accept']) ? (int)$album['accept'] : 0;
                                        $ile = isset($album['ile']) ? (int)$album['ile'] : 0;
                                        if ($ile === 0) {
                                            echo " (Brak zdjęć)";
                                        } elseif ($accept === 0) {
                                            echo " (Brak zaakceptowanych zdjęć)";
                                        } else {
                                            echo " ($ile zdjęć, $accept zaakceptowanych)";
                                        }
                                    ?>
                                </p>
                                
                                <!-- Przyciski akcji -->
                                <span class="tooltiptext">
                                    <button id="pzmien<?= htmlspecialchars($album['id']) ?>" onclick="pokaz(<?= htmlspecialchars($album['id']) ?>)">Zmień tytuł</button>
                                </span>
                                
                                <!-- Formularz zmiany tytułu albumu -->
                                <form class="zmiana" method="post" action="konto.php?type=albumy&action=updateTitle">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($album['id']) ?>">
                                    <span class="tooltiptext">
                                        <input type="text" name="nowytytul" class="ntytul" id="ntytul<?= htmlspecialchars($album['id']) ?>" style="display:none;" required="required" placeholder="Nowy tytuł">
                                        <input type="submit" name="zmien" class="zmien" id="zmien<?= htmlspecialchars($album['id']) ?>" value="Zmień Tytuł" style="display:none;">
                                        <input type="submit" name="usun2" id="usun" value="Usuń Album">
                                    </span>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="tooltip">
                        <p class="atitle">Brak Albumów</p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <div class="konto-sekcja" id="zdjecia">
            <?php if ($type === 'zdjecia'): ?>
                <?php if (isset($selectedAlbumId)): ?>
                    <?php if ($photos): ?>
                        <div class="photo-grid">
                            <?php foreach ($photos as $photo): ?>
                                <div class="tooltip">
                                    <img class="glowne" src="photo/<?= htmlspecialchars($selectedAlbumId) ?>/<?= htmlspecialchars($photo['opis']) ?>" height="180" alt="Zdjęcie <?= htmlspecialchars($photo['id']) ?>">
                                    <p class="atitle"><?= htmlspecialchars($photo['opiszdjecia']) ?></p>
                                    <span class="tooltiptext">
                                        <button id="pzmien<?= htmlspecialchars($photo['id']) ?>" onclick="pokaz(<?= htmlspecialchars($photo['id']) ?>)">Zmień Opis</button>
                                    </span>
                                    <form class="zmiana" method="post" action="konto.php?type=zdjecia&action=updateDescription">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($photo['id']) ?>">
                                        <input type="hidden" name="idalbumu" value="<?= htmlspecialchars($selectedAlbumId) ?>">
                                        <input type="hidden" name="opis" value="<?= htmlspecialchars($photo['opis']) ?>">
                                        <span class="tooltiptext">
                                            <input type="text" name="nowyopis" class="ntytul" id="ntytul<?= htmlspecialchars($photo['id']) ?>" style="display:none;" required="required" placeholder="Nowy opis">
                                            <input type="submit" name="zmien" class="zmien" id="zmien<?= htmlspecialchars($photo['id']) ?>" value="Zmień Opis" style="display:none;">
                                            <input type="submit" name="usun2" id="usun" value="Usuń Zdjęcie">
                                        </span>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>Nie masz jeszcze żadnych zdjęć w tym albumie.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <?php if ($albums): ?>
                        <div class="tooltip">
                            <p class="atitle">Wybierz album:</p>
                            <ul>
                                <?php foreach ($albums as $album): ?>
                                    <li>
                                        <a href="konto.php?type=zdjecia&id=<?= htmlspecialchars($album['id']) ?>">
                                            <img class="glowne" src="photo/folder.png" height="180" alt="Album <?= htmlspecialchars($album['tytul']) ?>">
                                            <p class="atitle"><?= htmlspecialchars($album['tytul']) ?></p>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php else: ?>
                        <p>Nie masz jeszcze żadnych albumów.</p>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<footer id="footer">
    <address>Autor: Andrzej Brzeziński</address>	
</footer>

<script>
    function pokaz(numer) {
        document.getElementById('ntytul' + numer).style.display = "block";
        document.getElementById("zmien" + numer).style.display = "block";
        document.getElementById("pzmien" + numer).style.display = "none";
    }

    function isclicked() {
        document.getElementById("podajhaslo").style.display = "block";
    }
</script>
</body>
</html>
