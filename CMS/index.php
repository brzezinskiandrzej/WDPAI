<?php
// index.php (wejście główne)

use App\Controllers\IndexController;

// **Prosta autoload** – jeśli używasz Composera, wystarczy `require 'vendor/autoload.php';`
require_once __DIR__ . '/classes/Database/DatabaseConnection.php';
require_once __DIR__ . '/classes/Repositories/AlbumRepository.php';
require_once __DIR__ . '/classes/Services/PaginationService.php';
require_once __DIR__ . '/classes/Renderers/PaginationRenderer.php';
require_once __DIR__ . '/classes/Services/AlbumService.php';
require_once __DIR__ . '/controllers/IndexController.php';

// Start sesji
session_start();

// Tworzymy kontroler i wyświetlamy stronę główną
$controller = new IndexController();
$controller->showIndexPage();
