<?php
class SessionInitializer {
    public static function initialize() {
        if(!isset($_SESSION['zalogowany']))
            $_SESSION['zalogowany'] = false;
        if(!isset($_SESSION['uprawnienia']))
            $_SESSION['uprawnienia'] = '';
        if(!isset($_SESSION['tablica']))
            $_SESSION['tablica'] = array();
    }
}

SessionInitializer::initialize();
?>
