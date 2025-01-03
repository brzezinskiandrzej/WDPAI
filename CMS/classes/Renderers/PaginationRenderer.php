<?php
namespace App\Renderers;

/**
 * Klasa odpowiedzialna za renderowanie HTML dla paginacji.
 */
class PaginationRenderer
{
    /**
     * Renderuje HTML dla paginacji.
     *
     * @param int $currentPage Aktualna strona.
     * @param int $totalPages Całkowita liczba stron.
     * @param string $baseUrl Bazowy URL do którego dodamy parametry paginacji.
     * @param array $additionalParams Dodatkowe parametry GET do zachowania w linkach.
     * @return string HTML z paginacją.
     */
    public function render(int $currentPage, int $totalPages, string $baseUrl, array $additionalParams = []): string
    {
        if ($totalPages < 1) return '';

        // Maksymalna liczba widocznych przycisków paginacji (bez Previous i Next)
        $maxVisible = 5;
        $pagination = '<nav aria-label="Page navigation"><ul class="pagination">';

        // Dodaj przycisk Previous
        if ($currentPage > 1) {
            $prevPage = $currentPage - 1;
            $queryParams = array_merge(['numer' => $prevPage], $additionalParams);
            $pagination .= '<li class="page-item">
                                <a class="page-link" href="' . htmlspecialchars($baseUrl) . '?' . http_build_query($queryParams) . '" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>';
        } else {
            $pagination .= '<li class="page-item disabled">
                                <span class="page-link" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </span>
                            </li>';
        }

        // Oblicz zakres stron do wyświetlenia
        $half = floor($maxVisible / 2);
        $start = max(1, $currentPage - $half);
        $end = min($totalPages, $currentPage + $half);

        if ($currentPage <= $half) {
            $end = min($totalPages, $maxVisible);
        } elseif ($currentPage + $half > $totalPages) {
            $start = max(1, $totalPages - $maxVisible + 1);
        }

        // Dodaj pierwszy przycisk, jeśli start > 1
        if ($start > 1) {
            $queryParams = array_merge(['numer' => 1], $additionalParams);
            $pagination .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($baseUrl) . '?' . http_build_query($queryParams) . '">1</a></li>';
            if ($start > 2) {
                $pagination .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        // Dodaj przyciski stron
        for ($i = $start; $i <= $end; $i++) {
            $queryParams = array_merge(['numer' => $i], $additionalParams);
            if ($i == $currentPage) {
                $pagination .= '<li class="page-item active" aria-current="page">
                                    <span class="page-link">' . $i . '</span>
                                </li>';
            } else {
                $pagination .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($baseUrl) . '?' . http_build_query($queryParams) . '">' . $i . '</a></li>';
            }
        }

        // Dodaj ostatni przycisk, jeśli end < totalPages
        if ($end < $totalPages) {
            if ($end < $totalPages - 1) {
                $pagination .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            $queryParams = array_merge(['numer' => $totalPages], $additionalParams);
            $pagination .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($baseUrl) . '?' . http_build_query($queryParams) . '">' . $totalPages . '</a></li>';
        }

        // Dodaj przycisk Next
        if ($currentPage < $totalPages) {
            $nextPage = $currentPage + 1;
            $queryParams = array_merge(['numer' => $nextPage], $additionalParams);
            $pagination .= '<li class="page-item">
                                <a class="page-link" href="' . htmlspecialchars($baseUrl) . '?' . http_build_query($queryParams) . '" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>';
        } else {
            $pagination .= '<li class="page-item disabled">
                                <span class="page-link" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </span>
                            </li>';
        }

        $pagination .= '</ul></nav>';

        return $pagination;
    }
}
