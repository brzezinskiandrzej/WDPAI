<?php

namespace App\Controllers;

use App\Services\PhotoService;

class TopFotoController
{
    public function showTopFotoPage(): void
    {
        session_start();

        // W razie potrzeby – parametry, np. ile zdjęć? 
        // (w oryginalnym kodzie to jest sztywne 20)
        $limit = 20;

        $photoService = new PhotoService();
        $topPhotos = $photoService->getTopRatedPhotos($limit);

        // Tytuł strony (do <title>)
        $title = "TOP $limit Of Image Space";

        // Wczytanie widoku
        require __DIR__ . '/../views/topFotoView.php';
    }
}
