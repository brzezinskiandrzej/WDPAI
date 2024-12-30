<?php
// nowe-foto.php – punkt wejściowy dla "najnowszych zdjęć"

require_once __DIR__ . '/classes/Database/DatabaseConnection.php';
require_once __DIR__ . '/classes/Repositories/PhotoRepository.php';
require_once __DIR__ . '/classes/Services/PaginationService.php';
require_once __DIR__ . '/classes/Services/PhotoService.php';
// i ewentualnie: require_once __DIR__ . '/classes/Services/MenuService.php'; 
// (jeśli nie masz autoloadera Composera)

require_once __DIR__ . '/controllers/NewestPhotoController.php';

use App\Controllers\NewestPhotoController;

$controller = new NewestPhotoController();
$controller->showNewestPhotosPage();
