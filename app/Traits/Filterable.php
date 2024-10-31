<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    public function scopeFilter(Builder $query, array $filters, array $searchableColumns = ['name']): Builder
    {
        $sortBy = $filters['sortKey'] ?? config('sorting.users.default_sort_column');
        $sortDirection = $filters['sortOrder'] ?? config('sorting.users.default_sort_order');
        $searchTerm = $filters['search'] ?? null;
        $validSortColumns = config('sorting.users.valid_sort_columns');

        return $query
            ->when($searchTerm, function(Builder $q) use ($searchTerm, $searchableColumns) {
                $q->where(function(Builder $subQ) use ($searchTerm, $searchableColumns) {
                    foreach ($searchableColumns as $column) {
                        $subQ->orWhere($column, 'like', "%{$searchTerm}%");
                    }
                });
            })
            ->when(in_array($sortBy, $validSortColumns, true), function(Builder $q) use ($sortBy, $sortDirection) {
                $q->orderBy($sortBy, $sortDirection);
            }, function(Builder $q) {
                $q->orderBy(config('sorting.users.default_sort_column'), config('sorting.users.default_sort_order'));
            });
    }
}
