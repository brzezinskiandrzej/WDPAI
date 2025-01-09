<?php
namespace App\Controllers;


use App\Repositories\UserRepository;
use App\Services\UserService;

session_start();


class AdminUserController
{
    private UserService $userService;
    

    public function __construct()
    {
       

        $userRepo = new UserRepository($this->conn);
        $this->userService = new UserService($userRepo);
    }

  
    public function showUsers()
    {
        $co = $_GET['co'] ?? null;

        
        if (!$co) {
            
            $users = [];
            require __DIR__ . '/../views/adminUsersView.php';
            return;
        }

       
        $myId = $_SESSION['tablica'][7] ?? null; 
        $users = $this->userService->getUsersByCo($co, $myId);

    
        require __DIR__ . '/../views/adminUsersView.php';
    }

   
    public function blockUser(int $userId) 
    {
        if ($userId === null) {
            header('Location: index.php?type=users');
            exit;
        }
        if (!$this->isAdmin()) { 
            $_SESSION['warning'] = 'Nie masz uprawnień do blokowania użytkowników.';
            header('Location: index.php?type=users');
            exit;
        }
        

        $this->userService->blockUser($userId);

        $_SESSION['warning3'] = 'Konto użytkownika zostało zablokowane 🙂';
        header('Location: index.php?type=users');
        exit;
    }

    
    public function unblockUser(int $userId)
    {
        if ($userId === null) {
            header('Location: index.php?type=users');
            exit;
        }
        if (!$this->isAdmin()) {
            $_SESSION['warning'] = 'Nie masz uprawnień do odblokowywania użytkowników.';
            header('Location: index.php?type=users');
            exit;
        }
        
        
        $this->userService->unblockUser($userId);

        $_SESSION['warning3'] = 'Konto użytkownika zostało odblokowane 🙂';
        header('Location: index.php?type=users');
        exit;
    }
    private function isAdmin(): bool
    {
        
        return isset($_SESSION['tablica'][5]) && 
           in_array($_SESSION['tablica'][5], ['administrator', 'moderator'], true);
    }

    
    public function deleteUser()
    {
        if (!isset($_POST['id'])) {
            header('Location: index.php?type=users');
            exit;
        }
        $userId = (int)$_POST['id'];

        $this->userService->deleteAccount($userId);

        $_SESSION['warning3'] = 'Konto użytkownika zostało usunięte 🙂';
        header('Location: index.php?type=users');
        exit;
    }

    
    public function changeUserPermission()
    {
        if (!isset($_POST['id']) || !isset($_POST['wybor'])) {
            header('Location: index.php?type=users');
            exit;
        }
        $userId = (int)$_POST['id'];
        $role   = $_POST['wybor'];

        $this->userService->changeUserPermissions($userId, $role);

        $_SESSION['warning3'] = 'Uprawnienia użytkownika zostały pomyślnie zmienione 🙂';
        header('Location: index.php?type=users');
        exit;
    }
}
