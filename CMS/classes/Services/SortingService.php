<?php

namespace App\Services;

class SortingService
{
    public function validateSortParams(?string $sortBy, ?string $sortType): array
    {
        $allowedSort = ['tytul', 'data', 'login'];
        if (!in_array($sortBy, $allowedSort)) {
            $sortBy = 'tytul';
        }

        $sortType = strtoupper($sortType) === 'DESC' ? 'DESC' : '';

        return [$sortBy, $sortType];
    }
}
