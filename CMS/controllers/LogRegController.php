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

    
    public function showForm(): void
    {
        session_start();
        
        
        $errorsLogin = $_SESSION['errorsLogin'] ?? [];
        $errorsRegister = $_SESSION['errorsRegister'] ?? [];
        unset($_SESSION['errorsLogin'], $_SESSION['errorsRegister']);

        require __DIR__ . '/../views/logRegView.php';
    }

   
    public function loginAction(): void
    {
        session_start();

        
        $login = $_POST['loginlog'] ?? '';
        $password = $_POST['haslolog'] ?? '';

        
        $result = $this->userService->loginUser($login, $password);

        if (isset($result['errors'])) {
            
            $_SESSION['errorsLogin'] = $result['errors'];
            header('Location: logrej.php'); 
            exit;
        }
        
        
        $user = $result['user'];
        $_SESSION['zalogowany'] = true;
        
        $_SESSION['tablica'] = [
            1 => $user['login'],
            2 => $user['haslo'],
            3 => $user['email'],
            4 => $user['zarejestrowany'],
            5 => $user['uprawnienia'],
            6 => $user['aktywny'],
            7 => $user['id']
        ];

        
        header('Location: index.php');
    }

   
    public function registerAction(): void
    {
        session_start();

        
        $login  = $_POST['login'] ?? '';
        $pass   = $_POST['haslo'] ?? '';
        $pass2  = $_POST['haslo2'] ?? '';
        $email  = $_POST['email'] ?? '';

        
        $result = $this->userService->registerUser($login, $pass, $pass2, $email);

        if (isset($result['errors'])) {
            $_SESSION['errorsRegister'] = $result['errors'];
            header('Location: logrej.php?sort=1'); 
            exit;
        }

        
        $_SESSION['zalogowany'] = true;
        
        $userData = $this->userService->loginUser($login, $pass);
        if (!isset($userData['errors'])) {
            
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
        
        
        header('Location: rejestracja-ok.php');
    }
}
