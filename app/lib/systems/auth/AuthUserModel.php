<?php

namespace Lib\Systems\Auth;

use Lib\Systems\Auth\Classes\AuthUser;
use Lib\Systems\Models\Model;

/**
 * Class AuthUserModel
 *
 * Represents the model for managing AuthUser entities.
 */
class AuthUserModel extends Model {
    /**
     * AuthUserModel constructor.
     *
     * Initializes the model with the table name 'auth_users'.
     */
    public function __construct() {
        parent::__construct('auth_users');
    }

    /**
     * Retrieve all AuthUser entities from the database.
     *
     * @return array|false Array of AuthUser objects, or false on failure.
     */
    public function all(): array|false|string {
        $result = parent::all();
        if (is_array($result))
            $result = array_map(fn(array $row): AuthUser => new AuthUser($row['id'], $row['username'], $row['password']), $result);
        return $result;
    }

    /**
     * Find an AuthUser entity by its ID.
     *
     * @param string|int|float|bool $id The ID of the AuthUser entity to find.
     * @return object|array|false|null AuthUser object if found, null if not found, false on failure.
     */
    public function find(string|int|float|bool $id): object|array|false|null {
        $result = parent::find($id);
        if (is_array($result))
            $result = new AuthUser($result['id'], $result['username'], $result['password']);
        return $result;
    }

    /**
     * Select AuthUser entities based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false Array of AuthUser objects matching conditions, or false on failure.
     */
    public function select_where(array $conditions): object|array|false {
        $result = parent::select_where($conditions);
        if (is_array($result))
            $result = array_map(fn(array $row): AuthUser => new AuthUser($row['id'], $row['username'], $row['password']), $result);
        return $result;
    }

    /**
     * Select the first AuthUser entity based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false|null AuthUser object if found, null if not found, false on failure.
     */
    public function select_where_first(array $conditions): object|array|false|null {
        $result = parent::select_where_first($conditions);
        if (is_array($result))
            $result = new AuthUser($result['id'], $result['username'], $result['password']);
        return $result;
    }
}
