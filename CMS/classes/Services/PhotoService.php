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
       
        $this->photoRepository = new PhotoRepository();
        $this->paginationService = new PaginationService();
    }

    
    public function getTopRatedPhotos(int $limit = 20): array
    {
        return $this->photoRepository->findTopRatedPhotos($limit);
    }
    public function getAcceptedPhotosCountForAlbum(int $albumId): int
    {
        return $this->photoRepository->countAcceptedPhotosByAlbum($albumId);
    }

    
    public function getAcceptedPhotosForAlbum(int $albumId, int $page, int $perPage): array
    {
        $offset = $this->paginationService->calculateOffset($page, $perPage);
        return $this->photoRepository->findAcceptedPhotosByAlbum($albumId, $perPage, $offset);
    }

    
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
        
        return $this->photoRepository->addRating($photoId, $userId, $rating);
    }
    public function getUserRating(int $photoId, int $userId): ?int
    {
        return $this->photoRepository->getUserRating($photoId, $userId);
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
       
        return $this->photoRepository->addComment($photoId, $userId, $comment);
    }

    
    public function findAcceptedCommentsByPhoto(int $photoId): array
    {
        return $this->photoRepository->findAcceptedCommentsByPhoto($photoId);
    }
    public function getUnacceptedCountComments(): int
    {
        return $this->photoRepository->countUnacceptedComments();
    }
    public function getAllCommentsOrdered(): array
    {
        return $this->photoRepository->findAllCommentsOrdered();
    }
    public function getUnacceptedComments(): array
    {
        return $this->photoRepository->findUnacceptedComments();
    }
    public function acceptComment(int $commentId): void
    {
        $this->photoRepository->acceptCommentById($commentId);
    }
    public function editComment(int $commentId, string $newText): void
    {
        $this->photoRepository->updateCommentText($commentId, $newText);
    }
    public function deleteComment(int $commentId): void
    {
        $this->photoRepository->deleteCommentById($commentId);
    }
    
    public function createPhoto(
        int $albumId,
        array $uploadedFile,  
        string $userPhotoDescription 
    ): array {
        $errors = [];

        
        if ($uploadedFile['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Błąd przesyłania pliku. Kod: " . $uploadedFile['error'];
            return ['errors' => $errors];
        }

        
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

        
        $albumDir = __DIR__ . '/../../photo/' . $albumId;
        if (!file_exists($albumDir)) {
            
            @mkdir($albumDir, 0777, true);
        }

        $filename = $uploadedFile['name']; 
        $targetPath = $albumDir . '/' . $filename;
        if (!move_uploaded_file($uploadedFile['tmp_name'], $targetPath)) {
            $errors[] = "Nie udało się zapisać pliku na serwerze.";
            return ['errors' => $errors];
        }

       
        
        $this->generateThumbnail($albumId, $imageInfo, $targetPath);

        
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
        
        $albumDir = __DIR__ . '/../../photo/' . $albumId;
        $thumbDir = $albumDir . '/min';
        if (!file_exists($thumbDir)) {
            @mkdir($thumbDir, 0777, true);
        }

        list($width, $height) = $imageInfo;
        $imageType = $imageInfo[2];

        
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


        if (strlen($newDescription) > 255) {
            $errors[] = "opis zdjęcia nie może przekraczać 255 znaków";
            return ['errors' => $errors];
        }


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
    public function getUnacceptedCount(): int
    {
        return $this->photoRepository->countUnacceptedPhotos();
    }

    public function getPhotosByAlbumId(int $albumId): array
    {
        return $this->photoRepository->findPhotosByAlbumAdmin($albumId);
    }

    public function getAlbumsHavingPhotos(): array
    {
        return $this->photoRepository->findAllAlbumsWithPhotos();
    }

    public function getUnacceptedPhotos(): array
    {
        return $this->photoRepository->findAllUnacceptedPhotos();
    }

    public function acceptPhoto(int $photoId): void
    {
        $this->photoRepository->acceptPhotoById($photoId);
    }

    public function deletePhotoCompletely(int $photoId, int $albumId, string $filename): void
    {
   
        $this->photoRepository->deletePhotoWithRelations($photoId, $albumId, $filename);
    }
    
}
