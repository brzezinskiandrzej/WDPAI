<?php


require_once __DIR__ . '/classes/Database/DatabaseConnection.php';
require_once __DIR__ . '/classes/Services/PaginationService.php';
require_once __DIR__ . '/classes/Repositories/AlbumRepository.php';
require_once __DIR__ . '/classes/Services/AlbumService.php';
require_once __DIR__ . '/controllers/AddAlbumController.php';

use App\Controllers\AddAlbumController;

$controller = new AddAlbumController();


$action = $_GET['action'] ?? null;

switch ($action) {
    case 'store':

        $controller->store();
        break;

    default:

        $controller->showForm();
        break;
}
