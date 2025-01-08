<?php

namespace App\Services;

use App\Repositories\AlbumRepository;

class AlbumService
{
    private AlbumRepository $albumRepository;
    private PaginationService $paginationService;

    public function __construct()
    {
      
        $this->albumRepository = new AlbumRepository();
        $this->paginationService = new PaginationService();
    }

   
    public function getPaginatedAlbumsWithAcceptedPhotos(
        ?string $sortBy,
        ?string $sortType,
        int $page,
        int $limit
    ): array {
        

        $offset = $this->paginationService->calculateOffset($page, $limit);
        return $this->albumRepository->findAlbumsWithAcceptedPhotos($sortBy, $sortType, $limit, $offset);
    }

    public function getNumberOfPages(int $itemsPerPage): int
    {
        $totalAlbums = $this->albumRepository->countAllWithAcceptedPhotos();
        return $this->paginationService->calculatePages($totalAlbums, $itemsPerPage);
    }
   
    public function createAlbum(int $userId, string $title): array
    {
        $errors = [];

       
        if (strlen($title) < 1 || strlen($title) > 100) {
            $errors[] = "Nazwa albumu musi mieć od 1 do 100 znaków.";
        } 
       

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        
        $albumId = $this->albumRepository->createAlbum($userId, $title);
        if (!$albumId) {
            return ['errors' => ["Wystąpił błąd podczas tworzenia albumu w bazie."]];
        }

       
        $folder = __DIR__ . '/../../photo/' . $albumId;
        if (!file_exists($folder)) {
            $ok = @mkdir($folder, 0777, true);
            if (!$ok) {
               
                return ['errors' => ["Album został dodany, ale nie udało się utworzyć folderu na zdjęcia."]];
            }
        }

        
        return [
            'album_id' => $albumId
        ];
    }
    public function getAlbumsByUser(int $userId): array
    {
        return $this->albumRepository->getAlbumsByUser($userId);
    }
    public function updateAlbumTitle(int $albumId, string $newTitle): array
    {
        $errors = [];

       
        if (empty($newTitle)) {
            $errors[] = "nazwa albumu nie może być pusta";
            return ['errors' => $errors];
        }
        if (strlen($newTitle) > 100) {
            $errors[] = "nazwa albumu musi mieć od 1 do 100 znaków";
            return ['errors' => $errors];
        }

       
        $success = $this->albumRepository->updateAlbumTitle($newTitle, $albumId);
        if (!$success) {
            $errors[] = "Nie udało się zaktualizować tytułu albumu.";
            return ['errors' => $errors];
        }

        return ['success' => true];
    }
    public function deleteAlbum(int $albumId): array
    {
        $errors = [];

        
        $success = $this->albumRepository->deleteAlbum($albumId);
        if (!$success) {
            $errors[] = "Nie udało się usunąć albumu.";
            return ['errors' => $errors];
        }

        return ['success' => true];
    }
    public function countAllAlbumsForAdmin(): int
    {
        return $this->albumRepository->countAllAlbumsForAdmin();
    }
    public function getAlbumsForAdmin(int $offset, int $limit): array
    {
        return $this->albumRepository->findAlbumsForAdmin($offset, $limit);
    }
    public function deleteAlbumCompletely(int $albumId): void
    {
        
        $this->albumRepository->deleteAlbumCompletely($albumId);

       
        $this->deleteAlbumFolder($albumId);
    }
    private function deleteAlbumFolder(int $albumId): void
    {
        $directory = __DIR__ . "/../../photo/" . $albumId; 

        if (is_dir($directory)) {
            
            $this->deleteDirectoryRecursively($directory);
        } else {
            
            throw new \Exception("Katalog albumu nie istnieje.");
        }
    }
    private function deleteDirectoryRecursively(string $dir): void
    {
        $items = scandir($dir);
        if ($items === false) {
            throw new \Exception("Nie można odczytać zawartości katalogu: $dir");
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $dir . DIRECTORY_SEPARATOR . $item;

            if (is_dir($path)) {
                $this->deleteDirectoryRecursively($path);
            } else {
                if (!unlink($path)) {
                    throw new \Exception("Nie można usunąć pliku: $path");
                }
            }
        }

        if (!rmdir($dir)) {
            throw new \Exception("Nie można usunąć katalogu: $dir");
        }
    }
}
