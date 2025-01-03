<?php
namespace App\Controllers;

use App\Repositories\AlbumRepository;
use App\Services\AlbumService;

/**
 * Klasa kontrolera obsługująca sekcję "Albumy" w panelu admina,
 * ZAMIENIA dotychczasową logikę w adminscript.php (case 'albumy'),
 * admintytul.php (zmiana tytułu / usuwanie albumu),
 * paginalbum.php (paginacja).
 */
class AdminAlbumController
{
    private AlbumService $albumService;
    

    public function __construct()
    {

        $albumRepo = new AlbumRepository($this->conn);
        $this->albumService = new AlbumService($albumRepo);
    }

    /**
     * Metoda wyświetlająca listę albumów z paginacją, liczbą zdjęć i niezaakceptowanych.
     * ZASTĘPUJE dawną część adminscript.php -> if(isset($_GET['type']) && $_GET['type']=='albumy') { ... }.
     */
    public function showAlbumy()
    {
        // Obsługa paginacji
        if (!isset($_SESSION['strona'])) {
            $_SESSION['strona'] = 0;
        }
        if (isset($_GET['numer'])) {
            $_SESSION['strona'] = ((int)$_GET['numer'] - 1) * 30;
        }

        $offset = $_SESSION['strona'] ?? 0;
        $limit  = 30;

        // Pobieramy łączną liczbę albumów (do wyliczenia liczby stron)
        $totalCount = $this->albumService->countAllAlbumsForAdmin();
        $numerstron = ceil($totalCount / 30);

        // Pobieramy faktyczną listę albumów (z polami: id, tytul, data, ile, accept, login, niezaakceptowane)
        $albums = $this->albumService->getAlbumsForAdmin($offset, $limit);

        // Komunikat w sesji (stare adminscript.php)
        if (!isset($_SESSION['warning3'])) {
            $_SESSION['warning3'] = '';
        }

        // Wczytujemy widok (adminAlbumyView.php), zachowując oryginalne identyfikatory
        require __DIR__ . '/../views/adminAlbumyView.php';
    }

    /**
     * Edycja tytułu albumu (ZASTĘPUJE admintytul.php -> if ($_POST['zmien'])).
     */
    public function editAlbumTitle()
    {
        if (!isset($_POST['id']) || !isset($_POST['nowytytul'])) {
            header('Location: index.php?type=albumy');
            exit;
        }
        $albumId = (int)$_POST['id'];
        $newTitle= $_POST['nowytytul'];

        // Walidacja tytułu
        $error = $this->validateAlbumTitle($newTitle);
        if ($error !== null) {
            $_SESSION['warning3'] = $error;
            header('Location: index.php?type=albumy');
            exit;
        }

        // W starym kodzie: $sql = "UPDATE albumy SET tytul = $1 WHERE id = $2"
        // Teraz powinniśmy użyć AlbumService -> updateTitle($albumId,$newTitle),
        // o ile istnieje taka metoda. Jeśli nie, dopiszmy ją do AlbumService.
        $this->albumService->updateAlbumTitle($albumId, $newTitle);

        $_SESSION['warning3'] = 'Tytuł został poprawnie zmieniony 🙂';
        header('Location: index.php?type=albumy');
        exit;
    }

    /**
     * Usuwanie albumu (ZASTĘPUJE admintytul.php -> if ($_POST['usun2'])).
     */
    public function deleteAlbum()
    {
        if (!isset($_POST['id'])) {
            header('Location: index.php?type=albumy');
            exit;
        }
        $albumId = (int)$_POST['id'];

        // Zgodnie z dawnym kodem, usuwamy album + zdjęcia, komentarze, oceny, katalog, itd.
        // Lepiej, żeby istniała metoda w AlbumService, np. $this->albumService->deleteAlbumWithFiles($albumId)
        // Która wykona operacje usunięcia w Repozytorium i skasuje katalog.
        $this->albumService->deleteAlbumCompletely($albumId);

        $_SESSION['warning3'] = 'Album został usunięty';
        header('Location: index.php?type=albumy');
        exit;
    }

    /**
     * Walidacja tytułu albumu (jak w dawnym albumn(...))
     */
    private function validateAlbumTitle(string $title): ?string
    {
        $pattern = "/^.{1,100}$/";
        $pattern2 = "/[^\s]+/";
        if (preg_match($pattern, $title)) {
            if (preg_match($pattern2, $title)) {
                return null; // brak błędu
            } else {
                return "nazwa albumu nie może być pusta";
            }
        } else {
            return "nazwa albumu musi mieć od 1 do 100 znaków";
        }
    }
}
