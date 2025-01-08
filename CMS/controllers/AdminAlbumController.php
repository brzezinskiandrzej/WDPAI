<?php
namespace App\Controllers;

use App\Repositories\AlbumRepository;
use App\Services\AlbumService;
use App\Services\PaginationService;
use App\Renderers\PaginationRenderer;


class AdminAlbumController
{
    private AlbumService $albumService;
    private PaginationService $paginationService;
    private PaginationRenderer $paginationRenderer;

    public function __construct()
    {

        $albumRepo = new AlbumRepository($this->conn);
        $this->albumService = new AlbumService($albumRepo);
        $this->paginationService = new PaginationService();
        $this->paginationRenderer = new PaginationRenderer();
    }

    public function showAlbumy()
    {

        if (!isset($_SESSION['strona'])) {
            $_SESSION['strona'] = 0;
        }
        if (isset($_GET['numer'])) {
            $_SESSION['strona'] = ((int)$_GET['numer'] - 1) * 30;
        }
        $currentPage = isset($_GET['numer']) ? (int)$_GET['numer'] : 1;
        if ($currentPage < 1) {
            $currentPage = 1;
        }
        
        $limit  = 30;

        $offset = $this->paginationService->calculateOffset($currentPage, $limit);
        $totalCount = $this->albumService->countAllAlbumsForAdmin();
        $numerstron = ceil($totalCount / $limit);

        
        $albums = $this->albumService->getAlbumsForAdmin($offset, $limit);

        
        if (!isset($_SESSION['warning3'])) {
            $_SESSION['warning3'] = '';
        }
        $paginationHtml = '';
        if ($numerstron > 1) {
            $baseUrl = 'index.php';
            $additionalParams = [
                'type' => 'albumy'
                
            ];
            $paginationHtml = $this->paginationRenderer->render($currentPage, $numerstron, $baseUrl, $additionalParams);
        }
        
        require __DIR__ . '/../views/adminAlbumyView.php';
    }

    
    public function editAlbumTitle()
    {
        if (!isset($_POST['id']) || !isset($_POST['nowytytul'])) {
            header('Location: index.php?type=albumy');
            exit;
        }
        $albumId = (int)$_POST['id'];
        $newTitle= $_POST['nowytytul'];

        
        $error = $this->validateAlbumTitle($newTitle);
        if ($error !== null) {
            $_SESSION['warning3'] = $error;
            header('Location: index.php?type=albumy');
            exit;
        }

        
        $this->albumService->updateAlbumTitle($albumId, $newTitle);

        $_SESSION['warning3'] = 'Tytuł został poprawnie zmieniony 🙂';
        header('Location: index.php?type=albumy');
        exit;
    }

    
    public function deleteAlbum()
    {
        if (!isset($_POST['id'])) {
            header('Location: index.php?type=albumy');
            exit;
        }
        $albumId = (int)$_POST['id'];

        
        $this->albumService->deleteAlbumCompletely($albumId);

        $_SESSION['warning3'] = 'Album został usunięty';
        header('Location: index.php?type=albumy');
        exit;
    }

    
    private function validateAlbumTitle(string $title): ?string
    {
        $pattern = "/^.{1,100}$/";
        $pattern2 = "/[^\s]+/";
        if (preg_match($pattern, $title)) {
            if (preg_match($pattern2, $title)) {
                return null; 
            } else {
                return "nazwa albumu nie może być pusta";
            }
        } else {
            return "nazwa albumu musi mieć od 1 do 100 znaków";
        }
    }
}
