<?php

namespace App\Controllers;

class RejestracjaOkController
{
    public function showSuccessPage(): void
    {
        session_start();
        
        
        require __DIR__ . '/../views/rejestracjaOkView.php';
    }
}
