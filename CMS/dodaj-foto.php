<?php
// dodaj-foto.php – front controller do dodawania zdjęcia

require_once __DIR__ . '/classes/Database/DatabaseConnection.php';
require_once __DIR__ . '/classes/Services/PaginationService.php';
require_once __DIR__ . '/classes/Repositories/AlbumRepository.php';
require_once __DIR__ . '/classes/Services/AlbumService.php';
require_once __DIR__ . '/classes/Repositories/PhotoRepository.php';
require_once __DIR__ . '/classes/Services/PhotoService.php';
require_once __DIR__ . '/controllers/AddPhotoController.php';

use App\Controllers\AddPhotoController;

$controller = new AddPhotoController();

$action = $_GET['action'] ?? null;
switch ($action) {
    case 'store':
        $controller->store();
        break;
    default:
        $controller->showForm();
        break;
}
