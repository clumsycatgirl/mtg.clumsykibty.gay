<?php

namespace Lib\Systems\Auth;

use Lib\Systems\Auth\Classes\AuthUserPermission;
use Lib\Systems\Models\Model;

/**
 * Class AuthUserPermissionModel
 *
 * Represents the model for managing AuthUserPermission entities.
 */
class AuthUserPermissionModel extends Model {
    /**
     * AuthUserPermissionModel constructor.
     *
     * Initializes the model with the table name 'auth_user_permissions'.
     */
    public function __construct() {
        parent::__construct('auth_user_permissions');
    }

    /**
     * Retrieve all AuthUserPermission entities from the database.
     *
     * @return array|false Array of AuthUserPermission objects, or false on failure.
     */
    public function all(): array|false|string {
        $result = parent::all();
        if (is_array($result))
            $result = array_map(fn(array $row): AuthUserPermission => new AuthUserPermission($row['id'], $row['user_id'], $row['permission_id']), $result);
        return $result;
    }

    /**
     * Find an AuthUserPermission entity by its ID.
     *
     * @param string|int|float|bool $id The ID of the AuthUserPermission entity to find.
     * @return object|array|false|null AuthUserPermission object if found, null if not found, false on failure.
     */
    public function find(string|int|float|bool $id): object|array|false|null {
        $result = parent::find($id);
        if (is_array($result))
            $result = new AuthUserPermission($result['id'], $result['user_id'], $result['permission_id']);
        return $result;
    }

    /**
     * Select AuthUserPermission entities based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false Array of AuthUserPermission objects matching conditions, or false on failure.
     */
    public function select_where(array $conditions): object|array|false {
        $result = parent::select_where($conditions);
        if (is_array($result))
            $result = array_map(fn(array $row): AuthUserPermission => new AuthUserPermission($row['id'], $row['user_id'], $row['permission_id']), $result);
        return $result;
    }

    /**
     * Select the first AuthUserPermission entity based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false|null AuthUserPermission object if found, null if not found, false on failure.
     */
    public function select_where_first(array $conditions): object|array|false|null {
        $result = parent::select_where_first($conditions);
        if (is_array($result))
            $result = new AuthUserPermission($result['id'], $result['user_id'], $result['permission_id']);
        return $result;
    }
}
