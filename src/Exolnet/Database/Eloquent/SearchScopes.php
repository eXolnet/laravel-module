<?php

namespace Exolnet\Database\Eloquent;

use Illuminate\Database\Eloquent\Builder;

trait SearchScopes
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|array $fields
     * @param string $q
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereSearch(Builder $query, $fields, $q)
    {
        $terms = preg_split('/\s+/', $q);

        return $this->scopeWhereFieldsAny($query, (array)$fields, function (Builder $query, $field) use ($terms) {
            $this->scopeWhereSearchTerms($query, $field, $terms);
        });
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $fields
     * @param callable $callback
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereFieldsAny(Builder $query, array $fields, callable $callback)
    {
        return $query->where(function (Builder $query) use ($fields, $callback) {
            foreach ((array)$fields as $field) {
                $query->orWhere(function (Builder $query) use ($field, $callback) {
                    return $callback($query, $field);
                });
            }
        });
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $field
     * @param array $terms
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereSearchTerms(Builder $query, $field, array $terms)
    {
        foreach ($terms as $term) {
            $this->scopeWhereSearchTerm($query, $field, $term);
        }

        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $field
     * @param string $term
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereSearchTerm(Builder $query, $field, $term)
    {
        return $query->where($field, 'LIKE', '%' . $term . '%');
    }
}
