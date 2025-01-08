<?php


require_once __DIR__ . '/classes/Database/DatabaseConnection.php';
require_once __DIR__ . '/classes/Repositories/PhotoRepository.php';
require_once __DIR__ . '/classes/Services/PaginationService.php';
require_once __DIR__ . '/classes/Services/PhotoService.php';

require_once __DIR__ . '/controllers/FotoController.php';

use App\Controllers\FotoController;

$controller = new FotoController();


$action = $_GET['action'] ?? null;

switch ($action) {
    case 'ratePhoto':
        $controller->ratePhotoAction();
        break;
    case 'addComment':
        $controller->addCommentAction();
        break;
    default:
        
        $controller->showPhotoPage();
        break;
}
