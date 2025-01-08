<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Logowanie / Rejestracja</title>
    <link rel="stylesheet" href="style/logrejcss.css">
    <link rel="stylesheet" href="style/footerelement.css">
    <link href="style/menu.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<div id="container">

    <?php
    
    include 'javascript/include/menu.php';
    ?>

    <div id="logowanko">
        
        <form id="rejestracja" method="post" action="logrej.php?action=register">
            <h3>Rejestracja</h3>
            <input type="text" name="login"    id="login"    required placeholder="Wpisz Login"><br>
            <p id="loginmistake" class="warnings"></p>

            <input type="password" name="haslo"  id="haslo"   required placeholder="Wpisz Hasło"><br>
            <p id="passwdmistake" class="warnings"></p>

            <input type="password" name="haslo2" id="haslo2"  required placeholder="Potwierdź Hasło"><br>
            <p id="passwdrepeat"  class="warnings"></p>

            <input type="text" name="email"     id="email"   required placeholder="Wpisz E-mail"><br>
            <p id="emailcheck"    class="warnings"></p>

            <input type="submit" name="sumbitsignin" id="submitsignin" value="Zarejestruj"><br>
            
            
            <?php if (!empty($errorsRegister)): ?>
                <div class="error-box">
                <?php foreach ($errorsRegister as $err): ?>
                    <p><?= htmlspecialchars($err) ?></p>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </form>

        
        <form id="logowanie" method="post" action="logrej.php?action=login">
            <h3>Logowanie</h3>
            <div class="odstep">
                <input type="text"     name="loginlog" id="loginlog" required placeholder="Wpisz Login"><br>
                <p id="islogin" class="warnings"></p>
            </div>
            <div class="odstep">
                <input type="password" name="haslolog" id="haslolog" required placeholder="Wpisz Hasło"><br>
                <p id="ispasswd" class="warnings"></p>
            </div>
            <input type="submit" name="formlog" id="submitlog" value="Zaloguj">

            
            <?php if (!empty($errorsLogin)): ?>
                <div class="error-box">
                <?php foreach ($errorsLogin as $err): ?>
                    <p><?= htmlspecialchars($err) ?></p>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </form>

        
        <button id="rejestruj"  onclick="myFunction()">Załóż konto</button>
        <button id="loguj"      onclick="myFunction2()">Zaloguj się</button>
    </div>

    <script src="javascript/jquery-3.6.0.min.js"></script>
    <script src="javascript/logrejjs.js"></script>
    <script src="javascript/include/menunagorze.js"></script>
</div>

<footer id="footer">
    <address>Autor: Andrzej Brzeziński</address>
</footer> 
</body>
</html>
