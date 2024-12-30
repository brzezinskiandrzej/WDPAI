<?php

namespace App\Services;

class PaginationService
{
    /**
     * Prosta funkcja do wyliczenia liczby stron
     */
    public function calculatePages(int $totalItems, int $itemsPerPage): int
    {
        if ($itemsPerPage <= 0) {
            return 1;
        }
        return (int) ceil($totalItems / $itemsPerPage);
    }

    /**
     * Zwraca offset na podstawie nr strony i ilości wyników na stronę.
     */
    public function calculateOffset(int $currentPage, int $itemsPerPage): int
    {
        // Przykład: strona 1 => offset 0
        //           strona 2 => offset = itemsPerPage
        return ($currentPage - 1) * $itemsPerPage;
    }
}
