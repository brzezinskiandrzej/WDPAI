<?php
include 'javascript/include/indexzdjecia.php';
include 'javascript/include/awyloguj.php';
include 'javascript/include/arejestruj.php';
include 'javascript/include/aloguj.php';
include 'javascript/include/adodajalbum.php';
include 'javascript/include/adodajzdjecie.php';
include 'javascript/include/akonto.php';
include 'javascript/include/aadmin.php';

if (isset($numerstron) && $numerstron > 1) {
    echo '<form id="numery">';
    for ($i = 1; $i <= $numerstron; $i++) {
        echo '<input type="submit" name="numer" value="' . $i . '">';
    }
    echo '</form>';
}
?>
