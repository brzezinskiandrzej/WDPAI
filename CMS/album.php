<?php


require_once __DIR__ . '/classes/Database/DatabaseConnection.php';
require_once __DIR__ . '/classes/Repositories/PhotoRepository.php';
require_once __DIR__ . '/classes/Services/PaginationService.php';
require_once __DIR__ . '/classes/Services/PhotoService.php';



require_once __DIR__ . '/controllers/AlbumController.php';

use App\Controllers\AlbumController;

$controller = new AlbumController();
$controller->showAlbumPage();
