<?php
// logrej.php – front controller dla logowania/rejestracji

require_once __DIR__ . '/classes/Database/DatabaseConnection.php';
require_once __DIR__ . '/classes/Repositories/UserRepository.php';
require_once __DIR__ . '/classes/Services/UserService.php';
require_once __DIR__ . '/controllers/LogRegController.php';

use App\Controllers\LogRegController;

$controller = new LogRegController();

// Sprawdzamy, jaką akcję user wykonuje: 
$action = $_GET['action'] ?? null;

switch ($action) {
    case 'login':
        $controller->loginAction();
        break;
    case 'register':
        $controller->registerAction();
        break;
    default:
        $controller->showForm(); 
        // wyświetla formularz logowania/rejestracji
        break;
}
