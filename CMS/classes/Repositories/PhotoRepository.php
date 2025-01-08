<?php

namespace App\Repositories;

use App\Database\DatabaseConnection;

class PhotoRepository
{
    private $conn;

    public function __construct()
    {
        $this->conn = DatabaseConnection::getInstance()->getConnection();
    }

    public function findTopRatedPhotos(int $limit = 20): array
    {
        $sql = <<<SQL
            SELECT 
                AVG(zo.ocena) AS ocena,
                COUNT(zo.id)  AS count_ocen,
                zo.id_zdjecia,
                a.tytul,
                u.login,
                a.id AS album_id,
                z.opis
            FROM zdjecia_oceny zo
            INNER JOIN uzytkownicy u  ON zo.id_uzytkownika = u.id
            INNER JOIN zdjecia    z  ON zo.id_zdjecia = z.id
            INNER JOIN albumy     a  ON z.id_albumu = a.id
            GROUP BY zo.id_zdjecia, a.tytul, u.login, a.id, z.opis
            ORDER BY ocena DESC
            LIMIT $limit
        SQL;

        $result = pg_query($this->conn, $sql);
        if (!$result) {
            return [];
        }

        $photos = [];
        while ($row = pg_fetch_assoc($result)) {
            $photos[] = $row;
        }
        return $photos;
    }
    public function countAcceptedPhotosByAlbum(int $albumId): int
    {
        $sql = <<<SQL
            SELECT COUNT(*) AS ile
            FROM zdjecia
            WHERE id_albumu = $1
              AND zaakceptowane = 1
        SQL;

        $result = pg_query_params($this->conn, $sql, [$albumId]);
        if (!$result) {
            return 0;
        }
        $row = pg_fetch_assoc($result);
        return (int)$row['ile'];
    }
    public function findAcceptedPhotosByAlbum(int $albumId, int $limit, int $offset): array
    {
        $sql = <<<SQL
            SELECT * 
            FROM zdjecia
            WHERE id_albumu = $1
              AND zaakceptowane = 1
            ORDER BY data DESC
            LIMIT $2 
            OFFSET $3
        SQL;

        $result = pg_query_params($this->conn, $sql, [$albumId, $limit, $offset]);
        if (!$result) {
            return [];
        }

        $photos = [];
        while ($row = pg_fetch_assoc($result)) {
            $photos[] = $row;
        }
        return $photos;
    }
    public function findNewestPhotos(int $limit = 20): array
    {
        $sql = <<<SQL
            SELECT 
                z.id as id_zdjecia,
                z.opis,
                z.data,
                z.opiszdjecia,
                a.id as album_id,
                a.tytul,
                u.login
            FROM zdjecia z
            INNER JOIN albumy a ON z.id_albumu = a.id
            INNER JOIN uzytkownicy u ON a.id_uzytkownika = u.id
            WHERE z.zaakceptowane = 1
            ORDER BY z.data DESC
            LIMIT $1
        SQL;


        $result = pg_query_params($this->conn, $sql, [$limit]);
        if (!$result) {
            return [];
        }

        $photos = [];
        while ($row = pg_fetch_assoc($result)) {
            $photos[] = $row;
        }

        return $photos;
    }

    public function findPhotoWithAlbum(int $photoId): ?array
    {
        $sql = <<<SQL
            SELECT 
                z.id,
                z.opis, 
                z.opiszdjecia, 
                z.data, 
                a.id as album_id,
                a.tytul as album_tytul,
                u.login as autor_login,
                u.id as autor_id
            FROM zdjecia z
            INNER JOIN albumy a ON z.id_albumu = a.id
            INNER JOIN uzytkownicy u ON a.id_uzytkownika = u.id
            WHERE z.id = $1
        SQL;

        $result = pg_query_params($this->conn, $sql, [$photoId]);
        if ($result && pg_num_rows($result) > 0) {
            return pg_fetch_assoc($result);
        }
        return null;
    }


