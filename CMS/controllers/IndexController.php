<?php

namespace App\Controllers;

use App\Services\AlbumService;

class IndexController
{
    public function showIndexPage(): void
    {
        session_start();

        // Odczyt parametrów
        $currentSort = $_GET['sort'] ?? 'tytul';
        $currentSortType = $_GET['type'] ?? '';  // '' lub 'DESC'
        $currentPage = isset($_GET['numer']) ? (int) $_GET['numer'] : 1;
        if ($currentPage < 1) {
            $currentPage = 1;
        }

        // Wywołanie warstwy logiki
        $albumService = new AlbumService();
        $albums = $albumService->getPaginatedAlbumsWithAcceptedPhotos(
            $currentSort,
            $currentSortType,
            $currentPage,
            20
        );

        $pagesCount = $albumService->getNumberOfPages(20);

        // Załadowanie widoku. Można przekazać zmienne przez extract() lub w dowolny inny sposób.
        $title = "IMAGE SPACE – Strona główna";
        require __DIR__ . '/../views/indexView.php';
    }
}
