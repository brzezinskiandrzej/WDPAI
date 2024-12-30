<?php
// rejestracja-ok.php – punkt wejściowy dla strony sukcesu rejestracji

require_once __DIR__ . '/classes/Database/DatabaseConnection.php';
// W tym przypadku nie potrzebujemy nic więcej z repozytoriów/serwisów, 
// bo to tylko statyczna strona – ewentualnie do menu.
require_once __DIR__ . '/controllers/RejestracjaOkController.php';

use App\Controllers\RejestracjaOkController;

$controller = new RejestracjaOkController();
$controller->showSuccessPage();
