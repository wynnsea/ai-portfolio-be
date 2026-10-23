<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Search, filter, sort and reorder support for the admin tables.
 *
 * The model must implement searchableColumns() and filterableColumns().
 */
trait HasAdminListing
{
    /**
     * @param  array{search?: ?string, sort?: ?string}&array<string, mixed>  $params
     */
    public function scopeAdminListing(Builder $query, array $params): void
    {
        $search = trim((string) ($params['search'] ?? ''));

        if ($search !== '') {
            $query->where(function (Builder $query) use ($search): void {
                foreach ($this->searchableColumns() as $column) {
                    if ($this->hasCast($column, ['array'])) {
                        $query->orWhereRaw("LOWER(CAST({$column} AS CHAR)) LIKE ?", ['%'.Str::lower($search).'%']);
                    } else {
                        $query->orWhere($column, 'like', "%{$search}%");
                    }
                }
            });
        }

        foreach ($this->filterableColumns() as $param => $column) {
            $value = $params[$param] ?? null;

            if (filled($value) && $value !== 'Semua') {
                $query->where($column, $value);
            }
        }

        $this->applySort($query, $params['sort'] ?? null);
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->latest('id');
    }

    /**
     * Set sort_order based on the position of each id in the list.
     *
     * @param  list<int>  $ids
     */
    public static function reorder(array $ids): void
    {
        DB::transaction(function () use ($ids): void {
            foreach (array_values($ids) as $position => $id) {
                static::query()->whereKey($id)->update(['sort_order' => $position]);
            }
        });
    }

    /**
     * @return list<string>
     */
    abstract public function searchableColumns(): array;

    /**
     * @return array<string, string> query param => column
     */
    abstract public function filterableColumns(): array;

    private function applySort(Builder $query, ?string $sort): void
    {
        $column = Str::snake(ltrim((string) $sort, '-'));

        if ($column === '' || ! in_array($column, $this->sortableColumns(), true)) {
            $query->ordered();

            return;
        }

        $query->orderBy($column, Str::startsWith($sort, '-') ? 'desc' : 'asc')->latest('id');
    }

    /**
     * @return list<string>
     */
    private function sortableColumns(): array
    {
        $columns = array_filter(
            $this->getFillable(),
            fn (string $column): bool => ! $this->hasCast($column, ['array']),
        );

        return [...$columns, 'id', 'created_at', 'updated_at'];
    }
}
