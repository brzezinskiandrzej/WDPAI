<?php
// foto.php – front controller dla wyświetlania i obsługi zdjęć

require_once __DIR__ . '/classes/Database/DatabaseConnection.php';
require_once __DIR__ . '/classes/Repositories/PhotoRepository.php';
require_once __DIR__ . '/classes/Services/PaginationService.php';
require_once __DIR__ . '/classes/Services/PhotoService.php';
// require_once __DIR__ . '/classes/Repositories/CommentRepository.php';
// require_once __DIR__ . '/classes/Services/CommentService.php';
require_once __DIR__ . '/controllers/FotoController.php';

use App\Controllers\FotoController;

$controller = new FotoController();

// Sprawdzamy, czy user wykonał akcję (np. ocenianie, dodawanie komentarza)
$action = $_GET['action'] ?? null;

switch ($action) {
    case 'ratePhoto':
        $controller->ratePhotoAction();
        break;
    case 'addComment':
        $controller->addCommentAction();
        break;
    default:
        // Domyślnie wyświetlamy stronę ze zdjęciem
        $controller->showPhotoPage();
        break;
}
