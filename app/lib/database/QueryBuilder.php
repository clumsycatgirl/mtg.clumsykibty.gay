<?php

namespace Lib\Database;

/**
 * Class QueryBuilder
 *
 * A class to build and execute SQL queries using a fluent interface.
 */
class QueryBuilder {
    private string $table;
    private array $select = ['*'];
    private array $where = [];
    private string $group_by = '';
    private array $having = [];
    private array $joins = [];
    private array $bindings = [];
    private ?int $limit = null;
    private ?int $offset = null;

    /**
     * Set the table for the query.
     *
     * @param string $table The name of the table.
     * @return static
     */
    public function from(string $table): static {
        $this->table = $table;
        return $this;
    }

    /**
     * Set the columns to be selected.
     *
     * @param array $columns The columns to select.
     * @return static
     */
    public function select(array $columns): static {
        $this->select = $columns;
        return $this;
    }

    /**
     * Add a WHERE clause to the query.
     *
     * @param string $column The column name.
     * @param QueryOperators $operator The comparison operator.
     * @param mixed $value The value to compare against.
     * @return static
     */
    public function where(string $column, QueryOperators $operator, mixed $value): static {
        $this->where[] = "$column {$operator->value} ?";
        $this->bindings[] = $value;
        return $this;
    }

    /**
     * Add a GROUP BY clause to the query.
     *
     * @param string $column The column name to group by.
     * @return static
     */
    public function group_by(string $column): static {
        $this->group_by = $column;
        return $this;
    }

    /**
     * Add a HAVING clause to the query.
     *
     * @param string $column The column name.
     * @param QueryOperators $operator The comparison operator.
     * @param mixed $value The value to compare against.
     * @return static
     */
    public function having(string $column, QueryOperators $operator, mixed $value): static {
        $this->having[] = "$column {$operator->value} ?";
        $this->bindings[] = $value;
        return $this;
    }

    /**
     * Add a JOIN clause to the query.
     *
     * @param string $table The name of the table to join.
     * @param string $first The first column for the join condition.
     * @param QueryOperators $operator The comparison operator.
     * @param string $second The second column for the join condition.
     * @param string $type The type of join (default is INNER).
     * @return static
     */
    public function join(string $table, string $first, QueryOperators $operator, string $second, string $type = 'INNER'): static {
        $this->joins[] = [
            'table' => $table,
            'type' => $type,
            'first' => $first,
            'operator' => $operator->value,
            'second' => $second,
        ];
        return $this;
    }

    /**
     * Set the LIMIT clause for the query.
     *
     * @param int $limit The number of records to return.
     * @return static
     */
    public function limit(int $limit): static {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Set the OFFSET clause for the query.
     *
     * @param int $offset The number of records to skip.
     * @return static
     */
    public function offset(int $offset): static {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Execute a raw SQL query.
     *
     * @param string $sql The raw SQL query.
     * @param array $bindings The bindings for the query.
     * @return false|\mysqli_stmt
     */
    public function raw(string $sql, array $bindings = []): false|\mysqli_stmt {
        return Database::query($sql, $bindings);
    }

    /**
     * Execute the built query and get the results.
     *
     * @return false|\mysqli_stmt
     */
    public function get(): false|\mysqli_stmt {
        return Database::query($this->get_query(), $this->get_bindings());
    }

    /**
     * Execute the built query and get the results as associative arrays.
     *
     * @return false|array
     */
    public function fetch_all(): false|array {
        $result = Database::query($this->get_query(), $this->get_bindings());
        if ($result === false)
            return false;
        return $result->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get the SQL query as a string.
     *
     * @return string
     */
    public function get_query(): string {
        $sql = "SELECT " . implode(', ', $this->select) . " FROM " . $this->table;

        foreach ($this->joins as $join) {
            $sql .= " " . strtoupper($join['type']) . " JOIN " . $join['table'] .
                " ON " . $join['first'] . " " . $join['operator'] . " " . $join['second'];
        }

        if (!empty($this->where)) {
            $sql .= " WHERE " . implode(' AND ', $this->where);
        }

        if (!empty($this->group_by)) {
            $sql .= " GROUP BY " . $this->group_by;
        }

        if (!empty($this->having)) {
            $sql .= " HAVING " . implode(' AND ', $this->having);
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT " . $this->limit;
        }

        if ($this->offset !== null) {
            $sql .= " OFFSET " . $this->offset;
        }

        return $sql;
    }

    /**
     * Get the bindings for the query.
     *
     * @return array
     */
    public function get_bindings(): array {
        return $this->bindings;
    }
}
