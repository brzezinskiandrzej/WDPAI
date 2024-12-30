<?php

namespace App\Controllers;

use App\Services\PhotoService;

class AlbumController
{
    public function showAlbumPage(): void
    {
        session_start();

        // 1. Pobieramy z GET:
        $albumId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($albumId <= 0) {
            // Można zrobić np. redirect na stronę główną, 
            // lub wyświetlić komunikat "Błędny album"
            header('Location: index.php');
            exit;
        }

        // 2. Ewentualnie numer strony
        $currentPage = isset($_GET['numer']) ? (int)$_GET['numer'] : 1;
        if ($currentPage < 1) {
            $currentPage = 1;
        }

        // 3. Ile zdjęć na stronę? 
        // Możesz wziąć np. 20 (tak jak w oryginale)
        $perPage = 20;

        // 4. Pobranie zdjęć z serwisu
        $photoService = new PhotoService();
        $photos = $photoService->getAcceptedPhotosForAlbum($albumId, $currentPage, $perPage);
        $pagesCount = $photoService->getNumberOfPagesForAlbum($albumId, $perPage);

        // 5. Tytuł (do <title>) – ewentualnie można pobrać też tytuł albumu z bazy 
        // (przez AlbumRepository) i wstawić w widoku
        $title = "Album #$albumId – Galeria";

        // 6. Ładujemy widok albumu
        require __DIR__ . '/../views/albumView.php';
    }
}
