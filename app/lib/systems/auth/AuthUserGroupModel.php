<?php

namespace Lib\Systems\Auth;

use Lib\Systems\Auth\Classes\AuthUserGroup;
use Lib\Systems\Models\Model;

/**
 * Class AuthUserGroupModel
 *
 * Represents the model for managing AuthUserGroup entities.
 */
class AuthUserGroupModel extends Model {
    /**
     * AuthUserGroupModel constructor.
     *
     * Initializes the model with the table name 'auth_user_groups'.
     */
    public function __construct() {
        parent::__construct('auth_user_groups');
    }

    /**
     * Retrieve all AuthUserGroup entities from the database.
     *
     * @return array|false Array of AuthUserGroup objects, or false on failure.
     */
    public function all(): array|false|string {
        $result = parent::all();
        if (is_array($result))
            $result = array_map(fn(array $row): AuthUserGroup => new AuthUserGroup($row['id'], $row['user_id'], $row['group_id']), $result);
        return $result;
    }

    /**
     * Find an AuthUserGroup entity by its ID.
     *
     * @param string|int|float|bool $id The ID of the AuthUserGroup entity to find.
     * @return object|array|false|null AuthUserGroup object if found, null if not found, false on failure.
     */
    public function find(string|int|float|bool $id): object|array|false|null {
        $result = parent::find($id);
        if (is_array($result))
            $result = new AuthUserGroup($result['id'], $result['user_id'], $result['group_id']);
        return $result;
    }

    /**
     * Select AuthUserGroup entities based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false Array of AuthUserGroup objects matching conditions, or false on failure.
     */
    public function select_where(array $conditions): object|array|false {
        $result = parent::select_where($conditions);
        if (is_array($result))
            $result = array_map(fn(array $row): AuthUserGroup => new AuthUserGroup($row['id'], $row['user_id'], $row['group_id']), $result);
        return $result;
    }

    /**
     * Select the first AuthUserGroup entity based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false|null AuthUserGroup object if found, null if not found, false on failure.
     */
    public function select_where_first(array $conditions): object|array|false|null {
        $result = parent::select_where_first($conditions);
        if (is_array($result))
            $result = new AuthUserGroup($result['id'], $result['user_id'], $result['group_id']);
        return $result;
    }
}
