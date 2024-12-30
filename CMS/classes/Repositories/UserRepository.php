<?php

namespace App\Repositories;

use App\Database\DatabaseConnection;

class UserRepository
{
    private $conn;

    public function __construct()
    {
        $this->conn = DatabaseConnection::getInstance()->getConnection();
    }

    /**
     * Sprawdza, czy login istnieje w bazie. Zwraca tablicę z danymi użytkownika lub null.
     */
    public function findByLogin(string $login): ?array
    {
        $sql = "SELECT * FROM uzytkownicy WHERE login = $1 LIMIT 1";
        $result = pg_query_params($this->conn, $sql, [$login]);
        if ($result && pg_num_rows($result) > 0) {
            return pg_fetch_assoc($result);
        }
        return null;
    }

    /**
     * Sprawdza, czy email istnieje w bazie. Zwraca tablicę lub null.
     */
    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM uzytkownicy WHERE email = $1 LIMIT 1";
        $result = pg_query_params($this->conn, $sql, [$email]);
        if ($result && pg_num_rows($result) > 0) {
            return pg_fetch_assoc($result);
        }
        return null;
    }

    /**
     * Dodaje nowego użytkownika do bazy. 
     * Zwraca true, jeśli się uda, false w razie błędu.
     */
    public function createUser(string $login, string $passwordHash, string $email): bool
    {
        $date = date('Y-m-d H:i:s');
        $sql = "INSERT INTO uzytkownicy (login, haslo, email, zarejestrowany, aktywny, uprawnienia)
                VALUES ($1, $2, $3, $4, 1, 'użytkownik')";
        $result = pg_query_params($this->conn, $sql, [
            $login, 
            $passwordHash, 
            $email, 
            $date
        ]);
        return (bool)$result;
    }
    /**
     * Pobiera dane użytkownika na podstawie ID.
     */
    public function getUserById(int $userId): ?array
    {
        // Zmień "users" na rzeczywistą nazwę tabeli w Twojej bazie danych
        $sql = "SELECT id, login, email FROM uzytkownicy WHERE id = $1";
        $result = pg_query_params($this->conn, $sql, [$userId]);

        if ($result && pg_num_rows($result) === 1) {
            return pg_fetch_assoc($result);
        }

        return null;
    }

    /**
     * Aktualizuje dane użytkownika.
     */
    public function updateUser(int $userId, string $username, string $email): bool
    {
        // Zmień "users" na rzeczywistą nazwę tabeli w Twojej bazie danych
        $sql = "UPDATE uzytkownicy SET login = $1, email = $2 WHERE id = $3";
        $result = pg_query_params($this->conn, $sql, [$username, $email, $userId]);

        return $result !== false;
    }

    
    

    /**
     * Aktualizuje email użytkownika.
     */
    public function updateEmail(int $userId, string $newEmail): bool
    {
        $sql = "UPDATE uzytkownicy SET email = $1 WHERE id = $2";
        $result = pg_query_params($this->conn, $sql, [$newEmail, $userId]);

        return $result !== false;
    }

    /**
     * Aktualizuje hasło użytkownika.
     */
    public function updatePassword(int $userId, string $newPassword): bool
    {
        $sql = "UPDATE uzytkownicy SET password = $1 WHERE id = $2";
        $result = pg_query_params($this->conn, $sql, [$newPassword, $userId]);

        return $result !== false;
    }

    /**
     * Usuwa konto użytkownika.
     */
    public function deleteUser(int $userId): bool
    {
        // Usunięcie z tabel zdjecia_oceny, zdjecia_komentarze, zdjecia, albumy, uzytkownicy
        $sql1 = "DELETE FROM zdjecia_oceny WHERE id_uzytkownika = $1";
        $sql2 = "DELETE FROM zdjecia_komentarze WHERE id_uzytkownika = $1";
        $sql3 = "DELETE FROM zdjecia USING albumy WHERE zdjecia.id_albumu = albumy.id AND albumy.id_uzytkownika = $1";
        $sql4 = "DELETE FROM albumy WHERE id_uzytkownika = $1";
        $sql5 = "DELETE FROM uzytkownicy WHERE id = $1";

        // Transakcja
        pg_query($this->conn, "BEGIN");
        $result1 = pg_query_params($this->conn, $sql1, [$userId]);
        $result2 = pg_query_params($this->conn, $sql2, [$userId]);
        $result3 = pg_query_params($this->conn, $sql3, [$userId]);
        $result4 = pg_query_params($this->conn, $sql4, [$userId]);
        $result5 = pg_query_params($this->conn, $sql5, [$userId]);

        if ($result1 && $result2 && $result3 && $result4 && $result5) {
            pg_query($this->conn, "COMMIT");
            return true;
        } else {
            pg_query($this->conn, "ROLLBACK");
            return false;
        }
    }

    
}
