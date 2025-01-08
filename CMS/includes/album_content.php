<?php
require_once 'includes/database/DatabaseInterface.php';
require_once 'includes/database/PostgresDatabase.php';
require_once 'includes/repositories/AlbumRepository.php';


$db = new PostgresDatabase();
$albumRepo = new AlbumRepository($db);


$userId = $_SESSION['tablica']['user_id'];
$albums = $albumRepo->getAlbumsByUserId($userId);

if (empty($albums)) {
    echo '<p>Brak albumów do wyświetlenia.</p>';
} else {
    foreach ($albums as $album) {
        echo '<div class="album">';
        echo '<h2>' . htmlspecialchars($album['title']) . '</h2>';
        echo '<p>Data utworzenia: ' . htmlspecialchars($album['created_at']) . '</p>';
        echo '<a href="view_album.php?album_id=' . htmlspecialchars($album['id']) . '">Zobacz zdjęcia</a>';
        echo '</div>';
    }
}
?>
