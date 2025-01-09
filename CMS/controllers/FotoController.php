<?php

namespace App\Controllers;

use App\Services\PhotoService;
use App\Services\CommentService; 


class FotoController
{
    private PhotoService $photoService;
    

    public function __construct()
    {
        $this->photoService = new PhotoService();
        
    }

    
    public function showPhotoPage(): void
    {
        session_start();
        
        
        $photoId = (int)($_GET['id'] ?? 0);
        $albumId = (int)($_GET['id_albumu'] ?? 0);
        if ($photoId <= 0 || $albumId <= 0) {
            header('Location: index.php');
            exit;
        }

        
        $photo = $this->photoService->getPhotoWithAlbum($photoId);
        if (!$photo) {
            header('Location: index.php');
            exit;
        }

        
        $ratingData = $this->photoService->getAverageRatingAndCount($photoId);
        
        
        $nextPhotoId = $this->photoService->getNextPhotoId(
            $albumId,
            $photo['data'], 
            $photoId
        );
        $prevPhotoId = $this->photoService->getPrevPhotoId(
            $albumId,
            $photo['data'], 
            $photoId
        );

        
        $comments = $this->photoService->findAcceptedCommentsByPhoto($photoId);
        $userRating = null;
    if (!empty($_SESSION['zalogowany']) && $_SESSION['zalogowany'] === true) {
        $userId = $_SESSION['tablica'][7];
        $userRating = $this->photoService->getUserRating($photoId, $userId); 
        
    }
        
        $title = "Foto #$photoId";
        require __DIR__ . '/../views/fotoView.php';
    }

    
    public function ratePhotoAction(): void
    {
        session_start();
        if (!$_SESSION['zalogowany']) {
            header('Location: logrej.php');
            exit;
        }

        $photoId = (int)($_POST['id'] ?? 0);
        $albumId = (int)($_POST['idalbm'] ?? 0);
        $rating  = (int)($_POST['star'] ?? 0);
        $userId  = $_SESSION['tablica'][7]; 

        if ($photoId <= 0 || $rating < 1 || $rating > 10) {
            
            header("Location: foto.php?id=$photoId&id_albumu=$albumId");
            exit;
        }
        try{
            $this->photoService->addRating($photoId, $userId, $rating);
            $_SESSION['warning3'] = 'Twoja ocena została zapisana.';
            header("Location: foto.php?id=$photoId&id_albumu=$albumId");
        }catch (\Exception $e) {
            $_SESSION['warning3'] = "Wystąpił błąd: " . htmlspecialchars($e->getMessage());
            header("Location: foto.php?id=$photoId&id_albumu=$albumId");
        }
        
    }

    
    public function addCommentAction(): void
{
    session_start();
    
    
    if (empty($_SESSION['zalogowany']) || !$_SESSION['zalogowany']) {
        header('Location: logrej.php');
        exit;
    }

    
    $photoId  = (int)($_POST['id_zdjecia'] ?? 0);
    $albumId  = (int)($_POST['idalbm'] ?? 0);
    $comment  = trim($_POST['kom'] ?? '');
    $userId   = $_SESSION['tablica'][7]; 

    
    if ($photoId > 0 && !empty($comment)) {
        $ok = $this->photoService->addComment($photoId, $userId, $comment);
        if ($ok) {
            $_SESSION['warning3'] = 'Twój komentarz czeka na akceptację administratora.';
        } else {
            $_SESSION['warning3'] = 'Wystąpił błąd podczas dodawania komentarza.';
        }
    }

    
    header("Location: foto.php?id=$photoId&id_albumu=$albumId");
    exit;
}
}
