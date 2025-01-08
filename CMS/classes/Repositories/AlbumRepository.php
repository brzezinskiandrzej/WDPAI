<?php

namespace App\Repositories;

use App\Database\DatabaseConnection;

class AlbumRepository
{
    private $conn;

    public function __construct()
    {
        $this->conn = DatabaseConnection::getInstance()->getConnection();
    }

    public function countAllWithAcceptedPhotos(): int
    {
        $sql = <<<SQL
            SELECT COUNT(*) as ile
            FROM (
                SELECT a.id
                FROM albumy a
                JOIN zdjecia z ON z.id_albumu = a.id
                GROUP BY a.id
                HAVING SUM(CASE WHEN z.zaakceptowane = 1 THEN 1 ELSE 0 END) > 0
            ) n
        SQL;

        $result = pg_query($this->conn, $sql);
        $row = pg_fetch_assoc($result);

        return (int) $row['ile'];
    }

    public function findAlbumsWithAcceptedPhotos(
        string $sortBy,
        string $sortType,
        int $limit,
        int $offset
    ): array {
        $allowedSort = ['tytul', 'data', 'login'];
        if (!in_array($sortBy, $allowedSort)) {
            $sortBy = 'tytul';
        }
        $sortType = strtoupper($sortType) === 'DESC' ? 'DESC' : '';

        $sql = <<<SQL
            SELECT a.id, 
                   a.tytul, 
                   a.data, 
                   COUNT(z.opis) AS ile, 
                   SUM(z.zaakceptowane) AS accept, 
                   u.login,
                   (
                       SELECT z2.opis 
                       FROM zdjecia z2
                       WHERE z2.id_albumu = a.id
                         AND z2.zaakceptowane = 1
                       ORDER BY z2.data ASC, z2.id ASC
                       LIMIT 1
                   ) AS first_photo
            FROM albumy a
            JOIN zdjecia z ON z.id_albumu = a.id
            JOIN uzytkownicy u ON u.id = a.id_uzytkownika
            GROUP BY a.id, u.login
            HAVING SUM(CASE WHEN z.zaakceptowane = 1 THEN 1 ELSE 0 END) > 0
            ORDER BY $sortBy $sortType
            LIMIT $limit OFFSET $offset
        SQL;
    
        $result = pg_query($this->conn, $sql);
        $albums = [];
        while ($row = pg_fetch_assoc($result)) {
            $albums[] = $row;
        }
        return $albums;
    }

