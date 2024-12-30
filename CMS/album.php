<?php
// album.php – punkt wejścia dla widoku "albumu"

require_once __DIR__ . '/classes/Database/DatabaseConnection.php';
require_once __DIR__ . '/classes/Repositories/PhotoRepository.php';
require_once __DIR__ . '/classes/Services/PaginationService.php';
require_once __DIR__ . '/classes/Services/PhotoService.php';

// Ewentualnie also for album data if needed
// require_once __DIR__ . '/classes/Repositories/AlbumRepository.php';
// require_once __DIR__ . '/classes/Services/AlbumService.php';

require_once __DIR__ . '/controllers/AlbumController.php';

use App\Controllers\AlbumController;

$controller = new AlbumController();
$controller->showAlbumPage();
