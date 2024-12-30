<?php

namespace App\Controllers;

class RejestracjaOkController
{
    public function showSuccessPage(): void
    {
        session_start();
        // Można ewentualnie tu sprawdzić, czy użytkownik faktycznie jest świeżo zarejestrowany,
        // ale najczęściej wystarczy samo wczytanie widoku.
        
        require __DIR__ . '/../views/rejestracjaOkView.php';
    }
}
