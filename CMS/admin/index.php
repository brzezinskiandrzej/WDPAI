<?php
/**
 * admin/index.php – Główny plik (Front Controller) panelu admina.
 * TYLKO OBSŁUGA SEKCJI 'albumy' w tej wersji.
 */

session_start();

// Zabezpieczenie: jeśli ktoś nie jest zalogowany albo nie ma uprawnień 'administrator' lub 'moderator',
// możesz go przekierować do logowania:
if (!isset($_SESSION['zalogowany']) || $_SESSION['zalogowany'] !== true) {
    header('Location: ../logrej.php');
    exit;
}

// Wczytujemy pliki z serwisami/repozytoriami (o ile nie mamy autoloadera):
require_once __DIR__ . '/../classes/Database/DatabaseConnection.php';
require_once __DIR__ . '/../classes/Services/PaginationService.php';
require_once __DIR__ . '/../classes/Repositories/AlbumRepository.php';
require_once __DIR__ . '/../classes/Services/AlbumService.php';
require_once __DIR__ . '/../classes/Repositories/PhotoRepository.php';
require_once __DIR__ . '/../classes/Services/PhotoService.php';
require_once __DIR__ . '/../classes/Repositories/UserRepository.php';
require_once __DIR__ . '/../classes/Services/UserService.php';
// Kontroler do albumów
require_once __DIR__ . '/../controllers/AdminAlbumController.php';
require_once __DIR__ . '/../controllers/AdminPhotoController.php';
require_once __DIR__ . '/../controllers/AdminCommentController.php';
require_once __DIR__ . '/../controllers/AdminUserController.php';
// Front Controller: decydujemy, jaką sekcję wyświetlić
$type = $_GET['type'] ?? null;

// Na początek: jeśli nie określono 'type', pokazujemy stronę główną panelu
if (!$type) {
    require __DIR__ . '/../views/adminIndexView.php';
    exit;
}

switch ($type) {
    case 'albumy':
        // Obsługa sekcji "Albumy"
        $albumController = new \App\Controllers\AdminAlbumController();
        // Sprawdzamy, czy jest jakaś akcja
        $action = $_GET['action'] ?? null;
        switch ($action) {
            case 'edit':
                $albumController->editAlbumTitle();
                break;
            case 'delete':
                $albumController->deleteAlbum();
                break;
            default:
                // Wyświetlamy listę albumów
                $albumController->showAlbumy();
                break;
        }
        break;
    case 'zdjecia':
        // NOWA SEKCJA
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
                // pokazujemy listę zdjęć
                $photoController->showZdjecia();
                break;
        }
        break;
    case 'kom':
            // NOWA SEKCJA: Komentarze
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
                    // brak akcji -> showUsers
                    $userController->showUsers();
                    break;
            }
            break;
    default:
        // w tej wersji – brak innej obsługi
        // docelowo dołożymy 'zdjecia', 'kom', 'users'
        // na razie wracamy do adminIndexView
        require __DIR__ . '/views/adminIndexView.php';
        break;
}
