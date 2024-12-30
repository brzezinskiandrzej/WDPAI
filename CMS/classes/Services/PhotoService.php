<?php

namespace App\Services;

use App\Repositories\PhotoRepository;
use App\Services\PaginationService;

class PhotoService
{
    private PhotoRepository $photoRepository;
    private PaginationService $paginationService;

    public function __construct()
    {
        // W większych projektach wstrzyknięcie przez DI Container
        $this->photoRepository = new PhotoRepository();
        $this->paginationService = new PaginationService();
    }

    /**
     * Zwraca listę top zdjęć z bazy,
     * ewentualnie można tutaj dodać logikę filtrowania itp.
     *
     * @param int $limit
     * @return array
     */
    public function getTopRatedPhotos(int $limit = 20): array
    {
        return $this->photoRepository->findTopRatedPhotos($limit);
    }
    public function getAcceptedPhotosCountForAlbum(int $albumId): int
    {
        return $this->photoRepository->countAcceptedPhotosByAlbum($albumId);
    }

    /**
     * Pobiera zdjęcia (zaakceptowane) z danego albumu, z uwzględnieniem paginacji.
     */
    public function getAcceptedPhotosForAlbum(int $albumId, int $page, int $perPage): array
    {
        $offset = $this->paginationService->calculateOffset($page, $perPage);
        return $this->photoRepository->findAcceptedPhotosByAlbum($albumId, $perPage, $offset);
    }

    /**
     * Liczy liczbę stron (dla zaakceptowanych zdjęć w albumie).
     */
    public function getNumberOfPagesForAlbum(int $albumId, int $perPage): int
    {
        $total = $this->getAcceptedPhotosCountForAlbum($albumId);
        return $this->paginationService->calculatePages($total, $perPage);
    }
    public function getNewestPhotos(int $limit = 20): array
    {
        return $this->photoRepository->findNewestPhotos($limit);
    }
    public function getPhotoWithAlbum(int $photoId): ?array
    {
        return $this->photoRepository->findPhotoWithAlbum($photoId);
    }

    public function addRating(int $photoId, int $userId, int $rating): bool
    {
        // Możemy np. sprawdzić, czy user już oceniał 
        // (w oryginalnym kodzie nie ma takiego sprawdzenia, ale można dodać)
        return $this->photoRepository->addRating($photoId, $userId, $rating);
    }

    public function getAverageRatingAndCount(int $photoId): array
    {
        $ratings = $this->photoRepository->findRatingsByPhoto($photoId);
        if (count($ratings) === 0) {
            return ['average' => 0, 'count' => 0];
        }
        $sum = array_sum($ratings);
        $count = count($ratings);
        $avg = $sum / $count;
        return ['average' => $avg, 'count' => $count];
    }

    public function getNextPhotoId(int $albumId, string $currentDate, int $currentPhotoId): ?int
    {
        $row = $this->photoRepository->findNextPhotoInAlbum($albumId, $currentDate, $currentPhotoId);
        return $row ? (int)$row['id'] : null;
    }

    public function getPrevPhotoId(int $albumId, string $currentDate, int $currentPhotoId): ?int
    {
        $row = $this->photoRepository->findPrevPhotoInAlbum($albumId, $currentDate, $currentPhotoId);
        return $row ? (int)$row['id'] : null;
    }
    public function addComment(int $photoId, int $userId, string $comment): bool
    {
        // (opcjonalnie: walidacja comment length itp.)
        return $this->photoRepository->addComment($photoId, $userId, $comment);
    }