    public function addRating(int $photoId, int $userId, int $rating): bool
    {
        $sql = <<<SQL
            INSERT INTO zdjecia_oceny (id_zdjecia, id_uzytkownika, ocena)
            VALUES ($1, $2, $3)
        SQL;
        $res = pg_query_params($this->conn, $sql, [$photoId, $userId, $rating]);
        return (bool) $res;
    }

    public function findRatingsByPhoto(int $photoId): array
    {
        $sql = <<<SQL
            SELECT ocena
            FROM zdjecia_oceny
            WHERE id_zdjecia = $1
        SQL;

        $result = pg_query_params($this->conn, $sql, [$photoId]);
        if (!$result) {
            return [];
        }
        $ratings = [];
        while ($row = pg_fetch_assoc($result)) {
            $ratings[] = (int)$row['ocena'];
        }
        return $ratings;
    }

    public function findNextPhotoInAlbum(int $albumId, string $currentPhotoDate, int $currentPhotoId): ?array
    {
        $sql = <<<SQL
            SELECT z.id
            FROM zdjecia z
            WHERE z.id_albumu = $1 
              AND z.data >= $2
              AND z.id != $3
              AND z.zaakceptowane = 1
            ORDER BY z.data 
            LIMIT 1
        SQL;

        $result = pg_query_params($this->conn, $sql, [
            $albumId, 
            $currentPhotoDate, 
            $currentPhotoId
        ]);
        if ($row = pg_fetch_assoc($result)) {
            return $row; 
        }
        return null;
    }


    public function findPrevPhotoInAlbum(int $albumId, string $currentPhotoDate, int $currentPhotoId): ?array
    {
        $sql = <<<SQL
            SELECT z.id
            FROM zdjecia z
            WHERE z.id_albumu = $1
              AND z.data <= $2
              AND z.id != $3
              AND z.zaakceptowane = 1
            ORDER BY z.data DESC
            LIMIT 1
        SQL;

        $result = pg_query_params($this->conn, $sql, [
            $albumId, 
            $currentPhotoDate, 
            $currentPhotoId
        ]);
        if ($row = pg_fetch_assoc($result)) {
            return $row;
        }
        return null;
    }
    public function addComment(int $photoId, int $userId, string $comment): bool
    {
        $sql = <<<SQL
            INSERT INTO zdjecia_komentarze (id_zdjecia, id_uzytkownika, data, komentarz, zaakceptowany)
            VALUES ($1, $2, NOW(), $3, 0)
        SQL;
        $res = pg_query_params($this->conn, $sql, [$photoId, $userId, $comment]);
        return (bool) $res;
    }

