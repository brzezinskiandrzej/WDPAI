<?php

namespace App\Controllers;

use App\Services\PhotoService;

class TopFotoController
{
    public function showTopFotoPage(): void
    {
        session_start();

        
        $limit = 20;

        $photoService = new PhotoService();
        $topPhotos = $photoService->getTopRatedPhotos($limit);

        
        $title = "TOP $limit Of Image Space";

        
        require __DIR__ . '/../views/topFotoView.php';
    }
}