    /**
     * Pobiera zaakceptowane komentarze do danego zdjęcia.
     */
    public function findAcceptedCommentsByPhoto(int $photoId): array
    {
        return $this->photoRepository->findAcceptedCommentsByPhoto($photoId);
    }
    /**
     * Zwraca tablicę:
     *   ['errors' => [...]] w razie błędów
     *   lub ['photo_id' => X] jeśli się uda zapisać
     */
    public function createPhoto(
        int $albumId,
        array $uploadedFile,        // np. $_FILES['photo']
        string $userPhotoDescription // opis w polu "opis"
    ): array {
        $errors = [];

        // 1. Walidacja pliku
        if ($uploadedFile['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Błąd przesyłania pliku. Kod: " . $uploadedFile['error'];
            return ['errors' => $errors];
        }

        // Sprawdź, czy to obrazek
        $imageInfo = getimagesize($uploadedFile['tmp_name']);
        if (!$imageInfo) {
            $errors[] = "Przesłany plik nie jest obrazkiem.";
            return ['errors' => $errors];
        }
        $imageType = $imageInfo[2];
        if (!in_array($imageType, [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF])) {
            $errors[] = "Tylko JPG, PNG lub GIF są dozwolone.";
            return ['errors' => $errors];
        }

        // 2. Przeniesienie pliku do photo/<albumId>/
        $albumDir = __DIR__ . '/../../photo/' . $albumId;
        if (!file_exists($albumDir)) {
            // lepiej wczesniej sprawdzić, czy album istnieje, ale tu załóżmy tak
            @mkdir($albumDir, 0777, true);
        }

        $filename = $uploadedFile['name']; // oryginalna nazwa
        $targetPath = $albumDir . '/' . $filename;
        if (!move_uploaded_file($uploadedFile['tmp_name'], $targetPath)) {
            $errors[] = "Nie udało się zapisać pliku na serwerze.";
            return ['errors' => $errors];
        }

        // 3. (opcjonalnie) generowanie miniaturki w photo/<albumId>/min/
        // ...tworzenie minatury...
        $this->generateThumbnail($albumId, $imageInfo, $targetPath);  // np. oddzielna metoda

        // 4. Zapis do bazy
        // wstawiamy rekord do zdjecia: opis = nazwa pliku, opiszdjecia = userPhotoDescription
        $photoId = $this->photoRepository->createPhoto(
            $albumId,
            $filename,
            $userPhotoDescription
        );
        if (!$photoId) {
            $errors[] = "Błąd zapisu w bazie zdjęć.";
            return ['errors' => $errors];
        }

        return ['photo_id' => $photoId];
    }

    private function generateThumbnail(int $albumId, array $imageInfo, string $sourcePath): void
    {
        // Przykład uproszczony
        $albumDir = __DIR__ . '/../../photo/' . $albumId;
        $thumbDir = $albumDir . '/min';
        if (!file_exists($thumbDir)) {
            @mkdir($thumbDir, 0777, true);
        }

        list($width, $height) = $imageInfo; // getimagesize
        $imageType = $imageInfo[2];

        // Tworzymy np. 180 px height miniaturkę
        $newHeight = 180;
        $newWidth  = intval($width * (180 / $height));
        $thumb = imagecreatetruecolor($newWidth, $newHeight);

        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $src = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $src = imagecreatefrompng($sourcePath);
                break;
            case IMAGETYPE_GIF:
                $src = imagecreatefromgif($sourcePath);
                break;
        }

        imagecopyresampled($thumb, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        // Zapis do pliku: np. "photo/<albumId>/min/<photoId>-min.jpg"
        // Ale my nie znamy $photoId jeszcze. Możemy po prostu nazwać "<filename>-min.jpg"
        $thumbPath = $thumbDir . '/' . basename($sourcePath) . '-min.jpg';
        imagejpeg($thumb, $thumbPath);

        imagedestroy($thumb);
        imagedestroy($src);
    }
    public function getPhotosByAlbum(int $albumId): array
    {
        return $this->photoRepository->getPhotosByAlbum($albumId);
    }
    public function changePhotoDescription(int $photoId, string $newDescription): array
    {
        $errors = [];

        // Walidacja opisu
        if (strlen($newDescription) > 255) {
            $errors[] = "opis zdjęcia nie może przekraczać 255 znaków";
            return ['errors' => $errors];
        }

        // Aktualizacja opisu w bazie danych
        $success = $this->photoRepository->updatePhotoDescription($newDescription, $photoId);
        if (!$success) {
            $errors[] = "Nie udało się zaktualizować opisu zdjęcia.";
            return ['errors' => $errors];
        }

        return ['success' => true];
    }
    public function deletePhoto(int $photoId): array
    {
        $errors = [];

        // Usunięcie zdjęcia
        $success = $this->photoRepository->deletePhoto($photoId);
        if (!$success) {
            $errors[] = "Nie udało się usunąć zdjęcia.";
            return ['errors' => $errors];
        }

        return ['success' => true];
    }
    public function getAlbumIdByPhotoId(int $photoId): ?int
    {
        $photo = $this->photoRepository->getPhotoById($photoId);
        return $photo['id_albumu'] ?? null;
    }
    public function getPhotoFilename(int $photoId): ?string
    {
        $photo = $this->photoRepository->getPhotoById($photoId);
        return $photo['opis'] ?? null;
    }
    
}
