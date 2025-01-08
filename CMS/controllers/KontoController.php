<?php

namespace App\Controllers;

use App\Services\UserService;
use App\Services\AlbumService;
use App\Services\PhotoService;

class KontoController
{
    private UserService $userService;
    private AlbumService $albumService;
    private PhotoService $photoService;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->albumService = new AlbumService();
        $this->photoService = new PhotoService();
    }

    
    public function handleRequest(string $type, ?string $action)
    {
        switch ($type) {
            case 'dane':
                if ($action === 'updateEmail') {
                    $this->updateEmail();
                } elseif ($action === 'updatePassword') {
                    $this->updatePassword();
                } else {
                    $this->showDane();
                }
                break;

            case 'albumy':
                if ($action === 'updateTitle') {
                    $this->updateAlbumTitle();
                } elseif ($action === 'delete') {
                    $this->deleteAlbum();
                } else {
                    $this->showAlbumy();
                }
                break;

            case 'zdjecia':
                if ($action === 'updateDescription') {
                    $this->updatePhotoDescription();
                } elseif ($action === 'delete') {
                    $this->deletePhoto();
                } else {
                    if (isset($_GET['id'])) {
                        error_log('ID albumu: ' . $_GET['id']);
                        $this->showPhotos($_GET['id']);
                    } else {
                        $this->showZdjecia();
                    }
                    
                }
                break;

            case 'usun':
                $this->deleteAccount();
                break;

            default:
                $this->showDane();
                break;
        }
    }
    private function render(string $view, array $data = [])
    {
        
        extract($data);
        require __DIR__ . '/../views/' . $view;
    }

    
    private function showDane()
    {
        if (!$this->isLoggedIn()) {
            $this->redirectToLogin();
        }

        
        $user = [
            'login' => $_SESSION['tablica'][1],
            'email' => $_SESSION['tablica'][3],
            'created_at' => $_SESSION['tablica'][4],
        ];

       
        $warning = $_SESSION['warning'] ?? '';
        $warning2 = $_SESSION['warning2'] ?? '';
        
        unset($_SESSION['warning'], $_SESSION['warning2']);

        
        $this->render('kontoView.php', [
            'type' => 'dane',
            'user' => $user,
            'warning' => $warning,
            'warning2' => $warning2,
        ]);
    }

    
    private function updateEmail()
    {
        if (!$this->isLoggedIn()) {
            $this->redirectToLogin();
        }

        $userId = $_SESSION['tablica'][7];
        $newEmail = $_POST['cemail'] ?? '';

        $result = $this->userService->updateEmail($userId, $newEmail);

        if (isset($result['errors'])) {
            $_SESSION['warning'] = implode('<br>', $result['errors']);
        } else {
            $_SESSION['warning'] = 'e-mail został poprawnie zmieniony 🙂';
        }

        $this->redirectToDane();
    }

    
    private function updatePassword()
    {
        if (!$this->isLoggedIn()) {
            $this->redirectToLogin();
        }

        $userId = $_SESSION['tablica'][7];
        $currentPassword = $_POST['chaslo'] ?? '';
        $newPassword = $_POST['checkpasswd'] ?? '';

        $result = $this->userService->updatePassword($userId, $currentPassword, $newPassword);

        if (isset($result['errors'])) {
            $_SESSION['warning2'] = implode('<br>', $result['errors']);
        } else {
            $_SESSION['warning2'] = 'hasło zostało poprawnie zmienione 🙂';
        }

        $this->redirectToDane();
    }

    
    private function showAlbumy()
    {
        if (!$this->isLoggedIn()) {
            $this->redirectToLogin();
        }

        $userId = $_SESSION['tablica'][7];
        $albums = $this->albumService->getAlbumsByUser($userId);
        
        
        $warning3 = $_SESSION['warning3'] ?? '';
        
        
        unset($_SESSION['warning3']);

        
        $this->render('kontoView.php', [
            'type' => 'albumy',
            'albums' => $albums,
            'warning3' => $warning3,
        ]);
    }

    
    private function updateAlbumTitle()
    {
        if (!$this->isLoggedIn()) {
            $this->redirectToLogin();
        }

        $albumId = $_POST['id'] ?? null;
        $newTitle = $_POST['nowytytul'] ?? '';

        if (!$albumId) {
            $_SESSION['warning3'] = "Nieprawidłowy identyfikator albumu.";
            $this->redirectToAlbumy();
        }

        $result = $this->albumService->updateAlbumTitle((int)$albumId, $newTitle);

        if (isset($result['errors'])) {
            $_SESSION['warning3'] = implode('<br>', $result['errors']);
        } else {
            $_SESSION['warning3'] = "Tytuł albumu został zaktualizowany.";
        }

        $this->redirectToAlbumy();
    }

    
    private function deleteAlbum()
    {
        if (!$this->isLoggedIn()) {
            $this->redirectToLogin();
        }

        $albumId = $_POST['id'] ?? null;

        if (!$albumId) {
            $_SESSION['warning3'] = "Nieprawidłowy identyfikator albumu.";
            $this->redirectToAlbumy();
        }

        
        $this->deleteAlbumFiles((int)$albumId);

        $result = $this->albumService->deleteAlbum((int)$albumId);

        if (isset($result['errors'])) {
            $_SESSION['warning3'] = implode('<br>', $result['errors']);
        } else {
            $_SESSION['warning3'] = "Album został usunięty.";
        }

        $this->redirectToAlbumy();
    }

    
    private function showZdjecia()
    {
        if (!$this->isLoggedIn()) {
            $this->redirectToLogin();
        }

        $userId = $_SESSION['tablica'][7];
        $albums = $this->albumService->getAlbumsByUser($userId);
        $photos = []; 

       
        $warning3 = $_SESSION['warning3'] ?? '';

        
        unset($_SESSION['warning3']);

        
        $this->render('kontoView.php', [
            'type' => 'zdjecia',
            'albums' => $albums,
            'photos' => $photos,
            'warning3' => $warning3,
        ]);
    }

    
    private function showPhotos(string $albumId)
    {
        if (!$this->isLoggedIn()) {
            $this->redirectToLogin();
        }

        $albumId = (int)$albumId;
        $photos = $this->photoService->getPhotosByAlbum($albumId);
        error_log('Zdjęcia w albumie: ' . count($photos));
       
        $warning3 = $_SESSION['warning3'] ?? '';

       
        unset($_SESSION['warning3']);

       
        $this->render('kontoView.php', [
            'type' => 'zdjecia',
            'selectedAlbumId' => $albumId,
            'albums' => $albums,
            'photos' => $photos,
            'warning3' => $warning3,
        ]);
    }

    
    private function updatePhotoDescription()
    {
        if (!$this->isLoggedIn()) {
            $this->redirectToLogin();
        }

        $photoId = $_POST['id'] ?? null;
        $newDescription = $_POST['nowyopis'] ?? '';

        if (!$photoId) {
            $_SESSION['warning3'] = "Nieprawidłowy identyfikator zdjęcia.";
            $this->redirectToZdjecia();
        }

        $result = $this->photoService->changePhotoDescription((int)$photoId, $newDescription);

        if (isset($result['errors'])) {
            $_SESSION['warning3'] = implode('<br>', $result['errors']);
        } else {
            $_SESSION['warning3'] = "Opis zdjęcia został zaktualizowany.";
        }

        $this->redirectToZdjecia();
    }

    
    private function deletePhoto()
    {
        if (!$this->isLoggedIn()) {
            $this->redirectToLogin();
        }

        $photoId = $_POST['id'] ?? null;

        if (!$photoId) {
            $_SESSION['warning3'] = "Nieprawidłowy identyfikator zdjęcia.";
            $this->redirectToZdjecia();
        }

       
        $this->deletePhotoFiles((int)$photoId);

        $result = $this->photoService->deletePhoto((int)$photoId);

        if (isset($result['errors'])) {
            $_SESSION['warning3'] = implode('<br>', $result['errors']);
        } else {
            $_SESSION['warning3'] = "Zdjęcie zostało usunięte.";
        }

        $this->redirectToZdjecia();
    }

    
    private function deleteAccount()
    {
        if (!$this->isLoggedIn()) {
            $this->redirectToLogin();
        }

        $userId = $_SESSION['tablica'][7];

        
        $this->deleteAllUserFiles($userId);

        $result = $this->userService->deleteAccount($userId);

        if (isset($result['errors'])) {
            $_SESSION['warning3'] = implode('<br>', $result['errors']);
            $this->redirectToDane();
        } else {
            $this->logoutAndRedirect();
        }
    }

   
    private function isLoggedIn(): bool
    {
        return isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] === true;
    }

    private function redirectToLogin(): void
    {
        header('Location: logrej.php');
        exit;
    }

    
    private function redirectToDane(): void
    {
        header('Location: konto.php?type=dane');
        exit;
    }

    private function redirectToAlbumy(): void
    {
        header('Location: konto.php?type=albumy');
        exit;
    }

    
    private function redirectToZdjecia(): void
    {
        
        if (isset($_POST['idalbumu'])) {
            header('Location: konto.php?type=zdjecia&id=' . intval($_POST['idalbumu']));
        } else {
            header('Location: konto.php?type=zdjecia');
        }
        exit;
    }

    
    private function deleteAlbumFiles(int $albumId): void
    {
        $directory = "photo/" . $albumId;
        $this->deleteAll($directory);
    }

    
    private function deletePhotoFiles(int $photoId): void
    {
       
        $albumId = $this->photoService->getAlbumIdByPhotoId($photoId);
        $filename = $this->photoService->getPhotoFilename($photoId);

        if ($albumId && $filename) {
            $filePath = "photo/" . $albumId . "/" . $filename;
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            
            $minFilePath = "photo/" . $albumId . "/min/" . $photoId . "-min.jpg";
            if (file_exists($minFilePath)) {
                unlink($minFilePath);
            }
        }
    }

    
    private function deleteAllUserFiles(int $userId): void
    {
        
        $albums = $this->albumService->getAlbumsByUser($userId);
        foreach ($albums as $album) {
            $albumId = $album['id'];
            $directory = "photo/" . $albumId;
            $this->deleteAll($directory);
        }
    }

   
    private function deleteAll(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        foreach (glob($dir . '/*') as $file) {
            if (is_dir($file)) {
                $this->deleteAll($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dir);
    }

    
    private function logoutAndRedirect(): void
    {
        session_destroy();
        header('Location: logrej.php');
        exit;
    }
}
