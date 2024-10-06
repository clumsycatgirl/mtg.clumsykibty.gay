<?php

namespace Lib\Database;

/**
 * Class Database
 *
 * Provides methods to interact with the database using the QueryBuilder.
 */
class Database {
    private static ?\mysqli $connection = null;

    /**
     * Establishes a connection to the database.
     *
     * @return \mysqli
     */
    public static function connect(): \mysqli {
        if (self::$connection === null) {
            $host = database_hostname;
            $port = database_port;
            $dbname = database_database;
            $username = database_username;
            $password = database_password;

            self::$connection = new \mysqli($host, $username, $password, $dbname, $port);

            if (self::$connection->connect_error) {
                log_error("Connection failed\n\t\tError: " . self::$connection->connect_error);
                die("Connection failed: " . self::$connection->connect_error);
            }
        }

        return self::$connection;
    }

    /**
     * Executes a raw SQL query with bindings.
     *
     * @param string $sql The SQL query.
     * @param array $bindings The bindings for the query.
     * @return \mysqli_stmt|false
     */
    public static function query(string $sql, array $bindings = []): \mysqli_stmt|false {
        $connection = self::connect();
        $statement = $connection->prepare($sql);

        if ($statement === false) {
            die("Error in query preparation: {$connection->error}");
        }

        if (!empty($bindings)) {
            $types = '';
            $params = [];

            foreach ($bindings as $key => &$value) {
                $params[$key] = &$value;
                $types .= match (gettype($value)) {
                    'integer' => 'i',
                    'double' => 'd',
                    default => 's',
                };
            }

            array_unshift($params, $types);
            call_user_func_array([$statement, 'bind_param'], $params);
        }

        $statement->execute();

        if ($statement->error) {
            log_error("Database error: \n\t\tError: $statement->error");
            return false;
        }

        return $statement;
    }

    /**
     * Retrieves all rows from a specified table.
     *
     * @param string $table The name of the table.
     * @return array|false
     */
    public static function table(string $table): array|false {
        $query_builder = (new QueryBuilder())->from($table);
        return self::fetch_all($query_builder);
    }

    /**
     * Finds a row by its primary key.
     *
     * @param string $table The name of the table.
     * @param string|int|float|bool $id The primary key value.
     * @param string $primary_key The name of the primary key column.
     * @return array|null|false
     */
    public static function find(string $table, string|int|float|bool $id, string $primary_key = 'id'): array|null|false {
        $query_builder = (new QueryBuilder())->from($table)->where($primary_key, QueryOperators::Equals, $id);
        return self::fetch_one($query_builder);
    }

    /**
     * Inserts a new row into a table.
     *
     * @param string $table The name of the table.
     * @param array $data The data to insert.
     * @return int|string|false
     */
    public static function insert(string $table, array $data): int|string|false {
        $columns = implode(', ', array_keys($data));
        $values = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO $table ($columns) VALUES ($values)";
        $bindings = array_values($data);

        $result = self::query($sql, $bindings);
        if ($result === false) {
            return false;
        }

        return self::connect()->insert_id;
    }

    /**
     * Updates a row by its primary key.
     *
     * @param string $table The name of the table.
     * @param string|int|float|bool $id The primary key value.
     * @param array $data The data to update.
     * @param string $primary_key The name of the primary key column.
     * @return bool
     */
    public static function update(string $table, string|int|float|bool $id, array $data, string $primary_key = 'id'): bool {
        $set_clause = implode(', ', array_map(fn(string $column): string => "$column = ?", array_keys($data)));
        $bindings = array_merge(array_values($data), [$id]);
        $sql = "UPDATE $table SET $set_clause WHERE $primary_key = ?";
        $result = self::query($sql, $bindings);

        return $result !== false;
    }

    /**
     * Deletes a row by its primary key.
     *
     * @param string $table The name of the table.
     * @param string|int|float|bool $id The primary key value.
     * @param string $primary_key The name of the primary key column.
     * @return bool
     */
    public static function delete(string $table, string|int|float|bool $id, string $primary_key = 'id'): bool {
        $sql = "DELETE FROM $table WHERE $primary_key = ?";
        $result = self::query($sql, [$id]);

        return $result !== false;
    }

    /**
     * Fetches all rows for a given query.
     *
     * @param QueryBuilder $query_builder The query builder instance.
     * @return array|false
     */
    private static function fetch_all(QueryBuilder $query_builder): array|false {
        $result = self::query($query_builder->get_query(), $query_builder->get_bindings());

        if ($result === false) {
            return false;
        }

        $rows = [];
        $meta = $result->result_metadata();

        while ($field = $meta->fetch_field()) {
            $row[$field->name] = null;
            $bind_params[] = &$row[$field->name];
        }

        call_user_func_array([$result, 'bind_result'], $bind_params);

        while ($result->fetch()) {
            $rows[] = array_map(
                fn(string|array $value): string|array|false => mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1'),
                array_filter($row, fn(string|array|null $value) => $value !== null));
        }

        return $rows;
    }

    /**
     * Fetches a single row for a given query.
     *
     * @param QueryBuilder $query_builder The query builder instance.
     * @return array|null|false
     */
    private static function fetch_one(QueryBuilder $query_builder): array|null|false {
        $result = self::query($query_builder->get_query(), $query_builder->get_bindings());

        if ($result === false) {
            return false;
        }

        $meta = $result->result_metadata();

        while ($field = $meta->fetch_field()) {
            $row[$field->name] = null;
            $bind_params[] = &$row[$field->name];
        }

        call_user_func_array([$result, 'bind_result'], $bind_params);

        if ($result->fetch()) {
            return array_map(fn(string|array|null $value): array|false|string|null => $value !== null ? mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1') : null, $row);
        }

        return null;
    }

    public static function escape($input) {
        if (is_string($input)) {
            return mysqli_real_escape_string(self::$connection, $input);
        }
        return $input;
    }
}
