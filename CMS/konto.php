<?php



session_start();


require_once __DIR__ . '/classes/Database/DatabaseConnection.php';
require_once __DIR__ . '/classes/Services/PaginationService.php';
require_once __DIR__ . '/classes/Repositories/UserRepository.php';
require_once __DIR__ . '/classes/Services/UserService.php';
require_once __DIR__ . '/classes/Services/AlbumService.php';
require_once __DIR__ . '/classes/Repositories/AlbumRepository.php';
require_once __DIR__ . '/classes/Services/PhotoService.php';
require_once __DIR__ . '/classes/Repositories/PhotoRepository.php';
require_once __DIR__ . '/controllers/KontoController.php';


use App\Controllers\KontoController;


$controller = new KontoController();


$type = $_GET['type'] ?? 'dane';
$action = $_GET['action'] ?? null;
error_log("type: " . $type);

$controller->handleRequest($type, $action);
?>