    public function createAlbum(int $userId, string $title): ?int
    {
        $date = date('Y-m-d H:i:s');
        $sql = <<<SQL
            INSERT INTO albumy (tytul, data, id_uzytkownika)
            VALUES ($1, $2, $3)
            RETURNING id
        SQL;

        $result = pg_query_params($this->conn, $sql, [
            $title,
            $date,
            $userId
        ]);

        if ($result && pg_num_rows($result) > 0) {
            $row = pg_fetch_assoc($result);
            return (int)$row['id'];
        }

        return null;
    }
    public function findAlbumsByUser(int $userId): array
    {
        $sql = "SELECT * FROM albumy WHERE id_uzytkownika = $1 ORDER BY data DESC";
        $result = pg_query_params($this->conn, $sql, [$userId]);

        $albums = [];
        if ($result) {
            while ($row = pg_fetch_assoc($result)) {
                $albums[] = $row; 

            }
        }
        return $albums;
    }
    public function getAlbumsByUser(int $userId): array
    {
        $sql = "SELECT a.id, a.tytul, COUNT(z.opis) AS ile, SUM(z.zaakceptowane) AS accept, u.login
                FROM albumy AS a
                LEFT JOIN zdjecia AS z ON z.id_albumu = a.id
                INNER JOIN uzytkownicy AS u ON u.id = a.id_uzytkownika
                WHERE u.id = $1
                GROUP BY a.id, a.tytul, u.login
                ORDER BY a.tytul;";
        $result = pg_query_params($this->conn, $sql, [$userId]);

        $albums = [];
        if ($result) {
            while ($row = pg_fetch_assoc($result)) {
                $albums[] = $row;
            }
        }

        return $albums;
    }
    public function updateAlbumTitle(string $newTitle,int $albumId): bool
    {
        $sql = "UPDATE albumy SET tytul = $1 WHERE id = $2";
        $result = pg_query_params($this->conn, $sql, [$newTitle, $albumId]);
        return $result !== false;
    }
    public function deleteAlbum(int $albumId): bool
    {
        $sql = "DELETE FROM albumy WHERE id = $1";
        $result = pg_query_params($this->conn, $sql, [$albumId]);
        return $result !== false;
    }
    public function countAllAlbumsForAdmin(): int
    {
        $sql = "SELECT COUNT(*) as ile FROM (
            SELECT a.id,a.tytul,COUNT(z.opis) as ile,SUM(z.zaakceptowane) as accept
            FROM albumy as a
            LEFT JOIN zdjecia as z on z.id_albumu=a.id
            GROUP BY a.id
        ) n";
        $res = pg_query($this->conn, $sql);
        $row = pg_fetch_assoc($res);
        return (int)($row['ile'] ?? 0);
    }

    public function findAlbumsForAdmin(int $offset, int $limit): array
    {
        $sql = "
           SELECT a.id, a.tytul, a.data,
                  COUNT(z.opis) as ile,
                  SUM(z.zaakceptowane) as accept,
                  u.login,
                  (COUNT(z.opis) - SUM(z.zaakceptowane)) as niezaakceptowane
           FROM albumy as a
           LEFT JOIN zdjecia as z ON z.id_albumu = a.id
           INNER JOIN uzytkownicy as u ON u.id = a.id_uzytkownika
           GROUP BY a.id, a.tytul, a.data, u.login
           ORDER BY niezaakceptowane DESC, a.id
           LIMIT $limit OFFSET $offset
        ";
        $res = pg_query($this->conn, $sql);

        $albums = [];
        while ($row = pg_fetch_assoc($res)) {
            $albums[] = $row;
        }
        return $albums;
    }
    public function deleteAlbumCompletely(int $albumId): void
    {
        
        pg_query($this->conn, "BEGIN");


        $sql2 = "
            DELETE FROM zdjecia_komentarze
            WHERE id_zdjecia IN (
                SELECT id FROM zdjecia WHERE id_albumu = $1
            )
        ";
        $result2 = pg_query_params($this->conn, $sql2, [$albumId]);
        if (!$result2) {
            pg_query($this->conn, "ROLLBACK");
            throw new \Exception("Błąd podczas usuwania komentarzy.");
        }

        
        $sql3 = "
            DELETE FROM zdjecia_oceny
            WHERE id_zdjecia IN (
                SELECT id FROM zdjecia WHERE id_albumu = $1
            )
        ";
        $result3 = pg_query_params($this->conn, $sql3, [$albumId]);
        if (!$result3) {
            pg_query($this->conn, "ROLLBACK");
            throw new \Exception("Błąd podczas usuwania ocen.");
        }

       
        $sql4 = "DELETE FROM zdjecia WHERE id_albumu = $1";
        $result4 = pg_query_params($this->conn, $sql4, [$albumId]);
        if (!$result4) {
            pg_query($this->conn, "ROLLBACK");
            throw new \Exception("Błąd podczas usuwania zdjęć.");
        }

        
        $sql = "DELETE FROM albumy WHERE id = $1";
        $result = pg_query_params($this->conn, $sql, [$albumId]);
        if (!$result) {
            pg_query($this->conn, "ROLLBACK");
            throw new \Exception("Błąd podczas usuwania albumu.");
        }

        
        $commit = pg_query($this->conn, "COMMIT");
        if (!$commit) {
            throw new \Exception("Błąd podczas zatwierdzania transakcji.");
        }
    }
    
    
}
