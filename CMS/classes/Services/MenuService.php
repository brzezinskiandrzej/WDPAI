<?php

namespace App\Classes\Services;

class MenuService
{
  
    public function getMenuItems(bool $isLoggedIn, string $role): array
    {
        
        $items = [];

        $items[] = [
            'id' => 'zaloz',
            'label' => 'Załóż album',
            'href' => 'dodaj-album.php',
            'visible' => $isLoggedIn, 
        ];


        $items[] = [
            'id' => 'dodaj',
            'label' => 'Dodaj zdjęcie',
            'href' => 'dodaj-foto.php',
            'visible' => $isLoggedIn,
        ];


        $items[] = [
            'id' => 'oceniane',
            'label' => 'Najlepiej oceniane',
            'href' => 'top-foto.php',
            'visible' => true, 
        ];


        $items[] = [
            'id' => 'najnowsze',
            'label' => 'Najnowsze',
            'href' => 'nowe-foto.php',
            'visible' => true,
        ];


        $items[] = [
            'id' => 'konto',
            'label' => 'Moje konto',
            'href' => 'konto.php',
            'visible' => $isLoggedIn, 
        ];

 
        $items[] = [
            'id' => 'wyloguj',
            'label' => 'Wyloguj się',
            'href' => 'wyloguj.php',
            'visible' => $isLoggedIn,
        ];

 
        $items[] = [
            'id' => 'logowanie2',
            'label' => 'Zaloguj się',
            'href' => 'logrej.php',
            'visible' => !$isLoggedIn, 
        ];


        $items[] = [
            'id' => 'rejestracja2',
            'label' => 'Rejestracja',
            'href' => 'logrej.php?sort=1',
            'visible' => !$isLoggedIn,
        ];


        $items[] = [
            'id' => 'admin',
            'label' => 'Panel administracyjny',
            'href' => 'admin/index.php',
            'visible' => ($role === 'administrator' || $role === 'moderator'),
        ];

        
        return $items;
    }
}