    public function findAcceptedCommentsByPhoto(int $photoId): array
    {
        $sql = <<<SQL
            SELECT c.*, u.login
            FROM zdjecia_komentarze c
            INNER JOIN uzytkownicy u ON c.id_uzytkownika = u.id
            WHERE c.id_zdjecia = $1
              AND c.zaakceptowany = 1
            ORDER BY c.data DESC
        SQL;

        $result = pg_query_params($this->conn, $sql, [$photoId]);
        if (!$result) {
            return [];
        }
        $comments = [];
        while ($row = pg_fetch_assoc($result)) {
            $comments[] = $row;
        }
        return $comments;
    }
    public function countUnacceptedComments(): int
    {
        $sql = "SELECT COUNT(*) as ile FROM zdjecia_komentarze WHERE zaakceptowany=0";
        $res = pg_query($this->conn, $sql);
        $row = pg_fetch_assoc($res);
        return (int)($row['ile'] ?? 0);
    }
    public function findAllCommentsOrdered(): array
    {
        $sql = "SELECT zdjecia_komentarze.id, zdjecia_komentarze.komentarz,
                       zdjecia_komentarze.zaakceptowany, zdjecia.opiszdjecia,
                       uzytkownicy.login
                FROM zdjecia_komentarze
                INNER JOIN zdjecia on zdjecia_komentarze.id_zdjecia=zdjecia.id
                INNER JOIN uzytkownicy on zdjecia_komentarze.id_uzytkownika=uzytkownicy.id
                ORDER BY zdjecia_komentarze.zaakceptowany";
        $res = pg_query($this->conn, $sql);

        $comments = [];
        while ($row = pg_fetch_assoc($res)) {
            $comments[] = $row;
        }
        return $comments;
    }
    public function findUnacceptedComments(): array
    {
        $sql = "SELECT zdjecia_komentarze.id, zdjecia_komentarze.komentarz,
                       zdjecia_komentarze.zaakceptowany, zdjecia.opiszdjecia,
                       uzytkownicy.login
                FROM zdjecia_komentarze
                INNER JOIN zdjecia on zdjecia_komentarze.id_zdjecia=zdjecia.id
                INNER JOIN uzytkownicy on zdjecia_komentarze.id_uzytkownika=uzytkownicy.id
                WHERE zdjecia_komentarze.zaakceptowany=0";
        $res = pg_query($this->conn, $sql);

        $comments = [];
        while ($row = pg_fetch_assoc($res)) {
            $comments[] = $row;
        }
        return $comments;
    }
    public function acceptCommentById(int $commentId): void
    {
        $sql = "UPDATE zdjecia_komentarze SET zaakceptowany=1 WHERE id=$1";
        pg_query_params($this->conn, $sql, [$commentId]);
    }
    public function updateCommentText(int $commentId, string $newText): void
    {
        $sql = "UPDATE zdjecia_komentarze SET komentarz=$1 WHERE id=$2";
        pg_query_params($this->conn, $sql, [$newText, $commentId]);
    }
    public function deleteCommentById(int $commentId): void
    {
        $sql = "DELETE FROM zdjecia_komentarze WHERE id=$1";
        pg_query_params($this->conn, $sql, [$commentId]);
    }
    public function createPhoto(
        int $albumId,
        string $filename,
        string $photoDescription
    ): ?int {
        $date = date('Y-m-d H:i:s');
        $sql = <<<SQL
            INSERT INTO zdjecia (opis, id_albumu, data, zaakceptowane, opiszdjecia)
            VALUES ($1, $2, $3, 0, $4)
            RETURNING id
        SQL;

        $result = pg_query_params($this->conn, $sql, [
            $filename,
            $albumId,
            $date,
            $photoDescription
        ]);

        if ($result && pg_num_rows($result) > 0) {
            $row = pg_fetch_assoc($result);
            return (int)$row['id'];
        }
        return null;
    }
    public function findPhotosByAlbum(int $albumId): array
    {
        $sql = <<<SQL
            SELECT * FROM zdjecia
            WHERE id_albumu = $1
            AND zaakceptowane = 1
            ORDER BY data DESC
        SQL;
        $result = pg_query_params($this->conn, $sql, [$albumId]);

        $photos = [];
        if ($result) {
            while ($row = pg_fetch_assoc($result)) {
                $photos[] = $row;
            }
        }
        return $photos;
    }
    public function getPhotosByAlbum(int $albumId): array
    {
        $sql = <<<SQL
            SELECT zdjecia.id, zdjecia.opis, zdjecia.opiszdjecia
            FROM zdjecia
            WHERE zdjecia.id_albumu = $1;
        SQL;
        $result = pg_query_params($this->conn, $sql, [$albumId]);

        $photos = [];
        if ($result) {
            while ($row = pg_fetch_assoc($result)) {
                $photos[] = $row;
            }
        }

        return $photos;
    }
    public function updatePhotoDescription(string $newDescription, int $photoId): bool
    {
        $sql = "UPDATE zdjecia SET opiszdjecia = $1 WHERE id = $2";
        $result = pg_query_params($this->conn, $sql, [$newDescription, $photoId]);
        return $result !== false;
    }
    public function deletePhoto(int $photoId): bool
    {
        $sql2 = "DELETE FROM zdjecia_komentarze WHERE id_zdjecia = $1";
        $sql3 = "DELETE FROM zdjecia_oceny WHERE id_zdjecia = $1";
        $sql4 = "DELETE FROM zdjecia WHERE id = $1";

        pg_query($this->conn, "BEGIN");

        $res2 = pg_query_params($this->conn, $sql2, [$photoId]);
        $res3 = pg_query_params($this->conn, $sql3, [$photoId]);
        $res4 = pg_query_params($this->conn, $sql4, [$photoId]);

        if ($res2 && $res3 && $res4) {
            pg_query($this->conn, "COMMIT");
            return true;
        } else {
            pg_query($this->conn, "ROLLBACK");
            return false;
        }
    }
    public function getPhotoById(int $photoId): ?array
    {
        $sql = "SELECT id_albumu, opis FROM zdjecia WHERE id = $1";
        $result = pg_query_params($this->conn, $sql, [$photoId]);

        if ($result && pg_num_rows($result) === 1) {
            return pg_fetch_assoc($result);
        }

        return null;
    }
    public function countUnacceptedPhotos(): int
    {
        $sql = "SELECT COUNT(*) as ile FROM zdjecia WHERE zaakceptowane=0";
        $res = pg_query($this->conn, $sql);
        $row = pg_fetch_assoc($res);
        return (int)($row['ile'] ?? 0);
    }
    public function findPhotosByAlbumAdmin(int $albumId): array
    {
        $sql = "SELECT zdjecia.id, zdjecia.opis, zdjecia.opiszdjecia,
                       zdjecia.zaakceptowane, albumy.tytul, albumy.id as albumid
                FROM zdjecia
                INNER JOIN albumy ON zdjecia.id_albumu=albumy.id
                WHERE albumy.id=$1";
        $res = pg_query_params($this->conn, $sql, [$albumId]);

        $photos = [];
        while ($row = pg_fetch_assoc($res)) {
            $photos[] = $row;
        }
        return $photos;
    }
    public function findAllAlbumsWithPhotos(): array
    {
        $sql = "SELECT albumy.id, albumy.tytul, COUNT(zdjecia.id) as ile
                FROM albumy
                INNER JOIN zdjecia ON zdjecia.id_albumu=albumy.id
                GROUP BY albumy.id";
        $res = pg_query($this->conn, $sql);

        $albums = [];
        while ($row = pg_fetch_assoc($res)) {
            $albums[] = $row;
        }
        return $albums;
    }
    public function findAllUnacceptedPhotos(): array
    {
        $sql = "SELECT zdjecia.id, zdjecia.opis, zdjecia.opiszdjecia,
                       albumy.tytul, albumy.id as albumid
                FROM zdjecia
                INNER JOIN albumy on zdjecia.id_albumu=albumy.id
                WHERE zdjecia.zaakceptowane=0";
        $res = pg_query($this->conn, $sql);

        $photos = [];
        while ($row = pg_fetch_assoc($res)) {
            $photos[] = $row;
        }
        return $photos;
    }
    public function acceptPhotoById(int $photoId): void
    {
        $sql = "UPDATE zdjecia SET zaakceptowane=1 WHERE id=$1";
        pg_query_params($this->conn, $sql, [$photoId]);
    }
    public function deletePhotoWithRelations(int $photoId, int $albumId, string $filename): void
    {
        $sql2 = "DELETE FROM zdjecia_komentarze
                 USING zdjecia
                 WHERE zdjecia_komentarze.id_zdjecia=zdjecia.id AND zdjecia.id=$1";
        $sql3 = "DELETE FROM zdjecia_oceny
                 USING zdjecia
                 WHERE zdjecia_oceny.id_zdjecia=zdjecia.id AND zdjecia.id=$1";
        $sql4 = "DELETE FROM zdjecia WHERE id=$1";

        pg_query_params($this->conn, $sql2, [$photoId]);
        pg_query_params($this->conn, $sql3, [$photoId]);
        pg_query_params($this->conn, $sql4, [$photoId]);

        $directory  = "../photo/".$albumId."/".$filename;
        $directory2 = "../photo/".$albumId."/min/".$photoId."-min.jpg";
        @unlink($directory);
        @unlink($directory2);
    }

    
}
