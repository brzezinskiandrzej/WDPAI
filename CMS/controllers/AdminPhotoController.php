<?php
namespace App\Controllers;


use App\Repositories\PhotoRepository;
use App\Services\PhotoService;

session_start();

/**
 * Kontroler sekcji "Zdjęcia" w panelu admina.
 * Zastępuje dawny kod z adminscript.php => if(type=='zdjecia') ...
 */
class AdminPhotoController
{
    private PhotoService $photoService;
    

    public function __construct()
    {
        

        $photoRepo = new PhotoRepository($this->conn);
        $this->photoService = new PhotoService($photoRepo);
    }

    /**
     * Główna metoda do wyświetlania zdjęć,
     * bazuje na parametrach: ?type=zdjecia&co=...&id=...
     */
    public function showZdjecia()
    {
        $co = $_GET['co'] ?? null;
        $id = $_GET['id'] ?? null;

        // 1) jeśli brak co => sprawdzamy liczbę niezaakceptowanych
        if (!$co) {
            $unacceptedCount = $this->photoService->getUnacceptedCount();
            if ($unacceptedCount == 0) {
                // przeniesienie starej logiki: header("Location: index.php?type=zdjecia&co=wszystko");
                header("Location: index.php?type=zdjecia&co=wszystko");
                exit;
            } else {
                // wyświetlamy "lubdiv" z guzikami (tylko/ wszystko)
                // W widoku wystarczy info, że $co = null i $photos / $albums puste
                $photos = [];
                $albums = [];
                require __DIR__ . '/../views/adminZdjeciaView.php';
                return;
            }
        }

        // 2) co=wszystko
        if ($co === 'wszystko') {
            if ($id) {
                // Wyświetlamy zdjęcia danego albumu
                $photos = $this->photoService->getPhotosByAlbumId((int)$id);
                $albums= []; // Niepotrzebne
                require __DIR__ . '/../views/adminZdjeciaView.php';
                return;
            } else {
                // Wyświetlamy listę albumów, które mają zdjęcia
                $albums = $this->photoService->getAlbumsHavingPhotos();
                $photos = []; // Niepotrzebne
                require __DIR__ . '/../views/adminZdjeciaView.php';
                return;
            }
        }

        // 3) co=tylko => tylko niezaakceptowane
        if ($co === 'tylko') {
            $photos = $this->photoService->getUnacceptedPhotos();
            $albums= [];
            require __DIR__ . '/../views/adminZdjeciaView.php';
            return;
        }

        // Fallback -> nic
        $photos = [];
        $albums = [];
        require __DIR__ . '/../views/adminZdjeciaView.php';
    }

    /**
     * Akceptacja zdjęcia (action=accept)
     */
    public function acceptPhoto()
    {
        if (!isset($_POST['id'])) {
            header('Location: index.php?type=zdjecia');
            exit;
        }
        $photoId = (int)$_POST['id'];

        // W starym kodzie: "UPDATE zdjecia SET zaakceptowane=1 WHERE id=..."
        // Teraz lepiej w PhotoService -> $this->photoService->acceptPhoto($photoId)
        $this->photoService->acceptPhoto($photoId);

        $_SESSION['warning3'] = 'Zdjęcie zostało zaakceptowane 🙂';
        header('Location: index.php?type=zdjecia');
        exit;
    }

    /**
     * Usuwanie zdjęcia (action=delete)
     */
    public function deletePhoto()
    {
        if (!isset($_POST['id']) || !isset($_POST['idalbumu']) || !isset($_POST['opis'])) {
            header('Location: index.php?type=zdjecia');
            exit;
        }
        $photoId  = (int)$_POST['id'];
        $albumId  = (int)$_POST['idalbumu'];
        $filename = $_POST['opis'];

        // W starym kodzie: usunięcie z bazy (komentarze, oceny, zdjecie),
        // plus usunięcie pliku:
        $this->photoService->deletePhotoCompletely($photoId, $albumId, $filename);

        $_SESSION['warning3'] = 'Zdjęcie zostało usunięte';
        header('Location: index.php?type=zdjecia');
        exit;
    }
}
