<?php

namespace Lib\Systems\Models;

use \Lib\Database\Database;
use \Lib\Database\QueryBuilder;
use \Lib\Database\QueryOperators;

/**
 * Class Model
 *
 * Base model class providing methods to interact with a database table.
 */
class Model {
    protected string $table;
    protected string $primary_key = 'id';
    protected string $last_query = '';

    /**
     * Constructor.
     * Sets the table name based on the class name (without "Model").
     */
    public function __construct(?string $table_name = null) {
        if ($table_name !== null) {
            $this->table = $table_name;
            return;
        }

        $class_name = str_replace('Model', '', substr(strrchr(get_class($this), '\\'), 1));
        $this->table = strtolower($class_name) . 's';
    }

    /**
     * Get all records from the table.
     *
     * @return object|array|false|string Array of records, false on failure, or string if raw query.
     */
    public function all(): array|false|string|object {
        return Database::table($this->table);
    }

    /**
     * Find a record by its primary key.
     *
     * @param string|int|float|bool $id The value of the primary key.
     * @return object|array|false|null Array representing the record, false on failure, or null if not found.
     */
    public function find(string|int|float|bool $id): array|false|null|object {
        return Database::find($this->table, $id, $this->primary_key);
    }

    /**
     * Insert a new record into the table.
     *
     * @param array $data Associative array of column values to insert.
     * @return false|int|string False on failure, inserted ID for success, or string if raw query.
     */
    public function insert(array $data): false|int|string {
        return Database::insert($this->table, $data);
    }

    /**
     * Update a record in the table.
     *
     * @param string|int|float|bool $id The value of the primary key for the record to update.
     * @param array $data Associative array of column values to update.
     * @return bool True on success, false on failure.
     */
    public function update(string|int|float|bool $id, array $data): bool {
        return Database::update($this->table, $id, $data, $this->primary_key);
    }

    /**
     * Delete a record from the table.
     *
     * @param string|int|float|bool $id The value of the primary key for the record to delete.
     * @return bool True on success, false on failure.
     */
    public function delete(string|int|float|bool $id): bool {
        return Database::delete($this->table, $id, $this->primary_key);
    }

    /**
     * Execute a raw SQL query.
     *
     * @param string $sql The raw SQL query.
     * @param array $bindings The bindings for the query.
     * @return false|\mysqli_stmt False on failure or mysqli_stmt for success.
     */
    public function raw_query(string $sql, array $bindings): false|\mysqli_stmt {
        $this->last_query = $sql;
        return Database::query($sql, $bindings);
    }

    /**
     * Select records from the table based on conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false Array of records, false on failure.
     */
    public function select_where(array $conditions): array|false|object {
        $query_builder = (new QueryBuilder())->from($this->table);

        foreach ($conditions as $column => $value) {
            $query_builder->where($column, QueryOperators::Equals, $value);
        }

        $sql = $query_builder->get_query();
        $bindings = $query_builder->get_bindings();
        $this->last_query = $sql;

        $result = $this->raw_query($sql, $bindings);
        if ($result === false)
            return false;

        $result = $result->get_result();
        if ($result === false)
            return false;

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Select the first record from the table based on conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false|null Array representing the first record, false on failure, or null if not found.
     */
    public function select_where_first(array $conditions): array|false|null|object {
        $query_builder = (new QueryBuilder())->from($this->table);

        foreach ($conditions as $column => $value) {
            $query_builder->where($column, QueryOperators::Equals, $value);
        }

        $query_builder->limit(1);

        $sql = $query_builder->get_query();
        $bindings = $query_builder->get_bindings();
        $this->last_query = $sql;

        $result = $this->raw_query($sql, $bindings);
        if ($result === false)
            return false;

        $result = $result->get_result();
        if ($result === false)
            return false;

        return $result->fetch_assoc();
    }

    /**
     * Alias for select_where_first method.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false|null Array representing the first record, false on failure, or null if not found.
     */
    public function first_where(array $conditions): array|null|false|object {
        return $this->select_where_first($conditions);
    }

    /**
     * Count all records in the table.
     *
     * @return int The number of records.
     */
    public function count(): int {
        $sql = "SELECT COUNT(*) FROM `$this->table`";
        $this->last_query = $sql;

        $result = $this->raw_query($sql, []);
        if ($result === false)
            return 0;

        $result = $result->get_result();
        if ($result === false)
            return 0;

        $row = $result->fetch_row();
        return $row[0];
    }

    /**
     * Count records in the table based on conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return int The number of records matching the conditions.
     */
    public function count_where(array $conditions): int {
        $query_builder = (new QueryBuilder())->from($this->table)->select(['COUNT(*) AS count']);

        foreach ($conditions as $column => $value) {
            $query_builder->where($column, QueryOperators::Equals, $value);
        }

        $sql = $query_builder->get_query();
        $bindings = $query_builder->get_bindings();
        $this->last_query = $sql;

        $result = $this->raw_query($sql, $bindings);
        if ($result === false)
            return 0;

        $result = $result->get_result();
        if ($result === false)
            return 0;

        $row = $result->fetch_assoc();
        return (int) $row['count'];
    }

    /**
     * Get the last executed SQL query.
     *
     * @return string The last executed SQL query.
     */
    public function get_last_query(): string {
        return $this->last_query;
    }

    /**
     * Get the table name.
     *
     * @return string The table name.
     */
    public function get_table(): string {
        return $this->table;
    }

    /**
     * Create a new query builder instance for the current table.
     *
     * @return QueryBuilder The query builder instance.
     */
    public function query(): QueryBuilder {
        return (new QueryBuilder())->from($this->get_table());
    }
}

