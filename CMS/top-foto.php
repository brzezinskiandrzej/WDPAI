<?php
// top-foto.php – punkt wejścia 
// (analogicznie do index.php, który wywołuje IndexController)

require_once __DIR__ . '/classes/Database/DatabaseConnection.php';
require_once __DIR__ . '/classes/Services/PaginationService.php';
require_once __DIR__ . '/classes/Repositories/PhotoRepository.php';
require_once __DIR__ . '/classes/Services/PhotoService.php';
require_once __DIR__ . '/controllers/TopFotoController.php';

// ewentualnie require_once do MenuService, jeśli nie używasz Composera
// lub composer autoload:
//// require __DIR__.'/vendor/autoload.php';

use App\Controllers\TopFotoController;

$controller = new TopFotoController();
$controller->showTopFotoPage();
