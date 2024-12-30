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

    /**
     * Próba logowania:
     *  - sprawdza, czy login istnieje,
     *  - czy hasło (md5) pasuje,
     *  - czy konto aktywne.
     * Zwraca tablicę z danymi lub komunikat błędu.
     */
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
        // Sprawdzamy hasło
        // (W starym kodzie było MD5, w nowym lepiej bcrypt lub password_hash, ale tu dopasujemy się do starego.)
        if (md5($password) !== $userData['haslo']) {
            $errors[] = "Nieprawidłowe hasło.";
            return ['errors' => $errors];
        }

        // Sukces – zwracamy dane usera
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

    /**
     * Próba rejestracji:
     *  - walidacja loginu, hasła, email,
     *  - sprawdzenie, czy login i email nie istnieją w bazie,
     *  - jeśli OK – utworzenie usera.
     */
    public function registerUser(
        string $login, 
        string $password, 
        string $passwordRepeat, 
        string $email
    ): array {
        $errors = [];

        // 1. walidacja loginu
        if (!preg_match("/^[0-9a-zA-Z]{8,16}$/", $login)) {
            $errors[] = "Login musi mieć 8-16 znaków (litery i cyfry).";
        } else {
            // czy login jest już w użyciu?
            if ($this->userRepository->findByLogin($login)) {
                $errors[] = "Login jest już zajęty.";
            }
        }

        // 2. walidacja hasła
        if (!preg_match("/^.{8,20}$/", $password)) {
            $errors[] = "Hasło musi mieć 8-20 znaków.";
        } elseif (!preg_match("/[a-z]/", $password)) {
            $errors[] = "Hasło musi zawierać co najmniej 1 małą literę.";
        } elseif (!preg_match("/[A-Z]/", $password)) {
            $errors[] = "Hasło musi zawierać co najmniej 1 wielką literę.";
        } elseif (!preg_match("/[0-9]/", $password)) {
            $errors[] = "Hasło musi zawierać co najmniej 1 cyfrę.";
        }

        // 3. porównanie hasła i powtórzenia
        if ($password !== $passwordRepeat) {
            $errors[] = "Hasła nie są identyczne.";
        }

        // 4. walidacja email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Niepoprawny adres email.";
        } else {
            // czy email jest w użyciu?
            if ($this->userRepository->findByEmail($email)) {
                $errors[] = "Ten email jest już w użyciu.";
            }
        }

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        // 5. rejestracja (md5 – niezalecane, ale kompatybilne z Twoim projektem)
        $passwordHash = md5($password);
        $ok = $this->userRepository->createUser($login, $passwordHash, $email);
        if (!$ok) {
            return ['errors' => ["Błąd zapisu w bazie danych."]];
        }

        // Sukces
        return ['success' => true];
    }
    public function getUser(int $userId): ?array
    {
        return $this->userRepository->getUserById($userId);
    }

    /**
     * Aktualizuje email użytkownika.
     */
    public function updateEmail(int $userId, string $newEmail): array
    {
        $errors = [];

        // Walidacja emaila
        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Nieprawidłowy format adresu email.";
            return ['errors' => $errors];
        }

        // Aktualizacja emaila
        $success = $this->userRepository->updateEmail($userId, $newEmail);

        if (!$success) {
            $errors[] = "Wystąpił błąd podczas aktualizacji adresu email.";
            return ['errors' => $errors];
        }
        $_SESSION['tablica'][3] = $newEmail;
        return ['success' => true];
    }

    /**
     * Aktualizuje hasło użytkownika.
     */
    public function updatePassword(int $userId, string $currentPassword, string $newPassword): array
    {
        $errors = [];

        // Pobranie aktualnego hasła z bazy
        if (md5($currentPassword) != $_SESSION['tablica'][2]) {
            $errors[] = "Niepoprawne obecne hasło.";
            return ['errors' => $errors];
        }

        
        $passwordValidation = $this->validatePassword($newPassword);
        if ($passwordValidation !== false) {
            $errors[] = $passwordValidation;
            return ['errors' => $errors];
        }

        // Hashowanie nowego hasła
        $hashedPassword = md5($newPassword);

        // Aktualizacja hasła
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

    /**
     * Usuwa konto użytkownika.
     */
    public function deleteAccount(int $userId): array
    {
        $errors = [];

        // Usuwanie konta
        $success = $this->userRepository->deleteUser($userId);

        if (!$success) {
            $errors[] = "Wystąpił błąd podczas usuwania konta.";
            return ['errors' => $errors];
        }

        return ['success' => true];
    }
    
}
