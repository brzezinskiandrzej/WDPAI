<?php

namespace App\Services;

class PaginationService
{

    public function calculatePages(int $totalItems, int $itemsPerPage): int
    {
        if ($itemsPerPage <= 0) {
            return 1;
        }
        return (int) ceil($totalItems / $itemsPerPage);
    }


    public function calculateOffset(int $currentPage, int $itemsPerPage): int
    {

        return ($currentPage - 1) * $itemsPerPage;
    }
}
