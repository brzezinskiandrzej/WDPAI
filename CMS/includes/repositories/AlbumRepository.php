<?php
namespace Repositories;

use Database\DatabaseInterface;

class AlbumRepository
{
    private DatabaseInterface $db;

    public function __construct(DatabaseInterface $db)
    {
        $this->db = $db;
    }

    public function getAlbumsByUserId(int $userId): array
    {
        $query = "
            SELECT id, title, created_at 
            FROM albums 
            WHERE user_id = $1 
            ORDER BY created_at DESC
        ";
        return $this->db->query($query, [$userId]);
    }
}
?>
