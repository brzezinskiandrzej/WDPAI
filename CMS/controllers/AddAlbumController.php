<?php

namespace App\Controllers;

use App\Services\AlbumService;

class AddAlbumController
{
    private AlbumService $albumService;

    public function __construct()
    {
        $this->albumService = new AlbumService();
    }

    
    public function showForm(): void
    {
        session_start();

        
        $errors = $_SESSION['addAlbumErrors'] ?? [];
        unset($_SESSION['addAlbumErrors']);

        require __DIR__ . '/../views/dodajAlbumView.php';
    }

    
    public function store(): void
    {
        session_start();

        if (!isset($_SESSION['zalogowany']) || $_SESSION['zalogowany'] !== true) {
            header('Location: logrej.php');
            exit;
        }

        $userId = $_SESSION['tablica'][7]; 
        $title  = $_POST['albumname'] ?? '';

        $result = $this->albumService->createAlbum($userId, $title);
        if (isset($result['errors'])) {
            $_SESSION['addAlbumErrors'] = $result['errors'];
            header('Location: dodaj-album.php');
            exit;
        }

       
        $albumId = $result['album_id'];
        header("Location: dodaj-foto.php?albumid=$albumId");
        exit;
    }
}
