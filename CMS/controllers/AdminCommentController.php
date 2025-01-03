<?php
namespace App\Controllers;


use App\Repositories\PhotoRepository;
use App\Services\PhotoService;

session_start();

/**
 * Kontroler sekcji "Komentarze" w panelu admina.
 * Zastępuje dawną część adminscript.php => if(type=='kom') ...
 */
class AdminCommentController
{
    private PhotoService $photoService;
   

    public function __construct()
    {
       

        $photoRepo = new PhotoRepository($this->conn);
        $this->photoService = new PhotoService($photoRepo);
    }

    /**
     * Główna metoda do wyświetlania komentarzy,
     * bazuje na parametrach: ?type=kom&co=...
     */
    public function showKomentarze()
    {
        $co = $_GET['co'] ?? null;

        // Jeśli brak 'co', w starym kodzie sprawdzaliśmy liczbę niezaakceptowanych
        if (!$co) {
            $count = $this->photoService->getUnacceptedCountComments();
            if ($count == 0) {
                // dawniej: header('Location:index.php?type=kom&co=wszystko');
                header('Location: index.php?type=kom&co=wszystko');
                exit;
            } else {
                // Wyświetlamy widok z "lubdiv", a listy komentarzy brak
                $comments = [];
                require __DIR__ . '/../views/adminKomentarzeView.php';
                return;
            }
        }

        // co=wszystko lub co=tylko
        if ($co === 'wszystko') {
            // W starym kodzie: if admin => edytuje, etc., ale logika roli i tak w widoku
            // wystarczy zwrócić wszystkie komentarze posortowane np. po zaakceptowany
            $comments = $this->photoService->getAllCommentsOrdered();
            require __DIR__ . '/../views/adminKomentarzeView.php';
            return;
        }

        if ($co === 'tylko') {
            $comments = $this->photoService->getUnacceptedComments();
            require __DIR__ . '/../views/adminKomentarzeView.php';
            return;
        }

        // Fallback -> pusta lista
        $comments = [];
        require __DIR__ . '/../views/adminKomentarzeView.php';
    }

    /**
     * Akceptacja komentarza (action=accept)
     */
    public function acceptComment()
    {
        if (!isset($_POST['id'])) {
            header('Location: index.php?type=kom');
            exit;
        }
        $commentId = (int)$_POST['id'];

        $this->photoService->acceptComment($commentId);

        $_SESSION['warning3'] = 'Komentarz został zaakceptowany 🙂';
        header('Location: index.php?type=kom');
        exit;
    }

    /**
     * Edycja komentarza (action=edit)
     */
    public function editComment()
    {
        if (!isset($_POST['id']) || !isset($_POST['textareanumber'])) {
            header('Location: index.php?type=kom');
            exit;
        }
        $commentId       = (int)$_POST['id'];
        $textareanumber = $_POST['textareanumber'];
        // Nazwa parametru: 'kom' . $textareanumber
        $newText = $_POST['kom'.$textareanumber] ?? '';

        // W starym kodzie: $title = str_replace("'", "''", $newText) ...
        // Teraz lepiej zrobimy to w CommentService->editComment($commentId, $newText),
        // który zabezpieczy dane.
        $this->photoService->editComment($commentId, $newText);

        $_SESSION['warning3'] = 'Komentarz został zedytowany 🙂';
        header('Location: index.php?type=kom');
        exit;
    }

    /**
     * Usuwanie komentarza (action=delete)
     */
    public function deleteComment()
    {
        if (!isset($_POST['id'])) {
            header('Location: index.php?type=kom');
            exit;
        }
        $commentId = (int)$_POST['id'];

        $this->photoService->deleteComment($commentId);

        $_SESSION['warning3'] = 'Komentarz został usunięty';
        header('Location: index.php?type=kom');
        exit;
    }
}
