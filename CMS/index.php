<?php


use App\Controllers\IndexController;


require_once __DIR__ . '/classes/Database/DatabaseConnection.php';
require_once __DIR__ . '/classes/Repositories/AlbumRepository.php';
require_once __DIR__ . '/classes/Services/PaginationService.php';
require_once __DIR__ . '/classes/Renderers/PaginationRenderer.php';
require_once __DIR__ . '/classes/Services/AlbumService.php';
require_once __DIR__ . '/controllers/IndexController.php';


session_start();


$controller = new IndexController();
$controller->showIndexPage();
