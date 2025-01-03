<?php

namespace App\Services;

use App\Repositories\AlbumRepository;

class AlbumService
{
    private AlbumRepository $albumRepository;
    private PaginationService $paginationService;

    public function __construct()
    {
        // Tutaj wstrzykujemy zależności – w większych projektach raczej przez DI Container
        $this->albumRepository = new AlbumRepository();
        $this->paginationService = new PaginationService();
    }

    /**
     * Zwraca tablicę albumów gotowych do wyświetlenia na index.php
     */
    public function getPaginatedAlbumsWithAcceptedPhotos(
        ?string $sortBy,
        ?string $sortType,
        int $page,
        int $limit
    ): array {
        // Możesz tu użyć SortingService, np.:
        // list($sortBy, $sortType) = (new SortingService())->validateSortParams($sortBy, $sortType);

        $offset = $this->paginationService->calculateOffset($page, $limit);
        return $this->albumRepository->findAlbumsWithAcceptedPhotos($sortBy, $sortType, $limit, $offset);
    }

    public function getNumberOfPages(int $itemsPerPage): int
    {
        $totalAlbums = $this->albumRepository->countAllWithAcceptedPhotos();
        return $this->paginationService->calculatePages($totalAlbums, $itemsPerPage);
    }
    /**
     * Tworzy nowy album dla zalogowanego użytkownika.
     * Może także tworzyć folder "photo/<id>".
     */
    public function createAlbum(int $userId, string $title): array
    {
        $errors = [];

        // 1. Walidacja tytułu
        if (strlen($title) < 1 || strlen($title) > 100) {
            $errors[] = "Nazwa albumu musi mieć od 1 do 100 znaków.";
        } 
        // Możesz dodać regex, sprawdzić czy nie jest pusta itp.

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        // 2. Zapis do bazy
        $albumId = $this->albumRepository->createAlbum($userId, $title);
        if (!$albumId) {
            return ['errors' => ["Wystąpił błąd podczas tworzenia albumu w bazie."]];
        }

        // 3. Tworzenie folderu (jeśli chcesz tak, jak w starym kodzie)
        $folder = __DIR__ . '/../../photo/' . $albumId;
        if (!file_exists($folder)) {
            $ok = @mkdir($folder, 0777, true);
            if (!$ok) {
                // Nie udało się stworzyć folderu – 
                // teoretycznie możesz usunąć album z bazy, 
                // albo zostawić i dać komunikat o błędzie.
                return ['errors' => ["Album został dodany, ale nie udało się utworzyć folderu na zdjęcia."]];
            }
        }

        // 4. Sukces – zwracamy id albumu
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

        // Walidacja tytułu
        if (empty($newTitle)) {
            $errors[] = "nazwa albumu nie może być pusta";
            return ['errors' => $errors];
        }
        if (strlen($newTitle) > 100) {
            $errors[] = "nazwa albumu musi mieć od 1 do 100 znaków";
            return ['errors' => $errors];
        }

        // Aktualizacja tytułu albumu
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

        // Usunięcie albumu
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
}
