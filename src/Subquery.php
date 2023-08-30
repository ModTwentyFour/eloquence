<?php

namespace Sofa\Eloquence;

use Illuminate\Database\Grammar;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\Expression;

class Subquery extends Expression
{
    /**
     * Query builder instance.
     *
     * @var \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     */
    protected QueryBuilder $query;

    /**
     * Alias for the subquery.
     *
     * @var ?string
     */
    protected ?string $alias;

    /**
     * Create new subquery instance.
     *
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
     * @param string|null $alias
     */
    public function __construct(EloquentBuilder|QueryBuilder $query, string $alias = null)
    {
        if ($query instanceof EloquentBuilder) {
            $query = $query->getQuery();
        }

        $this->setQuery($query);

        $this->alias = $alias;
    }

    /**
     * Set underlying query builder.
     *
     * @param \Illuminate\Database\Query\Builder $query
     */
    public function setQuery(QueryBuilder $query)
    {
        $this->query = $query;
    }

    /**
     * Get underlying query builder.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function getQuery(): EloquentBuilder|QueryBuilder
    {
        return $this->query;
    }

    /**
     * Get the value of the expression.
     * @param Grammar $grammar
     * @return float|int|string
     */
    public function getValue(Grammar $grammar)
    {
        $sql = '('.$this->query->toSql().')';

        if ($this->alias) {
            $alias = $this->query->getGrammar()->wrapTable($this->alias);

            $sql .= ' as '.$alias;
        }

        return $sql;
    }

    /**
     * Get subquery alias.
     *
     * @return string
     */
    public function getAlias(): ?string
    {
        return $this->alias;
    }

    /**
     * Set subquery alias.
     *
     * @param string $alias
     * @return $this
     */
    public function setAlias(string $alias): static
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Pass property calls to the underlying builder.
     *
     * @param string $property
     * @param  mixed  $value
     * @return void
     */
    public function __set(string $property, mixed $value): void
    {
        $this->query->{$property} = $value;
    }

    /**
     * Pass property calls to the underlying builder.
     *
     * @param string $property
     * @return mixed
     */
    public function __get(string $property): mixed
    {
        return $this->query->{$property};
    }

    /**
     * Pass method calls to the underlying builder.
     *
     * @param string $method
     * @param array $params
     * @return mixed
     */
    public function __call(string $method, array $params): mixed
    {
        return call_user_func_array([$this->query, $method], $params);
    }
}
