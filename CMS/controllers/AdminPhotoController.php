<?php
namespace App\Controllers;


use App\Repositories\PhotoRepository;
use App\Services\PhotoService;

session_start();

class AdminPhotoController
{
    private PhotoService $photoService;
    

    public function __construct()
    {
        

        $photoRepo = new PhotoRepository($this->conn);
        $this->photoService = new PhotoService($photoRepo);
    }

    
    public function showZdjecia()
    {
        $co = $_GET['co'] ?? null;
        $id = $_GET['id'] ?? null;

        
        if (!$co) {
            $unacceptedCount = $this->photoService->getUnacceptedCount();
            if ($unacceptedCount == 0) {
                
                header("Location: index.php?type=zdjecia&co=wszystko");
                exit;
            } else {
                
                $photos = [];
                $albums = [];
                require __DIR__ . '/../views/adminZdjeciaView.php';
                return;
            }
        }

        
        if ($co === 'wszystko') {
            if ($id) {
                
                $photos = $this->photoService->getPhotosByAlbumId((int)$id);
                $albums= []; 
                require __DIR__ . '/../views/adminZdjeciaView.php';
                return;
            } else {
                
                $albums = $this->photoService->getAlbumsHavingPhotos();
                $photos = []; 
                require __DIR__ . '/../views/adminZdjeciaView.php';
                return;
            }
        }

        
        if ($co === 'tylko') {
            $photos = $this->photoService->getUnacceptedPhotos();
            $albums= [];
            require __DIR__ . '/../views/adminZdjeciaView.php';
            return;
        }

        
        $photos = [];
        $albums = [];
        require __DIR__ . '/../views/adminZdjeciaView.php';
    }

   
    public function acceptPhoto()
    {
        if (!isset($_POST['id'])) {
            header('Location: index.php?type=zdjecia');
            exit;
        }
        $photoId = (int)$_POST['id'];

        
        $this->photoService->acceptPhoto($photoId);

        $_SESSION['warning3'] = 'Zdjęcie zostało zaakceptowane 🙂';
        header('Location: index.php?type=zdjecia');
        exit;
    }

    
    public function deletePhoto()
    {
        if (!isset($_POST['id']) || !isset($_POST['idalbumu']) || !isset($_POST['opis'])) {
            header('Location: index.php?type=zdjecia');
            exit;
        }
        $photoId  = (int)$_POST['id'];
        $albumId  = (int)$_POST['idalbumu'];
        $filename = $_POST['opis'];

       
        $this->photoService->deletePhotoCompletely($photoId, $albumId, $filename);

        $_SESSION['warning3'] = 'Zdjęcie zostało usunięte';
        header('Location: index.php?type=zdjecia');
        exit;
    }
}
