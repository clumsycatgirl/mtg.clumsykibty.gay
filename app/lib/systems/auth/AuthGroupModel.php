<?php

namespace Lib\Systems\Auth;

use Lib\Systems\Auth\Classes\AuthGroup;
use Lib\Systems\Models\Model;

/**
 * Class AuthGroupModel
 *
 * Represents the model for managing AuthGroup entities.
 */
class AuthGroupModel extends Model {
    /**
     * AuthGroupModel constructor.
     *
     * Initializes the model with the table name 'auth_groups'.
     */
    public function __construct() {
        parent::__construct('auth_groups');
    }

    /**
     * Retrieve all AuthGroup entities from the database.
     *
     * @return array|false Array of AuthGroup objects, or false on failure.
     */
    public function all(): array|false|string {
        $result = parent::all();
        if (is_array($result))
            $result = array_map(fn(array $row): AuthGroup => new AuthGroup($row['id'], $row['name']), $result);
        return $result;
    }

    /**
     * Find an AuthGroup entity by its ID.
     *
     * @param string|int|float|bool $id The ID of the AuthGroup entity to find.
     * @return object|array|false|null AuthGroup object if found, null if not found, false on failure.
     */
    public function find(string|int|float|bool $id): object|array|false|null {
        $result = parent::find($id);
        if (is_array($result))
            $result = new AuthGroup($result['id'], $result['name']);
        return $result;
    }

    /**
     * Select AuthGroup entities based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false Array of AuthGroup objects matching conditions, or false on failure.
     */
    public function select_where(array $conditions): object|array|false {
        $result = parent::select_where($conditions);
        if (is_array($result))
            $result = array_map(fn(array $row): AuthGroup => new AuthGroup($row['id'], $row['name']), $result);
        return $result;
    }

    /**
     * Select the first AuthGroup entity based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false|null AuthGroup object if found, null if not found, false on failure.
     */
    public function select_where_first(array $conditions): object|array|false|null {
        $result = parent::select_where_first($conditions);
        if (is_array($result))
            $result = new AuthGroup($result['id'], $result['name']);
        return $result;
    }
}
