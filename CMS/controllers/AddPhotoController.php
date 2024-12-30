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

    /**
     * Wyświetla formularz dodawania zdjęcia.
     */
    public function showForm(): void
    {
        session_start();
        if (!isset($_SESSION['zalogowany']) || $_SESSION['zalogowany'] !== true) {
            header('Location: logrej.php');
            exit;
        }

        $userId = $_SESSION['tablica'][7]; // ID użytkownika

        // 1. Sprawdź, ile albumów user ma
        $albums = $this->albumService->getAlbumsByUser($userId);
        $count = count($albums);

        if ($count === 0) {
            // Brak albumów → redirect do dodaj-album.php
            header('Location: dodaj-album.php?albumy=0');
            exit;
        }

        // Błędy
        $errors = $_SESSION['addPhotoErrors'] ?? [];
        unset($_SESSION['addPhotoErrors']);

        // 2. Jeśli jest 1 album → automatycznie wybierz ten album
        if ($count === 1) {
            $albumId = $albums[0]['id'];
        } else {
            // 3. Jeśli jest ich wiele, sprawdzamy, czy user w GET przekazał `albumid`
            $albumId = isset($_GET['albumid']) ? (int)$_GET['albumid'] : 0;
            // Jeśli albumId = 0, to user musi dopiero wybrać z listy – w widoku to obsłużymy.
        }

        // 4. Pobieramy zdjęcia z wybranego albumu, jeśli albumId > 0
        $photos = [];
        if ($albumId > 0) {
            $photos = $this->photoService->getPhotosByAlbum($albumId);
        }

        // 5. Wczytanie widoku
        require __DIR__ . '/../views/dodajFotoView.php';
    }

    /**
     * Obsługa przesłanego formularza zdjęcia.
     */
    public function store(): void
    {
        session_start();
        if (empty($_SESSION['zalogowany']) || !$_SESSION['zalogowany']) {
            header('Location: logrej.php');
            exit;
        }

        $albumId = isset($_POST['ida']) ? (int)$_POST['ida'] : 0;
        $userPhotoDescription = $_POST['opis'] ?? '';

        // Przetwarzanie pliku
        $result = $this->photoService->createPhoto(
            $albumId,
            $_FILES['photo'],
            $userPhotoDescription
        );

        if (isset($result['errors'])) {
            $_SESSION['addPhotoErrors'] = $result['errors'];
            // wracamy do formularza
            header("Location: dodaj-foto.php?albumid=$albumId");
            exit;
        }

        // Sukces – redirect z komunikatem
        $_SESSION['warning4'] = 'Zdjęcie zostało pomyślnie dodane do albumu.';
        header("Location: dodaj-foto.php?albumid=$albumId");
        exit;
    }
}
