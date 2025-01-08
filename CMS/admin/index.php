<?php

session_start();


if (!isset($_SESSION['zalogowany']) || $_SESSION['zalogowany'] !== true) {
    header('Location: ../logrej.php');
    exit;
}


require_once __DIR__ . '/../classes/Database/DatabaseConnection.php';
require_once __DIR__ . '/../classes/Services/PaginationService.php';
require_once __DIR__ . '/../classes/Renderers/PaginationRenderer.php';
require_once __DIR__ . '/../classes/Repositories/AlbumRepository.php';
require_once __DIR__ . '/../classes/Services/AlbumService.php';
require_once __DIR__ . '/../classes/Repositories/PhotoRepository.php';
require_once __DIR__ . '/../classes/Services/PhotoService.php';
require_once __DIR__ . '/../classes/Repositories/UserRepository.php';
require_once __DIR__ . '/../classes/Services/UserService.php';

require_once __DIR__ . '/../controllers/AdminAlbumController.php';
require_once __DIR__ . '/../controllers/AdminPhotoController.php';
require_once __DIR__ . '/../controllers/AdminCommentController.php';
require_once __DIR__ . '/../controllers/AdminUserController.php';

$type = $_GET['type'] ?? null;


if (!$type) {
    require __DIR__ . '/../views/adminIndexView.php';
    exit;
}

switch ($type) {
    case 'albumy':
        
        $albumController = new \App\Controllers\AdminAlbumController();
        $action = $_GET['action'] ?? null;
        switch ($action) {
            case 'edit':
                $albumController->editAlbumTitle();
                break;
            case 'delete':
                $albumController->deleteAlbum();
                break;
            default:
                $albumController->showAlbumy();
                break;
        }
        break;
    case 'zdjecia':
        
        $photoController = new \App\Controllers\AdminPhotoController();
        $action = $_GET['action'] ?? null;
        switch ($action) {
            case 'accept':
                $photoController->acceptPhoto();
                break;
            case 'delete':
                $photoController->deletePhoto();
                break;
            default:
                $photoController->showZdjecia();
                break;
        }
        break;
    case 'kom':
            
            $commentController = new \App\Controllers\AdminCommentController();
            $action = $_GET['action'] ?? null;
            switch ($action) {
                case 'accept':
                    $commentController->acceptComment();
                    break;
                case 'delete':
                    $commentController->deleteComment();
                    break;
                case 'edit':
                    $commentController->editComment();
                    break;
                default:
                    $commentController->showKomentarze();
                    break;
            }
            break;
    case 'users':
            $userController = new \App\Controllers\AdminUserController();
            $action = $_GET['action'] ?? null;
            switch ($action) {
                case 'block':
                    $userController->blockUser();
                    break;
                case 'unblock':
                    $userController->unblockUser();
                    break;
                case 'delete':
                    $userController->deleteUser();
                    break;
                case 'change':
                    $userController->changeUserPermission();
                    break;
                default:
                    $userController->showUsers();
                    break;
            }
            break;
    default:
        require __DIR__ . '/views/adminIndexView.php';
        break;
}
