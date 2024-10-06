<?php

namespace Lib\Systems\Auth;

use Lib\Systems\Auth\Classes\AuthGroupPermission;
use Lib\Systems\Models\Model;

/**
 * Class AuthGroupPermissionModel
 *
 * Represents the model for managing AuthGroupPermission entities.
 */
class AuthGroupPermissionModel extends Model {
    /**
     * AuthGroupPermissionModel constructor.
     *
     * Initializes the model with the table name 'auth_group_permissions'.
     */
    public function __construct() {
        parent::__construct('auth_group_permissions');
    }

    /**
     * Retrieve all AuthGroupPermission entities from the database.
     *
     * @return array|false Array of AuthGroupPermission objects, or false on failure.
     */
    public function all(): array|false|string {
        $result = parent::all();
        if (is_array($result))
            $result = array_map(fn(array $row): AuthGroupPermission => new AuthGroupPermission($row['id'], $row['group_id'], $row['permission_id']), $result);
        return $result;
    }

    /**
     * Find an AuthGroupPermission entity by its ID.
     *
     * @param string|int|float|bool $id The ID of the AuthGroupPermission entity to find.
     * @return object|array|false|null AuthGroupPermission object if found, null if not found, false on failure.
     */
    public function find(string|int|float|bool $id): object|array|false|null {
        $result = parent::find($id);
        if (is_array($result))
            $result = new AuthGroupPermission($result['id'], $result['group_id'], $result['permission_id']);
        return $result;
    }

    /**
     * Select AuthGroupPermission entities based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false Array of AuthGroupPermission objects matching conditions, or false on failure.
     */
    public function select_where(array $conditions): object|array|false {
        $result = parent::select_where($conditions);
        if (is_array($result))
            $result = array_map(fn(array $row): AuthGroupPermission => new AuthGroupPermission($row['id'], $row['group_id'], $row['permission_id']), $result);
        return $result;
    }

    /**
     * Select the first AuthGroupPermission entity based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false|null AuthGroupPermission object if found, null if not found, false on failure.
     */
    public function select_where_first(array $conditions): object|array|false|null {
        $result = parent::select_where_first($conditions);
        if (is_array($result))
            $result = new AuthGroupPermission($result['id'], $result['group_id'], $result['permission_id']);
        return $result;
    }
}
