<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService
{
    private UserRepository $userRepository;
    
    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function loginUser(string $login, string $password): array
    {
        $errors = [];
        $userData = $this->userRepository->findByLogin($login);
        if (!$userData) {
            $errors[] = "Nie znaleziono użytkownika o podanym loginie.";
            return ['errors' => $errors];
        }
        if ($userData['aktywny'] == '0') {
            $errors[] = "Konto jest zablokowane.";
            return ['errors' => $errors];
        }
        
        if (md5($password) !== $userData['haslo']) {
            $errors[] = "Nieprawidłowe hasło.";
            return ['errors' => $errors];
        }

        
        return [
            'user' => [
                'id' => $userData['id'],
                'login' => $userData['login'],
                'haslo' => $userData['haslo'],
                'email' => $userData['email'],
                'zarejestrowany' => $userData['zarejestrowany'],
                'uprawnienia' => $userData['uprawnienia'],
                'aktywny' => $userData['aktywny']
            ]
        ];
    }

   
    public function registerUser(
        string $login, 
        string $password, 
        string $passwordRepeat, 
        string $email
    ): array {
        $errors = [];

        
        if (!preg_match("/^[0-9a-zA-Z]{8,16}$/", $login)) {
            $errors[] = "Login musi mieć 8-16 znaków (litery i cyfry).";
        } else {
            
            if ($this->userRepository->findByLogin($login)) {
                $errors[] = "Login jest już zajęty.";
            }
        }

       
        if (!preg_match("/^.{8,20}$/", $password)) {
            $errors[] = "Hasło musi mieć 8-20 znaków.";
        } elseif (!preg_match("/[a-z]/", $password)) {
            $errors[] = "Hasło musi zawierać co najmniej 1 małą literę.";
        } elseif (!preg_match("/[A-Z]/", $password)) {
            $errors[] = "Hasło musi zawierać co najmniej 1 wielką literę.";
        } elseif (!preg_match("/[0-9]/", $password)) {
            $errors[] = "Hasło musi zawierać co najmniej 1 cyfrę.";
        }

        
        if ($password !== $passwordRepeat) {
            $errors[] = "Hasła nie są identyczne.";
        }

        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Niepoprawny adres email.";
        } else {
            
            if ($this->userRepository->findByEmail($email)) {
                $errors[] = "Ten email jest już w użyciu.";
            }
        }

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        
        $passwordHash = md5($password);
        $ok = $this->userRepository->createUser($login, $passwordHash, $email);
        if (!$ok) {
            return ['errors' => ["Błąd zapisu w bazie danych."]];
        }

        
        return ['success' => true];
    }
    public function getUser(int $userId): ?array
    {
        return $this->userRepository->getUserById($userId);
    }

    
    public function updateEmail(int $userId, string $newEmail): array
    {
        $errors = [];

        
        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Nieprawidłowy format adresu email.";
            return ['errors' => $errors];
        }

        
        $success = $this->userRepository->updateEmail($userId, $newEmail);

        if (!$success) {
            $errors[] = "Wystąpił błąd podczas aktualizacji adresu email.";
            return ['errors' => $errors];
        }
        $_SESSION['tablica'][3] = $newEmail;
        return ['success' => true];
    }

    
    public function updatePassword(int $userId, string $newPassword): array
    {
        $errors = [];

        
        

        
        $passwordValidation = $this->validatePassword($newPassword);
        if ($passwordValidation !== false) {
            $errors[] = $passwordValidation;
            return ['errors' => $errors];
        }

       
        $hashedPassword = md5($newPassword);

        
        $success = $this->userRepository->updatePassword($userId, $hashedPassword);

        if (!$success) {
            $errors[] = "Wystąpił błąd podczas aktualizacji hasła.";
            return ['errors' => $errors];
        }
        $_SESSION['tablica'][2] = $hashedPassword;
        return ['success' => true];
    }
    private function validatePassword(string $password)
    {
        $pattern = "/^.{8,20}$/";
        $pattern2 = "/[a-zźżąęćśłó]/";
        $pattern3 = "/[A-ZŻŹĄĘĆŚŁÓ]/";
        $pattern4 = "/[0-9]/";

        if (!preg_match($pattern, $password)) {
            return "hasło musi mieć od 8 do 20 znaków";
        } elseif (!preg_match($pattern2, $password)) {
            return "hasło musi posiadać co najmniej 1 małą literę";
        } elseif (!preg_match($pattern3, $password)) {
            return "hasło musi posiadać co najmniej 1 dużą literę";
        } elseif (!preg_match($pattern4, $password)) {
            return "hasło musi posiadać co najmniej 1 liczbę";
        } else {
            return false;
        }
    }

    
    public function deleteAccount(int $userId): array
    {
        $errors = [];

       
        $success = $this->userRepository->deleteUser($userId);

        if (!$success) {
            $errors[] = "Wystąpił błąd podczas usuwania konta.";
            return ['errors' => $errors];
        }

        return ['success' => true];
    }
    public function getUsersByCo(string $co, ?int $myId): array
    {
        
        switch ($co) {
            case 'zwykly':
                return $this->userRepository->getUsersByRole('użytkownik');

            case 'mod':
                return $this->userRepository->getUsersByRole('moderator');

            case 'admin':
              
                return $this->userRepository->getAdminsExceptMe($myId);

            case 'wszystko':
                
                return $this->userRepository->getAllExcept($myId);

            default:
               
                return [];
        }
    }
    public function unblockUser(int $userId): void
    {
        
        $this->userRepository->unblockUser($userId);
    }
    public function blockUser(int $userId): void
    {
        
        $this->userRepository->blockUser($userId);
    }
    public function changeUserPermissions(int $userId, string $newRole): void
    {
        $allowedRoles = ['administrator', 'moderator', 'użytkownik'];

        
        if (!in_array($newRole, $allowedRoles, true)) {
            throw new \Exception("Nieprawidłowa rola: {$newRole}. Dozwolone role: " . implode(', ', $allowedRoles));
        }

        
        $user = $this->userRepository->getUserById($userId);
        if (!$user) {
            throw new \Exception("Użytkownik o ID {$userId} nie istnieje.");
        }

        $this->userRepository->changeUserPermissions($userId, $newRole);
    }
    
}
