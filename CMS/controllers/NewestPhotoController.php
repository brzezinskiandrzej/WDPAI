<?php

namespace App\Controllers;

use App\Services\PhotoService;

class NewestPhotoController
{
    public function showNewestPhotosPage(): void
    {
        session_start();

        // Ile zdjęć chcemy wyświetlić?
        $limit = 20;

        $photoService = new PhotoService();
        $photos = $photoService->getNewestPhotos($limit);

        $title = "Najnowsze zdjęcia – Image Space";

        // Ładujemy widok
        require __DIR__ . '/../views/newFotoView.php';
    }
}
