<?php

namespace App\Classes\Services;

/**
 * MenuService odpowiada za zbudowanie listy linków do wyświetlenia w menu
 * w zależności od stanu zalogowania i uprawnień użytkownika.
 */
class MenuService
{
    /**
     * Zwraca tablicę asocjacyjną (lub obiektów) z informacją o linkach w menu.
     *
     * @param bool   $isLoggedIn  – czy użytkownik jest zalogowany
     * @param string $role        – np. 'administrator', 'moderator', 'użytkownik' albo pusty
     * @return array
     */
    public function getMenuItems(bool $isLoggedIn, string $role): array
    {
        // Sprawdzamy różne uprawnienia i logikę, którą miałeś w .php z JavaScriptem
        $items = [];

        // Przykładowo dodajemy elementy tak, jak w Twoim HTML (id="zaloz", id="dodaj" itd.)
        // Każdy element to tablica z kluczami:
        // 'label' – tekst/link
        // 'href'  – dokąd prowadzi
        // 'visible' – warunek czy się pokaże
        // 'id'   – jeżeli chcesz używać w <li id="...">
        
        // Załóż album
        $items[] = [
            'id' => 'zaloz',
            'label' => 'Załóż album',
            'href' => 'dodaj-album.php',
            'visible' => $isLoggedIn, // tylko, gdy jest zalogowany
        ];

        // Dodaj zdjęcie
        $items[] = [
            'id' => 'dodaj',
            'label' => 'Dodaj zdjęcie',
            'href' => 'dodaj-foto.php',
            'visible' => $isLoggedIn,
        ];

        // Najlepiej oceniane
        $items[] = [
            'id' => 'oceniane',
            'label' => 'Najlepiej oceniane',
            'href' => 'top-foto.php',
            'visible' => true, // zawsze widoczne
        ];

        // Najnowsze
        $items[] = [
            'id' => 'najnowsze',
            'label' => 'Najnowsze',
            'href' => 'nowe-foto.php',
            'visible' => true,
        ];

        // Konto
        $items[] = [
            'id' => 'konto',
            'label' => 'Moje konto',
            'href' => 'konto.php',
            'visible' => $isLoggedIn, 
        ];

        // Wyloguj się
        $items[] = [
            'id' => 'wyloguj',
            'label' => 'Wyloguj się',
            'href' => 'wyloguj.php',
            'visible' => $isLoggedIn,
        ];

        // Zaloguj się
        $items[] = [
            'id' => 'logowanie2',
            'label' => 'Zaloguj się',
            'href' => 'logrej.php',
            'visible' => !$isLoggedIn, // odwrotnie
        ];

        // Rejestracja
        $items[] = [
            'id' => 'rejestracja2',
            'label' => 'Rejestracja',
            'href' => 'logrej.php?sort=1',
            'visible' => !$isLoggedIn,
        ];

        // Panel administracyjny (admin)
        $items[] = [
            'id' => 'admin',
            'label' => 'Panel administracyjny',
            'href' => 'admin/index.php',
            'visible' => ($role === 'administrator' || $role === 'moderator'),
        ];

        // Możesz dodać inne linki, jeśli w oryginale występowały.
        
        return $items;
    }
}
