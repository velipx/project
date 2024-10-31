<?php

namespace App\Traits;

trait SortingValidation
{
    private function validateSorting(?string $sortKey, ?string $sortOrder, string $configKey): array
    {
        $sortConfig = config("sorting.$configKey");

        $sortKey = in_array($sortKey, $sortConfig['valid_sort_columns'])
            ? $sortKey
            : $sortConfig['default_sort_column'];

        $sortOrder = in_array(strtolower($sortOrder ?? ''), ['asc', 'desc'])
            ? $sortOrder
            : $sortConfig['default_sort_order'];

        return [$sortKey, $sortOrder];
    }
}
