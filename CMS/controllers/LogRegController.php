<?php

namespace App\Controllers;

use App\Services\UserService;

class LogRegController
{
    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    /**
     * Główny endpoint wyświetlający formularz logowania/rejestracji.
     * Ewentualnie przyjmuje parametry (np. `?sort=1` – by pokazać rejestrację).
     */
    public function showForm(): void
    {
        session_start();
        
        // Możesz użyć np. $_GET['mode'] by zdecydować, czy od razu pokazać blok Rejestracji
        // $mode = $_GET['mode'] ?? 'login'; 
        // Ale w oryginale pewnie i tak to przełączasz w widoku JS-em.

        // Rzucamy do widoku
        $errorsLogin = $_SESSION['errorsLogin'] ?? [];
        $errorsRegister = $_SESSION['errorsRegister'] ?? [];
        unset($_SESSION['errorsLogin'], $_SESSION['errorsRegister']);

        require __DIR__ . '/../views/logRegView.php';
    }

    /**
     * Obsługuje żądanie logowania (formularz "Zaloguj").
     */
    public function loginAction(): void
    {
        session_start();

        // Zakładamy, że formularz wysyła 'loginlog' i 'haslolog'
        $login = $_POST['loginlog'] ?? '';
        $password = $_POST['haslolog'] ?? '';

        // Weryfikacja
        $result = $this->userService->loginUser($login, $password);

        if (isset($result['errors'])) {
            // Błędy – zapisz do sesji
            $_SESSION['errorsLogin'] = $result['errors'];
            header('Location: logrej.php'); // wróć do formularza
            exit;
        }
        
        // Sukces – logujemy
        $user = $result['user'];
        $_SESSION['zalogowany'] = true;
        // w Twoim kodzie to $_SESSION['tablica'][1] = login, [2] = haslo, ...
        $_SESSION['tablica'] = [
            1 => $user['login'],
            2 => $user['haslo'],
            3 => $user['email'],
            4 => $user['zarejestrowany'],
            5 => $user['uprawnienia'],
            6 => $user['aktywny'],
            7 => $user['id']
        ];

        // Przekierowanie np. na index.php
        header('Location: index.php');
    }

    /**
     * Obsługa rejestracji (formularz "Zarejestruj").
     */
    public function registerAction(): void
    {
        session_start();

        // Pola: login, haslo, haslo2, email
        $login  = $_POST['login'] ?? '';
        $pass   = $_POST['haslo'] ?? '';
        $pass2  = $_POST['haslo2'] ?? '';
        $email  = $_POST['email'] ?? '';

        // Rejestracja
        $result = $this->userService->registerUser($login, $pass, $pass2, $email);

        if (isset($result['errors'])) {
            $_SESSION['errorsRegister'] = $result['errors'];
            header('Location: logrej.php?sort=1'); // ?sort=1 może pokazywać blok rejestracji
            exit;
        }

        // Sukces rejestracji -> automatycznie logujemy użytkownika? 
        // (tak bywa w oryginalnym kodzie) lub przekierowujemy do "rejestracja-ok.php".
        $_SESSION['zalogowany'] = true;
        // Znajdź świeżo utworzonego usera:
        $userData = $this->userService->loginUser($login, $pass);
        if (!isset($userData['errors'])) {
            // Zapisz do sesji
            $user = $userData['user'];
            $_SESSION['tablica'] = [
                1 => $user['login'],
                2 => $user['haslo'],
                3 => $user['email'],
                4 => $user['zarejestrowany'],
                5 => $user['uprawnienia'],
                6 => $user['aktywny'],
                7 => $user['id']
            ];
        }
        
        // Przekierowanie na np. "rejestracja-ok.php" 
        // albo od razu "index.php" (w Twoim starym kodzie było "rejestracja-ok.php").
        header('Location: rejestracja-ok.php');
    }
}
