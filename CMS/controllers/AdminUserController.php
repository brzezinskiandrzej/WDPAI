<?php
namespace App\Controllers;


use App\Repositories\UserRepository;
use App\Services\UserService;

session_start();

/**
 * Kontroler sekcji "Użytkownicy" w panelu admina.
 * Zastępuje starą część adminscript.php => if(type=='users') ...
 */
class AdminUserController
{
    private UserService $userService;
    

    public function __construct()
    {
       

        $userRepo = new UserRepository($this->conn);
        $this->userService = new UserService($userRepo);
    }

    /**
     * Główna metoda do wyświetlania użytkowników
     * bazuje na ?type=users&co=... (zwykly/mod/admin/wszystko)
     */
    public function showUsers()
    {
        $co = $_GET['co'] ?? null;

        // Jeśli brak 'co', w starym kodzie wyświetlaliśmy guziki do wyboru grup:
        if (!$co) {
            // Tylko wyświetlamy widok z guzikami (bez listy)
            $users = []; // brak listy
            require __DIR__ . '/../views/adminUsersView.php';
            return;
        }

        // Gdy jest 'co', pobieramy dane z serwisu:
        $myId = $_SESSION['tablica'][7] ?? null; // id aktualnie zalogowanego
        $users = $this->userService->getUsersByCo($co, $myId);

        // Teraz wyświetlamy widok z listą $users
        require __DIR__ . '/../views/adminUsersView.php';
    }

    /**
     * Blokowanie użytkownika (action=block)
     */
    public function blockUser()
    {
        if (!isset($_POST['id'])) {
            header('Location: index.php?type=users');
            exit;
        }
        $userId = (int)$_POST['id'];

        $this->userService->blockUser($userId);

        $_SESSION['warning3'] = 'Konto użytkownika zostało zablokowane 🙂';
        header('Location: index.php?type=users');
        exit;
    }

    /**
     * Odblokowanie użytkownika (action=unblock)
     */
    public function unblockUser()
    {
        if (!isset($_POST['id'])) {
            header('Location: index.php?type=users');
            exit;
        }
        $userId = (int)$_POST['id'];
        
        $this->userService->unblockUser($userId);

        $_SESSION['warning3'] = 'Konto użytkownika zostało odblokowane 🙂';
        header('Location: index.php?type=users');
        exit;
    }

    /**
     * Usuwanie użytkownika (action=delete)
     */
    public function deleteUser()
    {
        if (!isset($_POST['id'])) {
            header('Location: index.php?type=users');
            exit;
        }
        $userId = (int)$_POST['id'];

        $this->userService->deleteUserCompletely($userId);

        $_SESSION['warning3'] = 'Konto użytkownika zostało usunięte 🙂';
        header('Location: index.php?type=users');
        exit;
    }

    /**
     * Zmiana uprawnień użytkownika (action=change)
     */
    public function changeUserPermission()
    {
        if (!isset($_POST['id']) || !isset($_POST['wybor'])) {
            header('Location: index.php?type=users');
            exit;
        }
        $userId = (int)$_POST['id'];
        $role   = $_POST['wybor'];

        $this->userService->changeUserPermission($userId, $role);

        $_SESSION['warning3'] = 'Uprawnienia użytkownika zostały pomyślnie zmienione 🙂';
        header('Location: index.php?type=users');
        exit;
    }
}
