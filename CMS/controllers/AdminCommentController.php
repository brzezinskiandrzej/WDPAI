<?php
namespace App\Controllers;


use App\Repositories\PhotoRepository;
use App\Services\PhotoService;

session_start();


class AdminCommentController
{
    private PhotoService $photoService;
   

    public function __construct()
    {
       

        $photoRepo = new PhotoRepository($this->conn);
        $this->photoService = new PhotoService($photoRepo);
    }

    public function showKomentarze()
    {
        $co = $_GET['co'] ?? null;

       
        if (!$co) {
            $count = $this->photoService->getUnacceptedCountComments();
            if ($count == 0) {
                header('Location: index.php?type=kom&co=wszystko');
                exit;
            } else {

                $comments = [];
                require __DIR__ . '/../views/adminKomentarzeView.php';
                return;
            }
        }

       
        if ($co === 'wszystko') {
            
            $comments = $this->photoService->getAllCommentsOrdered();
            require __DIR__ . '/../views/adminKomentarzeView.php';
            return;
        }

        if ($co === 'tylko') {
            $comments = $this->photoService->getUnacceptedComments();
            require __DIR__ . '/../views/adminKomentarzeView.php';
            return;
        }

        
        $comments = [];
        require __DIR__ . '/../views/adminKomentarzeView.php';
    }

  
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

   
    public function editComment()
    {
        if (!isset($_POST['id']) || !isset($_POST['textareanumber'])) {
            header('Location: index.php?type=kom');
            exit;
        }
        $commentId       = (int)$_POST['id'];
        $textareanumber = $_POST['textareanumber'];
        
        $newText = $_POST['kom'.$textareanumber] ?? '';

 
        $this->photoService->editComment($commentId, $newText);

        $_SESSION['warning3'] = 'Komentarz został zedytowany 🙂';
        header('Location: index.php?type=kom');
        exit;
    }

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
