<?php

namespace Lib\Systems\Auth;

use Lib\Systems\Auth\Classes\AuthPermission;
use Lib\Systems\Models\Model;

/**
 * Class AuthPermissionModel
 *
 * Represents the model for managing AuthPermission entities.
 */
class AuthPermissionModel extends Model {
    /**
     * AuthPermissionModel constructor.
     *
     * Initializes the model with the table name 'auth_permissions'.
     */
    public function __construct() {
        parent::__construct('auth_permissions');
    }

    /**
     * Retrieve all AuthPermission entities from the database.
     *
     * @return array|false Array of AuthPermission objects, or false on failure.
     */
    public function all(): array|false|string {
        $result = parent::all();
        if (is_array($result))
            $result = array_map(fn(array $row): AuthPermission => new AuthPermission($row['id'], $row['name']), $result);
        return $result;
    }

    /**
     * Find an AuthPermission entity by its ID.
     *
     * @param string|int|float|bool $id The ID of the AuthPermission entity to find.
     * @return object|array|false|null AuthPermission object if found, null if not found, false on failure.
     */
    public function find(string|int|float|bool $id): object|array|false|null {
        $result = parent::find($id);
        if (is_array($result))
            $result = new AuthPermission($result['id'], $result['name']);
        return $result;
    }

    /**
     * Select AuthPermission entities based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false Array of AuthPermission objects matching conditions, or false on failure.
     */
    public function select_where(array $conditions): object|array|false {
        $result = parent::select_where($conditions);
        if (is_array($result))
            $result = array_map(fn(array $row): AuthPermission => new AuthPermission($row['id'], $row['name']), $result);
        return $result;
    }

    /**
     * Select the first AuthPermission entity based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false|null AuthPermission object if found, null if not found, false on failure.
     */
    public function select_where_first(array $conditions): object|array|false|null {
        $result = parent::select_where_first($conditions);
        if (is_array($result))
            $result = new AuthPermission($result['id'], $result['name']);
        return $result;
    }
}
