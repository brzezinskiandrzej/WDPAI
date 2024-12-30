<?php

namespace App\Controllers;

use App\Services\PhotoService;
use App\Services\CommentService; // Jeśli masz
// use App\Services\... // ewentualnie inne

class FotoController
{
    private PhotoService $photoService;
    // private CommentService $commentService; // jeśli masz osobny serwis

    public function __construct()
    {
        $this->photoService = new PhotoService();
        // $this->commentService = new CommentService();
    }

    /**
     * Wyświetla stronę z pojedynczym zdjęciem i jego danymi.
     */
    public function showPhotoPage(): void
    {
        session_start();
        
        // Odczyt id zdjęcia i albumu z GET:
        $photoId = (int)($_GET['id'] ?? 0);
        $albumId = (int)($_GET['id_albumu'] ?? 0);
        if ($photoId <= 0 || $albumId <= 0) {
            header('Location: index.php');
            exit;
        }

        // Pobieramy dane zdjęcia:
        $photo = $this->photoService->getPhotoWithAlbum($photoId);
        if (!$photo) {
            // Nie ma takiego zdjęcia w bazie
            header('Location: index.php');
            exit;
        }

        // Wyliczamy średnią ocen
        $ratingData = $this->photoService->getAverageRatingAndCount($photoId);
        
        // Next/Prev
        // W oryginalnym kodzie brałeś 'data' zdjęcia i porównywałeś.
        // Tutaj `$photo['data']` jest np. '2023-10-01 14:23:59'.
        // Mamy getNextPhotoId() i getPrevPhotoId() w serwisie:
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

        // Komentarze (jeśli w oryginale wyświetlasz):
        $comments = $this->photoService->findAcceptedCommentsByPhoto($photoId);

        // Załaduj widok
        // przekazując powyższe dane
        $title = "Foto #$photoId";
        require __DIR__ . '/../views/fotoView.php';
    }

    /**
     * Obsługa akcji oceniania (jeśli chcesz to w osobnej metodzie).
     */
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
        $userId  = $_SESSION['tablica'][7]; // w Twoim kodzie to ID usera

        if ($photoId <= 0 || $rating < 1 || $rating > 10) {
            // Nieprawidłowe dane – redirect np. do foto
            header("Location: foto.php?id=$photoId&id_albumu=$albumId");
            exit;
        }

        $this->photoService->addRating($photoId, $userId, $rating);
        header("Location: foto.php?id=$photoId&id_albumu=$albumId");
    }

    /**
     * Dodawanie komentarza (jeśli masz wydzielone).
     */
    public function addCommentAction(): void
{
    session_start();
    
    // Sprawdź czy user zalogowany
    if (empty($_SESSION['zalogowany']) || !$_SESSION['zalogowany']) {
        header('Location: logrej.php');
        exit;
    }

    // Pobierz parametry z $_POST
    $photoId  = (int)($_POST['id_zdjecia'] ?? 0);
    $albumId  = (int)($_POST['idalbm'] ?? 0);
    $comment  = trim($_POST['kom'] ?? '');
    $userId   = $_SESSION['tablica'][7]; // twoje ID użytkownika w sesji

    // (opcjonalnie walidacja $comment)
    if ($photoId > 0 && !empty($comment)) {
        $ok = $this->photoService->addComment($photoId, $userId, $comment);
        if ($ok) {
            $_SESSION['warning3'] = 'Twój komentarz czeka na akceptację administratora.';
        } else {
            $_SESSION['warning3'] = 'Wystąpił błąd podczas dodawania komentarza.';
        }
    }

    // Przekierowanie powrotne
    header("Location: foto.php?id=$photoId&id_albumu=$albumId");
    exit;
}
}
