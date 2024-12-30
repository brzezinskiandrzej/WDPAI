<?php
session_start();
?>
<html>
<head>
<style>
table, td, th {
  border: 1px solid black;
}
table {
  border-collapse: collapse;
}
form {
  text-align: center;
}
div {
  width: 100%;
  float: left;
  margin-bottom: 2px;
}
input {
  margin: 5px;
}
</style>
</head>
<body>
<div>
<form>
<input type="hidden" name="id" value="<?php if (isset($_GET['zmien'])) echo $_GET['zmien']; else echo 0; ?>">
<input name="p1" type="text" placeholder="Wpisz Nazwisko" required="required" value="<?php if (isset($_GET['zmien'])) {
    require 'javascript/databaseconnection.php';
    $select = pg_query($conn, "SELECT * FROM tabela WHERE id=" . intval($_GET['zmien']));
    $row2 = pg_fetch_assoc($select);
    pg_close($conn);
    echo htmlspecialchars($row2['p1']);
} ?>">
<br>
<input name="p2" type="date" placeholder="Wpisz Datę" required="required" value="<?php if (isset($_GET['zmien'])) {
    require 'javascript/databaseconnection.php';
    $select = pg_query($conn, "SELECT * FROM tabela WHERE id=" . intval($_GET['zmien']));
    $row2 = pg_fetch_assoc($select);
    pg_close($conn);
    echo date('Y-m-d', strtotime($row2['p2']));
} ?>">
<br>
<input name="p3" type="number" placeholder="Wpisz Numer" required="required" value="<?php if (isset($_GET['zmien'])) {
    require 'javascript/databaseconnection.php';
    $select = pg_query($conn, "SELECT * FROM tabela WHERE id=" . intval($_GET['zmien']));
    $row2 = pg_fetch_assoc($select);
    pg_close($conn);
    echo htmlspecialchars($row2['p3']);
} ?>">
<br>
<input name="submit" type="submit" value="Dodaj">

<?php 
if (isset($_GET['zmien']))
    echo '<input name="anuluj" type="button" onclick="location.href=\'laczeniezbazadanych.php?\'" value="Anuluj">';
?>
</form>
</div>
<br>

<?php

$_SESSION['start'] = 0;
$bool = false;
$i = 0;

/* Połączenie z serwerem PostgreSQL */
require 'javascript/databaseconnection.php';
$strony = pg_query($conn, "SELECT count(*) AS ile FROM tabela");
$row = pg_fetch_assoc($strony);
$numerstron = ceil($row['ile'] / 5);

if (isset($_GET['numer'])) {
    $_SESSION['strona'] = ($_GET['numer'] - 1) * 5;
} else {
    $_SESSION['strona'] = 0;
}

/* Ustawienie strony kodowej */
pg_query($conn, "SET NAMES 'UTF8'");
pg_query($conn, "SET CLIENT_ENCODING TO 'UTF8'");

if (isset($_GET['submit'])) {
    if ($_GET['id'] != 0) {
        $sql = "UPDATE tabela
                SET p1='" . pg_escape_string($conn, $_GET['p1']) . "',
                    p2='" . pg_escape_string($conn, $_GET['p2']) . "',
                    p3='" . intval($_GET['p3']) . "'
                WHERE id=" . intval($_GET['id']);
    } else {
        $zapytanie = pg_query($conn, "SELECT * FROM tabela ORDER BY p1");

        while ($wynik = pg_fetch_assoc($zapytanie)) {
            if ($_GET['p1'] == $wynik['p1'])
                $bool = true;
        }

        if ($bool == false) {
            $sql = "INSERT INTO tabela (p1, p2, p3)
                    VALUES (
                        '" . pg_escape_string($conn, $_GET['p1']) . "',
                        '" . pg_escape_string($conn, $_GET['p2']) . "',
                        '" . intval($_GET['p3']) . "'
                    )";
        } else {
            echo '<p align="center">Rekord o podanym nazwisku już istnieje</p>';
        }
    }

    if ($bool == false) {
        pg_query($conn, $sql);
    }
}

/* Wykonanie zapytania SQL */
if (isset($_GET['usun'])) {
    pg_query($conn, "DELETE FROM tabela WHERE id=" . intval($_GET['usun']));
}

/* Pobranie i wyświetlenie wszystkich rekordów wyniku zapytania */
if (isset($_GET['sort']))
    $_SESSION['sortowanie'] = $_GET['sort'];

if (isset($_SESSION['sortowanie']))
    $result = pg_query($conn, "SELECT * FROM tabela ORDER BY " . pg_escape_string($conn, $_SESSION['sortowanie']) . " LIMIT " . intval($_SESSION['strona']) . ", 5");
else
    $result = pg_query($conn, "SELECT * FROM tabela ORDER BY p1 LIMIT " . intval($_SESSION['strona']) . ", 5");

echo '<TABLE align="center">';
echo '<thead>
<tr>
<th><a name="sort" href="laczeniezbazadanych.php?sort=p1">' . ((isset($_SESSION['sortowanie']) && $_SESSION['sortowanie'] != "p1") ? "Pole 1" : "Pole 1 ▼") . '</th>
<th><a name="sort" href="laczeniezbazadanych.php?sort=p2">' . ((isset($_SESSION['sortowanie']) && $_SESSION['sortowanie'] == "p2") ? "Pole 2 ▼" : "Pole 2") . '</th>
<th><a name="sort" href="laczeniezbazadanych.php?sort=p3">' . ((isset($_SESSION['sortowanie']) && $_SESSION['sortowanie'] == "p3") ? "Pole 3 ▼" : "Pole 3") . '</th>
<th COLSPAN=2>Operacja</th>
</tr>
</thead>
<tbody>';

while ($row = pg_fetch_assoc($result)) {
    echo '<tr>
<td>' . htmlspecialchars($row['p1']) . '</td>
<td>' . htmlspecialchars($row['p2']) . '</td>
<td>' . htmlspecialchars($row['p3']) . '</td>
<TD><a name="zmien" href="laczeniezbazadanych.php?zmien=' . intval($row['id']) . '">zmień</td>
<td><a name="usun" href="laczeniezbazadanych.php?usun=' . intval($row['id']) . '" onclick="return confirm(\'czy na pewno chcesz usunac rekord?\')">usuń</td>
</tr>';
}
echo '</table>';
pg_close($conn);

echo '<form id="numery">';
for ($i = 1; $i <= $numerstron; $i++) {
    echo '<input type="submit" name="numer" value=' . $i . '>';
}
echo '</form>';
?>

</body>
</html>
