<?php

namespace App\Controllers;

use App\Services\PhotoService;

class AlbumController
{
    public function showAlbumPage(): void
    {
        session_start();

        
        $albumId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($albumId <= 0) {
            
            header('Location: index.php');
            exit;
        }

        
        $currentPage = isset($_GET['numer']) ? (int)$_GET['numer'] : 1;
        if ($currentPage < 1) {
            $currentPage = 1;
        }

        
        $perPage = 20;

        
        $photoService = new PhotoService();
        $photos = $photoService->getAcceptedPhotosForAlbum($albumId, $currentPage, $perPage);
        $pagesCount = $photoService->getNumberOfPagesForAlbum($albumId, $perPage);

       
        $title = "Album #$albumId – Galeria";

        
        require __DIR__ . '/../views/albumView.php';
    }
}
