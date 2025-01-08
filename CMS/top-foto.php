<?php


require_once __DIR__ . '/classes/Database/DatabaseConnection.php';
require_once __DIR__ . '/classes/Services/PaginationService.php';
require_once __DIR__ . '/classes/Repositories/PhotoRepository.php';
require_once __DIR__ . '/classes/Services/PhotoService.php';
require_once __DIR__ . '/controllers/TopFotoController.php';



use App\Controllers\TopFotoController;

$controller = new TopFotoController();
$controller->showTopFotoPage();
