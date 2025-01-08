<?php

namespace App\Controllers;

use App\Services\AlbumService;
use App\Services\PhotoService;

class AddPhotoController
{
    private AlbumService $albumService;
    private PhotoService $photoService;

    public function __construct()
    {
        $this->albumService = new AlbumService();
        $this->photoService = new PhotoService();
    }

    public function showForm(): void
    {
        session_start();
        if (!isset($_SESSION['zalogowany']) || $_SESSION['zalogowany'] !== true) {
            header('Location: logrej.php');
            exit;
        }

        $userId = $_SESSION['tablica'][7]; 

        
        $albums = $this->albumService->getAlbumsByUser($userId);
        $count = count($albums);

        if ($count === 0) {
            
            header('Location: dodaj-album.php?albumy=0');
            exit;
        }

        
        $errors = $_SESSION['addPhotoErrors'] ?? [];
        unset($_SESSION['addPhotoErrors']);

        
        if ($count === 1) {
            $albumId = $albums[0]['id'];
        } else {
            
            $albumId = isset($_GET['albumid']) ? (int)$_GET['albumid'] : 0;
            
        }

        
        $photos = [];
        if ($albumId > 0) {
            $photos = $this->photoService->getPhotosByAlbum($albumId);
        }

        
        require __DIR__ . '/../views/dodajFotoView.php';
    }

    
    public function store(): void
    {
        session_start();
        if (empty($_SESSION['zalogowany']) || !$_SESSION['zalogowany']) {
            header('Location: logrej.php');
            exit;
        }

        $albumId = isset($_POST['ida']) ? (int)$_POST['ida'] : 0;
        $userPhotoDescription = $_POST['opis'] ?? '';

       
        $result = $this->photoService->createPhoto(
            $albumId,
            $_FILES['photo'],
            $userPhotoDescription
        );

        if (isset($result['errors'])) {
            $_SESSION['addPhotoErrors'] = $result['errors'];
            
            header("Location: dodaj-foto.php?albumid=$albumId");
            exit;
        }

        
        $_SESSION['warning4'] = 'Zdjęcie zostało pomyślnie dodane do albumu.';
        header("Location: dodaj-foto.php?albumid=$albumId");
        exit;
    }
}
