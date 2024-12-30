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

    /**
     * Obsługuje żądania na podstawie typu i akcji.
     *
     * @param string $type
     * @param string|null $action
     */
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
        // Ekstrakcja danych do zmiennych
        extract($data);
        require __DIR__ . '/../views/' . $view;
    }

    /**
     * Wyświetla sekcję "Moje Dane".
     */
    private function showDane()
    {
        if (!$this->isLoggedIn()) {
            $this->redirectToLogin();
        }

        // Dane użytkownika z sesji
        $user = [
            'login' => $_SESSION['tablica'][1],
            'email' => $_SESSION['tablica'][3],
            'created_at' => $_SESSION['tablica'][4],
        ];

        // Komunikaty
        $warning = $_SESSION['warning'] ?? '';
        $warning2 = $_SESSION['warning2'] ?? '';
        // Czyszczenie komunikatów
        unset($_SESSION['warning'], $_SESSION['warning2']);

        // Renderowanie widoku
        $this->render('kontoView.php', [
            'type' => 'dane',
            'user' => $user,
            'warning' => $warning,
            'warning2' => $warning2,
        ]);
    }

    /**
     * Aktualizuje adres email użytkownika.
     */
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

    /**
     * Aktualizuje hasło użytkownika.
     */
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

    /**
     * Wyświetla sekcję "Moje Albumy".
     */
    private function showAlbumy()
    {
        if (!$this->isLoggedIn()) {
            $this->redirectToLogin();
        }

        $userId = $_SESSION['tablica'][7];
        $albums = $this->albumService->getAlbumsByUser($userId);
        
        // Komunikat
        $warning3 = $_SESSION['warning3'] ?? '';
        
        // Czyszczenie komunikatu
        unset($_SESSION['warning3']);

        // Renderowanie widoku
        $this->render('kontoView.php', [
            'type' => 'albumy',
            'albums' => $albums,
            'warning3' => $warning3,
        ]);
    }

    /**
     * Aktualizuje tytuł albumu.
     */
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

    /**
     * Usuwa album użytkownika.
     */
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

        // Usunięcie plików albumu z systemu plików
        $this->deleteAlbumFiles((int)$albumId);

        $result = $this->albumService->deleteAlbum((int)$albumId);

        if (isset($result['errors'])) {
            $_SESSION['warning3'] = implode('<br>', $result['errors']);
        } else {
            $_SESSION['warning3'] = "Album został usunięty.";
        }

        $this->redirectToAlbumy();
    }

    /**
     * Wyświetla sekcję "Moje Zdjęcia".
     */
    private function showZdjecia()
    {
        if (!$this->isLoggedIn()) {
            $this->redirectToLogin();
        }

        $userId = $_SESSION['tablica'][7];
        $albums = $this->albumService->getAlbumsByUser($userId);
        $photos = []; // Będzie wypełnione w zależności od wyboru albumu

        // Komunikat
        $warning3 = $_SESSION['warning3'] ?? '';

        // Czyszczenie komunikatu
        unset($_SESSION['warning3']);

        // Renderowanie widoku
        $this->render('kontoView.php', [
            'type' => 'zdjecia',
            'albums' => $albums,
            'photos' => $photos,
            'warning3' => $warning3,
        ]);
    }

    /**
     * Wyświetla zdjęcia wybranego albumu.
     *
     * @param string $albumId
     */
    private function showPhotos(string $albumId)
    {
        if (!$this->isLoggedIn()) {
            $this->redirectToLogin();
        }

        $albumId = (int)$albumId;
        $photos = $this->photoService->getPhotosByAlbum($albumId);
        error_log('Zdjęcia w albumie: ' . count($photos));
        // Komunikat
        $warning3 = $_SESSION['warning3'] ?? '';

        // Czyszczenie komunikatu
        unset($_SESSION['warning3']);

        // Renderowanie widoku
        $this->render('kontoView.php', [
            'type' => 'zdjecia',
            'selectedAlbumId' => $albumId,
            'albums' => $albums,
            'photos' => $photos,
            'warning3' => $warning3,
        ]);
    }

    /**
     * Zmienia opis zdjęcia.
     */
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

    /**
     * Usuwa zdjęcie użytkownika.
     */
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

        // Usunięcie plików zdjęcia z systemu plików
        $this->deletePhotoFiles((int)$photoId);

        $result = $this->photoService->deletePhoto((int)$photoId);

        if (isset($result['errors'])) {
            $_SESSION['warning3'] = implode('<br>', $result['errors']);
        } else {
            $_SESSION['warning3'] = "Zdjęcie zostało usunięte.";
        }

        $this->redirectToZdjecia();
    }

    /**
     * Usuwa konto użytkownika.
     */
    private function deleteAccount()
    {
        if (!$this->isLoggedIn()) {
            $this->redirectToLogin();
        }

        $userId = $_SESSION['tablica'][7];

        // Usunięcie wszystkich plików związanych z użytkownikiem
        $this->deleteAllUserFiles($userId);

        $result = $this->userService->deleteAccount($userId);

        if (isset($result['errors'])) {
            $_SESSION['warning3'] = implode('<br>', $result['errors']);
            $this->redirectToDane();
        } else {
            $this->logoutAndRedirect();
        }
    }

    /**
     * Sprawdza, czy użytkownik jest zalogowany.
     *
     * @return bool
     */
    private function isLoggedIn(): bool
    {
        return isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] === true;
    }

    /**
     * Przekierowuje użytkownika do strony logowania.
     */
    private function redirectToLogin(): void
    {
        header('Location: logrej.php');
        exit;
    }

    /**
     * Przekierowuje użytkownika do sekcji "Moje Dane".
     */
    private function redirectToDane(): void
    {
        header('Location: konto.php?type=dane');
        exit;
    }

    /**
     * Przekierowuje użytkownika do sekcji "Moje Albumy".
     */
    private function redirectToAlbumy(): void
    {
        header('Location: konto.php?type=albumy');
        exit;
    }

    /**
     * Przekierowuje użytkownika do sekcji "Moje Zdjęcia".
     */
    private function redirectToZdjecia(): void
    {
        // Sprawdzenie, czy istnieje ID albumu
        if (isset($_POST['idalbumu'])) {
            header('Location: konto.php?type=zdjecia&id=' . intval($_POST['idalbumu']));
        } else {
            header('Location: konto.php?type=zdjecia');
        }
        exit;
    }

    /**
     * Usuwa pliki albumu z systemu plików.
     *
     * @param int $albumId
     */
    private function deleteAlbumFiles(int $albumId): void
    {
        $directory = "photo/" . $albumId;
        $this->deleteAll($directory);
    }

    /**
     * Usuwa pliki zdjęcia z systemu plików.
     *
     * @param int $photoId
     */
    private function deletePhotoFiles(int $photoId): void
    {
        // Pobranie ID albumu i nazwy pliku zdjęcia
        $albumId = $this->photoService->getAlbumIdByPhotoId($photoId);
        $filename = $this->photoService->getPhotoFilename($photoId);

        if ($albumId && $filename) {
            $filePath = "photo/" . $albumId . "/" . $filename;
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Zakładam, że istnieje folder "min" z miniaturami
            $minFilePath = "photo/" . $albumId . "/min/" . $photoId . "-min.jpg";
            if (file_exists($minFilePath)) {
                unlink($minFilePath);
            }
        }
    }

    /**
     * Usuwa wszystkie pliki użytkownika z systemu plików.
     *
     * @param int $userId
     */
    private function deleteAllUserFiles(int $userId): void
    {
        // Pobranie wszystkich albumów użytkownika
        $albums = $this->albumService->getAlbumsByUser($userId);
        foreach ($albums as $album) {
            $albumId = $album['id'];
            $directory = "photo/" . $albumId;
            $this->deleteAll($directory);
        }
    }

    /**
     * Usuwa wszystkie pliki w danym katalogu.
     *
     * @param string $dir
     */
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

    /**
     * Wylogowuje użytkownika i przekierowuje do strony logowania.
     */
    private function logoutAndRedirect(): void
    {
        session_destroy();
        header('Location: logrej.php');
        exit;
    }
}
