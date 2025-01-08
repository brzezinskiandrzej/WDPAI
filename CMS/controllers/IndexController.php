<?php

namespace App\Controllers;

use App\Services\AlbumService;
use App\Services\PaginationService;
use App\Renderers\PaginationRenderer;

class IndexController
{
    private AlbumService $albumService;
    private PaginationService $paginationService;
    private PaginationRenderer $paginationRenderer;

    public function __construct()
    {
        $this->albumService = new AlbumService();
        $this->paginationService = new PaginationService();
        $this->paginationRenderer = new PaginationRenderer();
    }
    public function showIndexPage(): void
    {
        session_start();

        
        $currentSort = $_GET['sort'] ?? 'tytul';
        $currentSortType = $_GET['type'] ?? '';  
        $currentPage = isset($_GET['numer']) ? (int) $_GET['numer'] : 1;
        if ($currentPage < 1) {
            $currentPage = 1;
        }

        
        $limit = 20;

        
        $offset = $this->paginationService->calculateOffset($currentPage, $limit);
        $pagesCount = $this->albumService->getNumberOfPages($limit);

        
        $albums = $this->albumService->getPaginatedAlbumsWithAcceptedPhotos(
            $currentSort,
            $currentSortType,
            $currentPage,
            $limit
        );

        
        $title = "IMAGE SPACE – Strona główna";

        
        $paginationHtml = '';
        if ($pagesCount > 1) {
            $baseUrl = 'index.php';
            $additionalParams = [
                'sort' => $currentSort,
                'type' => $currentSortType
            ];
            $paginationHtml = $this->paginationRenderer->render($currentPage, $pagesCount, $baseUrl, $additionalParams);
        }

        
        require __DIR__ . '/../views/indexView.php';
    }
}
